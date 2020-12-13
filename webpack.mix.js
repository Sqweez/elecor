const mix = require('laravel-mix');
const path = require('path');
const MomentLocalesPlugin = require('moment-locales-webpack-plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');

mix.js('resources/js/app.js', 'public/js')
    .sass('resources/js/styles/app.scss', 'public/css')
    .webpackConfig({
        plugins: [
            new MomentLocalesPlugin(),
            new MiniCssExtractPlugin()
        ],
        resolve: {
            alias: {
                '@': path.resolve(__dirname, 'resources/js')
            }
        }
    })
    .options({
        extractVueStyles: false,
        processCssUrls: false
    });

if (mix.inProduction()) {
    mix.version();

}
