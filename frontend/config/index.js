import base from './_base'

let overrides = require(`./_${base.env}`);

export default Object.assign({}, base, overrides)
