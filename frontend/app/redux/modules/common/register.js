import {createAction, handleActions} from 'redux-actions'
import {fromJS} from 'immutable'
import {thunkAction} from '../../utils/common'
import * as api from '../../../api/registrationApi'
import {push} from 'react-router-redux'
import { addAlert } from './alerts'
import i18n from '../../../i18n'
const NS = '[RESET PASS]'
/* const GET_BILLING_PLANS = `${NS} Get billing plans`
const SELECT_BILLING_PLAN = `${NS} Select billing plan`
const SEND_REGISTER_REQUEST = `${NS} Send register request`
const FINISH_REGISTER = `${NS} Finish register`
const SHOW_REGISTER_ERROR = `${NS} Show register error` */
const REQUEST_PASSWORD_RESET = `${NS} Request password reset`
const CONFIRM_PASSWORD_RESET = `${NS} Confirm password reset`
const CLEAR_MESSAGES = `${NS} Clear messages`
/* 
const getBillingPlans = thunkAction(GET_BILLING_PLANS, ({token, fulfilled}) => {
  return api
    .getBillingPlans(token)
    .then((plans) => {
      fulfilled(plans)
    })
}, true)

const selectBillingPlan = createAction(SELECT_BILLING_PLAN, (billingPlan) => ({billingPlan}))

const showRegisterError = createAction(SHOW_REGISTER_ERROR)

const sendRegisterRequest = thunkAction(SEND_REGISTER_REQUEST, (formValues, {token, fulfilled, getState, dispatch}) => {

  const billingPlan = getState().getIn(['common', 'register', 'selectedBillingPlan'])
  const privatePerson = Boolean(formValues.privatePerson)

  let payload = {}
  Object.assign(payload, formValues, {
    billingPlanId: billingPlan.id,
    privatePerson: privatePerson
  })

  if (privatePerson) {
    delete payload.organizationName
    delete payload.organizationAddress
    delete payload.organizationEmail
    delete payload.organizationPhone
  }

  return api
    .sendRegistrationRequest(token, payload)
    .then((response) => {
      fulfilled(response)
      dispatch(showRegisterError(null))
      dispatch(push('/auth/register-finish'))
    })
    .catch((response) => {
      dispatch(showRegisterError(response[0]))
      throw response
    })

}, true)

const finishRegistration = thunkAction(FINISH_REGISTER, (formValues, {getState, token, fulfilled}) => {
  let verificationCode = getState().getIn(['common', 'register', 'registrationCode'])
  const payload = Object.assign({}, {
    code: verificationCode,
    card: {
      creditCardNumber: formValues.creditCardNumber,
      CVV: formValues.CVV,
      expireMonth: formValues.expireMonth,
      expireYear: formValues.expireYear,
      address: {
        country: formValues.country,
        city: formValues.city,
        street: formValues.street,
        postalCode: formValues.postalCode
      }
    }
  })
  return api
    .finishRegistration(token, payload)
    .then((response) => {
      fulfilled(response)
    })
}, true) */

const requestPasswordReset = thunkAction(REQUEST_PASSWORD_RESET, (email, {fulfilled}) => {
  return api
    .requestPasswordReset(null, {email})
    .then(() => {
      fulfilled(
        i18n.t('loginApp:messages.forgotPasswordSubmit', { email: email })
      );
    })
})

const confirmPasswordReset = thunkAction(REQUEST_PASSWORD_RESET, (confirmationToken, password, {dispatch}) => {
  return api
    .confirmPasswordReset(null, {confirmationToken, password})
    .then(() => {
      dispatch(push('/auth/login'))
      dispatch(addAlert({type: 'notice', message: i18n.t('loginApp:messages.passwordUpdated')}))
    })
})

const clearMessages = createAction(CLEAR_MESSAGES)

export const actions = {
  /* getBillingPlans,
  selectBillingPlan,
  sendRegisterRequest,
  finishRegistration, */
  requestPasswordReset,
  confirmPasswordReset,
  // showRegisterError,
  clearMessages
}

export const initialState = fromJS({
  selectedBillingPlan: '',
  billingPlans: [],
  isLoading: false,
  error: null,
  registrationCode: null,
  successMessage: null
})
/* 
const toggleLoading = (state, {payload: {isPending}}) => {
  return state.set('isLoading', isPending)
} */

export default handleActions({
/* 
  [`${GET_BILLING_PLANS} fulfilled`]: (state, {payload: plans}) => state.set('billingPlans', plans),

  [SELECT_BILLING_PLAN]: (state, {payload: {billingPlan}}) => {
    console.log(billingPlan)
    return state.set('selectedBillingPlan', billingPlan)
  },

  [`${SEND_REGISTER_REQUEST} pending`]: toggleLoading,
  [`${SEND_REGISTER_REQUEST} fulfilled`]: (state, {payload: response}) => {
    return state.merge({
      'registrationCode': response.code,
      'successMessage': response.message
    })
  },

  [`${FINISH_REGISTER} pending`]: toggleLoading,
  [`${FINISH_REGISTER} fulfilled`]: (state, {payload: response}) => {
    return state.set('successMessage', response.message)
  }, */
  [`${REQUEST_PASSWORD_RESET} fulfilled`]: (state, {payload: message}) => {
    return state.set('successMessage', message)
  },
  [`${CONFIRM_PASSWORD_RESET} fulfilled`]: (state, {payload: message}) => {
    return state.set('successMessage', message)
  },
  [CLEAR_MESSAGES]: (state) => {
    return state.set('successMessage', null)
  }
/* 
  [SHOW_REGISTER_ERROR]: (state, {payload: error}) => {
    return state.set('error', error)
  },

  [`${GET_BILLING_PLANS} pending`]: toggleLoading */

}, initialState)
