import {fromJS} from 'immutable'

const categoriesFromState = (fn) => {
  return (state, ...args) => {
    const categories = state.getIn(['appState', 'sidebar', 'categories']).toJS()
    return fromJS(fn(categories, ...args))
  }
}

const walkCategories = (fn) => {
  return (categories, ...args) => {
    const walker = (array) => {
      return array.map((category) => {
        category.childes = walker(category.childes)
        fn(category, ...args)
        return category
      })
    }
    return walker(categories)
  }
}

const feedHelper = (fn) => categoriesFromState(walkCategories(fn))

//return categories without deleted feed
export const deleteFeed = feedHelper((category, feedCategoryId, feedId) => {
  if (category.id === feedCategoryId) {
    category.feeds = category.feeds.filter((feed) => feed.id !== feedId)
  }
})

export const addFeed = feedHelper((category, feedCategoryId, feed) => {
  if (category.id === feedCategoryId) {
    category.feeds.push(feed)
  }
})

export const renameFeed = feedHelper((category, feedId, newFeedName, feedCategoryId) => {
  if (category.id === feedCategoryId) {
    category.feeds = category.feeds.map((feed) => {
      if (feed.id === feedId) {
        feed.name = newFeedName
      }
      return feed
    })
  }
})

const _deleteCategory = (categories, categoryId) => {
  return categories.map((category) => {
    if (category.childes.length > 0) {
      const initChildesLength = category.childes.length
      category.childes = category.childes.filter((childCategory) => {
        return childCategory.id !== categoryId
      })
      const childesLengthAfterFilter = category.childes.length
      // if there wasn't deletion continue to search
      if (childesLengthAfterFilter === initChildesLength) {
        category.childes = _deleteCategory(category.childes, categoryId)
      }
    }
    return category
  })
}

//return categories without deleted category
export const deleteCategory = categoriesFromState(_deleteCategory)

export const addCategory = feedHelper((category, parentId, newCategory) => {
  if (category.id === parentId) {
    category.childes.push(newCategory)
  }
})

export const renameCategory = feedHelper((category, categoryId, newCategoryName) => {
  if (category.id === categoryId) {
    category.name = newCategoryName
  }
})

export const checkIfDraggedCategoryDragToItsChild = (categories, newCategoryId) => {
  return categories.find((category) => {
    if (category.childes.length > 0) {
      checkIfDraggedCategoryDragToItsChild(category.childes, newCategoryId)
    }

    return category.id === newCategoryId
  })
}

export const findFeedById = (categories, feedId) => {
  let feed = null
  categories.forEach((category) => {
    if (!feed && category.feeds.length > 0) {
      const findResult = category.feeds.filter(feed => feed.id === feedId)
      feed = findResult[0] || null
      if (feed) {
        feed.category = category.id
      }

      if (!feed && category.childes.length > 0) {
        feed = findFeedById(category.childes, feedId)
      }
    }
  })
  return feed
}
