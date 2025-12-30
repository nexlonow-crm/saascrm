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

/*
|--------------------------------------------------------------------------
| Auth (no workspace required)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ✅ ENTRY ROUTE:
    // If logged in + has workspace -> redirect to /w/{workspace}/dashboard
    // If logged in + no workspace -> redirect to create workspace screen
    Route::get('/app', function () {
        $user = auth()->user();

        // Adjust this to your relationship / query
        // Example: user has many workspaces
        $workspace = $user->workspaces()->first();

        if (! $workspace) {
            return redirect()->route('workspaces.create');
        }

        return redirect()->route('dashboard', ['workspace' => $workspace->slug]);
    })->name('app');

    // ✅ Workspace create flow (no {workspace} needed)
    Route::get('/workspaces/create', [WorkspaceController::class, 'create'])->name('workspaces.create');
    Route::post('/workspaces', [WorkspaceController::class, 'store'])->name('workspaces.store');
});

/*
|--------------------------------------------------------------------------
| Workspace scoped routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'workspace'])
    ->prefix('w/{workspace:slug}')
    ->group(function () {

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('/deals/board', [DealsController::class, 'board'])->name('deals.board');
        Route::post('/deals/kanban/move', [DealsController::class, 'moveOnBoard'])->name('deals.kanban.move');

        Route::resource('contacts', ContactsController::class);
        Route::resource('companies', CompaniesController::class);
        Route::resource('deals', DealsController::class);

        Route::resource('activities', ActivityController::class)->only(['index', 'show']);
        Route::post('/activities', [ActivityController::class, 'store'])->name('activities.store');
        Route::put('/activities/{activity}', [ActivityController::class, 'update'])->name('activities.update');
        Route::delete('/activities/{activity}', [ActivityController::class, 'destroy'])->name('activities.destroy');

        Route::post('/notes', [NoteController::class, 'store'])->name('notes.store');
        Route::delete('/notes/{note}', [NoteController::class, 'destroy'])->name('notes.destroy');

        Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.readAll');

        Route::resource('pipelines', PipelinesController::class);

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
