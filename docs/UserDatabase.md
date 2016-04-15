# vakata\user\UserDatabase


## Methods

| Name | Description |
|------|-------------|
|[init](#vakata\user\userdatabaseinit)|Static init method.|
|[createUser](#vakata\user\userdatabasecreateuser)|Create and store a new user in the database.|
|[fromToken](#vakata\user\userdatabasefromtoken)|Creates a user instance from a token.|
|[permissionCreate](#vakata\user\userdatabasepermissioncreate)|Create a new permission|
|[permissionDelete](#vakata\user\userdatabasepermissiondelete)|Delete a permission.|
|[groupAddPermission](#vakata\user\userdatabasegroupaddpermission)|Add a permission to a group.|
|[groupDeletePermission](#vakata\user\userdatabasegroupdeletepermission)|Delete a permission from a group.|
|[groupCreate](#vakata\user\userdatabasegroupcreate)|Create a new group.|
|[groupDelete](#vakata\user\userdatabasegroupdelete)|Delete a group.|
|[addGroup](#vakata\user\userdatabaseaddgroup)|Add the user to a group|
|[addPermission](#vakata\user\userdatabaseaddpermission)|Give the user a new permission|
|[deleteGroup](#vakata\user\userdatabasedeletegroup)|Remove a user form a group|
|[deletePermission](#vakata\user\userdatabasedeletepermission)|Remove a permission the user has.|
|[setPrimaryGroup](#vakata\user\userdatabasesetprimarygroup)|Set the user's primary group|
|[secureToken](#vakata\user\userdatabasesecuretoken)|Static sign token function. Signs and encrypts a given JWT using the set of rules provided in `init`.|
|[verifyToken](#vakata\user\userdatabaseverifytoken)|Static function for token verification. Will throw UserExceptions on invalid tokens.|
|[ipAddress](#vakata\user\userdatabaseipaddress)|get the client's IP address|
|[userAgent](#vakata\user\userdatabaseuseragent)|Get the user agent from the request.|
|[permissions](#vakata\user\userdatabasepermissions)|Get the list of permissions in the system.|
|[permissionExists](#vakata\user\userdatabasepermissionexists)|Does a permission exist.|
|[groups](#vakata\user\userdatabasegroups)|Get a list of groups available in the system.|
|[groupExists](#vakata\user\userdatabasegroupexists)|Does a group exist.|
|[groupPermissions](#vakata\user\userdatabasegrouppermissions)|Get a list of permissions included in group|
|[groupHasPermission](#vakata\user\userdatabasegrouphaspermission)|Does a group have a given permission.|
|[__construct](#vakata\user\userdatabase__construct)|Create a new user instance.|
|[get](#vakata\user\userdatabaseget)|Get a piece of user data.|
|[set](#vakata\user\userdatabaseset)|Set a piece of user data.|
|[inGroup](#vakata\user\userdatabaseingroup)|Is the user in a group.|
|[hasPermission](#vakata\user\userdatabasehaspermission)|Does the user have a permission.|
|[getGroups](#vakata\user\userdatabasegetgroups)|Get the user's groups|
|[getPrimaryGroup](#vakata\user\userdatabasegetprimarygroup)|Get the user's primary group|

---



### vakata\user\UserDatabase::init
Static init method.  
In addition to the `User` options, the keys also include:  
* register - should new users with valid tokens be registered (defaults to `false`)  
* tableUsers - the table to store the users in (defaults to "users")  
* tableProviders - the table linking users to providers (defaults to "users_providers")  
* tableGroups - the table containing the available groups (defaults to "users_groups")  
* tablePermissions - the table containing the available permissions (defaults to "users_permissions")  
* tableGroupsPermissions - the table containing each group's permissions (defaults to "users_groups_permissions")  
* tableUserGroups - the table containing each user's groups (defaults to "users_user_groups")  
* tableUserPermissions - the table containing each user's permissions (defaults to "users_user_permissions")

```php
public static function init (  
    array $options,  
    \vakata\database\DatabaseInterface $db  
)   
```

|  | Type | Description |
|-----|-----|-----|
| `$options` | `array` | the options for future instances |
| `$db` | `\vakata\database\DatabaseInterface` | the DB instance |

---


### vakata\user\UserDatabase::createUser
Create and store a new user in the database.  


```php
public static function createUser (  
    array $data  
) : integer    
```

|  | Type | Description |
|-----|-----|-----|
| `$data` | `array` | the user data |
|  |  |  |
| `return` | `integer` | the user ID |

---


### vakata\user\UserDatabase::fromToken
Creates a user instance from a token.  


```php
public static function fromToken (  
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


### vakata\user\UserDatabase::permissionCreate
Create a new permission  


```php
public static function permissionCreate (  
    string $permission  
)   
```

|  | Type | Description |
|-----|-----|-----|
| `$permission` | `string` | the new permission |

---


### vakata\user\UserDatabase::permissionDelete
Delete a permission.  


```php
public static function permissionDelete (  
    string $permission  
)   
```

|  | Type | Description |
|-----|-----|-----|
| `$permission` | `string` | the permission to delete |

---


### vakata\user\UserDatabase::groupAddPermission
Add a permission to a group.  


```php
public static function groupAddPermission (  
    string $group,  
    string $permission  
)   
```

|  | Type | Description |
|-----|-----|-----|
| `$group` | `string` | the group to add a permission to |
| `$permission` | `string` | the permission to add |

---


### vakata\user\UserDatabase::groupDeletePermission
Delete a permission from a group.  


```php
public static function groupDeletePermission (  
    string $group,  
    string $permission  
)   
```

|  | Type | Description |
|-----|-----|-----|
| `$group` | `string` | the group being modified |
| `$permission` | `string` | the permission being removed |

---


### vakata\user\UserDatabase::groupCreate
Create a new group.  


```php
public static function groupCreate (  
    string $group,  
    array $permissions  
)   
```

|  | Type | Description |
|-----|-----|-----|
| `$group` | `string` | the group name |
| `$permissions` | `array` | optional array of permission for that group (defaults to an empty array) |

---


### vakata\user\UserDatabase::groupDelete
Delete a group.  


```php
public static function groupDelete (  
    string $group  
)   
```

|  | Type | Description |
|-----|-----|-----|
| `$group` | `string` | the group to delete |

---


### vakata\user\UserDatabase::addGroup
Add the user to a group  


```php
public function addGroup (  
    string $group  
)   
```

|  | Type | Description |
|-----|-----|-----|
| `$group` | `string` | the group to add the user to |

---


### vakata\user\UserDatabase::addPermission
Give the user a new permission  


```php
public function addPermission (  
    string $permission  
)   
```

|  | Type | Description |
|-----|-----|-----|
| `$permission` | `string` | the permission to give |

---


### vakata\user\UserDatabase::deleteGroup
Remove a user form a group  


```php
public function deleteGroup (  
    string $group  
)   
```

|  | Type | Description |
|-----|-----|-----|
| `$group` | `string` | the group to remove the user from |

---


### vakata\user\UserDatabase::deletePermission
Remove a permission the user has.  


```php
public function deletePermission (  
    string $permission  
)   
```

|  | Type | Description |
|-----|-----|-----|
| `$permission` | `string` | the permission to remove |

---


### vakata\user\UserDatabase::setPrimaryGroup
Set the user's primary group  


```php
public function setPrimaryGroup ()   
```


---


### vakata\user\UserDatabase::secureToken
Static sign token function. Signs and encrypts a given JWT using the set of rules provided in `init`.  


```php
public static function secureToken (  
    \JWT $token,  
    boolean|string $encrypt,  
    int|string $validity  
) : string    
```

|  | Type | Description |
|-----|-----|-----|
| `$token` | `\JWT` | the token to sign |
| `$encrypt` | `boolean`, `string` | should the token be encrypted (or a string key) (defaults to `true`) |
| `$validity` | `int`, `string` | the validity of the token in seconds or a strtotime expression (defaults to `86400`) |
|  |  |  |
| `return` | `string` | the signed and encrypted token |

---


### vakata\user\UserDatabase::verifyToken
Static function for token verification. Will throw UserExceptions on invalid tokens.  


```php
public static function verifyToken (  
    \JWT $token  
) : string    
```

|  | Type | Description |
|-----|-----|-----|
| `$token` | `\JWT` | the token to verify |
|  |  |  |
| `return` | `string` | the validated claims |

---


### vakata\user\UserDatabase::ipAddress
get the client's IP address  


```php
public static function ipAddress () : string    
```

|  | Type | Description |
|-----|-----|-----|
|  |  |  |
| `return` | `string` | the client's IP |

---


### vakata\user\UserDatabase::userAgent
Get the user agent from the request.  


```php
public static function userAgent () : string    
```

|  | Type | Description |
|-----|-----|-----|
|  |  |  |
| `return` | `string` | the user agent |

---


### vakata\user\UserDatabase::permissions
Get the list of permissions in the system.  


```php
public static function permissions () : array    
```

|  | Type | Description |
|-----|-----|-----|
|  |  |  |
| `return` | `array` | the permissions available |

---


### vakata\user\UserDatabase::permissionExists
Does a permission exist.  


```php
public static function permissionExists (  
    string $permission  
) : boolean    
```

|  | Type | Description |
|-----|-----|-----|
| `$permission` | `string` | the permission to check for |
|  |  |  |
| `return` | `boolean` | does the permission exist |

---


### vakata\user\UserDatabase::groups
Get a list of groups available in the system.  


```php
public static function groups () : array    
```

|  | Type | Description |
|-----|-----|-----|
|  |  |  |
| `return` | `array` | an array of group strings |

---


### vakata\user\UserDatabase::groupExists
Does a group exist.  


```php
public static function groupExists (  
    string $group  
) : boolean    
```

|  | Type | Description |
|-----|-----|-----|
| `$group` | `string` | the group to check for |
|  |  |  |
| `return` | `boolean` | does the group exist |

---


### vakata\user\UserDatabase::groupPermissions
Get a list of permissions included in group  


```php
public static function groupPermissions (  
    string $group  
) : array    
```

|  | Type | Description |
|-----|-----|-----|
| `$group` | `string` | the group to check |
|  |  |  |
| `return` | `array` | an array of permissions in that group |

---


### vakata\user\UserDatabase::groupHasPermission
Does a group have a given permission.  


```php
public static function groupHasPermission (  
    string $group,  
    string $permission  
) : boolean    
```

|  | Type | Description |
|-----|-----|-----|
| `$group` | `string` | the group to check |
| `$permission` | `string` | the permission to check |
|  |  |  |
| `return` | `boolean` | is the permission incldued in that group |

---


### vakata\user\UserDatabase::__construct
Create a new user instance.  


```php
public function __construct (  
    mixed $id,  
    array $data,  
    array $groups,  
    array $permissions,  
    string|null $primary  
)   
```

|  | Type | Description |
|-----|-----|-----|
| `$id` | `mixed` | the user ID |
| `$data` | `array` | optional array of user data (defaults to an empty array) |
| `$groups` | `array` | optional array of groups the user belongs to (defaults to an empty array) |
| `$permissions` | `array` | optional array of permissions the user has (defaults to an empty array) |
| `$primary` | `string`, `null` | the user's primary group name (defaults to `null`) |

---


### vakata\user\UserDatabase::get
Get a piece of user data.  


```php
public function get (  
    string $key,  
    mixed $default,  
    string $separator  
) : mixed    
```

|  | Type | Description |
|-----|-----|-----|
| `$key` | `string` | the data to search for - use '.' to traverse arrays |
| `$default` | `mixed` | optional default to return if the key does not exist, defaults to `null` |
| `$separator` | `string` | the separator to use when traversing arrays, defaults to '.' |
|  |  |  |
| `return` | `mixed` | the key value or the default |

---


### vakata\user\UserDatabase::set
Set a piece of user data.  


```php
public function set (  
    string $key,  
    mixed $value,  
    string $separator  
)   
```

|  | Type | Description |
|-----|-----|-----|
| `$key` | `string` | the key to set, use '.' to traverse arrays |
| `$value` | `mixed` | the new value for the key |
| `$separator` | `string` | the separator to use when traversing arrays, defaults to '.' |

---


### vakata\user\UserDatabase::inGroup
Is the user in a group.  


```php
public function inGroup (  
    string $group  
) : boolean    
```

|  | Type | Description |
|-----|-----|-----|
| `$group` | `string` | the group to check for |
|  |  |  |
| `return` | `boolean` | is the user in the group |

---


### vakata\user\UserDatabase::hasPermission
Does the user have a permission.  


```php
public function hasPermission (  
    string $permission  
) : boolean    
```

|  | Type | Description |
|-----|-----|-----|
| `$permission` | `string` | the permission to check for |
|  |  |  |
| `return` | `boolean` | does the user have that permission |

---


### vakata\user\UserDatabase::getGroups
Get the user's groups  


```php
public function getGroups (  
    array $groups  
)   
```

|  | Type | Description |
|-----|-----|-----|
| `$groups` | `array` | the user's group list |

---


### vakata\user\UserDatabase::getPrimaryGroup
Get the user's primary group  


```php
public function getPrimaryGroup (  
    string $group  
)   
```

|  | Type | Description |
|-----|-----|-----|
| `$group` | `string` | the user's primary group |

---

