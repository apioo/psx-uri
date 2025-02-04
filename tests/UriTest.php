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
use PSX\Uri\Uri;
use PSX\Uri\Url;

/**
 * UriTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    http://phpsx.org
 */
class UriTest extends TestCase
{
    public function testRfcExample1()
    {
        $uri = Uri::parse('ftp://ftp.is.co.za/rfc/rfc1808.txt');

        $this->assertInstanceOf(Uri::class, $uri);
        $this->assertEquals('ftp', $uri->getScheme());
        $this->assertEquals('ftp.is.co.za', $uri->getAuthority());
        $this->assertEquals(null, $uri->getUserInfo());
        $this->assertEquals(null, $uri->getUser());
        $this->assertEquals(null, $uri->getPassword());
        $this->assertEquals('ftp.is.co.za', $uri->getHost());
        $this->assertEquals(null, $uri->getPort());
        $this->assertEquals('/rfc/rfc1808.txt', $uri->getPath());
        $this->assertEquals(null, $uri->getQuery());
        $this->assertEquals(null, $uri->getFragment());
        $this->assertEquals([], $uri->getParameters());
        $this->assertEquals('ftp://ftp.is.co.za/rfc/rfc1808.txt', $uri->toString());
    }

    public function testRfcExample2()
    {
        $uri = Uri::parse('http://www.ietf.org/rfc/rfc2396.txt');

        $this->assertEquals('http', $uri->getScheme());
        $this->assertEquals('www.ietf.org', $uri->getAuthority());
        $this->assertEquals(null, $uri->getUserInfo());
        $this->assertEquals(null, $uri->getUser());
        $this->assertEquals(null, $uri->getPassword());
        $this->assertEquals('www.ietf.org', $uri->getHost());
        $this->assertEquals(null, $uri->getPort());
        $this->assertEquals('/rfc/rfc2396.txt', $uri->getPath());
        $this->assertEquals(null, $uri->getQuery());
        $this->assertEquals(null, $uri->getFragment());
        $this->assertEquals([], $uri->getParameters());
        $this->assertEquals('http://www.ietf.org/rfc/rfc2396.txt', $uri->toString());
    }

    public function testRfcExample3()
    {
        $uri = Uri::parse('ldap://[2001:db8::7]/c=GB?objectClass?one');

        $this->assertEquals('ldap', $uri->getScheme());
        $this->assertEquals('[2001:db8::7]', $uri->getAuthority());
        $this->assertEquals(null, $uri->getUserInfo());
        $this->assertEquals(null, $uri->getUser());
        $this->assertEquals(null, $uri->getPassword());
        $this->assertEquals('[2001:db8::7]', $uri->getHost());
        $this->assertEquals(null, $uri->getPort());
        $this->assertEquals('/c=GB', $uri->getPath());
        $this->assertEquals('objectClass?one', $uri->getQuery());
        $this->assertEquals(null, $uri->getFragment());
        $this->assertEquals(['objectClass?one' => ''], $uri->getParameters());
        $this->assertEquals('ldap://[2001:db8::7]/c=GB?objectClass?one', $uri->toString());
    }

    public function testRfcExample3_1()
    {
        $uri = Uri::parse('ldap://[2001:db8::7]:80/c=GB?objectClass?one');

        $this->assertEquals('ldap', $uri->getScheme());
        $this->assertEquals('[2001:db8::7]:80', $uri->getAuthority());
        $this->assertEquals(null, $uri->getUserInfo());
        $this->assertEquals(null, $uri->getUser());
        $this->assertEquals(null, $uri->getPassword());
        $this->assertEquals('[2001:db8::7]', $uri->getHost());
        $this->assertEquals(80, $uri->getPort());
        $this->assertEquals('/c=GB', $uri->getPath());
        $this->assertEquals('objectClass?one', $uri->getQuery());
        $this->assertEquals(null, $uri->getFragment());
        $this->assertEquals(['objectClass?one' => ''], $uri->getParameters());
        $this->assertEquals('ldap://[2001:db8::7]:80/c=GB?objectClass?one', $uri->toString());
    }

