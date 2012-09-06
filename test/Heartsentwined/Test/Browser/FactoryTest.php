<?php
namespace Heartsentwined\Test\Browser;

use Heartsentwined\Browser\Factory;

class FactoryTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->factory = new Factory;
    }

    public function testCookie()
    {
        mkdir('tmp');
        $this->factory
            ->setCookieDir('tmp')
            ->newInstance();

        $fileCount = 0;
        $handle = opendir('tmp');
        while (($file = readdir($handle)) !== false) {
            unlink("tmp/$file");
            $fileCount++;
        }
        closedir($handle);
        rmdir('tmp');

        $this->assertSame(1, $fileCount);
    }

    public function testRemoveOldCookie()
    {
        mkdir('tmp');

        $fh = fopen('tmp/deleted', 'x+');
        touch('tmp/deleted', time()-10*60);
        fclose($fh);

        $fh = fopen('tmp/remaining', 'x+');
        touch('tmp/remaining', time()-5*60);
        fclose($fh);

        $this->factory
            ->setCookieDir('tmp')
            ->setCookieLife(10);
        unset($this->factory); //trigger destructor

        $deletedCount = 0;
        $remainingCount = 0;
        $handle = opendir('tmp');
        while (($file = readdir($handle)) !== false) {
            if (strpos($file, 'deleted') !== false) {
                $deletedCount++;
            }
            if (strpos($file, 'remaining') !== false) {
                $remainingCount++;
            }
            unlink("tmp/$file");
        }
        closedir($handle);
        rmdir('tmp');

        $this->assertSame(0, $deletedCount);
        $this->assertSame(1, $remainingCount);
    }
}
