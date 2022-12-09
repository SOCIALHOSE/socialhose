import React from 'react'
import PropTypes from 'prop-types'
import classnames from 'classnames'
import { Button, Collapse, ListGroup, ListGroupItem } from 'reactstrap'
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome'
import { faSpinner } from '@fortawesome/free-solid-svg-icons'
import { translate } from 'react-i18next'
import { convertUTCtoLocal } from '../../../../../../common/helper'

export class History extends React.Component {
  static propTypes = {
    t: PropTypes.func.isRequired,
    notificationId: PropTypes.number.isRequired,
    state: PropTypes.object.isRequired,
    actions: PropTypes.object.isRequired
  }

  onToggle = () => {
    const { state, actions } = this.props
    if (!state.isOpen && !state.isLoadingCompleted) {
      this.showMore()
    }
    actions.toggleHistory()
  }

  showMore = () => {
    const { notificationId, state, actions } = this.props
    actions.getHistory(notificationId, state.page + 1, state.limit)
  }

  render() {
    const { state, t } = this.props
    const isOpen = state.isOpen
    const label = isOpen ? t('notificationsTab.history.hideSendHistory') : t('notificationsTab.history.showSendHistory')
    const iconClasses = classnames('lnr mr-2', {
      'lnr-chevron-right': !isOpen,
      'lnr-chevron-down': isOpen
    })

    return (
      <div className="history">
        <Button
          color="link"
          className="p-0 font-size-md"
          onClick={this.onToggle}
        >
          <i className={iconClasses} />
          {label}
        </Button>
        <Collapse isOpen={isOpen}>
          {state.isLoadingCompleted && (
            <div className="mt-3 ml-4">
              <p className="text-muted mb-1">{t('notificationsTab.history.sentTime')}</p>
              <ListGroup>
                {state.entities.map((entity, i) => (
                  <ListGroupItem
                    className="col-sm-6 p-2"
                    key={`history-date-${i}`}
                  >
                    {convertUTCtoLocal(entity.date, 'DD MMM YYYY HH:mm')}
                  </ListGroupItem>
                ))}
              </ListGroup>
              {!state.isPending && state.entities.length < state.totalCount && (
                <Button color="link" onClick={this.showMore}>
                  {t('notificationsTab.history.showMore')}
                </Button>
              )}
            </div>
          )}
          {state.isPending && (
            <p className="ml-4 mt-3">
              <FontAwesomeIcon icon={faSpinner} className="mr-2" pulse /> {t('notificationsTab.history.loading')}
            </p>
          )}
        </Collapse>
      </div>
    )
  }
}

export default translate(['tabsContent'], { wait: true })(History)
