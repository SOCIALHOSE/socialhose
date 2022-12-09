import React from 'react'
import PropTypes from 'prop-types'
import { translate } from 'react-i18next'
import ExportFeedsTable from './ExportFeedsTable'
import ExportPopup from './ExportPopup'
import { withRouter } from 'react-router-dom'
import reduxConnect from '../../../../../redux/utils/connect'
import { compose } from 'redux'
import { setDocumentData } from '../../../../../common/helper'

class ExportSubTab extends React.Component {
  static propTypes = {
    exportFeedsState: PropTypes.object.isRequired,
    actions: PropTypes.object.isRequired,
    history: PropTypes.object.isRequired,
    t: PropTypes.func.isRequired
  };

  componentDidMount() {
    setDocumentData('title', 'Export | Share')
  }

  componentWillUnmount() {
    setDocumentData('title')
  }

  componentWillMount = () => {
    this.props.actions.loadExportedFeeds()
  };

  goToFeed = (feedId) => {
    const {
      history,
      actions: { getFeedResults }
    } = this.props
    history.push('/app/search/search')
    getFeedResults({ page: 1 }, feedId)
  };

  render () {
    const { t, exportFeedsState, actions } = this.props
    return (
      <div>
        <p className="text-muted mb-3">{t('exportTab.topMessage')}</p>

        <ExportFeedsTable
          isLoading={exportFeedsState.isLoading}
          tableData={exportFeedsState.tableData}
          showPopup={actions.showExportPopup}
          unexportFeed={actions.unexportFeed}
          goToFeed={this.goToFeed}
        />

        {exportFeedsState.popupVisible && (
          <ExportPopup
            feed={exportFeedsState.selectedFeed}
            hidePopup={actions.hideExportPopup}
            exportFormat={exportFeedsState.exportFormat}
          />
        )}
      </div>
    )
  }
}

const applyDecorators = compose(
  withRouter,
  reduxConnect('exportFeedsState', ['appState', 'share', 'exportFeeds']),
  translate(['tabsContent'], { wait: true })
)
export default applyDecorators(ExportSubTab)
