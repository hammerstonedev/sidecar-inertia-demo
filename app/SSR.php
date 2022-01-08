<?php
/**
 * @author Aaron Francis <aarondfrancis@gmail.com|https://twitter.com/aarondfrancis>
 */

namespace App;

use Hammerstone\Sidecar\LambdaFunction;
use Hammerstone\Sidecar\Sidecar;
use Symfony\Component\Process\Process;

class SSR extends LambdaFunction
{
    public function memory()
    {
        return 2048;
    }

    public function handler()
    {
        return 'public/js/ssr.handler';
    }

    public function package()
    {
        return [
            'public/js/ssr.js',
        ];
    }

    public function beforeDeployment()
    {
        Sidecar::log('Executing beforeDeployment hooks');

        // Compile the SSR bundle before deploying.
        $this->compileJavascript();
    }

    protected function compileJavascript()
    {
        Sidecar::log('Compiling Inertia SSR JavaScript bundle.');
        Sidecar::log('Running npx mix --mix-config=webpack.ssr.mix.js');

        $command = ['npx', 'mix', '--mix-config=webpack.ssr.mix.js'];

        if (Sidecar::getEnvironment() === 'production') {
            $command[] = '--production';
        }

        $process = new Process($command, $cwd = base_path(), $env = []);

        // mustRun will throw an exception if it fails, which is what we want.
        $process->setTimeout(60)->disableOutput()->mustRun();

        Sidecar::log('JavaScript SSR bundle compiled!');
    }
}
