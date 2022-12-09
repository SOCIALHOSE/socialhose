import React from 'react'
import PropTypes from 'prop-types'
import { translate } from 'react-i18next'

export class TableHeaderSortItem extends React.Component {

  static propTypes = {
    t: PropTypes.func.isRequired,
    isSorted: PropTypes.bool.isRequired,
    sortDirection: PropTypes.string.isRequired,
    title: PropTypes.string.isRequired,
    width: PropTypes.string,
    sortAction: PropTypes.func.isRequired,
    fieldName: PropTypes.string.isRequired,
    textAlign: PropTypes.string
  };

  onClick = () => {
    const sortDirection = (!this.props.isSorted || this.props.sortDirection === 'desc') ? 'asc' : 'desc'
    this.props.sortAction({sortByField: this.props.fieldName, sortDirection: sortDirection})
  };

  render () {
    const {t, isSorted, sortDirection} = this.props
    return (
      <th style={{
        textAlign: this.props.textAlign || '',
        width: this.props.width || 'auto'
      }}>

        {t(this.props.title)}

        {!isSorted &&
          <i className="sorting-icon fa fa-sort" onClick={this.onClick}></i>
        }

        {isSorted && sortDirection === 'asc' &&
          <i className="sorting-icon fa fa-sort-asc" onClick={this.onClick}></i>
        }

        {isSorted && sortDirection === 'desc' &&
          <i className="sorting-icon fa fa-sort-desc" onClick={this.onClick}></i>
        }

      </th>
    )
  }

}

export default translate(['tabsContent'], { wait: true })(TableHeaderSortItem)
