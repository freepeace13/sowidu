const path = require('path');
const CircularDependencyPlugin = require('circular-dependency-plugin');

module.exports = {
  webpackFinal: (config) => {
    return {
      ...config,
      devServer: {
        watchOptions: {
          poll: true
        }
      },
      module: {
        ...config.module,
        rules: [
          ...config.module.rules,
          {
            test: /\.js$/,
            loader: 'babel-loader',
            // exclude: path.resolve(__dirname, '../resources/assets/js/services/models')
          },
          {
            test: /\.scss$/,
            use: [
              'style-loader',
              'css-loader',
              {
                loader: 'sass-loader',
                options: {
                  implementation: require('node-sass')
                }
              }
            ],
          },
          {
            test: /\.styl$/,
            use: [
              'style-loader',
              'css-loader',
              'stylus-loader'
            ],
          },
          // {
          //   test: /\.css$/,
          //   loader: 'postcss-loader',
          //   include: path.resolve(__dirname, "../.storybook")
          // }
        ]
      },
      resolve: {
        ...config.resolve,
        alias: {
          ...config.resolve.alias,
          '~': path.join(__dirname, '../resources/assets/js'),
          '@': path.join(__dirname, './resources/assets/js/components')
        }
      },
      plugins: [
        ...config.plugins,
        new CircularDependencyPlugin({
          // exclude detection of files based on a RegExp
          exclude: /a\.js|node_modules/,
          // add errors to webpack instead of warnings
          failOnError: false,
          // allow import cycles that include an asyncronous import,
          // e.g. via import(/* webpackMode: "weak" */ './file.js')
          allowAsyncCycles: false,
          // set the current working directory for displaying module paths
          cwd: process.cwd(),
        })
      ]
    };
  }
};