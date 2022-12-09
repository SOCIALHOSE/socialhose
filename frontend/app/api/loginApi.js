import $ from 'jquery'
import {createApi} from '../common/Common'
import config from '../appConfig'
import { errorConstants } from '../common/constants'
import i18n from '../i18n'

export const login = (userData) => {
  return new Promise((resolve, reject) => {
    $.ajax({
      type: 'POST',
      url: config.apiUrl + '/security/token/create',
      dataType: 'json',
      data: JSON.stringify({
        email: userData.email,
        password: userData.password
      }),
      success: function (data) {
        resolve(data)
      },
      error: function (jqXHR, textStatus, errorThrown) {
        const errMessage =
          jqXHR.responseJSON &&
          jqXHR.responseJSON.errors &&
          jqXHR.responseJSON.errors
            .map((err) =>
              i18n.t(`loginApp:errorMessages.${errorConstants[err]}`, {
                defaultValue: err || ''
              })
            )
            .join(' ');
        console.log(errorThrown + ': Error ' + jqXHR.status, 'jsonAPIERROR');
        reject({
          msg: errMessage || i18n.t('common:alerts.error.somethingWrong')
        });
      }
    })
  })
}

export const loginRefresh = (refreshToken) => {
  return new Promise((resolve, reject) => {
    $.ajax({
      type: 'POST',
      url: config.apiUrl + '/security/token/refresh',
      dataType: 'json',
      data: JSON.stringify({
        refreshToken: refreshToken
      }),
      success: function (data) {
        resolve(data)
      },
      error: function (jqXHR, textStatus, errorThrown) {
        console.log(errorThrown + ': Error ' + jqXHR.status, 'jsonAPIERROR')
        reject({msg: 'Your session is expired, please login again'})

        /* if (jqXHR.status === 401) {
          reject({msg: 'Your session is expired, please login again'});
        } else {
          reject({msg: 'Login error, please login again'});
        } */
      }
    })
  })
}

export const getRestrictions = createApi('GET', '/api/v1/users/current/restrictions', {
  inputData: (data) => data
})
