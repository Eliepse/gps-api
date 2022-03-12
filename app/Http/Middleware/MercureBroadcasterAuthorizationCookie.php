<?php

namespace App\Http\Middleware;

use App\Models\MercureSubscriberInterface;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;

class MercureBroadcasterAuthorizationCookie
{
	public function handle(Request $request, Closure $next)
	{
		/** @var Response $response */
		$response = $next($request);

		if (! method_exists($response, 'withCookie')) {
			return $response;
		}

		return $response->withCookie($this->createCookie($request->user(), $request->secure()));
	}


	private function createCookie(MercureSubscriberInterface $user, bool $secure)
	{
		// Add topic(s) this user has access to
		// This can also be URI Templates (to match several topics), or * (to match all topics)
		$subscriptions = [
			"*",
		];

		$jwtConfiguration = Configuration::forSymmetricSigner(
			new Sha256(),
			InMemory::plainText(config('broadcasting.connections.mercure.secret'))
		);

		$token = $jwtConfiguration->builder()
			->withClaim('mercure', [
				'subscribe' => $subscriptions,
				'publish' => ['*'],
				"payload" => [
					"type" => get_class($user),
					"name" => $user->getMercureName(),
				],
			])
			->getToken($jwtConfiguration->signer(), $jwtConfiguration->signingKey())
			->toString();

//		Log::channel("stderr")->debug($jwtConfiguration->signingKey()->contents());
//		Log::debug("Created Mercure token for: $user->id", ["token" => $token, "key" => config('broadcasting.connections.mercure.secret')]);

		return cookie()->make(
			'mercureAuthorization',
			$token,
			15,
			'/.well-known/mercure', // or which path you have mercure running
			parse_url(config('app.url'), PHP_URL_HOST),
			$secure,
			true
		);
	}
}