<?php

namespace App\Modules;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Spatie\TemporaryDirectory\TemporaryDirectory;

class ModuleManager
{
    public static function runProcess($module, ...$parameters){
        $output = null;

        $moduleName = $module->name;

        $temporaryDirectory = (new TemporaryDirectory())->create();
        $filename = $temporaryDirectory->path('input.dat');

        $script_type = $module->run_command;

        $processArray = [$script_type, $module->path, $filename];
        $jsonData = json_encode($parameters,JSON_FORCE_OBJECT);
        file_put_contents($filename,$jsonData);
        
        $process = new Process($processArray);
        $process->run();
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $output = $process->getOutput();
        $processedOutput = json_decode($output,true) ? json_decode($output,true) : $output;
        $temporaryDirectory->delete();

        return $processedOutput;
    }
}