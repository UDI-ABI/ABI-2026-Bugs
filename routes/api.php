<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\ContentVersionController;
use App\Http\Controllers\InvestigationLineController;
use App\Http\Controllers\ProgramController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ResearchGroupController;
use App\Http\Controllers\ThematicAreaController;
use App\Http\Controllers\VersionController;

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
    // El historico de versiones es inmutable: solo lectura, nunca se crea/edita/borra por esta API.
    // Nota: index/show siguen sin exigir sesion iniciada porque este grupo 'api' no arranca
    // sesion/cookies (ver Kernel::$middlewareGroups['api']), asi que 'auth' aqui rompería
    // las paginas versions/* y content-versions/* que hoy leen via fetch() con la cookie del
    // navegador. Protegerlas requiere habilitar Sanctum stateful (o mover estas rutas a 'web')
    // como cambio aparte; por ahora solo se cerro la escritura, que era el riesgo real.
    Route::apiResource('versions', VersionController::class)->only(['index', 'show']);
    Route::apiResource('content-versions', ContentVersionController::class)->only(['index', 'show']);
    Route::get('projects/meta', [ProjectController::class, 'meta'])->name('projects.meta');
    Route::post('projects/{project}/restore', [ProjectController::class, 'restore'])->name('projects.restore');
    Route::apiResource('projects', ProjectController::class);
});