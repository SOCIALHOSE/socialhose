import React, { Fragment } from 'react'
import PropTypes from 'prop-types'
import { translate } from 'react-i18next'
import Toggler from '../../../../../common/Table/Toggler'
import { Button, Card, CardBody, CardTitle, Label } from 'reactstrap'
import { convertUTCtoLocal } from '../../../../../../common/helper'

export class FormTopBar extends React.Component {
  static propTypes = {
    t: PropTypes.func.isRequired,
    formType: PropTypes.string.isRequired,
    receiver: PropTypes.object.isRequired,
    actions: PropTypes.object.isRequired
  }

  togglerAction = () => {
    const { actions, formType } = this.props
    actions.shareForms[formType].toggleActive()
  }

  onBack = () => {
    this.props.actions.switchShareSubScreen('recipients', 'tables')
  }

  onDelete = () => {
    const { formType, actions } = this.props
    actions.shareForms[formType].confirmDelete()
  }

  onSave = () => {
    const { actions, formType } = this.props
    actions.shareForms[formType].saveReceiver()
  }

  render() {
    const { t, formType, receiver } = this.props
    const hasItem = !!receiver.id
    const trPath = 'manageRecipientsTab.form'
    let title = t(`${trPath}.${formType}.unsaved`)
    if (hasItem) {
      title =
        formType === 'group'
          ? receiver.name
          : `${receiver.firstName} ${receiver.lastName}`
    }

    return (
      <Fragment>
        <Button
          className="btn-wide mb-3"
          size="sm"
          color="info"
          onClick={this.onBack}
        >
          <i className="lnr lnr-chevron-left"> </i>
        </Button>

        <Card className="main-card mb-3">
          <CardBody>
            <CardTitle>{title}</CardTitle>

            <div className="d-flex justify-content-between flex-wrap align-items-center">
              <div>
                <Label className="mr-2">
                  {t(`${trPath}.${formType}.nameStatus`)}
                </Label>
                <Toggler
                  id={receiver.id}
                  turnOnAction={this.togglerAction}
                  turnOffAction={this.togglerAction}
                  state={receiver.active}
                  enabledText="active"
                  disabledText="paused"
                />
              </div>
              <div>
                {hasItem && (
                  <Button
                    color="danger"
                    className="btn-icon mr-2"
                    onClick={this.onDelete}
                  >
                    <i className="lnr lnr-trash btn-icon-wrapper"></i>
                    {t(`${trPath}.${formType}.deleteButton`)}
                  </Button>
                )}
                <Button
                  color="secondary"
                  className="btn-icon mr-2"
                  onClick={this.onBack}
                >
                  <i className="lnr lnr-cross btn-icon-wrapper"></i>
                  {t(`${trPath}.cancel`)}
                </Button>
                <Button
                  color="success"
                  className="btn-icon mr-2"
                  onClick={this.onSave}
                >
                  <i className="lnr lnr-checkmark-circle btn-icon-wrapper" />
                  {t(`${trPath}.save`)}
                </Button>
              </div>
            </div>

            {hasItem && receiver.creationDate && (
              <p className="mt-1">
                {t(`${trPath}.${formType}.creationDate`)}:
                {convertUTCtoLocal(receiver.creationDate, 'DD MMM YYYY HH:mm')}
              </p>
            )}
          </CardBody>
        </Card>
      </Fragment>
    )
  }
}

export default translate(['tabsContent'], { wait: true })(FormTopBar)
