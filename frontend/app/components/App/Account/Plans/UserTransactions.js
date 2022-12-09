/* eslint-disable react/jsx-no-bind */
/* eslint-disable react/prop-types */
import React, { useState, useCallback, useEffect } from 'react';
import PropTypes from 'prop-types';
import { reduxActions } from '../../../../redux/utils/connect';
import {
  convertUTCtoLocal,
  getQueryParams,
  setDocumentData
} from '../../../../common/helper';
import { getTransactions } from '../../../../api/plans/userPlans';
import Table from '../../../common/Table/Table';
import { Button, Col } from 'reactstrap';
import ShowTransactionDetails from './ShowTransactionDetails';
import moment from 'moment';
import { capitalize } from 'lodash';
import { translate } from 'react-i18next';

function UserTransactions(props) {
  const [dataSource, setDataSource] = useState({ data: [] });
  const [loading, setLoading] = useState(true);
  const [selectedData, setSelectedData] = useState(false);
  const { actions, t } = props;

  useEffect(() => {
    setDocumentData('title', 'User Transactions');

    return () => setDocumentData('title'); // default
  }, []);

  const columns = [
    {
      id: 'activeDate',
      Header: t('plans.transactions.activationDate'),
      accessor: (d) => d.lines.data[0] && d.lines.data[0].period.start,
      Cell: (props) => convertUTCtoLocal(moment.unix(props.value), 'MM/DD/YYYY')
    },
    {
      id: 'expireDate',
      Header: t('plans.transactions.expirationDate'),
      accessor: (d) => d.lines.data[0] && d.lines.data[0].period.end,
      Cell: (props) => convertUTCtoLocal(moment.unix(props.value), 'MM/DD/YYYY')
    },
    {
      id: 'paid_at',
      Header: t('plans.transactions.transactionDate'),
      accessor: (d) => d.status_transitions.paid_at,
      Cell: (props) =>
        convertUTCtoLocal(moment.unix(props.value), 'MM/DD/YYYY HH:mm:ss')
    },
    {
      Header: t('plans.transactions.amount'),
      accessor: 'amount_paid',
      Cell: (props) => (props.value ? `$${props.value / 100}` : '-')
    },
    {
      Header: t('plans.transactions.status'),
      accessor: 'status',
      Cell: (props) => capitalize(props.value)
    },
    {
      Header: t('plans.transactions.actions'),
      accessor: 'id',
      Cell: (props) => (
        <Button
          outline
          className="border-0 btn-transition"
          color="primary"
          size="sm"
          onClick={() => setSelectedData(props.original)}
        >
          {t('plans.transactions.more')}
        </Button>
      )
    }
  ];

  function closeModal() {
    setSelectedData(false);
  }

  const getTransactionList = useCallback((page, pageSize) => {
    setLoading(true);
    const params = getQueryParams({ page, pageSize });
    getTransactions(params).then((res) => {
      if (res.error || !res.data || !res.data.success || !res.data.data) {
        setDataSource({ data: [] }); // comment this line when API is ready
        setLoading(false);
        return actions.addAlert({
          type: 'error',
          transKey: 'somethingWrong'
        });
      }

      // setDataSource(sampleData); // comment this line when API is ready
      setDataSource({
        data:
          res.data.data.data && res.data.data.data.length > 0
            ? res.data.data.data
            : []
      });
      setLoading(false);
    });
  }, []);

  const { data = [], totalCount = 0, limit = 100, page = 1 } = dataSource;
  return (
    <Col xs="12" lg="8" xl="9">
      <Table
        cardTitle={t('plans.transactions.heading')}
        columns={columns}
        data={data}
        totalCount={totalCount}
        showTotalCount
        limit={limit}
        page={page}
        isLoading={loading}
        onFetchData={getTransactionList}
      />
      <ShowTransactionDetails data={selectedData} closeModal={closeModal} />
    </Col>
  );
}

UserTransactions.propTypes = {
  t: PropTypes.func.isRequired,
  actions: PropTypes.object
};

export default reduxActions()(
  translate(['tabsContent'], { wait: true })(UserTransactions)
);
