# vakata\user\User


## Methods

| Name | Description |
|------|-------------|
|[__construct](#vakata\user\user__construct)|Create a new user instance.|
|[getID](#vakata\user\usergetid)|get the user's ID|
|[setID](#vakata\user\usersetid)|set the user's ID|
|[getData](#vakata\user\usergetdata)|get the user's data fields|
|[get](#vakata\user\userget)|Get a piece of user data.|
|[set](#vakata\user\userset)|Set a piece of user data.|
|[del](#vakata\user\userdel)|Delete an element from the storage.|
|[inGroup](#vakata\user\useringroup)|Is the user in a group.|
|[getGroups](#vakata\user\usergetgroups)|Get the user's groups|
|[getPrimaryGroup](#vakata\user\usergetprimarygroup)|Get the user's primary group|
|[hasPermission](#vakata\user\userhaspermission)|Does the user have a permission.|
|[getPermissions](#vakata\user\usergetpermissions)|Get the user's permissions|
|[addGroup](#vakata\user\useraddgroup)|Add the user to a group|
|[deleteGroup](#vakata\user\userdeletegroup)|Remove a user form a group|
|[setPrimaryGroup](#vakata\user\usersetprimarygroup)|Set the user's primary group|

---



### vakata\user\User::__construct
Create a new user instance.  


```php
public function __construct (  
    mixed $id,  
    array $data,  
    array $groups,  
    \vakata\user\GroupInterface $primary  
)   
```

|  | Type | Description |
|-----|-----|-----|
| `$id` | `mixed` | the user ID |
| `$data` | `array` | optional array of user data (defaults to an empty array) |
| `$groups` | `array` | optional array of GroupInterface objects the user belongs to (defaults to none) |
| `$primary` | `\vakata\user\GroupInterface` | the user's primary group name (defaults to `null`) |

---


### vakata\user\User::getID
get the user's ID  


```php
public function getID () : mixed    
```

|  | Type | Description |
|-----|-----|-----|
|  |  |  |
| `return` | `mixed` | the user ID |

---


### vakata\user\User::setID
set the user's ID  


```php
public function setID () : self    
```

|  | Type | Description |
|-----|-----|-----|
|  |  |  |
| `return` | `self` |  |

---


### vakata\user\User::getData
get the user's data fields  


```php
public function getData () : mixed    
```

|  | Type | Description |
|-----|-----|-----|
|  |  |  |
| `return` | `mixed` | the user's data |

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


### vakata\user\User::del
Delete an element from the storage.  


```php
public function del (  
    string $key,  
    string $separator  
) : mixed, null    
```

|  | Type | Description |
|-----|-----|-----|
| `$key` | `string` | the element to delete (can be a deeply nested element of the data array) |
| `$separator` | `string` | the string used to separate levels of the array, defaults to "." |
|  |  |  |
| `return` | `mixed`, `null` | the value that was just deleted or null |

---


### vakata\user\User::inGroup
Is the user in a group.  


```php
public function inGroup (  
    string|\vakata\user\GroupInterface $group  
) : boolean    
```

|  | Type | Description |
|-----|-----|-----|
| `$group` | `string`, `\vakata\user\GroupInterface` | the group to check for |
|  |  |  |
| `return` | `boolean` | is the user in the group |

---


### vakata\user\User::getGroups
Get the user's groups  


```php
public function getGroups () : array    
```

|  | Type | Description |
|-----|-----|-----|
|  |  |  |
| `return` | `array` | the user's group list |

---


### vakata\user\User::getPrimaryGroup
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


### vakata\user\User::getPermissions
Get the user's permissions  


```php
public function getPermissions () : array    
```

|  | Type | Description |
|-----|-----|-----|
|  |  |  |
| `return` | `array` | the user's permission list |

---


### vakata\user\User::addGroup
Add the user to a group  


```php
public function addGroup (  
    \vakata\user\GroupInterface $group  
) : self    
```

|  | Type | Description |
|-----|-----|-----|
| `$group` | `\vakata\user\GroupInterface` | the group to add the user to |
|  |  |  |
| `return` | `self` |  |

---


### vakata\user\User::deleteGroup
Remove a user form a group  


```php
public function deleteGroup (  
    \vakata\user\GroupInterface $group  
) : self    
```

|  | Type | Description |
|-----|-----|-----|
| `$group` | `\vakata\user\GroupInterface` | the group to remove the user from |
|  |  |  |
| `return` | `self` |  |

---


### vakata\user\User::setPrimaryGroup
Set the user's primary group  


```php
public function setPrimaryGroup (  
    \vakata\user\GroupInterface $group  
) : self    
```

|  | Type | Description |
|-----|-----|-----|
| `$group` | `\vakata\user\GroupInterface` | the group to set as primary |
|  |  |  |
| `return` | `self` |  |

---

