import React from 'react';
import PropTypes from 'prop-types';
import { Interpolate, translate } from 'react-i18next';
import Select from 'react-select';
import { Modal, ModalBody, ModalFooter, Button, ModalHeader } from 'reactstrap';

class ExportFeedsTableRow extends React.Component {
  static propTypes = {
    t: PropTypes.func.isRequired,
    feed: PropTypes.object.isRequired,
    showPopup: PropTypes.func.isRequired,
    unexportFeed: PropTypes.func.isRequired,
    goToFeed: PropTypes.func.isRequired
  };

  constructor(props) {
    super(props);
    this.state = {
      format: 'rss',
      modal: false
    };
  }

  showExportPopup = () => {
    this.props.showPopup(this.props.feed, this.state.format);
  };

  toggle = () => {
    this.setState((prev) => ({ modal: !prev.modal }));
  };

  exportOptions = [
    { label: 'RSS 2.0', value: 'rss' },
    { label: 'Atom 1.0', value: 'atom' },
    { label: 'TSV', value: 'tsv' },
    { label: 'HTML', value: 'html' }
  ];

  onChangeFormat = (format) => {
    this.setState({
      format: format
    });
  };

  onDeleteClick = () => {
    this.setState({ modal: false });
    this.props.unexportFeed(this.props.feed.id);
  };

  goToFeed = (e) => {
    e.preventDefault();
    this.props.goToFeed(this.props.feed.id);
  };

  render() {
    const { feed, t } = this.props;

    return (
      <tr>
        <td>
          <Button
            color="link"
            className={`feed-icon font-size-lg p-0 feed-type-mixed ${feed.class}`}
            onClick={this.goToFeed}
          >
            {feed.name}
          </Button>
        </td>

        <td>
          <Select
            options={this.exportOptions}
            value={this.state.format}
            simpleValue
            onChange={this.onChangeFormat}
            clearable={false}
          />
        </td>

        <td>
          <Button
            size="sm"
            color="primary"
            className="border-0 mr-2"
            onClick={this.showExportPopup}
          >
            {t('exportTab.export')}
          </Button>

          <Button
            outline
            size="sm"
            color="secondary"
            className="border-0"
            onClick={this.toggle}
          >
            {t('exportTab.delete')}
          </Button>

          <Modal
            isOpen={this.state.modal}
            toggle={this.toggle}
            backdrop="static"
          >
            <ModalHeader toggle={this.toggle}>
              {t('exportTab.confirm')}
            </ModalHeader>
            <ModalBody>
              <p>
                <Interpolate
                  t={t}
                  i18nKey="exportTab.exportDeleteMessage"
                  feedName={feed.name}
                />
              </p>
            </ModalBody>
            <ModalFooter>
              <Button color="light" onClick={this.toggle}>
                {t('common:commonWords.Cancel')}
              </Button>
              <Button color="danger" onClick={this.onDeleteClick}>
                {t('common:commonWords.Delete')}
              </Button>
            </ModalFooter>
          </Modal>
        </td>
      </tr>
    );
  }
}

export default translate(['tabsContent'], { wait: true })(ExportFeedsTableRow);
