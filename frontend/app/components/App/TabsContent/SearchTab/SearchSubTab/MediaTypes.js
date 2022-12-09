/* eslint-disable react/jsx-no-bind */
import React, { Fragment, useState } from 'react';
import PropTypes from 'prop-types';
import cx from 'classnames';
import { translate } from 'react-i18next';
import SearchDatesPopup from './SearchDatesPopup';
import { Modal, Button, ModalHeader, ModalBody } from 'reactstrap';
import { IoIosCalendar } from 'react-icons/io';

// previous commented code
// componentWillMount = () => {
//   const { actions, userSubscription } = this.props;
//   actions.setSearchLastDate(userSubscription);
// };
export function MediaTypes(props) {
  const [modal, setModal] = useState(false);

  const {
    t,
    mediaTypes,
    actions,
    chosenMediaTypes,
    toggleMediaType,
    toggleAllMediaTypes,
    restrictions
  } = props;

  const allSelected = mediaTypes.length === chosenMediaTypes.length;

  function toggle() {
    setModal((modal) => !modal);
  }

  // set only the allowed media types from restrictions initially
  function allowPermissions(mediaType) {
    if (!restrictions || !restrictions.plans) {
      return false;
    }

    // for selecting all
    if (!mediaType) {
      return mediaTypes.every((mt) => restrictions.plans[mt]);
    }

    return restrictions.plans[mediaType];
  }

  function toggleSingleType(mediaType, value) {
   /* const isFree = restrictions.plans.price === 0;
    // TODO: remove following restrictions when duplication fixes
    const restrictedTemporary =
      isFree && ['news', 'blogs'].includes(mediaType) && value;

    if (!allowPermissions(mediaType) || restrictedTemporary) { */
    if (!allowPermissions(mediaType)) {
      return actions.toggleUpgradeModal();
    }
    toggleMediaType(mediaType, value); // restrict condition
  }

  function toggleAllTypes() {
    // TODO: remove following restrictions when duplication fixes
    /* const isFree = restrictions.plans.price === 0;
    if (!allowPermissions() || isFree) { */
    if (!allowPermissions()) {
      return actions.toggleUpgradeModal();
    }
    toggleAllMediaTypes(!allSelected);
  }

  /*
  const {
     chosenSearchDate,
     chosenSearchInterval
     chosenStartDate,
     chosenEndDate
  } = props.searchByFiltersState
  const isIntervalBetween = chosenSearchInterval === 'between';
   const searchDateBtnText = isIntervalBetween &&
    chosenStartDate !== '' ||
    isIntervalBetween &&
    chosenEndDate !== ''
      ? chosenSearchDate : t('searchTab.userSubscription.' + chosenSearchDate);
  */

  return (
    <Fragment>
      <div className="d-flex justify-content-between align-items-start">
        <div data-tour="select-media-types">
          <Button
            outline
            size="sm"
            title={allSelected ? 'Click to deselect' : 'Click to select'}
            className="btn-pill mb-2 mr-2 px-3"
            color={cx('light', { active: allSelected })}
            onClick={toggleAllTypes}
          >
            {t('searchTab.sourceTypes.all')}
          </Button>
          {mediaTypes.map((mediaType, i) => {
            const isMediaTypeChosen =
              chosenMediaTypes.indexOf(mediaType) !== -1;
            return (
              <Button
                key={mediaType}
                outline
                size="sm"
                title={
                  isMediaTypeChosen ? 'Click to deselect' : 'Click to select'
                }
                className="btn-pill mb-2 mr-2 px-3"
                color={cx('light', {
                  active: isMediaTypeChosen
                })}
                onClick={() => toggleSingleType(mediaType, !isMediaTypeChosen)}
              >
                {t('searchTab.sourceTypes.' + mediaType)}
              </Button>
            );
          })}
        </div>
        <Button
          color="link"
          className="ml-2"
          onClick={toggle}
          data-tour="select-date-range"
        >
          <IoIosCalendar fontSize="24px" />
          {/* {t('searchTab.datesRange')} */}
        </Button>
      </div>
      <Modal isOpen={modal} toggle={toggle} data-tour="date-range-modal">
        <ModalHeader toggle={toggle}>Select dates</ModalHeader>
        <ModalBody>
          <SearchDatesPopup
            outsideClickIgnoreClass="react-datepicker"
            userSubscription={props.userSubscription}
            userSubscriptionDate={props.userSubscriptionDate}
            searchIntervals={props.searchByFiltersState.searchIntervals}
            searchLastDates={props.searchByFiltersState.searchLastDates}
            chosenSearchInterval={
              props.searchByFiltersState.chosenSearchInterval
            }
            chosenSearchLastDate={
              props.searchByFiltersState.chosenSearchLastDate
            }
            chosenStartDate={props.searchByFiltersState.chosenStartDate}
            chosenEndDate={props.searchByFiltersState.chosenEndDate}
            hideSearchDatesPopup={toggle}
            setSearchInterval={actions.setSearchInterval}
            setSearchLastDate={actions.setSearchLastDate}
            setSearchDate={actions.setSearchDate}
            setStartDate={actions.setStartDate}
            setEndDate={actions.setEndDate}
          />
        </ModalBody>
      </Modal>
    </Fragment>
  );
}

MediaTypes.propTypes = {
  t: PropTypes.func.isRequired,
  mediaTypes: PropTypes.array.isRequired,
  chosenMediaTypes: PropTypes.array.isRequired,
  toggleMediaType: PropTypes.func.isRequired,
  toggleAllMediaTypes: PropTypes.func.isRequired,
  restrictions: PropTypes.object.isRequired,
  actions: PropTypes.object.isRequired,
  userSubscriptionDate: PropTypes.string.isRequired,
  userSubscription: PropTypes.string.isRequired,
  searchByFiltersState: PropTypes.object.isRequired
};

export default translate(['tabsContent'], { wait: true })(MediaTypes);
