import React, { useEffect, useState } from 'react';
import PropTypes from 'prop-types';
import { Col, Row } from 'reactstrap';
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

function Sentiment(props) {
  const { actions, analyze, feedData, id, t } = props;
  const isMounted = useIsMounted();
  const [barData, setBarData] = useState(initialBar);
  const [pieData, setPieData] = useState(initialPie);

  useEffect(() => {
    if (!id) {
      return;
    }
    getBarChart();
    getPieChart();
  }, []);

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
        getBarChart();
        return;
      case cn.second:
        getPieChart();
        return;
    }
  }

  function getBarChart() {
    setBarData((prev) => ({ ...prev, loading: true }));
    getOverviewBarAPI('sentiment', id).then((res) => {
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
      const barOptions = {};
      data.forEach((feed) => {
        const { name, data } = feed;
        const labels = Object.keys(data[0].data).sort();
        const datasets = data.map((item) => ({
          name: item.name,
          type: barData.vertical ? 'bar' : 'line',
          smooth: true,
          data: labels.map((v) => item.data[v])
        }));

        barOptions[name] = getBarOptions(datasets, labels);
      });

      setBarData({
        data: barOptions,
        error: false,
        loading: false,
        vertical: false
      });
    });
  }

  function getPieChart() {
    setPieData((prev) => ({ ...prev, loading: true }));
    getOverviewPieAPI('sentiment', id).then((res) => {
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
      const pieOptions = {};
      Object.entries(data).forEach((feed) => {
        const [name, value] = feed;
        pieOptions[name] = getPieOptions(
          Object.entries(value).map((v) => ({
            name: v[0],
            value: v[1]
          }))
        );
      });

      setPieData({
        data: pieOptions,
        error: false,
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

  return feedData.feeds.map((feed) => (
    <Row key={feed.id}>
      <Col md="8">
        <ChartWrapper
          title={`${t('analyzeTab.charts.sentimentOverTime')} (${feed.feed})`}
          menus={barchartMenus(feed.id)}
        >
          <ECharts
            xLabel={barData.labels}
            loading={barData.loading}
            options={barData.data[feed.feed]}
          />
        </ChartWrapper>
      </Col>
      <Col md="4">
        <ChartWrapper
          title={`${t('analyzeTab.charts.shareofSentiment')} (${feed.feed})`}
          menus={piechartMenus(feed.id)}
        >
          <ECharts
            loading={pieData.loading}
            options={pieData.data[feed.feed]}
          />
        </ChartWrapper>
      </Col>
    </Row>
  ));
}

const cn = {
  first: 'Sentiment Over Time',
  second: 'Share of Sentiment'
};

Sentiment.propTypes = {
  actions: PropTypes.object,
  feedData: PropTypes.object,
  analyze: PropTypes.object,
  t: PropTypes.func
};

const applyDecorators = compose(
  reduxConnect('analyze', ['appState', 'analyze']),
  translate(['tabsContent'], { wait: true })
);

export default applyDecorators(React.memo(Sentiment));
