const Encore = require('@symfony/webpack-encore');

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    // directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // public path used by the web server to access the output path
    .setPublicPath('/build')
    // only needed for CDN's or sub-directory deploy
    //.setManifestKeyPrefix('build/')

    /*
     * ENTRY CONFIG
     *
     * Each entry will result in one JavaScript file (e.g. app.js)
     * and one CSS file (e.g. app.css) if your JavaScript imports CSS.
     */
    .addEntry('app', './assets/app.js')
    .addEntry('nav-responsive','./assets/javascript/nav-responsive.js')
    .addEntry('flip-card','./assets/javascript/flip-card.js')
    .addEntry('restaurant','./assets/javascript/restaurant.js')
    .addEntry('time','./assets/javascript/time.js')
    .addEntry('active-lateral','./assets/javascript/active-lateral.js')
    .addEntry('splide','./assets/splide.js')
    .addEntry('create-file','./assets/javascript/createFile.js')
    .addEntry('create-plat','./assets/javascript/createPlat.js')
    .addEntry('profil-action','./assets/javascript/profil-action.js')
    .addEntry('avatar-js','./assets/javascript/avatar.js')
    .addEntry('fontAwesome','./assets/fontAwesome.js')

    
    .copyFiles({
        from: './assets/images',
        pattern: /\.(png|jpg|jpeg|svg)$/,
        // le chemin ou je veux mettres mes images
        to: 'images/[path][name].[ext]'
    } )
    .copyFiles({
        from: './assets/javascript',
        pattern: /\.(js|jsx)$/,
        // le chemin ou je veux mettres mes images
        to: 'javascript/[path][name].[ext]'
    } )

    // enables the Symfony UX Stimulus bridge (used in assets/bootstrap.js)
    .enableStimulusBridge('./assets/controllers.json')

    // When enabled, Webpack "splits" your files into smaller pieces for greater optimization.
    .splitEntryChunks()

    // will require an extra script tag for runtime.js
    // but, you probably want this, unless you're building a single-page app
    .enableSingleRuntimeChunk()

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

    .configureBabel((config) => {
        config.plugins.push('@babel/plugin-proposal-class-properties');
    })

    // enables @babel/preset-env polyfills
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = 3;
    })

    // enables Sass/SCSS support
    .enableSassLoader()
    .enablePostCssLoader()

    // uncomment if you use TypeScript
    //.enableTypeScriptLoader()

    // uncomment if you use React
    //.enableReactPreset()

    // uncomment to get integrity="..." attributes on your script & link tags
    // requires WebpackEncoreBundle 1.4 or higher
    //.enableIntegrityHashes(Encore.isProduction())

    // uncomment if you're having problems with a jQuery plugin
    //.autoProvidejQuery()
;

module.exports = Encore.getWebpackConfig();
