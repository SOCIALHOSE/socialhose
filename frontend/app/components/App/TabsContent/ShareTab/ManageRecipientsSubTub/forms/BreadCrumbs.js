import React from 'react'
import PropTypes from 'prop-types'
import { translate } from 'react-i18next'

export class BreadCrumbs extends React.Component {
  static propTypes = {
    t: PropTypes.func.isRequired,
    title: PropTypes.string.isRequired,
    onBack: PropTypes.func.isRequired
  };

  render () {
    const { t, title, onBack } = this.props

    return (
      <div>
        <a href="#" onClick={onBack}>
          {t('tableSwitcher.recipients')}
        </a>
        <span> &gt; {title}</span>
      </div>
    )
  }

}

export default translate(['tabsContent'], { wait: true })(BreadCrumbs)
