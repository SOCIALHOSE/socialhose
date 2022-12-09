import React from 'react'
import PropTypes from 'prop-types'
import { translate, Interpolate } from 'react-i18next'
import PopupLayout from '../../../../common/Popups/PopupLayout'
import { CustomInput } from 'reactstrap'
import { getTitle } from '../../../../../common/helper'

export class SourceIndexUpdatePopup extends React.Component {
  static propTypes = {
    type: PropTypes.string.isRequired,
    sourceLists: PropTypes.array.isRequired,
    chosenLists: PropTypes.array.isRequired,
    chosenSourceIndexes: PropTypes.array.isRequired,
    updateItemTitle: PropTypes.string,
    actions: PropTypes.object.isRequired,
    t: PropTypes.func.isRequired
  };

  componentWillMount = () => {
    const { sourceLists, actions } = this.props
    if (sourceLists.length === 0) {
      actions.getMainSourceLists({page: 1, limit: 50})
    }
  };

  onChoseList = (e) => {
    const { type, chosenLists, actions } = this.props
    const isChecked = e.target.checked
    const listId = parseInt(e.target.dataset.listId)
    const lists = isChecked ? chosenLists.concat(listId) : chosenLists.filter((id) => listId !== id)
    const action = type === 'add' ? actions.setChosenListsToAddSources : actions.setChosenListsToUpdateSources

    action(lists)
  };

  onSubmit = () => {
    const { actions, chosenSourceIndexes, chosenLists, type } = this.props

    actions.addSourcesToList({
      sources: chosenSourceIndexes,
      sourceLists: chosenLists
    }, type === 'add')
  };

  getBodyTitle () {
    const { t, type, updateItemTitle } = this.props
    if (type === 'add') {
      return <p className="mb-3">{t('sourceListsTab.popup.addToListDesc')}</p>
    }
    else {
      return (
        <p className="mb-3">
          <Interpolate
            i18nKey='sourceListsTab.popup.updateListDesc'
            name={getTitle(updateItemTitle)}
          />
        </p>
      )
    }
  }

  render () {
    const { type, sourceLists, chosenLists, actions } = this.props
    const isAdd = type === 'add'
    const title = isAdd ? 'addToListTitle' : 'updateListTitle'
    const submitText = isAdd ? 'addBtn' : 'saveBtn'
    const hideAction = isAdd ? actions.toggleAddSourceToListPopup : actions.hideUpdateSourcePopup

    return (
      <PopupLayout
        title={`sourceListsTab.popup.${title}`}
        submitText={`sourceListsTab.popup.${submitText}`}
        onHide={hideAction}
        onSubmit={this.onSubmit}
      >
        <div>
          {this.getBodyTitle()}

          {sourceLists.length > 0 &&
            <ul className="row">
              {sourceLists.map((list, i) => {
                const isListChosen = chosenLists.includes(list.id)
                return (
                  <li key={i} className="col-md-4 col-sm-6 mb-2">
                    <CustomInput
                      type="checkbox"
                      id={'sourceListCheck-' + i}
                      className="d-flex"
                      data-list-id={list.id}
                      checked={isListChosen}
                      onChange={this.onChoseList}
                      label={list.name}
                    />
                  </li>
                )
              })}
            </ul>
          }
        </div>
      </PopupLayout>
    )
  }
}

export default translate(['tabsContent', 'common'], { wait: true })(SourceIndexUpdatePopup)

