import React, { Fragment } from 'react'
import PropTypes from 'prop-types'
import { translate } from 'react-i18next'
import classnames from 'classnames'
import { Button, Modal, ModalHeader, ModalBody, ModalFooter } from 'reactstrap'

export class PopupLayout extends React.Component {
  static propTypes = {
    className: PropTypes.string,
    children: PropTypes.element.isRequired,
    title: PropTypes.string,
    showFooter: PropTypes.bool,
    footer: PropTypes.element,
    cancelText: PropTypes.string,
    submitText: PropTypes.string,
    submitColor: PropTypes.string,
    onHide: PropTypes.func.isRequired,
    onSubmit: PropTypes.func,
    t: PropTypes.func.isRequired
  };

  hidePopup = () => {
    this.props.onHide()
  };

  onSubmit = () => {
    const { onSubmit } = this.props
    onSubmit() && this.hidePopup()
  };

  getHeader () {
    const { t, title = 'common:commonWords.Confirm' } = this.props
    return (
      <ModalHeader toggle={this.hidePopup}>
        {t(title)}
      </ModalHeader>
    )
  }

  getFooter () {
    const {
      t,
      footer,
      showFooter = true,
      cancelText = 'common:commonWords.Cancel',
      submitText = 'common:commonWords.Confirm',
      submitColor = 'primary'
    } = this.props
    if (!showFooter) return null

    const hasFooter = !!footer

    return (
      <ModalFooter>
        {hasFooter && footer}
        {!hasFooter && (
          <Fragment>
            <Button color="light" onClick={this.hidePopup}>
              {t(cancelText)}
            </Button>
            <Button color={submitColor} onClick={this.onSubmit}>
              {t(submitText)}
            </Button>
          </Fragment>
        )}
      </ModalFooter>
    )
  }

  render () {
    const { children, className } = this.props
    const classes = classnames('popup', className)

    return (
      <div className={classes}>
        <Modal isOpen toggle={this.hidePopup} backdrop="static">
          {this.getHeader()}
          <ModalBody>{children}</ModalBody>
          {this.getFooter()}
        </Modal>
      </div>
    )
  }
}

export default translate(['tabsContent', 'common'], { wait: true })(
  PopupLayout
)
