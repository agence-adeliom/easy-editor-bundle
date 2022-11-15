// webpack.mix.js

let mix = require('laravel-mix');
let webpack = require('webpack');
let tailwindcss = require('tailwindcss');
require('laravel-mix-polyfill');

mix.setPublicPath("src/Resources/public");
mix.js('assets/js/field-editor.js', '');
mix.sass('assets/scss/easy-editor.scss', '');

mix.polyfill();

mix.options({
    postCss: [ tailwindcss('./tailwind.config.js') ],
})

mix.webpackConfig({
    output: {
        publicPath: 'bundles/easyeditor/',
    },
    plugins: [
        // fix ReferenceError: Buffer/process is not defined
        new webpack.ProvidePlugin({
            process : 'process/browser',
            Buffer  : ['buffer', 'Buffer']
        })
    ]
})
