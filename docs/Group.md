# vakata\user\Group


## Methods

| Name | Description |
|------|-------------|
|[__construct](#vakata\user\group__construct)|Create a new group instance.|
|[getID](#vakata\user\groupgetid)|get the group's ID|
|[setID](#vakata\user\groupsetid)|set the group's ID|
|[getPermissions](#vakata\user\groupgetpermissions)|Get the group's permissions|
|[hasPermission](#vakata\user\grouphaspermission)|Does the group have a permission.|
|[addPermission](#vakata\user\groupaddpermission)|Give the group a new permission|
|[deletePermission](#vakata\user\groupdeletepermission)|Remove a permission the group has.|

---



### vakata\user\Group::__construct
Create a new group instance.  


```php
public function __construct (  
    mixed $id,  
    array $permissions  
)   
```

|  | Type | Description |
|-----|-----|-----|
| `$id` | `mixed` | the group ID |
| `$permissions` | `array` | optional array of permissions the group has (defaults to an empty array) |

---


### vakata\user\Group::getID
get the group's ID  


```php
public function getID () : mixed    
```

|  | Type | Description |
|-----|-----|-----|
|  |  |  |
| `return` | `mixed` | the group ID |

---


### vakata\user\Group::setID
set the group's ID  


```php
public function setID () : self    
```

|  | Type | Description |
|-----|-----|-----|
|  |  |  |
| `return` | `self` |  |

---


### vakata\user\Group::getPermissions
Get the group's permissions  


```php
public function getPermissions () : array    
```

|  | Type | Description |
|-----|-----|-----|
|  |  |  |
| `return` | `array` | the group's permission list |

---


### vakata\user\Group::hasPermission
Does the group have a permission.  


```php
public function hasPermission (  
    string $permission  
) : boolean    
```

|  | Type | Description |
|-----|-----|-----|
| `$permission` | `string` | the permission to check for |
|  |  |  |
| `return` | `boolean` | does the group have that permission |

---


### vakata\user\Group::addPermission
Give the group a new permission  


```php
public function addPermission (  
    string $permission  
) : self    
```

|  | Type | Description |
|-----|-----|-----|
| `$permission` | `string` | the permission to give |
|  |  |  |
| `return` | `self` |  |

---


### vakata\user\Group::deletePermission
Remove a permission the group has.  


```php
public function deletePermission (  
    string $permission  
) : self    
```

|  | Type | Description |
|-----|-----|-----|
| `$permission` | `string` | the permission to remove |
|  |  |  |
| `return` | `self` |  |

---

