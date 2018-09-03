var Encore = require('@symfony/webpack-encore');

const CopyWebpackPlugin = require('copy-webpack-plugin');

Encore
    // directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // public path used by the web server to access the output path
    .setPublicPath('/build')
    // only needed for CDN's or sub-directory deploy
    //.setManifestKeyPrefix('build/')

    // fixes modules that expect jQuery to be global
    .autoProvidejQuery()
    .addPlugin(new CopyWebpackPlugin([
        // copies to {output}/static
        { from: './assets/static', to: 'static' }
    ]))
    .enableSassLoader()
    /*
     * ENTRY CONFIG
     *
     * Add 1 entry for each "page" of your app
     * (including one that's included on every page - e.g. "app")
     *
     * Each entry will result in one JavaScript file (e.g. app.js)
     * and one CSS file (e.g. app.css) if you JavaScript imports CSS.
     */
    .createSharedEntry('bse_layout', './assets/js/bse/layout.js')
    .addEntry('bse/enderecoForm', './assets/js/bse/enderecoForm.js')

    .addEntry('fis/emissaoNFe/form', './assets/js/fis/emissaoNFe/form.js')

    .addEntry('crm/clienteList', './assets/js/crm/clienteList.js')
    .addEntry('crm/clienteForm', './assets/js/crm/clienteForm.js')

    .addEntry('fin/carteiraList', './assets/js/fin/carteiraList.js')
    .addEntry('fin/bancoList', './assets/js/fin/bancoList.js')
    .addEntry('fin/grupoList', './assets/js/fin/grupoList.js')
    .addEntry('fin/centroCustoList', './assets/js/fin/centroCustoList.js')
    .addEntry('fin/modoList', './assets/js/fin/modoList.js')
    .addEntry('fin/bandeiraCartaoList', './assets/js/fin/bandeiraCartaoList.js')
    .addEntry('fin/operadoraCartaoList', './assets/js/fin/operadoraCartaoList.js')
    .addEntry('fin/registroConferenciaList', './assets/js/fin/registroConferenciaList.js')
    .addEntry('fin/regraImportacaoLinhaList', './assets/js/fin/regraImportacaoLinhaList.js')


    /*
     * FEATURE CONFIG
     *
     * Enable & configure other features below. For a full
     * list of features, see:
     * https://symfony.com/doc/current/frontend.html#adding-more-features
     */
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    // enables hashed filenames (e.g. app.abc123.css)
    .enableVersioning(Encore.isProduction())

    // enables Sass/SCSS support
    //.enableSassLoader()

    // uncomment if you use TypeScript
    //.enableTypeScriptLoader()

    // uncomment if you're having problems with a jQuery plugin
    //.autoProvidejQuery()
;

module.exports = Encore.getWebpackConfig();
