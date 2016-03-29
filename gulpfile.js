var elixir = require('laravel-elixir');

elixir.config.assetsPath = '';
elixir.config.sourcemaps = false;

elixir(function(mix) {
    mix.sass(['admin.scss'], 'css/admin.css');
    mix.sass(['light.scss'], 'css/light.css');
    mix.sass(['dark.scss'], 'css/dark.css');
});