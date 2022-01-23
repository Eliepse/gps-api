<?php

namespace App\Http\Middleware;

use App\Enums\TrackerStatus;
use App\Models\Tracker;
use App\Models\User;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class TrackerOnlyMiddleware
{
	/**
	 * Handle an incoming request.
	 *
	 * @param Request $request
	 * @param Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
	 *
	 * @return Response|RedirectResponse
	 * @throws HttpException
	 */
	public function handle(Request $request, Closure $next)
	{
		/** @var ?User|?Tracker $authModel */
		$authModel = $request->user();

		if (! $authModel) {
			throw new HttpException(statusCode: 401);
		}

		if (! is_a($authModel, Tracker::class, false)) {
			throw new HttpException(statusCode: 403);
		}

		if ($authModel->status === TrackerStatus::Banned) {
			throw new HttpException(statusCode: 403);
		}

		return $next($request);
	}
}
