# vakata\user\User


## Methods

| Name | Description |
|------|-------------|
|[init](#vakata\user\userinit)|Static init method.|
|[secureToken](#vakata\user\usersecuretoken)|Static sign token function. Signs and encrypts a given JWT using the set of rules provided in `init`.|
|[verifyToken](#vakata\user\userverifytoken)|Static function for token verification. Will throw UserExceptions on invalid tokens.|
|[fromToken](#vakata\user\userfromtoken)|Creates a user instance from a token.|
|[ipAddress](#vakata\user\useripaddress)|get the client's IP address|
|[userAgent](#vakata\user\useruseragent)|Get the user agent from the request.|
|[permissions](#vakata\user\userpermissions)|Get the list of permissions in the system.|
|[permissionExists](#vakata\user\userpermissionexists)|Does a permission exist.|
|[permissionCreate](#vakata\user\userpermissioncreate)|Create a new permission|
|[permissionDelete](#vakata\user\userpermissiondelete)|Delete a permission.|
|[groups](#vakata\user\usergroups)|Get a list of groups available in the system.|
|[groupExists](#vakata\user\usergroupexists)|Does a group exist.|
|[groupPermissions](#vakata\user\usergrouppermissions)|Get a list of permissions included in group|
|[groupHasPermission](#vakata\user\usergrouphaspermission)|Does a group have a given permission.|
|[groupAddPermission](#vakata\user\usergroupaddpermission)|Add a permission to a group.|
|[groupDeletePermission](#vakata\user\usergroupdeletepermission)|Delete a permission from a group.|
|[groupCreate](#vakata\user\usergroupcreate)|Create a new group.|
|[groupDelete](#vakata\user\usergroupdelete)|Delete a group.|
|[__construct](#vakata\user\user__construct)|Create a new user instance.|
|[get](#vakata\user\userget)|Get a piece of user data.|
|[set](#vakata\user\userset)|Set a piece of user data.|
|[inGroup](#vakata\user\useringroup)|Is the user in a group.|
|[hasPermission](#vakata\user\userhaspermission)|Does the user have a permission.|
|[addGroup](#vakata\user\useraddgroup)|Add the user to a group|
|[addPermission](#vakata\user\useraddpermission)|Give the user a new permission|
|[deletePermission](#vakata\user\userdeletepermission)|Remove a permission the user has.|
|[deleteGroup](#vakata\user\userdeletegroup)|Remove a user form a group|

---



### vakata\user\User::init
Static init method.  
Options include:  
* issuer - the issuer to use when signing JWT's  
* key - the key to use when signing JWT's (could be an array of keys)  
* validateIpAddress - should IP's be validated in tokens (defaults to `true`)  
* validateUserAgent - should the user agent be validated in tokens (defaults to `true`)  
* validateSessionID - should the session ID be validated in tokens (defaults to `true`)  
* groups - the groups in the system, an array of strings  
* permissions - the permissions in the system, an array of strings

```php
public static function init (  
    array $options  
)   
```

|  | Type | Description |
|-----|-----|-----|
| `$options` | `array` | the options for future instances |

---


### vakata\user\User::secureToken
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


### vakata\user\User::verifyToken
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


### vakata\user\User::fromToken
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


### vakata\user\User::ipAddress
get the client's IP address  


```php
public static function ipAddress () : string    
```

|  | Type | Description |
|-----|-----|-----|
|  |  |  |
| `return` | `string` | the client's IP |

---


### vakata\user\User::userAgent
Get the user agent from the request.  


```php
public static function userAgent () : string    
```

|  | Type | Description |
|-----|-----|-----|
|  |  |  |
| `return` | `string` | the user agent |

---


### vakata\user\User::permissions
Get the list of permissions in the system.  


```php
public static function permissions () : array    
```

|  | Type | Description |
|-----|-----|-----|
|  |  |  |
| `return` | `array` | the permissions available |

---


### vakata\user\User::permissionExists
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


### vakata\user\User::permissionCreate
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


### vakata\user\User::permissionDelete
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


### vakata\user\User::groups
Get a list of groups available in the system.  


```php
public static function groups () : array    
```

|  | Type | Description |
|-----|-----|-----|
|  |  |  |
| `return` | `array` | an array of group strings |

---


### vakata\user\User::groupExists
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


### vakata\user\User::groupPermissions
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


### vakata\user\User::groupHasPermission
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


### vakata\user\User::groupAddPermission
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


### vakata\user\User::groupDeletePermission
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


### vakata\user\User::groupCreate
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


### vakata\user\User::groupDelete
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


### vakata\user\User::__construct
Create a new user instance.  


```php
public function __construct (  
    mixed $id,  
    array $data,  
    array $groups,  
    array $permissions  
)   
```

|  | Type | Description |
|-----|-----|-----|
| `$id` | `mixed` | the user ID |
| `$data` | `array` | optional array of user data (defaults to an empty array) |
| `$groups` | `array` | optional array of groups the user belongs to (defaults to an empty array) |
| `$permissions` | `array` | optional array of permissions the user has (defaults to an empty array) |

---


### vakata\user\User::get
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


### vakata\user\User::set
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


### vakata\user\User::inGroup
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


### vakata\user\User::hasPermission
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


### vakata\user\User::addGroup
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


### vakata\user\User::addPermission
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


### vakata\user\User::deletePermission
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


### vakata\user\User::deleteGroup
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

