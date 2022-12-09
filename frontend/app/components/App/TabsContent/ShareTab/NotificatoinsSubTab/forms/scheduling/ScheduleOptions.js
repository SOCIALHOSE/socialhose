import React from 'react'
import PropTypes from 'prop-types'
import { translate } from 'react-i18next'
import ScheduleSelectField from './ScheduleSelectField'
import { IoIosCloseCircleOutline } from 'react-icons/io'

export class ScheduleOptions extends React.Component {
  static propTypes = {
    t: PropTypes.func.isRequired,
    id: PropTypes.oneOfType([
      PropTypes.string,
      PropTypes.number
    ]),
    type: PropTypes.string.isRequired,
    item: PropTypes.object.isRequired,
    constants: PropTypes.object.isRequired,
    canDelete: PropTypes.bool,
    onChange: PropTypes.func.isRequired,
    onRemove: PropTypes.func
  };

  onChange = (field, value) => {
    const { id, item, onChange } = this.props
    const newItem = {
      ...item,
      [field]: value
    }
    onChange(id, newItem)
  };

  onRemove = () => {
    const { id, canDelete, onRemove } = this.props
    if (canDelete && onRemove) {
      onRemove(id)
    }
  };

  render () {
    const { t, id, type, item, canDelete = false, constants } = this.props
    const showTime = (type === 'daily' && item.time === 'once') || (type !== 'daily')

    return (
      <div className="schedule-options">
        {id !== 'new' &&
          <div>{t(`notificationsTab.form.type.${type}`)}&nbsp;</div>
        }
        <p>Send</p>
        {type === 'daily' &&
          <div className="schedule-options__group">
            <ScheduleSelectField
              field='time'
              items={constants.time}
              value={item.time}
              onChange={this.onChange}
            />

            <ScheduleSelectField
              field='days'
              items={constants.days}
              value={item.days}
              onChange={this.onChange}
            />
          </div>
        }

        {type === 'weekly' &&
          <div className="schedule-options__group">
            <ScheduleSelectField
              field='period'
              items={constants.period}
              value={item.period}
              onChange={this.onChange}
            />

            <ScheduleSelectField
              field='day'
              items={constants.day}
              value={item.day}
              onChange={this.onChange}
            />
          </div>
        }

        {type === 'monthly' &&
          <div className="schedule-options__group">
            <ScheduleSelectField
              needTranslate={false}
              field='monthDay'
              items={constants.monthDay}
              value={item.monthDay}
              onChange={this.onChange}
            />
          </div>
        }

        {(type === 'weekly' || type === 'monthly') &&
          <div>of the month&nbsp;</div>
        }

        {showTime &&
          <div className="schedule-options__group">
            <span>at</span>
            <ScheduleSelectField
              needTranslate={false}
              field='hour'
              items={constants.hour}
              value={item.hour}
              onChange={this.onChange}
            />
            <span>:</span>
            <ScheduleSelectField
              needTranslate={false}
              field='minute'
              items={constants.minute}
              value={item.minute}
              onChange={this.onChange}
            />
          </div>
        }

        {canDelete && (
          <button
            title="Remove"
            type="button"
            className="btn p-0"
            onClick={this.onRemove}
          >
            <IoIosCloseCircleOutline size={22} className="text-danger ml-2" />
          </button>
        )}
      </div>
    )
  }

}

export default translate(['tabsContent'], { wait: true })(ScheduleOptions)
