import React from 'react'
import PropTypes from 'prop-types'
import Alert from './Alert'

export class Alerts extends React.Component {
  static propTypes = {
    alerts: PropTypes.array.isRequired,
    removeAlert: PropTypes.func.isRequired
  };

  forEachAlert = (callback) => {
    return this.props.alerts
      .map((alert) => {
        return (typeof alert === 'string') ? {message: alert} : alert
      })
      .map(callback)
  };

  render () {
    const { alerts, removeAlert } = this.props
    if (alerts.length <= 0) return null

    return (
      <div className="alerts-container">
        {this.forEachAlert((alert, i) => {
          return (
            <Alert
              key={i}
              removeAlert={removeAlert}
              alert={alert}
            />
          )
        })}
      </div>
    )
  }

}

export default Alerts
