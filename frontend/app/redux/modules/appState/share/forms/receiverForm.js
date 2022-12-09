import {ReduxModule} from '../../../abstract/reduxModule'
import {switchShareSubScreen} from '../tabs'

export const FILL_FORM = 'Fill form'
const CHANGE_FIELD = 'Change field'
const CHOOSE_TAB = 'Choose tab'
const TOGGLE_ACTIVE = 'Toggle active'
const CLEAR = 'Clear form'
const TOGGLE_SUBSCRIPTION = 'Toggle subscription'
const TOGGLE_GROUP = 'Toggle group'
const TOGGLE_RECIPIENT = 'Toggle recipient'

const CONFIRM_DELETE = 'Confirm delete'
const CANCEL_DELETE = 'Cancel delete'
const DELETE_ITEMS = 'Delete receiver'

export class ReceiverForm extends ReduxModule {

  constructor (api) {
    super()
    this.api = api
  }

  getTableTabs () {
    //implement in subclasses
  }

  getInitialState () {
    const tableTabs = this.getTableTabs()
    return {
      active: true,
      tabs: {
        active: tableTabs[0],
        all: tableTabs
      },
      id: null,
      isDeletePopupVisible: false
    }
  }

  toggleArrayField (actionName, fieldName) {
    return this.createHandler(actionName,
      (id, turnOn) => ({id, turnOn}),
      (state, {payload: {id, turnOn}}) => {
        let array = state.get(fieldName) //immutable!
        if (turnOn) {
          if (!array.includes(id)) {
            array = array.push(id)
          }
        } else {
          array = array.filter((item) => item !== id)
        }
        return state.set(fieldName, array)
      }
    )
  }

  /** Delete only current item in form, function name is for compatibility with DeletePopup **/
  deleteItems = (ids, {token, fulfilled, getState, dispatch}) => {
    return this.api
      .deleteItems(token, {ids})
      .then(() => {
        dispatch(switchShareSubScreen('recipients', 'tables'))
        dispatch(this.cancelDelete())
      })
  };

  defineActions () {

    const chooseTableTab = this.setIn(CHOOSE_TAB, ['tabs', 'active'])
    const toggleActive = this.toggle(TOGGLE_ACTIVE, 'active')

    const clearForm = this.resetToInitialState(CLEAR)
    const fillForm = this.createAction(FILL_FORM, item => item)

    const changeField = this.setField(CHANGE_FIELD)
    const toggleSubscription = this.toggleArrayField(TOGGLE_SUBSCRIPTION, 'subscriptions')
    const toggleRecipient = this.toggleArrayField(TOGGLE_RECIPIENT, 'recipients')
    const toggleGroup = this.toggleArrayField(TOGGLE_GROUP, 'groups')

    const confirmDelete = this.reset(CONFIRM_DELETE, 'isDeletePopupVisible', true)
    this.cancelDelete = this.reset(CANCEL_DELETE, 'isDeletePopupVisible', false)
    const deleteItems = this.thunkAction(DELETE_ITEMS, this.deleteItems)

    return {
      clearForm,
      changeField,
      chooseTableTab,
      toggleActive,
      fillForm,
      toggleSubscription,
      toggleRecipient,
      toggleGroup,
      confirmDelete,
      cancelDelete: this.cancelDelete,
      deleteItems
    }
  }

  defineReducers () {
    return {
      [FILL_FORM]: (state, {payload: item}) => {
        return this.normalize(item)
      }
    }
  }

}
