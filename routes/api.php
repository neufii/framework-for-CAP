<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\IndicatorController;
use App\Http\Controllers\LearnerController;
use App\Http\Controllers\QuestionInstanceController;

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

Route::get('questions/{id}/solution',[QuestionInstanceController::class, 'getSolution']);
