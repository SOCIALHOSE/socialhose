import React from 'react';
import PropTypes from 'prop-types';
import { translate } from 'react-i18next';
import { Col, CustomInput } from 'reactstrap';

export class LangsTab extends React.Component {
  static propTypes = {
    chosenLanguages: PropTypes.array.isRequired,
    searchLanguages: PropTypes.array.isRequired,
    toggleLang: PropTypes.func.isRequired,
    toggleAllLangs: PropTypes.func.isRequired,
    t: PropTypes.func.isRequired
  };

  toggleLangs = ({ target: { id, checked } }) => {
    this.props.toggleLang(id, checked);
  };

  toggleAllLangs = (e) => {
    this.props.toggleAllLangs(e.target.checked);
  };

  render() {
    const { t } = this.props;
    const { searchLanguages, chosenLanguages } = this.props;
    return (
      <Col sm={12} className="search-by-lang">
        <CustomInput
          id="article-check-all"
          type="checkbox"
          label={t('common:language.all')}
          checked={searchLanguages.length === chosenLanguages.length}
          onChange={this.toggleAllLangs}
        />

        {searchLanguages.map((lang) => (
          <CustomInput
            key={lang}
            id={lang}
            type="checkbox"
            checked={chosenLanguages.indexOf(lang) !== -1}
            label={t('common:language.' + lang)}
            onChange={this.toggleLangs}
          />
        ))}
      </Col>
    );
  }
}

export default translate(['tabsContent'], { wait: true })(LangsTab);
