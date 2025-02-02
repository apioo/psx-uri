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

namespace PSX\Uri\Tests;

use PHPUnit\Framework\TestCase;
use PSX\Uri\Exception\InvalidFormatException;
use PSX\Uri\Urn;

/**
 * UrnTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class UrnTest extends TestCase
{
    public function testUrn()
    {
        $urn = Urn::parse('urn:uuid:f81d4fae-7dec-11d0-a765-00a0c91e6bf6');

        $this->assertInstanceOf(Urn::class, $urn);
        $this->assertEquals('urn', $urn->getScheme());
        $this->assertEquals('uuid:f81d4fae-7dec-11d0-a765-00a0c91e6bf6', $urn->getPath());
        $this->assertEquals('uuid', $urn->getNid());
        $this->assertEquals('f81d4fae-7dec-11d0-a765-00a0c91e6bf6', $urn->getNss());
    }

    public function testInvalidUrn()
    {
        $this->expectException(InvalidFormatException::class);

        Urn::parse('foobar');
    }

    public function testUrnCompare()
    {
        $urns = array(
            'URN:foo:a123,456',
            'urn:foo:a123,456',
            'urn:FOO:a123,456',
            'urn:foo:A123,456',
            'urn:foo:a123%2C456',
            'URN:FOO:a123%2c456',
        );

        foreach ($urns as $rawUrn) {
            $urn = Urn::parse($rawUrn);

            $this->assertEquals('urn:foo:a123,456', $urn->__toString());
        }
    }
}
