<?php
namespace Heartsentwined\Test\Browser;

use Heartsentwined\Browser\Factory;
use Heartsentwined\Browser\Exception;

class BrowserTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        if (!is_dir('tmp')) mkdir('tmp');
        $factory = new Factory;
        $factory
            ->setCookieDir('tmp')
            ->setCookieLife(2)
            ->setConnectTimeout(120);
        $this->browser = $factory->newInstance();
    }

    public function tearDown()
    {
        if ($handle = opendir('tmp')) {
            while (($file = readdir($handle)) !== false) {
                if ($file === '.' || $file === '..') continue;
                unlink("tmp/$file");
            }
            closedir($handle);
            rmdir('tmp');
        }
    }

    public function testGet()
    {
        $body = $this->browser->get('http://google.com');
        $this->assertNotEmpty($body);

        $body = $this->browser->get('http://example.test');
        $this->assertSame('', $body);

        $body = $this->browser->get('');
        $this->assertSame('', $body);

        $body = $this->browser->get('example');
        $this->assertSame('', $body);
    }

    public function testIndirectGet()
    {
        $this->browser->setUri('http://google.com');
        $body = $this->browser->get();
        $this->assertNotEmpty($body);

        $this->browser->setUri('http://example.test');
        $body = $this->browser->get();
        $this->assertSame('', $body);

        $this->browser->setUri('');
        $body = $this->browser->get();
        $this->assertSame('', $body);

        $this->browser->setUri('example');
        $body = $this->browser->get();
        $this->assertSame('', $body);
    }

    public function testPost()
    {
        $body = $this->browser->post('http://google.com', array());
        $this->assertNotEmpty($body);

        $body = $this->browser->post('http://example.test', array());
        $this->assertSame('', $body);

        $body = $this->browser->post('', array());
        $this->assertSame('', $body);

        $body = $this->browser->post('example', array());
        $this->assertSame('', $body);
    }

    public function testIndirectPost()
    {
        $this->browser->setUri('http://google.com');
        $body = $this->browser->post('', array());
        $this->assertNotEmpty($body);

        $this->browser->setUri('http://example.test');
        $body = $this->browser->post('', array());
        $this->assertSame('', $body);

        $this->browser->setUri('');
        $body = $this->browser->post('', array());
        $this->assertSame('', $body);

        $this->browser->setUri('example');
        $body = $this->browser->post('', array());
        $this->assertSame('', $body);
    }
}
