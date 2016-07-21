<?php
/**
 * @author @jayS-de <jens.schulze@commercetools.de>
 */

namespace JaySDe\HandlebarsBundle\Helper;

use LightnCandy\SafeString;
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
    private $defaultNamespace;

    /**
     * @var string
     */
    private $interpolationPrefix;

    /**
     * @var string
     */
    private $interpolationSuffix;

    public function __construct(
        TranslatorInterface $translator = null,
        $defaultNamespace,
        $interpolationPrefix,
        $interpolationSuffix
    ) {
        $this->translator = $translator;
        $this->defaultNamespace = $defaultNamespace;
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

        $bundle = $this->getOptionValue($options, 'bundle', $this->defaultNamespace);
        $locale = $this->getOptionValue($options, 'locale');
        $count = $this->getOptionValue($options, 'count');

        $args = $this->transformOptions($options);

        return new SafeString($this->trans($context, $args, $count, $bundle, $locale));
    }

    private function trans($context, $args, $count, $bundle, $locale)
    {
        if (is_null($count)) {
            $trans = $this->translator->trans($context, $args, $bundle, $locale);
        } else {
            $trans = $this->translator->transChoice($context, $count, $args, $bundle, $locale);
        }

        return $trans;
    }

    private function transformOptions($options)
    {
        $args = [];
        foreach ($options as $key => $value) {
            $key = $this->interpolationPrefix.$key.$this->interpolationSuffix;
            $args[$key] = $value;
        }
        return $args;
    }

    private function getOptionValue($options, $key, $default = null)
    {
        return isset($options[$key]) ? $options[$key] : $default;
    }
}
