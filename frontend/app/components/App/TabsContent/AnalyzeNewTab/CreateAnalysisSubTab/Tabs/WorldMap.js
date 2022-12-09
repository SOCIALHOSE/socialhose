import React, { useEffect, useRef, useState, Fragment } from 'react';
import PropTypes from 'prop-types';
import { Row, Col, ButtonGroup, Button } from 'reactstrap';

import 'leaflet/dist/leaflet.css';
import L from 'leaflet';
import 'leaflet-dvf/dist/leaflet-dvf';
// keep above 3 in sequence
import ChartWrapper from '../ChartWrapper';
import { getWorldMapAPI } from '../../../../../../api/analytics/createAnalytics';
import useIsMounted from '../../../../../common/hooks/useIsMounted';
import { translate } from 'react-i18next';

const initialPie = {
  data: [],
  error: undefined,
  loading: true,
  selected: undefined
};

function WorldMap(props) {
  const { id, t } = props;
  const mapRef = useRef();
  const isMounted = useIsMounted();
  const [pieData, setPieData] = useState(initialPie);
  const [markers, setMarkers] = useState([]);

  const feedNames = (pieData.data && Object.keys(pieData.data)) || [];

  useEffect(() => {
    mapRef.current = L.map('leaflet-map', {
      center: [0, 0],
      zoom: 2,
      layers: [
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
          noWrap: true,
          attribution:
            '&copy; <a target="_blank" noreferrer noopener href="http://osm.org/copyright">OpenStreetMap</a> contributors'
        })
      ]
    });

    mapRef.current.whenReady(getMapSentiments);
  }, []);

  useEffect(() => {
    const { data, selected, error } = pieData;
    const selectedData = data[feedNames[selected]];
    const hasErr = error && error[feedNames[selected]];
    clearMap();

    if (selectedData && !hasErr) {
      // loop to add marker
      const markersList = [];
      selectedData.forEach((data) => {
        const [lat, lng] = getLatLong(data.LatLng);
        if (!lat || !lng) {
          return;
        }

        let pieChartMarker = new L.PieChartMarker(new L.LatLng(lat, lng), {
          ...options,
          data: {
            positive: data.POSITIVE,
            negative: data.NEGATIVE,
            neutral: data.NEUTRAL
          }
        });
        pieChartMarker.addTo(mapRef.current);
        markersList.push(pieChartMarker);
      });
      // eslint-disable-next-line new-cap
      const group = new L.featureGroup(markersList);
      mapRef.current.fitBounds(group.getBounds());
      setMarkers(markersList);
    }
  }, [pieData.data, pieData.selected]);

  function getLatLong(str) {
    const [lat, lng] = str.split(', ');
    return [lat && parseFloat(lat), lng && parseFloat(lng)];
  }

  function clearMap() {
    if (mapRef.current) {
      markers.forEach((v) => {
        mapRef.current.removeLayer(v);
      });
    }
  }

  function getMapSentiments() {
    setPieData((prev) => ({ ...prev, loading: true }));
    getWorldMapAPI(id).then((res) => {
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
      const dataValues = {};
      const errors = {};

      data.map((feed) => {
        const { name, data } = feed;
        if (!data || (Array.isArray(data) && data.length < 1)) {
          errors[name] = t('analyzeTab.noData');
        }
        dataValues[name] = data;
      });

      setPieData({
        data: dataValues,
        error: errors,
        loading: false,
        selected: 0
      });
    });
  }

  const style = {
    height: 'max(300px, calc(100vh - 200px))'
  };

  return (
    <Row>
      <Col md="12">
        <ChartWrapper title="Distribution by Sentiments">
          <Fragment>
            <ButtonGroup size="sm" className="d-block mb-2 text-right">
              {feedNames.map((name, i) => (
                <Button
                  outline
                  key={name}
                  title={name}
                  color="secondary"
                  onClick={function () {
                    setPieData((prev) => ({
                      ...prev,
                      selected: i
                    }));
                  }}
                  active={pieData.selected === i}
                >
                  {name}
                </Button>
              ))}
            </ButtonGroup>
            <div className="position-relative">
              <div id="leaflet-map" style={style} />
              {pieData.error && pieData.error[feedNames[pieData.selected]] ? (
                <div className="no-data" style={{ zIndex: 1000 }}>
                  {pieData.error[feedNames[pieData.selected]]}
                </div>
              ) : null}
            </div>
          </Fragment>
        </ChartWrapper>
      </Col>
    </Row>
  );
}

const options = {
  stroke: false,
  fillOpacity: 0.7,
  radius: 20,
  gradient: false,
  chartOptions: {
    positive: {
      fillColor: '#00FF00',
      displayText: function (value) {
        return value.toFixed(0);
      }
    },
    negative: {
      fillColor: '#FF0000',
      displayText: function (value) {
        return value.toFixed(0);
      }
    },
    neutral: {
      fillColor: '#000000',
      displayText: function (value) {
        return value.toFixed(0);
      }
    }
  }
  // Other L.Path style options
};

WorldMap.propTypes = {
  actions: PropTypes.object,
  feedData: PropTypes.object,
  id: PropTypes.string,
  t: PropTypes.func.isRequired,
  analyze: PropTypes.object
};

export default translate(['tabsContent'], { wait: true })(WorldMap);
