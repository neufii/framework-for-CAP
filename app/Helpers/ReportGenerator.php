<?php

namespace App\Helpers;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

use App\Modules\ModuleManager;

use App\Models\QuestionInstance;
use App\Models\QuestionStat;
use App\Models\Indicator;
use App\Models\Learner;
use App\Models\Module;

use Spatie\TemporaryDirectory\TemporaryDirectory;

class ReportGenerator{
    public static function evaluateGenerator($generatorId, $numberOfQuestions = 2000, $threshold = 0.5, $preferredLevel = null, $indicatorId = null){
        $generator = Module::findOrFail($generatorId);
        if($generator->name != 'generator'){
            return "a selected module is not a generator";
        }

        if(!isset($indicatorId)){
            $indicator = $generator->compatibleIndicators->first();
        }
        else{
            $indicator = Indicator::findOrFail($indicatorId);
        }

        echo("Generator Evaluation:\n Generator ID: ".$generator->id."\n");
        echo("Start:\t".date("Y-m-d H:i:s")."\n");

        $firstGeneratedQuestion = 0;
        $questions = [];

        for($i=0;$i<$numberOfQuestions;$i++){
            //prepare question
            $questionI = QuestionInstance::generate($indicator,$preferredLevel,$generator);
            if($i == 0) $firstGeneratedQuestion = $questionI->id;
            array_push($questions,$questionI->question);

            // for($j=0;$j<$numberOfQuestions;$j++){
            //     //create distance matrix
            //     if($j == $numberOfQuestions-1){
            //         fwrite($fp, '0');
            //     }
            //     else if($i == $j || $i < $j){
            //         fwrite($fp, '0,');
            //     }
            //     else{
            //         fwrite($fp, $questionI->getDistance(QuestionInstance::findOrFail($testQuestionIds[$j])));  
            //         fwrite($fp, ',');
            //     }
            // }
            // fwrite($fp, "\n");
            if($i%4 == 0) echo("question / \t".$i."\r");
            if($i%4 == 1) echo("question - \t".$i."\r");
            if($i%4 == 2) echo("question \\ \t".$i."\r");
            if($i%4 == 3) echo("question - \t".$i."\r");
        }

        //calculate distance
        $distanceDirectory = (new TemporaryDirectory())->create();
        $distanceFile = $distanceDirectory->path('distance.dat');
        $dfp = fopen($distanceFile, 'a');
        $distanceMatrix = ModuleManager::runProcess($indicator->compatibleModules()->distanceCalculator()->active()->latest()->first(),$questions);
        fwrite($dfp, $distanceMatrix);

        //clustering
        $processArray = ['python3', __DIR__."/Scripts/evaluator.py", $distanceFile, $threshold];

        $process = new Process($processArray);
        $process->run();
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $output = json_decode($process->getOutput());
        $distanceDirectory->delete();

        $ids = [];
        foreach($output->sample_ids_in_largest_cluster as $id){
            $ids[] = $id+$firstGeneratedQuestion;
        }

        echo("Finish:\t".date("Y-m-d H:i:s")."\n");
        echo("==== Result ====\n");

        $reportName = "generatorReport".time().".txt";
        $report = fopen(__DIR__."/Reports/GeneratorReport/".$reportName, 'a');
        fwrite($report, "Report Date:\t".date("Y-m-d H:i:s")."\n");
        fwrite($report, "Generator ID:\t".$generator->id."\n");  
        fwrite($report, "Indicator ID:\t".$indicator->id."\n");  
        if($preferredLevel) fwrite($report, "Question Level:\t".$preferredLevel."\n");  
        fwrite($report, "Threshold:\t".$threshold."\n");  
        fwrite($report, "Total Generated Questions:\t".$numberOfQuestions."\n");
        fwrite($report, "\n==== Result ====\n");
        fwrite($report, "Total Clusters:\t".$output->total_clusters."\n");
        fwrite($report, "Average Questions Per Cluster:\t".$output->average_question_per_clusters."\tStandard Deviation:\t".$output->std."\n");
        fwrite($report, "Total Questions in Largest Cluster:\t".$output->questions_in_largest_cluster."\n");
        fwrite($report, "Sample Question Instances' ID in Largest Cluster:\t".implode(", ",$ids)."\n");
        fclose($report);

        return "Generator Report Generated";
    }

