<?php

namespace App\Http\Controllers;

use App\Models\Indicator;
use App\Models\QuestionInstance;
use App\Models\Learner;
use App\Models\Script;

use App\Services\QuestionInstanceService;
use App\Services\LearnerService;

use App\Modules\Repositories\Interfaces\Generator;
use App\Modules\Repositories\Interfaces\Selector;
use App\Modules\Repositories\Interfaces\QuestionDisplay;
use App\Modules\Repositories\Interfaces\FeedbackDisplay;
use App\Modules\Repositories\Interfaces\AnswerChecker;
use App\Modules\Repositories\Interfaces\Updater;
use App\Modules\Repositories\Interfaces\DistanceCalculator;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Response;

class ReportController extends Controller
{
    private $service;

    public function __construct(QuestionInstanceService $service){
        $this->service = $service;
    }

    public function getSystemReport(){

        $totalIndicators = Indicator::get()->count();
        $totalLearners = Learner::get()->count();
        $totalQuestions = QuestionInstance::get()->count();

        return Response::json([
            'status' => 'completed',
            'message' => 'System Report Retreived',
            'data' => [
                'total_indicators' => $totalIndicators,
                'total_learners' => $totalLearners,
                'total_questions' => $totalQuestions,
            ],
        ], 200);
    }

    public function getQuestionUniquenessReport($generatorId, Request $request){
        //get params
        $numberOfQuestions = $request->input('number_of_questions', 2000);
        $threshold = $request->input('threshold', 0.5);
        $preferredLevel = $request->input('level', 2);
        $generatorScript = Script::findOrFail($generatorId);
        $indicator = $generatorScript->compatibleIndicators()->first();

        // $questionInstanceService = new QuestionInstanceService();
        $questions = [];

        //generate new question for evaluation
        for($i = 0; $i < $numberOfQuestions; $i++){
            $question = $this->service->generate($indicator, $preferredLevel, $generatorScript);
            array_push($questions,$question);
        }

        //evaluate
        $result = $this->service->evaluateUniqueness($questions, $threshold, $indicator);

        return Response::json([
            'status' => 'completed',
            'message' => 'System Report Retreived',
            'data' => [
                'total_question' => $numberOfQuestions,
                'distance_threshold' => $threshold,
                'statistic' => $result,
            ],
        ], 200);
    }

    public function getIndicatorReport($indicatorId){
        $indicator = Indicator::with('compatibleScripts')->findOrFail($indicatorId);
        
        return Response::json([
            'status' => 'completed',
            'message' => 'Indicator Report Retreived',
            'data' => [
                'indicator' => $indicator,
                'total_learners' => $indicator->learners()->count(),
                'total_questions' => [
                    'active' => $indicator->questions()->where('is_active',1)->count(),
                    'inactive' => $indicator->questions()->where('is_active',0)->count()
                ],
                'active_questions_avg_rating' => $indicator->questions()->where('is_active',1)->avg('rating'),
            ],
        ], 200);

    }

    public function getQuestionInstanceReport($questionId){
        $question = QuestionInstance::findOrFail($questionId);

        $questionsRating = $question->indicator->questions()->where('is_active',1)->orderBy('rating','asc')->pluck('rating')->toArray();
        $percentile = array_search($question->rating,$questionsRating) * 100/count($questionsRating);

        return Response::json([
            'status' => 'completed',
            'message' => 'Indicator Report Retreived',
            'data' => [
                'id' => $question->id,
                'indicator' => $question->indicator,
                'generator_script_id' => $question->generator_script_id,
                'is_active' => $question->is_active,
                'total_attempts' => $question->total_attempts, 
                'total_correct_attempts' => $question->correct_attempts, 
                'average_time_used' => $question->average_time_used, 
                'upvotes' => $question->upvotes,
                'downvotes' => $question->downvotes,
                'initial_level' => $question->initial_level,
                'rating' => $question->rating,
                'percentile' => $percentile,
            ],
        ], 200);
    }
    
    public function getLearnerReport($learnerId){
        $learner = Learner::findOrFail($learnerId);
        $indicators = $learner->indicators;

        $learnerService = new LearnerService($learner);
        $indicatorsData = [];

        foreach((array) $indicators as $indicator){
            $stat = $learnerService->getStatistic($question->indicator);

            $data = [];
            $data['id'] = $indicator->id;
            $data['name'] = $indicator->name;
            $data['description'] = $indicator->description;
            $data['statistic'] = [
                'rating' => $stat->rating,
                'total_attempts' => $stat->total_attempts,
                'total_correct_attempts' => $stat->correct_attempts,
                'average time used' => $stat->average_time_used
            ];

            array_push($indicatorsData,$data);
        }

        return Response::json([
            'status' => 'completed',
            'message' => 'Learner Report Retreived',
            'data' => [
                'learner' => $learner,
                'indicators' => $indicatorsData,
            ],
        ], 200);
    }
}
