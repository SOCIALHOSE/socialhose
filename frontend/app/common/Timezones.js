import moment from 'moment-timezone'

const getZonesNames = function () {
  return moment.tz.names()
}

export const getCurrentTimezone = function () {
  return moment.tz.guess()
}

const getTimezones = function () {
  const names = getZonesNames()
  return names.map(name => {
    const zone = moment.tz.zone(name)
    const utc = moment.parseZone(zone).format('Z')
    const label = `(UTC ${utc}) ${name}`
    return {value: name, label}
  })
}

export const timezones = getTimezones()
