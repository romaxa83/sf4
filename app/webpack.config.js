var Encore = require('@symfony/webpack-encore');

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')
    .cleanupOutputBeforeBuild()
    .enableSourceMaps(!Encore.isProduction())
    .addEntry('js/app',[
        './node_modules/jquery/dist/jquery.slim.js',
        './node_modules/popper.js/dist/popper.min.js',
        './node_modules/bootstrap/dist/js/bootstrap.min.js',
        './node_modules/holderjs/holder.min.js'
    ])
    .addStyleEntry('css/app',[
        './node_modules/bootstrap/dist/css/bootstrap.min.css',
        './assets/css/app.css'
    ])
;

module.exports = Encore.getWebpackConfig();