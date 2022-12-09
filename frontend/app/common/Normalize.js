export const normalize = function (arr, entityCallback) {
  let ids = []
  let entities = {}
  arr.forEach((item, i) => {
    if (item.id) {
      ids.push(item.id)

      if (entityCallback) {
        entities[item.id] = entityCallback(item, item.id)
      }
      else {
        entities[item.id] = item
      }
    } else {
      ids.push(i.toString())

      if (entityCallback) {
        entities[i] = entityCallback(item, i)
      }
      else {
        entities[i] = item
      }
    }
  })

  return {ids, entities}
}
