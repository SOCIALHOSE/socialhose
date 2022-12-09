// no more used
export const paramsMapping = {
  savedFeeds: 'savedFeeds',
  searchesPerDay: 'searchesPerDay',
  webFeeds: 'webFeedLicense',
  alerts: 'alerts',
  news: 'news',
  blogs: 'blogs',
  reddit: 'Reddit',
  instagram: 'Instagram',
  twitter: 'Twitter',
  analytics: 'Analytics',
  newsletter: 'Newsletter',
  subscriberAccounts: 'subscriberAccounts',
  masterAccounts: 'masterAccounts'
};

export const planButtons = [
  {
    name: 'Social Starter',
    defaultValues: {
      savedFeeds: 5,
      searchesPerDay: 10,
      webFeeds: 0,
      alerts: 0,
      subscriberAccounts: 1,
      news: false,
      blog: false,
      reddit: false,
      instagram: true,
      twitter: false,
      analytics: true,
      newsletter: false
    }
  },
  {
    name: 'PR Starter',
    wide: true,
    defaultValues: {
      savedFeeds: 5,
      searchesPerDay: 10,
      webFeeds: 0,
      alerts: 0,
      subscriberAccounts: 1,
      news: true,
      blog: true,
      reddit: true,
      instagram: false,
      twitter: true,
      analytics: true,
      newsletter: false
    }
  },
  {
    name: 'The Works',
    defaultValues: {
      savedFeeds: 5,
      searchesPerDay: 10,
      webFeeds: 2,
      alerts: 2,
      subscriberAccounts: 3,
      news: true,
      blog: true,
      reddit: true,
      instagram: true,
      twitter: true,
      analytics: true,
      newsletter: true
    }
  }
];

export const mediaTypes = [
  { title: 'News', transKey: 'news', name: 'news', price: '$20' },
  { title: 'Blog', transKey: 'blogs', name: 'blog', price: '$15' },
  { title: 'Reddit', transKey: 'reddit', name: 'reddit', price: '$1' },
  {
    title: 'Instagram',
    transKey: 'instagram',
    name: 'instagram',
    price: '$3'
  },
  { title: 'Twitter', transKey: 'twitter', name: 'twitter', price: '$3' }
];

export const features = [
  {
    name: 'analytics',
    title: 'Analytics',
    transKey: 'analytics',
    price: '$15'
    // desc: 'Analytics can be added to any package $15 x Number of Feeds'
  }
  /*  {
    name: 'Newsletter',
    price: '$5',
    desc:
      '$5 per alert newsletter with unlimited recipients and recipient groups'
  } */
];

export const addonFeatures = [
  {
    name: 'subscriberAccounts',
    title: 'User Accounts',
    transKey: 'userAccounts',
    props: {
      min: 1,
      defaultValue: 1,
      max: 100
    }
  }
];

export const licenses = [
  {
    name: 'savedFeeds',
    title: 'Feed Licenses',
    transKey: 'feedsLicenses',
    props: {
      min: 0,
      max: 200,
      marks: {
        0: 0,
        200: 200
      },
      step: 1
    }
  },
  {
    name: 'searchesPerDay',
    title: 'Search Licenses (per day)',
    transKey: 'searchLicenses',
    props: {
      min: 10,
      max: 200,
      marks: {
        10: 10,
        200: 200
      },
      step: 10
    }
  },
  {
    name: 'webFeeds',
    title: 'Webfeed Licenses',
    transKey: 'webfeedLicenses',
    price: '$5',
    props: {
      min: 0,
      max: 200,
      marks: {
        0: 0,
        200: 200
      }
    }
  },
  {
    name: 'alerts',
    title: 'Alert Licenses',
    transKey: 'alertLicenses',
    props: {
      min: 0,
      max: 100,
      marks: {
        0: 0,
        100: 100
      }
    }
  }
  /*   {
    name: 'newsletterLicenses',
    title: 'Newsletter Licenses',
    props: {
      min: 0,
      max: 10,
      marks: {
        0: 0,
        10: 10
      }
    }
  }, */
];

