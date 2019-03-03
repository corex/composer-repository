<?php

namespace CoRex\Composer\Repository\Helpers;

use CoRex\Helpers\Str;

class Input
{
    // Standard schemes.
    private static $schemes = [
        'http' => 80,
        'https' => 443,
        'ftp' => 21
    ];

    /**
     * Get url.
     *
     * @param boolean $addPath Default false.
     * @return string
     */
    public static function getBaseUrl($addPath = false)
    {
        $url = 'http';
        if (self::isSsl()) {
            $url .= 's';
        }
        $url .= '://' . self::getHost();
        if ($addPath) {
            $path = self::getPath();
            if ($path != '') {
                $url .= '/' . $path;
            }
        }
        return $url;
    }

    /**
     * Get uri.
     *
     * @return string
     */
    public static function getUri()
    {
        // Set basic protocol.
        $parts = [$uri = self::getScheme(), '://'];

        // Set auth username and auth password.
        $authUsername = self::getAuthUsername();
        $authPassword = self::getAuthPassword();
        if ($authUsername !== null && $authPassword !== null) {
            $parts[] = $authUsername . ':' . $authPassword . '@';
        }

        // Set host.
        $parts[] = self::getHost();

        // Set port.
        $port = self::getPort();
        if ($port != self::getStandardPort(self::getScheme())) {
            $parts[] = ':' . $port;
        }

        // Set path.
        $path = self::getPath();
        if ($path !== null && $path != '') {
            $parts[] = '/' . $path;
        }

        // Set query.
        $query = Input::getQuery();
        if (is_array($query) && count($query) > 0) {
            $queryString = [];
            foreach ($query as $key => $value) {
                $queryKeyValue = $key . '=';
                if ($value !== null) {
                    if (is_string($value)) {
                        $value = urlencode($value);
                    }
                    $queryKeyValue .= $value;
                }
                $queryString[] = $queryKeyValue;
            }
            if (count($queryString) > 0) {
                $parts[] = '?' . implode('&', $queryString);
            }
        }

        return implode('', $parts);
    }

    /**
     * Get host.
     *
     * @return string
     */
    public static function getHost()
    {
        $host = null;
        if (isset($_SERVER['HTTP_X_FORWARDED_HOST'])) {
            $host = $_SERVER['HTTP_X_FORWARDED_HOST'];
        } elseif (isset($_SERVER['HTTP_HOST'])) {
            $host = $_SERVER['HTTP_HOST'];
        } elseif (isset($_SERVER['SERVER_NAME'])) {
            $host = $_SERVER['SERVER_NAME'];
        } else {
            $host = gethostname();
        }
        return $host;
    }

    /**
     * Get port (default 80).
     *
     * @return integer
     */
    public static function getPort()
    {
        $port = 0;
        if (isset($_SERVER['SERVER_PORT'])) {
            $port = intval($_SERVER['SERVER_PORT']);
        }
        if ($port == 0) {
            $port = self::getStandardPort();
        }
        return $port;
    }

    /**
     * Get standard port. If not found, then 0.
     *
     * @param string $scheme
     * @return integer
     */
    public static function getStandardPort($scheme = null)
    {
        if ($scheme === null) {
            $scheme = self::getScheme();
        }
        if (isset(self::$schemes[$scheme])) {
            return self::$schemes[$scheme];
        }
        return 0;
    }

    /**
     * Get domain.
     *
     * @return string
     */
    public static function getDomain()
    {
        return self::getHost();
    }

    /**
     * Get method.
     * @param boolean $strLower Default false.
     *
     * @return string
     */
    public static function getMethod($strLower = false)
    {
        $method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET';
        if ($strLower) {
            $method = strtolower($method);
        } else {
            $method = strtoupper($method);
        }
        return $method;
    }

    /**
     * Get scheme.
     *
     * @return string
     */
    public static function getScheme()
    {
        $protocol = isset($_SERVER['REQUEST_SCHEME']) ? strtolower($_SERVER['REQUEST_SCHEME']) : 'http';
        if ($protocol == 'http' && self::isSsl()) {
            $protocol .= 's';
        }
        return $protocol;
    }

    /**
     * Is ssl.
     *
     * @return boolean
     */
    public static function isSsl()
    {
        $isSecure = false;
        if (isset($_SERVER['HTTPS']) && in_array($_SERVER['HTTPS'], ['on', '1'])) {
            $isSecure = true;
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
            $isSecure = true;
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on') {
            $isSecure = true;
        }
        return $isSecure;
    }

