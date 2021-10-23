let mix = require('laravel-mix');

mix.autoload({
    jquery: ['$', 'window.jQuery', 'jQuery']
});

mix.js('vue-src/main.js', 'assets/js/main.js').vue({ version: 3 });