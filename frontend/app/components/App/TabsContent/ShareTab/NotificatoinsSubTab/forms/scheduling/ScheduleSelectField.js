import React from 'react'
import PropTypes from 'prop-types'
import { translate } from 'react-i18next'
import Select from 'react-select'
import classnames from 'classnames'
import { padLeft, addOrdinalSuffix } from '../../../../../../../common/StringUtils'

export class ScheduleSelectField extends React.Component {
  static propTypes = {
    t: PropTypes.func.isRequired,
    needTranslate: PropTypes.bool,
    field: PropTypes.string.isRequired,
    items: PropTypes.array.isRequired,
    value: PropTypes.oneOfType([
      PropTypes.string,
      PropTypes.number
    ]),
    onChange: PropTypes.func.isRequired
  };

  paddingFields = ['hour', 'minute'];
  suffixFields = ['monthDay'];

  onChange = (item) => {
    const { field, onChange } = this.props
    onChange(field, item.value)
  };

  render () {
    const { t, needTranslate = true, items, value, field } = this.props
    const classes = classnames('schedule-select-field', `schedule-select-field--${field}`)
    const options = items.map(item => {
      let label = ''
      if (needTranslate) {
        label = t(`notificationsTab.form.${field}.${item}`)
      }
      else {
        label = (this.paddingFields.includes(field)) ? padLeft(item.toString(), 2) : item
        label = (this.suffixFields.includes(field)) ? addOrdinalSuffix(item) : label
      }
      return {
        value: item,
        label
      }
    })

    return (
      <Select
        className={classes}
        options={options}
        value={value}
        clearable={false}
        onChange={this.onChange}
      />
    )
  }

}

export default translate(['tabsContent'], { wait: true })(ScheduleSelectField)
