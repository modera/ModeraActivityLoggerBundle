<?php

namespace Modera\TranslationsBundle\Compiler;

/**
 * You can use this instance of this class to get information regarding translations compilation result. Usually
 * you won't want to create instances of this class manually, but instead use AsyncTranslationsCompiler service.
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2016 Modera Foundation
 */
class CompilationResult
{
    /**
     * @var int
     */
    private $exitCode;

    /**
     * @var string
     */
    private $rawOutput;

    /**
     * @internal
     *
     * @param int    $exitCode
     * @param string $rawOutput
     */
    public function __construct($exitCode, $rawOutput)
    {
        $this->exitCode = $exitCode;
        $this->rawOutput = $rawOutput;
    }

    /**
     * @return bool
     */
    public function isSuccessful()
    {
        return 0 == $this->exitCode;
    }

    /**
     * @return int
     */
    public function getExitCode()
    {
        return $this->exitCode;
    }

    /**
     * Returns console command output as is.
     *
     * @return string
     */
    public function getRawOutput()
    {
        return $this->rawOutput;
    }

    /**
     * Extracts exception error message from command's output.
     *
     * @return string
     */
    public function getErrorMessage()
    {
        // extracting message manually seemed like a faster solution than adding "format" support
        // to console command
        if ($this->isSuccessful()) {
            return '';
        }

        $splitOutput = explode("\n", $this->getRawOutput());
        $startIndex = null;
        $endIndex = null;
        foreach ($splitOutput as $i => $line) {
            $line = trim($line);

            if (null == $startIndex && preg_match('/\.*\[.+\].*/', $line)) {
                $startIndex = $i + 1;
            }

            if (null !== $startIndex && 'Exception trace:' == $line) {
                $endIndex = $i - 1;
            }
        }

        if (null !== $startIndex && null !== $endIndex) {
            $extractedChunk = array_slice($splitOutput, $startIndex, $endIndex - $startIndex);

            foreach ($extractedChunk as $i => $value) {
                $extractedChunk[$i] = trim($value);
            }

            return implode("\n", $extractedChunk);
        }

        return '';
    }
}
