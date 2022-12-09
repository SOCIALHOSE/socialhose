import React, { useEffect, useState } from 'react'
import PropTypes from 'prop-types'
import { Modal } from 'reactstrap'

function ModalPopup(props) {
  const { children, modalProps, show, hideModal, handled } = props
  const [open, setOpen] = useState(true)

  useEffect(() => setOpen(false)) // when unmounts

  function toggle() {
    setOpen((prev) => !prev)
  }

  return (
    <Modal
      isOpen={show || open}
      toggle={handled ? hideModal : toggle}
      backdrop="static"
      {...modalProps}
    >
      {children && (handled ? children : children(toggle, open))}
    </Modal>
  )
}

ModalPopup.propTypes = {
  handled: PropTypes.bool,
  show: PropTypes.bool,
  hideModal: PropTypes.func,
  children: PropTypes.func.isRequired,
  modalProps: PropTypes.object
}

export default React.memo(ModalPopup)
