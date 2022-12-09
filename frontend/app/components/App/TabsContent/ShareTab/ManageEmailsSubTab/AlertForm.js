import PropTypes from 'prop-types'
import {AlertForm as BaseAlertForm} from '../NotificatoinsSubTab/forms/AlertForm'
import {translate} from 'react-i18next'

export class AlertForm extends BaseAlertForm {

  static propTypes = {
    t: PropTypes.func.isRequired,
    state: PropTypes.object.isRequired,
    actions: PropTypes.object.isRequired,
    switchShareSubScreen: PropTypes.func.isRequired
  }

}

export default translate(['tabsContent'], { wait: true })(AlertForm)
