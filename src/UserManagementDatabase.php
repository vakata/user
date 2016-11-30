<?php
namespace vakata\user;

use vakata\database\DatabaseInterface;

class UserManagementDatabase extends UserManagement
{
    protected $db;
    protected $options;

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
     * @param  \vakata\database\DatabaseInterface $db the DB instance
     * @param  array  $options the options for future instances
     */
    public function __construct(DatabaseInterface $db, array $options = [])
    {
        $options = array_merge([
            'tableUsers'             => 'users',
            'tableProviders'         => 'users_providers',
            'tableGroups'            => 'users_groups',
            'tablePermissions'       => 'users_permissions',
            'tableGroupsPermissions' => 'users_groups_permissions',
            'tableUserGroups'        => 'users_user_groups'
        ], $options);

        $this->options = $options;
        $this->db = $db;

        $temp = $this->db->all("
            SELECT
                g.grp,
                p.perm
            FROM " . $options['tableGroups'] . " g
            LEFT JOIN " . $options['tableGroupsPermissions'] . " p ON p.grp = g.grp
            ORDER BY g.grp, p.perm
        ", null, null, false, 'assoc_lc');
        $groups = [];
        foreach ($temp as $row) {
            if (!isset($groups[$row['grp']])) {
                $groups[$row['grp']] = [];
            }
            $groups[$row['grp']][] = $row['perm'];
        }
        foreach ($groups as $id => $permissions) {
            $groups[$id] = new Group($id, $permissions);
        }

        $permissions = $this->db->all("SELECT perm FROM " . $options['tablePermissions'] . " ORDER BY perm");

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
            'mail'       => null,
            'provider'   => null,
            'providerId' => null
        ], $user->getData());
        $this->db->begin();
        try {
            $userId = $user->getID();
            // if there is a valid email address - try to locate a user with this mail address
            if (!$userId && filter_var((string)$data['mail'], FILTER_VALIDATE_EMAIL)) {
                $userId = $this->db->one(
                    "SELECT usr FROM " . $this->options['tableUsers'] . " WHERE mail = ?",
                    [ (string)$data['mail'] ]
                );
            }
            // if there was not user with that email address, or the email was invalid - register a new user
            if (!$userId) {
                if ($this->db->driver() === 'oracle') {
                    $userId = 0;
                    $this->db->query(
                        "INSERT INTO " . $this->options['tableUsers'] . " (name, mail) VALUES (?, ?) RETURNING usr INTO ?",
                        [ (string)$data['name'], (string)$data['mail'], &$userId ]
                    );
                } else {
                    $userId = $this->db->query(
                        "INSERT INTO " . $this->options['tableUsers'] . " (name, mail) VALUES (?, ?)",
                        [ (string)$data['name'], (string)$data['mail'] ]
                    )->insertId();
                }
            }
            if ($data['provider'] && $data['providerId']) {
                $this->db->query(
                    "INSERT INTO " . $this->options['tableProviders'] . " (provider, id, usr, created) VALUES (?, ?, ?, ?)",
                    [ (string)$data['provider'], (string)$data['providerId'], $userId, date('Y-m-d H:i:s') ]
                );
            }
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
            $this->db->commit();
            $user->setID($userId);
            parent::saveUser($user);
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
            $data = array_merge([ 'provider' => '', 'providerId' => $id ], $data);
            $primary = null;

            $groups = $this->db->all(
                "SELECT grp FROM " . $this->options['tableUserGroups'] . " WHERE usr = ? ORDER BY grp",
                [ $id ]
            );
            $groups = array_map(function ($v) {
                return $this->getGroup($v);
            }, $groups);
            $primary = $this->db->one(
                "SELECT grp FROM " . $this->options['tableUserGroups'] . " WHERE usr = ? AND main = 1",
                [ $id ]
            );
            if ($primary) {
                $primary = $this->getGroup($primary);
            }
            $user = new User($data['usr'], $data, $groups, $primary);
            parent::saveUser($user);
            return $user;
        }
    }

    /**
     * Get a user instance by provider ID
     * @param  string  $provider the authentication provider
     * @param  mixed   $id the user ID
     * @return \vakata\user\UserInterface a user instance
     */
    public function getUserByProviderID($provider, $id) : UserInterface
    {
        $user = $this->db->one(
            "SELECT usr FROM " . $this->options['tableProviders'] . " WHERE provider = ? AND id = ?",
            [ $provider, $id ]
        );
        if (!$user) {
            throw new UserException('Invalid user');
        }
        return $this->getUser($user);
    }

    /**
     * Get a group by its ID
     * @param  string   $id the ID to search for
     * @return \vakata\user\GroupInterface       the group instance
     */
    public function getGroup($id) : GroupInterface
    {
        try {
            return parent::getGroup($id);
        }
        catch (UserException $e) {
            if (!$this->db->one("SELECT grp FROM " . $this->options['tableUserGroups'] . " WHERE grp = ?", $id)) {
                throw new UserException("Group does not exist");
            }
            $group = new Group(
                $id,
                $this->db->all("SELECT perm FROM " . $this->options['tableGroupsPermissions'] . " WHERE grp = ?", $id)
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
        $trans = $this->db->begin();
        try {
            if (!$this->db->one(
                "SELECT 1 FROM " . $this->options['tableGroups'] . " WHERE grp = ?",
                [ $group->getID() ]
            )) {
                $this->db->query(
                    "INSERT INTO " . $this->options['tableGroups'] . " (grp, created) VALUES (?, ?)",
                    [ $group->getID(), date('Y-m-d H:i:s') ]
                );
            }
            foreach ($group->getPermissions() as $permission) {
                if (!$this->db->one(
                    "SELECT 1 FROM " . $this->options['tablePermissions'] . " WHERE perm = ?",
                    [ $permission ]
                )) {
                    $this->db->query(
                        "INSERT INTO " . $this->options['tablePermissions'] . " (perm, created) VALUES (?, ?)
                        ON DUPLICATE KEY UPDATE perm = perm",
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
            $this->db->commit();
            parent::saveGroup($group);
            return $this;
        }
        catch (\Exception $e) {
            $this->db->rollback($trans);
            throw $e;
        }
    }
}
