<?php
namespace vakata\user;

class Group implements GroupInterface
{
    protected $id;
    protected $name;
    protected $permissions;

    /**
     * Create a new group instance.
     * @param  mixed       $id          the group ID
     * @param  string      $name        the group frienly name
     * @param  array       $permissions optional array of permissions the group has (defaults to an empty array)
     */
    public function __construct($id, string $name, array $permissions = [])
    {
        $this->id = $id;
        $this->name = $name;
        $this->permissions = array_values(array_unique($permissions));
    }
    /**
     * get the group's ID
     * @return mixed the group ID
     */
    public function getID()
    {
        return $this->id;
    }
    /**
     * get the group's ID
     * @return mixed the group ID
     */
    public function getName()
    {
        return $this->name;
    }
    /**
     * set the group's ID
     * @return self
     */
    public function setID($id)
    {
        $this->id = $id;
        return $this;
    }
    /**
     * set the group's friendly name
     * @return self
     */
    public function setName(string $name)
    {
        $this->name = $name;
        return $this;
    }
    /**
     * Get the group's permissions
     * @return  array  the group's permission list
     */
    public function getPermissions() : array
    {
        return $this->permissions;
    }
    /**
     * Does the group have a permission.
     * @param  string        $permission the permission to check for
     * @return boolean                   does the group have that permission
     */
    public function hasPermission(string $permission) : bool
    {
        return in_array($permission, $this->permissions);
    }
    /**
     * Give the group a new permission
     * @param  string        $permission the permission to give
     * @return  self
     */
    public function addPermission(string $permission) : GroupInterface
    {
        $this->permissions[] = $permission;
        $this->permissions = array_values(array_unique($this->permissions));
        return $this;
    }
    /**
     * Remove a permission the group has.
     * @param  string           $permission the permission to remove
     * @return  self
     */
    public function deletePermission(string $permission) : GroupInterface
    {
        $index = array_search($permission, $this->permissions);
        if ($index !== false) {
            unset($this->permissions[$index]);
            $this->permissions = array_values($this->permissions);
        }
        return $this;
    }

    public function __toString() : string
    {
        return (string)$this->id;
    }
}
