import base from './_base'

let overrides = {
  compiler_enable_hmr: true,
  compiler_public_path: `http://${base.server_host}:${base.server_port}/`
};

export default overrides;
