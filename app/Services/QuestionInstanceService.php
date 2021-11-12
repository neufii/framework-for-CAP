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

    public function generate(Indicator $indicator, int $preferredLevel=null, Script $preferredGenerator=null){
        $generated_question = $this->generator->execute($indicator, $preferredLevel, $preferredGenerator);
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
        // $question->generator_id = $generator->id; ควรจะมีมั้ย? เพื่อจะได้ track ได้ว่ามาจาก script เบอร์ไหน
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

    public function check(Learner $learner, string $learnerAnswer){
        return $this->answerChecker->execute($this->questionInstance, $learnerAnswer);
    }

    public function updateRating(Learner $learner, bool $isCorrect){
        $ratings = $this->updater->execute($this->$questionInstance, $learner, $isCorrect);

        $this->questionInstance->rating = $ratings['questionInstanceRating'];
        $this->questionInstance->save();
        
        $learner->learningIndicators()->sync([$this->questionInstance->indicator_id => [ 'rating' => $ratings['learnerRating']] ], false);
    }

    public function addHistory(Learner $learner, string $learnerAnswer, bool $isCorrect, integer $timeUsed){
        $learner->history()->attach($this->questionInstance,['answer' => $learnerAnswer ,'time_used' => $timeUsed]);

        $learnerService = new LearnerService($learner);

        $learnerAttempts = $learnerService->getStatistic($this->questionInstance->indicator)->total_attempts;
        $learner->learningIndicators()->sync([$this->questionInstance->indicator_id => [ 'total_attempts' => $learnerAttempts+1] ], false);

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
            $this->setQuestionInstance->save();
        }
        else if($action == 'downvote'){
            $this->questionInstance->downvotes += 1;
            $this->setQuestionInstance->save();
        }

        return [
            'upvotes' => $this->questionInstance->upvotes,
            'downvotes' => $this->questionInstance->downvotes,
        ];
    }

    public function evaluateUniqueness(array $questions){
        $distanceMatrix = $this->distanceCalculator->execute($questions);
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
    }
}