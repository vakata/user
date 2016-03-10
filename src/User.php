<?php
namespace vakata\user;

use vakata\jwt\JWT;
use vakata\database\DatabaseInterface;

class User
{
    protected $id;
    protected $data = [];

    public static function signToken(JWT $token, $key, $issuer = null, array $rules = [])
    {
        $rules = array_merge([
            'ipAddress' => true,
            'userAgent' => true,
            'sessionID' => true
        ], $rules);
        if ($issuer) {
            $token->setIssuer($issuer);
        }
        if ($rules['ipAddress']) {
            $token->setClaim('ip', static::ipAddress());
        }
        if ($rules['userAgent']) {
            $token->setClaim('ua', static::userAgent());
        }
        if ($rules['sessionID']) {
            $token->setClaim('sess', session_id());
        }
        $token->sign($key);
        return (string)$token;
    }
    public static function verifyToken(JWT $token, $key, $issuer = null)
    {
        if (!$token->isSigned()) {
            throw new userException('Token not signed');
        }
        $verify = [];
        if ($issuer) {
            $verify['iss'] = $issuer;
        }
        if ($token->getClaim('ip')) {
            $verify['ip'] = static::ipAddress();
        }
        if ($token->getClaim('ua')) {
            $verify['ua'] = static::userAgent();
        }
        if ($token->getClaim('session')) {
            $verify['sess'] = session_id();
        }
        if (!$token->isValid($verify)) {
            throw new userException('Token not valid');
        }
        if (!$token->verifyHash($this->key)) {
            throw new userException('Invalid token signature');
        }
        return [
            'provider' => $token->getClaim('provider'),
            'id'       => $token->getClaim('id'),
            'mail'     => $token->getClaim('mail'),
            'name'     => $token->getClaim('name')
        ];
    }
    /**
     * get the client's IP address
     * @method ipAddress
     * @return string               the client's IP
     * @codeCoverageIgnore
     */
    public static function ipAddress()
    {
        $ip = '0.0.0.0';
        // TODO: check if remote_addr is a cloudflare one and only then read the connecting ip
        // https://www.cloudflare.com/ips-v4
        // https://www.cloudflare.com/ips-v6
        if (false && isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
            $ip = $_SERVER["HTTP_CF_CONNECTING_IP"];
        }
        elseif (isset($_SERVER['REMOTE_ADDR']) && isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        }
        elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        }
        elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        if (strpos($ip, ',') !== false) {
            $ip = @end(explode(',', $ip));
        }
        $ip = trim($ip);
        if (false === ($ip = filter_var($ip, FILTER_VALIDATE_IP))) {
            $ip = '0.0.0.0';
        }
        return $ip;
    }
    /**
     * Get the user agent from the request.
     * @method userAgent
     * @return string               the user agent
     * @codeCoverageIgnore
     */
    public static function userAgent()
    {
        return isset($_SERVER) && isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
    }

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }
    public function get($key, $default = null, $separator = '.')
    {
        $key = array_filter(explode($separator, $key));
        $tmp = $this->data;
        foreach ($key as $k) {
            if (!isset($tmp[$k])) {
                return $default;
            }
            $tmp = $tmp[$k];
        }
        return $tmp;
    }
    public function __get($k)
    {
        return $this->get($k);
    }

    public function loadDatabase(DatabaseInterface $db, $table = 'users', $providerTable = 'user_providers')
    {
    }
    public function loadLDAP()
    {
    }
}
