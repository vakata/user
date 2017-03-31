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
     * @param  array       $users        array of UserInterface objects
     */
    public function __construct(array $groups = [], array $permissions = [], array $users = [])
    {
        $this->permissions = $permissions;
        foreach ($groups as $group) {
            $this->groups[$group->getID()] = $group;
            $this->permissions = $this->permissions + $group->getPermissions();
        }
        foreach ($users as $user) {
            $this->users[$user->getID()] = $user;
            foreach ($user->getGroups() as $group) {
                if (!isset($this->groups[$group->getID()])) {
                    $this->groups[$group->getID()] = $group;
                    $this->permissions = $this->permissions + $group->getPermissions();
                }
            }
        }
        $this->permissions = array_unique(array_values($this->permissions));
    }
    /**
     * Get a user instance by provider ID
     * @param  string  $provider the authentication provider
     * @param  mixed   $id the user ID
     * @return \vakata\user\UserInterface a user instance
     */
    public function getUserByProviderID($provider, $id) : UserInterface
    {
        foreach ($this->users as $user) {
            foreach ($user->getProviders() as $prov) {
                if ($provider === $prov->getProvider() && $id === $prov->getID()) {
                    return $user;
                }
            }
        }
        throw new UserException('User not found', 404);
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
     * Delete a user.
     * @param  \vakata\user\UserInterface $user the user to delete
     * @return self
     */
    public function deleteUser(UserInterface $user) : UserManagementInterface
    {
        $index = array_search($user, $this->users);
        if ($index !== false) {
            unset($this->user[$index]);
        }
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
    /**
     * Delete a group.
     * @param  \vakata\user\GroupInterface $group the group to delete
     * @return self
     */
    public function deleteGroup(GroupInterface $group) : UserManagementInterface
    {
        $index = array_search($group, $this->groups);
        if ($index !== false) {
            unset($this->groups[$index]);
        }
        return $this;
    }
    /**
     * Add a permission.
     * @param  string $permission the permission to add
     * @return self
     */
    public function addPermission(string $permission) : UserManagementInterface
    {
        $this->permissions[] = $permission;
        $this->permissions = array_values(array_unique($this->permissions));
        return $this;
    }
    /**
     * Remove a permission.
     * @param  string $permission the permission to remove
     * @return self
     */
    public function deletePermission(string $permission) : UserManagementInterface
    {
        $index = array_search($permission, $this->permissions);
        if ($index !== false) {
            unset($this->permissions[$index]);
            $this->permissions = array_values($this->permissions);
            foreach ($this->groups as $group) {
                if ($group->hasPermission($permission)) {
                    $group->deletePermission($permission);
                }
            }
        }
        return $this;
    }
}