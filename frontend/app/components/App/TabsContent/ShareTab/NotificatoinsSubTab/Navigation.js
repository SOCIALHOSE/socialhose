import React from 'react'
import PropTypes from 'prop-types'
import { translate } from 'react-i18next'
import { Button } from 'reactstrap'

class Navigation extends React.Component {
  static propTypes = {
    t: PropTypes.func.isRequired,
    actions: PropTypes.object.isRequired
  };

  backToTables = () => {
    this.props.actions.switchShareSubScreen('notifications', 'tables')
  };

  render () {

    return (
      <Button className="btn-wide mb-2" size="sm" color="info" onClick={this.backToTables}>
        <i className="lnr lnr-chevron-left"> </i>
      </Button>
    )
  }
}

export default translate(['tabsContent'], { wait: true })(Navigation)
