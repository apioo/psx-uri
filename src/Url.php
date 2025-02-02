<?php
/*
 * PSX is an open source PHP framework to develop RESTful APIs.
 * For the current version and information visit <https://phpsx.org>
 *
 * Copyright (c) Christoph Kappestein <christoph.kappestein@gmail.com>
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

use PSX\Uri\Exception\InvalidFormatException;

/**
 * Class URL represents a Uniform Resource Locator, a pointer to a "resource" on the World Wide Web. A resource can be
 * something as simple as a file or a directory, or it can be a reference to a more complicated object, such as a query
 * to a database or to a search engine
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Url extends Uri
{
    protected function __construct(?string $scheme, ?string $authority, ?string $path, ?string $query, ?string $fragment)
    {
        parent::__construct($scheme, $authority, $path, $query, $fragment);

        // we need at least a scheme and host
        if (empty($this->scheme) || empty($this->host)) {
            throw new InvalidFormatException('Invalid url syntax');
        }

        // check port if available
        if ($this->port !== null) {
            if ($this->port < 1 || $this->port > 0xFFFF) {
                throw new InvalidFormatException('Invalid port range');
            }
        }
    }
}
