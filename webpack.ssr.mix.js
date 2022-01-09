const path = require('path')
const mix = require('laravel-mix')

mix
    .js('resources/js/ssr.js', 'public/js')
    .options({
        manifest: false
    })
    .vue({
        version: 3,
        useVueStyleLoader: true,
        options: {
            optimizeSSR: true
        }
    })
    .alias({
        '@': path.resolve('resources/js')
    })
    .webpackConfig({
        target: 'node',
        externals: {
            node: true,
            // Sidecar will ship a file called compiledZiggy as a part of
            // the package. We don't want webpack to try to inline it
            // because it doesn't exist at the time webpack runs.
            './compiledZiggy': 'require("./compiledZiggy")'
        },
        resolve: {
            alias: {
                ziggy: path.resolve('vendor/tightenco/ziggy/src/js'),
            },
        },
    })
