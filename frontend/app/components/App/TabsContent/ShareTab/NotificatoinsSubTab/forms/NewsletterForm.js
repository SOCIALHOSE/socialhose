import React from 'react'
import PropTypes from 'prop-types'
import { translate } from 'react-i18next'
import { Card, CardBody, CardTitle, Col, Form, FormGroup, Input, Label } from 'reactstrap'

export class NewsletterForm extends React.Component {
  static propTypes = {
    t: PropTypes.func.isRequired,
    state: PropTypes.object.isRequired,
    actions: PropTypes.object.isRequired
  };

  changeName = (event) => {
    this.props.actions.changeName(event.target.value)
  };

  render () {
    const { state, t } = this.props

    return (
      <Card className="main-card mb-3">
        <CardBody>
          <CardTitle>{t('notificationsTab.newsLetter.createNewsletter')}</CardTitle>
          <Form>
            <FormGroup row>
              <Label sm={2}>{t('notificationsTab.newsLetter.name')}</Label>
              <Col sm={10}>
                <Input type="text" value={state.name} onChange={this.changeName} />
              </Col>
            </FormGroup>
          </Form>
        </CardBody>
      </Card>
    )

  }

}
export default translate(['tabsContent'], { wait: true })(NewsletterForm)
