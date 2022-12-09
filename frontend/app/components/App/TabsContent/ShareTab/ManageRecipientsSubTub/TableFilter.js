import React from 'react'
import PropTypes from 'prop-types'
import classnames from 'classnames'
import { Button, Input, InputGroup, InputGroupAddon } from 'reactstrap'

const INPUT_THROTTLE_TIME = 300

export class TableFilter extends React.Component {
  static propTypes = {
    type: PropTypes.string.isRequired,
    onFilterRequest: PropTypes.func.isRequired
  };

  constructor () {
    super()
    this.state = {
      value: ''
    }
  }

  onFilter = (event) => {
    this._onFilterImpl(event.target.value)
  };

  _onFilterImpl (filterValue) {
    const { onFilterRequest } = this.props
    this.setState({ value: filterValue })
    if (this.inputDelay) {
      clearTimeout(this.inputDelay)
    }
    this.inputDelay = setTimeout(() => {
      onFilterRequest(filterValue)
    }, INPUT_THROTTLE_TIME)
  }

  onClear = () => {
    const value = this.state.value
    value && this._onFilterImpl('')
  };

  render () {
    const { type } = this.props
    const value = this.state.value
    const hasValue = !!value
    const iconClasses = classnames('fa', {
      'fa-search': !hasValue,
      'fa-times': hasValue
    })

    const placeholder = `Find ${type}`

    return (
      <InputGroup className="mb-3">
        <Input
          type="text"
          placeholder={placeholder}
          value={value}
          onChange={this.onFilter}
        />
        <InputGroupAddon addonType="append">
          <Button color="primary" onClick={this.onClear}>
            <i className={iconClasses}></i>
          </Button>
        </InputGroupAddon>
        {/* <button
          className="cw-grid__filter-button"
          type="button"
          onClick={this.onClear}
        >
          <i className={iconClasses} />
        </button> */}
      </InputGroup>
    )
  }
}

export default TableFilter
