<?php

namespace vakata\user;

use vakata\kvstore\StorageInterface;

interface UserInterface extends StorageInterface
{
    public function getID(): string;
    public function setID(string $id) : UserInterface;
    public function getData() : array;
    public function inGroup($group) : bool;
    public function getGroups() : array;
    public function getPrimaryGroup(): ?GroupInterface;
    public function hasPermission(string $permission) : bool;
    public function getPermissions() : array;
    public function getProviders() : array;

    public function addGroup(GroupInterface $group) : UserInterface;
    public function deleteGroup(GroupInterface $group) : UserInterface;
    public function setPrimaryGroup(GroupInterface $group) : UserInterface;

    public function addProvider(Provider $provider) : UserInterface;
    public function deleteProvider(Provider $provider) : UserInterface;
}