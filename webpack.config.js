const path = require('path');
const Encore = require('@symfony/webpack-encore');

const pluginName = 'fcm';
// eslint-disable-next-line no-shadow
const getConfig = (pluginName, type) => {
  Encore.reset();

  Encore.setOutputPath(`public/build/codebuds/${pluginName}/${type}/`)
    .setPublicPath(`/build/codebuds/${pluginName}/${type}/`)
    .addEntry(`codebuds-${pluginName}-${type}`, path.resolve(__dirname, `./src/Resources/assets/${type}/entry.js`))
    .disableSingleRuntimeChunk()
    .cleanupOutputBeforeBuild()
    .enableSourceMaps(!Encore.isProduction())
    .enableSassLoader();

  const config = Encore.getWebpackConfig();
  config.name = `codebuds-${pluginName}-${type}`;

  return config;
};

Encore.setOutputPath('src/Resources/public/build/')
  .setPublicPath('/public/build/')
  .addEntry(`codebuds-${pluginName}-admin`, path.resolve(__dirname, './src/Resources/assets/admin/entry.js'))
  .cleanupOutputBeforeBuild()
  .disableSingleRuntimeChunk()
  .enableSassLoader();

const distConfig = Encore.getWebpackConfig();
distConfig.name = 'codebuds-plugin-dist';

Encore.reset();

const adminConfig = getConfig(pluginName, 'admin');

module.exports = [adminConfig, distConfig];
