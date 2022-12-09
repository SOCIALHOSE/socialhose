import React from 'react'
import PropTypes from 'prop-types'
import { translate } from 'react-i18next'
import RadioField from '../RadioField'

export class TypeSelector extends React.Component {
  static propTypes = {
    t: PropTypes.func.isRequired,
    types: PropTypes.array.isRequired,
    activeType: PropTypes.string.isRequired,
    onChange: PropTypes.func.isRequired
  };

  render () {
    const { t, types, activeType, onChange } = this.props

    return (
      <div className="d-flex mb-2">
        {types.map((type, i) => {
          return (
            <RadioField
              key={'schedule-type-' + i}
              label={t(`notificationsTab.form.type.${type}`)}
              name="schedule-type"
              checkedValue={activeType}
              value={type}
              onChange={onChange}
            />
          )
        })}
      </div>
    )
  }

}

export default translate(['tabsContent'], { wait: true })(TypeSelector)
