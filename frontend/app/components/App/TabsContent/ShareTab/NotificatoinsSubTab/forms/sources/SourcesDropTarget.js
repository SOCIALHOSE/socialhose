import React from 'react'
import PropTypes from 'prop-types'
import { compose } from 'redux'
import { DropTarget } from 'react-dnd'
import { translate } from 'react-i18next'

const target = {
  drop (props, monitor) {
    const item = monitor.getItem()
    props.addSource(item.feed)
  },

  canDrop (props, monitor) {
    return true
  }
}

export class SourcesDropTargetClass extends React.Component {
  static propTypes = {
    t: PropTypes.func.isRequired,
    connectDropTarget: PropTypes.func.isRequired,
    addSource: PropTypes.func.isRequired
  };

  render () {
    const { connectDropTarget, t } = this.props

    return connectDropTarget(
      <div className='dropzone-wrapper dropzone-wrapper-sm'>
        <p className="dropzone-content">{t('notificationsTab.form.dragFeed')}</p>
      </div>
    )
  }

}

export const SourcesDropTarget = compose(
  DropTarget('feed', target, (connect, monitor) => ({
    connectDropTarget: connect.dropTarget(),
    itemType: monitor.getItemType()
  })),
  translate(['tabsContent'], { wait: true })
)(SourcesDropTargetClass)

export default SourcesDropTarget