export const industryList = [
  { label: 'Accounting', value: 'Accounting' },
  { label: 'Airlines/Aviation', value: 'Airlines/Aviation' },
  {
    label: 'Alternative Dispute Resolution',
    value: 'Alternative Dispute Resolution'
  },
  { label: 'Alternative Medicine', value: 'Alternative Medicine' },
  { label: 'Animation', value: 'Animation' },
  { label: 'Apparel & Fashion', value: 'Apparel & Fashion' },
  { label: 'Architecture & Planning', value: 'Architecture & Planning' },
  { label: 'Arts and Crafts', value: 'Arts and Crafts' },
  { label: 'Automotive', value: 'Automotive' },
  { label: 'Aviation & Aerospace', value: 'Aviation & Aerospace' },
  { label: 'Banking', value: 'Banking' },
  { label: 'Biotechnology', value: 'Biotechnology' },
  { label: 'Broadcast Media', value: 'Broadcast Media' },
  { label: 'Building Materials', value: 'Building Materials' },
  {
    label: 'Business Supplies and Equipment',
    value: 'Business Supplies and Equipment'
  },
  { label: 'Capital Markets', value: 'Capital Markets' },
  { label: 'Chemicals', value: 'Chemicals' },
  {
    label: 'Civic & Social Organization',
    value: 'Civic & Social Organization'
  },
  { label: 'Civil Engineering', value: 'Civil Engineering' },
  { label: 'Commercial Real Estate', value: 'Commercial Real Estate' },
  {
    label: 'Computer & Network Security',
    value: 'Computer & Network Security'
  },
  { label: 'Computer Games', value: 'Computer Games' },
  { label: 'Computer Hardware', value: 'Computer Hardware' },
  { label: 'Computer Networking', value: 'Computer Networking' },
  { label: 'Computer Software', value: 'Computer Software' },
  { label: 'Internet', value: 'Internet' },
  { label: 'Construction', value: 'Construction' },
  { label: 'Consumer Electronics', value: 'Consumer Electronics' },
  { label: 'Consumer Goods', value: 'Consumer Goods' },
  { label: 'Consumer Services', value: 'Consumer Services' },
  { label: 'Cosmetics', value: 'Cosmetics' },
  { label: 'Dairy', value: 'Dairy' },
  { label: 'Defense & Space', value: 'Defense & Space' },
  { label: 'Design', value: 'Design' },
  { label: 'Education Management', value: 'Education Management' },
  { label: 'E-Learning', value: 'E-Learning' },
  {
    label: 'Electrical/Electronic Manufacturing',
    value: 'Electrical/Electronic Manufacturing'
  },
  { label: 'Entertainment', value: 'Entertainment' },
  { label: 'Environmental Services', value: 'Environmental Services' },
  { label: 'Events Services', value: 'Events Services' },
  { label: 'Executive Office', value: 'Executive Office' },
  { label: 'Facilities Services', value: 'Facilities Services' },
  { label: 'Farming', value: 'Farming' },
  { label: 'Financial Services', value: 'Financial Services' },
  { label: 'Fine Art', value: 'Fine Art' },
  { label: 'Fishery', value: 'Fishery' },
  { label: 'Food & Beverages', value: 'Food & Beverages' },
  { label: 'Food Production', value: 'Food Production' },
  { label: 'Fund-Raising', value: 'Fund-Raising' },
  { label: 'Furniture', value: 'Furniture' },
  { label: 'Gambling & Casinos', value: 'Gambling & Casinos' },
  { label: 'Glass, Ceramics & Concrete', value: 'Glass, Ceramics & Concrete' },
  { label: 'Government Administration', value: 'Government Administration' },
  { label: 'Government Relations', value: 'Government Relations' },
  { label: 'Graphic Design', value: 'Graphic Design' },
  {
    label: 'Health, Wellness and Fitness',
    value: 'Health, Wellness and Fitness'
  },
  { label: 'Higher Education', value: 'Higher Education' },
  { label: 'Hospital & Health Care', value: 'Hospital & Health Care' },
  { label: 'Hospitality', value: 'Hospitality' },
  { label: 'Human Resources', value: 'Human Resources' },
  { label: 'Import and Export', value: 'Import and Export' },
  {
    label: 'Individual & Family Services',
    value: 'Individual & Family Services'
  },
  { label: 'Industrial Automation', value: 'Industrial Automation' },
  { label: 'Information Services', value: 'Information Services' },
  {
    label: 'Information Technology and Services',
    value: 'Information Technology and Services'
  },
  { label: 'Insurance', value: 'Insurance' },
  { label: 'International Affairs', value: 'International Affairs' },
  {
    label: 'International Trade and Development',
    value: 'International Trade and Development'
  },
  { label: 'Investment Banking', value: 'Investment Banking' },
  { label: 'Investment Management', value: 'Investment Management' },
  { label: 'Judiciary', value: 'Judiciary' },
  { label: 'Law Enforcement', value: 'Law Enforcement' },
  { label: 'Law Practice', value: 'Law Practice' },
  { label: 'Legal Services', value: 'Legal Services' },
  { label: 'Legislative Office', value: 'Legislative Office' },
  { label: 'Leisure, Travel & Tourism', value: 'Leisure, Travel & Tourism' },
  { label: 'Libraries', value: 'Libraries' },
  { label: 'Logistics and Supply Chain', value: 'Logistics and Supply Chain' },
  { label: 'Luxury Goods & Jewelry', value: 'Luxury Goods & Jewelry' },
  { label: 'Machinery', value: 'Machinery' },
  { label: 'Management Consulting', value: 'Management Consulting' },
  { label: 'Maritime', value: 'Maritime' },
  { label: 'Market Research', value: 'Market Research' },
  { label: 'Marketing and Advertising', value: 'Marketing and Advertising' },
  {
    label: 'Mechanical or Industrial Engineering',
    value: 'Mechanical or Industrial Engineering'
  },
  { label: 'Media Production', value: 'Media Production' },
  { label: 'Medical Devices', value: 'Medical Devices' },
  { label: 'Medical Practice', value: 'Medical Practice' },
  { label: 'Mental Health Care', value: 'Mental Health Care' },
  { label: 'Military', value: 'Military' },
  { label: 'Mining & Metals', value: 'Mining & Metals' },
  { label: 'Motion Pictures and Film', value: 'Motion Pictures and Film' },
  { label: 'Museums and Institutions', value: 'Museums and Institutions' },
  { label: 'Music', value: 'Music' },
  { label: 'Nanotechnology', value: 'Nanotechnology' },
  { label: 'Newspapers', value: 'Newspapers' },
  {
    label: 'Nonprofit Organization Management',
    value: 'Nonprofit Organization Management'
  },
  { label: 'Oil & Energy', value: 'Oil & Energy' },
  { label: 'Online Media', value: 'Online Media' },
  { label: 'Outsourcing/Offshoring', value: 'Outsourcing/Offshoring' },
  { label: 'Package/Freight Delivery', value: 'Package/Freight Delivery' },
  { label: 'Packaging and Containers', value: 'Packaging and Containers' },
  { label: 'Paper & Forest Products', value: 'Paper & Forest Products' },
  { label: 'Performing Arts', value: 'Performing Arts' },
  { label: 'Pharmaceuticals', value: 'Pharmaceuticals' },
  { label: 'Philanthropy', value: 'Philanthropy' },
  { label: 'Photography', value: 'Photography' },
  { label: 'Plastics', value: 'Plastics' },
  { label: 'Political Organization', value: 'Political Organization' },
  {
    label: 'Primary/Secondary Education',
    value: 'Primary/Secondary Education'
  },
  { label: 'Printing', value: 'Printing' },
  {
    label: 'Professional Training & Coaching',
    value: 'Professional Training & Coaching'
  },
  { label: 'Program Development', value: 'Program Development' },
  { label: 'Public Policy', value: 'Public Policy' },
  {
    label: 'Public Relations and Communications',
    value: 'Public Relations and Communications'
  },
  { label: 'Public Safety', value: 'Public Safety' },
  { label: 'Publishing', value: 'Publishing' },
  { label: 'Railroad Manufacture', value: 'Railroad Manufacture' },
  { label: 'Ranching', value: 'Ranching' },
  { label: 'Real Estate', value: 'Real Estate' },
  {
    label: 'Recreational Facilities and Services',
    value: 'Recreational Facilities and Services'
  },
  { label: 'Religious Institutions', value: 'Religious Institutions' },
  { label: 'Renewables & Environment', value: 'Renewables & Environment' },
  { label: 'Research', value: 'Research' },
  { label: 'Restaurants', value: 'Restaurants' },
  { label: 'Retail', value: 'Retail' },
  {
    label: 'Security and Investigations',
    value: 'Security and Investigations'
  },
  { label: 'Semiconductors', value: 'Semiconductors' },
  { label: 'Shipbuilding', value: 'Shipbuilding' },
  { label: 'Sporting Goods', value: 'Sporting Goods' },
  { label: 'Sports', value: 'Sports' },
  { label: 'Staffing and Recruiting', value: 'Staffing and Recruiting' },
  { label: 'Supermarkets', value: 'Supermarkets' },
  { label: 'Telecommunications', value: 'Telecommunications' },
  { label: 'Textiles', value: 'Textiles' },
  { label: 'Think Tanks', value: 'Think Tanks' },
  { label: 'Tobacco', value: 'Tobacco' },
  {
    label: 'Translation and Localization',
    value: 'Translation and Localization'
  },
  {
    label: 'Transportation/Trucking/Railroad',
    value: 'Transportation/Trucking/Railroad'
  },
  { label: 'Utilities', value: 'Utilities' },
  {
    label: 'Venture Capital & Private Equity',
    value: 'Venture Capital & Private Equity'
  },
  { label: 'Veterinary', value: 'Veterinary' },
  { label: 'Warehousing', value: 'Warehousing' },
  { label: 'Wholesale', value: 'Wholesale' },
  { label: 'Wine and Spirits', value: 'Wine and Spirits' },
  { label: 'Wireless', value: 'Wireless' },
  { label: 'Writing and Editing', value: 'Writing and Editing' }
];
