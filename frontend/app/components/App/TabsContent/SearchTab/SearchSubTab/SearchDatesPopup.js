import React from 'react'
import moment from 'moment'
import PropTypes from 'prop-types'
import { translate } from 'react-i18next'
import BetweenDatepickers from './SearchBy/BetweenDatepickers'
import { compose } from 'redux'
import classnames from 'classnames'
import { Button, CustomInput, FormGroup } from 'reactstrap'

export class SearchDatesPopup extends React.Component {
  static propTypes = {
    userSubscriptionDate: PropTypes.string.isRequired,
    userSubscription: PropTypes.string.isRequired,
    t: PropTypes.func.isRequired,
    searchIntervals: PropTypes.array.isRequired,
    searchLastDates: PropTypes.array.isRequired,
    chosenSearchInterval: PropTypes.string.isRequired,
    chosenSearchLastDate: PropTypes.string.isRequired,
    chosenStartDate: PropTypes.string.isRequired,
    chosenEndDate: PropTypes.string.isRequired,
    setSearchInterval: PropTypes.func.isRequired,
    setSearchLastDate: PropTypes.func.isRequired,
    setSearchDate: PropTypes.func.isRequired,
    setStartDate: PropTypes.func.isRequired,
    setEndDate: PropTypes.func.isRequired
  }

  setSearchInterval = (e) => {
    const chosenInterval = e.target.dataset.interval
    const chosenStartDate = this.props.chosenStartDate
    const chosenEndDate = this.props.chosenEndDate
    const chosenLastDate = this.props.chosenSearchLastDate
    const isIntervalBetween = chosenInterval === 'between'

    this.props.setSearchInterval(chosenInterval)

    if (
      (isIntervalBetween && chosenStartDate !== '') ||
      (isIntervalBetween && chosenEndDate !== '')
    ) {
      const endDate = chosenEndDate !== '' ? chosenEndDate : 'now'
      const startDate = chosenStartDate !== '' ? chosenStartDate : 'until'

      this.props.setSearchDate(startDate + ' - ' + endDate)
    }

    if (chosenInterval === 'all') {
      this.props.setSearchDate('all')
    }

    if (chosenInterval === 'last') {
      this.props.setSearchDate(chosenLastDate)
    }
  }

  setLastDate = (e) => {
    const chosenLastDate = e.target.dataset.lastDate
    const isDisabled = e.target.dataset.disabled === 'true'

    if (isDisabled) return false

    if (this.props.chosenSearchInterval !== 'last') {
      this.props.setSearchInterval('last')
    }

    this.props.setSearchLastDate(chosenLastDate)
    this.props.setSearchDate(chosenLastDate)
  }

  onReset = () => {
    this.props.setSearchInterval('all')
    this.props.setSearchDate('all')
    this.props.setStartDate('')
    this.props.setEndDate('')
  }

  render() {
    const {
      t,
      chosenSearchInterval,
      chosenStartDate,
      chosenEndDate,
      setSearchInterval,
      setSearchDate,
      setStartDate,
      setEndDate,
      chosenSearchLastDate,
      searchIntervals,
      searchLastDates,
      userSubscription
    } = this.props
    const subscriptionLimitIndex = searchLastDates.indexOf(userSubscription)
    const minDate = moment().startOf('day').subtract(
      parseInt(userSubscription.slice(0, -1)),
      'days'
    )

    return (
      <div>
        <div className="d-flex justify-content-between">
          <p className="mb-2">
            {t('searchTab.searchDates.subscriptionLabel')}:
            <strong>
              {t('searchTab.userSubscription.' + this.props.userSubscription)}
            </strong>
          </p>
          <div>
            <Button color="warning" className="mb-2" onClick={this.onReset}>
              {t('searchTab.searchDates.resetBtn')}
            </Button>
          </div>
        </div>

        <FormGroup>
          {searchIntervals.map((interval, i) => {
            return (
              <div key={interval}>
                <CustomInput
                  checked={this.props.chosenSearchInterval === interval}
                  type="radio"
                  id={'search-interval-' + interval}
                  data-interval={interval}
                  name="date-interval"
                  label={t('searchTab.searchDates.' + interval)}
                  onChange={this.setSearchInterval}
                />

                {interval === 'last' && (
                  <ul className="search-last-dates mx-3">
                    {searchLastDates.map((lastDate, i) => {
                      const isDisabled = i > subscriptionLimitIndex
                      const isActive =
                        chosenSearchLastDate === lastDate &&
                        chosenSearchInterval === 'last'
                      const className = classnames('search-last-dates__item', {
                        disabled: isDisabled,
                        active: isActive
                      })

                      return (
                        <li
                          key={'last-date-' + i}
                          data-last-date={lastDate}
                          data-disabled={isDisabled}
                          className={className}
                          onClick={this.setLastDate}
                        >
                          {t('searchTab.searchDates.' + lastDate)}
                        </li>
                      )
                    })}
                  </ul>
                )}

                {interval === 'between' && (
                  <BetweenDatepickers
                    chosenSearchInterval={chosenSearchInterval}
                    chosenStartDate={chosenStartDate}
                    chosenEndDate={chosenEndDate}
                    minDate={minDate}
                    setSearchInterval={setSearchInterval}
                    setSearchDate={setSearchDate}
                    setStartDate={setStartDate}
                    setEndDate={setEndDate}
                  />
                )}
              </div>
            )
          })}
        </FormGroup>
      </div>
    )
  }
}

const applyDecorators = compose(translate(['tabsContent'], { wait: true }))

export default applyDecorators(SearchDatesPopup)
