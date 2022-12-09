import React from 'react'
import PropTypes from 'prop-types'
import { translate } from 'react-i18next'
import { Button } from 'reactstrap'

export class DeleteButton extends React.Component {
  static propTypes = {
    t: PropTypes.func.isRequired,
    id: PropTypes.number.isRequired,
    onDelete: PropTypes.func.isRequired
  }

  onDelete = () => {
    this.props.onDelete(this.props.id)
  }

  render() {
    return (
      <Button color="link" className="text-danger p-0" onClick={this.onDelete}>
        <i className="lnr lnr-trash" />
      </Button>
    )
  }
}

export default translate(['tabsContent'], { wait: true })(DeleteButton)
