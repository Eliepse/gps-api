<?php

use App\Http\Controllers\CreateTraceController;
use App\Http\Controllers\GetLiveDataController;
use App\Http\Controllers\StopTraceController;
use App\Http\Controllers\Trackers\RegisterTrackerController;
use App\Http\Controllers\Trackers\UpdateTrackerController;
use App\Http\Controllers\UserInfoController;
use App\Http\Middleware\MercureBroadcasterAuthorizationCookie;
use App\Models\Tracker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;

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

Broadcast::routes(['middleware' => ['auth:sanctum']]);

Route::post("/tracker/{tracker:uid}/register", RegisterTrackerController::class);

Route::middleware('auth:sanctum')->group(function () {
	Route::get("/me", UserInfoController::class)->middleware([MercureBroadcasterAuthorizationCookie::class]);

	Route::get("/mercure-token", fn() => response()->noContent())->middleware([MercureBroadcasterAuthorizationCookie::class]);

	Route::get("/recoverData", GetLiveDataController::class);

	Route::get('/tracker', function (Request $request) {
		/** @var Tracker $tracker */
		$tracker = $request->user();
		$tracker->seen()->save();
		return [...$tracker->toArray(), "topics" => [$tracker->broadcastChannel(), $tracker->getBroadcastToUserChannel()],
		];
	})->middleware(["tracker", MercureBroadcasterAuthorizationCookie::class]);

	Route::post("/tracker/self-update", UpdateTrackerController::class)->middleware([MercureBroadcasterAuthorizationCookie::class]);

	Route::post("/trace", CreateTraceController::class);
	Route::post("/trace/{trace:uid}/stop", StopTraceController::class);
});
