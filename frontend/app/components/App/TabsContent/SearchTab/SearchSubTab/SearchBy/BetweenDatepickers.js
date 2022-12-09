import React from 'react'
import PropTypes from 'prop-types'
import { DateRangePicker } from 'react-dates'
import moment from 'moment'
import { getMomentObject } from '../../../../../../common/helper'

export class BetweenDatepickers extends React.Component {
  state = {}

  static propTypes = {
    chosenSearchInterval: PropTypes.string.isRequired,
    chosenStartDate: PropTypes.string.isRequired,
    chosenEndDate: PropTypes.string.isRequired,
    setSearchInterval: PropTypes.func.isRequired,
    setSearchDate: PropTypes.func.isRequired,
    setStartDate: PropTypes.func.isRequired,
    minDate: PropTypes.object,
    setEndDate: PropTypes.func.isRequired
  }

  swapDate = (startDate, endDate) => {
    if (startDate.isAfter(endDate)) {
      const temp = startDate
      startDate = endDate
      endDate = temp
    }
    return { startDate, endDate }
  }
  /* 
  setDates = (date, isStartDate) => {
    const {
      chosenStartDate,
      chosenEndDate,
      setStartDate,
      setEndDate,
      setSearchDate
    } = this.props

    const hasStartDate = !!chosenStartDate
    const hasEndDate = !!chosenEndDate
    let startDate = hasStartDate ? moment(chosenStartDate) : moment()
    let endDate = hasEndDate ? moment(chosenEndDate) : moment()

    startDate = isStartDate ? date : startDate
    endDate = !isStartDate ? date : endDate

    const swappedDate = this.swapDate(startDate, endDate)
    startDate = swappedDate.startDate.format('YYYY-MM-DD')
    endDate = swappedDate.endDate.format('YYYY-MM-DD')

    setStartDate(startDate.format('YYYY-MM-DD'))
    setEndDate(endDate.format('YYYY-MM-DD'))

    const endDateLabel = hasEndDate ? endDate : 'now'
    const startDateLabel = hasStartDate ? startDate : 'until'
    let label = isStartDate
      ? `${startDate} - ${endDateLabel}`
      : `${startDateLabel} - ${endDate}`
    setSearchDate(label)
  } */

  setBetweenInterval = () => {
    const { chosenSearchInterval, setSearchInterval } = this.props
    if (chosenSearchInterval === 'between') return false

    setSearchInterval('between')
  }

  handleDateChange = ({ startDate, endDate }) => {
    const { setStartDate, setEndDate } = this.props

    setStartDate(startDate ? startDate.format('YYYY-MM-DD') : null)
    setEndDate(endDate ? endDate.format('YYYY-MM-DD') : null)

    if (startDate && endDate) {
      this.setBetweenInterval()
    }
  }

  onFocusChange = (focus) => {
    this.setState({ focusedInput: focus })
  }

  isOutsideRange = (date) => {
    const today = moment()
    return date.isAfter(today) || date.isBefore(this.props.minDate)
  }

  render() {
    const { chosenStartDate, chosenEndDate } = this.props
    const today = moment()
    const startDate = getMomentObject(chosenStartDate)
    const endDate = getMomentObject(chosenEndDate)

    return (
      <div className="ml-3">
        <DateRangePicker
          startDateId="startDate"
          endDateId="endDate"
          startDate={startDate}
          endDate={endDate}
          onDatesChange={this.handleDateChange}
          focusedInput={this.state.focusedInput}
          onFocusChange={this.onFocusChange}
          displayFormat="MM/DD/YYYY"
          startDatePlaceholderText="Start Date"
          endDatePlaceholderText="End Date"
          numberOfMonths={1}
          maxDate={today}
          // eslint-disable-next-line react/jsx-no-bind
          isOutsideRange={this.isOutsideRange}
        />
      </div>
    )
  }
}

export default BetweenDatepickers
