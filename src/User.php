<?php
namespace vakata\user;

use vakata\jwt\JWT;
use vakata\jwt\TokenException;

class User
{
    protected $id;
    protected $data = [];
    protected $groups = [];
    protected $permissions = [];

    protected static $options = [];

    /**
     * Static init method.
     *
     * Options include:
     * * issuer - the issuer to use when signing JWT's
     * * key - the key to use when signing JWT's (could be an array of keys)
     * * validateIpAddress - should IP's be validated in tokens (defaults to `true`)
     * * validateUserAgent - should the user agent be validated in tokens (defaults to `true`)
     * * validateSessionID - should the session ID be validated in tokens (defaults to `true`)
     * * groups - the groups in the system, an array of strings
     * * permissions - the permissions in the system, an array of strings
     * @method init
     * @param  array  $options the options for future instances
     */
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
    /**
     * Static sign token function. Signs and encrypts a given JWT using the set of rules provided in `init`.
     * @method secureToken
     * @param  JWT        $token    the token to sign
     * @param  boolean|string $encrypt  should the token be encrypted (or a string key) (defaults to `true`)
     * @param  int|string $validity the validity of the token in seconds or a strtotime expression (defaults to `86400`)
     * @return string               the signed and encrypted token
     */
    public static function secureToken(JWT $token, $encrypt = true, $validity = 86400)
    {
        if (!static::$options['key']) {
            throw new UserException('No key set');
        }
        
        $validity = is_numeric($validity) ? time() + $validity : strtotime($validity);
        if ($validity === false) {
            throw new UserException('Invalid token expire time');
        }
        $token->setExpiration($validity);

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
        return $encrypt ?
            $token->toString(is_string($encrypt) ? $encrypt : md5(static::$options['key'])) :
            $token->toString();
    }
    /**
     * Static function for token verification. Will throw UserExceptions on invalid tokens.
     * @method verifyToken
     * @param  JWT         $token the token to verify
     * @return string             the validated claims
     */
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
    /**
     * Creates a user instance from a token.
     * @method fromToken
     * @param  JWT|string    $token the token
     * @param  string $decryptionKey optional decryption key string
     * @return \vakata\user\User    the new user instance
     */
    public static function fromToken($token, $decryptionKey = null)
    {
        if (is_string($token)) {
            try {
                $token = JWT::fromString($token, $decryptionKey ? $decryptionKey : md5(static::$options['key']));
            } catch (TokenException $e) {
                throw new UserException('Invalid token');
            }
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
    /**
     * Get the list of permissions in the system.
     * @method permissions
     * @return array      the permissions available
     */
    public static function permissions()
    {
        return static::$options['permissions'];
    }
    /**
     * Does a permission exist.
     * @method permissionExists
     * @param  string           $permission the permission to check for
     * @return boolean                      does the permission exist
     */
    public static function permissionExists($permission)
    {
        return in_array($permission, static::$options['permissions']);
    }
    /**
     * Create a new permission
     * @method permissionCreate
     * @param  string           $permission the new permission
     */
    public static function permissionCreate($permission)
    {
        if (static::permissionExists($permission)) {
            throw new UserException('Permission already exists');
        }
        static::$options['permissions'][] = $permission;
    }
    /**
     * Delete a permission.
     * @method permissionDelete
     * @param  string           $permission the permission to delete
     */
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
    /**
     * Get a list of groups available in the system.
     * @method groups
     * @return array an array of group strings
     */
    public static function groups()
    {
        return static::$options['groups'];
    }
    /**
     * Does a group exist.
     * @method groupExists
     * @param  string      $group the group to check for
     * @return boolean            does the group exist
     */
    public static function groupExists($group)
    {
        return isset(static::$options['groups'][$group]);
    }
    /**
     * Get a list of permissions included in group
     * @method groupPermissions
     * @param  string           $group the group to check
     * @return array                   an array of permissions in that group
     */
    public static function groupPermissions($group)
    {
        if (!static::groupExists($group)) {
            throw new UserException('Group not found');
        }
        return static::$options['groups'][$group];
    }
    /**
     * Does a group have a given permission.
     * @method groupHasPermission
     * @param  string             $group      the group to check
     * @param  string             $permission the permission to check
     * @return boolean                        is the permission incldued in that group
     */
    public static function groupHasPermission($group, $permission)
    {
        return in_array($permission, static::groupPermissions($group));
    }
    /**
     * Add a permission to a group.
     * @method groupAddPermission
     * @param  string             $group      the group to add a permission to
     * @param  string             $permission the permission to add
     */
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
    /**
     * Delete a permission from a group.
     * @method groupDeletePermission
     * @param  string                $group      the group being modified
     * @param  string                $permission the permission being removed
     */
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
    /**
     * Create a new group.
     * @method groupCreate
     * @param  string      $group       the group name
     * @param  array       $permissions optional array of permission for that group (defaults to an empty array)
     */
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
    /**
     * Delete a group.
     * @method groupDelete
     * @param  string      $group the group to delete
     */
    public static function groupDelete($group)
    {
        if (!static::groupExists($group)) {
            throw new Exception("Group not found");
        }
        unset(static::$options['groups'][$group]);
    }

    /**
     * Create a new user instance.
     * @method __construct
     * @param  mixed       $id          the user ID
     * @param  array       $data        optional array of user data (defaults to an empty array)
     * @param  array       $groups      optional array of groups the user belongs to (defaults to an empty array)
     * @param  array       $permissions optional array of permissions the user has (defaults to an empty array)
     */
    public function __construct($id, array $data = [], array $groups = [], array $permissions = [])
    {
        $this->id = $id;
        $this->data = $data;
        $this->groups = $groups;
        $this->permissions = $permissions;
    }
    /**
     * Get a piece of user data.
     * @method get
     * @param  string $key       the data to search for - use '.' to traverse arrays
     * @param  mixed  $default   optional default to return if the key does not exist, defaults to `null`
     * @param  string $separator the separator to use when traversing arrays, defaults to '.'
     * @return mixed             the key value or the default
     */
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
    /**
     * Set a piece of user data.
     * @method set
     * @param  string $key       the key to set, use '.' to traverse arrays
     * @param  mixed  $value     the new value for the key
     * @param  string $separator the separator to use when traversing arrays, defaults to '.'
     */
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
    /**
     * Is the user in a group.
     * @method inGroup
     * @param  string  $group the group to check for
     * @return boolean        is the user in the group
     */
    public function inGroup($group)
    {
        if (!static::groupExists($group)) {
            throw new UserException('Invalid group');
        }
        return in_array($group, $this->groups);
    }
    /**
     * Does the user have a permission.
     * @method hasPermission
     * @param  string        $permission the permission to check for
     * @return boolean                   does the user have that permission
     */
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
    /**
     * Add the user to a group
     * @method addGroup
     * @param  string   $group the group to add the user to
     */
    public function addGroup($group)
    {
        if (!static::groupExists($group)) {
            throw new UserException('Invalid group');
        }
        $this->groups[] = $group;
    }
    /**
     * Give the user a new permission
     * @method addPermission
     * @param  string        $permission the permission to give
     */
    public function addPermission($permission)
    {
        if (!static::permissionExists($permission)) {
            throw new UserException('Invalid permission');
        }
        $this->permissions[] = $permission;
    }
    /**
     * Remove a permission the user has.
     * @method deletePermission
     * @param  string           $permission the permission to remove
     */
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
    /**
     * Remove a user form a group
     * @method deleteGroup
     * @param  string      $group the group to remove the user from
     */
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
