import {createAction, handleActions} from 'redux-actions'
import {fromJS} from 'immutable'
import * as api from '../../../api/articlesApi'
import {addAlert} from '../common/alerts'
import {getSidebarCategories} from './sidebar'
import {thunkAction} from '../../utils/common'
import * as _ from 'lodash'

/**
 *
 * Constants
 */
const NS = '[Articles]'
const SHOW_DELETE_POPUP = `${NS} Show delete popup`
const HIDE_DELETE_POPUP = `${NS} Hide delete popup`
const SHOW_EMAIL_POPUP = `${NS} Show email popup`
const HIDE_EMAIL_POPUP = `${NS} Hide email popup`
const SHOW_COMMENT_POPUP = `${NS} Show comment popup`
const HIDE_COMMENT_POPUP = `${NS} Hide comment popup`
const SHOW_CLIP_POPUP = `${NS} Show clip popup`
const HIDE_CLIP_POPUP = `${NS} Hide clip popup`
const SHOW_EMAIL_CONFIRM_POPUP = `${NS} Show email confirm popup`
const HIDE_EMAIL_CONFIRM_POPUP = `${NS} Hide email confirm popup`
const SET_EMAIL_PARAMS = `${NS} Set email params`

//This actions, when fulfilled reduced in search.js! so export this
export const COMMENT_ARTICLE = `${NS} Comment article`
export const UPDATE_COMMENT = `${NS} Update comment`
export const DELETE_COMMENT = `${NS} Delete comment`
export const LOAD_MORE_COMMENTS = `${NS} Load more comments`

export const DELETE_ARTICLES_FROM_FEED = `${NS} Delete articles from feed`
export const DELETE_ARTICLES = `${NS} Delete articles from search results`

const EMAIL_ARTICLES = `${NS} Email articles`
const SEND_DOCUMENTS_BY_EMAIL = `${NS} Send documents by email`
const CLIP_ARTICLES = `${NS} Clip articles`
const GET_RECENT_CLIP_FEEDS = `${NS} Get recent clip feeds`
const READ_ARTICLE_LATER = `${NS} Read article later`
const LOAD_RECIPIENTS = `${NS} Load recipients`

const SHOW_SHARE_MENU = `${NS} Show share menu`
const HIDE_SHARE_MENU = `${NS} Hide share menu`

export const ARTICLE_COMMENTS_LIMIT = 100

/**
 * Actions
 */
const showDeleteArticlesPopup = createAction(SHOW_DELETE_POPUP, articles => articles)
const showEmailArticlesPopup = createAction(SHOW_EMAIL_POPUP, articles => articles)
const showCommentArticlePopup = createAction(SHOW_COMMENT_POPUP, (article, comment) => ({article, comment}))
const showClipArticlesPopup = createAction(SHOW_CLIP_POPUP, articles => articles)
const showEmailConfirmPopup = createAction(SHOW_EMAIL_CONFIRM_POPUP)
const showShareMenu = createAction(SHOW_SHARE_MENU, article => article)
const hideDeleteArticlesPopup = createAction(HIDE_DELETE_POPUP)
const hideEmailArticlesPopup = createAction(HIDE_EMAIL_POPUP)
const hideCommentArticlePopup = createAction(HIDE_COMMENT_POPUP)
const hideClipArticlesPopup = createAction(HIDE_CLIP_POPUP)
const hideEmailConfirmPopup = createAction(HIDE_EMAIL_CONFIRM_POPUP)
const hideShareMenu = createAction(HIDE_SHARE_MENU)

const commentArticle = thunkAction(COMMENT_ARTICLE, (comment, articleId, {token, fulfilled}) => {
  return api
    .commentDocument(token, comment, articleId)
    .then((comment) => {
      fulfilled({comment, articleId})
    })
})

const updateComment = thunkAction(UPDATE_COMMENT, (newComment, articleId, {getState, token, fulfilled}) => {
  const commentToUpdate = getState().getIn(['appState', 'articles', 'commentPopup', 'comment']).toJS()
  return api
    .updateComment(token, newComment, commentToUpdate.id)
    .then((comment) => {
      fulfilled({comment, articleId})
    })
})

const deleteComment = thunkAction(DELETE_COMMENT, (commentId, articleId, {token, fulfilled}) => {
  return api
    .deleteComment(token, undefined, commentId)
    .then(() => {
      fulfilled({commentId, articleId})
    })
})

const loadMoreComments = thunkAction(LOAD_MORE_COMMENTS, (articleId, offset, {token, fulfilled}) => {
  return api
    .getComments(token, {offset: offset, limit: ARTICLE_COMMENTS_LIMIT}, articleId)
    .then((response) => {
      fulfilled({response, articleId})
    })
})

const deleteArticles = createAction(DELETE_ARTICLES, ids => ids)

const deleteArticlesFromFeed = thunkAction(DELETE_ARTICLES_FROM_FEED, (ids, feedId, {token, dispatch, fulfilled}) => {
  return api
    .deleteDocumentsFromFeed(token, ids, feedId)
    .then(() => {
      dispatch(deleteArticles(ids))
      fulfilled({ids, feedId})
      dispatch(addAlert({
        type: 'notice',
        transKey: 'articleDeleted'
      }))
    })
})

const setEmailParams = createAction(SET_EMAIL_PARAMS, params => params)

const emailArticles = thunkAction(EMAIL_ARTICLES, (params, {dispatch}) => {
  dispatch(setEmailParams(params))
  if (params.subject) {
    dispatch(sendDocumentsByEmail())
  } else {
    dispatch(showEmailConfirmPopup())
  }
})

