<?php
namespace Heartsentwined\Browser;

use Heartsentwined\ArgValidator\ArgValidator;
use Heartsentwined\Browser\Exception;
use Zend\Http\Client;

/**
 * extended browser client
 */
class Browser extends Client
{
    /**
     * quick method to GET a page
     *
     * @param string $url
     * @return string response body
     */
    public function get($url = '')
    {
        ArgValidator::assert($url, 'string');
        if (!empty($url)) {
            $this->setUri($url);
        }
        ArgValidator::assert($this->getUri(), array('string', 'min' => 1));

        try {
            $oriMethod = $this->getMethod();
            $this->setMethod('GET');

            $body = $this->send()->getBody();

            $this->setMethod($oriMethod);

            return $body;
        } catch (\Exception $e) {
            return '';
        }
    }

    /**
     * quick method to POST to a page
     *
     * @param string $url
     * @param array $params
     * @return string response body
     */
    public function post($url = '', array $params = array())
    {
        ArgValidator::assert($url, 'string');
        if (!empty($url)) {
            $this->setUri($url);
        }
        ArgValidator::assert($this->getUri(), array('string', 'min' => 1));

        try {
            $oriMethod = $this->getMethod();
            $this->setMethod('POST');
            $this->setParameterPost($params);

            $body = $this->send()->getBody();

            $this->setMethod($oriMethod);

            return $body;
        } catch (\Exception $e) {
            return '';
        }
    }
}
