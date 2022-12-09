import React from 'react'
import PropTypes from 'prop-types'
import { translate } from 'react-i18next'
import moment from 'moment'
import Select from 'react-select'
import {
  Button,
  Modal,
  ModalHeader,
  ModalBody,
  Label,
  Input,
  ModalFooter,
  FormGroup,
  Col,
  Container
} from 'reactstrap'
import QuillEditor from '../../../../common/QuillEditor'

const replyToEmail = 'support@socialhose.io'

export class EmailArticlesPopup extends React.Component {
  static propTypes = {
    articlesToEmail: PropTypes.array.isRequired,
    emailArticles: PropTypes.func.isRequired,
    hidePopup: PropTypes.func.isRequired,
    recipients: PropTypes.object.isRequired,
    loadRecipients: PropTypes.func.isRequired,
    children: PropTypes.any,
    t: PropTypes.func.isRequired
  }

  constructor(props) {
    super(props)
    this.state = {
      selectedRecipients: ''
    }
    this.editorRef = React.createRef()
  }

  componentWillMount = () => {
    !this.props.recipients.all.length && this.props.loadRecipients()
  }

  componentDidMount = () => {
    this.props.loadRecipients()
  }

  hidePopup = () => {
    this.props.hidePopup()
  }

  collectParams = () => { // need to change with states
    const recipients = this.state.selectedRecipients
    if (!recipients) return false
    return {
      emailTo: recipients.map((r) => r.value),
      emailReplyTo: document.getElementById('email-reply-to').value,
      subject: document.getElementById('email-subject').value,
      content: this.editorRef.current && this.editorRef.current.root.innerHTML
    }
  }

  onSubmit = () => {
    const params = this.collectParams()
    if (params) {
      this.props.emailArticles(params)
    }
  }

  changeRecipient = (value) => {
    this.setState({
      selectedRecipients: value
    })
  }

  validEmails = (str) => {
    const re = /\S+@\S+\.\S+/
    const arr = str.split(',')
    for (let s of arr) {
      if (!re.test(s)) {
        return false
      }
    }
    return true
  }

  emailRe = /\S+@\S+\.\S+/

  isValidNewOption = ({ label }) => {
    return this.emailRe.test(label)
  }

  promptTextCreator = (label) => {
    return label
  }

  render() {
    const { t, articlesToEmail, recipients } = this.props
    const { selectedRecipients } = this.state

    const recipientsAll = recipients.all.map((recipient) => ({
      value: recipient,
      label: recipient
    }))

    return (
      <Modal
        isOpen
        size="lg"
        toggle={this.hidePopup}
        backdrop="static"
      >
        <ModalHeader toggle={this.hidePopup}>
          {t('searchTab.emailPopup.header')}
        </ModalHeader>
        <ModalBody>
          <Container>
            <FormGroup row>
              <Label htmlFor="email-to" sm={2}>
                {t('searchTab.emailPopup.labelTo')}
              </Label>
              <Col sm={10}>
                {recipients.pending && <i className="fa fa-spinner fa-pulse m-2" />}
                {!recipients.pending && (
                  <Select.Creatable
                    multi
                    value={selectedRecipients}
                    options={recipientsAll}
                    onChange={this.changeRecipient}
                    isValidNewOption={this.isValidNewOption}
                    promptTextCreator={this.promptTextCreator}
                    noResultsText="Email not valid"
                  />
                )}
              </Col>
            </FormGroup>
            <FormGroup row>
              <Label htmlFor="email-reply-to" sm={2}>
                {t('searchTab.emailPopup.labelReplyTo')}
              </Label>
              <Col sm={10}>
                <Input
                  type="email"
                  id="email-reply-to"
                  defaultValue={replyToEmail}
                />
              </Col>
            </FormGroup>
            <FormGroup row>
              <Label htmlFor="email-subject" sm={2}>
                {t('searchTab.emailPopup.labelSubject')}
              </Label>
              <Col sm={10}>
                <Input type="text" id="email-subject" />
              </Col>
            </FormGroup>

            <div className="email-popup">
              <QuillEditor
                className="email-popup__articles email-editor"
                reference={this.editorRef}
                id="email-editor"
              >
                {articlesToEmail.map((article) => {
                  return (
                    <div className="email-popup__article" key={article.id}>
                      <h2 className="article__title">
                        <a href={article.source.link}>{article.title}</a>
                      </h2>

                      <div className="article__about-info">
                        <a href={article.source.link} target="blank">
                          {article.source.title}
                        </a>{' '}
                        <span> | </span>
                        <a href={article.author.link} target="blank">
                          {article.author.name}
                        </a>{' '}
                        <span> | </span>
                        {moment(article.published).format('LLL')}
                      </div>

                      <p className="article__desc">{article.content}</p>
                    </div>
                  )
                })}
              </QuillEditor>
            </div>
          </Container>
          {this.props.children}
        </ModalBody>

        <ModalFooter>
          <Button color="light" onClick={this.hidePopup}>
            {t('common:commonWords.Cancel')}
          </Button>
          <Button color="primary" onClick={this.onSubmit}>
            {t('searchTab.emailPopup.submitBtn')}
          </Button>
        </ModalFooter>
      </Modal>
    )
  }
}

export default translate(['tabsContent', 'common'], { wait: true })(
  EmailArticlesPopup
)
