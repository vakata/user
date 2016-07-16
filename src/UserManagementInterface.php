<?php

namespace vakata\user;

use vakata\jwt\JWT;

interface UserManagementInterface
{
    public static function init(array $options);
    public static function secureToken(JWT $token, $encrypt = true, $validity = 86400);
    public static function verifyToken(JWT $token);
    public static function fromToken($token, $decryptionKey = null);
    public static function ipAddress();
    public static function userAgent();
    public static function permissions();
    public static function permissionExists($permission);
    public static function permissionCreate($permission);
    public static function permissionDelete($permission);
    public static function groups();
    public static function groupExists($group);
    public static function groupPermissions($group);
    public static function groupHasPermission($group, $permission);
    public static function groupAddPermission($group, $permission);
    public static function groupDeletePermission($group, $permission);
    public static function groupCreate($group, $permissions = []);
    public static function groupDelete($group);
}