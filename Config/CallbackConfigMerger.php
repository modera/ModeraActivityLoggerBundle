<?php

namespace Modera\MjrIntegrationBundle\Config;

/**
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2014 Modera Foundation
 */
class CallbackConfigMerger implements ConfigMergerInterface
{
    private $callback;

    /**
     * @param callable $callback
     */
    public function __construct($callback)
    {
        if (!is_callable($callback)) {
            throw new \InvalidArgumentException(
                'Given $callback is not callable.'
            );
        }

        $this->callback = $callback;
    }

    /**
     * {@inheritdoc}
     */
    public function merge(array $existingConfig)
    {
        return call_user_func($this->callback, $existingConfig);
    }
}
