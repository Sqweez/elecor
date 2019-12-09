const mix = require('laravel-mix');
const path = require('path');
const ExtractTextPlugin = require('extract-text-webpack-plugin');
const MomentLocalesPlugin = require('moment-locales-webpack-plugin');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const devMode = process.env.NODE_ENV !== 'production';
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
    .sass('resources/sass/app.scss', 'public/css')
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
    });
