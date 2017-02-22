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

## Bundle and dependency overview
The following bundles are part of the `zicht/cms`. All of these bundles are
designed to operate *independent* of the others. This means that if you do not
wish to use Sonata, for example, you are still able to use the PageBundle, even
though you will not use Sonata as a CMS. 

This also means that you can use the `zicht/url-bundle` without any of the
other bundles, say for instance if you only want to use the "aliasing" feature.

The `zicht/cms` metapackage, however, provides a working set of versions for
all bundles that work well together.  

### List of bundles
The following bundles are considered "part of the suite". This identifies the
main functionality that the zicht/cms provides. There are more libraries that
can be hooked up to the CMS (such as user management and content versioning)
but they are not considered part of the zicht/cms essentials, and therefore not
part of the suite.

All of the libraries and bundles follow the
[`zicht/decorum`](https://github.com/zicht/decorum). 

#### [`zicht/framework-extra-bundle`](https://github.com/zicht/framework-extra-bundle)
This library contains a few commonly used utilities. 

#### [`zicht/page-bundle`](https://github.com/zicht/page-bundle)
Pages are considered objects that can be composed of different elements and can
appear in different forms. This bundle utilizes doctrine's "Inheritance Mapping" to
implement this. Template differentation is done based on either a mapped field
in the page, or it is part of the class hierarchy of the page.

Pages can identify their own controllers, templates, etc simply by implementing.

Integrates tightly with:
* zicht/url-bundle for aliasing pages
* zicht/menu-bundle for putting pages in a menu

#### [`zicht/menu-bundle`](https://github.com/zicht/menu-bundle)
Store menus of your site in the database using a nested set, render the menu
with Knp (or render it yourself).

#### [`zicht/url-bundle`](https://github.com/zicht/url-bundle)
The URL bundle has two major functions:

* Being able to "link to objects" in stead of routes.
* Aliasing (which decouples routing from having SEO-friendly urls)

### Libraries that are implicitly required
The following zicht libraries are implicitly required by one or some of the
zicht bundles.

#### [`zicht/util`](https://github.com/zicht/util)
This library contains some useful utilities, such as string manipulation,
html/xml introspection, a Mutex. This is considered a useful "root" dependency
for any type of web project.

#### [`zicht/itertools`](https://github.com/zicht/itertools)
This library contains a set of useful tools and utilities to work with
collections.


Hard dependencies 
* [`symfony/symfony`](https://github.com/symfony/symfony), currently supported
  LTS version: *2.7*.

Soft dependencies


* [`


The documentation for each bundle i

## Issues and troubleshooting
Please report issues in pull requests and the github issue reporter.



