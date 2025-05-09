<?php
namespace vakata\user;

use vakata\cache\CacheInterface;
use vakata\cache\CacheException;
use vakata\database\DBInterface;
use vakata\database\DBException;

class UserManagementDatabase extends UserManagement
{
    protected $db;
    protected $options;
    protected $unique;
    protected $cache;
    protected $key;
    protected $expire;

    /**
     * Static init method.
     *
     * Options include:
     * * tableUsers - the table to store the users in (defaults to "users")
     * * tableProviders - the table linking users to providers (defaults to "users_providers")
     * * tableGroups - the table containing the available groups (defaults to "users_groups")
     * * tablePermissions - the table containing the available permissions (defaults to "users_permissions")
     * * tableGroupsPermissions - the table containing each group's permissions (defaults to "users_groups_permissions")
     * * tableUserGroups - the table containing each user's groups (defaults to "users_user_groups")
     * * tableUserPermissions - the table containing each user's permissions (defaults to "users_user_permissions")
     * @param  DBInterface $db the DB instance
     * @param  array  $options the options for future instances
     * @param  array  $unique  array of fields to use to enforce user uniqueness (defaults to empty)
     */
    public function __construct(
        DBInterface $db,
        array $options = [],
        array $unique = [],
        ?CacheInterface $cache = null,
        string $key = 'umd',
        int $expire = 86400
    )
    {
        $options = array_merge([
            'tableUsers'             => 'users',
            'tableProviders'         => 'users_providers',
            'tableGroups'            => null,
            'tablePermissions'       => null,
            'tableGroupsPermissions' => null,
            'tableUserGroups'        => null
        ], $options);

        $this->options = $options;
        $this->unique = $unique;
        $this->db = $db;
        $this->cache = $cache;
        $this->key = $key;
        $this->expire = $expire;

        $temp = null;
        if ($this->cache && $this->expire) {
            try {
                $temp = $this->cache->get($this->key . '_groups');
            } catch (CacheException $ignore) {
                $temp = null;
            }
        }
        if (!isset($temp)) {
            $temp = [];
            if (isset($options['tableGroups']) && isset($options['tableGroupsPermissions'])) {
                $temp = $this->db->all("
                    SELECT
                        g.grp,
                        g.name,
                        p.perm
                    FROM " . $options['tableGroups'] . " g
                    LEFT JOIN " . $options['tableGroupsPermissions'] . " p ON p.grp = g.grp
                    ORDER BY g.grp, p.perm
                ", null, null, false, 'assoc_lc');
            }
            if ($this->cache && $this->expire) {
                try {
                    $this->cache->set($this->key . '_groups', $temp, $this->expire);
                } catch (CacheException $ignore) {}
            }
        }
        $groups = [];
        foreach ($temp as $row) {
            $row['grp'] = (string)$row['grp'];
            if (!isset($groups[$row['grp']])) {
                $groups[$row['grp']] = [ 'name' => $row['name'], 'permissions' => [] ];
            }
            if ($row['perm']) {
                $groups[$row['grp']]['permissions'][] = $row['perm'];
            }
        }
        foreach ($groups as $id => $group) {
            $groups[$id] = new Group($id, $group['name'], $group['permissions']);
        }

        $permissions = null;
        if ($this->cache && $this->expire) {
            try {
                $permissions = $this->cache->get($this->key . '_perms');
            } catch (CacheException $ignore) {
                $permissions = null;
            }
        }
        if (!isset($permissions)) {
            $permissions = [];
            if (isset($options['tablePermissions'])) {
                $permissions = $this->db->all("SELECT perm FROM " . $options['tablePermissions'] . " ORDER BY perm");
            }
            if ($this->cache && $this->expire) {
                try {
                    $this->cache->set($this->key . '_perms', $permissions, $this->expire);
                } catch (CacheException $ignore) {}
            }
        }

        parent::__construct($groups, $permissions);
    }
    /**
     * save a user instance
     * @param  \vakata\user\UserInterface $user the user to store
     * @return self
     */
    public function saveUser(UserInterface $user) : UserManagementInterface
    {
        $data = array_merge([
            'name'       => null,
            'mail'       => null
        ], $user->getData());
        $this->db->begin();
        try {
            $userId = $user->getID();
            if ($userId && !$this->db->one("SELECT 1 FROM " . $this->options['tableUsers'] . " WHERE usr = ?", $userId)) {
                $userId = null;
            }
            // no user id and uniqueness constraints are configured - try to find the same user
            if (!$userId && count($this->unique)) {
                $sql = [];
                $par = [];
                foreach ($this->unique as $key) {
                    if (!isset($data[$key])) {
                        $sql = [];
                        break;
                    }
                    $sql[] = $key . ' = ?';
                    $par[] = $data[$key];
                }
                if (count($sql)) {
                    $userId = $this->db->one(
                        "SELECT usr FROM " . $this->options['tableUsers'] . " WHERE " . implode(' AND ', $sql),
                        $par
                    );
                }
            }
            unset($data['usr']);
            if (!$userId) {
                $userId = $this->db->table($this->options['tableUsers'])->insert($data)['usr'];
            } else {
                $this->db->table($this->options['tableUsers'])->filter('usr', $userId)->update($data);
            }
            $groupIDs = array_map(function ($v) {
                return $v->getID();
            }, $user->getGroups());
            if (isset($this->options['tableUserGroups'])) {
                $this->db->query(
                    "DELETE FROM " . $this->options['tableUserGroups'] . " WHERE usr = ?".
                    (count($groupIDs) ? " AND grp NOT IN (??)" : ""),
                    (count($groupIDs) ? [ $userId, $groupIDs ] : [$userId])
                );
                foreach ($user->getGroups() as $group) {
                    if (!$this->db->one(
                        "SELECT 1 FROM " . $this->options['tableUserGroups'] . " WHERE usr = ? AND grp = ?",
                        [ $userId, $group->getID() ]
                    )) {
                        $this->db->query(
                            "INSERT INTO " . $this->options['tableUserGroups'] . " (usr, grp, created) VALUES (?, ?, ?)",
                            [ $userId, $group->getID(), date('Y-m-d H:i:s') ]
                        );
                    }
                }
                $this->db->query(
                    "UPDATE " . $this->options['tableUserGroups'] . " SET main = 0 WHERE usr = ?",
                    [ $userId ]
                );
                if ($user->getPrimaryGroup()) {
                    $this->db->query(
                        "UPDATE " . $this->options['tableUserGroups'] . " SET main = 1 WHERE usr = ? AND grp = ?",
                        [ $userId, $user->getPrimaryGroup()->getID() ]
                    );
                }
            }

            $sql = [];
            $par = [$userId];
            foreach ($user->getProviders() as $provider) {
                $sql[] = '(provider = ? AND id = ?)';
                $par[] = $provider->getProvider();
                $par[] = $provider->getID();
            }
            $this->db->query(
                "DELETE FROM " . $this->options['tableProviders'] . " WHERE usr = ?" .
                 (count($sql) ? " AND NOT (".implode(' OR ', $sql).")" : ""),
                $par
            );
            foreach ($user->getProviders() as $provider) {
                $tmp = $this->db->one(
                    "SELECT usrprov, usr FROM " . $this->options['tableProviders'] . " WHERE provider = ? AND id = ?",
                    [ $provider->getProvider(), $provider->getID() ]
                );
                if (!$tmp) {
                    $this->db->query(
                        "INSERT INTO " . $this->options['tableProviders'] . " (provider, id, usr, name, data, created, used) VALUES (?, ?, ?, ?, ?, ?, ?)",
                        [ $provider->getProvider(), $provider->getID(), $userId, $provider->getName(), $provider->getData(), date('Y-m-d H:i:s', $provider->getCreated()), $provider->getUsed() ? date('Y-m-d H:i:s', $provider->getUsed()) : null ]
                    );
                } else {
                    if ((string)$tmp['usr'] !== (string)$userId) {
                        continue;
                    }
                    $this->db->query(
                        "UPDATE " . $this->options['tableProviders'] . " SET name = ?, data = ?, created = ?, used = ?, disabled = ? WHERE usrprov = ?",
                        [ $provider->getName(), $provider->getData(), date('Y-m-d H:i:s', $provider->getCreated()), $provider->getUsed() ? date('Y-m-d H:i:s', $provider->getUsed()) : null, $provider->enabled() ? 0 : 1, $tmp['usrprov'] ]
                    );
                }
            }
            $this->db->commit();
            $user->setID((string)$userId);
            parent::saveUser($user);
            return $this;
        }
        catch (\Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }
    /**
     * Delete a user.
     * @param  \vakata\user\UserInterface $user the user to delete
     * @return self
     */
    public function deleteUser(UserInterface $user) : UserManagementInterface
    {
        $this->db->begin();
        try {
            if (isset($this->options['tableUserGroups'])) {
                $this->db->query(
                    "DELETE FROM " . $this->options['tableUserGroups'] . " WHERE usr = ?",
                    [ $user->getID() ]
                );
            }
            $this->db->query(
                "DELETE FROM " . $this->options['tableProviders'] . " WHERE usr = ?",
                [ $user->getID() ]
            );
            $this->db->query(
                "DELETE FROM " . $this->options['tableUsers'] . " WHERE usr = ?",
                [ $user->getID() ]
            );
            $this->db->commit();
            parent::deleteUser($user);
            return $this;
        }
        catch (\Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    /**
     * Get a user instance by ID
     * @param  mixed  $id the user ID
     * @return \vakata\user\UserInterface a user instance
     */
    public function getUser($id) : UserInterface
    {
        try {
            return parent::getUser($id);
        }
        catch (UserException $e) {
            $data = $this->db->one(
                "SELECT * FROM " . $this->options['tableUsers'] . " WHERE usr = ?",
                [ $id ], 'assoc_lc'
            );
            if (!$data) {
                throw new UserException("User does not exist");
            }
            $primary = null;
            $groups = [];
            if (isset($this->options['tableUserGroups'])) {
                foreach (
                    $this->db->all(
                        "SELECT grp, main FROM " . $this->options['tableUserGroups'] . " WHERE usr = ? ORDER BY grp",
                        [ $id ]
                    ) as $v
                ) {
                    $grp = $this->getGroup($v['grp']);
                    if ((int)$v['main']) {
                        $primary = $grp;
                    }
                    $groups[] = $this->getGroup($v['grp']);
                }
            }

            $providers = $this->db->all(
                "SELECT * FROM " . $this->options['tableProviders'] . " WHERE usr = ?",
                [ $id ], null, false, 'assoc_lc'
            );
            $providers = array_map(function ($v) {
                return new Provider($v['provider'], $v['id'], $v['name'], $v['data'], $v['created'], $v['used'], (int)$v['disabled'] === 1);
            }, $providers);

            $user = new User((string)$data['usr'], $data, $groups, $primary, $providers);
            parent::saveUser($user);
            return $user;
        }
    }

    /**
     * Get a user instance by provider ID
     * @param  string  $provider the authentication provider
     * @param  string  $id the user ID
     * @return \vakata\user\UserInterface a user instance
     */
    public function getUserByProviderID(string $provider, string $id, bool $updateUsed = false) : UserInterface
    {
        $user = $this->db->one(
            "SELECT usr, usrprov FROM " . $this->options['tableProviders'] . " WHERE provider = ? AND id = ? AND disabled = 0",
            [ $provider, $id ]
        );
        if (!$user) {
            throw new UserException('User not found', 404);
        }
        $prov = $user['usrprov'];
        $user = $this->getUser((string)$user['usr']);
        if ($updateUsed) {
            $this->db->query(
                "UPDATE " . $this->options['tableProviders'] . " SET used = ? WHERE usrprov = ?",
                [ date('Y-m-d H:i:s'), $prov ]
            );
        }
        return $user;
    }

    /**
     * Get a group by its ID
     * @param  string   $id the ID to search for
     * @return \vakata\user\GroupInterface       the group instance
     */
    public function getGroup(string $id) : GroupInterface
    {
        try {
            return parent::getGroup($id);
        }
        catch (UserException $e) {
            $data = null;
            if ($this->options['tableGroups']) {
                $data = $this->db->one(
                    "SELECT grp, name FROM " . $this->options['tableGroups'] . " WHERE grp = ?",
                    $id
                );
            }
            if (!$data) {
                throw new UserException("Group does not exist");
            }
            $group = new Group(
                $id,
                $data['name'],
                isset($this->options['tableGroupsPermissions']) ?
                    $this->db->all(
                        "SELECT perm FROM " . $this->options['tableGroupsPermissions'] . " WHERE grp = ?",
                        $id
                    ) :
                    []
            );
            parent::saveGroup($group);
            return $group;
        }
    }
    /**
     * Save a group.
     * @param  \vakata\user\GroupInterface $group the group to save
     * @return self
     */
    public function saveGroup(GroupInterface $group) : UserManagementInterface
    {
        if (!isset($this->options['tableGroups'])) {
            throw new UserException("Cannot save without table");
        }
        $this->db->begin();
        try {
            if (!$group->getID() || !$this->db->one(
                "SELECT 1 FROM " . $this->options['tableGroups'] . " WHERE grp = ?",
                [ $group->getID() ]
            )) {
                $groupID = $this->db->table($this->options['tableGroups'])->insert([
                    'name' => $group->getName(),
                    'created' => date('Y-m-d H:i:s')
                ])['grp'];
                $group->setID((string)$groupID);
            } else {
                $this->db->query(
                    "UPDATE " . $this->options['tableGroups'] . " SET name = ? WHERE grp = ?",
                    [ $group->getName(), $group->getID() ]
                );
            }
            if (
                isset($this->options['tableGroupsPermissions']) &&
                isset($this->options['tablePermissions'])
            ) {
                $permissions = $group->getPermissions();
                $this->db->query(
                    "DELETE FROM " . $this->options['tableGroupsPermissions'] . " WHERE grp = ?" .
                    (count($permissions) ? " AND perm NOT IN (??)" : ""),
                    (count($permissions) ? [ $group->getID(), $permissions ] : [$group->getID()])
                );
                foreach ($group->getPermissions() as $permission) {
                    if (!$this->db->one(
                        "SELECT 1 FROM " . $this->options['tablePermissions'] . " WHERE perm = ?",
                        [ $permission ]
                    )) {
                        $this->db->query(
                            "INSERT INTO " . $this->options['tablePermissions'] . " (perm, created) VALUES (?, ?)",
                            [ $permission, date('Y-m-d H:i:s') ]
                        );
                    }
                    if (!$this->db->one(
                        "SELECT 1 FROM " . $this->options['tableGroupsPermissions'] . " WHERE grp = ? AND perm = ?",
                        [ $group->getID(), $permission ]
                    )) {
                        $this->db->query(
                            "INSERT INTO " . $this->options['tableGroupsPermissions'] . " (grp, perm, created) VALUES (?, ?, ?)",
                            [ $group->getID(), $permission, date('Y-m-d H:i:s') ]
                        );
                    }
                }
            }
            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollback();
            throw $e;
        }
        parent::saveGroup($group);
        try {
            if ($this->cache) {
                $this->cache->delete($this->key . '_groups');
            }
        } catch (CacheException $ignore) {}
        return $this;
    }
    /**
     * Delete a group.
     * @param  \vakata\user\GroupInterface $group the group to delete
     * @return self
     */
    public function deleteGroup(GroupInterface $group) : UserManagementInterface
    {
        if (!isset($this->options['tableGroups']) || !isset($this->options['tableUserGroups'])) {
            throw new UserException("Cannot save without table");
        }
        $this->db->begin();
        try {
            $this->db->query(
                "DELETE FROM " . $this->options['tableUserGroups'] . " WHERE grp = ?",
                [ $group->getID() ]
            );
            if (isset($this->options['tableGroupsPermissions'])) {
                $this->db->query(
                    "DELETE FROM " . $this->options['tableGroupsPermissions'] . " WHERE grp = ?",
                    [ $group->getID() ]
                );
            }
            $this->db->query(
                "DELETE FROM " . $this->options['tableGroups'] . " WHERE grp = ?",
                [ $group->getID() ]
            );
            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollback();
            throw $e;
        }
        parent::deleteGroup($group);
        try {
            if ($this->cache) {
                $this->cache->delete($this->key . '_groups');
            }
        } catch (CacheException $ignore) {}
        return $this;
    }
    /**
     * Add a permission.
     * @param  string $permission the permission to add
     * @return self
     */
    public function addPermission(string $permission) : UserManagementInterface
    {
        if (!isset($this->options['tablePermissions'])) {
            throw new UserException("Cannot save without table");
        }
        $this->db->begin();
        try {
            if (!$this->db->one(
                "SELECT 1 FROM " . $this->options['tablePermissions'] . " WHERE perm = ?",
                [ $permission ]
            )) {
                $this->db->query(
                    "INSERT INTO " . $this->options['tablePermissions'] . " (perm, created) VALUES (?, ?)",
                    [ $permission, date('Y-m-d H:i:s') ]
                );
            }
            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollback();
            throw $e;
        }
        parent::addPermission($permission);
        try {
            if ($this->cache) {
                $this->cache->delete($this->key . '_perms');
            }
        } catch (CacheException $ignore) {}
        return $this;
    }
    /**
     * Remove a permission.
     * @param  string $permission the permission to remove
     * @return self
     */
    public function deletePermission(string $permission) : UserManagementInterface
    {
        if (!isset($this->options['tablePermissions'])) {
            throw new UserException("Cannot save without table");
        }
        $this->db->begin();
        try {
            $this->db->query(
                "DELETE FROM " . $this->options['tableGroupsPermissions'] . " WHERE perm = ?",
                [ $permission ]
            );
            $this->db->query(
                "DELETE FROM " . $this->options['tablePermissions'] . " WHERE perm = ?",
                [ $permission ]
            );
            $this->db->commit();
        } catch (\Exception $e) {
            $this->db->rollback();
            throw $e;
        }
        parent::deletePermission($permission);
        try {
            if ($this->cache) {
                $this->cache->delete($this->key . '_perms');
            }
        } catch (CacheException $ignore) {}
        return $this;
    }

    public function searchUsers(array $query) : array
    {
        $q = $this->db->table($this->options['tableUsers']);
        foreach ($query as $k => $v) {
            $q->filter($k, $v);
        }
        try {
            $ids = $q->select(['usr']);
            $matches = [];
            foreach ($ids as $id) {
                $matches[] = $this->getUser((string)$id['usr']);
            }
        } catch (DBException $e) {
            throw new UserException("Invalid filter");
        }
        return $matches;
    }
}
