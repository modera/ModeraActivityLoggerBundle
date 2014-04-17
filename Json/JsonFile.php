<?php

namespace Modera\UpgradeBundle\Json;

/**
 * @author    Sergei Vizel <sergei.vizel@modera.org>
 * @copyright 2014 Modera Foundation
 */
class JsonFile
{
    /**
     * @var string
     */
    private $path;

    /**
     * @param string $path
     */
    public function __construct($path)
    {
        $this->path = $path;
    }

    /**
     * Reads json file.
     *
     * @throws \RuntimeException
     * @return mixed
     */
    public function read()
    {
        $json = @file_get_contents($this->path);

        if (false === $json) {
            throw new \RuntimeException(
                'Could not read ' . $this->path
            );
        }

        return json_decode($json, true);
    }

    /**
     * Writes json file.
     *
     * @param array $hash writes hash into json file
     * @param int $options json_encode options (defaults to JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
     * @throws \RuntimeException
     */
    public function write(array $hash, $options = 448)
    {
        if (!file_exists(dirname($this->path))) {
            throw new \RuntimeException(
                'Could not write ' . $this->path
            );
        }

        $encode = json_encode($hash, $options);
        file_put_contents($this->path, $encode . ($options & JSON_PRETTY_PRINT ? "\n" : ''));
    }
}
