<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\InvestigationLineController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ResearchGroupController;
use App\Http\Controllers\ThematicAreaController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::name('api.')->group(function () {
    Route::apiResource('research-groups', ResearchGroupController::class);
    Route::apiResource('programs', ProgramController::class);
    Route::apiResource('investigation-lines', InvestigationLineController::class);
    Route::apiResource('thematic-areas', ThematicAreaController::class);
    Route::apiResource('contents', ContentController::class);
    // El historico de versiones (versions / content-versions) vive en routes/web.php,
    // dentro del grupo auth+role:research_staff, porque necesita sesion iniciada para
    // exigir login en la lectura y este grupo 'api' no arranca sesion/cookies.
    Route::get('projects/meta', [ProjectController::class, 'meta'])->name('projects.meta');
    Route::post('projects/{project}/restore', [ProjectController::class, 'restore'])->name('projects.restore');
    Route::apiResource('projects', ProjectController::class);
});