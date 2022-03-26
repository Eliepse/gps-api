<?php

namespace App\Http\Middleware;

use App\Mercure\MercureManager;
use App\Models\MercureSubscriberInterface;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MercureBroadcasterAuthorizationCookie
{
	public function __construct(private MercureManager $mercure) { }


	public function handle(Request $request, Closure $next)
	{
		/** @var Response $response */
		$response = $next($request);

		if (! method_exists($response, 'withCookie')) {
			return $response;
		}

		return $response->withCookie($this->createCookie($request->user()));
	}


	private function createCookie(MercureSubscriberInterface $user)
	{
		return $this->mercure->makeCookieToken(["*"], ["*"], $user->getMercurePayload());
	}
}