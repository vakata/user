<?php
namespace vakata\user;

interface GroupInterface
{
    public function getID();
    public function getName();
    public function setName(string $name);
    public function getPermissions() : array;
    public function hasPermission(string $permission) : bool;
    public function addPermission(string $permission) : GroupInterface;
    public function deletePermission(string $permission) : GroupInterface;
    public function __toString() : string;
}
