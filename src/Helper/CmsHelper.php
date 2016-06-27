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
    private $translationHelper;

    public function __construct(TranslationHelper $translationHelper)
    {
        $this->translationHelper = $translationHelper;
    }

    public function handle($context, $options)
    {
        $options = isset($options['hash']) ? $options['hash'] : [];
        $bundle = isset($options['bundle']) ? $options['bundle'] . ':' : '';

        $cmsKey = $bundle . $context;

        $result = $this->translationHelper->handle($cmsKey, $options);

        return $result;
    }
}
