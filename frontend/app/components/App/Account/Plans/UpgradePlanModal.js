import React from 'react';
import PropTypes from 'prop-types';
import { Link } from 'react-router-dom';
import { Button, Modal, ModalBody, ModalHeader } from 'reactstrap';
import { planRoutes } from './UserPlans';
import { Trans, translate } from 'react-i18next';

function UpgradePlanModal({ isModalOpen = false, toggle, t }) {
  function toggleModal() {
    return toggle();
  }

  return (
    <Modal
      isOpen={isModalOpen}
      toggle={toggleModal}
      modalClassName="zoom-modal"
      backdrop="static"
    >
      <ModalHeader toggle={toggleModal} />
      <ModalBody className="px-4 px-sm-5 pb-5">
        <div className="text-center">
          <div className="display-4 mb-2">
            <i className="lnr-rocket text-primary"></i>
          </div>
          <h3 className="mb-3">{t('plans.upgradeModal.heading')}</h3>
          <div className="mb-4">
            <p className="text-muted">
              <Trans i18nKey="plans.upgradeModal.text">
                You have to upgrade your plan to get access of these features.
                Take a look at our bite-sized
                <strong>à la carte menu options</strong> with monthly billing.
              </Trans>{' '}
              <a
                href="https://www.socialhose.io/en/pricing"
                rel="noopener noreferrer"
                target="_blank"
              >
                {t('plans.upgradeModal.learnMore')}
              </a>
            </p>
          </div>
          <div>
            <Button
              tag={Link}
              to={`/app/plans/${planRoutes.update}`}
              onClick={toggleModal}
              className="btn-pill btn-wide d-block mx-auto"
              color="success"
              size="lg"
            >
              {t('plans.upgradeModal.upgradeNowBtn')}
            </Button>
            <Button color="link" size="sm" onClick={toggleModal}>
              {t('plans.upgradeModal.maybeLaterBtn')}
            </Button>
          </div>
        </div>
      </ModalBody>
    </Modal>
  );
}

UpgradePlanModal.propTypes = {
  isModalOpen: PropTypes.bool,
  t: PropTypes.func.isRequired,
  toggle: PropTypes.func
};

export default React.memo(
  translate(['tabsContent'], { wait: true })(UpgradePlanModal)
);
