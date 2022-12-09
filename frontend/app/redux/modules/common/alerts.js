import React, { Fragment } from 'react';
import ReduxModule from '../abstract/reduxModule';
import { toast } from 'react-toastify';
import i18n from '../../../i18n';
import { fromJS } from 'immutable';
import { isLive } from '../../../common/constants';

const ADD_ALERT = 'Add alert';
const REMOVE_ALERT = 'Remove alert';
const REMOVE_ALL_ALERTS = 'Remove alert';

export class Alerts extends ReduxModule {
  getNamespace() {
    return '[Alert]';
  }

  defineActions() {
    const addAlert = this.createAction(ADD_ALERT, (options) => options);
    const removeAlert = this.createAction(REMOVE_ALERT, (id) => id);
    const removeAllAlerts = this.createAction(REMOVE_ALL_ALERTS, () => {});

    return {
      addAlert,
      removeAlert,
      removeAllAlerts
    };
  }

  getInitialState() {
    return [];
  }

  defineReducers() {
    return {
      [ADD_ALERT]: (state, { payload: options }) => {
        showAlert(options);

        // handling breaking api errors (should be catch by backend)
        const newOptions = Array.isArray(options)
          ? options.map((v) =>
              typeof v === 'string' && v.startsWith('Error: ') && isLive
                ? i18n.t('common:alerts.error.somethingWrong2')
                : v
            )
          : options;

        return state.concat(newOptions);
      },
      [REMOVE_ALERT]: (state, { payload: id }) => {
        return state.filter((alert) => alert.id !== id);
      },
      [REMOVE_ALL_ALERTS]: () => {
        return fromJS([]);
      }
    };
  }
}

const alerts = new Alerts();
alerts.init();

const backendErrs = ['Error: ', 'Can\'t exec search'];

const showAlert = (alertMessages) => {
  const alertsArr = Array.isArray(alertMessages)
    ? alertMessages
    : [alertMessages];

  alertsArr
    .map((alert) => {
      return typeof alert === 'string'
        ? {
          message:
              isLive && backendErrs.some((v) => alert.startsWith(v))
                ? i18n.t('common:alerts.error.somethingWrong2')
                : alert
        } // handling breaking api errors (should be catch by backend)
        : alert;
    })
    .map((alert) => {
      const interpolateParameters = alert ? alert.parameters : {};
      const i18nKey = alert && `alerts.${alert.type}.${alert.transKey}`;

      if (alert) {
        toast(
          <Fragment>
            {alert.type ? (
              <p className="mb-2 text-uppercase">
                {i18n.t(`alerts.type.${oldValueMapping[alert.type]}`, {
                  defaultValue: oldValueMapping[alert.type]
                })}
              </p>
            ) : (
              ''
            )}
            {(i18nKey &&
              i18n.t(i18nKey, {
                ...interpolateParameters,
                defaultValue: alert.message || 'Unknown error'
              })) ||
              alert.message ||
              'Unknown error'}
          </Fragment>,
          {
            type: oldValueMapping[alert.type || 'warning']
          }
        );
      } else {
        toast.warn('Unknown error');
      }
    });
};

const oldValueMapping = {
  notice: 'success',
  warning: 'warning',
  error: 'error'
};

export const addAlert = alerts.actions.addAlert;

export default alerts;