    /**
     * In case we have no ending bracket in an ipv6 we can only extract the user
     */
    public function testRfcExample3_2()
    {
        $uri = Uri::parse('ldap://foo@[2001:db8::7/c=GB?objectClass?one');

        $this->assertEquals('ldap', $uri->getScheme());
        $this->assertEquals('foo@[2001:db8::7', $uri->getAuthority());
        $this->assertEquals('foo', $uri->getUserInfo());
        $this->assertEquals('foo', $uri->getUser());
        $this->assertEquals(null, $uri->getPassword());
        $this->assertEquals('[2001:db8::7', $uri->getHost());
        $this->assertEquals(null, $uri->getPort());
        $this->assertEquals('/c=GB', $uri->getPath());
        $this->assertEquals('objectClass?one', $uri->getQuery());
        $this->assertEquals(null, $uri->getFragment());
        $this->assertEquals(['objectClass?one' => ''], $uri->getParameters());
        $this->assertEquals('ldap://foo@[2001:db8::7/c=GB?objectClass?one', $uri->toString());
    }

    public function testRfcExample4()
    {
        $uri = Uri::parse('mailto:John.Doe@example.com');

        $this->assertEquals('mailto', $uri->getScheme());
        $this->assertEquals(null, $uri->getAuthority());
        $this->assertEquals(null, $uri->getUserInfo());
        $this->assertEquals(null, $uri->getUser());
        $this->assertEquals(null, $uri->getPassword());
        $this->assertEquals(null, $uri->getHost());
        $this->assertEquals(null, $uri->getPort());
        $this->assertEquals('John.Doe@example.com', $uri->getPath());
        $this->assertEquals(null, $uri->getQuery());
        $this->assertEquals(null, $uri->getFragment());
        $this->assertEquals([], $uri->getParameters());
        $this->assertEquals('mailto:John.Doe@example.com', $uri->toString());
    }

    public function testRfcExample5()
    {
        $uri = Uri::parse('mailto://John.Doe@example.com');

        $this->assertEquals('mailto', $uri->getScheme());
        $this->assertEquals('John.Doe@example.com', $uri->getAuthority());
        $this->assertEquals('John.Doe', $uri->getUserInfo());
        $this->assertEquals('John.Doe', $uri->getUser());
        $this->assertEquals(null, $uri->getPassword());
        $this->assertEquals('example.com', $uri->getHost());
        $this->assertEquals(null, $uri->getPort());
        $this->assertEquals(null, $uri->getPath());
        $this->assertEquals(null, $uri->getQuery());
        $this->assertEquals(null, $uri->getFragment());
        $this->assertEquals([], $uri->getParameters());
        $this->assertEquals('mailto://John.Doe@example.com', $uri->toString());
    }

    public function testRfcExample6()
    {
        $uri = Uri::parse('news:comp.infosystems.www.servers.unix');

        $this->assertEquals('news', $uri->getScheme());
        $this->assertEquals(null, $uri->getAuthority());
        $this->assertEquals(null, $uri->getUserInfo());
        $this->assertEquals(null, $uri->getUser());
        $this->assertEquals(null, $uri->getPassword());
        $this->assertEquals(null, $uri->getHost());
        $this->assertEquals(null, $uri->getPort());
        $this->assertEquals('comp.infosystems.www.servers.unix', $uri->getPath());
        $this->assertEquals(null, $uri->getQuery());
        $this->assertEquals(null, $uri->getFragment());
        $this->assertEquals([], $uri->getParameters());
        $this->assertEquals('news:comp.infosystems.www.servers.unix', $uri->toString());
    }

    public function testRfcExample7()
    {
        $uri = Uri::parse('tel:+1-816-555-1212');

        $this->assertEquals('tel', $uri->getScheme());
        $this->assertEquals(null, $uri->getAuthority());
        $this->assertEquals(null, $uri->getUserInfo());
        $this->assertEquals(null, $uri->getUser());
        $this->assertEquals(null, $uri->getPassword());
        $this->assertEquals(null, $uri->getHost());
        $this->assertEquals(null, $uri->getPort());
        $this->assertEquals('+1-816-555-1212', $uri->getPath());
        $this->assertEquals(null, $uri->getQuery());
        $this->assertEquals(null, $uri->getFragment());
        $this->assertEquals([], $uri->getParameters());
        $this->assertEquals('tel:+1-816-555-1212', $uri->toString());
    }

