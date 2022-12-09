import appConfig from '../appConfig.js';

const { appEnv, hubSpotportalID } = appConfig;

// when `npm start` server in local
export const isDevelopment = process.env.NODE_ENV === 'development';

// when `npm run build`
export const isProduction = process.env.NODE_ENV === 'production';

// when run locally or build is generated for corresponding sites
export const isLive = appEnv === 'live';
export const isStaging = appEnv === 'staging';
export const isLocal = appEnv === 'local';

export const errorConstants = {
  'Bad credentials.': 'badCredentials'
};

export const hubspotBaseURL = `https://api.hsforms.com/submissions/v3/integration/submit/${hubSpotportalID}`;
