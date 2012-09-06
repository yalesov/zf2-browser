<?php
namespace Heartsentwined\Browser;

use Heartsentwined\ArgValidator\ArgValidator;
use Heartsentwined\Browser\Exception;

/**
 * factory class for browser client
 */
class Factory
{
    protected $cookieDir;
    protected $cookieLife;
    protected $connectTimeout;
    protected $options;
    protected $headers;

    protected $wd;

    /**
     * directory for storing cookies
     *
     * @param string $cookieDir
     * @return $this
     */
    public function setCookieDir($cookieDir)
    {
        ArgValidator::assert($cookieDir, 'string');
        $this->cookieDir = $cookieDir;
        return $this;
    }

    /**
     * getCookieDir
     *
     * @return string
     */
    public function getCookieDir()
    {
        return $this->cookieDir;
    }

    /**
     * lifetime for cookie files
     *
     * @param int $cookieLife (minute)
     * @return $this
     */
    public function setCookieLife($cookieLife)
    {
        ArgValidator::assert($cookieLife, 'int');
        $this->cookieLife = $cookieLife;
        return $this;
    }

    /**
     * getCookieLife
     *
     * @return int
     */
    public function getCookieLife()
    {
        return $this->cookieLife;
    }

    /**
     * max time to wait when connecting
     *
     * @param int $connectTimeout (second)
     * @return $this
     */
    public function setConnectTimeout($connectTimeout)
    {
        ArgValidator::assert($connectTimeout, 'int');
        $this->connectTimeout = $connectTimeout;
        return $this;
    }

    /**
     * getConnectTimeout
     *
     * @return int
     */
    public function getConnectTimeout()
    {
        return $this->connectTimeout;
    }

    /**
     * @see \Zend\Http\Client::setOptions()
     *
     * @param \Traversable $options
     * @return $this
     */
    public function setOptions(\Traversable $options)
    {
        $this->options = $options;
        return $this;
    }

    /**
     * getOptions
     *
     * @return \Traversable
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @see \Zend\Http\Client::setHeaders()
     *
     * @param \Traversable $headers
     * @return $this
     */
    public function setHeaders(\Traversable $headers)
    {
        $this->headers = $headers;
        return $this;
    }

    /**
     * getHeaders
     *
     * @return \Traversable
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * working directory
     *
     * @param string $wd
     * @return $this
     */
    public function setWd($wd)
    {
        ArgValidator::assert($wd, 'string');
        $this->wd = $wd;
        return $this;
    }

    /**
     * getWd
     *
     * @return string
     */
    public function getWd()
    {
        return $this->wd;
    }

    /**
     * get a new browser instance
     * useful if you want to clear previous state before browsing
     *
     * @return Browser
     */
    public function newInstance()
    {
        ArgValidator::assert($this->getCookieDir(), array('string', 'min' => 1));

        $browser = new Browser;

        if ($options = $this->getOptions() && count($options)) {
            $browser->setOptions($options);
        }
        if ($headers = $this->getHeaders() && count($headers)) {
            $browser->setHeaders($headers);
        }

        //sets adapter here to force-load it, in order to set CURL opts
        $this->setAdapter('Zend\Http\Client\Adapter\Curl');
        $adapter = $this->getAdapter();

        if ($connectTimeout = $this->getConnectTimeout()) {
            $adapter->setCurlOption(CURLOPT_CONNECTTIMEOUT, $connectTimeout);
        }

        $this->setWd(getcwd());

        //random cookie file
        $cookieFile = $this->getCookieDir() . '/' . md5(rand().microtime(true));
        $fh = fopen($cookieFile, 'x+');
        fclose($fh);

        $adapter->setCurlOption(CURLOPT_COOKIEJAR, $cookieFile);
        $adapter->setCurlOption(CURLOPT_COOKIEFILE, $cookieFile);

        return $browser;
    }

    public function __destruct()
    {
        clearstatcache();
        try {
            $wd = getcwd();
            chdir($this->getWd());
            $cookieDir = $this->getCookieDir();
            if ($handle = opendir($cookieDir)) {
                while (($file = readdir($handle)) !== false) {
                    if (filemtime("$cookieDir/$file")
                        <= time() - 60 * $this->getCookieLife()) {
                        @unlink("$cookieDir/$file");
                    }
                }
            }
            closedir($handle);
            chdir($wd);
        } catch (\Exception $e) {
        }
    }
}
