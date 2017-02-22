# [`zicht/cms`](https://github.com/zicht/cms) tutorials

This repository serves as a documentation and example repository for the
`zicht/cms` bundles.

## Quickstart
* Fork or clone this repository
* `composer install`
* Start hacking away

## Tutorial
The [tutorial](doc/tutorial.md) is a step-by-step document on how to set up the
CMS from scratch. It includes setting up symfony as a base installation. If you
have the time, I'd advise you to follow the tutorial, because it gives you a 
clear overview of the different bundles, how they relate to each other and how
they can operate independently of each other.

## Bundle interoperation 
The following bundles are part of the `zicht/cms`. All of these bundles are
designed to operate *independently* of the others. This means that if you do
not wish to use Sonata, for example, you are still able to use the PageBundle,
even though you will not use Sonata as a CMS.

This also means that you can use the `zicht/url-bundle` without any of the
other bundles, say for instance if you only want to use the "aliasing" feature.

In other words: it's worth getting to know the bundles without tying yourself
to how we organized them.

The `zicht/cms` metapackage, however, provides a working set of versions for
all bundles that work well together and is therefore a sensible place to look
for information on how the bundles interoperate.

### The suite
The following bundles are considered "part of the suite". This identifies the
main functionality that the zicht/cms provides. There are more libraries that
can be hooked up to the CMS (such as user management and content versioning)
but they are not considered part of the zicht/cms essentials, and therefore not
part of the suite. They are listed on further down on this page 

All of the libraries and bundles follow the
[`zicht/decorum`](https://github.com/zicht/decorum). 

#### [`zicht/framework-extra-bundle`](https://github.com/zicht/framework-extra-bundle)
This library contains a few commonly used utilities. 

#### [`zicht/page-bundle`](https://github.com/zicht/page-bundle)
Pages are considered objects that can be composed of different elements and can
appear in different forms. This bundle utilizes doctrine's "Inheritance Mapping" to
implement this. Template differentation is done based on either a mapped field
in the page, or it is part of the class hierarchy of the page.

Pages can identify their own controllers, templates, etc simply by overriding
methods of the base model. 

Integrates tightly with:
* zicht/url-bundle for aliasing pages
* zicht/menu-bundle for putting pages in a menu
* zicht/admin-bundle for management of the pages using sonata as a backend

#### [`zicht/menu-bundle`](https://github.com/zicht/menu-bundle)
Store menus of your site in the database using a nested set, render the menu
with Knp (or render it yourself).

Integrates tightly with:
* zicht/admin-bundle for management of the pages using sonata as a backend

#### [`zicht/url-bundle`](https://github.com/zicht/url-bundle)
The URL bundle has two major functions:

* Being able to link to objects in stead of routes (called "url providers")
* Aliasing (which decouples routing from having SEO-friendly urls)

Integrates tightly with:
* zicht/admin-bundle for management of the aliases and redirects using sonata
  as a backend. 

#### [`zicht/messages-bundle`](https://github.com/zicht/messags-bundle)
The messages bundle provides a means of managing messages (translatable
strings) in the database rather than in files.

This library is totally independent of any of the other bundles.

#### [`zicht/filemanager-bundle`](https://github.com/zicht/filemanager-bundle)
Annotate properties in your Doctrine entity to reflect locally stored files.
Easily manage these files through symfony forms. Easily retrieve url's to files
in your templates using twig extensions.

This library is totally independent of any of the other bundles.

#### [`zicht/admin-bundle`](https://github.com/zicht/admin-bundle)
Provides integration with the [Sonata Project](https://sonata-project.org/)
admin bundle.

Integrates tightly with:
* `zicht/page-bundle` for managing pages
* `zicht/messages-bundle` for managing the messages
* `zicht/url-bundle` for managing the urls

#### [`zicht/tinymce-bundle`](https://github.com/zicht/tinymce-bundle)
Integrate with [TinyMCE](https://www.tinymce.com/) for wysiwyg content editing.

### Libraries that are implicitly required
The following zicht libraries are implicitly required by one or some of the
zicht bundles.

#### [`zicht/util`](https://github.com/zicht/util)
Some useful low-level utilities, such as for string/url manipulation, html/xml
introspection, a Mutex. This is considered a useful "root" dependency for any
type of web project.

#### [`zicht/itertools`](https://github.com/zicht/itertools)
A set of useful tools and utilities to work with collections.

#### [`zicht/symfony-util`](https://github.com/zicht/symfony-util)
Provides a base kernel with some added features on top of symfony's HttpKernel. 

Features include:
* Split up bundle configuration files
* Local override for configuration
* Split kernels for different parts of your application
* Request handling for service-less requests (not needing to boot the
  container)

### Suggested bundles
#### [`zicht/solr-bundle`](https://github.com/zicht/solr-bundle)
Provides integration with Apache SOLR

#### [`zicht/user-bundle`](https://github.com/zicht/user-bundle)
Manage your users in the database.

#### [`zicht/versioning-bundle`](https://github.com/zicht/versioning-bundle)
Add content versioning to your CMS

#### [`zicht/moxiemanager-bundle`](https://github.com/zicht/moxiemanager-bundle)
Integrate with [MoxieManager](http://www.moxiemanager.com/)

### `zicht
* If you have a license for MoxieManager, you can install zicht/moxie

## Hard external dependencies 
* [`symfony/symfony`](https://github.com/symfony/symfony), currently supported
  LTS version: *2.7*. We intend to support 2.8 as a transitional phase, but
  will make the move to the next LTS 3.4 as soon as that reached RC. 

## Issues and troubleshooting
Please report issues for documentation in `zicht/cms-tutorial` and report
issues regarding the dependencies in `zicht/cms`. Any other issue can be
reported in the related library or bundle repository.
