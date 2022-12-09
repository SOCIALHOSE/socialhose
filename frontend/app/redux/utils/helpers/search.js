import {fromJS} from 'immutable'
import {ADV_FILTERS_LIMIT} from '../../modules/appState/search'
/**
 * Commenting helpers
 */

export const indexById = (arr, id) => arr.findIndex((a) => a.id === id)

const changeArticleComments = (fn) => {
  return (articles, articleId, comment) => {
    let result = articles.toJS()
    const articleIndex = indexById(result, articleId)
    if (articleIndex !== -1) {
      fn(result[articleIndex].comments, comment)
    } else {
      console.error(`search.js - cannot find article with id ${articleId}`)
    }
    return fromJS(result)
  }
}

export const loadMoreComments = changeArticleComments((comments, newComments) => {
  comments.data = comments.data.concat(newComments)
  comments.count += newComments.length
})

export const addComment = changeArticleComments((comments, comment) => {
  comments.data.unshift(comment)
  comments.count++
  comments.totalCount++
})

export const updateComment = changeArticleComments((comments, comment) => {
  const commentIndex = indexById(comments.data, comment.id)
  if (commentIndex !== -1) {
    comments.data[commentIndex] = comment
  } else {
    console.error(`search.js::updateComment() cannot find comment with id ${comment.id}`)
  }
})

export const deleteComment = changeArticleComments((comments, commentId) => {
  const commentIndex = indexById(comments.data, commentId)
  if (commentIndex !== -1) {
    comments.data.splice(commentIndex, 1)
    comments.count--
    comments.totalCount--
  } else {
    console.error(`search.js::deleteComment() cannot find comment with id ${commentId}`)
  }
})

//End commenting helpers

//Ensure that "selectedFilters" is always in "allFilters"
//allFilters = {groupName: [{value: "", count: ""}] }
//selectedFilters = {groupName: {"value": "count"}}
export const mergeAdvancedFilters = (allFilters, selectedFilters, pages) => {

  const _insertFilter = (groupName, value, count) => {
    let group = allFilters[groupName] || (allFilters[groupName] = [])

    const found = group.find((item) => item.value === value)
    if (!found) {
      group.unshift({value, count})
      let pageGroup = pages[groupName] || (pages[groupName] = {count: ADV_FILTERS_LIMIT, totalCount: 0})
      pageGroup.totalCount++
    }
  }

  for (let groupName in selectedFilters) {
    if (selectedFilters.hasOwnProperty(groupName)) {
      let group = selectedFilters[groupName]

      for (let value in group) {
        if (group.hasOwnProperty(value)) {
          _insertFilter(groupName, value, group[value])
        }
      }

    }
  }

}
