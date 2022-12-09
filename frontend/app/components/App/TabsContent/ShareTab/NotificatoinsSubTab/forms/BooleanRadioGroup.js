import React from 'react'
import PropTypes from 'prop-types'
import RadioField from './RadioField'
import { Col, FormGroup } from 'reactstrap'
import { translate } from 'react-i18next'

export class BooleanRadioGroup extends React.PureComponent {
  static propTypes = {
    mainLabel: PropTypes.string.isRequired,
    trueLabel: PropTypes.string,
    falseLabel: PropTypes.string,
    name: PropTypes.string.isRequired,
    value: PropTypes.bool,
    onChange: PropTypes.func.isRequired
  }

  onChange = (event) => {
    const { onChange } = this.props
    let value = event.target.value
    if (value === 'true' || value === 'false') {
      value = value === 'true'
    }

    onChange(value)
  }

  render() {
    const {
      trueLabel = this.props.t('commonWords.Yes'),
      falseLabel = this.props.t('commonWords.No'),
      mainLabel,
      name,
      value,
      onChange
    } = this.props

    return (
      <FormGroup row>
        <Col sm={2}>{mainLabel}</Col>
        <Col sm={10}>
          <div className="d-flex">
            <RadioField
              label={trueLabel}
              name={name}
              checkedValue={value}
              value
              onChange={onChange}
            />

            <RadioField
              label={falseLabel}
              name={name}
              checkedValue={value}
              value={false}
              onChange={onChange}
            />
          </div>
        </Col>
      </FormGroup>
    )
  }
}

export default translate(['common'], { wait: true })(BooleanRadioGroup)
