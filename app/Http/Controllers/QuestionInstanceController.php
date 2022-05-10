<?php

namespace App\Http\Controllers;

use App\Models\Indicator;
use App\Models\QuestionInstance;
use App\Models\Learner;

use App\Services\QuestionInstanceService;
use App\Services\LearnerService;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Response;

class QuestionInstanceController extends Controller
{
    private $service;

    public function __construct(QuestionInstanceService $service){
        $this->service = $service;
    }

    public function getQuestion($indicatorId, Request $request)
    {
        $params = [
            'includeHistory' => filter_var($request->input('indclude_history',false), FILTER_VALIDATE_BOOLEAN),
            'preferredLevel' => $request->input('level'),
            'learnerId' => $request->input('id')
        ];

        $learner = Learner::findOrFail($params['learnerId']);
        if(!isset($learner)){
            return response()->json(['message' => 'Learner with user id '.$params['userId'].' not found'], 404);
        }

        $indicator = Indicator::findOrFail($indicatorId);

        //if indicator is not added to learner's interessted list 
        if(!$learner->learningIndicators()->where('indicator_id',$indicator->id)->first()){
            $learner->learningIndicators()->attach($indicator,['rating' => 0 ,'total_attempts' => 0]);
        }
        //check compatible question
        $instanceData = $this->service->select($learner, $indicator, $params['includeHistory'], $params['preferredLevel']);
        $instance = $instanceData['questionInstance'];

        if(!isset($instance)){
            //nothing return from item bank
            $instance = $this->service->generate($indicator,$instanceData['targetLevel']);
        }
        
        if(!isset($instance)){
            //nothing return from generator
            return response()->json(['message' => 'Cannot Generate Question Instance'], 500);
        }

        //generate displayable question
        $script = $this->service->setQuestionInstance($instance)->getDisplayableQuestionScript();

        if(!isset($script)){
            //nothing return from question display
            return response()->json(['message' => 'Cannot Generate Displayable Question Script'], 500);
        }

        return Response::json([
            'status' => 'completed',
            'message' => 'Question ID:'.$instance->id.' Retreived',
            'data' => [
                'instance'=> [
                    'id'=> $instance->id,
                    'indicator'=> $instance->indicator,
                    'initial_level' => $instance->initial_level,
                    'rating' => $instance->rating,
                    'total_attempts' => $instance->total_attempts,
                    'average_time_used' => $instance->average_time_used,
                ], 
                'script'=>$script,
            ]
        ], 200);

    }

    public function submit(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'learner_id' => ['required','integer'],
            'answer' => ['required', 'string'],
            'time_used' => ['required', 'integer'],
         ]);
      
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $learner = Learner::findOrFail($request['learner_id']);
        if(!isset($learner)){
            return response()->json(['message' => 'Learner id '.$request['learner_id'].' not found'], 404);
        }
        
        $question = QuestionInstance::findOrFail($id);
        $isCorrect = $this->service->setQuestionInstance($instance)->check($learner, $request['answer']);

        //update rating
        $isCorrect = $this->service->setQuestionInstance($instance)->updateRating($learner, $isCorrect);

        //update history
        $this->service->setQuestionInstance($instance)->addHistory($learner, $learnerAnswer,$isCorrect, $timeUsed);

        //feedback
        $script = $this->service->setQuestionInstance($instance)->getDisplayableFeedbackScript($isCorrect, $request['answer']);

        //learner's rating
        $learnerService = new LearnerService($learner);
        $learnerRating = $learnerService->getStatistic($question->indicator)->rating;

        return Response::json([
            'status' => 'completed',
            'message' => 'Question ID:'.$question->id.' Updated and an Answer is Checked',
            'data' => [
                'question_id'=> $question->id,
                'is_correct'=> $isCorrect,
                'time_used'=> $request['time_used'],
                'question_rating' => QuestionInstance::findOrFail($id)->rating, //get updated rating
                'learner_rating' => $learnerRating,
                'script' => $script,
            ]
        ], 200);
    }

    public function vote(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'action' => ['required', 'in:upvote,downvote']
         ]);
      
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $question = QuestionInstance::findOrFail($id);

        $votes = $this->service->setQuestionInstance($instance)->vote($request['action']);

        return Response::json([
            'status' => 'completed',
            'message' => 'Question ID:'.$id.' Vote Counts Updated',
            'data' => $votes,
        ], 200);
    }
}
