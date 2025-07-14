process.env.DISABLE_NOTIFIER = true;
var elixir = require('laravel-elixir');
require('laravel-elixir-vue');
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

elixir(function(mix) {
    mix.sass(['app.scss', 'sweetalert.scss', 'bootstrap-select.scss', 'custom.scss'], './public/css/app.css')
     .sass('splash.scss', './public/css/splash.css')
    	.sass('query-builder.scss', './public/css/query-builder.css')
          .sass('dashboard.scss', './public/css/dashboard.css')
    	.sass('variables.scss', './public/css/variables.css')
          .sass('datetimepicker.scss', './public/css/datetimepicker.css')
     .styles('node_modules/admin-lte/bootstrap/css/bootstrap.css', 'public/css/bootstrap.css')
     .scripts('./node_modules/bootstrap-sass/assets/javascripts/bootstrap.js', './public/js/bootstrap.js')
     .scripts('./resources/assets/js/query-builder.js', './public/js/query-builder.js')
     .scripts('./node_modules/dot/doT.min.js', './public/js/dot.js')
      .scripts('./resources/assets/js/dropzone.js', './public/js/dropzone.js')
      .scripts('./resources/assets/js/extendext.js', './public/js/extendext.js')
      .scripts('./resources/assets/js/sweetalert.js', './public/js/sweetalert.js')
      .scripts('./resources/assets/js/bootstrap-select.js', './public/js/bootstrap-select.js')
      .scripts('./resources/assets/js/vue/add-subcategory.js', './public/js/add-subcategory.js')
      .scripts('./resources/assets/js/datetimepicker.min.js', './public/js/datetimepicker.min.js')
      .scripts('./resources/assets/js/moment.js', './public/js/moment.js')
      .scripts('./resources/assets/js/lync-presence.js', './public/js/lync-presence.js')
      .less('./node_modules/admin-lte/build/less/AdminLTE.less', './public/css/adminlte.css')
     .less('./node_modules/admin-lte/build/less/skins/_all-skins.less', './public/css/skins.css')
     .scripts(['./node_modules/admin-lte/dist/js/app.js', 
               './node_modules/admin-lte/plugins/datepicker/bootstrap-datepicker.js',
               './node_modules/admin-lte/plugins/select2/select2.js',
               './node_modules/moment/moment.js'], 'public/js/adminlte.js')
     .version(['css/adminlte.css', 'css/app.css', 'css/skins.css']);

});


/*

 mix.sass( ['partials/*.scss','app.scss'] )
       .scripts([
       		'libs/sweetalert.min.js'
       	], './public/js/libs.js')
       .styles([
       		'libs/sweetalert.css'
       	], './public/css/libs.css')

*/