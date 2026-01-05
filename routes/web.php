<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ContactsController;
use App\Http\Controllers\CompaniesController;
use App\Http\Controllers\DealsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PipelinesController;
use App\Http\Controllers\PipelineStageController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\DashboardApiController;
use App\Http\Controllers\WorkspaceController;

/*
|--------------------------------------------------------------------------
| Public
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('welcome'); // not logged-in => welcome with login/register
})->name('home');

require __DIR__.'/auth.php';


// Workspace creation (no workspace required)
Route::middleware('auth')->group(function () {
    Route::get('/workspaces/create', [\App\Http\Controllers\WorkspaceController::class, 'create'])
        ->name('workspaces.create');

    Route::get('/workspaces', [WorkspaceController::class, 'index'])
        ->name('workspaces.index');

    Route::post('/workspaces', [\App\Http\Controllers\WorkspaceController::class, 'store'])
        ->name('workspaces.store');

    Route::post('/workspaces/switch', [WorkspaceController::class, 'switch'])
        ->name('workspaces.switch');


});


/*
|--------------------------------------------------------------------------
| Auth (no workspace required)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->get('/app', function () {
    $user = auth()->user();

    // 1) Prefer last active workspace if still valid membership
    if ($user->last_workspace_id) {
        $last = $user->workspaces()
            ->where('workspaces.id', $user->last_workspace_id)
            ->first();

        if ($last) {
            return redirect()->route('dashboard', ['workspace' => $last->slug]);
        }
    }

    // 2) Otherwise fallback to first workspace
    $workspace = $user->workspaces()->orderBy('workspaces.id')->first();

    if (!$workspace) {
        return redirect()->route('workspaces.create');
    }

    return redirect()->route('dashboard', ['workspace' => $workspace->slug]);
})->name('app');


/*
|--------------------------------------------------------------------------
| Workspace scoped routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'workspace'])
    ->prefix('w/{workspace:slug}')
     ->scopeBindings()
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::resource('pipelines', PipelinesController::class);
       

        Route::get('/deals/board', [DealsController::class, 'board'])
            ->name('deals.board')
            ->middleware('feature:deals.basic');

        Route::post('/deals/kanban/move', [DealsController::class, 'moveOnBoard'])->name('deals.kanban.move');

        Route::resource('contacts', ContactsController::class)->middleware('feature:contacts');
        Route::resource('companies', CompaniesController::class)->middleware('feature:companies');
        Route::resource('deals', DealsController::class)->middleware('feature:deals.basic');
        // Route::resource('pipelines', PipelinesController::class)->middleware('feature:pipelines.basic');


        Route::resource('activities', ActivityController::class)->only(['index', 'show']);
        Route::post('/activities', [ActivityController::class, 'store'])->name('activities.store');
        Route::put('/activities/{activity}', [ActivityController::class, 'update'])->name('activities.update');
        Route::delete('/activities/{activity}', [ActivityController::class, 'destroy'])->name('activities.destroy');

        Route::post('/notes', [NoteController::class, 'store'])->name('notes.store');
        Route::delete('/notes/{note}', [NoteController::class, 'destroy'])->name('notes.destroy');

        Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.readAll');

        

        Route::post('pipelines/{pipeline}/stages', [PipelineStageController::class, 'store'])->name('pipelines.stages.store');
        Route::put('pipelines/{pipeline}/stages/{stage}', [PipelineStageController::class, 'update'])->name('pipelines.stages.update');
        Route::delete('pipelines/{pipeline}/stages/{stage}', [PipelineStageController::class, 'destroy'])->name('pipelines.stages.destroy');
        Route::post('pipelines/{pipeline}/stages/reorder', [PipelineStageController::class, 'reorder'])->name('pipelines.stages.reorder');

        Route::prefix('api/dashboard')->group(function () {
            Route::get('/kpis', [DashboardApiController::class, 'kpis'])->name('api.dashboard.kpis');
            Route::get('/deals-by-stage', [DashboardApiController::class, 'dealsByStage'])->name('api.dashboard.dealsByStage');
            Route::get('/deals-trend', [DashboardApiController::class, 'dealsTrend'])->name('api.dashboard.dealsTrend');
            Route::get('/activities-summary', [DashboardApiController::class, 'activitiesSummary'])->name('api.dashboard.activitiesSummary');
        });
    });
