let mix = require('laravel-mix');

mix.setPublicPath('public');

mix.autoload({
    'jquery': ['$', 'window.jQuery',"jQuery","window.$","jquery","window.jquery"]
});

mix.extract(['vue', 'jquery']);

mix.js('resources/assets/js/app.js', 'public/web/js');

mix.js('resources/assets/js/frame.js', 'public/web/js');

mix.js('resources/assets/js/gameapp.js', 'public/web/js');

mix.styles([
    'resources/assets/css/app.css',
], 'public/css/app.css');

mix.styles([
    'resources/assets/css/game/bet.css',
], 'public/css/game.css');

if (mix.inProduction()) {
    mix.version();
    mix.disableNotifications();
}