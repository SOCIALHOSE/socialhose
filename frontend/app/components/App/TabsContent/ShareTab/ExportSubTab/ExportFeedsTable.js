import React from 'react'
import PropTypes from 'prop-types'
import { translate } from 'react-i18next'
import ExportFeedsTableRow from './ExportFeedsTableRow'
import LoadersAdvanced from '../../../../common/Loader/Loader'
import { Table, Card, CardBody } from 'reactstrap'

class ExportFeedsTable extends React.Component {
  static propTypes = {
    t: PropTypes.func.isRequired,
    isLoading: PropTypes.bool.isRequired,
    tableData: PropTypes.array.isRequired,
    showPopup: PropTypes.func.isRequired,
    unexportFeed: PropTypes.func.isRequired,
    goToFeed: PropTypes.func.isRequired
  }

  render() {
    const {
      tableData,
      isLoading,
      showPopup,
      unexportFeed,
      goToFeed,
      t
    } = this.props

    return (
      <Card className="main-card mb-3">
        {isLoading && <LoadersAdvanced />}
        <CardBody>
          <Table striped bordered className="mb-0">
            <thead>
              <tr>
                <th>{t('exportTab.feedName')}</th>
                <th>{t('exportTab.exportWith')}</th>
                <th>{t('exportTab.actions')}</th>
              </tr>
            </thead>
            <tbody>
              {tableData.map((feed) => {
                return (
                  <ExportFeedsTableRow
                    key={feed.id}
                    feed={feed}
                    showPopup={showPopup}
                    unexportFeed={unexportFeed}
                    goToFeed={goToFeed}
                  />
                )
              })}
            </tbody>
          </Table>
        </CardBody>
      </Card>
    )
  }
}

export default translate(['tabsContent'], { wait: true })(ExportFeedsTable)
