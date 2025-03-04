const defaultTheme = require("tailwindcss/defaultTheme");

module.exports = {
	content: [
		"./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
		"./storage/framework/views/*.php",
		"./resources/**/*.{js,css,scss}",
		"./resources/views/**/*.blade.php",
	],
	theme: {
		extend: {
			fontFamily: {
				sans: ["Nunito", ...defaultTheme.fontFamily.sans],
			},
		},
	},

	plugins: [require("@tailwindcss/forms")],
};
