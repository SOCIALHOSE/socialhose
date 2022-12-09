import { useEffect } from 'react';
import { useHistory, useLocation } from 'react-router-dom';
import { isLive } from '../../../common/constants';

function usePageTracking() {
  const location = useLocation();
  const history = useHistory();

  useEffect(() => {
    // google analytics
    if (window.gtag && isLive) {
      setTimeout(() => {
        window.gtag('event', 'page_view', {
          page_location: window.location.href,
          page_path: location.pathname + location.search
          // page_title: '<Page Title>',
        });
      }, 0);
    }
  }, [window.gtag, location]);

  useEffect(() => {
    isLive &&
      history.listen(() => {
        // Added to history listen to prevent first pageview call which is called by hubspot tracking script
        setTimeout(() => { // to wait until document title updates
          const _hsq = window._hsq;
          if (location && _hsq) {
            // hubspot tracking
            _hsq.push(['setPath', location.pathname + location.search]);
            _hsq.push(['trackPageView']);
          }

          if (location && window.lintrk) {
            // linkedin insight tracking
            window.lintrk('track');
          }
        }, 0);
      });
  }, []);

  return null;
}

export default usePageTracking;
