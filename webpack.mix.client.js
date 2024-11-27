const mix = require('laravel-mix');

mix.less('resources/style/client/app.less', 'public/css/pre/app.css')
	.sass('resources/style/client/editor.scss', 'public/css/pre/editor.css')
	.combine(['public/css/pre/app.css', 'public/css/pre/editor.css'], 'public/css/app.css')
	.js('resources/js/client/main.js', 'public/js')
	.vue()
	.webpackConfig({
		stats: "minimal",
		module: {
			rules: [{
				test: /\.less$/,
				use: [
					{
						loader: "less-loader",
						options: {
							lessOptions: {
								javascriptEnabled: true,
							}
						}
					}
				]
			}]
		},
	})
	.options({
		processCssUrls: false
	});

mix.sourceMaps(false)

mix.version()

