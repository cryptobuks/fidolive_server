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

mix.js('resources/js/app.js', 'public/js')
	.extract(['vue'])

mix.sass('resources/sass/main.scss', 'public/css')
	.sass('resources/sass/style.scss', 'public/css')
	.sass('resources/sass/view.scss', 'public/css')

if (!mix.inProduction()) {
	mix.sourceMaps()
} else {
	mix.version()
}