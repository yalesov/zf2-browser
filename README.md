# zf2-browser

[![Build Status](https://secure.travis-ci.org/yalesov/zf2-browser.png)](http://travis-ci.org/yalesov/zf2-browser)

web browser with quick browse methods using zf2's HTTP Client and cURL

# Installation

[Composer](http://getcomposer.org/):

```json
{
    "require": {
        "yalesov/zf2-browser": "1.*"
    }
}
```

Copy `config/browser.local.php.dist` into `(app root)/config/autoload/browser.local.php`, and edit configs as described below.

# Config

The `browser` alias can be changed to anything you like.

- `cookieDir`: directory for storing cookies. Make sure you create this directory, and that it is writable by `www-data` (or whatever your PHP scripts run as); or at least that its parent directory is writable (the script will create this dir for you in this case).
- `cookieLife`: lifetime for cookie files (minute)
- `connectTimeout`: max time to wait when connecting (second)
- `options`: wrapper for `\Zend\Http\Client::setOptions()`
- `headers`: wrapper for `\Zend\Http\Client::setHeaders()`

# Usage

Get a browser instance

```php
// $locator instanceof ServiceLocator
$browser = $locator->get('browser')->newInstance();
```

`GET` a page

```php
$responseBody = $browser->get('http://example.com');
```

`POST` to a page with param `foo` = `bar`

```php
$responseBody = $browser->post('http://example.com', array('foo' => 'bar'));
```
