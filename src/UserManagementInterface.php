<?php

namespace vakata\user;

use vakata\jwt\JWT;

interface UserManagementInterface
{
    public function getUser($id) : UserInterface;
    public function saveUser(UserInterface $user) : UserManagementInterface;
    public function deleteUser(UserInterface $user) : UserManagementInterface;

    public function getGroup($id) : GroupInterface;
    public function saveGroup(GroupInterface $group) : UserManagementInterface;
    public function deleteGroup(GroupInterface $group) : UserManagementInterface;

    public function addPermission(string $permission) : UserManagementInterface;
    public function deletePermission(string $permission) : UserManagementInterface;

    public function permissions() : array;
    public function permissionExists(string $permission) : bool;
    public function groups() : array;
    public function groupExists(string $group) : bool;

    public function getUserByProviderID($provider, $id) : UserInterface;
}