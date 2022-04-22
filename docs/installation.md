# Installation

## Configurations

### Assets

Add the bundles assets to the framework configuration.

```yaml
# config/packages/assets.yaml
framework:
    assets:
        packages:
            fcm_admin:
                json_manifest_path: '%kernel.project_dir%/public/build/codebuds/fcm/admin/manifest.json'

```

#### Webpack

Add the configuration to your webpack encore configuration

```yaml
webpack_encore:
    builds:
        fcm_admin: '%kernel.project_dir%/public/build/codebuds/fcm/admin'
```

#### Twig

Add the entry to your admin template

```html
{{ encore_entry_link_tags('codebuds-fcm-admin', null, 'admin') }}
```

### Routes

Add the plugin routes

```yaml
# config/routes.yaml
codebuds_fcm_plugin:
    resource: '@CodeBudsSyliusFCMPlugin/Resources/config/routing.yml'
```

### Sylius config

Add the plugin configuration for sylius

```yaml
# config/_sylius.yaml
imports:
    - { resource: "@CodeBudsSyliusFCMPlugin/Resources/config/config.yml" }
```

### Bundle

If you are not using Symfony Flex you need to import the bundle.

```php
// config/bundles.php
<?php

return [
    CodeBuds\SyliusFCMPlugin\CodeBudsSyliusFCMPlugin::class => ['all' => true],
]
```

## Run stuff

## Add tokens

In the `App\Entity\Customer\Customer` implement the `CodeBuds\SyliusFCMPlugin\Entity\FCMTokenOwnerInterface` and use
the `CodeBuds\SyliusFCMPlugin\Entity\FCMTokenOwnerTrait`. Add the `$fcmTokens` field.

```php
<?php

declare(strict_types=1);

namespace App\Entity\Customer;

use CodeBuds\SyliusFCMPlugin\Entity\CustomerFCMToken;
use CodeBuds\SyliusFCMPlugin\Entity\FCMRecipientInterface;
use CodeBuds\SyliusFCMPlugin\Entity\FCMRecipientTrait;
use CodeBuds\SyliusFCMPlugin\Entity\FCMTokenOwnerInterface;
use CodeBuds\SyliusFCMPlugin\Entity\FCMTokenOwnerTrait;
use Doctrine\ORM\Mapping as ORM;
use Sylius\Component\Core\Model\Customer as BaseCustomer;
use Sylius\Component\Customer\Model\CustomerInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="sylius_customer")
 */
class Customer extends BaseCustomer implements FCMTokenOwnerInterface, FCMRecipientInterface
{
    use SoftDeleteableEntity;
    use FCMTokenOwnerTrait;
    use FCMRecipientTrait;
}
```

#### ShopUser tokens
```php
class ShopUser extends BaseShopUser implements FCMTokenOwnerInterface
{
use FCMTokenOwnerTrait;

    /**
     * @ORM\OneToMany(targetEntity="CodeBuds\SyliusFCMPlugin\Entity\ShopUserFCMToken", mappedBy="owner")
     */
    private $fcmTokens;
}
```
