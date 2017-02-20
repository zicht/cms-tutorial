The most effective way of getting to know Zicht CMS is to follow the tutorial
for installation.

# Step 1: setting up a symfony sandbox

Setting up Symfony sandbox is easy. You can start with the Symfony standard
edition, but that would leave you with removing and replacing a lot of stuff
you don't really need. So my advice would be to follow these simple steps in
stead:

## 1.1 Initialize your repo:

Assuming you have `composer` available (https://getcomposer.org), you can run
the following:

```
mkdir ~/my-cms && cd $_
composer init
composer require "zicht/cms"
```

All dependencies will get installed now. 

## 1.2 Configure autoloading
The best practice for autoloading is to have one autoload.php in your `app/`
folder, which wraps the composer autoloader inside the Doctrine annotation
registry's loader:

### `app/autoload.php`
```
<?php

use Doctrine\Common\Annotations\AnnotationRegistry;

$loader = require __DIR__.'/../vendor/autoload.php';
AnnotationRegistry::registerLoader([$loader, 'loadClass']);
```

## 1.3 Setup the kernel

Create a kernel which extends the Zicht base kernel (see
https://github.com/zicht/symfony-util for more information). You will gain some
benefits that will come up later in this tutorial. You can implement the
`registerBundles` with the bare essentials, which in practice come down to the
following:

### `app/AppKernel.php`
```
<?php

use Zicht\SymfonyUtil\HttpKernel\Kernel;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        return [
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
        ];
    }
}
```

### 1.4 Create the bootstrap file
Run `php vendor/sensio/distribution-bundle/Resources/bin/build_bootstrap.php`
to generate the bootstrap cachefile. Then add a file to `app/bootstrap.php`
which includes the three essential files for running the app:

#### `app/bootstrap.php`
```
<?php

require_once __DIR__ . '/autoload.php';
require_once __DIR__ . '/bootstrap.php.cache';
require_once __DIR__ . '/AppKernel.php';
```

### 1.5 Create the console front controller

#### `app/console`
```
#!/usr/bin/env php
<?php

require_once __DIR__ . '/bootstrap.php';

(new AppKernel())->console();
```
The `console` function is provided by Zicht's base kernel


### 1.6 Create the web front controller
```
<?php

require_once __DIR__ . '/../app/bootstrap.php';

(new AppKernel())->web();
```

### 1.7 Setup basic configuration

Because you don't want your application to become vitally dependent on the
configuration of a bundle, Zicht's symfony base kernel provides a means of
configuring each bundle in it's own file. It followings the
`app/config/bundles/underscore_name_of_bundle.yml` naming convention. This
means that you can disable a bundle by simply commenting it's instantiation
line in the AppKernel. Of course if the bundle has dependees, it could trigger
errors anyway, but at least your app won't break because it loads config of a
bundle that is not enabled.

Additionally, the kernel detects the `APPLICATION_ENV` environment variable to
see which environment your app is running on. It assumes `production`, but it
is a good idea to utilize `development`, `testing`, `staging` and `production
for this. You can, of course, use your own preference for this.

Finally, there is an option to identify an application name, which can be
configured as an environment variable or passed at construction time. See the
documentation of https://github.com/zicht/symfony-util for more information on
how to utilize this.

By default, the zicht kernel also checks for a file called `config_local.yml`
and prefers that over any of the application env settings. This is useful if
your deployment strategy fits that setup, and/or if you want to do local
overrides of particular settings, without bothering your colleagues with
changes in the shared configuration. It is a good idea to ignore
`app/config/config_local.yml` from your VCS.

Add the following files:

#### `app/config/config_development.yml`
```
imports:
    - resource: parameters_development.yml
```

#### `app/config/parameters_development.yml`
```
imports:
    - resource: parameters/common.yml

parameters:
    database_host:  # your mysql host
    database_user:      # your mysql user
    database_password:  # your mysql password
    database_name:      # your mysql database name
```

#### `app/config/parameters/common.yml`
```
parameters:
    database_driver: pdo_mysql
    database_port: 3306

    locale: en
    secret: # a secret you should generate yourself
    assets_version: development
```


#### `app/config/bundles/doctrine.yml`

```
services:
    doctrine.platform.mysql:
        class: Doctrine\DBAL\Platforms\MySqlPlatform

doctrine:
    dbal:
        default_connection: default
        connections:
            default:
                driver:   %database_driver%
                host:     %database_host%
                port:     %database_port%
                dbname:   %database_name%
                user:     %database_user%
                password: %database_password%
                charset:  UTF8
                mapping_types:
                    enum: string
                platform_service: doctrine.platform.mysql

    orm:
        auto_generate_proxy_classes: %kernel.debug%
        auto_mapping: true
        dql:
            numeric_functions:
                RAND: 'Zicht\Bundle\FrameworkExtraBundle\Doctrine\FunctionNode\Rand'
        naming_strategy: doctrine.orm.naming_strategy.underscore
```

```
framework:
    secret:          %secret%
    router:
        resource: "%kernel.root_dir%/config/routing.yml"
        strict_requirements: %kernel.debug%
```

You are done setting up your base symfony application.


### 2 Configure an application bundle
At this point, we are also going to add an application-specific bundle. We tend
to follow the convention that each project has their own bundle, containing
typically anything that was built specifically for that project. Sometimes one
project has even more bundles, to have a logical distinction between different
types of functionality (e.g., CMS/content-related code in bundle A, third party
integrations in bundle B).

In the rest of this tutorial, we will use Acme as the vendor name and
SiteBundle as the bundle name:

#### 2.1 Configure autoloading

In `composer.json` add the following:

```json
{
    /* ... */
    "autoload": {
        "psr-4": {
            "Acme\\": "src/"
        }
    }
    /* ... */
}
```

Then, add the files as they appear in `zicht/cms-tutorial/src`.


## 2. Configuring the Page bundle
The page bundle provides a simple means of storing pages of any type in the
database. It makes extensive use of doctrine's feature which is called
"inheritance mapping". Different page types are reflected by creating new
classes that extend a base page. Page composition (how different elements are
displayed on a page) is configured by adding what's called "Content items" to
pages. Contentitems also follow inheritance mapping, so they can be of
different types, and can appear on different locations on the page. You are not
required to use content items, but we've found it a very powerful way of
customizing CMS'es without having to introduce new database models. However, in
some cases introducing your own data model is much more efficient than
utilizing the pages model. You are not at all required to use the PageBundle to
make use of any of the other features of the Zicht CMS.

### 2.1 Add the page bundle to your AppKernel

Of course, you need to register this bundle in the AppKernel:

```
    public function registerBundles()
    {
        return [
            /* ... */

            new Zicht\Bundle\PageBundle\ZichtPageBundle()
        ];
    }
```


### Configure bundle routing
Since this is the first bundle you will have the routing for, you can add the
routing config to `app/config/routing.yml`

```
# If you intend to be multilingual, you will want the _locale parameter in your route:
zicht_page:     { resource: "@ZichtPageBundle/Controller", prefix: "/{_locale}" }

# If not:
zicht_page:     { resource: "@ZichtPageBundle/Controller", prefix: "/" }
```

For the rest of the tutorial I will assume that you will want to have a
multi-lingual site. This will cause pages to be rendered when you follow the
route `/en/page/{id}`. So for the first page we built, we can see it at
`/en/page/1`.

Of course you are not obliged to follow this routing, you can configure your
own following the Symfony routing configuration, however you wish. 

### Add a base configuration for the bundle

#### `app/config/bundles/zicht_page.yml`

This configuration is needed to tell the page bundle which you consider your
"base" page, i.e. the page that is the class that all other pages will extend.

```
zicht_page:
    pageClass: 'Acme\SiteBundle\Entity\Page'
    contentItemClass: ''
    types:
        page:
            article: Acme\SiteBundle\Entity\ArticlePage


zicht_page:
    pageClass: 'Acme\SiteBundle\Entity\Page'
    contentItemClass: ''
    types:
        page:
            article: Acme\SiteBundle\Entity\ArticlePage



