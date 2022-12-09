import webpack from 'webpack'
import webpackMiddleware from 'webpack-dev-middleware'
import webpackHotMiddleware from 'webpack-hot-middleware'

import express from 'express'
import http from 'http'
import path from 'path'

import config from '../config'
import webpackConfig from '../webpack/webpack.config'

const app = express()

// Webpack dev server
if (config.globals.__DEV__) {
  const compiler = webpack(webpackConfig)
  const devMiddleware = webpackMiddleware(compiler, {
    publicPath: webpackConfig.output.publicPath,
    stats: {
      colors: true,
      hash: false,
      timings: true,
      chunks: false,
      chunkModules: false,
      modules: false
    },
    watchOptions: {
      aggregateTimeout: 300,
      poll: 300
    }
  })
  app.use(devMiddleware)
  app.use(webpackHotMiddleware(compiler))

  app.listen(config.webpack_port, config.server_host, function (err) {
    if (err) console.log(err)
    console.log('WebpackDevServer listening at localhost:' + config.webpack_port)
  })

  const fs = devMiddleware.fileSystem
  app.get('*', function (req, res) {
    const file = fs.readFileSync(path.join(compiler.outputPath, 'index.html'))
    res.send(file.toString())
  })
}
else {
  app.get('*', (req, res) => res.sendFile(path.join(webpackConfig.output.path, 'index.html')))
}

app.use(express.static(config.path_base))

const server = http.createServer(app)
const port = config.server_port

server.listen(port, function (err) {
  if (err) {
    console.log(err)
  }
  console.log('Server running on port ' + port)
})
