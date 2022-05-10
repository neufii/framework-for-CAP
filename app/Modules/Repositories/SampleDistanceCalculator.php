<?php

namespace App\Modules\Repositories;

use App\Modules\Repositories\Interfaces\DistanceCalculator as DistanceCalculatorInterface;

use App\Models\QuestionInstance;
use App\Models\Indicator;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Spatie\TemporaryDirectory\TemporaryDirectory;

class SampleDistanceCalculator implements DistanceCalculatorInterface {
 
    public function execute(array $questions, float $threshold, Indicator $indicator){ //receive only question part of questionInstance
        //not using script
        // $distanceCalculatorScript = $indicator->first()->compatibleScripts()->distanceCalculator()->active()->latest()->first();

        // if(!isset($distanceCalculatorScript)){
        //     return null;
        // }

        //add questions to file
        // $temporaryDirectory = (new TemporaryDirectory())->create();
        // $filename = $temporaryDirectory->path('questions.dat');
        // $jsonData = json_encode($questions,JSON_FORCE_OBJECT);
        // file_put_contents($filename,$jsonData);

        // $processArray = [$distanceCalculatorScript->type, $distanceCalculatorScript->path, $filename];

        // $process = new Process($processArray);
        // $process->run();
        // if (!$process->isSuccessful()) {
        //     throw new ProcessFailedException($process);
        //     $temporaryDirectory->delete();
        // }

        // $distanceMatrix = $process->getOutput();
        // $temporaryDirectory->delete();

        $distanceMatrix = array();
        for($i = 0; $i < count($questions); $i++){
            for($j = 0; $j < count($questions); $j++){
                $distanceMatrix[$i][$j] = $this->calculateDistance($questions[$i], $questions[$j]);
            }
        }

        return $distanceMatrix;
    }

    private function calculateDistance(String $question1, String $question2){
        return 0;
    }
}