import React, { Fragment, useEffect, useState } from 'react';
import PropTypes from 'prop-types';
import { Button, ButtonGroup, Col, Row } from 'reactstrap';
import ECharts from '../../../../../common/charts/ECharts';
import ChartWrapper from '../ChartWrapper';
import {
  getBarOptions,
  getPieOptions
} from '../../../../../common/charts/ChartsOptions';
import { IoIosAdd, IoIosRefresh, IoIosCheckmark } from 'react-icons/io';
import reduxConnect from '../../../../../../redux/utils/connect';
import translate from 'react-i18next/dist/commonjs/translate';
import { compose } from 'redux';
import {
  getOverviewBarAPI,
  getOverviewPieAPI
} from '../../../../../../api/analytics/createAnalytics';
import useIsMounted from '../../../../../common/hooks/useIsMounted';

const initialBar = {
  data: [],
  error: undefined,
  loading: true,
  vertical: false
};

const initialPie = { data: [], error: undefined, loading: true };

function ResultsTab(props) {
  const { actions, analyze, feedData, id, t } = props;
  const isMounted = useIsMounted();
  const [barData, setBarData] = useState(initialBar);
  const [barTimeData, setBarTimeData] = useState(initialBar);
  const [pieData, setPieData] = useState(initialPie);
  const [pieTimeData, setPieTimeData] = useState(initialPie);
  const [filter, setFilter] = useState(filtersNames[0].id);

  useEffect(() => {
    if (!id) {
      return;
    }
    if (filter === filtersNames[0].id) {
      getBarChart();
      getPieChart();
    } else {
      getBarChartFeeds();
      getPieChartFeeds();
    }
  }, [filter]);

  useEffect(() => {
    if (barData.data) {
      setBarData((prev) => ({
        ...prev,
        data: {
          ...prev.data,
          xAxis: prev.data.yAxis,
          yAxis: prev.data.xAxis
        }
      }));
    }
  }, [barData.vertical]);

  function updateResult(foo, id) {
    switch (id) {
      case cn.first:
        filter === filtersNames[0].id ? getBarChart() : getBarChartFeeds();
        return;
      case cn.second:
        filter === filtersNames[0].id ? getPieChart() : getPieChartFeeds();
        return;
    }
  }

  function getBarChart() {
    setBarData((prev) => ({ ...prev, loading: true }));
    getOverviewBarAPI(filter, id).then((res) => {
      if (!isMounted.current) {
        return false;
      }
      if (res.error || !res.data.data) {
        // on error
        setBarData((prev) => ({
          ...prev,
          loading: false,
          error: res.errorMessage
        }));
        return;
      }
      const { data } = res.data;
      const labels = data[0] ? Object.keys(data[0].data) : [];
      const datasets = data.map((item) => ({
        name: item.name,
        type: barData.vertical ? 'bar' : 'line',
        smooth: true,
        data: Object.values(item.data)
      }));

      const barOptions = getBarOptions(datasets, labels);

      setBarData({
        data: barOptions,
        error: false,
        loading: false,
        vertical: false
      });
    });
  }

  function getBarChartFeeds() {
    setBarTimeData((prev) => ({ ...prev, loading: true }));
    getOverviewBarAPI(filter, id).then((res) => {
      if (!isMounted.current) {
        return false;
      }

      if (res.error || !res.data.data) {
        // on error
        setBarTimeData((prev) => ({
          ...prev,
          loading: false,
          error: res.errorMessage
        }));
        return;
      }
      const { data } = res.data;
      const barOptions = {};
      const errors = {};

      data.map((feed) => {
        const { name, data } = feed;

        if (!data || (Array.isArray(data) && data.length < 1)) {
          errors[name] = t('analyzeTab.noData');
          return;
        }

        const labels = Object.keys(data[0].data).sort();
        const datasets = data.map((item) => ({
          name: item.name,
          type: barTimeData.vertical ? 'bar' : 'line',
          smooth: true,
          data: labels.map((v) => item.data[v])
        }));

        barOptions[name] = getBarOptions(datasets, labels);
      });

      setBarTimeData({
        data: barOptions,
        error: errors,
        loading: false,
        vertical: false
      });
    });
  }

  function getPieChart() {
    setPieData((prev) => ({ ...prev, loading: true }));
    getOverviewPieAPI(filter, id).then((res) => {
      if (!isMounted.current) {
        return false;
      }
      if (res.error || !res.data.data) {
        // alert on error
        setPieData((prev) => ({
          ...prev,
          loading: false,
          error: res.errorMessage
        }));
        return;
      }

      const { data } = res.data;
      const pieOptions = getPieOptions(
        Object.entries(data).map((v) => ({ name: v[0], value: v[1] }))
      );

      setPieData({
        data: pieOptions,
        error: false,
        loading: false
      });
    });
  }

  function getPieChartFeeds() {
    setPieTimeData((prev) => ({ ...prev, loading: true }));
    getOverviewPieAPI(filter, id).then((res) => {
      if (!isMounted.current) {
        return false;
      }
      if (res.error || !res.data.data) {
        // alert on error
        setPieTimeData((prev) => ({
          ...prev,
          loading: false,
          error: res.errorMessage
        }));
        return;
      }

      const { data } = res.data;
      const pieOptions = {};
      const errors = {};

      Object.entries(data).forEach((feed) => {
        const [name, value] = feed;

        if (!value || (Array.isArray(value) && value.length < 1)) {
          errors[name] = t('analyzeTab.noData');
        }

        pieOptions[name] = getPieOptions(
          Object.entries(value).map((v) => ({
            name: v[0],
            value: v[1]
          }))
        );
      });

      setPieTimeData({
        data: pieOptions,
        error: errors,
        loading: false
      });
    });
  }

  function changeVertical() {
    setBarData((prev) => ({ ...prev, vertical: !prev.vertical }));
  }

  const hideChart1Alert = (id) =>
    analyze.alertCharts.find((v) => v.name === cn.first && v.id === id);
  const hideChart2Alert = (id) =>
    analyze.alertCharts.find((v) => v.name === cn.second && v.id === id);

  const barchartMenus = (id) => [
    {
      title: '', // t('analyzeTab.chartMenus.addToAlert'),
      icon: IoIosAdd,
      size: 24,
      fn: () => actions.addAlertChart({ name: cn.first, id }),
      showInMore: false,
      hide: hideChart1Alert(id)
    },
    {
      title: '', // t('analyzeTab.chartMenus.addedToAlerts'),
      icon: IoIosCheckmark,
      size: 24,
      showInMore: false,
      hide: !hideChart1Alert(id)
    },
    {
      title: t('analyzeTab.chartMenus.refresh'),
      icon: IoIosRefresh,
      fn: () => updateResult(null, cn.first),
      showInMore: false
    },
    /* {
       title: t('analyzeTab.chartMenus.addToDashboard'),
      fn: () => {},
      showInMore: true
    }, */
    {
      title: 'Toggle Horizontal/Vertical',
      fn: changeVertical,
      showInMore: true
    }
  ];
  const piechartMenus = (id) => [
    {
      title: '', // t('analyzeTab.chartMenus.addToAlert'),
      icon: IoIosAdd,
      size: 24,
      fn: () => actions.addAlertChart({ name: cn.second, id }),
      showInMore: false,
      hide: hideChart2Alert(id)
    },
    {
      title: '', // t('analyzeTab.chartMenus.addedToAlerts'),
      icon: IoIosCheckmark,
      size: 24,
      showInMore: false,
      hide: !hideChart2Alert(id)
    },
    {
      title: t('analyzeTab.chartMenus.refresh'),
      icon: IoIosRefresh,
      fn: () => updateResult(null, cn.second),
      showInMore: false
    }
    // {  title: t('analyzeTab.chartMenus.addToDashboard'), fn: () => {}, showInMore: true }
  ];

  return (
    <Fragment>
      <div className="mask-line overflow-auto white-space-nowrap pl-3 mb-3">
        <ButtonGroup size="sm">
          {filtersNames.map((item) => (
            <Button
              outline
              key={item.id}
              title={item.name}
              color="secondary"
              onClick={function () {
                setFilter(item.id);
              }}
              active={filter === item.id}
            >
              {t(`analyzeTab.overviewCharts.${item.transKey}`)}
            </Button>
          ))}
        </ButtonGroup>
      </div>
      {filter === filtersNames[0].id ? ( // feeds in single graph
        <Row>
          <Col md="8">
            <ChartWrapper
              title={t('analyzeTab.charts.mentionsOverTime')}
              menus={barchartMenus('none')}
            >
              <ECharts
                xLabel={barData.labels}
                loading={barData.loading}
                options={barData.data}
              />
            </ChartWrapper>
          </Col>
          <Col md="4">
            <ChartWrapper
              title={t('analyzeTab.charts.mentions')}
              menus={piechartMenus('none')}
            >
              <ECharts loading={pieData.loading} options={pieData.data} />
            </ChartWrapper>
          </Col>
        </Row>
      ) : (
        feedData.feeds.map((feed) => (
          <Row key={feed.id}>
            <Col md="8">
              <ChartWrapper
                title={`${t('analyzeTab.charts.mentionsOverTime')} (${
                  feed.feed
                })`}
                menus={barchartMenus(feed.id)}
              >
                <ECharts
                  xLabel={barTimeData.labels}
                  loading={barTimeData.loading}
                  options={barTimeData.data && barTimeData.data[feed.feed]}
                  message={barTimeData.error && barTimeData.error[feed.feed]}
                />
              </ChartWrapper>
            </Col>
            <Col md="4">
              <ChartWrapper
                title={`${t('analyzeTab.charts.mentions')} (${feed.feed})`}
                menus={piechartMenus(feed.id)}
              >
                <ECharts
                  loading={pieTimeData.loading}
                  options={pieTimeData.data[feed.feed]}
                  message={pieTimeData.error && pieTimeData.error[feed.feed]}
                />
              </ChartWrapper>
            </Col>
          </Row>
        ))
      )}
    </Fragment>
  );
}

const cn = {
  first: 'Mentions Over Time',
  second: 'Share of Mentions'
};

const filtersNames = [
  { name: 'None', transKey: 'none', id: 'none' },
  { name: 'Media Types', transKey: 'mediaTypes', id: 'media' },
  { name: 'Sentiments', transKey: 'sentiments', id: 'sentiment' },
  // { name: 'Countries', transKey:'countries', id: 'country' },
  { name: 'Languages', transKey: 'languages', id: 'language' }
];

ResultsTab.propTypes = {
  actions: PropTypes.object,
  id: PropTypes.string,
  t: PropTypes.func,
  feedData: PropTypes.object,
  analyze: PropTypes.object
};

const applyDecorators = compose(
  reduxConnect('analyze', ['appState', 'analyze']),
  translate(['tabsContent'], { wait: true })
);

export default applyDecorators(React.memo(ResultsTab));
