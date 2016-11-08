HandlebarsBundle
============

This [Symfony](http://symfony.com/) bundle provides integration for the [Handlebars](http://handlebarsjs.com/) template engine using [LightnCandy](https://packagist.org/packages/zordius/lightncandy) as renderer.

[![Build Status](https://img.shields.io/travis/jayS-de/HandlebarsBundle/master.svg?style=flat-square)](https://travis-ci.org/jayS-de/HandlebarsBundle) [![Scrutinizer](https://img.shields.io/scrutinizer/g/jayS-de/HandlebarsBundle.svg?style=flat-square)](https://scrutinizer-ci.com/g/jayS-de/HandlebarsBundle/) [![Scrutinizer](https://img.shields.io/scrutinizer/coverage/g/jayS-de/HandlebarsBundle.svg?style=flat-square)](https://scrutinizer-ci.com/g/jayS-de/HandlebarsBundle/) [![Packagist](https://img.shields.io/packagist/v/jays-de/handlebars-bundle.svg?style=flat-square)](https://packagist.org/packages/jays-de/handlebars-bundle) [![SensioLabsInsight](https://insight.sensiolabs.com/projects/e0c910db-d32d-4136-9a55-fbe6153c576f/mini.png)](https://insight.sensiolabs.com/projects/e0c910db-d32d-4136-9a55-fbe6153c576f)

Installation
------------

### Prerequisites

 * Symfony 2.8+
 * composer


### Installation

```bash
composer require jays-de/handlebars-bundle

Composer will install the bundle to your project's `vendor/` directory.

### 2. Enable the bundle

Enable the bundle in the kernel:

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new \JaySDe\HandlebarsBundle\HandlebarsBundle();
    );
}
```

### 3. Enable the Handlebars template engine in the config

``` yaml
    # app/config/config.yml
    framework:
        templating:      { engines: ['twig', 'handlebars'] }
```

Documentation
-------------

### Usage

Files in your Resources/view with a .hbs or .handlebars ending are supported.

```
public function indexAction(Request $request)
    {
        ...
        return $this->render('index.hbs', [...]);
    }
```

This will render the file index.hbs in your `Resources/views` folder.

### Configuration flags

It's possible to set or unset the different flags provided by LightnCandy. Therefore set in your config.yml the fields flags and excludedFlags. The bundle will ensure that default flags are set, to prevent a non working template engine. The complete list of flags can be found at the [LnC documentation](https://github.com/zordius/lightncandy#compile-options)

```yaml
 # app/config/config.yml
handlebars:
  flags:
    - FLAG_BESTPERFORMANCE
  excludedFlags:
    - FLAG_STANDALONE
```

### Helper functions

#### Built-In helpers

The Bundle comes with some built-in helpers.

##### Linking to pages

Use the path helper and refer to the route

```
<a href="{{route_path '_welcome' }}">Home</a>
```

Its also possible to add parameters to the referenced route

```
<a href="{{route_path 'article_show' slug=article.slug}}">{{ article.title }}</a>
```

You can also generate an absolute URL using the url helper

```
<a href="{{route_url '_welcome' }}">Home</a>
```

##### Linking to assets

To avoid hardcoding pathes to assets like images, javascript or stylesheets use the asset helper.

```
<img src="{{asset 'images/logo.png' }}" />

<link href="{{asset 'css/blog.css' }}" rel="stylesheet" />
```

#### Adding new Helpers
To add new helper functions to the handlebars engine, you just have to create a class implementing ```JaySDe\HandlebarsBundle\Helper\HelperInterface``` and create a service definition with the tag ```handlebars.helper```. The ID of the tag is the helpers block name inside handlebars templates.

Example:

```xml
<service id="handlebars.helper.trans" class="JaySDe\HandlebarsBundle\Helper\TranslationHelper">
	<tag name="handlebars.helper" id="i18n" />
	<argument type="service" id="translator" />
</service>
```

The helper registry also supports to register any callable. So it's possible to create a class with the magic __invoke() method and define a service for it

```php
class MyHelper{
    public function __invoke($context, $options) {}
}
```

```xml
<service id="handlebars.helper.my" class="MyHelper">
	<tag name="handlebars.helper" id="my" />
</service>
```

or using a factory method returning an anonymous function for example

```php
class HelperFactory{
    public function getMyHelper() {
        return function($context, $options) {}
    }
}
```

```xml
<services>
    <service id="handlebar.helper_factory" class="HelperFactory" />
    <service id="handlebars.helper.my" class="callable">
        <factory service="handlebar.helper_factory" method="getMyHelper" />
        <tag name="handlebars.helper" id="my" />
    </service>
</services>
```

Authors
-------

Jens Schulze - <jens.schulze@commercetools.de>

See also the list of [contributors](https://github.com/jayS-de/HandlebarsBundle/contributors) who participated in this project.

Submitting bugs and feature requests
------------------------------------

Bugs and feature requests are tracked on [GitHub](https://github.com/jayS-de/HandlebarsBundle/issues).
