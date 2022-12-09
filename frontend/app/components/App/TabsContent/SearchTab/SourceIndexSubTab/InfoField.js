import React from 'react';
import PropTypes from 'prop-types';
import { translate } from 'react-i18next';
import { Col } from 'reactstrap';

export class InfoField extends React.Component {
  static propTypes = {
    t: PropTypes.func.isRequired,
    label: PropTypes.string,
    labelValue: PropTypes.string,
    children: PropTypes.oneOfType([PropTypes.string, PropTypes.element])
  };

  render() {
    const { t, label, children, labelValue } = this.props;

    return (
      <li className="row">
        <Col sm="4">
          <p className="mb-1">{labelValue || t(label)}</p>
        </Col>
        <Col sm="8">
          <p className="mb-1">{children}</p>
        </Col>
      </li>
    );
  }
}

export default translate(['tabsContent'], { wait: true })(InfoField);
