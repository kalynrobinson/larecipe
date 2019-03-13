const { mix } = require('laravel-mix');

mix.options({processCssUrls: false, publicPath: "./" })
	.sass('resources/sass/app.scss', 'publishable/assets/css')
	.js('resources/js/app.js', 'publishable/assets/js')
	.sourceMaps()
	.copy('publishable/assets', '../reactive-docs/public/vendor/binarytorch/larecipe/assets');