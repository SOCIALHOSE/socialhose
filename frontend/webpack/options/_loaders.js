import cssnano from 'cssnano'
import ExtractTextPlugin from 'extract-text-webpack-plugin'
import config from '../../config'

const isProduction = config.env === 'production'

// ------------------------------------
// Pre-Loaders
// ------------------------------------
export let preLoaders = [
  {
    test: /\.js$/,
    loader: 'eslint',
    exclude: /node_modules/
  }
]

export let eslint = {
  configFile: `${config.path_base}/.eslintrc`,
  emitWarning: !isProduction
}

// ------------------------------------
// Loaders
// ------------------------------------
let sassLoaders = isProduction
  ? ExtractTextPlugin.extract(
      'style-loader',
      'css-loader!postcss-loader!sass-loader'
    )
  : 'style-loader!css-loader!postcss-loader!sass-loader'

let cssLoaders = isProduction
  ? ExtractTextPlugin.extract('style-loader', 'css-loader')
  : 'style-loader!css-loader'

export let loaders = [
  // ES-2015
  {
    test: /\.js$/,
    exclude: /node_modules/,
    loader: 'babel',
    compact: false,
    query: {
      cacheDirectory: true,
      plugins: ['transform-runtime'],
      presets: !isProduction
        ? ['es2015', 'react', 'stage-0', 'react-hmre']
        : ['es2015', 'react', 'stage-0']
    }
  },
  // Styles
  {
    test: /\.scss$/,
    include: /app/,
    loader: sassLoaders
  },
  {
    test: /\.css$/,
    exclude: /app/,
    loader: cssLoaders
  },
  // Fonts
  {
    test: /\.woff(\?.*)?$/,
    loader: 'file?prefix=fonts/&name=[path][name].[ext]'
  },
  {
    test: /\.woff2(\?.*)?$/,
    loader: 'file?prefix=fonts/&name=[path][name].[ext]'
  },
  {
    test: /\.ttf(\?.*)?$/,
    loader: 'file?prefix=fonts/&name=[path][name].[ext]'
  },
  {
    test: /\.eot(\?.*)?$/,
    loader: 'file?prefix=fonts/&name=[path][name].[ext]'
  },
  {
    test: /\.svg(\?.*)?$/,
    loader: 'file?prefix=fonts/&name=[path][name].[ext]'
  },
  // Images
  { test: /\.(png|jpg|gif)$/, loader: 'url' },

  //json
  {
    test: /\.json$/,
    loader: 'json-loader'
  }
]

export let postcss = [
  cssnano({
    sourcemap: true,
    autoprefixer: {
      add: true,
      remove: true,
      browsers: ['last 2 versions']
    },
    safe: true,
    discardComments: {
      removeAll: true
    }
  })
]

export let sassLoader = {
  includePaths: `${config.path_client}/styles`
}
