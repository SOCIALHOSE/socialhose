import React from 'react';
import PropTypes from 'prop-types';
import { translate } from 'react-i18next';
import config from '../../../../../appConfig';
import {
  Button,
  Modal,
  ModalHeader,
  ModalBody,
  ModalFooter,
  Table
} from 'reactstrap';

class ExportPopup extends React.Component {
  static propTypes = {
    t: PropTypes.func.isRequired,
    feed: PropTypes.object.isRequired,
    hidePopup: PropTypes.func.isRequired,
    exportFormat: PropTypes.string.isRequired
  };

  hidePopup = () => {
    this.props.hidePopup();
  };

  hidePopupFromOutside = (e) => {
    if (e.target === e.currentTarget) this.hidePopup();
  };

  exportOptions = {
    rss: 'RSS 2.0',
    atom: 'Atom 1.0',
    tsv: 'TSV',
    html: 'HTML'
  };

  render() {
    const { t, feed, exportFormat } = this.props;

    const href = `${config.apiUrl}/feed/${feed.id}.${exportFormat}`;

    return (
      <Modal isOpen toggle={this.hidePopup} backdrop="static" size="lg">
        <ModalHeader toggle={this.hidePopup}>
          {this.exportOptions[exportFormat] + ' ' + t('exportTab.export')}
        </ModalHeader>
        <ModalBody>
          <div className="mb-4">
            <p>{t('exportTab.exportPopup.line1')}</p>
            <p className="text-muted font-size-xs mb-2">
              ({t('exportTab.exportPopup.line2')})
            </p>
            <a
              href={href}
              target="_blank"
              className="font-weight-bold"
              rel="noopener noreferrer"
            >
              {href}
            </a>
          </div>
          <p className="mb-2">{t('exportTab.exportPopup.line3')}</p>
          <Table striped>
            <tbody>
              <tr>
                <th scope="row">n</th>
                <td>{t('exportTab.exportPopup.param1')}</td>
              </tr>
              <tr>
                <th scope="row">ext</th>
                <td>{t('exportTab.exportPopup.param2')}</td>
              </tr>
              {exportFormat !== 'tsv' && (
                <tr>
                  <th scope="row">img</th>
                  <td>{t('exportTab.exportPopup.param3')}</td>
                </tr>
              )}
              {exportFormat !== 'tsv' && exportFormat !== 'html' && (
                <tr>
                  <th scope="row">text_format</th>
                  <td>{t('exportTab.exportPopup.param4')}</td>
                </tr>
              )}
            </tbody>
          </Table>
        </ModalBody>
        <ModalFooter>
          <Button color="light" onClick={this.hidePopup}>
            {t('exportTab.close')}
          </Button>
        </ModalFooter>
      </Modal>
    );
  }
}

export default translate(['tabsContent'], { wait: true })(ExportPopup);
