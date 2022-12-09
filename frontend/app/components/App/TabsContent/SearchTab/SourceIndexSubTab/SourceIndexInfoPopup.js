import React from 'react';
import PropTypes from 'prop-types';
import { translate } from 'react-i18next';
import PopupLayout from '../../../../common/Popups/PopupLayout';
import InfoField from './InfoField';
import {
  capOnlyFirstLetter,
  getTitle,
  notNullAndUnd
} from '../../../../../common/helper';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faCheckCircle } from '@fortawesome/free-solid-svg-icons';

class SourceIndexInfoPopup extends React.Component {
  static propTypes = {
    source: PropTypes.object.isRequired,
    hideSourceInfoPopup: PropTypes.func.isRequired,
    t: PropTypes.func.isRequired
  };

  render() {
    let { t, source, hideSourceInfoPopup } = this.props;
    /* 
    const loc = cl(
      source.city,
      source.state,
      source.country && t(`common:country.${source.country}`)
    )
      .split(' ')
      .join(', '); */
    /* 
    source = {
      ...source,
      tags: ['Lorem', 'ipsum', 'dolor', 'ipsum', 'dolor', 'ipsum', 'dolor'],
      verified: true,
      followers: 3333,
      following: 33,
      favorites: 333,
      title: 'Title',
      url: 'URL',
      type: 'Type',
      subType: 'Sub Type',
      lang: 'en',
      location: 'Washington, DC',
      country: 'US',
      spam_probability: '20%',
      likes: 3
    }; */

    return (
      <PopupLayout
        className="source-info-popup"
        title="sourceIndexTab.sourceInfoPopupTitle"
        showFooter={false}
        onHide={hideSourceInfoPopup}
      >
        <ul className="container">
          <InfoField label="sourceIndexTab.titleLabel">
            <a href={source.url} target="_blank" rel="noopener noreferrer">
              {getTitle(source.title)}
            </a>
          </InfoField>

          {source.url && (
            <InfoField label="sourceIndexTab.homeUrl">{source.url}</InfoField>
          )}

          {source.type && (
            <InfoField label="sourceIndexTab.mediaType">
              {capOnlyFirstLetter(source.type)}
            </InfoField>
          )}

          {source.subType && (
            <InfoField labelValue="Sub Type">
              {capOnlyFirstLetter(source.subType)}
            </InfoField>
          )}

          {source.verified && (
            <InfoField labelValue="Verified">
              <FontAwesomeIcon
                title="Source Verified"
                className="text-primary"
                icon={faCheckCircle}
              />
            </InfoField>
          )}

          {source.lang && (
            <InfoField label="sourceIndexTab.lang">
              {t(`common:language.${source.lang}`, '-')}
            </InfoField>
          )}

          {source.location && (
            <InfoField labelValue="Location">{source.location}</InfoField>
          )}

          {source.country && (
            <InfoField label="sourceIndexTab.country">
              {t(`common:country.${source.country}`)}
            </InfoField>
          )}

          {notNullAndUnd(source.followers) && (
            <InfoField labelValue="Followers">{source.followers}</InfoField>
          )}

          {notNullAndUnd(source.following) && (
            <InfoField labelValue="Following">{source.following}</InfoField>
          )}

          {notNullAndUnd(source.favorites) && (
            <InfoField labelValue="Favorites">{source.favorites}</InfoField>
          )}

          {notNullAndUnd(source.likes) && (
            <InfoField labelValue="Likes">{source.likes}</InfoField>
          )}

          {source.tags && source.tags.length > 0 && (
            <InfoField labelValue="Tags">{source.tags.join(', ')}</InfoField>
          )}

          {source.spam_probability && (
            <InfoField labelValue="Spam Probability">
              {source.spam_probability}
            </InfoField>
          )}

          {source.source_profiles && (
            <InfoField labelValue="Source profiles">
              {source.source_profiles.join(', ')}
            </InfoField>
          )}
        </ul>
      </PopupLayout>
    );
  }
}

export default translate(['tabsContent'], { wait: true })(SourceIndexInfoPopup);
