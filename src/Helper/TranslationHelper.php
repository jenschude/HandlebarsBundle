<?php
/**
 * @author @jayS-de <jens.schulze@commercetools.de>
 */

namespace JaySDe\HandlebarsBundle\Helper;

use Symfony\Component\Translation\TranslatorInterface;

class TranslationHelper implements HelperInterface
{
    /**
     * @var TranslatorInterface
     */
    private static $translator;
    /**
     * @var string
     */
    private static $defaultNamespace = 'translations';

    /**
     * @var string
     */
    private static $interpolationPrefix = '__';

    /**
     * @var string
     */
    private static $interpolationSuffix = '__';

    public function __construct(
        TranslatorInterface $translator = null,
        $defaultNamespace = null,
        $interpolationPrefix = '__',
        $interpolationSuffix = '__'
    ) {
        self::$translator = $translator;
        if (!is_null($defaultNamespace)) {
            self::$defaultNamespace = $defaultNamespace;
        }
        self::$interpolationPrefix = $interpolationPrefix;
        self::$interpolationSuffix = $interpolationSuffix;
    }

    public static function handle($context, $options)
    {
        $options = isset($options['hash']) ? $options['hash'] : [];
        if (strstr($context, ':')) {
            list($bundle, $context) = explode(':', $context, 2);
            $options['bundle'] = $bundle;
        }
        $bundle = isset($options['bundle']) ? $options['bundle'] : self::$defaultNamespace;
        $locale = isset($options['locale']) ? $options['locale'] : null;
        $count = isset($options['count']) ? $options['count'] : null;
        $args = [];
        foreach ($options as $key => $value) {
            $key = self::$interpolationPrefix . $key . self::$interpolationSuffix;
            $args[$key] = $value;
        }

        if (is_null($count)) {
            $trans = self::$translator->trans($context, $args, $bundle, $locale);
        } else {
            $trans = self::$translator->transChoice($context, $count, $args, $bundle, $locale);
        }

        return new \LightnCandy\SafeString($trans);
    }
}