    public static function evaluateSystem(){
        $reportName = "systemReport".time().".txt";
        $report = fopen(__DIR__."/Reports/SystemReport/".$reportName, 'a');

        $indicators = Indicator::all();
        $totalIndicators = $indicators->count();
        $totalLearners = Learner::all()->count();
        $totalQuestions = QuestionInstance::all()->count();

        fwrite($report, "Report Date:\t".date("Y-m-d H:i:s")."\n");
        fwrite($report, "==== Overall ====\n");  

        fwrite($report, "Total Indicators:\t".$totalIndicators."\n");  
        fwrite($report, "Total Learners:\t".$totalLearners."\n");  
        fwrite($report, "Total Questions:\t".$totalQuestions."\n");

        fwrite($report, "\n==== Indicators ====\n");  
        foreach($indicators as $indicator){
            fwrite($report, "Indicator ID:\t".$indicator->id."\n");  
            fwrite($report, $indicator->name."\n");  
            fwrite($report, "".$indicator->description."\n");  

            $learners = $indicator->learners()->get();
            $totalLearners = $learners->count();
            $ratings = $learners->pluck('pivot.rating');
            $avgRating = $learners->avg('pivot.rating');
            $stdDev = 0;
            foreach($ratings as $rating){
                $stdDev += pow(($rating - $avgRating), 2);
            }
            $stdDev = (float)sqrt($stdDev/$totalLearners);
            fwrite($report, "Total Learners:\t".$totalLearners."\n");
            fwrite($report, "Max Learners' Rating:\t".$learners->max('pivot.rating')."\n");
            fwrite($report, "Min Learners' Rating:\t".$learners->min('pivot.rating')."\n");
            fwrite($report, "Average Learners' Rating:\t".$avgRating."\t Standard Deviation:\t".$stdDev."\n");  
            fwrite($report, "Median Learners' Rating:\t".$learners->median('pivot.rating')."\n");

            $activeGenerators = $indicator->compatibleModules()->generator()->active()->get();
            $totalQuestions = $indicator->questions()->whereIn('generator_id',$activeGenerators->pluck('id'))->count();
            $ratings = $indicator->questions()->whereIn('generator_id',$activeGenerators->pluck('id'))->with('statistic')->get()->pluck('statistic.rating');
            $avgRating = $ratings->avg();
            $totalRating = $ratings->count();
            $stdDev = 0;
            foreach($ratings as $rating){
                $stdDev += pow(($rating - $avgRating), 2);
            }
            $stdDev = (float)sqrt($stdDev/$totalRating);

            fwrite($report, "\nTotal Active Generators ID:\t".$activeGenerators->count()."\n");
            fwrite($report, "Total Questions:\t".$totalQuestions."\n");
            fwrite($report, "Max Questions' Rating:\t".$ratings->max()."\n");
            fwrite($report, "Min Questions' Rating:\t".$ratings->min()."\n");
            fwrite($report, "Average Questions' Rating:\t".$avgRating."\t Standard Deviation:\t".$stdDev."\n");  
            fwrite($report, "Median Questions' Rating:\t".$ratings->median()."\n");

            unset($learners,$totalLearners,$totalQuestions,$ratings,$avgRating,$stdDev);

            fwrite($report, "\n\t==== Active Generator ====\n");
            foreach($activeGenerators as $generator){
                fwrite($report, "\tGenerator Id:\t".$generator->id."\n");
                if($generator->isLatest) fwrite($report, "\t**new question instances will be generated from this generator**\n");

                $questions = $indicator->questions()->where('generator_id',$generator->id)->with('statistic')->get();
                $avgUpvotes = $questions->pluck('statistic.upvotes')->avg();
                $avgDownvotes = $questions->pluck('statistic.downvotes')->avg();
                fwrite($report, "\tAverage Upvotes:\t".$avgUpvotes."\n");
                fwrite($report, "\tAverage Downvotes:\t".$avgDownvotes."\n");
                
                $questionIds = $indicator->questions()->pluck('id');
                fwrite($report, "\tlevel\t\t\ttotal questions\t\texpected rating\t\tmin\t\t\tmax\t\t\taverage\t\tSD\n");
                for($i=0;$i<4;$i++){
                    $levelQuestions = $indicator->questions()->where('generator_id',$generator->id)->whereHas('statistic',function($query) use($i){
                        return $query->where('initial_level',$i+1);
                    })->with('statistic')->get();
                    $avgRating = $levelQuestions->average('rating');
                    $stdDev = 0;
                    foreach($levelQuestions->pluck('rating') as $rating){
                        $stdDev += pow(($rating - $avgRating), 2);
                    }
                    $stdDev = (float)sqrt($stdDev/$totalRating);

                    $median = QuestionStat::whereIn('question_id',$questionIds)->get()->median('rating');
                    if($i==0){
                        $min = QuestionStat::whereIn('question_id',$questionIds)->where('rating','<=',$median)->get()->min('rating');
                        if(!isset($min)) $min = 0.0;   
                        
                        $max = QuestionStat::whereIn('question_id',$questionIds)->where('rating','<=',$median)->get()->median('rating');
                        if(!isset($max)) $max = 0.0;
                    }
                    else if($i == 1){
                        $min = QuestionStat::whereIn('question_id',$questionIds)->where('rating','<=',$median)->get()->median('rating');
                        if(!isset($min)) $min = 0.0;
                        $max = QuestionStat::whereIn('question_id',$questionIds)->get()->median('rating');
                        if(!isset($max)) $max = 0.0;
                    }
                    else if($i == 2){
                        $min = QuestionStat::whereIn('question_id',$questionIds)->get()->median('rating');
                        if(!isset($min)) $min = 0.0;
                        $max = QuestionStat::whereIn('question_id',$questionIds)->where('rating','>=',$median)->get()->median('rating');
                        if(!isset($max)) $max = 0.0;
                    }
                    else{
                        $min = QuestionStat::whereIn('question_id',$questionIds)->where('rating','>=',$median)->get()->median('rating');
                        if(!isset($min)) $min = 0.0;
                        $max = QuestionStat::whereIn('question_id',$questionIds)->where('rating','<=',$median)->get()->max('rating');
                        if(!isset($max)) $max = 0.0;
                    }
                    fwrite($report, "\t".($i+1)."\t\t\t\t".$levelQuestions->count()."\t\t\t\t\t".str_pad($min."-".$max,9)."\t\t\t".
                        str_pad($levelQuestions->min('rating'),5)."\t\t".str_pad($levelQuestions->max('rating'),5)."\t\t".
                        str_pad($avgRating,5)."\t\t".$stdDev."\n");
                }
                $noLvQuestions = $indicator->questions()->where('generator_id',$generator->id)->whereHas('statistic',function($query){
                    return $query->whereNull('initial_level');
                })->get();
                fwrite($report, "\tno init lv\t\t".$noLvQuestions->count()."\n");
                fwrite($report, "\ttotal\t\t\t".$questions->count()."\n");

                unset($questions,$avgUpvotes,$avgDownvotes,$generator,$questionIds,$levelQuestions,$median,$min,$max,$avgRating,$stdDev,$noLvQuestions);
                fwrite($report, "\t========\n\n");  
            }
            fwrite($report, "\n================\n\n"); 
        }
        return("System Report Generated"); 
    }
}