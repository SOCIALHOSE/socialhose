import {createApi} from '../common/Common'

const root = '/security/registration'

export const getBillingPlans = createApi('GET', `${root}/plans`)

export const sendRegistrationRequest = createApi('POST', root)

export const finishRegistration = createApi('POST', `${root}/finish`)

export const autocompleteOrganizationName = createApi('GET', `${root}/organizationAutocomplete`, {
  inputData: (organizationName) => ({organizationName})
})

export const requestPasswordReset = createApi('POST', '/security/resetting/request')
export const confirmPasswordReset = createApi('POST', '/security/resetting/confirm')
