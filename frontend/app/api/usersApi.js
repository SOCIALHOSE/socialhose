import {createApi} from '../common/Common'

const root = '/api/v1/users'

export const changePassword = createApi('POST', `${root}/change-password`)
