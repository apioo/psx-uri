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

use InvalidArgumentException;

/**
 * Represents a URL. A string is only a valid URL if it has a scheme and host
 * part. If the URL is not valid an exception is thrown. Note if you want
 * display a URL you need to escape the URL according to the context. I.e. to
 * display the URL in a HTML context it is nessacary to use htmlspecialchars
 * since the URL could contain a XSS vector
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class Url extends Uri
{
    protected function parse($url)
    {
        $url = (string) $url;

        // append http scheme for urls starting with //. Normally // means that
        // we use the scheme from the base url but in this context there is no
        // base url available so we assume http
        if (substr($url, 0, 2) == '//') {
            $url = 'http:' . $url;
        }

        parent::parse($url);

        // we need at least a scheme and host
        if (empty($this->scheme) || empty($this->host)) {
            throw new InvalidArgumentException('Invalid url syntax');
        }

        // check port if available
        if ($this->port !== null) {
            if ($this->port < 1 || $this->port > 0xFFFF) {
                throw new InvalidArgumentException('Invalid port range');
            }
        }
    }
}
