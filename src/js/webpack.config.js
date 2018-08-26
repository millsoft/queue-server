// webpack v4
const path = require('path');
const ExtractTextPlugin = require('extract-text-webpack-plugin');
//const VueLoaderPlugin = require('../lib/plugin')
const VueLoaderPlugin = require('vue-loader');


module.exports = (env = {})  => {

  return {
  entry: [ './src/js/app.js'],
  output: {
    path: path.resolve(__dirname, '../../dist/js'),
    filename: 'queueapp.js',
    publicPath: ""

  },

    resolve: {
        alias: {
            'vue$': 'vue/dist/vue.esm.js'
        },
        extensions: ['*', '.js', '.vue', '.json']
    },

  module: {
    rules: [
      {
        test: /\.js$/,
        exclude: /node_modules/,
        use: {
          loader: "babel-loader"
        }
      },
      {
        test: /\.css$/,
        use: ExtractTextPlugin.extract(
          {
            fallback: 'style-loader',
            use: ['css-loader']
          })
      },
      {
          test: /\.vue$/,
          loader: 'vue-loader'
      }
    ]
  }
}

};