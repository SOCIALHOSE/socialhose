import React, { useEffect, useState } from 'react';
import PropTypes from 'prop-types';
import { Col, Row } from 'reactstrap';
import ECharts from '../../../../../common/charts/ECharts';
import 'echarts-wordcloud';
import { capitalize } from 'lodash';
import ChartWrapper from '../ChartWrapper';
import {
  getBarOptions,
  PieToolbox,
  WordCloudOptions
} from '../../../../../common/charts/ChartsOptions';
import { IoIosAdd, IoIosRefresh, IoIosCheckmark } from 'react-icons/io';
import reduxConnect from '../../../../../../redux/utils/connect';
import translate from 'react-i18next/dist/commonjs/translate';
import { compose } from 'redux';
import {
  getThemesCloudAPI,
  getThemesTimeAPI
} from '../../../../../../api/analytics/createAnalytics';
import useIsMounted from '../../../../../common/hooks/useIsMounted';
import { capFirstLetter } from '../../../../../../common/helper';

const initialBar = {
  data: [],
  error: undefined,
  loading: true,
  vertical: false
};

const initialPie = { data: [], error: undefined, loading: true };

function Themes(props) {
  const { actions, analyze, feedData, id, t } = props;
  const isMounted = useIsMounted();
  const [barData, setBarData] = useState(initialBar);
  const [wordData, setWordData] = useState(initialPie);

  useEffect(() => {
    if (!id) {
      return;
    }
    getBarChart();
    getWordCloud();
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
        getWordCloud();
        return;
    }
  }

  function getBarChart() {
    setBarData((prev) => ({ ...prev, loading: true }));
    getThemesTimeAPI(id).then((res) => {
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
      let labels = null;
      const barOptions = {};
      const errors = {};
      data.forEach((feedData) => {
        const { name, data } = feedData;
        const datasets = data.map((item) => ({
          name: capitalize(item.name),
          type: barData.vertical ? 'bar' : 'line',
          smooth: true,
          data: Object.values(item.data)
        }));

        if (!labels && data && data[0] && data[0].data) {
          labels = Object.keys(data[0].data);
        }

        barOptions[name] = getBarOptions(datasets, labels);

        if (!datasets || (Array.isArray(datasets) && datasets.length < 1)) {
          errors[name] = t('analyzeTab.noData');
        }
      });

      setBarData({
        data: barOptions,
        error: errors,
        loading: false,
        vertical: false
      });
    });
  }

  function getWordCloud() {
    setWordData((prev) => ({ ...prev, loading: true }));
    getThemesCloudAPI(id).then((res) => {
      if (!isMounted.current) {
        return false;
      }
      if (res.error || !res.data.data) {
        // alert on error
        setWordData((prev) => ({
          ...prev,
          loading: false,
          error: res.errorMessage
        }));
        return;
      }

      const { data } = res.data;
      const cloudOptions = {};
      const errors = {};
      data.forEach((feed) => {
        const { name, data } = feed;
        if (!data || (Array.isArray(data) && data.length < 1)) {
          errors[name] = t('analyzeTab.noData');
        }

        cloudOptions[name] = {
          tooltip: {
            show: true
          },
          toolbox: PieToolbox,
          series: [
            {
              ...WordCloudOptions,
              data: Object.entries(data).map((v) => ({
                name: capFirstLetter(v[0]),
                value: v[1]
              }))
            }
          ]
        };
      });
      setWordData({
        data: cloudOptions,
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
    /*   {
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
  const wordCloudMenus = (id) => [
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
          title={`${t('analyzeTab.charts.themesOverTime')} (${feed.feed})`}
          menus={barchartMenus(feed.id)}
        >
          <ECharts
            xLabel={barData.labels}
            loading={barData.loading}
            options={barData.data[feed.feed]}
            message={barData.error && barData.error[feed.feed]}
          />
        </ChartWrapper>
      </Col>
      <Col md="4">
        <ChartWrapper
          title={`${t('analyzeTab.charts.topThemes')} (${feed.feed})`}
          menus={wordCloudMenus(feed.id)}
        >
          <ECharts
            loading={wordData.loading}
            options={wordData.data[feed.feed]}
            message={barData.error && barData.error[feed.feed]}
          />
        </ChartWrapper>
      </Col>
    </Row>
  ));
}

const cn = {
  first: 'Themes over time',
  second: 'Top Themes'
};

Themes.propTypes = {
  chartData: PropTypes.object,
  actions: PropTypes.object,
  feedData: PropTypes.object,
  t: PropTypes.func,
  analyze: PropTypes.object
};

const applyDecorators = compose(
  reduxConnect('analyze', ['appState', 'analyze']),
  translate(['tabsContent'], { wait: true })
);

export default applyDecorators(React.memo(Themes));
