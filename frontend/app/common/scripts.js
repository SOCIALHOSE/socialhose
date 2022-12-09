import React from 'react';
import appConfig from '../appConfig';

const { gtagID, gtagID2, fbPixelID, hubSpotID, insightTagID } = appConfig;

export const gtagScriptURL = (
  <script
    async
    src={`https://www.googletagmanager.com/gtag/js?id=${gtagID}`}
  ></script>
);

export const gtagScript = (
  <script>{`
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', '${gtagID}', { send_page_view: false });
    gtag('config', '${gtagID2}', { send_page_view: false });
  `}</script>
);

export const fbPixelScript = (
  <script>{`
    !function(f,b,e,v,n,t,s)
    {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
    n.callMethod.apply(n,arguments):n.queue.push(arguments)};
    if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
    n.queue=[];t=b.createElement(e);t.async=!0;
    t.src=v;s=b.getElementsByTagName(e)[0];
    s.parentNode.insertBefore(t,s)}(window,document,'script',
    'https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', '${fbPixelID}'); 
    fbq('track', 'PageView');
  `}</script>
);

export const hubspotTracking = (
  <script
    type="text/javascript"
    id="hs-script-loader"
    async
    defer
    src={`//js.hs-scripts.com/${hubSpotID}.js`}
  ></script>
);

export const linkedInsightTag = [
  <script key="insight-tag" type="text/javascript">{`
  _linkedin_partner_id = "${insightTagID}"; window._linkedin_data_partner_ids = window._linkedin_data_partner_ids || []; window._linkedin_data_partner_ids.push(_linkedin_partner_id);
  `}</script>,

  <script key="insight-tag-2" type="text/javascript">{`
    (function(){var s = document.getElementsByTagName("script")[0]; var b = document.createElement("script"); b.type = "text/javascript";b.async = true; b.src = "https://snap.licdn.com/li.lms-analytics/insight.min.js"; s.parentNode.insertBefore(b, s);})(); 
  `}</script>
];
