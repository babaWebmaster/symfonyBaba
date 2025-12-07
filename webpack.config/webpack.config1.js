const Encore = require('@symfony/webpack-encore');
const fs = require('fs');
const path = require('path');

if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')
    .cleanupOutputBeforeBuild()
    .enableStimulusBridge('./assets/controllers.json')
    .autoProvidejQuery()
    .splitEntryChunks()
    .enableSingleRuntimeChunk()

    // === JS ===
    .addEntry('app', './assets/app.js')
    .addEntry('frontJs', './assets/front.js')

    // === SCSS fixes ===
    .addEntry('login', './assets/styles/admin/login.scss')
    .addEntry('reset', './assets/styles/reset_password/reset.scss')
    .addEntry('appFront', './assets/styles/appFront.scss')
    .addEntry('adminGallery', './assets/styles/admin/admin-gallery.scss')

    // === Sass loader obligatoire pour compiler les SCSS ===
    .enableSassLoader()
    .enablePostCssLoader()
    .enableSourceMaps(!Encore.isProduction())
;

// === Ajout dynamique des SCSS front ===
const frontDir = path.resolve(__dirname, 'assets/styles/front');
if (fs.existsSync(frontDir)) {
    fs.readdirSync(frontDir).forEach(file => {
        if (file.endsWith('.scss')) {
            const name = file.replace('.scss', '');
            Encore.addStyleEntry(name, path.join(frontDir, file));
        }
    });
}

// --- Dev-server simple (reload complet) ---
Encore.configureDevServerOptions(options => {
    options.hot = false;          // HMR désactivé
    options.liveReload = true;    // reload complet
    options.port = 8080;
    options.static = {
        directory: path.resolve(__dirname, 'public'),
    };
    options.proxy = {
        '/': {
            target: 'http://127.0.0.1:8000',
            changeOrigin: true,
        },
    };
    options.client = {
        overlay: false
    };
});

// --- Versioning en prod ---
if (Encore.isProduction()) {
    Encore.enableVersioning();
}

module.exports = Encore.getWebpackConfig();
