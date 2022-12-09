import React from 'react';
import PropTypes from 'prop-types';
import { translate, Interpolate } from 'react-i18next';
import ReactTable from 'react-table';
import 'react-table/react-table.css';
import Pager from '../Pager/Pager';
import { Row, Col, Card, CardBody, CardTitle } from 'reactstrap';
import LoadersAdvanced from '../Loader/Loader';

// const steps = [
//   { name: 'Account Information' },
//   { name: 'Payment Information' },
//   { name: 'Finish Wizard' }
// ]

export class Table extends React.Component {
  static propTypes = {
    t: PropTypes.func,
    data: PropTypes.array.isRequired,
    columns: PropTypes.array.isRequired,
    totalCount: PropTypes.number.isRequired,
    showTotalCount: PropTypes.bool,
    noCard: PropTypes.bool,
    limit: PropTypes.number.isRequired,
    page: PropTypes.number.isRequired,
    isLoading: PropTypes.bool.isRequired,
    onFetchData: PropTypes.func.isRequired,
    onRowClick: PropTypes.func,
    cardTitle: PropTypes.string
  };

  onFetchData = (state) => {
    this.props.onFetchData(
      state.page,
      state.pageSize,
      state.sorted,
      state.filtered
    );
  };

  onPageAction = (pageState) => {
    const { totalCount } = this.props;
    const gridState = this.refs.grid.state;
    const { page, pageSize, sorted, filtered } = gridState;
    let state = { page, pageSize, sorted, filtered };

    if (pageState.limitByPage) {
      state.pageSize = pageState.limitByPage;
    }
    if (pageState.currentPage) {
      state.page = pageState.currentPage - 1;
    }
    if (totalCount < state.pageSize) {
      state.page = 0;
    }

    this.onFetchData(state);
  };

  getPagination = () => {
    const { showTotalCount = false, totalCount, page, limit } = this.props;
    const numPages = Math.ceil(totalCount / limit);

    return totalCount > 0 ? (
      <div className="px-3">
        {showTotalCount && (
          <div className="results-table-count-info">
            <Interpolate
              i18nKey="sourceIndexTab.showingCounter"
              startCount={limit * page - (limit - 1)}
              endCount={limit * page}
              totalCount={totalCount}
            />
          </div>
        )}

        <Pager
          pagerAction={this.onPageAction}
          currentPage={page}
          limitByPage={limit}
          numPages={numPages}
        />
      </div>
    ) : null;
  };

  getLoading = (props) => {
    if (!props.loading) return null;

    return <LoadersAdvanced />;
    // <div className="component-loader"></div>;
  };

  getTrProps = (state, rowInfo, column, instance) => {
    const { onRowClick } = this.props;
    let result = {};
    if (onRowClick) {
      result.onClick = (e) => {
        onRowClick(e, state, rowInfo, column, instance);
      };
    }
    return result;
  };

  NoDataConst = () => (
    <div className="p-4 text-center text-black-50">
      {this.props.t('common:messages.noRows', {
        defaultValue: 'No rows found'
      })}
    </div>
  );

  render() {
    const {
      data,
      columns,
      page,
      limit,
      isLoading,
      cardTitle,
      noCard
    } = this.props;

    const renderTable = (
      <ReactTable
        ref="grid"
        className="cw-grid -striped"
        data={data}
        columns={columns}
        minRows={0}
        manual
        page={page - 1}
        pageSize={limit}
        defaultPageSize={10}
        loading={isLoading}
        PaginationComponent={this.getPagination}
        LoadingComponent={this.getLoading}
        onFetchData={this.onFetchData}
        getTrProps={this.getTrProps}
        NoDataComponent={this.NoDataConst}
      />
    );

    if (noCard) {
      return renderTable;
    }

    return (
      <Row>
        <Col md="12">
          <Card className="main-card mb-3">
            <CardBody>
              {cardTitle && <CardTitle>{cardTitle}</CardTitle>}
              {renderTable}
            </CardBody>
          </Card>
        </Col>
      </Row>
    );
  }
}

export default translate(['tabsContent'], { wait: true })(Table);
