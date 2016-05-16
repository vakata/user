<?php
namespace vakata\user;

use vakata\jwt\JWT;
use vakata\jwt\TokenException;
use vakata\database\DatabaseInterface as DBI;

class UserDatabase extends User
{
    protected static $db = null;

    /**
     * Static init method.
     *
     * In addition to the `User` options, the keys also include:
     * * register - should new users with valid tokens be registered (defaults to `false`)
     * * tableUsers - the table to store the users in (defaults to "users")
     * * tableProviders - the table linking users to providers (defaults to "users_providers")
     * * tableGroups - the table containing the available groups (defaults to "users_groups")
     * * tablePermissions - the table containing the available permissions (defaults to "users_permissions")
     * * tableGroupsPermissions - the table containing each group's permissions (defaults to "users_groups_permissions")
     * * tableUserGroups - the table containing each user's groups (defaults to "users_user_groups")
     * * tableUserPermissions - the table containing each user's permissions (defaults to "users_user_permissions")
     * @method init
     * @param  array  $options the options for future instances
     * @param  \vakata\database\DatabaseInterface $db the DB instance
     */
    public static function init(array $options, DBI $db = null)
    {
        if (!$db) {
            throw new UserException("Please provide a DB connection");
        }
        $options = array_merge([
            'register'               => false,
            'tableUsers'             => 'users',
            'tableProviders'         => 'users_providers',
            'tableGroups'            => 'users_groups',
            'tablePermissions'       => 'users_permissions',
            'tableGroupsPermissions' => 'users_groups_permissions',
            'tableUserGroups'        => 'users_user_groups',
            'tableUserPermissions'   => 'users_user_permissions',
            'groups'                 => [],
            'permissions'            => []
        ], $options);

        static::$db = $db;

        $temp = static::$db->all("
            SELECT
                g.grp,
                p.perm
            FROM " . $options['tableGroups'] . " g
            LEFT JOIN " . $options['tableGroupsPermissions'] . " p ON p.grp = g.grp
            ORDER BY g.grp, p.perm
        ");
        $groups = [];
        foreach ($temp as $row) {
            if (!isset($groups[$row['grp']])) {
                $groups[$row['grp']] = [];
                if (isset($options[$row['grp']])) {
                    $groups[$row['grp']] = $options[$row['grp']];
                }
            }
            $groups[$row['grp']][] = $row['perm'];
        }
        $options['groups'] = $groups;

        $permissions = static::$db->all("SELECT perm FROM " . $options['tablePermissions'] . " ORDER BY perm");
        $options['permissions'] = array_merge($permissions, $options['permissions']);

        parent::init($options);
    }
    /**
     * Create and store a new user in the database.
     * @method createUser
     * @param  array      $data the user data
     * @return integer          the user ID
     */
    public static function createUser(array $data = [])
    {
        $data = array_merge([
            'name'       => null,
            'mail'       => null,
            'provider'   => null,
            'providerId' => null
        ], $data);
        static::$db->begin();
        try {
            $userId = null;
            // if there is a valid email address - try to locate a user with this mail address
            if (filter_var((string)$data['mail'], FILTER_VALIDATE_EMAIL)) {
                $userId = static::$db->one(
                    "SELECT user FROM " . static::$options['tableUsers'] . " WHERE mail = ?",
                    [ (string)$data['mail'] ]
                );
            }
            // if there was not user with that email address, or the email was invalid - register a new user
            if (!$userId) {
                $userId = static::$db->query(
                    "INSERT INTO " . static::$options['tableUsers'] . " (name, mail) VALUES (?, ?)",
                    [ (string)$data['name'], (string)$data['mail'] ]
                )->insertId();
            }
            if ($data['provider'] && $data['providerId']) {
                static::$db->query(
                    "INSERT INTO " . static::$options['tableProviders'] . " (provider, id, user) VALUES (?, ?, ?)",
                    [ (string)$data['provider'], (string)$data['providerId'], $userId ]
                );
            }
            static::$db->commit();
        } catch (\Exception $e) {
            static::$db->rollback();
            throw $e;
        }
        return $userId;
    }
    /**
     * Creates a user instance from a token.
     * @method fromID
     * @param  int|string     $userId the user ID
     * @param  array          $data   optional additional data for the user
     * @return \vakata\user\User    the new user instance
     */
    public static function fromID($userId, array $data = [])
    {
        $userData = static::$db->one(
            "SELECT * FROM " . static::$options['tableUsers'] . " WHERE user = ?",
            [ $userId ]
        );
        if (!$userData) {
            throw new UserException("User does not exist");
        }
        $userData = array_merge($userData, [ 'provider' => '', 'providerId' => $userId ], $data);

        return new static(
            $userId,
            $userData,
            static::$db->all(
                "SELECT grp FROM " . static::$options['tableUserGroups'] . " WHERE user = ? ORDER BY grp",
                [ $userId ]
            ),
            static::$db->all(
                "SELECT perm FROM " . static::$options['tableUserPermissions'] . " WHERE user = ? ORDER BY perm",
                [ $userId ]
            ),
            static::$db->one(
                "SELECT grp FROM " . static::$options['tableUserGroups'] . " WHERE user = ? AND main = 1 ORDER BY grp",
                [ $userId ]
            )
        );
    }
    /**
     * Creates a user instance from a token.
     * @method fromToken
     * @param  JWT|string    $token the token
     * @param  string $decryptionKey optional decryption key string
     * @return \vakata\user\User    the new user instance
     */
    public static function fromToken($token, $decryptionKey = null)
    {
        if (is_string($token)) {
            try {
                $token = JWT::fromString($token, $decryptionKey ? $decryptionKey : md5(static::$options['key']));
            } catch (TokenException $e) {
                throw new UserException('Invalid token');
            }
        }
        $data = static::verifyToken($token);

        $userId = static::$db->one(
            "SELECT user FROM " . static::$options['tableProviders'] . " WHERE provider = ? AND id = ?",
            [ $data['provider'], $data['providerId'] ]
        );
        if (!$userId) {
            if (!static::$options['register']) {
                throw new UserException("User does not exist");
            }
            $userId = static::createUser($data);
        }

        return static::fromID($userId, $data);
    }
    /**
     * Create a new permission
     * @method permissionCreate
     * @param  string           $permission the new permission
     */
    public static function permissionCreate($permission)
    {
        parent::permissionCreate($permission);
        static::$db->query(
            "INSERT INTO " . static::$options['tablePermissions'] . " (perm, created) VALUES (?, ?)",
            [ $permission, date('Y-m-d H:i:s') ]
        );
    }
    /**
     * Delete a permission.
     * @method permissionDelete
     * @param  string           $permission the permission to delete
     */
    public static function permissionDelete($permission)
    {
        parent::permissionDelete($permissions);
        try {
            static::$db->query(
                "DELETE FROM " . static::$options['tableUserPermissions'] . " WHERE perm = ?",
                [ $permission ]
            );
            static::$db->query(
                "DELETE FROM " . static::$options['tableGroupsPermissions'] . " WHERE perm = ?",
                [ $permission ]
            );
            static::$db->query(
                "DELETE FROM " . static::$options['tablePermissions'] . " WHERE perm = ?",
                [ $permission ]
            );
            static::$db->commit($trans);
        } catch (\Exception $e) {
            static::$db->rollback($trans);
            throw $e;
        }
    }
    /**
     * Add a permission to a group.
     * @method groupAddPermission
     * @param  string             $group      the group to add a permission to
     * @param  string             $permission the permission to add
     */
    public static function groupAddPermission($group, $permission)
    {
        parent::groupAddPermission($group, $permission);
        static::$db->query(
            "INSERT INTO " . static::$options['tableGroupsPermissions'] . " (grp, perm, created) VALUES (?, ?, ?)",
            [ $group, $permission, date('Y-m-d H:i:s') ]
        );
    }
    /**
     * Delete a permission from a group.
     * @method groupDeletePermission
     * @param  string                $group      the group being modified
     * @param  string                $permission the permission being removed
     */
    public static function groupDeletePermission($group, $permission)
    {
        parent::groupDeletePermission($group, $permission);
        static::$db->query(
            "DELETE FROM " . static::$options['tableGroupsPermissions'] . " WHERE grp = ? AND perm = ?",
            [ $group, $permission ]
        );
    }
    /**
     * Create a new group.
     * @method groupCreate
     * @param  string      $group       the group name
     * @param  array       $permissions optional array of permission for that group (defaults to an empty array)
     */
    public static function groupCreate($group, $permissions = [])
    {
        parent::groupCreate($group, $permissions);
        static::$db->query(
            "INSERT INTO " . static::$options['tableGroups'] . " (grp, created) VALUES (?, ?)",
            [ $group, date('Y-m-d H:i:s') ]
        );
    }
    /**
     * Delete a group.
     * @method groupDelete
     * @param  string      $group the group to delete
     */
    public static function groupDelete($group)
    {
        parent::groupDelete($group);
        $trans = static::$db->begin();
        try {
            static::$db->query(
                "DELETE FROM " . static::$options['tableUserGroups'] . " WHERE grp = ?",
                [ $group ]
            );
            static::$db->query(
                "DELETE FROM " . static::$options['tableGroupsPermissions'] . " WHERE grp = ?",
                [ $group ]
            );
            static::$db->query(
                "DELETE FROM " . static::$options['tableGroups'] . " WHERE grp = ?",
                [ $group ]
            );
            static::$db->commit($trans);
        } catch (\Exception $e) {
            static::$db->rollback($trans);
            throw $e;
        }
    }
    /**
     * Add the user to a group
     * @method addGroup
     * @param  string   $group the group to add the user to
     */
    public function addGroup($group)
    {
        parent::addGroup($group);
        static::$db->query(
            "INSERT INTO " . static::$options['tableUserGroups'] . " (user, grp, created) VALUES (?, ?, ?)",
            [ $this->data['user'], $group, date('Y-m-d H:i:s') ]
        );
    }
    /**
     * Give the user a new permission
     * @method addPermission
     * @param  string        $permission the permission to give
     */
    public function addPermission($permission)
    {
        parent::addPermission($permission);
        static::$db->query(
            "INSERT INTO " . static::$options['tableUserPermissions'] . " (user, perm, created) VALUES (?, ?, ?)",
            [ $this->data['user'], $permission, date('Y-m-d H:i:s') ]
        );
    }
    /**
     * Remove a user form a group
     * @method deleteGroup
     * @param  string      $group the group to remove the user from
     */
    public function deleteGroup($group)
    {
        parent::deleteGroup($group);
        static::$db->query(
            "DELETE FROM " . static::$options['tableUserGroups'] . " WHERE user = ? AND grp = ?",
            [ $this->data['user'], $group ]
        );
    }
    /**
     * Remove a permission the user has.
     * @method deletePermission
     * @param  string           $permission the permission to remove
     */
    public function deletePermission($permission)
    {
        parent::deletePermission($permission);
        static::$db->query(
            "DELETE FROM " . static::$options['tableUserPermissions'] . " WHERE user = ? AND perm = ?",
            [ $this->data['user'], $permission ]
        );
    }
    /**
     * Set the user's primary group
     * @method setPrimaryGroup
     */
    public function setPrimaryGroup($group)
    {
        parent::setPrimaryGroup($group);
        static::$db->query(
            "UPDATE " . static::$options['tableUserGroups'] . " SET main = 0 WHERE user = ?",
            [ $this->data['user'] ]
        );
        static::$db->query(
            "UPDATE " . static::$options['tableUserGroups'] . " SET main = 1 WHERE user = ? AND grp = ?",
            [ $this->data['user'], $group ]
        );
    }
}
