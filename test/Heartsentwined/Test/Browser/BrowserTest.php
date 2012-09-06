<?php
namespace Heartsentwined\Test\Browser;

use Heartsentwined\Browser\Factory;
use Heartsentwined\Browser\Exception;

class BrowserTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        mkdir('tmp');
        $factory = new Factory;
        $factory
            ->setCookieDir('tmp')
            ->setCookieLife(2)
            ->setConnectTimeout(120);
        $this->browser = $factory->newInstance();
    }

    public function tearDown()
    {
        $handle = opendir('tmp');
        while (($file = readdir($handle)) !== false) {
            if ($file === '.' || $file === '..') continue;
            unlink("tmp/$file");
        }
        closedir($handle);
        rmdir('tmp');
    }

    public function testGet()
    {
        $body = $this->browser->get('http://google.com');
        $this->assertNotEmpty($body);
    }

    public function testIndirectGet()
    {
        $this->browser->setUri('http://google.com');
        $body = $this->browser->get();
        $this->assertNotEmpty($body);
    }

    public function testPost()
    {
        $body = $this->browser->post('http://google.com', array());
        $this->assertNotEmpty($body);
    }

    public function testIndirectPost()
    {
        $this->browser->setUri('http://google.com');
        $body = $this->browser->post('', array());
        $this->assertNotEmpty($body);
    }
}
