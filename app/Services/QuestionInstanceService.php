<?php

namespace App\Services;

use App\Models\Indicator;
use App\Models\Learner;
use App\Models\Script;
use App\Models\QuestionInstance;

use App\Services\LearnerService;

use App\Modules\Repositories\Interfaces\Generator;
use App\Modules\Repositories\Interfaces\Selector;
use App\Modules\Repositories\Interfaces\QuestionDisplay;
use App\Modules\Repositories\Interfaces\FeedbackDisplay;
use App\Modules\Repositories\Interfaces\AnswerChecker;
use App\Modules\Repositories\Interfaces\Updater;
use App\Modules\Repositories\Interfaces\DistanceCalculator;

use Spatie\TemporaryDirectory\TemporaryDirectory;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Carbon\Carbon;

class QuestionInstanceService
{
    private $questionInstance;
    private $generator;
    private $selector;

    public function __construct(
            Generator $generator, 
            Selector $selector, 
            QuestionDisplay $questionDisplay, 
            FeedbackDisplay $feedbackDisplay, 
            AnswerChecker $answerChecker,
            Updater $updater,
            DistanceCalculator $distanceCalculator,
            QuestionInstance $questionInstance = null
        ){
        $this->questionInstance = $questionInstance;
        $this->generator = $generator;
        $this->selector = $selector;
        $this->questionDisplay = $questionDisplay;
        $this->feedbackDisplay = $feedbackDisplay;
        $this->answerChecker = $answerChecker;
        $this->updater = $updater;
        $this->distanceCalculator = $distanceCalculator;
    }

    public function setQuestionInstance(QuestionInstance $questionInstance){
        $this->questionInstance = $questionInstance;
        return $this;
    }

    public function generate(Indicator $indicator, int $preferredLevel=2, Script $preferredGeneratorScript=null){
        $generated_question = $this->generator->execute($indicator, $preferredLevel, $preferredGeneratorScript);
        if(!isset($generated_question)){
            return null;
        }
        
        //store to database
        $question = new QuestionInstance();
        $question->question = $generated_question['question'];
        $question->answer = $generated_question['answer'];
        $question->solution = $generated_question['solution'];
        $question->initial_level = $generated_question['level'];
        $question->indicator_id = $indicator->id;
        $question->generator_script_id = $generated_question['script_id'];
        $question->rating = 0.0;
        $question->total_attempts = 0;
        $question->save();

        return $question;
    }

    public function select(Learner $learner, Indicator $indicator, bool $includeHistory=false, integer $preferredLevel=null){
        return $this->selector->execute($learner,$indicator,$includeHistory,$preferredLevel);
    }

    public function getDisplayableQuestionScript(){
        return $this->questionDisplay->execute($this->questionInstance);
    }

    public function getDisplayableFeedbackScript(bool $isCorrect, string $learnerAnswer){
        return $this->feedbackDisplay->execute($this->questionInstance, $isCorrect, $learnerAnswer);
    }

    public function check(string $learnerAnswer){
        return $this->answerChecker->execute($this->questionInstance, $learnerAnswer);
    }

    public function updateRating(Learner $learner, bool $isCorrect){
        $ratings = $this->updater->execute($this->questionInstance, $learner, $isCorrect);

        $this->questionInstance->rating = $ratings['questionRating'];
        $this->questionInstance->save();
        
        $learner->learningIndicators()->sync([$this->questionInstance->indicator_id => [ 'rating' => $ratings['learnerRating']] ], false);
    }

    public function addHistory(Learner $learner, string $learnerAnswer, bool $isCorrect, int $timeUsed){
        $learner->history()->attach($this->questionInstance,['answer' => $learnerAnswer, 'is_correct' => $isCorrect ,'time_used' => $timeUsed]);

        $learnerService = new LearnerService($learner);
        $learnerStat = $learnerService->getStatistic($this->questionInstance->indicator);
        $learnerAttempts = $learnerStat["total_attempts"];
        $learnerCorrectAttempts = $learnerStat["correct_attempts"];

        $learner->learningIndicators()->sync([$this->questionInstance->indicator_id => [ 
            'total_attempts' => $learnerAttempts+1,
            // 'correct_attempts' => $learnerCorrectAttempts + ($isCorrect ? 1:0),
        ] ], false);

        $newTotalAttempts = $this->questionInstance->total_attempts+1;
        $this->questionInstance->average_time_used = (($this->questionInstance->average_time_used * $this->questionInstance->total_attempts)+$timeUsed)/$newTotalAttempts;
        $this->questionInstance->total_attempts = $newTotalAttempts;

        if($isCorrect) {
            $this->questionInstance->correct_attempts += 1;
        }

        $this->questionInstance->save();
    }

    public function vote(string $action){
        if($action == 'upvote'){
            $this->questionInstance->upvotes += 1;
            $this->questionInstance->save();
        }
        else if($action == 'downvote'){
            $this->questionInstance->downvotes += 1;
            $this->questionInstance->save();
        }

        return [
            'upvotes' => $this->questionInstance->upvotes,
            'downvotes' => $this->questionInstance->downvotes,
        ];
    }

    public function evaluateUniqueness(array $questions, float $threshold, Indicator $indicator){
        $distanceMatrix = $this->distanceCalculator->execute(array_column($questions, 'question'), $threshold, $indicator); //send only question part to calculator

        $temporaryDirectory = (new TemporaryDirectory())->create();
        $distanceFile = $temporaryDirectory->path('distance.dat');
        $dfp = fopen($distanceFile, 'a');
        $distanceStr = '';
        foreach($distanceMatrix as $key => $arr){
             $distanceStr .= implode(',', $arr) . PHP_EOL;
        }
        fwrite($dfp, $distanceStr);
        fclose($dfp);

        //clustering with evaluator.py
        $processArray = ['python3', dirname(__DIR__,1)."/Modules/Scripts/evaluator.py", $distanceFile, $threshold];
        
        $process = new Process($processArray);
        $process->run();
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $output = json_decode($process->getOutput());
        $temporaryDirectory->delete();

        $ids = [];
        foreach($output->sample_ids_in_largest_cluster as $id){
            $ids[] = $questions[$id]['id'];
        }

        return [
            'total_clusters' => $output->total_clusters,
            'avg_questions_per_cluster' => $output->average_question_per_clusters,
            'standard deviation' => $output->std,
            'total_question_in_the_largest_cluster' => $output->questions_in_largest_cluster,
            'example_ids_in_the_largest_cluster' => $ids,
        ];
    }
}