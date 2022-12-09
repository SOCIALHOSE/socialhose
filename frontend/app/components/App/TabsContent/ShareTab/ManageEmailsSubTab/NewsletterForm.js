import PropTypes from 'prop-types'
import {NewsletterForm as BaseNewsletterForm} from '../NotificatoinsSubTab/forms/NewsletterForm'
import {translate} from 'react-i18next'

export class NewsletterForm extends BaseNewsletterForm {
  static propTypes = {
    t: PropTypes.func.isRequired,
    state: PropTypes.object.isRequired,
    actions: PropTypes.object.isRequired
  };

}

export default translate(['tabsContent'], { wait: true })(NewsletterForm)
