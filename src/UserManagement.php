<?php

namespace vakata\user;

use vakata\jwt\JWT;

class UserManagement implements UserManagementInterface
{
    protected $options = [];
    protected $permissions = [];
    protected $users = [];
    protected $groups = [];

    /**
     * create an instance
     *
     * * Options include:
     * * issuer - the issuer to use when signing JWTs
     * * cryptokey - the key to used to encrypt / decrypt JWTs
     * * key - the key to use when signing JWT's (could be an array of keys)
     * * validateIpAddress - the required IP address of the user (defaults to `null`)
     * * validateUserAgent - the required user agent of the user (defaults to `null`)
     * * validateSessionID - the required session ID of the user (defaults to `null`)
     *
     * @method __construct
     * @param  array       $groups       array of GroupInterface objects
     * @param  array       $permissions  array of strings
     * @param  array       $options      the instance's options described above
     */
    public function __construct(array $groups = [], array $permissions = [], array $options = [])
    {
        $this->options = array_merge([
            'issuer' => null,
            'key' => null,
            'cryptokey' => null,
            'validateIpAddress' => null,
            'validateUserAgent' => null,
            'validateSessionID' => null
        ], $options);
        $this->permissions = $permissions;
        foreach ($groups as $group) {
            $this->groups[$group->getID()] = $group;
            $this->permissions = $this->permissions + $group->getPermissions();
        }
        $this->permissions = array_unique(array_values($this->permissions));
    }
    /**
     * Signs and encrypts a given JWT using the set of rules provided when creating the instance.
     * @method secureToken
     * @param  JWT        $token    the token to sign
     * @param  int|string $validity the validity of the token in seconds or a strtotime expression (defaults to `86400`)
     * @return string               the signed (and optionally encrypted) token
     */
    public function secureToken(JWT $token, int $validity = 86400) : string
    {
        $validity = is_numeric($validity) ? time() + $validity : strtotime($validity);
        if ($validity === false) {
            throw new UserException('Invalid token expire time');
        }
        $token->setExpiration($validity);

        if ($this->options['issuer']) {
            $token->setIssuer($this->options['issuer']);
        }
        if ($this->options['validateIpAddress']) {
            $token->setClaim('ip', $this->ipAddress());
        }
        if ($this->options['validateUserAgent']) {
            $token->setClaim('ua', $this->userAgent());
        }
        if ($this->options['validateSessionID']) {
            $token->setClaim('sess', session_id());
        }
        if ($this->options['key']) {
            $token->sign($this->options['key']);
        }
        return $this->options['cryptokey'] ?
            $token->toString($this->options['cryptokey']) :
            $token->toString();
    }
    /**
     * Parse, verify and validate a token.
     * @method parseToken
     * @param  JWT|string    $token the token
     * @return array of token claims
     */
    public function parseToken($token) : array
    {
        if (is_string($token)) {
            try {
                $token = JWT::fromString($token, $this->options['cryptokey']);
            } catch (TokenException $e) {
                throw new UserException('Invalid token');
            }
        }
        if (!$token->isSigned() && isset($this->options['key'])) {
            throw new UserException('Token not signed');
        }
        if (!$token->verifyHash($this->options['key'])) {
            throw new UserException('Invalid token signature');
        }
        $verify = [];
        if (isset($this->options['issuer'])) {
            $verify['iss'] = $this->options['issuer'];
        }
        if (isset($this->options['validateIpAddress'])) {
            $verify['ip'] = $this->options['validateIpAddress'];
        }
        if (isset($this->options['validateUserAgent'])) {
            $verify['ua'] = $this->options['validateUserAgent'];
        }
        if (isset($this->options['validateSessionID'])) {
            $verify['sess'] = $this->options['validateSessionID'];
        }
        if (!$token->isValid($verify)) {
            throw new UserException('Token not valid');
        }

        return array_merge([
            'provider'   => null,
            'id'         => null,
            'mail'       => null,
            'name'       => null
        ], $token->getClaims());
    }
    /**
     * Creates a user instance from a token.
     * @method fromToken
     * @param  JWT|string    $token the token
     * @return \vakata\user\User    the new user instance
     */
    public function fromToken($token) : UserInterface
    {
        $data = $this->parseToken($token);
        $data['providerId'] = $data['id'];
        $data['id'] = $data['providerId'] . '@' . $data['provider'];
        return $this->userStorage[$data['id']] = new User($data['id'], $data);
    }
    /**
     * Get the list of permissions in the system.
     * @method permissions
     * @return array      the permissions available
     */
    public function permissions() : array
    {
        return $this->permissions;
    }
    /**
     * Does a permission exist.
     * @method permissionExists
     * @param  string           $permission the permission to check for
     * @return boolean                      does the permission exist
     */
    public function permissionExists(string $permission) : bool
    {
        return in_array($permission, $this->permissions);
    }
    /**
     * Get a list of groups available in the system.
     * @method groups
     * @return array an array of GroupInterface objects
     */
    public function groups() : array
    {
        return $this->groups;
    }
    /**
     * Does a group exist.
     * @method groupExists
     * @param  string      $group the group to check for
     * @return boolean            does the group exist
     */
    public function groupExists(string $group) : bool
    {
        return isset($this->groups[$group]);
    }
    
    /**
     * Get a user instance by ID
     * @method getUser
     * @param  mixed  $id the user ID
     * @return \vakata\user\UserInterface a user instance
     */
    public function getUser($id) : UserInterface
    {
        if (!isset($this->userStorage[$id])) {
            throw new UserException('User not found', 404);
        }
        return $this->userStorage[$id];
    }
    /**
     * save a user instance
     * @method saveUser
     * @param  \vakata\user\UserInterface $user the user to store
     * @return self
     */
    public function saveUser(UserInterface $user) : UserManagementInterface
    {
        $this->userStorage[$user->getID()] = $user;
        return $this;
    }
    /**
     * Get a group by its ID
     * @method getGroup
     * @param  string   $id the ID to search for
     * @return \vakata\user\GroupInterface       the group instance
     */
    public function getGroup($id) : GroupInterface
    {
        if (!isset($this->groups[$id])) {
            throw new UserException('Group not found');
        }
        return $this->groups[$id];
    }
    /**
     * Save a group.
     * @method saveGroup
     * @param  \vakata\user\GroupInterface $group the group to save
     * @return self
     */
    public function saveGroup(GroupInterface $group) : UserManagementInterface
    {
        if (!isset($this->groups[$group->getID()])) {
            $this->groups[$group->getID()] = $group;
        }
        $this->permissions = array_values(array_unique(array_merge($this->permissions, $group->getPermissions())));
        return $this;
    }
}