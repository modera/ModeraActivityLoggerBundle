<?php

namespace Modera\FoundationBundle\Twig;

/**
 * Base twig extensions used throughout the foundation.
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2013 Modera Foundation
 */
final class Extension extends \Twig_Extension
{
    const NAME = 'modera-foundation-extension';

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return self::NAME;
    }

    /**
     * {@inheritDoc}
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('mf_prepend_every_line', array($this, 'filter_prepend_every_line'))
        );
    }

    /**
     * Prepends every line of given $input with $prefix $multiplier-times.
     *
     * @param string $input
     * @param string $multiplier
     * @param string $prefix
     * @param bool   $skipFirstLine
     *
     * @return string
     */
    public function filter_prepend_every_line($input, $multiplier, $prefix = ' ', $skipFirstLine = false)
    {
        $output = explode("\n", $input);

        foreach ($output as $i=>&$line) {
            if ($skipFirstLine && 0 === $i) {
                continue;
            }

            $line = str_repeat($prefix, $multiplier) . $line;
        }

        return implode("\n", $output);
    }
}