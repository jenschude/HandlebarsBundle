<?php
/**
 * @author @jayS-de <jens.schulze@commercetools.de>
 */


namespace JaySDe\HandlebarsBundle;


use Symfony\Component\Stopwatch\Stopwatch;

class HandlebarsProfileExtension
{
    private $actives = array();
    private $stopwatch;
    private $events;

    public function __construct(\Twig_Profiler_Profile $profile, Stopwatch $stopwatch = null)
    {
        $this->actives[] = $profile;

        $this->stopwatch = $stopwatch;
        $this->events = new \SplObjectStorage();
    }

    public function enter(\Twig_Profiler_Profile $profile)
    {
        if ($this->stopwatch && $profile->isTemplate()) {
            $this->events[$profile] = $this->stopwatch->start($profile->getName(), 'template');
        }

        $this->actives[0]->addProfile($profile);
        array_unshift($this->actives, $profile);
    }

    public function leave(\Twig_Profiler_Profile $profile)
    {
        $profile->leave();
        array_shift($this->actives);

        if (1 === count($this->actives)) {
            $this->actives[0]->leave();
        }
        if ($this->stopwatch && $profile->isTemplate()) {
            $this->events[$profile]->stop();
            unset($this->events[$profile]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'profiler';
    }
}
