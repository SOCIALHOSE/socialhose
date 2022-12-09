import {createApi} from '../common/Common'

/**
 * payload: {ids: [...]}
 */
export const deleteDocumentsFromFeed = createApi('POST', '/api/v1/feed/{feedId}/documents/delete', {
  inputData: (idsArray) => JSON.stringify({ids: idsArray}),
  urlData: (params, feedId) => ({feedId})
})

/**
 * payload: {emailTo, emailReplyTo, subject, content}
 */
export const sendDocumentsByEmail = createApi('POST', '/api/v1/documents/email', {
})

/**
 * payload: {title, comment}
 */
export const commentDocument = createApi('POST', '/api/v1/documents/{documentId}/comments', {
  urlData: (params, documentId) => ({documentId})
})

/**
 * payload: {ids: []}
 */
export const clipDocuments = createApi('POST', '/api/v1/feed/{feedId}/documents/clip', {
  urlData: (params, feedId) => ({feedId}),
  inputData: (idsArray) => JSON.stringify({ids: idsArray})
})

/**
 * payload: {title, comment}
 */
export const updateComment = createApi('PUT', '/api/v1/comments/{commentId}', {
  urlData: (params, commentId) => ({commentId})
})

export const deleteComment = createApi('DELETE', '/api/v1/comments/{commentId}', {
  urlData: (params, commentId) => ({commentId})
})

export const getComments = createApi('GET', '/api/v1/documents/{documentId}/comments', {
  inputData: (params) => params,
  urlData: (params, documentId) => ({documentId})
})

export const readLater = createApi('POST', '/api/v1/feed/readLater/{documentId}', {
  urlData: (params, documentId) => ({documentId})
})

export const getRecentClipFeeds = createApi('GET', '/api/v1/feed/recentClip')
