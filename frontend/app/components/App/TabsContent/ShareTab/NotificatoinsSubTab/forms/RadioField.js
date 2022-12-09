import React from 'react'
import PropTypes from 'prop-types'
import { CustomInput } from 'reactstrap'

export class RadioField extends React.PureComponent {
  static propTypes = {
    label: PropTypes.string.isRequired,
    name: PropTypes.string.isRequired,
    checkedValue: PropTypes.oneOfType([
      PropTypes.string,
      PropTypes.bool
    ]),
    value: PropTypes.oneOfType([
      PropTypes.string,
      PropTypes.bool
    ]),
    onChange: PropTypes.func.isRequired
  };

  onChange = (event) => {
    const { onChange } = this.props
    let value = event.target.value
    if (value === 'true' || value === 'false') {
      value = value === 'true'
    }

    onChange(value)
  };

  render () {
    const { label, name, checkedValue, value } = this.props

    return (
      <CustomInput
        id={`${name}_${value}`}
        type="radio"
        className="mr-2"
        name={name}
        value={value}
        checked={checkedValue === value}
        onChange={this.onChange}
        label={label}
      />
    )
  }

}

export default RadioField
