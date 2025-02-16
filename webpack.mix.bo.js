const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
*/

// back office assets
mix.sass('resources/style/back-office/app.scss', 'public/css/back-office', {}, [
	require('tailwindcss'),
]).options({
	postCss: [require('autoprefixer')],
});

mix.js('resources/js/back-office/app.js', 'public/js/back-office').react().version();

mix.webpackConfig({
	stats: "minimal",
})

mix.extract();

mix.version()

mix.sourceMaps(false)