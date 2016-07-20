HandlebarsBundle
============

This [Symfony3](http://symfony.com/) bundle provides integration for the [Handlebars](http://handlebarsjs.com/) template engine using [LightnCandy](https://packagist.org/packages/zordius/lightncandy) as renderer.

Installation
------------

### Prerequisites

 * Symfony3
 * composer


### Installation

```bash
composer require jays-de/handlebars-bundle dev-master

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

``` yml
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

### Helper functions

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
<service id="handlebar.helper_factory" class="HelperFactory" />
<service id="handlebars.helper.trans" class="callable">
	<factory service="handlebar.helper_factory" method="getMyHelper">
	<tag name="handlebars.helper.my" id="my" />
</service>
```

Authors
-------

Jens Schulze - <jens.schulze@commercetools.de>

See also the list of [contributors](https://github.com/jayS-de/HandlebarsBundle/contributors) who participated in this project.

Submitting bugs and feature requests
------------------------------------

Bugs and feature requests are tracked on [GitHub](https://github.com/jayS-de/HandlebarsBundle/issues).
