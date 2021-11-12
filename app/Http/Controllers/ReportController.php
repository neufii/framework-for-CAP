<?php

namespace App\Http\Controllers;

use App\Models\QuestionInstance;
use App\Models\Learner;
use App\Models\Script;

use App\Services\QuestionInstanceService;
use App\Services\LearnerService;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Response;

class ReportController extends Controller
{
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

    public function getQuestionUniquenessReport(
            $indicatorId, 
            int $numberOfQuestions = 2000, 
            float $threshold = 0.5, 
            int $preferredLevel = NULL, 
            Script $preferredScript = NULL
    ){
        $questionInstanceService = new QuestionInstanceService();
        $quetionIds = [];
        $indicator = Indicator::findOrFail($indicatorId);

        //generate new question for evaluation
        for($i = 0; $i < $numberOfQuestions; $i++){
            $question = $questionInstanceService->generate($indicator, $preferredLevel, $preferredGenerator);
            array_push($quetionIds,$question->id);
        }

        //evaluate
        $result = $questionInstanceService->evaluateUniqueness($questionIds);

        return Response::json([
            'status' => 'completed',
            'message' => 'System Report Retreived',
            'data' => $result,
        ], 200);
    }

    public function getIndicatorReport($indicatorId){
        $indicator = Indicator::findOrFail($indicatorId);
        
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
            ],
        ], 200);

    }

    public function getQuestionInstanceReport($questionId){
        $question = QuestionInstance::findOrFail($questionId);

        $questionsRating = $question->indicator->questions()->orderBy('rating','asc')->pluck('rating');
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

        $stat = $learnerService->getStatistic($question->indicator);

        foreach($indicators as $indicator){
            $data = [];
            $data['id'] = $indicator->id;
            $data['name'] = $indicator->name;
            $data['description'] = $indicator->description;
            $data['statistic'] = [
                'rating' => $stat->rating,
                'total_attempts' => $stat->total_attempts,
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
