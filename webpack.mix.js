const path = require("path");
const mix = require("laravel-mix");
const tailwindcss = require("tailwindcss");

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

mix
	.js("resources/js/app.js", "public/js")
	.alias({
		components: path.join(__dirname, "resources/js/components"),
		pages: path.join(__dirname, "resources/js/pages"),
		store: path.join(__dirname, "resources/js/store"),
	})
	.react()
	.sass("resources/css/app.scss", "public/css")
	.options({
		postCss: [tailwindcss()],
	});

if (mix.inProduction()) {
	mix.version();
} else {
	mix.sourceMaps();
	mix.disableSuccessNotifications();
}
