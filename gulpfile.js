var elixir = require('laravel-elixir');

elixir.config.assetsPath = '';
elixir.config.sourcemaps = false;

elixir(function(mix) {
    mix.sass(['light.scss'], 'css/light.css');
    mix.sass(['dark.scss'], 'css/dark.css');
});
