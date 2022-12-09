import React from 'react'
import PropTypes from 'prop-types'
import { translate } from 'react-i18next'

export class CheckboxCell extends React.Component {
  static propTypes = {
    t: PropTypes.func.isRequired,
    id: PropTypes.oneOfType([
      PropTypes.string,
      PropTypes.number
    ]),
    checked: PropTypes.bool.isRequired,
    onChange: PropTypes.func.isRequired
  };

  onChange = () => {
    this.props.onChange(this.props.id)
  };

  render () {
    return (
      <input type="checkbox" checked={this.props.checked} onChange={this.onChange} />
    )
  }

}

export default translate(['tabsContent'], { wait: true })(CheckboxCell)
