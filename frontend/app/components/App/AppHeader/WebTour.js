import React, { useEffect, useState } from 'react';
import PropTypes from 'prop-types';
import { find } from 'lodash';
import { disableBodyScroll, enableBodyScroll } from 'body-scroll-lock';
import Tour from 'reactour';
import { useHistory, useLocation } from 'react-router';

import reduxConnect from '../../../redux/utils/connect';
import tourPages from './WebTourSteps';

function WebTour({
  actions,
  store: {
    common: { base },
    appState: { themeOptions }
  }
}) {
  const [hasSidebar, setHasSidebar] = useState(window.innerWidth > 991);
  const [tourData, setTourData] = useState({ content: [] });
  const location = useLocation();
  const { replace } = useHistory();

  const { isTourOpen = false } = base;
  const params = new URLSearchParams(location.search);
  const webtour = params.get('webtour');

  useEffect(() => {
    if (webtour) {
      const tour = find(tourPages, {
        to: location.pathname
      });

      if (tour) {
        setTourData(tour);
        window.gtag && window.gtag('event', 'tutorial_begin', {
          name: tour.name
        });
        actions.toggleWebTour(); // open tour if param is available
      }
    } else {
      actions.toggleWebTour(); // close if param is removed
    }
  }, [webtour]);

  useEffect(() => {
    if (isTourOpen) {
      if (window.innerWidth > 991) {
        !hasSidebar && setHasSidebar(true);
      } else {
        hasSidebar && setHasSidebar(false);
      }
    }
  }, [window.innerWidth]);

  const accentColor = '#0094bd';

  function closeWebTour() {
    const queryParams = new URLSearchParams(location.search);

    if (queryParams.has('webtour')) {
      queryParams.delete('webtour');
      replace({
        search: queryParams.toString()
      });
    }
  }

  function getCurrentStep(step) {
    const stepState = tourData.content;
    const stepDetails = stepState.find((v, i) => i === step);

    if (step === stepState.length - 1) {
      window.gtag && window.gtag('event', 'tutorial_complete', {
        name: tourData.name
      });
    }

    if (!hasSidebar) {
      if (stepDetails.needSidebar) {
        !themeOptions.enableMobileMenu && actions.setEnableMobileMenu(true);
      } else {
        themeOptions.enableMobileMenu && actions.setEnableMobileMenu(false);
      }
    }
  }

  function disableBody(target) {
    disableBodyScroll(target);
  }

  function enableBody(target) {
    enableBodyScroll(target);
  }

  return (
    <Tour
      onRequestClose={closeWebTour}
      steps={tourData.content}
      getCurrentStep={getCurrentStep}
      isOpen={isTourOpen && tourData.content && tourData.content.length > 0}
      maskClassName="mask"
      className="helper"
      rounded={5}
      startAt={0}
      closeWithMask={false}
      accentColor={accentColor}
      onAfterOpen={disableBody}
      onBeforeClose={enableBody}
      disableFocusLock
      lastStepNextButton={<div className="btn btn-primary">Finish</div>}
    />
  );
}

WebTour.propTypes = {
  actions: PropTypes.object,
  store: PropTypes.object
};

export default reduxConnect()(WebTour);
