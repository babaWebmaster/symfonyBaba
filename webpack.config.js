const Encore = require('@symfony/webpack-encore');
const fs = require('fs');
const path = require('path');

if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    // Dossiers de sortie
    .setOutputPath('public/build/')
    .setPublicPath('/build')
    .cleanupOutputBeforeBuild()

    // Stimulus & jQuery
    .enableStimulusBridge('./assets/controllers.json')
    .autoProvidejQuery()

    // Optimisations
    .splitEntryChunks()
    .enableSingleRuntimeChunk()

    // Entrées JS
    .addEntry('app', './assets/app.js')
    .addEntry('frontJs', './assets/front.js')

    // Styles globaux (avec HMR)
    .addEntry('appFront', './assets/styles/front/appFront.scss')
    .addEntry('login', './assets/styles/admin/login.scss')
    .addEntry('reset', './assets/styles/reset_password/reset.scss')
    .addEntry('adminGallery', './assets/styles/admin/admin-gallery.scss')

    // Loaders
    .enableSassLoader()
    .enablePostCssLoader()
    .enableSourceMaps(!Encore.isProduction())
    .configureBabelPresetEnv(config => {
        config.useBuiltIns = 'usage';
        config.corejs = '3.38';
    })

    // DevServer
    .configureDevServerOptions(options => {
        options.hot = true; // ✅ HMR activé
        options.liveReload = true; // désactive le rechargement complet
        options.client = { overlay: false }; // pas d’overlay
        options.port = 8080;
        options.static = { directory: path.resolve(__dirname, 'public') };
        options.proxy = { '/': { target: 'http://127.0.0.1:8000', changeOrigin: true } };
        options.watchFiles = {
        paths: ['templates/**/*.twig', 'assets/**/*'],
        options: {
            usePolling: true,
        },
        };
    });
    

/**
 * === SCSS PAR PAGE ===
 * On les compile séparément pour que Symfony puisse les inclure.
 * On ne désactive PAS l’extraction CSS pour ces fichiers.
 */
const frontDir = path.resolve(__dirname, 'assets/styles/front/pages');
if (fs.existsSync(frontDir)) {
    fs.readdirSync(frontDir).forEach(file => {
        if (file.endsWith('.scss')) {
            const name = file.replace('.scss', '');
            Encore.addStyleEntry(name, path.join(frontDir, file));
        }
    });
}

// === Gestion de la versioning & CSS extraction ===
if (Encore.isProduction()) {
    Encore.enableVersioning();
} else {
    // ⚡️ On garde l'extraction pour les CSS par page,
    // mais HMR agit toujours sur les CSS globaux.
    // Rien à désactiver ici.
}

module.exports = Encore.getWebpackConfig();
