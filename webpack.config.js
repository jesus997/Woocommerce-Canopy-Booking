var ExtractTextPlugin = require('extract-text-webpack-plugin'),
    path = require('path'),
    debug = process.env.NODE_ENV !== "production",
    webpack = require('webpack');

module.exports = {
  devtool: debug ? "inline-sourcemap" : null,
  entry: {
    './admin/css/wcb-admin': './assets/css/admin/main.scss',
    './admin/js/wcb-admin': './assets/js/admin/main.js',
    './public/css/wcb-public': './assets/css/public/main.scss',
    './public/js/wcb-public': './assets/js/public/main.js'
  },
  output: {
    path: path.join(__dirname, './'),
    filename: '[name].js'
  },
  externals: {
    jquery: 'jQuery'
  },
  module: {
    rules: [
      /*
      your other rules for JavaScript transpiling go in here
      */
      { // css / sass / scss loader for webpack
        test: /\.(css|sass|scss)$/,
        use: ExtractTextPlugin.extract({
          use: ['css-loader', 'sass-loader'],
        })
      }
    ]
  },
  plugins: debug ? [
    new ExtractTextPlugin({ // define where to save the file
      filename: '[name].css',
      allChunks: true,
    }),
  ] : [
    new ExtractTextPlugin("[name].css"),
    new webpack.DefinePlugin({
        'process.env':{
            'NODE_ENV': JSON.stringify('production')
        }
    }),
    new webpack.optimize.UglifyJsPlugin({
        compress:{
            warnings: true
        }
    })
  ],
};