<?php

namespace vakata\user;

use vakata\jwt\JWT;

interface UserInterface
{
    public function inGroup($group);
    public function hasPermission($permission);
    public function addGroup($group);
    public function addPermission($permission);
    public function deletePermission($permission);
    public function deleteGroup($group);
    public function getGroups();
    public function getPrimaryGroup();
    public function setPrimaryGroup($group);
}