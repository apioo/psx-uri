<?php
/*
 * PSX is an open source PHP framework to develop RESTful APIs.
 * For the current version and information visit <https://phpsx.org>
 *
 * Copyright 2010-2023 Christoph Kappestein <christoph.kappestein@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace PSX\Uri;

/**
 * Represents a Uniform Resource Identifier (URI) reference
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 * @see     http://www.ietf.org/rfc/rfc3986.txt
 *
 * @psalm-consistent-constructor
 */
class Uri implements UriInterface, \JsonSerializable, \Stringable
{
    protected ?string $scheme;
    protected ?string $authority;
    protected ?string $path;
    protected ?string $query;
    protected ?string $fragment;

    protected ?string $user = null;
    protected ?string $password = null;
    protected ?string $host = null;
    protected ?int $port = null;
    protected array $parameters = [];

    protected function __construct(?string $scheme, ?string $authority, ?string $path, ?string $query, ?string $fragment)
    {
        $this->scheme = $scheme;
        $this->authority = $authority;
        $this->path = $path;
        $this->query = $query;
        $this->fragment = $fragment;

        $this->parseAuthority($authority);
        $this->parseParameters($query);
    }

    public function getScheme(): string
    {
        return $this->scheme ?? '';
    }

    public function getAuthority(): string
    {
        return $this->authority ?? '';
    }

    public function getUserInfo(): string
    {
        if (!empty($this->user)) {
            return $this->user . ($this->password !== null ? ':' . $this->password : '');
        } else {
            return '';
        }
    }

    public function getUser(): ?string
    {
        return $this->user;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function getHost(): string
    {
        return $this->host ?? '';
    }

    public function getPort(): ?int
    {
        return $this->port;
    }

    public function getPath(): string
    {
        return $this->path ?? '';
    }

    public function getQuery(): string
    {
        return $this->query ?? '';
    }

    public function getFragment(): string
    {
        return $this->fragment ?? '';
    }

    public function isAbsolute(): bool
    {
        return !empty($this->scheme);
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function getParameter(string $name): mixed
    {
        return $this->parameters[$name] ?? null;
    }

    public function withScheme($scheme): static
    {
        $me = clone $this;
        $me->scheme = $scheme;
        return $me;
    }

    public function withAuthority(string $authority): static
    {
        $me = clone $this;
        $me->authority = $authority;
        $this->parseAuthority($authority);
        return $me;
    }

    public function withUserInfo($user, $password = null): static
    {
        $me = clone $this;
        $me->authority = self::buildAuthority($user, $password, $this->host, $this->port);
        return $me;

    }

    public function withHost($host): static
    {
        $me = clone $this;
        $me->host = $host;
        $me->authority = self::buildAuthority($this->user, $this->password, $host, $this->port);
        return $me;
    }

    public function withPort($port): static
    {
        $me = clone $this;
        $me->port = $port;
        $me->authority = self::buildAuthority($this->user, $this->password, $this->host, $port);
        return $me;
    }

    public function withPath($path): static
    {
        $me = clone $this;
        $me->path = $path;
        return $me;
    }

    public function withQuery($query): static
    {
        $me = clone $this;
        $me->query = $query;
        $me->parseParameters($query);
        return $me;
    }

    public function withFragment($fragment): static
    {
        $me = clone $this;
        $me->fragment = $fragment;
        return $me;
    }

    public function withParameters(array $parameters): static
    {
        $me = clone $this;
        $me->query = http_build_query($parameters, '', '&');
        $me->parameters = $parameters;
        return $me;
    }

    /**
     * Returns the string representation of the URI
     *
     * @see http://tools.ietf.org/html/rfc3986#section-5.3
     */
    public function toString(): string
    {
        $result = '';

        if (!empty($this->scheme)) {
            $result.= $this->scheme . ':';
        }

        if (!empty($this->authority)) {
            $result.= '//' . $this->authority;
        }

        $result.= $this->path ?? '';

        if (!empty($this->query)) {
            $result.= '?' . $this->query;
        }

        if (!empty($this->fragment)) {
            $result.= '#' . $this->fragment;
        }

        return $result;
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    public function jsonSerialize(): string
    {
        return $this->toString();
    }

    /**
     * Parses the given URI into the components
     *
     * @see http://tools.ietf.org/html/rfc3986#appendix-B
     */
    public static function parse(string $uri): static
    {
        $matches = [];
        preg_match('!' . self::getPattern() . '!', $uri, $matches);

        return new static(
            $matches[2] ?? null,
            $matches[4] ?? null,
            $matches[5] ?? null,
            $matches[7] ?? null,
            $matches[9] ?? null,
        );
    }

    /**
     * @see http://tools.ietf.org/html/rfc3986#appendix-B
     */
    public static function of(?string $scheme, ?string $authority, ?string $path, ?string $query, ?string $fragment): static
    {
        return new static($scheme, $authority, $path, $query, $fragment);
    }

    protected function parseAuthority(?string $authority): void
    {
        if (empty($authority)) {
            return;
        }

        $userInfo = strstr($authority, '@', true);
        $part     = $userInfo === false ? $authority : substr(strstr($authority, '@'), 1);

        // in case of ipv6
        if (isset($part[0]) && $part[0] == '[') {
            $pos = strpos($part, ']');

            if ($pos !== false) {
                $this->host = substr($part, 0, $pos + 1);
                $this->port = (int) substr($part, $pos + 2);
            } else {
                $this->host = $part;
            }
        } else {
            $host = strstr($part, ':', true);

            if ($host === false) {
                $this->host = $part;
            } else {
                $this->host = $host;
                $this->port = (int) substr(strstr($part, ':'), 1);
            }
        }

        if (!empty($userInfo)) {
            if (str_contains($userInfo, ':')) {
                $this->user     = strstr($userInfo, ':', true);
                $this->password = substr(strstr($userInfo, ':'), 1);
            } else {
                $this->user     = $userInfo;
            }
        }
    }

    protected function parseParameters(?string $query): void
    {
        $this->parameters = [];
        if (!empty($query)) {
            parse_str($query, $this->parameters);
        }
    }

    /**
     * @see https://tools.ietf.org/html/rfc3986#appendix-B
     */
    public static function getPattern(): string
    {
        return '^(([^:/?#]+):)?(//([^/?#]*))?([^?#]*)(\?([^#]*))?(#(.*))?';
    }

    private static function buildAuthority(?string $user, ?string $password, ?string $host, ?int $port): ?string
    {
        if ($host === null) {
            return null;
        }

        if (!empty($user)) {
            $userInfo  = $user . ($password !== null ? ':' . $password : '');
            $authority = $userInfo . '@' . $host;
        } else {
            $authority = $host;
        }

        if (!empty($port)) {
            $authority.= ':' . $port;
        }

        return $authority;
    }
}
