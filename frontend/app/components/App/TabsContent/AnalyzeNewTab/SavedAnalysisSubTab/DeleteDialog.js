import React, { useState } from 'react';
import PropTypes from 'prop-types';
import { Button, Modal, ModalBody, ModalFooter, ModalHeader } from 'reactstrap';
import { deleteAnalytics } from '../../../../../api/analytics/savedAnalytics';
import { translate } from 'react-i18next';

function DeleteDialog(props) {
  const [loading, setLoading] = useState(false);
  const { actions, data, toggle, fetchData, t } = props;

  function handleSubmit() {
    setLoading(true);
    deleteAnalytics(data.value).then((res) => {
      if (res.error) {
        res.data
          ? actions.addAlert(res.data)
          : actions.addAlert({ type: 'error', transKey: 'somethingWrong' });
        setLoading(false);
        return;
      }
      actions.addAlert({ type: 'notice', transKey: 'analyticsDeleted' });
      setLoading(false);
      toggle();
      fetchData();
    });
  }

  return (
    <Modal isOpen={!!data} toggle={toggle} backdrop="static">
      <ModalHeader toggle={toggle}>
        {t('tabsContent:analyzeTab.deleteAnalysis')}
      </ModalHeader>
      <ModalBody>
        <div>
          <p>{t('messages.deleteMessage')}</p>
        </div>
      </ModalBody>
      <ModalFooter>
        <Button color="link" onClick={toggle}>
          {t('commonWords.Cancel')}
        </Button>
        <Button color="danger" disabled={loading} onClick={handleSubmit}>
          {loading ? t('commonWords.loading') : t('commonWords.Delete')}
        </Button>
      </ModalFooter>
    </Modal>
  );
}

DeleteDialog.propTypes = {
  toggle: PropTypes.func,
  t: PropTypes.func.isRequired,
  data: PropTypes.object.isRequired,
  fetchData: PropTypes.func,
  actions: PropTypes.object
};

export default React.memo(translate(['common'], { wait: true })(DeleteDialog));
