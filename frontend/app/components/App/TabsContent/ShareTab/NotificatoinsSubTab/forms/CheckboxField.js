import React from 'react'
import PropTypes from 'prop-types'
import { Col, CustomInput, FormGroup, Label } from 'reactstrap'

export class CheckboxField extends React.PureComponent {
  static propTypes = {
    label: PropTypes.string.isRequired,
    additionalLabel: PropTypes.string.isRequired,
    value: PropTypes.bool.isRequired,
    onChange: PropTypes.func.isRequired
  };

  onChange = () => {
    const { onChange, value } = this.props
    onChange(!value)
  };

  render () {
    const { label, additionalLabel, value } = this.props
    
    return (
      <FormGroup row>
        <Label sm={2}>{label}</Label>
        <Col sm={10}>
          <CustomInput
            id={label}
            type="checkbox"
            checked={value} 
            onChange={this.onChange} 
            label={additionalLabel} 
          />
        </Col>
      </FormGroup>
    )
  }

}

export default CheckboxField