    /**
     * Get user-agent.
     *
     * @return string
     */
    public static function getUserAgent()
    {
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            return $_SERVER['HTTP_USER_AGENT'];
        }
        return '';
    }

    /**
     * Get remote ip.
     *
     * @return string
     */
    public static function getRemoteIp()
    {
        if (isset($_SERVER['REMOTE_ADDR'])) {
            return $_SERVER['REMOTE_ADDR'];
        }
        return '';
    }

    /**
     * Get path.
     *
     * @return string
     */
    public static function getPath()
    {
        $uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
        if (strpos($uri, '?') > 0) {
            $uri = substr($uri, 0, strpos($uri, '?'));
        }
        $uri = preg_replace("/^\\/(.*)$/", "$1", $uri);
        $uri = preg_replace("/^(.*)\\/$/", "$1", $uri);
        return $uri;
    }

    /**
     * Split uri into key/value.
     *
     * @param array $keys
     * @return array
     */
    public static function getPathSegments(array $keys)
    {
        if (count($keys) == 0) {
            return [];
        }
        return Str::splitIntoKeyValue(self::getPath(), '/', $keys);
    }

    /**
     * Get query.
     *
     * @param string $name Default '' which means all.
     * @param mixed $defaultValue Default null.
     * @return array|mixed
     */
    public static function getQuery($name = '', $defaultValue = null)
    {
        $queryStringParts = [];
        if (isset($_SERVER['QUERY_STRING'])) {
            parse_str($_SERVER['QUERY_STRING'], $queryStringParts);
        }
        if ($name != '') {
            if (isset($queryStringParts[$name])) {
                return $queryStringParts[$name];
            } else {
                return $defaultValue;
            }
        }
        return $queryStringParts;
    }

    /**
     * Get query string.
     *
     * @return string
     */
    public static function getQueryString()
    {
        return isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '';
    }

    /**
     * Get request.
     *
     * @param string $name
     * @param mixed $defaultValue Default null.
     * @return mixed
     */
    public static function getRequest($name, $defaultValue = null)
    {
        if (isset($_REQUEST[$name])) {
            return $_REQUEST[$name];
        }
        return $defaultValue;
    }

    /**
     * Set request.
     *
     * @param string $name
     * @param mixed $value
     */
    public static function setRequest($name, $value)
    {
        $_REQUEST[$name] = $value;
    }

    /**
     * Unset request.
     *
     * @param string $name
     */
    public static function unsetRequest($name)
    {
        if (self::requestExist($name)) {
            unset($_REQUEST[$name]);
        }
    }

    /**
     * Request exist.
     *
     * @param string $name
     * @return boolean
     */
    public static function requestExist($name)
    {
        return isset($_REQUEST[$name]);
    }

    /**
     * Get headers.
     *
     * @return array|false
     */
    public static function getHeaders()
    {
        $headers = [];
        if (function_exists('getallheaders')) {
            // @codeCoverageIgnoreStart
            $headers = getallheaders();
            // @codeCoverageIgnoreEnd
        } else {
            foreach ($_SERVER as $name => $value) {
                if (substr($name, 0, 5) == 'HTTP_') {
                    $header = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))));
                    $headers[$header] = $value;
                }
            }
        }
        return $headers;
    }

    /**
     * Get header.
     *
     * @param string $header
     * @param string $defaultValue
     * @return mixed|string
     */
    public static function getHeader($header, $defaultValue = '')
    {
        $header = strtolower(str_replace(['_', ' '], '-', $header));
        $headers = self::getHeaders();
        if (is_array($headers)) {
            foreach ($headers as $name => $value) {
                if (strtolower($name) == $header) {
                    return $value;
                }
            }
        }
        return $defaultValue;
    }

    /**
     * Get header Content-Type.
     *
     * @param string $defaultValue Default ''.
     * @return mixed|string
     */
    public static function getHeaderContentType($defaultValue = '')
    {
        return self::getHeader('Content-Type', $defaultValue);
    }

    /**
     * Get header Accept.
     *
     * @param string $defaultValue Default ''.
     * @return mixed|string
     */
    public static function getHeaderAccept($defaultValue = '')
    {
        return self::getHeader('Accept', $defaultValue);
    }

    /**
     * Is headers sent.
     *
     * @return boolean
     */
    public static function isHeadersSent()
    {
        return strlen(ob_get_contents()) > 0;
    }

    /**
     * Return body.
     *
     * @return string
     */
    public static function getBody()
    {
        // @codeCoverageIgnoreStart
        return file_get_contents('php://input');
        // @codeCoverageIgnoreEnd
    }

    /**
     * Get auth username.
     *
     * @return string|null
     */
    public static function getAuthUsername()
    {
        $result = null;
        if (isset($_SERVER['PHP_AUTH_USER'])) {
            $result = $_SERVER['PHP_AUTH_USER'];
        }
        if (trim($result) == '') {
            $result = null;
        }
        return $result;
    }

    /**
     * Get auth password.
     *
     * @return string|null
     */
    public static function getAuthPassword()
    {
        $result = null;
        if (isset($_SERVER['PHP_AUTH_PW'])) {
            $result = $_SERVER['PHP_AUTH_PW'];
        }
        if (trim($result) == '') {
            $result = null;
        }
        return $result;
    }
}