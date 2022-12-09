import React, { Fragment } from 'react'
import PropTypes from 'prop-types'
import { translate } from 'react-i18next'
import TypeSelector from './TypeSelector'
import ScheduleOptions from './ScheduleOptions'
import { Button, ListGroup, ListGroupItem } from 'reactstrap'

export class Scheduling extends React.Component {
  static propTypes = {
    t: PropTypes.func.isRequired,
    state: PropTypes.object.isRequired,
    actions: PropTypes.object.isRequired
  };

  onChange = (id, item) => {
    const { actions } = this.props
    if (id === 'new') {
      actions.changeNewSchedule(item)
    } else {
      actions.changeExistingSchedule(item, id)
    }
  };

  addSchedule = () => {
    this.props.actions.addSchedule()
  };

  removeSchedule = (id) => {
    this.props.actions.removeSchedule(id)
  };

  render () {
    const { state, actions, t } = this.props

    const constants = state.constants
    const activeType = state.newTime.type

    return (
      <Fragment>
        <TypeSelector
          types={constants.type}
          activeType={activeType}
          onChange={actions.changeScheduleType}
        />

        <div className="new-schedule mb-3">
          <ScheduleOptions
            id="new"
            type={activeType}
            item={state.newTime}
            constants={constants}
            onChange={this.onChange}
          />

          <Button
            color="primary"
            onClick={this.addSchedule}
          >
            {t('notificationsTab.form.add')}
          </Button>
        </div>

        <div className="schedule-list">
          <p className="text-muted mb-1">
            {t('notificationsTab.form.activeScheduledTimes')} <span>({state.times.length})</span>
          </p>

          <ListGroup>
            {state.times.map((time, i) => {
              return (
                <ListGroupItem
                  key={'schedule--added-time-' + i}
                >
                  <ScheduleOptions
                    id={i}
                    type={time.type}
                    item={time}
                    canDelete
                    constants={constants}
                    onChange={this.onChange}
                    onRemove={this.removeSchedule}
                  />
                </ListGroupItem>
              )
            })}
          </ListGroup>
        </div>
      </Fragment>
    )
  }
}

export default translate(['tabsContent'], { wait: true })(Scheduling)
