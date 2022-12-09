import React from 'react'
import PropTypes from 'prop-types'
import { translate } from 'react-i18next'
import classnames from 'classnames'

export class FilterItem extends React.Component {

  static propTypes = {
    t: PropTypes.func.isRequired,
    title: PropTypes.string.isRequired,
    value: PropTypes.string.isRequired,
    count: PropTypes.number.isRequired,
    onItemClick: PropTypes.func.isRequired,
    selectionState: PropTypes.number
  };

  onItemClick = () => {
    const { onItemClick, value } = this.props
    onItemClick(value)
  };

  render () {
    const { selectionState, title, count } = this.props
    const mainClass = 'filters-table__item'
    const classes = classnames(mainClass, {
      [`${mainClass}--included`]: selectionState === 1,
      [`${mainClass}--excluded`]: selectionState === -1
    })

    return (
      <div className={classes} onClick={this.onItemClick}>
        {title}
        <span>{count}</span>
      </div>
    )
  }

}

export default translate(['tabsContent'], { wait: true })(FilterItem)
