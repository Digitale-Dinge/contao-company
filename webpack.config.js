const Encore = require('@symfony/webpack-encore');

if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    .setOutputPath('./public/')
    .setPublicPath('/bundles/digitaledingecompany/')
    .setManifestKeyPrefix('')

    .cleanupOutputBeforeBuild()
    .disableSingleRuntimeChunk()

    .addEntry('opening-times', './assets/opening-times.js')
    .addStyleEntry('company-bundle', './assets/styles/company-bundle.pcss')

    .enablePostCssLoader()
    .enableVersioning(Encore.isProduction());

module.exports = Encore.getWebpackConfig();
