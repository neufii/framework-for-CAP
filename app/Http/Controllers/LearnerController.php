<?php

namespace App\Http\Controllers;

use App\Models\Learner;

use App\Services\LearnerService;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Response;

class LearnerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Response::json([
            'status' => 'completed',
            'message' => 'Learners Retreived',
            'data' => Learner::get(),
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $learner = LearnerService::register();

        return Response::json([
            'status' => 'completed',
            'message' => 'Learner Stored',
            'data' => $learner,
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Response::json([
            'status' => 'completed',
            'message' => 'Learner Retreived',
            'data' => Learner::findOrFail($id),
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $learner = Learner::findOrFail($id);
        $learner->delete();

        return Response::json([
            'status' => 'completed',
            'message' => 'Learner Deleted',
            'data' => $learner,
        ], 200);
    }
}
