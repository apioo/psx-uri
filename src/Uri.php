<?php
/*
 * PSX is an open source PHP framework to develop RESTful APIs.
 * For the current version and information visit <https://phpsx.org>
 *
 * Copyright 2010-2022 Christoph Kappestein <christoph.kappestein@gmail.com>
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
 * Uri
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 * @see     http://www.ietf.org/rfc/rfc3986.txt
 */
class Uri implements UriInterface
{
    protected ?string $scheme = null;
    protected ?string $authority = null;
    protected ?string $path = null;
    protected ?string $query = null;
    protected ?string $fragment = null;
    protected ?string $user = null;
    protected ?string $password = null;
    protected ?string $host = null;
    protected ?int $port = null;
    protected array $parameters = [];

    public function __construct(string|\Stringable $uri)
    {
        $this->parse((string) $uri);
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

    public function getParameter($name): mixed
    {
        return $this->parameters[$name] ?? null;
    }

    public function withScheme($scheme): static
    {
        $me = clone $this;
        $me->scheme = $scheme;
        return $me;
    }

    public function withAuthority($authority): static
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
        return $this->withQuery(http_build_query($parameters, '', '&'));
    }

    /**
     * Returns the string representation of the URI
     *
     * @see http://tools.ietf.org/html/rfc3986#section-5.3
     * @return string
     */
    public function toString(): string
    {
        return self::build($this->scheme, $this->authority, $this->path, $this->query, $this->fragment);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }

    /**
     * Parses the given URI into the components
     *
     * @see http://tools.ietf.org/html/rfc3986#appendix-B
     */
    protected function parse(string $uri)
    {
        $matches = [];
        preg_match('!' . self::getPattern() . '!', $uri, $matches);

        $authority = $matches[4] ?? null;
        $query     = $matches[7] ?? null;

        $this->scheme    = $matches[2] ?? null;
        $this->authority = $authority;
        $this->path      = $matches[5] ?? null;
        $this->query     = $query;
        $this->fragment  = $matches[9] ?? null;

        $this->parseAuthority($authority);
        $this->parseParameters($query);
    }

    /**
     * @see http://tools.ietf.org/html/rfc3986#appendix-B
     */
    public static function create(?string $scheme, ?string $authority, ?string $path, ?string $query, ?string $fragment): self
    {
        return new self(self::build($scheme, $authority, $path, $query, $fragment));
    }

    protected function parseAuthority(?string $authority)
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

    protected function parseParameters(?string $query)
    {
        if (!empty($query)) {
            parse_str($query, $this->parameters);
        } else {
            $this->parameters = [];
        }
    }

    /**
     * @see https://tools.ietf.org/html/rfc3986#appendix-B
     */
    public static function getPattern(): string
    {
        return '^(([^:/?#]+):)?(//([^/?#]*))?([^?#]*)(\?([^#]*))?(#(.*))?';
    }

    private static function build(?string $scheme, ?string $authority, ?string $path, ?string $query, ?string $fragment): string
    {
        $result = '';

        if (!empty($scheme)) {
            $result.= $scheme . ':';
        }

        if (!empty($authority)) {
            $result.= '//' . $authority;
        }

        $result.= $path;

        if (!empty($query)) {
            $result.= '?' . $query;
        }

        if (!empty($fragment)) {
            $result.= '#' . $fragment;
        }

        return $result;
    }

    private static function buildAuthority(?string $user, ?string $password, ?string $host, ?int $port): ?string
    {
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
