import React from 'react';
import Slider from 'react-slick';
import PropTypes from 'prop-types';
import { Col } from 'reactstrap';

import bg1 from '../../images/bg-images/007.jpg';
import bg2 from '../../images/bg-images/004.jpg';
import bg3 from '../../images/bg-images/012.jpg';
import { translate } from 'react-i18next';

const settings = {
  dots: true,
  infinite: true,
  speed: 500,
  arrows: true,
  slidesToShow: 1,
  slidesToScroll: 1,
  fade: true,
  initialSlide: 0,
  autoplay: true,
  adaptiveHeight: true
};

function CommonSection({ t }) {
  return (
    <Col lg="4" className="d-none d-lg-block">
      <div className="slider-light">
        <Slider {...settings}>
          <div className="vh-100 d-flex justify-content-center align-items-center bg-midnight-bloom">
            <div
              className="slide-img-bg opacity-2"
              style={{
                backgroundImage: 'url(' + bg1 + ')'
              }}
            />
            <div className="slider-content">
              <h3>{t('commonSection.insightsHeading')}</h3>
              <p>{t('commonSection.insightsText')}</p>
            </div>
          </div>
          <div className="vh-100 d-flex justify-content-center align-items-center bg-premium-dark">
            <div
              className="slide-img-bg"
              style={{
                backgroundImage: 'url(' + bg2 + ')'
              }}
            />
            <div className="slider-content">
              <h3>{t('commonSection.brandHeading')}</h3>
              <p>{t('commonSection.brandText')}</p>
            </div>
          </div>
          <div className="vh-100 d-flex justify-content-center align-items-center bg-plum-plate">
            <div
              className="slide-img-bg"
              style={{
                backgroundImage: 'url(' + bg3 + ')'
              }}
            />
            <div className="slider-content">
              <h3>{t('commonSection.socialHeading')}</h3>
              <p>{t('commonSection.socialText')}</p>
            </div>
          </div>
        </Slider>
      </div>
    </Col>
  );
}

CommonSection.propTypes = {
  t: PropTypes.func.isRequired
};

export default translate(['loginApp'], { wait: true })(CommonSection);
