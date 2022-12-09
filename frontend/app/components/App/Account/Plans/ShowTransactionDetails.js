import React, { useEffect } from 'react';
import PropTypes from 'prop-types';
import {
  Button,
  Col,
  Modal,
  ModalBody,
  ModalFooter,
  ModalHeader,
  Row,
  Table
} from 'reactstrap';
import { convertUTCtoLocal } from '../../../../common/helper';
import moment from 'moment';
import { capitalize } from 'lodash';
import { translate } from 'react-i18next';

function ShowTransactionDetails(props) {
  const { data, closeModal, t } = props;

  const plan = data && data.lines && data.lines.data && data.lines.data[0];

  useEffect(() => {
    return () => closeModal();
  }, []);

  return (
    <Modal isOpen={!!data && !!plan} toggle={closeModal} size="lg">
      <ModalHeader toggle={closeModal}>
        {t('plans.transactions.modal.heading')}
      </ModalHeader>
      <ModalBody>
        {data && (
          <Row>
            <Col xs="12" lg="6" className="mb-3">
              <h6 className="mb-3">
                {t('plans.transactions.modal.transactionDetails')}
              </h6>
              <Table striped>
                <tbody>
                  <tr>
                    <th>{t('plans.transactions.modal.transactionDate')}</th>
                    <td>
                      {convertUTCtoLocal(
                        moment.unix(
                          data.status_transitions &&
                            data.status_transitions.paid_at
                        ),
                        'MM/DD/YYYY hh:mm:ss a'
                      )}
                    </td>
                  </tr>
                  <tr>
                    <th>{t('plans.transactions.modal.activationDate')}</th>
                    <td>
                      {convertUTCtoLocal(
                        moment.unix(plan && plan.period.start),
                        'MM/DD/YYYY'
                      )}
                    </td>
                  </tr>
                  <tr>
                    <th>{t('plans.transactions.modal.expirationDate')}</th>
                    <td>
                      {convertUTCtoLocal(
                        moment.unix(plan && plan.period.end),
                        'MM/DD/YYYY'
                      )}
                    </td>
                  </tr>
                  <tr>
                    <th>{t('plans.transactions.modal.amount')}</th>
                    <td>${data.amount_paid / 100}</td>
                  </tr>
                  <tr>
                    <th>{t('plans.transactions.modal.status')}</th>
                    <td>{capitalize(data.status)}</td>
                  </tr>
                </tbody>
              </Table>
            </Col>
            <Col xs="12" lg="6" className="mb-3">
              <h6 className="mb-3">
                {t('plans.transactions.modal.billingDetails')}
              </h6>
              <Table striped>
                <tbody>
                  <tr>
                    <th>{t('plans.transactions.modal.name')}</th>
                    <td>{data.customer_name || '-'}</td>
                  </tr>
                  <tr>
                    <th>{t('plans.transactions.modal.email')}</th>
                    <td>{data.customer_email || '-'}</td>
                  </tr>
                  <tr>
                    <th>{t('plans.transactions.modal.phone')}</th>
                    <td>{data.customer_phone || '-'}</td>
                  </tr>
                  <tr>
                    <th>{t('plans.transactions.modal.address')}</th>
                    <td>{data.customer_address || '-'}</td>
                  </tr>
                  <tr>
                    <th>{t('plans.transactions.modal.invoiceNo')}</th>
                    <td>
                      {data.number} (
                      <a
                        href={data.hosted_invoice_url}
                        rel="noopener noreferrer"
                        target="_blank"
                      >
                        {t('plans.transactions.modal.showInvoiceLink')}
                      </a>
                      )
                    </td>
                  </tr>
                </tbody>
              </Table>
            </Col>
            {/* <Col xs="12" lg="6" className="mb-3">
            <h6 className="mb-3">Plan Details</h6>
            <Table striped>
              <tbody>
                <tr>
                  <th>Feeds Licenses</th>
                  <td>0</td>
                </tr>
                <tr>
                  <th>Webfeed Licenses</th>
                  <td>0</td>
                </tr>
                <tr>
                  <th>Newsletter Licenses</th>
                  <td>0</td>
                </tr>
                <tr>
                  <th>User Accounts</th>
                  <td>0</td>
                </tr>
                <tr>
                  <th>Analytics</th>
                  <td>No</td>
                </tr>
              </tbody>
            </Table>
          </Col> */}
          </Row>
        )}
      </ModalBody>
      <ModalFooter>
        <Button color="link" onClick={closeModal}>
          {t('plans.transactions.modal.cancelBtn')}
        </Button>
      </ModalFooter>
    </Modal>
  );
}

ShowTransactionDetails.propTypes = {
  t: PropTypes.func,
  closeModal: PropTypes.func,
  data: PropTypes.oneOfType([PropTypes.object, PropTypes.bool]).isRequired
};

export default React.memo(
  translate(['tabsContent'], { wait: true })(ShowTransactionDetails)
);
