import config from '../config'

import { entry, output, resolve } from './options/_common'
import { plugins } from './options/_plugins'
import { preLoaders, loaders, eslint, postcss, sassLoader } from './options/_loaders'

const webpackConfig = {
  name: 'client',
  target: 'web',
  devtool: config.compiler_devtool,
  resolve,
  entry,
  output,
  plugins,
  module: {
    preLoaders,
    loaders
  },
  eslint,
  sassLoader,
  postcss
}

export default webpackConfig
