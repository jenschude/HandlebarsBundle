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
    private $translator;
    /**
     * @var string
     */
    private $defaultNamespace = 'translations';

    /**
     * @var string
     */
    private $interpolationPrefix = '__';

    /**
     * @var string
     */
    private $interpolationSuffix = '__';

    public function __construct(
        TranslatorInterface $translator = null,
        $defaultNamespace = null,
        $interpolationPrefix = '__',
        $interpolationSuffix = '__'
    ) {
        $this->translator = $translator;
        if (!is_null($defaultNamespace)) {
            $this->defaultNamespace = $defaultNamespace;
        }
        $this->interpolationPrefix = $interpolationPrefix;
        $this->interpolationSuffix = $interpolationSuffix;
    }

    public function handle($context, $options)
    {
        $options = isset($options['hash']) ? $options['hash'] : [];
        if (strstr($context, ':')) {
            list($bundle, $context) = explode(':', $context, 2);
            $options['bundle'] = $bundle;
        }
        $bundle = isset($options['bundle']) ? $options['bundle'] : $this->defaultNamespace;
        $locale = isset($options['locale']) ? $options['locale'] : null;
        $count = isset($options['count']) ? $options['count'] : null;
        $args = [];
        foreach ($options as $key => $value) {
            $key = $this->interpolationPrefix . $key . $this->interpolationSuffix;
            $args[$key] = $value;
        }

        if (is_null($count)) {
            $trans = $this->translator->trans($context, $args, $bundle, $locale);
        } else {
            $trans = $this->translator->transChoice($context, $count, $args, $bundle, $locale);
        }

        return new \LightnCandy\SafeString($trans);
    }
}
