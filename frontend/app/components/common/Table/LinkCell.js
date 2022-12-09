import React from 'react'
import PropTypes from 'prop-types'
import { translate } from 'react-i18next'
import { Button } from 'reactstrap';

export class LinkCell extends React.Component {
  static propTypes = {
    t: PropTypes.func.isRequired,
    item: PropTypes.object.isRequired,
    children: PropTypes.oneOfType([
      PropTypes.string,
      PropTypes.element
    ]),
    onClick: PropTypes.func.isRequired
  };

  onClick = () => {
    this.props.onClick(this.props.item)
  };

  render () {
    return (
      <Button
        type="button"
        color="link"
        className="btn-anchor"
        onClick={this.onClick}
      >
        {this.props.children}
      </Button>
    )
  }

}

export default translate(['tabsContent'], { wait: true })(LinkCell)
