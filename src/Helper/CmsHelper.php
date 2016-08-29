<?php
/**
 * @author @jayS-de <jens.schulze@commercetools.de>
 */

namespace JaySDe\HandlebarsBundle\Helper;

class CmsHelper implements HelperInterface
{
    private $defaultNamespace;

    /**
     * @var TranslationHelper
     */
    private $translationHelper;

    public function __construct(
        TranslationHelper $translationHelper,
        $defaultNamespace = null
    ) {
        $this->translationHelper = $translationHelper;
        $this->defaultNamespace = $defaultNamespace;
    }

    public function handle($context, $options)
    {
        $options = isset($options['hash']) ? $options['hash'] : [];
        $bundle = $this->defaultNamespace;
        if (isset($options['bundle'])) {
            $bundle = $options['bundle'].':';
            unset($options['bundle']);
        }

        $cmsKey = $bundle.$context;

        $result = $this->translationHelper->handle($cmsKey, $options);

        return $result;
    }
}
