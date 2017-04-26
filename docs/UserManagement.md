# vakata\user\UserManagement


## Methods

| Name | Description |
|------|-------------|
|[__construct](#vakata\user\usermanagement__construct)|create an instance|
|[getUserByProviderID](#vakata\user\usermanagementgetuserbyproviderid)|Get a user instance by provider ID|
|[permissions](#vakata\user\usermanagementpermissions)|Get the list of permissions in the system.|
|[permissionExists](#vakata\user\usermanagementpermissionexists)|Does a permission exist.|
|[groups](#vakata\user\usermanagementgroups)|Get a list of groups available in the system.|
|[groupExists](#vakata\user\usermanagementgroupexists)|Does a group exist.|
|[getUser](#vakata\user\usermanagementgetuser)|Get a user instance by ID|
|[saveUser](#vakata\user\usermanagementsaveuser)|save a user instance|
|[deleteUser](#vakata\user\usermanagementdeleteuser)|Delete a user.|
|[getGroup](#vakata\user\usermanagementgetgroup)|Get a group by its ID|
|[saveGroup](#vakata\user\usermanagementsavegroup)|Save a group.|
|[deleteGroup](#vakata\user\usermanagementdeletegroup)|Delete a group.|
|[addPermission](#vakata\user\usermanagementaddpermission)|Add a permission.|
|[deletePermission](#vakata\user\usermanagementdeletepermission)|Remove a permission.|

---



### vakata\user\UserManagement::__construct
create an instance  


```php
public function __construct (  
    array $groups,  
    array $permissions,  
    array $users  
)   
```

|  | Type | Description |
|-----|-----|-----|
| `$groups` | `array` | array of GroupInterface objects |
| `$permissions` | `array` | array of strings |
| `$users` | `array` | array of UserInterface objects |

---


### vakata\user\UserManagement::getUserByProviderID
Get a user instance by provider ID  


```php
public function getUserByProviderID (  
    string $provider,  
    mixed $id  
) : \vakata\user\UserInterface    
```

|  | Type | Description |
|-----|-----|-----|
| `$provider` | `string` | the authentication provider |
| `$id` | `mixed` | the user ID |
|  |  |  |
| `return` | `\vakata\user\UserInterface` | a user instance |

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


### vakata\user\UserManagement::deleteUser
Delete a user.  


```php
public function deleteUser (  
    \vakata\user\UserInterface $user  
) : self    
```

|  | Type | Description |
|-----|-----|-----|
| `$user` | `\vakata\user\UserInterface` | the user to delete |
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


### vakata\user\UserManagement::deleteGroup
Delete a group.  


```php
public function deleteGroup (  
    \vakata\user\GroupInterface $group  
) : self    
```

|  | Type | Description |
|-----|-----|-----|
| `$group` | `\vakata\user\GroupInterface` | the group to delete |
|  |  |  |
| `return` | `self` |  |

---


### vakata\user\UserManagement::addPermission
Add a permission.  


```php
public function addPermission (  
    string $permission  
) : self    
```

|  | Type | Description |
|-----|-----|-----|
| `$permission` | `string` | the permission to add |
|  |  |  |
| `return` | `self` |  |

---


### vakata\user\UserManagement::deletePermission
Remove a permission.  


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