    public function testRfcExample8()
    {
        $uri = Uri::parse('telnet://192.0.2.16:80/');

        $this->assertEquals('telnet', $uri->getScheme());
        $this->assertEquals('192.0.2.16:80', $uri->getAuthority());
        $this->assertEquals(null, $uri->getUserInfo());
        $this->assertEquals(null, $uri->getUser());
        $this->assertEquals(null, $uri->getPassword());
        $this->assertEquals('192.0.2.16', $uri->getHost());
        $this->assertEquals(80, $uri->getPort());
        $this->assertEquals('/', $uri->getPath());
        $this->assertEquals(null, $uri->getQuery());
        $this->assertEquals(null, $uri->getFragment());
        $this->assertEquals([], $uri->getParameters());
        $this->assertEquals('telnet://192.0.2.16:80/', $uri->toString());
    }

    public function testRfcExample9()
    {
        $uri = Uri::parse('urn:oasis:names:specification:docbook:dtd:xml:4.1.2');

        $this->assertEquals('urn', $uri->getScheme());
        $this->assertEquals(null, $uri->getAuthority());
        $this->assertEquals(null, $uri->getUserInfo());
        $this->assertEquals(null, $uri->getUser());
        $this->assertEquals(null, $uri->getPassword());
        $this->assertEquals(null, $uri->getHost());
        $this->assertEquals(null, $uri->getPort());
        $this->assertEquals('oasis:names:specification:docbook:dtd:xml:4.1.2', $uri->getPath());
        $this->assertEquals(null, $uri->getQuery());
        $this->assertEquals(null, $uri->getFragment());
        $this->assertEquals([], $uri->getParameters());
        $this->assertEquals('urn:oasis:names:specification:docbook:dtd:xml:4.1.2', $uri->toString());
    }

    public function testFull()
    {
        $uri = Uri::parse('foo://user:password@example.com:8042/over/there?name=ferret&foo=bar#nose');

        $this->assertEquals('foo', $uri->getScheme());
        $this->assertEquals('user:password@example.com:8042', $uri->getAuthority());
        $this->assertEquals('user:password', $uri->getUserInfo());
        $this->assertEquals('user', $uri->getUser());
        $this->assertEquals('password', $uri->getPassword());
        $this->assertEquals('example.com', $uri->getHost());
        $this->assertEquals(8042, $uri->getPort());
        $this->assertEquals('/over/there', $uri->getPath());
        $this->assertEquals('name=ferret&foo=bar', $uri->getQuery());
        $this->assertEquals('nose', $uri->getFragment());
        $this->assertEquals(['name' => 'ferret', 'foo' => 'bar'], $uri->getParameters());
        $this->assertEquals('foo://user:password@example.com:8042/over/there?name=ferret&foo=bar#nose', $uri->toString());
    }

    public function testRelativeUrl()
    {
        $uri = Uri::parse('/foo/bar?param=value');

        $this->assertEquals(null, $uri->getScheme());
        $this->assertEquals(null, $uri->getAuthority());
        $this->assertEquals(null, $uri->getUserInfo());
        $this->assertEquals(null, $uri->getHost());
        $this->assertEquals(null, $uri->getPort());
        $this->assertEquals('/foo/bar', $uri->getPath());
        $this->assertEquals('param=value', $uri->getQuery());
        $this->assertEquals(null, $uri->getFragment());
        $this->assertEquals(['param' => 'value'], $uri->getParameters());
        $this->assertEquals('/foo/bar?param=value', $uri->toString());
    }

    public function testUrlNoScheme()
    {
        $uri = Uri::parse('//google.com/foo/bar?foo=bar');

        $this->assertEquals(null, $uri->getScheme());
        $this->assertEquals('google.com', $uri->getAuthority());
        $this->assertEquals(null, $uri->getUserInfo());
        $this->assertEquals('google.com', $uri->getHost());
        $this->assertEquals(null, $uri->getPort());
        $this->assertEquals('/foo/bar', $uri->getPath());
        $this->assertEquals('foo=bar', $uri->getQuery());
        $this->assertEquals(null, $uri->getFragment());
        $this->assertEquals(['foo' => 'bar'], $uri->getParameters());
        $this->assertEquals('//google.com/foo/bar?foo=bar', $uri->toString());
    }

