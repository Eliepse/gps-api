<?php

namespace App\Mercure;

use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;

class MercureManager
{
	private string $hubUrl;
	private string $hubHost;
	private string $hubPath;
	private bool $hubHttps;

	private Configuration $jwtConfig;


	public function __construct()
	{
		$this->hubUrl = config("broadcasting.connections.mercure.url");
		$parsedUrl = parse_url($this->hubUrl);
		$this->hubHost = $parsedUrl["host"];
		$this->hubPath = $parsedUrl["path"];
		$this->hubHttps = $parsedUrl["scheme"] === "https";

		$this->jwtConfig = Configuration::forSymmetricSigner(
			new Sha256(),
			InMemory::plainText(config('broadcasting.connections.mercure.secret'))
		);
	}


	/**
	 * Generate a cookie with the authorization token included for the Mercure Hub.
	 *
	 * @param string[] $subscribeTopics - Allowed topics to subscribe to
	 * @param string[] $publishTopics - Allowed topics to publish on
	 * @param array|null $payload - Additionnal data to pass for subscription events and api
	 *
	 * @throws BindingResolutionException
	 */
	public function makeCookieToken(array $subscribeTopics, array $publishTopics = [], ?array $payload = null)
	{
		return cookie()->make(
			name: 'mercureAuthorization',
			value: $this->makeToken($subscribeTopics, $publishTopics, $payload),
			minutes: 15,
			domain: $this->hubHost,
			path: $this->hubPath,
			secure: $this->hubHttps,
			httpOnly: true,
		);
	}


	/**
	 * Generate a authorization token for the Mercure Hub.
	 *
	 * @param string[] $subscribeTopics - Allowed topics to subscribe to
	 * @param string[] $publishTopics - Allowed topics to publish on
	 * @param array|null $payload - Additionnal data to pass for subscription events and api
	 *
	 * @return string
	 */
	public function makeToken(array $subscribeTopics, array $publishTopics = [], ?array $payload = null): string
	{
		$tokenContent = ['subscribe' => $subscribeTopics, 'publish' => $publishTopics];

		// We only add a payload if it is set
		if (! is_null($payload)) {
			$tokenContent["payload"] = $payload;
		}

		return $this->jwtConfig->builder()
			->withClaim('mercure', $tokenContent)
			->getToken($this->jwtConfig->signer(), $this->jwtConfig->signingKey())
			->toString();
	}


	/**
	 * Make a token that authorize subscribing and publishing on all topics.
	 *
	 * @param array|null $payload - Additionnal data to pass for subscription events and api
	 *
	 * @return string
	 */
	public function makeSudoToken(?array $payload = null): string
	{
		return $this->makeToken(["*"], ["*"], $payload);
	}


	/**
	 * Makes a request to the hub to get the active subscriber of a topic.
	 * If no topic is specified, all subscriptions are returned.
	 *
	 * @param string|null $topic - The topic to check, all topic when not set.
	 *
	 * @return array
	 */
	public function fetchSubscriptions(?string $topic = null): array
	{
		$url = "$this->hubUrl/subscriptions";

		if (! is_null($topic)) {
			$url .= "/" . urlencode($topic);
		}

		try {
			$response = Http::withHeaders(["Authorization" => "Bearer {$this->makeSudoToken()}"])->get($url);
			$subscriptions = $response->json()["subscriptions"];
		} catch (Exception $e) {
			Log::error("Could not connect to the Mercure Hub", ["message" => $e->getMessage()]);
			$subscriptions = [];
		}

		return $subscriptions;
	}
}