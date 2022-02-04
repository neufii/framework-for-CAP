<?php

namespace App\Modules\Repositories;

use App\Modules\Repositories\Interfaces\DistanceCalculator as DistanceCalculatorInterface;

use App\Models\QuestionInstance;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class SampleDistanceCalculator implements DistanceCalculatorInterface {
 
    public function execute(array $questions, float $threshold){ //receive only question part of questionInstance
        $distanceCalculatorScript = $questionInstance->indicator()->first()->compatibleScripts()->distanceCalculator()->active()->latest()->first();

        if(!isset($distanceCalculatorScript)){
            return null;
        }

        //add questions to file
        $temporaryDirectory = (new TemporaryDirectory())->create();
        $filename = $temporaryDirectory->path('questions.dat');
        $jsonData = json_encode($questions,JSON_FORCE_OBJECT);
        file_put_contents($filename,$jsonData);

        $processArray = [$distanceCalculatorScript->type, $distanceCalculatorScript->path, $filename];

        $process = new Process($processArray);
        $process->run();
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $distanceMatrix = $process->getOutput();
        $temporaryDirectory->delete();

        return $distanceMatrix;
    }
}