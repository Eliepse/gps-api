<?php

namespace App\Http\Csp;

use Spatie\Csp\Directive;
use Spatie\Csp\Keyword;

class CustomPolicy extends \Spatie\Csp\Policies\Basic
{
	public function configure()
	{
		$this
			->addDirective(Directive::BASE, Keyword::SELF)
			->addDirective(Directive::CONNECT, Keyword::SELF)
			->addDirective(Directive::DEFAULT, Keyword::SELF)
			->addDirective(Directive::DEFAULT, "http://localhost:8080")
			->addDirective(Directive::DEFAULT, "https://fonts.gstatic.com")
			->addDirective(Directive::DEFAULT, "wss://*.pusher.com")
			->addDirective(Directive::FORM_ACTION, Keyword::SELF)
			->addDirective(Directive::IMG, Keyword::SELF)
			->addDirective(Directive::IMG, "*.openstreetmap.fr")
			->addDirective(Directive::OBJECT, Keyword::NONE)
			->addDirective(Directive::SCRIPT, Keyword::SELF)
			->addDirective(Directive::STYLE, Keyword::SELF)
			->addDirective(Directive::STYLE, "http://localhost:8080")
			->addDirective(Directive::STYLE, "https://fonts.googleapis.com")
			->addDirective(Directive::STYLE, Keyword::UNSAFE_INLINE)
			->addDirective(Directive::STYLE, Keyword::UNSAFE_EVAL)
			->addDirective(Directive::SCRIPT, "http://localhost:8080")
			->addDirective(Directive::SCRIPT, Keyword::UNSAFE_EVAL)
			->addDirective(Directive::SCRIPT, Keyword::UNSAFE_INLINE)
			->addDirective(Directive::CONNECT, "ws://localhost:8080")
			->addDirective(Directive::CONNECT, "wss://192.168.1.35:6001")
			->addDirective(Directive::CONNECT, 'wss://*.pusher.com')
			->addDirective(Directive::SCRIPT, 'wss://*.pusher.com');
	}
}