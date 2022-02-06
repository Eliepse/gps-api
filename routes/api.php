<?php

use App\Http\Controllers\CreateTraceController;
use App\Http\Controllers\GetLiveDataController;
use App\Http\Controllers\StopTraceController;
use App\Http\Controllers\Trackers\RegisterTrackerController;
use App\Http\Controllers\UpdateTraceCoordinatesController;
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
	Route::get("/me", fn(Request $request) => $request->user())->middleware(["user"]);

	Route::get("/recoverData", GetLiveDataController::class);

	Route::get('/tracker', function (Request $request) {
		/** @var Tracker $tracker */
		$tracker = $request->user();
		$tracker->seen();
		$tracker->save();
		return $tracker;
	})->middleware(["tracker"]);
	Route::post("/trace", CreateTraceController::class);
	Route::post("/trace/{trace:uid}/stop", StopTraceController::class);

	Route::post("/traces/{trace}/coordinates", UpdateTraceCoordinatesController::class)->middleware(["tracker"]);
});
