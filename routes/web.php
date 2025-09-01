<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\SiteBuilderController;
use App\Http\Controllers\BlockController;
use App\Http\Controllers\MassEmailController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Главная страница - перенаправляем на конструктор для авторизованных пользователей
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('site-builder.index');
    }
    return redirect()->route('login');
})->name('home');

// Маршруты конструктора сайтов (только для авторизованных пользователей)
Route::prefix('site-builder')->name('site-builder.')->middleware('auth')->group(function () {
    Route::get('/', [SiteBuilderController::class, 'index'])->name('index');
    Route::post('/preview', [SiteBuilderController::class, 'preview'])->name('preview');
    Route::post('/build', [SiteBuilderController::class, 'build'])->name('build');
    Route::post('/update-block-content', [SiteBuilderController::class, 'updateBlockContent'])->name('update-block-content');
    Route::get('/download/{site}', [SiteBuilderController::class, 'download'])->name('download');
    Route::get('/sites', [SiteBuilderController::class, 'sites'])->name('sites');
});

// Маршруты управления блоками (только для авторизованных пользователей)
Route::resource('blocks', BlockController::class)->middleware('auth');
Route::get('/blocks/{block}/preview', [BlockController::class, 'preview'])->name('blocks.preview')->middleware('auth');
Route::get('/blocks/{block}/mini-preview', [BlockController::class, 'miniPreview'])->name('blocks.mini-preview')->middleware('auth');

// Маршруты массовой рассылки (только для авторизованных пользователей)
Route::prefix('mass-email')->name('mass-email.')->middleware('auth')->group(function () {
    Route::get('/', [MassEmailController::class, 'index'])->name('index');
    Route::post('/import', [MassEmailController::class, 'importEmails'])->name('import');
    Route::post('/send-batch', [MassEmailController::class, 'sendBatch'])->name('send-batch');
    Route::get('/stats', [MassEmailController::class, 'getStats'])->name('stats');
    Route::post('/reset-failed', [MassEmailController::class, 'resetFailed'])->name('reset-failed');
    Route::post('/clear', [MassEmailController::class, 'clearQueue'])->name('clear');
    Route::get('/template', [MassEmailController::class, 'previewTemplate'])->name('template');
});

Auth::routes();
if (app()->environment('production')) {
    URL::forceScheme('https');
}
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
