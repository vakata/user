<?php
namespace vakata\user;

use vakata\jwt\JWT;

class User
{
    protected $id;
    protected $data = [];
    protected $groups = [];
    protected $permissions = [];

    protected static $options = [];

    public static function init(array $options)
    {
        $options = array_merge([
            'issuer' => null,
            'key' => null,
            'validateIpAddress' => true,
            'validateUserAgent' => true,
            'validateSessionID' => true,
            'groups' => [],
            'permissions' => []
        ], $options);
        foreach ($options['groups'] as $group => $permissions) {
            $options['permissions'] = $options['permissions'] + $permissions;
            $options['groups'][$group] = array_unique(array_values($permissions));
        }
        $options['permissions'] = array_unique(array_values($options['permissions']));
        static::$options = $options;
    }
    public static function signToken(JWT $token)
    {
        if (!static::$options['key']) {
            throw new UserException('No key set');
        }
        if (static::$options['issuer']) {
            $token->setIssuer($issuer);
        }
        if (static::$options['validateIpAddress']) {
            $token->setClaim('ip', static::ipAddress());
        }
        if (static::$options['validateUserAgent']) {
            $token->setClaim('ua', static::userAgent());
        }
        if (static::$options['validateSessionID']) {
            $token->setClaim('sess', session_id());
        }
        $token->sign(static::$options['key']);
        return $token->toString(md5(static::$options['key']));
    }
    public static function verifyToken(JWT $token)
    {
        if (!static::$options['key']) {
            throw new UserException('No key set');
        }
        if (!$token->isSigned()) {
            throw new UserException('Token not signed');
        }
        $verify = [];
        if (static::$options['issuer']) {
            $verify['iss'] = static::$options['issuer'];
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
            throw new UserException('Token not valid');
        }
        if (!$token->verifyHash(static::$options['key'])) {
            throw new UserException('Invalid token signature');
        }
        $data = array_merge([
            'provider'   => null,
            'id'         => null,
            'mail'       => null,
            'name'       => null
        ], $token->getClaims());
        $data['providerId'] = $data['id'];
        return $data;
    }
    public static function fromToken($token)
    {
        if (is_string($token)) {
            $token = JWT::fromString($token, md5(static::$options['key']));
        }
        $data = static::verifyToken($token);
        new static(md5(((string)$data['providerId']) . '@' . ((string)$data['provider'])), $data);
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

    public static function permissions()
    {
        return static::$options['permissions'];
    }
    public static function permissionExists($permission)
    {
        return in_array($permission, static::$options['permissions']);
    }
    public static function permissionCreate($permission)
    {
        if (static::permissionExists($permission)) {
            throw new UserException('Permission already exists');
        }
        static::$options['permissions'][] = $permission;
    }
    public static function permissionDelete($permission)
    {
        $index = array_search($permission, static::$options['permissions']);
        if ($index === false) {
            throw new UserException("Permission not found");
        }
        unset(static::$options['permissions'][$index]);
        static::$options['permissions'] = array_values(static::$options['permissions']);
        foreach (static::$options['groups'] as $group => $permissions) {
            $index = array_search($permission, $permissions);
            if ($index !== false) {
                unset(static::$options['groups'][$group][$index]);
                static::$options['groups'][$group] = array_values(static::$options['groups'][$group]);
            }
        }
    }
    public static function groups()
    {
        return static::$options['groups'];
    }
    public static function groupExists($group)
    {
        return isset(static::$options['groups'][$group]);
    }
    public static function groupPermissions($group)
    {
        if (!static::groupExists($group)) {
            throw new UserException('Group not found');
        }
        return static::$options['groups'][$group];
    }
    public static function groupHasPermission($group, $permission)
    {
        return in_array($permission, static::groupPermissions($group));
    }
    public static function groupAddPermission($group, $permission)
    {
        if (!isset(static::$options['groups'][$group])) {
            throw new UserException('Group not found');
        }
        if (static::groupHasPermission($group, $permission)) {
            throw new UserException('Group already has permission');
        }
        if (!static::permissionExists($permission)) {
            static::permissionCreate($permission);
        }
        static::$options['groups'][$group][] = $permission;
    }
    public static function groupDeletePermission($group, $permission)
    {
        if (!static::groupExists($group)) {
            throw new UserException('Group not found');
        }
        $index = array_search($permission, static::$options['groups'][$group]);
        if ($index === false) {
            throw new UserException('Permission not found in group');
        }
        unset(static::$options['groups'][$group][$index]);
        static::$options['groups'][$group] = array_values(static::$options['groups'][$group]);
    }
    public static function groupCreate($group, $permissions = [])
    {
        if (static::groupExists($group)) {
            throw new UserException('Group already exists');
        }
        $permissions = array_values(array_unique($permissions));
        foreach ($permissions as $permission) {
            if (!static::permissionExists($permission)) {
                static::permissionCreate($permission);
            }
        }
        static::$options['groups'][$group] = $permissions;
    }
    public static function groupDelete($group)
    {
        if (!static::groupExists($group)) {
            throw new Exception("Group not found");
        }
        unset(static::$options['groups'][$group]);
    }

    public function __construct($id, array $data = [], array $groups = [], array $permissions = [])
    {
        $this->id = $id;
        $this->data = $data;
        $this->groups = $groups;
        $this->permissions = $permissions;
    }
    public function get($key, $default = null, $separator = '.')
    {
        if ($key === 'id') {
            return $this->id;
        }
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
    public function set($key, $value, $separator = '.')
    {
        $key = array_filter(explode($separator, $key));
        $tmp = &$this->data;
        foreach ($key as $k) {
            if (!isset($tmp[$k])) {
                $tmp[$k] = [];
            }
            $tmp = &$tmp[$k];
        }
        return $tmp = is_array($tmp) && is_array($value) && count($tmp) ? array_merge($tmp, $value) : $value;
    }
    public function __get($k)
    {
        return $k === 'id' ? $this->id : $this->get($k);
    }
    public function inGroup($group)
    {
        if (!static::groupExists($group)) {
            throw new UserException('Invalid group');
        }
        return in_array($group, $this->groups);
    }
    public function hasPermission($permission)
    {
        if (!static::permissionExists($permission)) {
            throw new UserException('Invalid permission');
        }
        if (in_array($permission, $this->permissions)) {
            return true;
        }
        foreach ($this->groups as $group) {
            if (static::groupHasPermission($group, $permission)) {
                return true;
            }
        }
        return false;
    }
    public function addGroup($group)
    {
        if (!static::groupExists($group)) {
            throw new UserException('Invalid group');
        }
        $this->groups[] = $group;
    }
    public function addPermission($permission)
    {
        if (!static::permissionExists($permission)) {
            throw new UserException('Invalid permission');
        }
        $this->permissions[] = $permission;
    }
    public function deletePermission($permission)
    {
        if (!static::permissionExists($permission)) {
            throw new UserException('Invalid permission');
        }
        $index = array_search($permission, $this->permissions);
        if ($index === false) {
            throw new UserException('User does not have this permission');
        }
        unset($this->permissions[$index]);
        $this->permissions = array_values($this->permissions);
    }
    public function deleteGroup($group)
    {
        if (!static::groupExists($group)) {
            throw new UserException('Invalid group');
        }
        $index = array_search($group, $this->groups);
        if ($index === false) {
            throw new UserException('User does not belong to this group');
        }
        unset($this->groups[$index]);
        $this->groups = array_values($this->groups);
    }
}
