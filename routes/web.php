<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\SiteBuilderController;
use App\Http\Controllers\BlockController;
use App\Http\Controllers\EmailCampaignController;
use App\Http\Controllers\EmailDashboardController;
use App\Http\Controllers\EmailTrackingController;

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

// Маршруты для Email-кампаний и Dashboard
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/email/dashboard', [EmailDashboardController::class, 'index'])->name('email.dashboard');
    Route::get('/email/dashboard/realtime', [EmailDashboardController::class, 'realtimeStats'])->name('email.dashboard.realtime');
    
    // Кампании
    Route::get('/email/campaigns', [EmailCampaignController::class, 'index'])->name('email.campaigns.index');
    Route::get('/email/campaigns/create', [EmailCampaignController::class, 'create'])->name('email.campaigns.create');
    Route::post('/email/campaigns', [EmailCampaignController::class, 'store'])->name('email.campaigns.store');
    Route::get('/email/campaigns/{campaign}', [EmailDashboardController::class, 'campaignDetails'])->name('email.campaigns.show');
    Route::post('/email/campaigns/{campaign}/pause', [EmailDashboardController::class, 'pauseCampaign'])->name('email.campaigns.pause');
    Route::post('/email/campaigns/{campaign}/resume', [EmailDashboardController::class, 'resumeCampaign'])->name('email.campaigns.resume');
    Route::get('/email/campaigns/{campaign}/export', [EmailDashboardController::class, 'exportCampaign'])->name('email.campaigns.export');
    
    // Старые роуты для обратной совместимости
    Route::get('/email-campaign', [EmailCampaignController::class, 'index'])->name('email-campaign.index');
    Route::post('/email-campaign/send', [EmailCampaignController::class, 'send'])->name('email-campaign.send');
    Route::get('/email-campaign/preview', [EmailCampaignController::class, 'preview'])->name('email-campaign.preview');
});

// Публичные роуты для отслеживания (без middleware auth)
Route::group(['prefix' => 'email'], function () {
    Route::get('/track/open/{trackingId}', [EmailTrackingController::class, 'trackOpen'])->name('email.track.open');
    Route::get('/track/click/{trackingId}', [EmailTrackingController::class, 'trackClick'])->name('email.track.click');
    Route::get('/unsubscribe/{trackingId}', [EmailTrackingController::class, 'unsubscribe'])->name('email.unsubscribe');
    Route::post('/unsubscribe/{trackingId}', [EmailTrackingController::class, 'unsubscribe']);
});

Auth::routes();
if (app()->environment('production')) {
    URL::forceScheme('https');
}
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
