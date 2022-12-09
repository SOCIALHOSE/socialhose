import React, { useEffect, useState } from 'react';
import { Helmet } from 'react-helmet';
import {
  fbPixelScript,
  gtagScript,
  gtagScriptURL,
  hubspotTracking,
  linkedInsightTag
} from '../common/scripts';

function SiteScripts() {
  const [initHubSpot, setInitHubSpot] = useState(false);

  useEffect(() => {
    /* Below pushes a setPath call into _hsq before the tracking code loads to set the URL that gets tracked for the first page view (the trackPageView function is called automatically when the tracking code is loaded). Subsequent calls are in usePageTracking.js
     */
    const _hsq = (window._hsq = window._hsq || []);
    _hsq.push(['setPath', window.location.pathname + window.location.search]);
    setInitHubSpot(true);
  }, []);

  return (
    <Helmet>
      {gtagScriptURL}
      {gtagScript}

      {fbPixelScript}
      {/* noscript tag for fb pixel may not be required here */}

      {initHubSpot && hubspotTracking}

      {linkedInsightTag}
      {/* noscript tag for insight tag may not be required here */}
    </Helmet>
  );
}

export default SiteScripts;
