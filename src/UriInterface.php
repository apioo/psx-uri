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

use Psr\Http\Message\UriInterface as PsrUriInterface;

/**
 * Represents an URI. Provides getters to retrieve parts of the URI. The class
 * tries to parse the given string into the URI specific components:
 *
 *   foo://example.com:8042/over/there?name=ferret#nose
 *   \_/   \______________/\_________/ \_________/ \__/
 *    |           |            |            |        |
 * scheme     authority       path        query   fragment
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 * @see     http://www.ietf.org/rfc/rfc3986.txt
 */
interface UriInterface extends PsrUriInterface
{
    /**
     * Tells whether or not this URI is absolute
     * 
     * @return boolean
     */
    public function isAbsolute();

    /**
     * Returns all parameters from the query fragment as array
     * 
     * @return array
     */
    public function getParameters();

    /**
     * Returns a specific query parameter
     * 
     * @param string $name
     * @return mixed
     */
    public function getParameter($name);

    /**
     * Creates a new URI with the provided parameters
     * 
     * @param array $parameters
     * @return static
     */
    public function withParameters(array $parameters);
}