    public function testFileWindowsScheme()
    {
        $uri = Uri::parse('file:///c:/somedir/file.txt');

        $this->assertEquals('file', $uri->getScheme());
        $this->assertEquals(null, $uri->getAuthority());
        $this->assertEquals(null, $uri->getUserInfo());
        $this->assertEquals(null, $uri->getHost());
        $this->assertEquals(null, $uri->getPort());
        $this->assertEquals('/c:/somedir/file.txt', $uri->getPath());
        $this->assertEquals(null, $uri->getQuery());
        $this->assertEquals(null, $uri->getFragment());
        $this->assertEquals([], $uri->getParameters());
        $this->assertEquals('file:/c:/somedir/file.txt', $uri->toString());
    }

    public function testUrlEncode()
    {
        $uri = Uri::parse('http://foo.com?url=' . urlencode('http://google.de'));

        $this->assertEquals('http', $uri->getScheme());
        $this->assertEquals('foo.com', $uri->getAuthority());
        $this->assertEquals(null, $uri->getUserInfo());
        $this->assertEquals(null, $uri->getUser());
        $this->assertEquals(null, $uri->getPassword());
        $this->assertEquals('foo.com', $uri->getHost());
        $this->assertEquals(null, $uri->getPort());
        $this->assertEquals('', $uri->getPath());
        $this->assertEquals('url=http%3A%2F%2Fgoogle.de', $uri->getQuery());
        $this->assertEquals(null, $uri->getFragment());
        $this->assertEquals(['url' => 'http://google.de'], $uri->getParameters());
        $this->assertEquals('http://foo.com?url=http%3A%2F%2Fgoogle.de', $uri->toString());
    }

    public function testNoUrlEncode()
    {
        $uri = Uri::parse('http://foo.com?url=http://google.de');

        $this->assertEquals('http', $uri->getScheme());
        $this->assertEquals('foo.com', $uri->getAuthority());
        $this->assertEquals(null, $uri->getUserInfo());
        $this->assertEquals(null, $uri->getUser());
        $this->assertEquals(null, $uri->getPassword());
        $this->assertEquals('foo.com', $uri->getHost());
        $this->assertEquals(null, $uri->getPort());
        $this->assertEquals('', $uri->getPath());
        $this->assertEquals('url=http://google.de', $uri->getQuery());
        $this->assertEquals(null, $uri->getFragment());
        $this->assertEquals(['url' => 'http://google.de'], $uri->getParameters());
        $this->assertEquals('http://foo.com?url=http://google.de', $uri->toString());
    }

    public function testEmptyString()
    {
        $uri = Uri::parse('');

        $this->assertEquals('', $uri->toString());
    }

    public function testNonEmptyString()
    {
        $uri = Uri::parse('foo');

        $this->assertEquals('foo', $uri->toString());
    }

    /**
     * Test to check whether we can materialize the URI into an string form
     * without loosing any information
     *
     * @dataProvider toStringProvider
     */
    public function testToString($rawUri)
    {
        $uri = Uri::parse($rawUri);

        $this->assertEquals($rawUri, $uri->toString(), $rawUri);
    }

    public function toStringProvider()
    {
        return array(
            ['//www.yahoo.com'],
            ['/foo/bar/index.php?bar=test'],
            ['http://www.yahoo.com'],
            ['http://www.yahoo.com/'],
            ['http://www.yahoo.com/foo/bar'],
            ['http://www.yahoo.com?foo=bar&bar=foo'],
            ['http://www.yahoo.com:8080'],
            ['http://www.yahoo.com:8080/foo/bar'],
            ['http://www.yahoo.com:8080?foo=bar&bar=foo'],
            ['http://benutzername:passwort@hostname:8080/pfad?argument=wert#textanker'],
            ['ftp://ftp.is.co.za/rfc/rfc1808.txt'],
            ['http://www.ietf.org/rfc/rfc2396.txt'],
            ['ldap://[2001:db8::7]/c=GB?objectClass?one'],
            ['mailto:John.Doe@example.com'],
            ['news:comp.infosystems.www.servers.unix'],
            ['tel:+1-816-555-1212'],
            ['telnet://192.0.2.16:80/'],
            ['urn:oasis:names:specification:docbook:dtd:xml:4.1.2'],
        );
    }

    public function testSetScheme()
    {
        $uri = Uri::parse('https://www.yahoo.com');

        $this->assertEquals('https', $uri->getScheme());
        $this->assertEquals('https://www.yahoo.com', $uri->toString());
    }

