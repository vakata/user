<?php
namespace vakata\user;

interface GroupInterface
{
    public function getID(): string;
    public function setID(string $id): GroupInterface;
    public function getName(): string;
    public function setName(string $name): GroupInterface;
    public function getPermissions() : array;
    public function hasPermission(string $permission) : bool;
    public function addPermission(string $permission) : GroupInterface;
    public function deletePermission(string $permission) : GroupInterface;
    public function __toString() : string;
}
