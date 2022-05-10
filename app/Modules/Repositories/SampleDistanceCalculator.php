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

        $distanceMatrix = array();
        for($i = 0; $i < count($questions); $i++){
            for($j = 0; $j < count($questions); $j++){
                $distanceMatrix[$i][$j] = $this->calculateDistance($questions[$i], $questions[$j]);
            }
        }

        return $distanceMatrix;
    }

    private function calculateDistance(String $question1, String $question2){
        $arrQ1 = json_decode($question1, true);
        $arrQ2 = json_decode($question2, true);

        $filteredQ1 = array();

        foreach($arrQ1 as $key => $value){
            if($value['type'] == 'number'){
                array_push($filteredQ1, $value['content']);
            }
            else if(
                $value['type'] == 'plus_key' || 
                $value['type'] == 'min_key' || 
                $value['type'] == 'mul_key' || 
                $value['type'] == 'div_key'
            ){
                array_push($filteredQ1, $value['type']);
            }
        }

        $filteredQ2 = array();

        foreach($arrQ2 as $key => $value){
            if($value['type'] == 'number'){
                array_push($filteredQ2, $value['content']);
            }
            else if(
                $value['type'] == 'plus_key' || 
                $value['type'] == 'min_key' || 
                $value['type'] == 'mul_key' || 
                $value['type'] == 'div_key'
            ){
                array_push($filteredQ2, $value['type']);
            }
        }

        $distance = 1 - count(array_intersect($filteredQ1, $filteredQ2)) / count(array_unique(array_merge($filteredQ1, $filteredQ2)));

        // var_dump($distance);
        return $distance;
    }
}