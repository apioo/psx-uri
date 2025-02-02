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
use PSX\Uri\Url;
use PSX\Uri\Urn;

/**
 * UrlTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class UrlTest extends TestCase
{
    public function testUrl()
    {
        $url = Url::parse('http://benutzername:passwort@hostname:8080/pfad?argument=wert#textanker');

        $this->assertInstanceOf(Url::class, $url);
        $this->assertEquals('http', $url->getScheme());
        $this->assertEquals('benutzername:passwort', $url->getUserInfo());
        $this->assertEquals('hostname', $url->getHost());
        $this->assertEquals('8080', $url->getPort());
        $this->assertEquals('/pfad', $url->getPath());
        $this->assertEquals(array('argument' => 'wert'), $url->getParameters());
        $this->assertEquals('textanker', $url->getFragment());
    }

    public function testUrlIpv6()
    {
        $url = Url::parse('http://[FEDC:BA98:7654:3210:FEDC:BA98:7654:3210]:80/index.html');

        $this->assertEquals('http', $url->getScheme());
        $this->assertEquals(null, $url->getUserInfo());
        $this->assertEquals('[FEDC:BA98:7654:3210:FEDC:BA98:7654:3210]', $url->getHost());
        $this->assertEquals(80, $url->getPort());
        $this->assertEquals('/index.html', $url->getPath());
        $this->assertEquals(array(), $url->getParameters());
        $this->assertEquals(null, $url->getFragment());
    }

    public function testInvalidUrl()
    {
        $this->expectException(InvalidFormatException::class);

        Url::parse('foobar');
    }

    public function testInvalidUrlEmptyHost()
    {
        $this->expectException(InvalidFormatException::class);

        Url::parse('foo://');
    }

    public function testInvalidUrlEmptyHostButPath()
    {
        $this->expectException(InvalidFormatException::class);

        Url::parse('foo:///foo');
    }

    public function testInvalidUrlEmptyHostButQuery()
    {
        $this->expectException(InvalidFormatException::class);

        Url::parse('foo://?foo=bar');
    }

    public function testInvalidUrlEmptyHostButFragment()
    {
        $this->expectException(InvalidFormatException::class);

        Url::parse('foo://#foo');
    }

    public function testPort()
    {
        $uri = Url::parse('http://www.yahoo.com:8080/');

        $this->assertEquals('http://www.yahoo.com:8080/', $uri->toString());
    }

    public function testSetPortInvalidRangeMin()
    {
        $this->expectException(InvalidFormatException::class);

        $port = -1;
        $uri  = Url::parse('http://www.yahoo.com:' . $port . '/');
    }

    public function testSetPortInvalidRangeMax()
    {
        $this->expectException(InvalidFormatException::class);

        $port = 0xFFFF + 1;
        $uri  = Url::parse('http://www.yahoo.com:' . $port . '/');
    }

    public function testUrlWithoutFile()
    {
        $url = Url::parse('http://127.0.0.1/projects/foo/bar/?project=symfony%2Fsymfony&source=1&destination=2');

        $this->assertEquals('http', $url->getScheme());
        $this->assertEquals(null, $url->getUserInfo());
        $this->assertEquals('127.0.0.1', $url->getHost());
        $this->assertEquals(null, $url->getPort());
        $this->assertEquals('/projects/foo/bar/', $url->getPath());
        $this->assertEquals(array('project' => 'symfony/symfony', 'source' => '1', 'destination' => '2'), $url->getParameters());
        $this->assertEquals(null, $url->getFragment());
    }

    public function testUrlFragmentEncoding()
    {
        $url = Url::parse('http://127.0.0.1/foobar?bar=foo#!"#$%&\'()*+,-./0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\]^_`abcdefghijklmnopqrstuvwxyz{|}~');

        $this->assertEquals('http', $url->getScheme());
        $this->assertEquals(null, $url->getUserInfo());
        $this->assertEquals('127.0.0.1', $url->getHost());
        $this->assertEquals(null, $url->getPort());
        $this->assertEquals('/foobar', $url->getPath());
        $this->assertEquals(array('bar' => 'foo'), $url->getParameters());
        $this->assertEquals('!"#$%&\'()*+,-./0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\]^_`abcdefghijklmnopqrstuvwxyz{|}~', $url->getFragment());
    }
}
