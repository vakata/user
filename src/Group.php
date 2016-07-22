<?php
namespace vakata\user;

class Group implements GroupInterface
{
    protected $id;
    protected $permissions;

    /**
     * Create a new group instance.
     * @method __construct
     * @param  mixed       $id          the group ID
     * @param  array       $permissions optional array of permissions the group has (defaults to an empty array)
     */
    public function __construct($id, array $permissions = [])
    {
        $this->id = $id;
        $this->permissions = array_values(array_unique($permissions));
    }
    /**
     * get the group's ID
     * @method getID
     * @return mixed the group ID
     */
    public function getID()
    {
        return $this->id;
    }
    /**
     * set the group's ID
     * @method setID
     * @return self
     */
    public function setID($id)
    {
        $this->id = $id;
        return $this;
    }
    /**
     * Get the group's permissions
     * @method getPermissions
     * @return  array  the group's permission list
     */
    public function getPermissions() : array
    {
        return $this->permissions;
    }
    /**
     * Does the group have a permission.
     * @method hasPermission
     * @param  string        $permission the permission to check for
     * @return boolean                   does the group have that permission
     */
    public function hasPermission(string $permission) : bool
    {
        return in_array($permission, $this->permissions);
    }
    /**
     * Give the group a new permission
     * @method addPermission
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
     * @method deletePermission
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
