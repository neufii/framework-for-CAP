<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Modules\ModuleManager;

class QuestionInstance extends Model
{
    use HasFactory;

    //relationship
    public function indicator(){
        return $this->belongsTo('App\Models\Indicator', 'indicator_id', 'id');
    }

    public function history(){
        return $this->belongsToMany('App\Models\Learner', 'history', 'question_id', 'learner_id')
        ->withPivot('answer', 'time_used')
        ->withTimestamps();
    }

    public function generator(){
        return $this->belongsTo('App\Models\Module', 'id');
    }

    public function statistic(){
        return $this->hasOne('App\Models\QuestionStat', 'id');
    }

    //scope
    public function scopeRatingBetween($query,$min,$max){
        return $query->whereHas('statistic',function ($q) use ($min, $max){
            return $q->where('rating','>=',$min)->where('rating','<',$max);
        });
    }

    public function scopeRatingGreaterOrEqaul($query,$min){
        return $query->whereHas('statistic',function ($q) use ($min){
            return $q->where('rating','>=',$min);
        });
    }

    public function scopeRatingLessThan($query,$max){
        return $query->whereHas('statistic',function ($q) use ($max){
            return $q->where('rating','<',$max);
        });
    }

    //accessor
    public function getRatingAttribute(){
        return $this->statistic()->first()->rating;
    }

    public function getTotalAttemptsAttribute(){
        return $this->statistic()->first()->total_attempts;
    }

    //function
    public static function generate($indicator, $preferredLevel=null, $preferredGenerator = null){
        $generator = $preferredGenerator ? $preferredGenerator : $indicator->compatibleModules()->generator()->active()->latest()->first();
        if(isset($generator)){
            $generated_question = ModuleManager::runProcess($generator,$indicator->id, $preferredLevel);
        }
        if(!isset($generator) || !isset($generated_question)){
            //no generator or nothing return from generator
            return null;
        }
        
        //store to database
        $question = new QuestionInstance();
        $question->question = $generated_question['question'];
        $question->answer = $generated_question['answer'];
        $question->solution = $generated_question['solution'];
        $question->indicator_id = $indicator->id;
        $question->generator_id = $generator->id;
        $question->save();

        $stat = new QuestionStat();
        $stat->initial_level = $generated_question['level'];
        $stat->question()->associate($question);
        $stat->save();

        return $question;
    }

