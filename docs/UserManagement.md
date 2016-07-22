# vakata\user\UserManagement


## Methods

| Name | Description |
|------|-------------|
|[__construct](#vakata\user\usermanagement__construct)|create an instance|
|[secureToken](#vakata\user\usermanagementsecuretoken)|Signs and encrypts a given JWT using the set of rules provided when creating the instance.|
|[parseToken](#vakata\user\usermanagementparsetoken)|Parse, verify and validate a token.|
|[fromToken](#vakata\user\usermanagementfromtoken)|Creates a user instance from a token.|
|[permissions](#vakata\user\usermanagementpermissions)|Get the list of permissions in the system.|
|[permissionExists](#vakata\user\usermanagementpermissionexists)|Does a permission exist.|
|[groups](#vakata\user\usermanagementgroups)|Get a list of groups available in the system.|
|[groupExists](#vakata\user\usermanagementgroupexists)|Does a group exist.|
|[getUser](#vakata\user\usermanagementgetuser)|Get a user instance by ID|
|[saveUser](#vakata\user\usermanagementsaveuser)|save a user instance|
|[getGroup](#vakata\user\usermanagementgetgroup)|Get a group by its ID|
|[saveGroup](#vakata\user\usermanagementsavegroup)|Save a group.|

---



### vakata\user\UserManagement::__construct
create an instance  
* Options include:  
* issuer - the issuer to use when signing JWTs  
* cryptokey - the key to used to encrypt / decrypt JWTs  
* key - the key to use when signing JWT's (could be an array of keys)  
* validateIpAddress - the required IP address of the user (defaults to `null`)  
* validateUserAgent - the required user agent of the user (defaults to `null`)  
* validateSessionID - the required session ID of the user (defaults to `null`)

```php
public function __construct (  
    array $groups,  
    array $permissions,  
    array $options  
)   
```

|  | Type | Description |
|-----|-----|-----|
| `$groups` | `array` | array of GroupInterface objects |
| `$permissions` | `array` | array of strings |
| `$options` | `array` | the instance's options described above |

---


### vakata\user\UserManagement::secureToken
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


### vakata\user\UserManagement::parseToken
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


### vakata\user\UserManagement::fromToken
Creates a user instance from a token.  


```php
public function fromToken (  
    \JWT|string $token  
) : \vakata\user\User    
```

|  | Type | Description |
|-----|-----|-----|
| `$token` | `\JWT`, `string` | the token |
|  |  |  |
| `return` | `\vakata\user\User` | the new user instance |

---


### vakata\user\UserManagement::permissions
Get the list of permissions in the system.  


```php
public function permissions () : array    
```

|  | Type | Description |
|-----|-----|-----|
|  |  |  |
| `return` | `array` | the permissions available |

---


### vakata\user\UserManagement::permissionExists
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


### vakata\user\UserManagement::groups
Get a list of groups available in the system.  


```php
public function groups () : array    
```

|  | Type | Description |
|-----|-----|-----|
|  |  |  |
| `return` | `array` | an array of GroupInterface objects |

---


### vakata\user\UserManagement::groupExists
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


### vakata\user\UserManagement::getUser
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


### vakata\user\UserManagement::saveUser
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


### vakata\user\UserManagement::getGroup
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


### vakata\user\UserManagement::saveGroup
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

