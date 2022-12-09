import path from 'path'
import webpack from 'webpack'
import config from '../config'

let webpackConfig = {
  entry: {
    vendors: [path.join(__dirname, 'vendors/vendors.js')]
  },
  output: {
    path: config.path_dist,
    filename: 'cw-[name].js',
    library: '[name]'
  },
  plugins: [
    new webpack.DefinePlugin(config.globals),
    new webpack.DllPlugin({
      path: path.join(config.path_dist, 'cw-[name]-manifest.json'),
      name: '[name]',
      context: path.resolve(__dirname, '../app')
    })
  ],
  resolve: {
    root: path.resolve(__dirname, '../app'),
    modulesDirectories: ['node_modules']
  }
}

if (process.env.NODE_ENV === 'production') {
  webpackConfig.plugins.push(
    new webpack.optimize.OccurrenceOrderPlugin(),
    new webpack.optimize.DedupePlugin(),
    new webpack.optimize.UglifyJsPlugin({
      compress: {
        unused: true,
        dead_code: true
      }
    })
  )
}

const compiler = webpack(webpackConfig)
compiler.run((err, stats) => {
  console.log(stats.toString({
    chunks: false,
    chunkModules: false,
    colors: true
  }))
  console.log(err)
})