    public static function selectQuestion($learner, $indicator, $includeHistory=false, $preferredLevel=null){
        $selector = $indicator->compatibleModules()->selector()->active()->latest()->first();
        if(isset($selector)){
            $customInstance = ModuleManager::runProcess($selector, $learner->id, $indicator->id, $includeHistory, $preferredLevel);
        }
        
        if(isset($customInstance)){
            $selectedInstance = $customInstance['questionInstanceId'] ? QuestionInstance::findOrFail($customInstance['questionInstanceId']) : null;
            $targetLevel = $customInstance['targetLevel'] ? $customInstance['targetLevel'] : null;
        }
        else{
            $selectedInstance = null;

            //no selector or nothing return from selector, use default selecting algorithm
            if(!$includeHistory){
                $history = $learner->history()->where('indicator_id',$indicator->id)->pluck('question_id');
            }
            else{
                $history = $learner->history()->where('indicator_id',$indicator->id)->orderByDesc('history.created_at')->take(20)->pluck('question_id');
            }

            //get only question from active generators
            $generatorsId = $indicator->compatibleModules()->generator()->active()->get()->pluck('id');
            $query = QuestionInstance::where('indicator_id',$indicator->id)->whereNotIn('id',$history)->whereIn('generator_id',$generatorsId);
            $allQuestionIds = QuestionInstance::where('indicator_id',$indicator->id)->pluck('id');

            if(isset($preferredLevel)){
                $median = QuestionStat::whereIn('question_id',$allQuestionIds)->get()->median('rating');
                if(!isset($median)) $median = 0.0;
                switch($preferredLevel){
                    case 1:{
                        $max = QuestionStat::whereIn('question_id',$allQuestionIds)->where('rating','<',$median)->get()->median('rating');
                        if(!isset($max)) $max = 0.0;
                        $query = $query->ratingLessThan($max);
                        break;
                    }
                    case 2:{
                        $min = QuestionStat::whereIn('question_id',$allQuestionIds)->where('rating','<',$median)->get()->median('rating');
                        if(!isset($min)) $min = 0.0;
                        $query = $query->ratingBetween($min,$median);
                        break;
                    }
                    case 3:{
                        $max = QuestionStat::whereIn('question_id',$allQuestionIds)->where('rating','>',$median)->get()->median('rating');
                        if(!isset($max)) $max = 0.0;
                        $query = $query->ratingBetween($median,$max);
                        break;
                    }
                    case 4:{
                        $min = QuestionStat::whereIn('question_id',$allQuestionIds)->where('rating','>',$median)->get()->median('rating');
                        if(!isset($min)) $min = 0.0;
                        if(!isset($max)) $max = 0.0;
                        $query = $query->ratingGreaterOrEqaul($min);
                        break;
                    }
                }
                $selectedInstance = $query->inRandomOrder()->first();
                $targetLevel = $preferredLevel;
            }
            else{
                $probCorrect = 0;
                while($probCorrect<= 0.5 || $probCorrect >= 1){
                    $x = mt_rand()/mt_getrandmax();
                    $y = mt_rand()/mt_getrandmax();
                    $probCorrect =  sqrt(-2*log($x))*cos(2*pi()*$y)*0.1 + 0.7;
                }
                $targetRating = $learner->getRating($indicator->id) - log($probCorrect/(1-$probCorrect));
                $collection = $query->get();
                $upperInstance = QuestionInstance::where('indicator_id',$indicator->id)
                    ->whereNotIn('id',$history)
                    ->whereIn('generator_id',$generatorsId)
                    ->whereHas('statistic',function($q) use($targetRating){
                        $q->where('rating','>',$targetRating);
                    })->with('statistic')->get()->sortBy('statistic.rating')->first();
                $lowerInstance = QuestionInstance::where('indicator_id',$indicator->id)
                    ->whereNotIn('id',$history)
                    ->whereIn('generator_id',$generatorsId)
                    ->whereHas('statistic',function($q) use($targetRating){
                        $q->where('rating','<=',$targetRating);
                    })->with('statistic')->get()->sortByDesc('statistic.rating')->first();

                if(!isset($upperInstance)){
                    $targetInstance = $lowerInstance;
                }
                else if(!isset($lowerInstance)){
                    $targetInstance = $upperInstance;
                }
                else{
                    $targetInstance = abs($upperInstance->rating - $targetRating) < abs($lowerInstance->rating - $targetRating) ? $upperInstance : $lowerInstance;
                }

                if(isset($targetInstance)){
                    $selectedInstance = $query->whereHas('statistic',function($q) use($targetInstance){
                        $q->where('rating',$targetInstance->rating);
                    })->inRandomOrder()->first();
                    $targetLevel = null;
                }

                if(!isset($selectedInstance)){
                    //no instance selected, find target level for generator.
                    if(empty($allQuestionIds->toArray()) || !isset($allQuestionIds)){
                        //no question in question bank, generate level 2 question
                        $targetLevel = 2;
                    }
                    else{
                        $median = QuestionStat::whereIn('question_id',$allQuestionIds)->get()->median('rating');

                        if($targetRating > $median){
                            $higherMedian = QuestionStat::whereIn('question_id',$allQuestionIds)->where('rating','>=',$median)->get()->median('rating');
                            $targetLevel = $targetRating > $higherMedian ? 4:3;
                        }
                        else{
                            $lowerMedian = QuestionStat::whereIn('question_id',$allQuestionIds)->where('rating','<=',$median)->get()->median('rating');
                            $targetLevel = $targetRating > $lowerMedian ? 2:1;
                        }
                    }
                }
            }
        }
        
        return ['targetLevel'=>$targetLevel, 'questionInstance' => $selectedInstance];
    }

