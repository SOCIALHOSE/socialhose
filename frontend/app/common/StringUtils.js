export const padLeft = function (string, total) {
  if (typeof string !== 'string') {
    throw new Error('First parameter must be a string')
  }
  if (typeof total !== 'number') {
    throw new Error('Second parameter must be a integer')
  }
  return new Array(total - string.length + 1).join('0') + string
}

export const addOrdinalSuffix = function (num) {
  if (typeof num !== 'number') {
    return num
  }

  const j = num % 10
  const k = num % 100

  if (j === 1 && k !== 11) {
    return num + 'st'
  }
  if (j === 2 && k !== 12) {
    return num + 'nd'
  }
  if (j === 3 && k !== 13) {
    return num + 'rd'
  }

  return num + 'th'
}
