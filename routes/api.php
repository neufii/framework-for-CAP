<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\IndicatorController;
use App\Http\Controllers\LearnerController;
use App\Http\Controllers\QuestionInstanceController;
use App\Http\Controllers\ReportController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::resource('indicators',IndicatorController::class)->except([
    'create' ,'edit'
]);

Route::resource('learners',LearnerController::class)->except([
    'create' ,'edit'
]);

Route::get('indicators/{indicatorId}/get-question',[QuestionInstanceController::class, 'getQuestion']);

Route::put('questions/{questionId}/submit', [QuestionInstanceController::class, 'submit']);
Route::put('questions/{questionId}/vote',[QuestionInstanceController::class, 'vote']);

//reports
Route::get('reports/system',[ReportController::class, 'getSystemReport']);
Route::get('reports/generator/{id}/uniqueness',[ReportController::class, 'getQuestionUniquenessReport']);
Route::get('reports/indicator/{id}',[ReportController::class, 'getIndicatorReport']);
Route::get('reports/question/{id}',[ReportController::class, 'getQuestionInstanceReport']);
Route::get('reports/learner/{id}',[ReportController::class, 'getLearnerReport']);
