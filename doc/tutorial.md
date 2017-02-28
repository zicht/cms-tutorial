# Tutorial

## When to use this tutorial
If you want to get to know the bundles and how to work together.

## What this tutorial is not
This is not an end user tutorial of how to use the CMS when it is running. The
Zicht CMS is highly customizable, so there is not much common to tell about any
of the CMS'es.

## How to use this tutorial
The most effective way of getting to know Zicht CMS is to follow the tutorial
for installation and try to build it up yourself. If you want to take that
approach, You should reserve at least a few hours of time to do the entire
tutorial, depending on your experience with Symfony.

However, it is probably better to clone this repository or simply compare all
of the tags for each of the steps.

For example, if you want to see what changes were made between 1.7 and 3.2, you
can inspect the changes on github like this:

https://github.com/zicht/cms-tutorial/compare/1.7...3.2

You can also refer to all of the releases in
https://github.com/zicht/cms-tutorial/releases and compare them to see what was
needed to get from one point to another. The numbers in the tags refer to the
sections of the tutorial, namely what the result should be at the end of that
section.

However, if you are looking for a quickstart, you can best clone this repo at
any tag that you want to use, and start hacking away, or simply add `zicht/cms` as
a requirement to your project and copy/paste the configurations from any tag
you wish to use.

Well then, if you're still reading, you're probably interesting in starting
this.  Let's start!

# Step 1: setting up a symfony sandbox
Setting up Symfony sandbox is easier than you may think, especially if you know
exactly what you do and don't need. 

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

