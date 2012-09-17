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
        }
    }

    public function testGet()
    {
        $body = $this->browser->get('http://google.com');
        $this->assertNotEmpty($body);

        $body = $this->browser->get('http://1.com');
        $this->assertSame('', $body);

        $body = $this->browser->get('');
        $this->assertSame('', $body);

        $body = $this->browser->get('1');
        $this->assertSame('', $body);
    }

    public function testIndirectGet()
    {
        $this->browser->setUri('http://google.com');
        $body = $this->browser->get();
        $this->assertNotEmpty($body);

        $this->browser->setUri('http://1.com');
        $body = $this->browser->get();
        $this->assertSame('', $body);

        $this->browser->setUri('');
        $body = $this->browser->get();
        $this->assertSame('', $body);

        $this->browser->setUri('1');
        $body = $this->browser->get();
        $this->assertSame('', $body);
    }

    public function testPost()
    {
        $body = $this->browser->post('http://google.com', array());
        $this->assertNotEmpty($body);

        $body = $this->browser->post('http://1.com', array());
        $this->assertSame('', $body);

        $body = $this->browser->post('', array());
        $this->assertSame('', $body);

        $body = $this->browser->post('1', array());
        $this->assertSame('', $body);
    }

    public function testIndirectPost()
    {
        $this->browser->setUri('http://google.com');
        $body = $this->browser->post('', array());
        $this->assertNotEmpty($body);

        $this->browser->setUri('http://1.com');
        $body = $this->browser->post('', array());
        $this->assertSame('', $body);

        $this->browser->setUri('');
        $body = $this->browser->post('', array());
        $this->assertSame('', $body);

        $this->browser->setUri('1');
        $body = $this->browser->post('', array());
        $this->assertSame('', $body);
    }
}
