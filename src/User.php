<?php
namespace vakata\user;

use vakata\jwt\JWT;
use vakata\jwt\TokenException;
use vakata\kvstore\StorageInterface;

class User implements UserInterface
{
    public static $permissions;

    protected $id;
    protected $data;
    protected $storage;
    protected $groups = [];
    protected $primary = null;
    protected $providers = [];

    /**
     * Create a new user instance.
     * @param  mixed       $id          the user ID
     * @param  array       $data        optional array of user data (defaults to an empty array)
     * @param  array       $groups      optional array of GroupInterface objects the user belongs to (defaults to none)
     * @param  array       $providers   optional array of Provider objects identifying the user (defaults to none)
     * @param  \vakata\user\GroupInterface $primary     the user's primary group name (defaults to `null`)
     */
    public function __construct($id, array $data = [], array $groups = [], GroupInterface $primary = null, array $providers = [])
    {
        $this->id = $id;
        $this->data = $data;
        $this->storage = new \vakata\kvstore\Storage($this->data);
        foreach ($groups as $group) {
            $this->groups[$group->getID()] = $group;
        }
        $this->primary = $primary !== null && isset($this->groups[$primary->getID()]) ? $primary : null;
        $this->providers = $providers;
    }
    /**
     * get the user's ID
     * @return mixed the user ID
     */
    public function getID()
    {
        return $this->id;
    }
    /**
     * set the user's ID
     * @return self
     */
    public function setID($id) : UserInterface
    {
        $this->id = $id;
        return $this;
    }
    /**
     * get the user's data fields
     * @return mixed the user's data
     */
    public function getData() : array
    {
        return $this->data;
    }
    /**
     * Get a piece of user data.
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
        return $this->storage->get($key, $default, $separator);
    }
    /**
     * Set a piece of user data.
     * @param  string $key       the key to set, use '.' to traverse arrays
     * @param  mixed  $value     the new value for the key
     * @param  string $separator the separator to use when traversing arrays, defaults to '.'
     */
    public function set($key, $value, $separator = '.')
    {
        return $this->storage->set($key, $value, $separator);
    }
    /**
     * Delete an element from the storage.
     * @param  string $key       the element to delete (can be a deeply nested element of the data array)
     * @param  string $separator the string used to separate levels of the array, defaults to "."
     * @return mixed|null        the value that was just deleted or null
     */
    public function del($key, $separator = '.')
    {
        return $this->storage->del($key, $separator);
    }

    public function __get($k)
    {
        return $this->get($k);
    }
    public function __set($k, $v)
    {
        return $this->set($k, $v);
    }
    /**
     * Is the user in a group.
     * @param  string|\vakata\user\GroupInterface  $group the group to check for
     * @return boolean        is the user in the group
     */
    public function inGroup($group) : bool
    {
        $id = ($group instanceof GroupInterface) ? $group->getID() : $group;
        return isset($this->groups[$id]);
    }
    /**
     * Get the user's groups
     * @return  array  the user's group list
     */
    public function getGroups() : array
    {
        return $this->groups;
    }
    /**
     * Get the user's primary group
     * @param  string      $group the user's primary group
     */
    public function getPrimaryGroup()
    {
        return $this->primary !== null ? $this->primary : (count($this->groups) ? current($this->groups) : null);
    }
    /**
     * Does the user have a permission.
     * @param  string        $permission the permission to check for
     * @param  boolean       $strict if set to true will return false for permissions that do not exist
     * @return boolean                   does the user have that permission
     */
    public function hasPermission(string $permission, bool $strict = false) : bool
    {
        if (!in_array($permission, static::$permissions)) {
            return $strict ? false : true;
        }
        foreach ($this->groups as $group) {
            if ($group->hasPermission($permission)) {
                return true;
            }
        }
        return false;
    }
    /**
     * Get the user's permissions
     * @return  array  the user's permission list
     */
    public function getPermissions() : array
    {
        $permissions = [];
        foreach ($this->groups as $group) {
            $permissions = array_merge($permissions, $group->getPermissions());
        }
        return array_values(array_unique($permissions));
    }
    /**
     * Add the user to a group
     * @param  \vakata\user\GroupInterface   $group the group to add the user to
     * @return  self
     */
    public function addGroup(GroupInterface $group) : UserInterface
    {
        $this->groups[$group->getID()] = $group;
        return $this;
    }
    /**
     * Remove a user form a group
     * @param  vakata\user\GroupInterface      $group the group to remove the user from
     * @return  self
     */
    public function deleteGroup(GroupInterface $group) : UserInterface
    {
        if (isset($this->groups[$group->getID()])) {
            unset($this->groups[$group->getID()]);
            if ($this->primary !== null && $this->primary->getID() === $group->getID()) {
                $this->primary = null;
            }
        }
        return $this;
    }

    /**
     * Set the user's primary group
     * @param  vakata\user\GroupInterface      $group the group to set as primary
     * @return  self
     */
    public function setPrimaryGroup(GroupInterface $group) : UserInterface
    {
        if (!$this->inGroup($group)) {
            throw new UserException('User does not belong to this group');
        }
        $this->primary = $group;
        return $this;
    }

    /**
     * Get the user's providers
     * @return  array  the user's group list
     */
    public function getProviders() : array
    {
        return $this->providers;
    }
    /**
     * Add a provider to the
     * @param  \vakata\user\Provider   $provider the provider to add
     * @return  self
     */
    public function addProvider(Provider $provider) : UserInterface
    {
        $this->providers[] = $provider;
        return $this;
    }
    /**
     * Remove a user form a group
     * @param  vakata\user\GroupInterface      $group the group to remove the user from
     * @return  self
     */
    public function deleteProvider(Provider $provider) : UserInterface
    {
        if (($index = array_search($provider, $this->providers)) !== false) {
            unset($this->providers[$index]);
        }
        return $this;
    }
}