The `console` function is provided by [Zicht's base
kernel](https://github.com/zicht/symfony-util).


### 1.6 Create the web front controller
```
<?php

require_once __DIR__ . '/../app/bootstrap.php';

(new AppKernel())->web();
```

The `web` function is provided by [Zicht's base
kernel](https://github.com/zicht/symfony-util).

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


# 2 Configure an application bundle
At this point, we are also going to add an application-specific bundle. We tend
to follow the convention that each project has their own bundle, containing
typically anything that was built specifically for that project. Sometimes one
project has even more bundles, to have a logical distinction between different
types of functionality (e.g., CMS/content-related code in bundle A, third party
integrations in bundle B).

In the rest of this tutorial, we will use Acme as the vendor name and
SiteBundle as the bundle name:

## 2.1 Configure autoloading

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

and add the bundle file:

#### `src/Acme/SiteBundle.php`

```
namespace Acme\SiteBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class AcmeSiteBundle extends Bundle
{
}
```

And register the bundle in your AppKernel.

# 3. Configuring the Page bundle
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

## 3.1 Add the page bundle to your AppKernel

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


## 3.2 Configure bundle routing
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

## 3.3 Add a base configuration for the bundle

#### `app/config/bundles/zicht_page.yml`

```
zicht_page:
    pageClass: 'Acme\SiteBundle\Entity\Page'
    contentItemClass: ''
    types:
        page:
            article: Acme\SiteBundle\Entity\ArticlePage
```

This configuration is needed to tell the page bundle which you consider your
"base" page, i.e. the page that is the class that all other pages will extend.
This more or less reflects the inheritance mapping you will use in your
doctrine config. 

## 3.4 Configuring the entities

See the source code for this tutorial how these entities are configured:

* https://github.com/zicht/cms-tutorial/tree/3.4/src/SiteBundle/Entity

## 3.5 Add the template
To get twig to work, we need to tweak the framework configuration a bit.

See
https://github.com/zicht/cms-tutorial/blob/3.5/src/SiteBundle/Resources/views/Page/article.html.twig
for an example of the template. The name of the template is derived from the
`PageManager::getTemplate()` method, which defaults to a strategy where the
bundle name of the entity is inferred, and `:Page:` is added to the, and finally the
Page's getTemplateName() is called. You can easily override this by either extending the page manager,
or overloading the `getTemplateName` method in your page instance. It might be helpful, for example to have different templates for the same type of page, based on a mapped field.

## 3.6 Add a page
In MySQL, execute the following queries:

```sql
INSERT INTO page(id, title, type) VALUES(1, 'Home', 'article');
INSERT INTO article_page(id, content) VALUES(1, '<p>Welcome!</p>');
```

This creates a "JOINED" model, with id 1. Read more about how inheritance
mapping works in the the [doctrine manual about inheritance
mapping](http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/inheritance-mapping.html)

As soon as these steps are done, point your browser to the running application
and open the url `/en/page/1`. This will show you the page with title 'Home'
and content "Welcome :)". Yay!

#### Exercise
Try adding an extra type of page. Here are the steps:

1. Create an extra entity class
2. Add it to the `zicht_page.yml` configuration
3. Create a migration for the changes in the database
   (doctrine:migrations:diff) and apply the migration
   
# 3.7 Add a language property
The keen reader probably noticed the query above missing a language property. 
This is a good time to explain to you that the language property is only important for one thing: routing a page. You can check that opening the page on the url `/fr/page/1` will also work. This is because the `fr` or `en` part of the route matches the default symfony `_locale` parameter which has no "real" relation to the page's language property. Since all pages get a new id, the language is not important to identify the page. Considering that if you always route to pages including the correct locale, it is irrelevant for the controller to know which language the page has. This simplifies things greatly. This also means that you *must* include the locale parameter in the route for the symfony locale to be set. 

This has two consequences:
* When you want to route to a page, you must always include it's locale. 
  This is where the **URL providers** kick in, and this is something we will get 
  to later in this tutorial (configuring the url bundle)
* The locale is always included in the url for a page. But you don't always want to show 
  it as such. This is where **aliasing** kicks in; also more on that later.

# 4. Using fixtures 
Since it is really tedious to write fixture data in SQL files (though it is
great training for your mad skills), you probably want data fixtures in place.
So add the `DoctrineFixturesBundle` to your development kernel:

```
        $ret = [ /* ... */ ];

        if ($this->getEnvironment() === 'development') {
            $ret[] = new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle();
        }

        return $ret;
```

# 4.1 Create fixtures with the Fixture builder
and add a fixture class to the correct namespace. The
`ZichtFrameworkExtraBundle` contains a tool that can be useful if you want to
create fixtures without writing too much code for it. This is called the
fixture builder and works on the basis of a principle that when you are
building an entity. Read the
[documentation](https://github.com/zicht/framework-extra-bundle) for more info.
In the example the builder is used, because it is particularly useful for
tree-like structures (such as a menu).

https://github.com/zicht/cms-tutorial/blob/4.1/src/SiteBundle/DataFixtures/ORM/SiteFixtures.php#L32

Note that for building fixtures it is extra useful to have constructor defaults in the entity:

https://github.com/zicht/cms-tutorial/blob/4.1/src/SiteBundle/Entity/Page.php#L37

# 5. Configuring the `zicht/url-bundle`

## 5.1 enable the url bundle

Enable the url bundle in your AppKernel, by adding it to the registerBundles() call. 

Then, add the following config:

#### `app/config/bundles/zicht_url.yml`

```
zicht_page:
    aliasing: true
```

This enables one of the key features of the url bundle. As discussed before in 3.7, 
aliasing is the feature that allows you to configure your own way of showing URL's
to the client for any type of object.

## 5.2 enable aliasing for pages

Now, the Page bundle implements aliasing for pages. So, let's enable aliasing in 
the page bundle:

#### `app/config/bundles/zicht_page.yml`
```
zicht_page:
    aliasing: true
```

The default aliasing is based on the title of the page and simply prefixes it with 
a slash. You can generate aliases for all pages using the `zicht:page:alias` command.

Assuming you have the fixtures loaded, you will now have a few aliased pages. Every
time you reload the fixtures, the aliases will be regenerated, because the url bundle
hooks into the Doctrine events to create aliases and remove them when the objects
they point to are removed or created. 

There are different options for the behavior when updating an object. You can also
have multiple aliases per object. For more info on the details of generating aliases
and tweaking this to your needs, see the 
[documentation of zicht/url-bundle](https://github.com/zicht/url-bundle)

*Note* aliasing is only done for public pages. Since we do not have security 
configured yet, all pages are considered public.

## 5.3 Configure a home page

By far the easiest approach to make this work is simply updating the url_alias to
point to '/' in stead of '/home':

```sql
UPDATE url_alias SET public_url='/' WHERE public_url='/home'
```

There are several other approaches which you can use, but they all involve 
configuration.

# 6. Configuring the `zicht/menu-bundle`
Let's add the ZichtMenuBundle to the app kernel, and add it's config. By
default the menu builder simply queries the root items from the menuitem table
and select them using the Gedmo "nested set" implementation. This is a very
efficient way of querying tree-like data from a relational database storage.
The result is [Knp menu](https://github.com/KnpLabs/KnpMenuBundle), which you
might already know. It is a fairly feature-complete abstraction layer for
representing menus and being able to render (parts of) menus and breadcrumb
list. The ZichtMenuBundle simply provides a means of storing such menus in the
MySQL backend in an efficient manner.

So, to get the ZichtMenuBundle working, you will need two other bundles
configured as well:

## 6.1 StofDoctrineExtensionsBundle

Add the StofDoctrineExtensionsBundle() to `AppKernel::registerBundles()` and
add the following configuration.

### `app/config/bundles/stof_doctrine_extensions.yml`

```
stof_doctrine_extensions:
    orm:
        default:
            tree: true
```
This will make the "Tree" annotations work, which are used in the MenuBundle to
store NestedSet data about all nodes. Read more about what a [nested
set](https://en.wikipedia.org/wiki/Nested_set_model) is on Wikipedia

## 6.2 KnpMenuBundle

You only need to register this bundle, nothing more. Read more about the [Knp
menu features here](https://github.com/KnpLabs/KnpMenuBundle).

## 6.3 `zicht_menu` config
Finally, you will need to add a config identifying which menus you have
prepared.

### `app/config/bundles/zicht_menu.yml`

```
zicht_menu:
    menus: []
```

## 6.4 Create the menu
Assuming you have set up the fixtures, you can configure a menu fairly easily
by creating `MenuItem` entities and persisting them. 

```
class MenuFixtures implements FixtureInterface, ContainerAwareInterface
{
    public function load()
    {
        $em = $this->container->get('doctrine')->getManager();
        
        $rootItem = new MenuItem('main', '', 'main');
        
        // root items need a language, ascendants inherit the language from the root.
        $rootItem->setLanguage('en'); 

        $child1 = new MenuItem('Home', '/');
        $rootItem->addChild($child1);

        $child2 = new MenuItem('Products', '/');
        $rootItem->addChild($child2);

        // etc.

        $em->persist($rootItem);
        $em->persist($child1);
        $em->persist($child2);
        $em->flush();
    }
}
```

The example from the source repository utilizes the fixture builder, which is
documented more thoroughly in
[zicht/framework-extra-bundle](https://github.com/zicht/framework-extra-bundle). 
See also chapter 4 on configuring fixtures.

See https://github.com/zicht/cms-tutorial/blob/6.4/src/SiteBundle/DataFixtures/ORM/SiteFixtures.php#L57

## 6.5 add the menu to the bundle config
To identify that you want to use the `main` root item as a menu, you need to
configure this:

```
zicht_menu:
    menus: ['main']
```

## 6.6 render the menu
If you have load the fixtures you can now render the menu in your template
using the knp menu function

```
<menu>
    {{ knp_menu_render('main') }}
</menu>
```

Read the documentation on the [knp
menu](http://symfony.com/doc/master/bundles/KnpMenuBundle/index.html) library
for how to customize your rendering.

Note that the rendered HTML source code shows **aliased** urls. Even though 
your database still contains unaliased urls. There is no magic, it is 
implemented in a Kernel listener which simply rewrites all the HTML right 
before it is sent to the client. It doesn't matter if the url's come from the
menu, from your hardcoded urls in your template, or from user-managed content,
they will be aliased with the correct current url.

## 6.7 Make the fixtures more robust
Since every time you reload the fixtures, your pages might get new ID's (and
hard coding ID's is nearly always a bad idea, you can utilize the url provider
to generate the correct urls in the Menu for each of the referred pages.

A simple trick for this is to introduce a pages array which you fill in the
one builder, and read out in the next one. Check out:

https://github.com/zicht/cms-tutorial/compare/6.6...6.7

# 7. "Well, how about those admin screens?"
These few steps show you how to very quickly set up a database-backed website
which you can manage in any way you seem fit. You can hook up pretty much any
admin that can work with Doctrine models, write your own forms, etc. But if you
planned on building something yourself, you would probably not end up here ;)

The `zicht/cms` bundles are integrated with the [sonata
admin](https://sonata-project.org/bundles/admin/2-3/doc/index.html) libraries,
currently version 2.3. Newer versions of the zicht/cms will likely support
higher versions of sonata.

## 7.1 Enable the admin bundles
You can enable the admin bundles by configuring the following:

### Register the admin bundles in the kernel:

```php
    $ret = [
        /* ... */
        new Sonata\AdminBundle\SonataAdminBundle(),
        new Sonata\BlockBundle\SonataBlockBundle(),
        new Sonata\DoctrineORMAdminBundle\SonataDoctrineORMAdminBundle(),
        new Sonata\CoreBundle\SonataCoreBundle(),
        new Zicht\Bundle\AdminBundle\ZichtAdminBundle(),
        /* ... */
    ];
```

You will need to add the configuration for forms and in the `framework`
configuration:

#### `app/config/bundles/framework.yml`

```
framework:
    # ...
    forms: ~
```

Add a base config for the `sonata_block` bundle for it to run without
complaining:

#### `app/config/bundles/sonata_block.yml`
```
sonata_block:
    default_contexts: [cms]
    blocks:
        sonata.admin.block.admin_list: ~
```

And since you will need authorization for sonata to work, we add the
security.yml as follows:

#### `app/config/bundles/security.yml`

```
security:
    access_decision_manager:
        strategy:             unanimous

    providers:
        in_memory:
            memory: ~

    firewalls:
        admin:
            pattern: ^/
            switch_user: true
            context: user
            anonymous: true
            http_basic:
                realm: 'Secured Area'
```

This is the bare minimum for sonata to work. Obviously, in most cases you will
want to configure some users and roles that you will want to grant or deny
access. It is beyond the scope of this tutorial to configure this, as it is
part of Symfony's core. We will configure that in 7.4.

You will also need to add the sonata routing to the already existing
`zicht_page` routing:

#### `app/config/routing.yml`

```
# ....

admin:
    resource: '@SonataAdminBundle/Resources/config/routing/sonata_admin.xml'
    prefix: /admin

_sonata_admin:
    resource: .
    type: sonata_admin
    prefix: /admin

# ....
```

Now you should be able to open the /admin/dashboard route and you should see
the MenuItem entity available for you to edit (because the ZichtMenuBundle does
not require further configuration to work within Sonata).


### 7.2 move the Page classes to their own namespace
Since we will be introducing Admin classes for all types of pages later, it is 
good practice to move the classes to their own namespace.

A good approach for this is to have you base class available at:
`[Bundle]\Entity\Page` and all of it's derived classes at
`[Bundle]\Entity\Page\DerivedPage`. So, for the above example, the class should
be moved from `Acme\SiteBundle\Entity\ArticlePage` to
`Acme\SiteBundle\Entity\Page\ArticlePage`. If you have created another page
type, move that to that namespace as well. 


### 7.3 set page "public"
By default, the PageInterface has an `isPublic` method that is used to
distinguish between pages that are shown to the public, and pages that aren't.
You can implement your own logic in it, but as a general approach, it usually
is better to create separate voters for whatever rules you impose. The default
voter that is registered, denies access if a Page is not public, and you are
not logged in.

So if you now visit `/home`, you will get an HTTP basic authentication popup,
because you are not allowed to see the page. The base implementation for
pages returns `false`.

The obvious approach for this is to introduce a boolean field on the base page
class and have it managed by doctrine. For the time being though, we will
simply consider all pages public.

### 7.4 add an `/admin` restriction

#### `app/config/bundles/security.yml`

All you really need is an access restriction on the `/admin` path:

```
security:
    # ...
    access_control:
        - { path: ^/admin, roles: ROLE_ADMIN }
```

Depending on how you plan to integrate your user management you need to
configure your `providers` section. The easiest solution is to add a plain-text
password to your security configuration. This is obviously not the most secure
way, but for the purposes of this tutorial, it will suffice.

*Tip*: if you use plain text passwords, always use a password that is very
easy. This will remind you of the fact that it is still insecure and needs
proper configuration.

```
security:
    # ...
    providers:
        in_memory:
            memory:
                users:
                    admin: { password: "1", roles: ["ROLE_ADMIN"] }
    # ...
```

#### Integrate user and role management
You can use [zicht/user-bundle](https://github.com/zicht/user-bundle) for a
pragmatic approach to user management in the CMS. Of course, if you prefer to use
another bundle, such as FOSUserBundle or the KunstmaanUserManagementBundle that
is totally up to you.

It's configuration is beyond the scope of this tutorial. Just follow the
instructions in the README if you wish to use the `zicht/user-bundle`.

### 7.5 enable the page admins
You will need to configure admin services for your pages. The approach of the 
page bundle is that all page types have their own admin, but they all extend
the same 'base page admin'. This goes for content items as well. 

Upon initialization of the bundle's configuration, when the `admin` flag is 
enabled, the configuration for each of the configured classes is generated.
Again, you are of course not obliged to use this, it is a convenience.

Each of the class names is mapped to an Admin class. At this point, you
probably want to move your Page and ContentItem classes to their own namespace,
because the list of classes will probably grow.

#### 7.5.1 Add base admin implementation

It will prove useful to have a base page admin you will use as a basis for all
your page admins:

```
namespace Acme\SiteBundle\Admin;

use Zicht\Bundle\PageBundle\Admin\PageAdmin as BasePageAdmin;

class PageAdmin extends BasePageAdmin
{
}
```

You can use this to add application-wide customizations to the admin. For
example, you could use this to add a custom field that you have for all pages,
or you could use this to configure the page overviews.

#### 7.5.2 Configure a DI extension
At this point, we're going to use XML for service definitions. You can,
however, map this to your own preferred configuration.

We'll need a DI extension:

```
namespace Acme\SiteBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection as DI;

class AcmeSiteExtension extends DI\Extension\Extension
{
    public function load(array $configs, DI\ContainerBuilder $container)
    {
        $loader = new DI\Loader\XmlFileLoader(
            $container, 
            new FileLocator(__DIR__.'/../Resources/config')
        );

        $loader->load('admin.xml');
    }
}
```

#### 7.5.3 configure an admin base service

We usually split up admin configuration for the sake of being able to use split
configurations for separate parts of your application. In other words, not in all
incarnations of your bundle, you will want the admin part to be loaded. You
could, for example, only load the admin classes if the SonataAdminBundle is enabled.

See [zicht/symfony-util](https://github.com/zicht/symfony-util)'s documentation
on how to use split configs. 

##### `src/SiteBundle/Resources/config/admin.xml`

```
<?xml version="1.0" ?>

<container xmlns="http://symfony.com/schema/dic/services"
           xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
           xsi:schemaLocation="http://symfony.com/schema/dic/services http://symfony.com/schema/dic/services/services-1.0.xsd">

    <services>
        <!-- admin services go here -->
    </services>
</container>
```

Now we will need the admin base service. Though this is not strictly all
required, this is the most common configuration you will use. The configuration
options for the `label_translator_strategy` and `setTranslationDomain` are best
practices we use for translations across all of our projects. Of course, you
can configure this to your own needs (that is why you need to configure it in
the first place: to have as much freedom as possible).

```xml
<service id="acme.admin.page" class="Acme\SiteBundle\Admin\PageAdmin">
    <tag name="sonata.admin" manager_type="orm" group="Structure" 
         label="Pages" label_translator_strategy="sonata.admin.label.strategy.underscore"/>
    <argument/>
    <argument>Acme\SiteBundle\Entity\Page</argument>
    <argument>ZichtAdminBundle:CRUD</argument>
    <argument>acme.admin.content_item</argument>

    <call method="setPageManager">
        <argument type="service" id="zicht_page.page_manager"/>
    </call>
    <call method="setUrlProvider">
        <argument type="service" id="zicht_url.provider"/>
    </call>
    <call method="setMenuManager">
        <argument type="service" id="zicht_menu.menu_manager"/>
    </call>
    <call method="setTranslationDomain">
        <argument>admin</argument>
    </call>
</service>
```

#### Configure the page admin base

In the `zicht_page` bundle configuration you need to refer to `acme.admin.page`
service code to inform the page bundle you want to use that as a basis for your
other definitions:

```
zicht_page:
    admin:
        base:
            page: acme.admin.page
```    

#### 7.5.4 Add concrete implementations
When running `app/console`, you will see a message informing you that the
`ArticlePageAdmin` is not found:

> The PageBundle was unable to create a service definition for
> Acme\SiteBundle\Entity\Page\ArticlePage because the associated class
> Acme\SiteBundle\Admin\Page\ArticlePageAdmin was not found  

The `GenerateAdminServicesCompilerPass` generated page admins for each of the
classes that are available. The name mapping simply replaces the 'Entity'
subnamespace with 'Admin' and adds the 'Admin' suffix. So, in our example, the
admin class name for the ArticlePage would become: 

```
Acme\SiteBundle\Admin\Page\ArticlePageAdmin
```

Implement this by extending your base admin:

```
namespace Acme\SiteBundle\Admin\Page;

use Acme\SiteBundle\Admin\PageAdmin;
use Sonata\AdminBundle\Form\FormMapper;

class ArticlePageAdmin extends PageAdmin
{
}
```

### 7.6 Content items: rinse/repeat
More or less the same steps are needed for content items:

#### `src/SiteBundle/Resources/config/admin.xml`

We will add two admins, in stead of one. The first one is needed at the
collection level, which is used to display a list of content items.

The default implementation will be sortable, have an editable title, and
will have a link to a detail admin for detailed configuration of the
content items.

The best approach at this point is simply inspect the diff on github:
https://github.com/zicht/cms-tutorial/compare/7.5.4..7.6

The interesting parts are:
* [`app/config/bundles/zicht_page.yml`](https://github.com/zicht/cms-tutorial/tree/7.6/app/config/bundles/zicht_page.yml)
* [`src/SiteBundle/Resources/config/admin.xml`](https://github.com/zicht/cms-tutorial/tree/7.6/src/SiteBundle/Resources/config/admin.xml)



