import React from 'react'
import PropTypes from 'prop-types'
import { translate } from 'react-i18next'
import { ButtonGroup, Button } from 'reactstrap'

export class Toggler extends React.Component {
  static propTypes = {
    t: PropTypes.func.isRequired,
    id: PropTypes.oneOfType([PropTypes.string, PropTypes.number]),
    turnOnAction: PropTypes.func.isRequired,
    turnOffAction: PropTypes.func.isRequired,
    state: PropTypes.bool.isRequired,
    enabledText: PropTypes.string.isRequired,
    disabledText: PropTypes.string.isRequired
  };

  onOnClick = () => {
    !this.props.state && this.props.turnOnAction(this.props.id)
  };

  onOffClick = () => {
    this.props.state && this.props.turnOffAction(this.props.id)
  };

  render () {
    const { enabledText, disabledText, state, t } = this.props
    return (
      <ButtonGroup size="sm">
        <Button
          outline
          color="success"
          onClick={this.onOnClick}
          active={state}
        >
          {t('toggler.' + enabledText)}
        </Button>
        <Button
          outline
          color="success"
          onClick={this.onOffClick}
          active={!state}
        >
          {t('toggler.' + disabledText)}
        </Button>
      </ButtonGroup>
    )
  }
}

export default translate(['tabsContent'], { wait: true })(Toggler)
