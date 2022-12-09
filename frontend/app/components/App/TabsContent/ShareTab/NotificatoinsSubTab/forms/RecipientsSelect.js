import React from 'react'
import PropTypes from 'prop-types'
import Select from 'react-select'
import { Col, FormGroup, Label } from 'reactstrap'

export class RecipientsSelect extends React.Component {
  static propTypes = {
    t: PropTypes.func.isRequired,
    state: PropTypes.object.isRequired,
    actions: PropTypes.object.isRequired
  };

  loadOptions = (input) => {
    const { actions } = this.props
    return actions.getRecipients(input)
  };

  changeRecipient = (value) => {
    const { actions } = this.props
    actions.changeRecipients(value)
  };

  render () {
    const { state, t } = this.props
    const recipients = state.recipients

    return (
      <FormGroup row>
        <Label sm={2}>{t('notificationsTab.form.recipient')}</Label>
        <Col sm={10}>
          <Select.Async
            name="recipient-select"
            loadOptions={this.loadOptions}
            multi
            value={recipients}
            onChange={this.changeRecipient}
          />
        </Col>
      </FormGroup>
    )
  }

}

export default RecipientsSelect
