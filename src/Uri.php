<?php
/*
 * PSX is a open source PHP framework to develop RESTful APIs.
 * For the current version and informations visit <http://phpsx.org>
 *
 * Copyright 2010-2018 Christoph Kappestein <christoph.kappestein@gmail.com>
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
    /**
     * @var string
     */
    protected $scheme;

    /**
     * @var string
     */
    protected $authority;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var string
     */
    protected $query;

    /**
     * @var string
     */
    protected $fragment;

    /**
     * @var string
     */
    protected $user;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var string
     */
    protected $host;

    /**
     * @var string
     */
    protected $port;

    /**
     * @var array
     */
    protected $parameters;

    /**
     * @param string $uri
     * @param string $authority
     * @param string $path
     * @param string $query
     * @param string $fragment
     */
    public function __construct($uri, $authority = null, $path = null, $query = null, $fragment = null)
    {
        if (func_num_args() == 1) {
            $this->parse($uri);
        } else {
            $this->scheme    = $uri;
            $this->authority = $authority;
            $this->path      = $path;
            $this->query     = $query;
            $this->fragment  = $fragment;

            $this->parseAuthority($authority);
            $this->parseParameters($query);
        }
    }

    /**
     * @inheritdoc
     */
    public function getScheme()
    {
        return $this->scheme;
    }

    /**
     * @inheritdoc
     */
    public function getAuthority()
    {
        return $this->authority;
    }

    /**
     * @inheritdoc
     */
    public function getUserInfo()
    {
        if (!empty($this->user)) {
            return $this->user . ($this->password !== null ? ':' . $this->password : '');
        } else {
            return '';
        }
    }

    /**
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @inheritdoc
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @inheritdoc
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @inheritdoc
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @inheritdoc
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @inheritdoc
     */
    public function getFragment()
    {
        return $this->fragment;
    }

    /**
     * @inheritdoc
     */
    public function isAbsolute()
    {
        return !empty($this->scheme);
    }

    /**
     * @inheritdoc
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @inheritdoc
     */
    public function getParameter($name)
    {
        return isset($this->parameters[$name]) ? $this->parameters[$name] : null;
    }

    /**
     * @inheritdoc
     */
    public function withScheme($scheme)
    {
        return new static(
            $scheme,
            $this->authority,
            $this->path,
            $this->query,
            $this->fragment
        );
    }

    /**
     * @param string $authority
     * @return static
     */
    public function withAuthority($authority)
    {
        return new static(
            $this->scheme,
            $authority,
            $this->path,
            $this->query,
            $this->fragment
        );
    }

    /**
     * @inheritdoc
     */
    public function withUserInfo($user, $password = null)
    {
        if (!empty($user)) {
            $userInfo  = $user . ($password !== null ? ':' . $password : '');
            $authority = $userInfo . '@' . $this->host;
        } else {
            $authority = $this->host;
        }

        if (!empty($this->port)) {
            $authority.= ':' . $this->port;
        }

        return new static(
            $this->scheme,
            $authority,
            $this->path,
            $this->query,
            $this->fragment
        );
    }

    /**
     * @inheritdoc
     */
    public function withHost($host)
    {
        if (!empty($host)) {
            $userInfo = $this->getUserInfo();
            if (!empty($userInfo)) {
                $authority = $userInfo . '@' . $host;
            } else {
                $authority = $host;
            }

            if (!empty($this->port)) {
                $authority.= ':' . $this->port;
            }
        } else {
            $authority = null;
        }

        return new static(
            $this->scheme,
            $authority,
            $this->path,
            $this->query,
            $this->fragment
        );
    }

    /**
     * @inheritdoc
     */
    public function withPort($port)
    {
        $userInfo = $this->getUserInfo();
        if (!empty($userInfo)) {
            $authority = $userInfo . '@' . $this->host;
        } else {
            $authority = $this->host;
        }

        if (!empty($port)) {
            $authority.= ':' . $port;
        }

        return new static(
            $this->scheme,
            $authority,
            $this->path,
            $this->query,
            $this->fragment
        );
    }

    /**
     * @inheritdoc
     */
    public function withPath($path)
    {
        return new static(
            $this->scheme,
            $this->authority,
            $path,
            $this->query,
            $this->fragment
        );
    }

    /**
     * @inheritdoc
     */
    public function withQuery($query)
    {
        return new static(
            $this->scheme,
            $this->authority,
            $this->path,
            $query,
            $this->fragment
        );
    }

    /**
     * @inheritdoc
     */
    public function withFragment($fragment)
    {
        return new static(
            $this->scheme,
            $this->authority,
            $this->path,
            $this->query,
            $fragment
        );
    }

    /**
     * @inheritdoc
     */
    public function withParameters(array $parameters)
    {
        return $this->withQuery(http_build_query($parameters, '', '&'));
    }

    /**
     * Returns the string representation of the URI
     *
     * @see http://tools.ietf.org/html/rfc3986#section-5.3
     * @return string
     */
    public function toString()
    {
        $result = '';

        if (!empty($this->scheme)) {
            $result.= $this->scheme . ':';
        }

        if (!empty($this->authority)) {
            $result.= '//' . $this->authority;
        }

        $result.= $this->path;

        if (!empty($this->query)) {
            $result.= '?' . $this->query;
        }

        if (!empty($this->fragment)) {
            $result.= '#' . $this->fragment;
        }

        return $result;
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
     * @param string $uri
     */
    protected function parse($uri)
    {
        $uri     = (string) $uri;
        $matches = array();

        preg_match('!' . self::getPattern() . '!', $uri, $matches);

        $scheme    = isset($matches[2]) ? $matches[2] : null;
        $authority = isset($matches[4]) ? $matches[4] : null;
        $path      = isset($matches[5]) ? $matches[5] : null;
        $query     = isset($matches[7]) ? $matches[7] : null;
        $fragment  = isset($matches[9]) ? $matches[9] : null;

        $this->scheme    = $scheme;
        $this->authority = $authority;
        $this->path      = $path;
        $this->query     = $query;
        $this->fragment  = $fragment;

        $this->parseAuthority($authority);
        $this->parseParameters($query);
    }

    /**
     * @param string $authority
     */
    protected function parseAuthority($authority)
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
                $this->port = substr($part, $pos + 2);
            } else {
                $this->host = $part;
            }
        } else {
            $host = strstr($part, ':', true);

            if ($host === false) {
                $this->host = $part;
            } else {
                $this->host = $host;
                $this->port = substr(strstr($part, ':'), 1);
            }
        }

        if (!empty($userInfo)) {
            if (strpos($userInfo, ':') !== false) {
                $this->user     = strstr($userInfo, ':', true);
                $this->password = substr(strstr($userInfo, ':'), 1);
            } else {
                $this->user     = $userInfo;
            }
        }
    }

    /**
     * @param string $query
     */
    protected function parseParameters($query)
    {
        if (!empty($query)) {
            parse_str($query, $this->parameters);
        } else {
            $this->parameters = array();
        }
    }

    /**
     * @see https://tools.ietf.org/html/rfc3986#appendix-B
     */
    public static function getPattern()
    {
        return '^(([^:/?#]+):)?(//([^/?#]*))?([^?#]*)(\?([^#]*))?(#(.*))?';
    }
}
