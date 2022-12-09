import React from 'react';
import PropTypes from 'prop-types';
import { translate } from 'react-i18next';
import Select from 'react-select';
import {
  Button,
  Modal,
  ModalHeader,
  ModalBody,
  FormGroup,
  Label,
  Input,
  ModalFooter
} from 'reactstrap';

export class AddClippingsFeedPopup extends React.Component {
  static propTypes = {
    parentId: PropTypes.number.isRequired,
    hidePopup: PropTypes.func.isRequired,
    addClippingsFeed: PropTypes.func.isRequired,
    addAlert: PropTypes.func.isRequired,
    categories: PropTypes.array.isRequired,
    t: PropTypes.func.isRequired
  };

  constructor(props) {
    super(props);
    this.state = {
      parentId: props.parentId,
      feedName: ''
    };
  }

  onChangeName = (e) => {
    const { value } = e.target;
    this.setState({ feedName: value });
  };

  hidePopup = () => {
    this.props.hidePopup();
  };

  onSubmit = () => {
    const { parentId } = this.state;
    const { addAlert, addClippingsFeed, hidePopup } = this.props;
    const { feedName } = this.state;
    if (feedName) {
      addClippingsFeed(feedName, parentId);
      hidePopup();
    } else {
      addAlert({
        type: 'error',
        transKey: 'feedNameEmpty'
      });
    }
  };

  flattenCategories = (categories, level = '') => {
    return categories.reduce((result, category) => {
      result.push({
        label:
          level +
          this.props.t(`sidebar.${category.name}`, {
            defaultValue: category.name
          }),
        value: category.id
      });
      if (category.childes && category.childes.length) {
        return result.concat(
          this.flattenCategories(category.childes, '- ' + level)
        );
      }
      return result;
    }, []);
  };

  onParentCategorySelect = (value) => {
    this.setState({ parentId: value });
  };

  render() {
    const { t, categories } = this.props;
    const { parentId, feedName } = this.state;
    const options = this.flattenCategories(categories);

    return (
      <Modal isOpen toggle={this.hidePopup} backdrop="static">
        <ModalHeader toggle={this.hidePopup}>
          {t('sidebarPopup.addClippingsFeed')}
        </ModalHeader>
        <ModalBody>
          <FormGroup>
            <Label>{t('sidebarPopup.feedName')}</Label>
            <Input type="text" value={feedName} onChange={this.onChangeName} />
          </FormGroup>
          <FormGroup>
            <Label>{t('sidebarPopup.folder')}</Label>
            <Select
              onChange={this.onParentCategorySelect}
              options={options}
              value={parentId}
              editable={false}
              clearable={false}
              simpleValue
            />
          </FormGroup>
        </ModalBody>
        <ModalFooter>
          <Button color="light" onClick={this.hidePopup}>
            {t('commonWords.Cancel')}
          </Button>
          <Button color="primary" onClick={this.onSubmit}>
            {t('sidebarPopup.addClippingsFeed')}
          </Button>
        </ModalFooter>
      </Modal>
    );
  }
}

export default translate(['common'], { wait: true })(AddClippingsFeedPopup);
