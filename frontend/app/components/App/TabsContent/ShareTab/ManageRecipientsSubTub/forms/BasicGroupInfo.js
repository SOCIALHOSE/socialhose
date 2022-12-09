import React from 'react'
import PropTypes from 'prop-types'
import { translate } from 'react-i18next'
import InputField from './InputField'
import { Card, CardBody, CardTitle, Form, FormGroup, Input, Label } from 'reactstrap'

export class BasicGroupInfo extends React.Component {
  static propTypes = {
    t: PropTypes.func.isRequired,
    item: PropTypes.object.isRequired,
    formActions: PropTypes.object.isRequired
  };

  onChangeFor = (field) => (event) => {
    const { formActions } = this.props
    formActions.changeField(field, event.target.value)
  };

  render () {
    const { t, item } = this.props

    return (
      <Card>
        <CardBody>
          <CardTitle>{t('manageRecipientsTab.form.group.basicInfo')}</CardTitle>
          <Form>
            <InputField
              formType="group"
              field="name"
              value={item.name}
              onChangeFor={this.onChangeFor}
            />

            <FormGroup>
              <Label>
                {t('manageRecipientsTab.form.group.description')}
              </Label>
              <Input 
                type="textarea" 
                rows="5" 
                onChange={this.onChangeFor('description')} 
                value={item.description} 
              />
            </FormGroup>
          </Form>

          <hr />
          {!!item.recipients && (
            <p>
              {t('manageRecipientsTab.form.group.recipientsNumber')}:{' '}
              {item.recipients.length}
            </p>
          )}
        </CardBody>
      </Card>
    )
  }

}

export default translate(['tabsContent'], { wait: true })(BasicGroupInfo)
