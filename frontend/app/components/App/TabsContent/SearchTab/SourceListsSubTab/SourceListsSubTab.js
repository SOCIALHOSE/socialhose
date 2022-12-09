import React, { Fragment } from 'react'
import PropTypes from 'prop-types'
import SourceLists from './SourceLists'
import SourcesOfList from './SourcesOfList'
import { withRouter } from 'react-router-dom'
import reduxConnect from '../../../../../redux/utils/connect'
import { compose } from 'redux'
import { setDocumentData } from '../../../../../common/helper'

class SourceListsSubTab extends React.Component {
  static propTypes = {
    sourcesState: PropTypes.object.isRequired,
    actions: PropTypes.object.isRequired
  }

  componentDidMount() {
    setDocumentData('title', 'Source Lists | Search')
  }

  componentWillUnmount() {
    setDocumentData('title')
  }

  render() {
    const { sourcesState, actions } = this.props
    const { sourcesOfListState, sourceListsState } = sourcesState
    const sourcesOfListVisible = sourcesOfListState.isSourcesOfListVisible

    return (
      <Fragment>
        {!sourcesOfListVisible && (
          <SourceLists sourceListsState={sourceListsState} actions={actions} />
        )}

        {sourcesOfListVisible && (
          <SourcesOfList
            sourcesOfListState={sourcesOfListState}
            actions={actions}
          />
        )}
      </Fragment>
    )
  }
}

const applyDecorators = compose(
  withRouter,
  reduxConnect('sourcesState', ['appState', 'sourcesState'])
)

export default applyDecorators(SourceListsSubTab)
