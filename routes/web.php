<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactsController;
use App\Http\Controllers\CompaniesController;
use App\Http\Controllers\DealsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PipelinesController;
use App\Http\Controllers\PipelineStageController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\NoteController;

Route::get('/', function () {
    return view('welcome');
});

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';


Route::middleware(['auth', 'tenant'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Kanban board
    Route::get('/deals/board', [DealsController::class, 'board'])
        ->name('deals.board');

    
    // AJAX move endpoint for drag-and-drop
    Route::post('/deals/kanban/move', [DealsController::class, 'moveOnBoard'])
        ->name('deals.kanban.move');
        
    // Core resources
    Route::resource('contacts', ContactsController::class);
    Route::resource('companies', CompaniesController::class);
    Route::resource('deals', DealsController::class);

    // Activities
    Route::resource('activities', ActivityController::class)->only(['index', 'show']); // optional
    Route::post('/activities', [ActivityController::class, 'store'])->name('activities.store');
    Route::put('/activities/{activity}', [ActivityController::class, 'update'])->name('activities.update');
    Route::delete('/activities/{activity}', [ActivityController::class, 'destroy'])->name('activities.destroy');

    // Notes
    Route::post('/notes', [NoteController::class, 'store'])->name('notes.store');
    Route::delete('/notes/{note}', [NoteController::class, 'destroy'])->name('notes.destroy');

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.readAll');

    // Pipelines CRUD
    Route::resource('pipelines', PipelinesController::class);

    // Stages inside a pipeline
    Route::post('pipelines/{pipeline}/stages', [PipelineStageController::class, 'store'])
        ->name('pipelines.stages.store');

    Route::put('pipelines/{pipeline}/stages/{stage}', [PipelineStageController::class, 'update'])
        ->name('pipelines.stages.update');

    Route::delete('pipelines/{pipeline}/stages/{stage}', [PipelineStageController::class, 'destroy'])
        ->name('pipelines.stages.destroy');

    Route::post('pipelines/{pipeline}/stages/reorder', [PipelineStageController::class, 'reorder'])
        ->name('pipelines.stages.reorder');

    

});