    public function getDisplayableQuestion(){
        $questionDisplay = $this->indicator()->first()->compatibleModules()->questionDisplay()->active()->latest()->first();
        if(!isset($questionDisplay)){
            //no question display
            return;
        }
        return ModuleManager::runProcess($questionDisplay, $this->question);
    }

    public function getDisplayableSolution(){
        $solutionDisplay = $this->indicator()->first()->compatibleModules()->solutionDisplay()->active()->latest()->first();
        if(!isset($solutionDisplay)){
            //no solution display
            return;
        }
        return ModuleManager::runProcess($solutionDisplay, $this->solution);
    }

    public function check($learnerAnswer){
        if(!$learnerAnswer){
            return false;
        }

        $correctAnswer =  $this->answer;

        $checker = $this->indicator()->first()->compatibleModules()->checker()->active()->latest()->first();
        if(!isset($checker)){
            //no answer checker
            return;
        }
        $isCorrect = ModuleManager::runProcess($checker, $learnerAnswer, $correctAnswer);

        return filter_var($isCorrect, FILTER_VALIDATE_BOOLEAN);
    }

    public function addHistory($learner, $learnerAnswer, $isCorrect, $timeUsed){
        if(isset($learner)){
            $learner->history()->attach($this,['answer' => $learnerAnswer ,'time_used' => $timeUsed]);
            $learnerAttempts = $learner->getTotalAttempts($this->indicator->id);
            $learner->learningIndicators()->sync([$this->indicator_id => [ 'total_attempts' => $learnerAttempts+1] ], false);

            $stat = $this->statistic()->first();
            $newTotalAttempts = $stat->total_attempts+1;
            $stat->average_time_used = (($stat->average_time_used * $stat->total_attempts)+$timeUsed)/$newTotalAttempts;
            $stat->total_attempts = $newTotalAttempts;
            if($isCorrect) {
                $stat->correct_attempts += 1;
            }
            $stat->save();
        }
    }

    public function updateRating($learner, $isCorrect){   
        $updater = $this->indicator()->first()->compatibleModules()->updater()->active()->latest()->first();
        if(isset($updater)){
            $newRatings = ModuleManager::runProcess($updater, $this->id, $learner->id, $isCorrect);
        }

        if(!isset($newRatings)){
            $questionRating = $this->rating;
            $learnerRating = $learner->getRating($this->indicator->id);
            $learnerAttempts = $learner->getTotalAttempts($this->indicator->id);
            $score = $isCorrect ? 1 : 0;
    
            $questionUncertaincy = 1/(1+(0.05*$this->totalAttempts));
            $learnerUncertaincy = 1/(1+(0.05*$learnerAttempts));

            $expectedScore = 1/(1+exp(-($learnerRating-$questionRating)));

            $newQuestionRating = $questionRating + $questionUncertaincy*($expectedScore - $score);
            $newLearnerRating = $learnerRating + $learnerUncertaincy*($score - $expectedScore);

            $newRatings['questionRating'] = $newQuestionRating;
            $newRatings['learnerRating'] = $newLearnerRating;
        }

        $questionStat = $this->statistic()->first();
        $questionStat->rating  = $newRatings['questionRating'];
        $questionStat->save();
        $learner->learningIndicators()->sync([$this->indicator_id => [ 'rating' => $newRatings['learnerRating']] ], false);
    }

    public function upvote(){
        $stat = $this->statistic()->first();
        $stat->upvotes += 1;
        $stat->save();
    }

    public function downvote(){
        $stat = $this->statistic()->first();
        $stat->downvotes += 1;
        $stat->save();
    }
}
