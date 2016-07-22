# vakata\user\UserManagementDatabase


## Methods

| Name | Description |
|------|-------------|
|[__construct](#vakata\user\usermanagementdatabase__construct)|Static init method.|
|[fromToken](#vakata\user\usermanagementdatabasefromtoken)|Creates a user instance from a token.|
|[saveUser](#vakata\user\usermanagementdatabasesaveuser)|save a user instance|
|[getUser](#vakata\user\usermanagementdatabasegetuser)|Get a user instance by ID|
|[getGroup](#vakata\user\usermanagementdatabasegetgroup)|Get a group by its ID|
|[saveGroup](#vakata\user\usermanagementdatabasesavegroup)|Save a group.|
|[secureToken](#vakata\user\usermanagementdatabasesecuretoken)|Signs and encrypts a given JWT using the set of rules provided when creating the instance.|
|[parseToken](#vakata\user\usermanagementdatabaseparsetoken)|Parse, verify and validate a token.|
|[permissions](#vakata\user\usermanagementdatabasepermissions)|Get the list of permissions in the system.|
|[permissionExists](#vakata\user\usermanagementdatabasepermissionexists)|Does a permission exist.|
|[groups](#vakata\user\usermanagementdatabasegroups)|Get a list of groups available in the system.|
|[groupExists](#vakata\user\usermanagementdatabasegroupexists)|Does a group exist.|

---



### vakata\user\UserManagementDatabase::__construct
Static init method.  
In addition to the `UserManagement` options, the keys also include:  
* tableUsers - the table to store the users in (defaults to "users")  
* tableProviders - the table linking users to providers (defaults to "users_providers")  
* tableGroups - the table containing the available groups (defaults to "users_groups")  
* tablePermissions - the table containing the available permissions (defaults to "users_permissions")  
* tableGroupsPermissions - the table containing each group's permissions (defaults to "users_groups_permissions")  
* tableUserGroups - the table containing each user's groups (defaults to "users_user_groups")  
* tableUserPermissions - the table containing each user's permissions (defaults to "users_user_permissions")

```php
public function __construct (  
    \vakata\database\DatabaseInterface $db,  
    array $options  
)   
```

|  | Type | Description |
|-----|-----|-----|
| `$db` | `\vakata\database\DatabaseInterface` | the DB instance |
| `$options` | `array` | the options for future instances |

---


### vakata\user\UserManagementDatabase::fromToken
Creates a user instance from a token.  


```php
public function fromToken (  
    \JWT|string $token,  
    string $decryptionKey  
) : \vakata\user\User    
```

|  | Type | Description |
|-----|-----|-----|
| `$token` | `\JWT`, `string` | the token |
| `$decryptionKey` | `string` | optional decryption key string |
|  |  |  |
| `return` | `\vakata\user\User` | the new user instance |

---


### vakata\user\UserManagementDatabase::saveUser
save a user instance  


```php
public function saveUser (  
    \vakata\user\UserInterface $user  
) : self    
```

|  | Type | Description |
|-----|-----|-----|
| `$user` | `\vakata\user\UserInterface` | the user to store |
|  |  |  |
| `return` | `self` |  |

---


### vakata\user\UserManagementDatabase::getUser
Get a user instance by ID  


```php
public function getUser (  
    mixed $id  
) : \vakata\user\UserInterface    
```

|  | Type | Description |
|-----|-----|-----|
| `$id` | `mixed` | the user ID |
|  |  |  |
| `return` | `\vakata\user\UserInterface` | a user instance |

---


### vakata\user\UserManagementDatabase::getGroup
Get a group by its ID  


```php
public function getGroup (  
    string $id  
) : \vakata\user\GroupInterface    
```

|  | Type | Description |
|-----|-----|-----|
| `$id` | `string` | the ID to search for |
|  |  |  |
| `return` | `\vakata\user\GroupInterface` | the group instance |

---


### vakata\user\UserManagementDatabase::saveGroup
Save a group.  


```php
public function saveGroup (  
    \vakata\user\GroupInterface $group  
) : self    
```

|  | Type | Description |
|-----|-----|-----|
| `$group` | `\vakata\user\GroupInterface` | the group to save |
|  |  |  |
| `return` | `self` |  |

---


### vakata\user\UserManagementDatabase::secureToken
Signs and encrypts a given JWT using the set of rules provided when creating the instance.  


```php
public function secureToken (  
    \JWT $token,  
    int|string $validity  
) : string    
```

|  | Type | Description |
|-----|-----|-----|
| `$token` | `\JWT` | the token to sign |
| `$validity` | `int`, `string` | the validity of the token in seconds or a strtotime expression (defaults to `86400`) |
|  |  |  |
| `return` | `string` | the signed (and optionally encrypted) token |

---


### vakata\user\UserManagementDatabase::parseToken
Parse, verify and validate a token.  


```php
public function parseToken (  
    \JWT|string $token  
) : array    
```

|  | Type | Description |
|-----|-----|-----|
| `$token` | `\JWT`, `string` | the token |
|  |  |  |
| `return` | `array` | of token claims |

---


### vakata\user\UserManagementDatabase::permissions
Get the list of permissions in the system.  


```php
public function permissions () : array    
```

|  | Type | Description |
|-----|-----|-----|
|  |  |  |
| `return` | `array` | the permissions available |

---


### vakata\user\UserManagementDatabase::permissionExists
Does a permission exist.  


```php
public function permissionExists (  
    string $permission  
) : boolean    
```

|  | Type | Description |
|-----|-----|-----|
| `$permission` | `string` | the permission to check for |
|  |  |  |
| `return` | `boolean` | does the permission exist |

---


### vakata\user\UserManagementDatabase::groups
Get a list of groups available in the system.  


```php
public function groups () : array    
```

|  | Type | Description |
|-----|-----|-----|
|  |  |  |
| `return` | `array` | an array of GroupInterface objects |

---


### vakata\user\UserManagementDatabase::groupExists
Does a group exist.  


```php
public function groupExists (  
    string $group  
) : boolean    
```

|  | Type | Description |
|-----|-----|-----|
| `$group` | `string` | the group to check for |
|  |  |  |
| `return` | `boolean` | does the group exist |

---

