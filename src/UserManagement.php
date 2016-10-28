<?php

namespace vakata\user;

class UserManagement implements UserManagementInterface
{
    protected $permissions = [];
    protected $users = [];
    protected $groups = [];

    /**
     * create an instance
     * @param  array       $groups       array of GroupInterface objects
     * @param  array       $permissions  array of strings
     */
    public function __construct(array $groups = [], array $permissions = [], array $options = [])
    {
        $this->permissions = $permissions;
        foreach ($groups as $group) {
            $this->groups[$group->getID()] = $group;
            $this->permissions = $this->permissions + $group->getPermissions();
        }
        $this->permissions = array_unique(array_values($this->permissions));
    }
    /**
     * Get the list of permissions in the system.
     * @return array      the permissions available
     */
    public function permissions() : array
    {
        return $this->permissions;
    }
    /**
     * Does a permission exist.
     * @param  string           $permission the permission to check for
     * @return boolean                      does the permission exist
     */
    public function permissionExists(string $permission) : bool
    {
        return in_array($permission, $this->permissions);
    }
    /**
     * Get a list of groups available in the system.
     * @return array an array of GroupInterface objects
     */
    public function groups() : array
    {
        return $this->groups;
    }
    /**
     * Does a group exist.
     * @param  string      $group the group to check for
     * @return boolean            does the group exist
     */
    public function groupExists(string $group) : bool
    {
        return isset($this->groups[$group]);
    }
    
    /**
     * Get a user instance by ID
     * @param  mixed  $id the user ID
     * @return \vakata\user\UserInterface a user instance
     */
    public function getUser($id) : UserInterface
    {
        if (!isset($this->users[$id])) {
            throw new UserException('User not found', 404);
        }
        return $this->users[$id];
    }
    /**
     * save a user instance
     * @param  \vakata\user\UserInterface $user the user to store
     * @return self
     */
    public function saveUser(UserInterface $user) : UserManagementInterface
    {
        $this->users[$user->getID()] = $user;
        return $this;
    }
    /**
     * Get a group by its ID
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