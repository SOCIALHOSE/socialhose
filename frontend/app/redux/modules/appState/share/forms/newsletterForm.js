import {NotificationForm} from './notificationForm'

export class NewsletterForm extends NotificationForm {

  getNamespace () {
    return '[Newsletter Form]'
  }

  getInitialState () {
    return {
    }
  }

}

const instance = new NewsletterForm()
instance.init()
export default instance
