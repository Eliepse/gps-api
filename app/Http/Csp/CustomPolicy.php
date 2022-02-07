<?php

namespace App\Http\Csp;

use Spatie\Csp\Directive;
use Spatie\Csp\Keyword;

class CustomPolicy extends \Spatie\Csp\Policies\Basic
{
	public function configure()
	{
		$this
			// Part of default
			->addDirective(Directive::BASE, Keyword::SELF)
			->addDirective(Directive::CONNECT, Keyword::SELF)
			->addDirective(Directive::DEFAULT, Keyword::SELF)
			->addDirective(Directive::FORM_ACTION, Keyword::SELF)
			->addDirective(Directive::IMG, Keyword::SELF)
			->addDirective(Directive::MEDIA, Keyword::SELF)
			->addDirective(Directive::OBJECT, Keyword::NONE)
			->addDirective(Directive::SCRIPT, Keyword::SELF)
			->addDirective(Directive::STYLE, Keyword::SELF)
			// Custom
			->addDirective(Directive::DEFAULT, [
				"ws: wss:",
				"localhost:8080",
				"https://fonts.gstatic.com https://fonts.googleapis.com",
				"*.pusher.com",
			])
			->addDirective(Directive::IMG, ["*.openstreetmap.fr"])
			->addDirective(Directive::CONNECT, ["ws: wss: *.pusher.com"])
			->addDirective(Directive::STYLE, [
				Keyword::UNSAFE_EVAL,
				Keyword::UNSAFE_INLINE,
				"https://fonts.gstatic.com https://fonts.googleapis.com",
			])
			->addDirective(Directive::SCRIPT, [
				Keyword::UNSAFE_EVAL,
				Keyword::UNSAFE_INLINE,
			]);
	}
}