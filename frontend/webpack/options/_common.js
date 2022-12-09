import config from '../../config'

const isProduction = config.env === 'production'

const buildEntryPoint = function (entryPoint) {
  let entry = [entryPoint]
  if (!isProduction) {
    entry.unshift(
      'webpack-hot-middleware/client?path=/__webpack_hmr'
    )
  }
  return entry
}

export const entry = {
  'cw': buildEntryPoint(`${config.path_client}/main.js`)
}

export const output = {
  libraryTarget: 'var',
  library: 'CW',
  filename: '[name].js',
  path: config.path_dist,
  publicPath: config.compiler_public_path
}

export const resolve = {
  root: config.path_client,
  extensions: ['', '.js']
}
