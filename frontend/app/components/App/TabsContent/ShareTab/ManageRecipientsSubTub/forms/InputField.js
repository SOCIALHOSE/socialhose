import React from 'react'
import PropTypes from 'prop-types'
import { translate } from 'react-i18next'
import { FormGroup, Input, Label } from 'reactstrap'

export class InputField extends React.Component {
  static propTypes = {
    t: PropTypes.func.isRequired,
    formType: PropTypes.string.isRequired,
    field: PropTypes.string.isRequired,
    value: PropTypes.string.isRequired,
    onChangeFor: PropTypes.func.isRequired
  };

  render () {
    const { t, formType, field, value, onChangeFor } = this.props
    const trPath = `manageRecipientsTab.form.${formType}`

    return (
      <FormGroup>
        <Label>{t(`${trPath}.${field}`)}</Label>
        <Input type="text" onChange={onChangeFor(field)} value={value} />
      </FormGroup>
    )
  }

}

export default translate(['tabsContent'], { wait: true })(InputField)
