const mix = require('laravel-mix');
const path = require('path');
mix.setPublicPath(path.join(__dirname, ""));
mix.disableNotifications();

// mix.js('resources/js/app.js', 'js')
//    .sass('resources/sass/app.scss', 'css').version();

mix.scripts([
   'resources/js/app.js'
], 'public/js/app.js').version();

mix.styles([
   'resources/styles/app.css'
], 'public/css/app.css').version();