const sendDocumentsByEmail = thunkAction(SEND_DOCUMENTS_BY_EMAIL, ({token, getState, fulfilled, dispatch}) => {
  const params = getState().getIn(['appState', 'articles', 'emailPopup', 'emailParams'])
  return api
    .sendDocumentsByEmail(token, params)
    .then(() => {
      dispatch(hideEmailArticlesPopup())
      fulfilled()
    })
})

const clipArticles = thunkAction(CLIP_ARTICLES, (feedId, {token, fulfilled, getState, dispatch}) => {
  const articlesToClip = getState().getIn(['appState', 'articles', 'clipPopup', 'articles']).toJS()
  const documentIds = articlesToClip.map((a) => a.id)
  return api
    .clipDocuments(token, documentIds, feedId)
    .then(() => {
      dispatch(addAlert([{type: 'notice', transKey: 'clipDocument'}]))
      dispatch(hideClipArticlesPopup())
      fulfilled()
    })
})

const getRecentClipFeeds = thunkAction(GET_RECENT_CLIP_FEEDS, ({token, fulfilled}) => {
  return api
    .getRecentClipFeeds(token)
    .then(fulfilled)
})

const readArticleLater = thunkAction(READ_ARTICLE_LATER, (article, {token, dispatch, fulfilled}) => {
  return api
    .readLater(token, undefined, article.id)
    .then(() => {
      dispatch(addAlert([{type: 'notice', transKey: 'clipDocument'}]))
      dispatch(getSidebarCategories())
      fulfilled()
    })
})

const loadRecipients = thunkAction(LOAD_RECIPIENTS, ({getState, fulfilled}) => {
  setTimeout(() => {
    const user = getState().getIn(['common', 'auth', 'user'])
    fulfilled([user.get('email')])
  }, 100)
}, true)

export const actions = {
  showDeleteArticlesPopup,
  showCommentArticlePopup,
  showEmailArticlesPopup,
  showClipArticlesPopup,
  showEmailConfirmPopup,
  showShareMenu,
  hideDeleteArticlesPopup,
  hideCommentArticlePopup,
  hideEmailArticlesPopup,
  hideClipArticlesPopup,
  hideEmailConfirmPopup,
  hideShareMenu,
  commentArticle,
  updateComment,
  deleteComment,
  loadMoreComments,
  deleteArticles,
  deleteArticlesFromFeed,
  emailArticles,
  clipArticles,
  getRecentClipFeeds,
  readArticleLater,
  loadRecipients,
  sendDocumentsByEmail
}

/**
 * State
 */
export const initialState = fromJS({
  emailPopup: {
    visible: false,
    articles: [],
    recipients: {
      all: [],
      pending: false
    },
    emailParams: null
  },
  deletePopup: {
    visible: false,
    articles: []
  },
  clipPopup: {
    visible: false,
    articles: []
  },
  commentPopup: {
    visible: false,
    article: null,
    comment: null
  },
  emailConfirmPopup: {
    visible: false
  },
  shareMenu: {
    visible: false,
    article: null
  },
  excludedArticles: [], //map of ids
  recentClipFeeds: []
})

/**
 * Reducers
 */
const hidePopup = (type) => (state) =>
  state.mergeIn([type + 'Popup'], {visible: false, articles: []})

const showPopup = (type) => (state, {payload: articles}) =>
  state.mergeIn([type + 'Popup'], {visible: true, articles: articles})

export default handleActions({

  [SHOW_DELETE_POPUP]: showPopup('delete'),
  [SHOW_EMAIL_POPUP]: showPopup('email'),
  [SHOW_CLIP_POPUP]: showPopup('clip'),
  [SHOW_EMAIL_CONFIRM_POPUP]: showPopup('emailConfirm'),
  [HIDE_DELETE_POPUP]: hidePopup('delete'),
  [HIDE_EMAIL_POPUP]: hidePopup('email'),
  [HIDE_CLIP_POPUP]: hidePopup('clip'),
  [HIDE_EMAIL_CONFIRM_POPUP]: hidePopup('emailConfirm'),

  [SHOW_COMMENT_POPUP]: (state, {payload: {article, comment}}) => {
    return state.mergeIn(['commentPopup'], {
      visible: true,
      article: article,
      comment: comment
    })
  },

  [HIDE_COMMENT_POPUP]: (state) => {
    return state.mergeIn(['commentPopup'], {
      visible: false,
      article: null,
      comment: null
    })
  },

  [DELETE_ARTICLES]: (state, {payload: ids}) => {
    const excludedArticles = state.get('excludedArticles')
    const results = _.union(excludedArticles, ids)
    return state.set('excludedArticles', results)
  },

  [`${GET_RECENT_CLIP_FEEDS} fulfilled`]: (state, {payload: recentClipFeeds}) => {
    return state.set('recentClipFeeds', recentClipFeeds)
  },

  [SHOW_SHARE_MENU]: (state, {payload: article}) => {
    return state.mergeIn(['shareMenu'], {
      visible: true,
      article
    })
  },

  [HIDE_SHARE_MENU]: (state) => {
    return state.setIn(['shareMenu', 'visible'], false)
  },

  [`${LOAD_RECIPIENTS} pending`]: (state, {payload: {isPending}}) => {
    return state.setIn(['emailPopup', 'recipients', 'pending'], isPending)
  },

  [`${LOAD_RECIPIENTS} fulfilled`]: (state, {payload: emails}) => {
    return state.setIn(['emailPopup', 'recipients', 'all'], emails)
  },

  [SET_EMAIL_PARAMS]: (state, {payload: params}) => state.setIn(['emailPopup', 'emailParams'], params)

}, initialState)

