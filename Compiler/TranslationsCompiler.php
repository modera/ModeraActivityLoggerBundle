<?php

namespace Modera\TranslationsBundle\Compiler;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;

/**
 * You can use this service to compile translations, under the hood service relies on a console command because
 * some operations cannot be performed in scope of request and a separate process has be to be used instead.
 *
 * @author    Sergei Lissovski <sergei.lissovski@modera.org>
 * @copyright 2016 Modera Foundation
 */
class TranslationsCompiler
{
    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * @param KernelInterface $kernel
     */
    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * Compiles translations and clears cache for a current environment, newly compiled translations
     * will be detected by Symfony on a next request.
     *
     * @throws \Exception
     *
     * @return CompilationResult
     */
    public function compile()
    {
        $app = $this->createApplication();

        $input = new ArrayInput(array(
            'command' => 'modera:translations:compile',
            '--no-ansi' => true,
            '-v' => true,
        ));
        $input->setInteractive(false);

        $compileOutput = new BufferedOutput();

        $compileTranslationsExitCode = $app->run($input, $compileOutput);
        if (0 == $compileTranslationsExitCode) {
            $cacheClearOutput = new BufferedOutput();

            $input = new ArrayInput(array(
                'command' => 'cache:clear',
                '--env' => $this->kernel->getEnvironment(),
                '--no-ansi' => true,
            ));
            $input->setInteractive(false);

            $cacheClearExitCode = $app->run($input, $cacheClearOutput);

            if (0 == $cacheClearExitCode) {
                return new CompilationResult($compileTranslationsExitCode, $compileOutput->fetch());
            } else {
                return new CompilationResult($cacheClearExitCode, $cacheClearOutput->fetch());
            }
        }

        return new CompilationResult($compileTranslationsExitCode, $compileOutput->fetch());
    }

    /**
     * @return Application
     */
    private function createApplication()
    {
        $app = new Application($this->kernel);
        $app->setAutoExit(false);

        return $app;
    }
}
