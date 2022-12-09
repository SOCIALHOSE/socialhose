import React from 'react'
import PropTypes from 'prop-types'
import { translate } from 'react-i18next'
import InputField from './InputField'
import { Card, CardBody, CardTitle, Form } from 'reactstrap'

export class BasicRecipientInfo extends React.Component {
  static propTypes = {
    t: PropTypes.func.isRequired,
    item: PropTypes.object.isRequired,
    formActions: PropTypes.object.isRequired
  }

  onChangeFor = (field) => (event) => {
    const { formActions } = this.props
    formActions.changeField(field, event.target.value)
  }

  render() {
    const { item, t } = this.props

    return (
      <Card>
        <CardBody>
          <CardTitle>{t('manageRecipientsTab.form.recipient.basicInfo')}</CardTitle>
          <Form>
            <InputField
              formType="recipient"
              field="firstName"
              value={item.firstName}
              onChangeFor={this.onChangeFor}
            />

            <InputField
              formType="recipient"
              field="lastName"
              value={item.lastName}
              onChangeFor={this.onChangeFor}
            />

            <InputField
              formType="recipient"
              field="email"
              value={item.email}
              onChangeFor={this.onChangeFor}
            />
          </Form>
        </CardBody>
      </Card>
    )
  }
}

export default translate(['tabsContent'], { wait: true })(BasicRecipientInfo)