    public function testSetUser()
    {
        $uri = Uri::parse('http://foo@www.yahoo.com');

        $this->assertEquals('foo', $uri->getUserInfo());
        $this->assertEquals('foo', $uri->getUser());
        $this->assertEquals('http://foo@www.yahoo.com', $uri->toString());
    }

    public function testSetPassword()
    {
        $uri = Uri::parse('http://www.yahoo.com');

        $this->assertEquals(null, $uri->getPassword());
        $this->assertEquals('http://www.yahoo.com', $uri->toString());
    }

    public function testSetUserPassword()
    {
        $uri = Uri::parse('http://foo:bar@www.yahoo.com');

        $this->assertEquals('foo:bar', $uri->getUserInfo());
        $this->assertEquals('foo', $uri->getUser());
        $this->assertEquals('bar', $uri->getPassword());
        $this->assertEquals('http://foo:bar@www.yahoo.com', $uri->toString());
    }

    public function testSetHost()
    {
        $uri = Uri::parse('http://google.com');

        $this->assertEquals('google.com', $uri->getAuthority());
        $this->assertEquals('google.com', $uri->getHost());
        $this->assertEquals(null, $uri->getPort());
        $this->assertEquals('http://google.com', $uri->toString());
    }

    public function testSetPort()
    {
        $uri = Uri::parse('http://www.yahoo.com:8080');

        $this->assertEquals('www.yahoo.com:8080', $uri->getAuthority());
        $this->assertEquals('www.yahoo.com', $uri->getHost());
        $this->assertEquals(8080, $uri->getPort());
        $this->assertEquals('http://www.yahoo.com:8080', $uri->toString());
    }

    public function testSetPath()
    {
        $uri = Uri::parse('http://www.yahoo.com/foo');

        $this->assertEquals('/foo', $uri->getPath());
        $this->assertEquals('http://www.yahoo.com/foo', $uri->toString());
    }

    public function testSetQuery()
    {
        $uri = Uri::parse('http://www.yahoo.com/?foo=bar&bar=foo');

        $this->assertEquals('foo=bar&bar=foo', $uri->getQuery());
        $this->assertEquals(['foo' => 'bar', 'bar' => 'foo'], $uri->getParameters());
        $this->assertEquals('http://www.yahoo.com/?foo=bar&bar=foo', $uri->toString());
    }

    public function testIsAbsolute()
    {
        $uri = Uri::parse('http://www.yahoo.com/foo?foo=bar&bar=foo');
        $this->assertTrue($uri->isAbsolute());

        $uri = Uri::parse('/foo?foo=bar&bar=foo');
        $this->assertFalse($uri->isAbsolute());
    }

    public function testGetParameters()
    {
        $uri = Uri::parse('http://www.yahoo.com/foo?foo=bar&bar=foo');

        $this->assertEquals(['foo' => 'bar', 'bar' => 'foo'], $uri->getParameters());
    }

    public function testGetParameter()
    {
        $uri = Uri::parse('http://www.yahoo.com/foo?foo=bar&bar=foo');

        $this->assertEquals('bar', $uri->getParameter('foo'));
    }

    public function testSetFragment()
    {
        $uri = Uri::parse('http://www.yahoo.com/#foo');

        $this->assertEquals('foo', $uri->getFragment());
        $this->assertEquals('http://www.yahoo.com/#foo', $uri->toString());
    }

    public function testWithScheme()
    {
        $uri = Uri::parse('http://www.yahoo.com');

        $this->assertEquals('https://www.yahoo.com', $uri->withScheme('https')->toString());

        $uri = Uri::parse('http://user:password@example.com:8042/over/there?name=ferret&foo=bar#nose');

        $this->assertEquals('https://user:password@example.com:8042/over/there?name=ferret&foo=bar#nose', $uri->withScheme('https')->toString());
    }

    public function testWithAuthority()
    {
        $uri = Uri::parse('http://www.yahoo.com');

        $this->assertEquals('http://google.com', $uri->withAuthority('google.com')->toString());

        $uri = Uri::parse('http://user:password@example.com:8042/over/there?name=ferret&foo=bar#nose');

        $this->assertEquals('http://google.com/over/there?name=ferret&foo=bar#nose', $uri->withAuthority('google.com')->toString());
    }

