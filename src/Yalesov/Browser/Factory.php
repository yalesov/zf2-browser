<?php
namespace Yalesov\Browser;

use Yalesov\ArgValidator\ArgValidator;
use Yalesov\Browser\Exception;

/**
 * factory class for browser client
 */
class Factory
{
  protected $cookieDir = 'data/tmp/cookie';
  protected $cookieLife = 1440;
  protected $connectTimeout = 120;
  protected $options = array();
  protected $headers = array();

  protected $wd;

  /**
   * directory for storing cookies
   *
   * @param  string $cookieDir
   * @return self
   */
  public function setCookieDir($cookieDir)
  {
    ArgValidator::assert($cookieDir, array('string', 'min' => 1));
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
   * @param  int  $cookieLife (minute)
   * @return self
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
   * @param  int  $connectTimeout (second)
   * @return self
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
   * @param  array $options
   * @return self
   */
  public function setOptions(array $options)
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
   * @param  array $headers
   * @return self
   */
  public function setHeaders(array $headers)
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
    $browser->setAdapter('Zend\Http\Client\Adapter\Curl');
    $adapter = $browser->getAdapter();

    if ($connectTimeout = $this->getConnectTimeout()) {
      $adapter->setCurlOption(CURLOPT_CONNECTTIMEOUT, $connectTimeout);
    }

    $cookieDir = $this->getCookieDir();
    if (!is_dir($cookieDir)) mkdir($cookieDir, 0755, true);

    //random cookie file
    $cookieFile = $cookieDir . '/' . md5(rand().microtime(true));
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
      $cookieDir = $this->getCookieDir();
      if (is_dir($cookieDir) && $handle = opendir($cookieDir)) {
        while (($file = readdir($handle)) !== false) {
          if ($file === '.' || $file === '..') continue;
          if (filemtime("$cookieDir/$file")
            <= time() - 60 * $this->getCookieLife()) {
            unlink("$cookieDir/$file");
          }
        }
        closedir($handle);
      }
    } catch (\Exception $e) {
    }
  }
}
