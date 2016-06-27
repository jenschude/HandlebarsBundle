HandlebarsBundle
============

This [Symfony](http://symfony.com/) bundle provides integration for the [Handlebars](http://handlebarsjs.com/) template engine using [LightnCandy](https://packagist.org/packages/zordius/lightncandy) as renderer.

It is a backport of jayS-de/HandlebarsBundle to Symfony 2.8.

Installation
------------

### Prerequisites

 * Symfony 2.8+
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

Authors
-------

This backport - [Steve Jordan](https://github.com/stevejordan)

The upstream bundle - [Jens Schulze](https://github.com/jayS-de)
