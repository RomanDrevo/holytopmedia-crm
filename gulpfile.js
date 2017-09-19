const elixir = require('laravel-elixir');

require('laravel-elixir-vue-2');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir(mix => {
    mix.sass('app.scss')
       .webpack('app.js');

    mix.styles([
        "./public/css/bootstrap.min.css",
        "./public/css/font-awesome.min.css",
        "./public/css/datepicker.css",
        "./public/css/dataTables.bootstrap.css",
        "./public/css/select2.min.css",
        "./public/css/metisMenu.min.css",
        "./public/bower_components/sweetalert2/dist/sweetalert2.min.css",
        "./public/bower_components/toastr/toastr.min.css",
        "./public/css/main-admin.css"
    ], './public/css/main-app.css');

    mix.scripts([
        "./public/js/jquery.min.js",
        "./public/js/bootstrap.min.js",
        "./public/js/select2.full.min.js",
        "./public/js/bootstrap-datepicker.js",
        "./public/bower_components/sweetalert2/dist/sweetalert2.min.js",
        "./public/bower_components/toastr/toastr.min.js",
        "./public/js/jquery.dataTables.min.js",
        "./public/js/metisMenu.min.js",
        "./public/js/main-admin.js",
        "./public/js/main.js"
    ], './public/js/main-app.js');
});
