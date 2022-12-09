import React from 'react'
import PropTypes from 'prop-types'
import { translate } from 'react-i18next'
import { EMAILS_SUBSCREENS } from '../../../../../redux/modules/appState/share/tabs'
import { Button } from 'reactstrap'

class Navigation extends React.Component {
  static propTypes = {
    t: PropTypes.func.isRequired,
    actions: PropTypes.object.isRequired
  };

  backToTable = () => {
    this.props.actions.switchShareSubScreen(
      'emails',
      EMAILS_SUBSCREENS.EMAILS_TABLE
    )
  };

  render () {

    return (
      <Button className="btn-wide mb-2" size="sm" color="info" onClick={this.backToTable}>
        <i className="lnr lnr-chevron-left"> </i>
      </Button>
    )
  }
}

export default translate(['tabsContent'], { wait: true })(Navigation)