    public function testWithPath()
    {
        $uri = Uri::parse('http://www.yahoo.com/foo/bar');

        $this->assertEquals('http://www.yahoo.com/bar', $uri->withPath('/bar')->toString());

        $uri = Uri::parse('http://user:password@example.com:8042/over/there?name=ferret&foo=bar#nose');

        $this->assertEquals('http://user:password@example.com:8042/bar?name=ferret&foo=bar#nose', $uri->withPath('/bar')->toString());
    }

    public function testWithQuery()
    {
        $uri = Uri::parse('http://www.yahoo.com/?foo=bar');

        $this->assertEquals(['foo' => 'bar'], $uri->getParameters());

        $uri = $uri->withQuery('bar=foo');

        $this->assertEquals(['bar' => 'foo'], $uri->getParameters());
        $this->assertEquals('http://www.yahoo.com/?bar=foo', $uri->toString());

        $uri = Uri::parse('http://user:password@example.com:8042/over/there?name=ferret&foo=bar#nose');

        $this->assertEquals('http://user:password@example.com:8042/over/there?bar=foo#nose', $uri->withQuery('bar=foo')->toString());
    }

    public function testWithFragment()
    {
        $uri = Uri::parse('http://www.yahoo.com/#foo');

        $this->assertEquals('http://www.yahoo.com/#bar', $uri->withFragment('bar')->toString());

        $uri = Uri::parse('http://user:password@example.com:8042/over/there?name=ferret&foo=bar#nose');

        $this->assertEquals('http://user:password@example.com:8042/over/there?name=ferret&foo=bar#bar', $uri->withFragment('bar')->toString());
    }

    public function testWithParameters()
    {
        $uri = Uri::parse('http://www.yahoo.com/?foo=bar');

        $this->assertEquals(['foo' => 'bar'], $uri->getParameters());

        $uri = $uri->withParameters(['bar' => 'foo']);

        $this->assertEquals(['bar' => 'foo'], $uri->getParameters());
        $this->assertEquals('http://www.yahoo.com/?bar=foo', $uri->toString());

        $uri = Uri::parse('http://user:password@example.com:8042/over/there?name=ferret&foo=bar#nose');

        $this->assertEquals('http://user:password@example.com:8042/over/there?bar=foo#nose', $uri->withParameters(['bar' => 'foo'])->toString());
    }

    public function testWithUserInfo()
    {
        $uri = Uri::parse('http://www.yahoo.com/');

        $this->assertEquals('http://foo@www.yahoo.com/', $uri->withUserInfo('foo')->toString());
        $this->assertEquals('http://foo:bar@www.yahoo.com/', $uri->withUserInfo('foo', 'bar')->toString());

        $uri = Uri::parse('http://user:password@example.com:8042/over/there?name=ferret&foo=bar#nose');

        $this->assertEquals('http://example.com:8042/over/there?name=ferret&foo=bar#nose', $uri->withUserInfo('')->toString());
        $this->assertEquals('http://foo@example.com:8042/over/there?name=ferret&foo=bar#nose', $uri->withUserInfo('foo')->toString());
        $this->assertEquals('http://bar:foo@example.com:8042/over/there?name=ferret&foo=bar#nose', $uri->withUserInfo('bar', 'foo')->toString());
    }

    public function testWithHost()
    {
        $uri = Uri::parse('http://www.yahoo.com/');

        $this->assertEquals('http://google.com/', $uri->withHost('google.com')->toString());

        $uri = Uri::parse('http://user:password@example.com:8042/over/there?name=ferret&foo=bar#nose');

        $this->assertEquals('http://user:password@google.com:8042/over/there?name=ferret&foo=bar#nose', $uri->withHost('google.com')->toString());

        $uri = Uri::parse('http://www.yahoo.com/foo/');

        $this->assertEquals('http:/foo/', $uri->withHost(null)->toString());
    }

    public function testWithPort()
    {
        $uri = Uri::parse('http://www.yahoo.com/');

        $this->assertEquals('http://www.yahoo.com:8080/', $uri->withPort(8080)->toString());

        $uri = Uri::parse('http://user:password@example.com:8042/over/there?name=ferret&foo=bar#nose');

        $this->assertEquals('http://user:password@example.com:8080/over/there?name=ferret&foo=bar#nose', $uri->withPort(8080)->toString());
    }

    public function test__toString()
    {
        $uri = Uri::parse('http://www.yahoo.com/');

        $this->assertEquals('http://www.yahoo.com/', (string) $uri);
    }
}
