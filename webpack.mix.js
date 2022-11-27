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
    .js('resources/js/jquery.magnific-popup.min.js', 'public/js')
    .js('resources/js/masonry.pkgd.min.js', 'public/js')
    .js('resources/js/jquery.barfiller.js', 'public/js')
    .js('resources/js/jquery.slicknav.js', 'public/js')
    .js('resources/js/main.js', 'public/js')
    .js('resources/js/owl.carousel.min.js', 'public/js')
    .js('resources/js/adminApp.js', 'public/js')
    .copy('resources/img/', 'public/images', false)
    .copy('resources/fonts/', 'public/fonts', false)
    .vue();
    // .sass('resources/sass/app.scss', 'public/css');
    // .postCss('resources/css/app.css', 'public/css', [
    //     //
    // ]);
    // .postCss('resources/css/adminApp.css', 'public/css', [
    //     //
    // ]);
