const mix = require('laravel-mix')
require('laravel-mix-merge-manifest')

if (process.env.section) {
	require(`${__dirname}/webpack.mix.${process.env.section}.js`)
}

mix.mergeManifest()
