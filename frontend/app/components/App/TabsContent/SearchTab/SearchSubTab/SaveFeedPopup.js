import React from 'react'
import PropTypes from 'prop-types'
import { translate } from 'react-i18next'
import {
  Button,
  Modal,
  ModalHeader,
  ModalBody,
  Label,
  Input,
  ModalFooter,
  FormGroup
} from 'reactstrap'

export class SaveFeedPopup extends React.Component {
  static propTypes = {
    feedCategories: PropTypes.array.isRequired,
    saveType: PropTypes.string.isRequired,
    toggleSaveFeedPopup: PropTypes.func.isRequired,
    addAlert: PropTypes.func.isRequired,
    onSaveAsFeed: PropTypes.func.isRequired,
    getSidebarCategories: PropTypes.func.isRequired,
    t: PropTypes.func.isRequired
  }

  constructor(props) {
    super(props)
    this.state = {
      isFeedNameError: false,
      feedCategoriesKeys: [],
      feedName: '',
      selectCategory: ''
    }
  }

  componentWillMount = () => {
    let nestingCount = -1
    this.getCategoriesKeys(this.props.feedCategories, nestingCount)
  }

  //function that generates new array of categories without nesting
  getCategoriesKeys = (categories, nestingCount) => {
    nestingCount += 1
    categories.forEach((category) => {
      if (category.subType === 'deleted_content') return false

      const categoryName = '-'.repeat(nestingCount) + ' ' + category.name

      const feedCategoriesKeys = this.state.feedCategoriesKeys
      feedCategoriesKeys.push({ id: category.id, name: categoryName })
      this.setState({
        feedCategoriesKeys: feedCategoriesKeys,
        selectCategory: feedCategoriesKeys[0].id.toString()
      })

      if (category.childes.length) {
        this.getCategoriesKeys(category.childes, nestingCount)
      }
    })
  }

  changeHandler = (e) => {
    const { name, value } = e.target
    this.setState({ [name]: value })
  }

  hidePopupFromOutside = (e) => {
    if (e.target === e.currentTarget) this.hidePopup()
  }

  hidePopup = () => {
    this.props.toggleSaveFeedPopup()
  }

  onSubmit = () => {
    const { feedName: name, selectCategory: category } = this.state

    if (!name || !name.trim()) {
      this.setState({ isFeedNameError: true })
      return false
    }

    this.props.onSaveAsFeed(name, category)
    this.hidePopup()
  }

  render() {
    const { t } = this.props

    const {
      feedCategoriesKeys,
      isFeedNameError,
      feedName,
      selectCategory
    } = this.state

    return (
      <Modal isOpen toggle={this.hidePopup} backdrop="static" data-tour="feed-save-modal">
        <ModalHeader toggle={this.hidePopup}>
          {t('searchTab.saveFeedPopup.' + this.props.saveType)}
        </ModalHeader>
        <ModalBody>
          <FormGroup>
            <Label>
              {t('searchTab.saveFeedPopup.nameLabel')}<span className="text-danger">*</span>
            </Label>
            <Input
              name="feedName"
              type="text"
              value={feedName}
              onChange={this.changeHandler}
            />
            {isFeedNameError && (
              <p className="text-danger">
                {t('searchTab.saveFeedPopup.feedNameErrorMsg')}
              </p>
            )}
          </FormGroup>
          <FormGroup>
            <Label>
              {t('searchTab.saveFeedPopup.folderLabel')}<span className="text-danger">*</span>
            </Label>
            <Input
              name="selectCategory"
              type="select"
              value={selectCategory}
              onChange={this.changeHandler}
            >
              {feedCategoriesKeys.map((category) => {
                return (
                  <option key={category.id} value={category.id}>
                    {category.name}
                  </option>
                )
              })}
            </Input>
          </FormGroup>
        </ModalBody>
        <ModalFooter>
          <Button color="light" onClick={this.hidePopup}>
            {t('common:commonWords.Cancel')}
          </Button>
          <Button color="primary" onClick={this.onSubmit}>
            {t('searchTab.saveBtn')}
          </Button>
        </ModalFooter>
      </Modal>
    )
  }
}

export default translate(['tabsContent', 'common'], { wait: true })(
  SaveFeedPopup
)
