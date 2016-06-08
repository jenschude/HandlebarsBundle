<?php
/**
 * @author @jayS-de <jens.schulze@commercetools.de>
 */

namespace JaySDe\HandlebarsBundle\Helper;

class CmsHelper implements HelperInterface
{
    /**
     * @var TranslationHelper
     */
    private static $translationHelper;

    public function __construct(TranslationHelper $translationHelper)
    {
        self::$translationHelper = $translationHelper;
    }

    public static function handle($context, $options)
    {
        $options = isset($options['hash']) ? $options['hash'] : [];
        $bundle = isset($options['bundle']) ? $options['bundle'] . ':' : '';

        $cmsKey = $bundle . $context;

        $result = self::$translationHelper->handle($cmsKey, $options);

        return $result;
    }
}
