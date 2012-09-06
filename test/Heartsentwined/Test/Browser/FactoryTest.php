<?php
namespace Heartsentwined\Test\Browser;

use Heartsentwined\Browser\Factory;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->factory = new Factory;
        mkdir('tmp');
        $this->factory
            ->setCookieDir('tmp');
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

    public function testCookie()
    {
        $this->factory
            ->newInstance();

        $fileCount = 0;
        $handle = opendir('tmp');
        while (($file = readdir($handle)) !== false) {
            if ($file === '.' || $file === '..') continue;
            $fileCount++;
        }
        closedir($handle);

        $this->assertSame(1, $fileCount);
    }

    public function testRemoveOldCookie()
    {
        $fh = fopen('tmp/deleted', 'x+');
        touch('tmp/deleted', time()-10*60);
        fclose($fh);

        $fh = fopen('tmp/remaining', 'x+');
        touch('tmp/remaining', time()-5*60);
        fclose($fh);

        $this->factory
            ->setCookieLife(10);
        unset($this->factory); //trigger destructor

        $deletedCount = 0;
        $remainingCount = 0;
        $handle = opendir('tmp');
        while (($file = readdir($handle)) !== false) {
            if ($file === '.' || $file === '..') continue;
            if (strpos($file, 'deleted') !== false) {
                $deletedCount++;
            }
            if (strpos($file, 'remaining') !== false) {
                $remainingCount++;
            }
            unlink("tmp/$file");
        }
        closedir($handle);

        $this->assertSame(0, $deletedCount);
        $this->assertSame(1, $remainingCount);
    }
}
