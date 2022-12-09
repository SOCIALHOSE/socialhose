import React from 'react'
import PropTypes from 'prop-types'
import { translate, Interpolate } from 'react-i18next'

export class Alert extends React.Component {
  static propTypes = {
    alert: PropTypes.func.isRequired,
    removeAlert: PropTypes.func.isRequired,
    t: PropTypes.func.isRequired
  };

  componentDidMount = () => {
    setTimeout(this.closeAlert, 5000)
  };

  closeAlert = () => {
    const { removeAlert, alert } = this.props
    removeAlert(alert.id)
  };

  onClose = (e) => {
    e.preventDefault()
    this.closeAlert()
  };

  render () {
    const { alert } = this.props
    const interpolateParameters = alert.parameters || {}
    return (
      <div className={'alert ' + alert.type}>
        <div className="alert__head">
          <h2 className="alert__title">{alert.type}</h2>

          <a href="#" className="alert__close-btn" onClick={this.onClose}>
            <i className="fa fa-times"> </i>
          </a>
        </div>

        <div className="alert__body">
          <p className="alert__text">
            <Interpolate
              i18nKey={'alerts.' + alert.type + '.' + alert.transKey}
              {...interpolateParameters}
              options={{defaultValue: alert.message || 'Unknown error'}}
            />
          </p>
        </div>
      </div>
    )
  }
}

export default translate(['common'], { wait: true })(Alert)
