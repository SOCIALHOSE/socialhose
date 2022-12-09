import React from 'react'
import PropTypes from 'prop-types'
import { translate } from 'react-i18next'

export class SortableTh extends React.Component {
  static propTypes = {
    t: PropTypes.func.isRequired,
    title: PropTypes.string.isRequired
  };

  render () {
    const { t, title } = this.props

    return (
      <div className="sortable-th">
        {t(title)}

        <i className="sorting-icon fa fa-sort" />
        <i className="sorting-icon fa fa-sort-asc" />
        <i className="sorting-icon fa fa-sort-desc" />
      </div>
    )
  }
}

export default translate(['tabsContent'], { wait: true })(SortableTh)
