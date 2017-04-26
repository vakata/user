# user

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Code Climate][ico-cc]][link-cc]
[![Tests Coverage][ico-cc-coverage]][link-cc]

A PHP user class.

## Install

Via Composer

``` bash
$ composer require vakata/user
```

## Usage

``` php
use \vakata\database\DB;
use \vakata\user\UserManagementDatabase;
use \vakata\user\Group;
use \vakata\user\User;

$db = new DB('mysql://root@127.0.0.1/dbname');

$usrm = new UserManagementDatabase($db, [
    'tableUsers'             => 'users',
    'tableProviders'         => 'user_providers',
    'tableGroups'            => 'groups',
    'tablePermissions'       => 'permissions',
    'tableGroupsPermissions' => 'group_permissions',
    'tableUserGroups'        => 'user_groups'
]);

// get a user by ID
$user = $usrm->getUser(1);
// or by a provider
$user = $usrm->getUserByProviderID($provider, $providerID);

// add a group
$group = new Group(1, "Name", ["some", "permissions"]);
$usrm->saveGroup($group);
// add the new group to a user
$user->addGroup($group);
$usrm->saveUser($user);
```

Read more in the [API docs](docs/README.md)

## Testing

``` bash
$ composer test
```


## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email github@vakata.com instead of using the issue tracker.

## Credits

- [vakata][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information. 

[ico-version]: https://img.shields.io/packagist/v/vakata/user.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/vakata/user/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/vakata/user.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/vakata/user.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/vakata/user.svg?style=flat-square
[ico-cc]: https://img.shields.io/codeclimate/github/vakata/user.svg?style=flat-square
[ico-cc-coverage]: https://img.shields.io/codeclimate/coverage/github/vakata/user.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/vakata/user
[link-travis]: https://travis-ci.org/vakata/user
[link-scrutinizer]: https://scrutinizer-ci.com/g/vakata/user/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/vakata/user
[link-downloads]: https://packagist.org/packages/vakata/user
[link-author]: https://github.com/vakata
[link-contributors]: ../../contributors
[link-cc]: https://codeclimate.com/github/vakata/user

