import React, {
  useState,
  useCallback,
  Fragment,
  useEffect,
  useMemo
} from 'react';
import PropTypes from 'prop-types';
import cx from 'classnames';
import {
  NavLink,
  Redirect,
  Route,
  Switch,
  useHistory,
  useParams
} from 'react-router-dom';
import {
  Button,
  DropdownItem,
  DropdownMenu,
  DropdownToggle,
  UncontrolledDropdown
} from 'reactstrap';
import { IoIosTrash } from 'react-icons/io';

import {
  Results,
  Performance,
  Influencers,
  Sentiment,
  Themes,
  Demographics
  // WorldMap
} from './Tabs';
import AlertDialog from './AlertDialog';
import reduxConnect from '../../../../../redux/utils/connect';
import translate from 'react-i18next/dist/commonjs/translate';
import { compose } from 'redux';
import { getAnalyticDetailsAPI } from '../../../../../api/analytics/createAnalytics';
import useIsMounted from '../../../../common/hooks/useIsMounted';
import { setDocumentData } from '../../../../../common/helper';
import { Interpolate } from 'react-i18next';

// exported for routing
export const subChartCategories = [
  {
    title: 'Overview',
    transKey: 'overview',
    path: 'overview',
    component: Results
  },
  {
    title: 'Performance',
    transKey: 'performance',
    path: 'performance',
    component: Performance
  },
  {
    title: 'Influencers',
    transKey: 'influencers',
    path: 'influencers',
    component: Influencers
  },
  {
    title: 'Sentiment',
    transKey: 'sentiment',
    path: 'sentiment',
    component: Sentiment
  },
  { title: 'Themes', transKey: 'themes', path: 'themes', component: Themes },
  {
    title: 'Demographics',
    transKey: 'demographics',
    path: 'demographics',
    component: Demographics
  }
  // { title: 'World Map', transKey: 'worldMap', path: 'worldmap', component: WorldMap }
];

function ShowCharts({ analyze, actions, t }) {
  const isMounted = useIsMounted();
  const history = useHistory();
  const params = useParams();
  const [chartData, setChartData] = useState({});
  const [alertModal, setAlertModal] = useState(false);
  const [fetching, setFetching] = useState(true);
  const [feedData, setFeedData] = useState(null);

  const { removeAlertChart, resetAlertChart } = actions;
  const { alertCharts } = analyze;

  useEffect(() => {
    setDocumentData('title', 'View Analysis | Analyze');
    return () => {
      setDocumentData('title');
    };
  }, []);

  useEffect(() => {
    if (!params.id || isNaN(params.id)) {
      history.push('/app/analyze/saved');
    } else {
      getAnalyticData();
    }

    return () => resetAlertChart(); // reset store
  }, [params.id]);

  const updateResult = useCallback((data, chartName) => {
    setChartData((prev) => ({ ...prev, [chartName]: data }));
  }, []);

  const subChartRoutes = useMemo(() => {
    return subChartCategories.map(({ path, component: SubChart }) => (
      <Route exact key={path} path={`/app/analyze/${params.id}/${path}`}>
        <SubChart
          id={params.id}
          feedData={feedData}
          chartData={chartData}
          updateResult={updateResult}
        />
      </Route>
    ));
  }, [updateResult, chartData, feedData, params.id]);

  function getAnalyticData() {
    setFetching(true);
    getAnalyticDetailsAPI(params.id).then((res) => {
      if (!isMounted.current) {
        return;
      }
      if (res.error || !res.data || !res.data.context) {
        setFetching(false);
        res.data
          ? actions.addAlert(res.data)
          : actions.addAlert({ type: 'error', transKey: 'somethingWrong' });
        history.push('/app/analyze/saved');
        return;
      }

      const { context } = res.data;
      const date = context && context.rawFilters && context.rawFilters.date;
      setFeedData({
        feeds: context.feeds.map((item) => ({
          feed: item.name,
          id: item.id
        })),
        startDate: date && date.start,
        endDate: date && date.end
      });
      setFetching(false);
    });
  }

  function toggleModal() {
    setAlertModal((prev) => !prev);
  }

  if (fetching) {
    return 'Loading...';
  }

  const isRTL = document.documentElement.dir === 'rtl';
  return (
    <Fragment>
      <div
        className="d-flex"
        style={{ position: 'absolute', top: 0, right: 0 }}
      >
        {alertCharts && alertCharts.length > 0 && (
          <UncontrolledDropdown className="d-inline-block">
            <DropdownToggle color="info" className="btn-shadow" caret>
              <Interpolate
                t={t}
                i18nKey="analyzeTab.createAlert"
                alertsLength={alertCharts.length}
              />
            </DropdownToggle>
            <DropdownMenu
              className={`dropdown-menu-right rm-pointers dropdown-menu-shadow dropdown-menu-hover-link${
                isRTL ? ' dropdown-menu-left' : ''
              }`}
            >
              <DropdownItem header>
                {t('analyzeTab.selectedCharts')}
              </DropdownItem>
              {alertCharts.map((chart, i) => (
                <div className="dropdown-item" key={`${chart.name}_${i}}`}>
                  <span>
                    {chart.name}
                    {isNaN(chart.id) ? '' : ` (#${chart.id})`}
                  </span>
                  <Button
                    className="btn-icon btn-icon-only ml-auto mr-2 p-1"
                    color="danger"
                    onClick={function () {
                      removeAlertChart({ name: chart.name, id: chart.id });
                    }}
                  >
                    <IoIosTrash fontSize="1rem" className="ml-auto" />
                  </Button>
                </div>
              ))}
              <DropdownItem divider />
              <div className="p-2 pr-3 text-right">
                <Button
                  className="btn-shadow btn-sm"
                  color="primary"
                  onClick={toggleModal}
                >
                  {t('analyzeTab.createAlertBtn')}
                </Button>
              </div>
            </DropdownMenu>
          </UncontrolledDropdown>
        )}
        {/*
       <Button 
          className="btn-icon ml-2"
          color="info"
          // change style for mobile view
        >
          <IoIosSave className="btn-icon-wrapper" />
          Save
        </Button> */}
      </div>
      <div className="btn-actions-pane-right mask-line overflow-auto mb-3 pl-3">
        {subChartCategories.map((cat, i, arr) => (
          <Button
            key={cat.title}
            title={cat.title}
            tag={NavLink}
            to={`/app/analyze/${params.id}/${cat.path}`}
            size="sm"
            outline
            color="primary"
            className={cx('btn-pill btn-wide', {
              'mr-1 ml-1': i !== 0 && i !== arr.length - 1
            })}
            activeClassName="active"
          >
            {t(`analyzeTab.overviewCharts.${cat.transKey}`)}
          </Button>
        ))}
      </div>

      <AlertDialog
        isOpen={alertModal}
        toggle={toggleModal}
        alertCharts={alertCharts}
        resetAlertChart={resetAlertChart}
      />

      <Switch>
        {subChartRoutes}
        <Redirect
          to={`/app/analyze/${params.id}/${subChartCategories[0].path}`}
        />
      </Switch>
    </Fragment>
  );
}

ShowCharts.propTypes = {
  t: PropTypes.func.isRequired,
  analyze: PropTypes.object,
  actions: PropTypes.object
};

const applyDecorators = compose(
  reduxConnect('analyze', ['appState', 'analyze']),
  translate(['tabsContent'], { wait: true })
);

export default applyDecorators(ShowCharts);
