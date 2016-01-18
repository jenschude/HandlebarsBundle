<?php
/**
 * @author @jayS-de <jens.schulze@commercetools.de>
 */


namespace JaySDe\HandlebarsBundle;


use Symfony\Component\Translation\TranslatorInterface;

class HandlebarsHelper
{
    /**
     * @var TranslatorInterface
     */
    public static $translator;
    /**
     * @var string
     */
    public static $defaultNamespace = 'translations';

    /**
     * @var string
     */
    public static $interpolationPrefix = '__';

    /**
     * @var string
     */
    public static $interpolationSuffix = '__';

    public function __construct(
        TranslatorInterface $translator = null,
        $defaultNamespace = null,
        $interpolationPrefix = '__',
        $interpolationSuffix = '__'
    ) {
        static::$translator = $translator;
        if (!is_null($defaultNamespace)) {
            static::$defaultNamespace = $defaultNamespace;
        }
        static::$interpolationPrefix = $interpolationPrefix;
        static::$interpolationSuffix = $interpolationSuffix;
    }

    public static function json($context)
    {
        return json_encode($context);
    }

    public static function trans($context, $options)
    {
        $options = isset($options['hash']) ? $options['hash'] : [];
        if (strstr($context, ':')) {
            list($bundle, $context) = explode(':', $context, 2);
            $options['bundle'] = $bundle;
        }
        $bundle = isset($options['bundle']) ? $options['bundle'] : \JaySDe\HandlebarsBundle\HandlebarsHelper::$defaultNamespace;
        $locale = isset($options['locale']) ? $options['locale'] : null;
        $count = isset($options['count']) ? $options['count'] : null;
        $args = [];
        foreach ($options as $key => $value) {
            $key = \JaySDe\HandlebarsBundle\HandlebarsHelper::$interpolationPrefix . $key . \JaySDe\HandlebarsBundle\HandlebarsHelper::$interpolationSuffix;
            $args[$key] = $value;
        }

        if (is_null($count)) {
            return \JaySDe\HandlebarsBundle\HandlebarsHelper::$translator->trans($context, $args, $bundle, $locale);
        } else {
            return \JaySDe\HandlebarsBundle\HandlebarsHelper::$translator->transChoice($context, $count, $args, $bundle, $locale);
        }
    }
}
