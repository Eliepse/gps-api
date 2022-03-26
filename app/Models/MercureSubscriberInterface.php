<?php

namespace App\Models;

interface MercureSubscriberInterface
{
	public function getMercureType(): string;


	public function getMercureId(): int|string;


	public function getMercureName(): string;


	public function getMercurePayload(): array;
}