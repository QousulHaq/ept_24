const mix = require('laravel-mix');

mix.sass('resources/style/client/editor.scss', 'public/css/pre/editor.css', {}, [
	require('tailwindcss'),
]).options({
	postCss: [require('autoprefixer')],
})

mix.js('resources/js/client/main.jsx', 'public/js')
	.react()
	.webpackConfig({
		stats: "minimal",
	})
	.options({
		processCssUrls: false
	});

mix.sourceMaps(false)

mix.version()

