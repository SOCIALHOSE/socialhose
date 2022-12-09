import $ from 'jquery'
import config from '../appConfig'

export const parseSearchDays = function (date) {
  const period = date.slice(-1)
  const dateNum = parseInt(date)

  if (period === 'd') {
    return dateNum
  } else {
    let daysCount = 0

    for (let i = 0; i <= dateNum; i++) {
      const date = new Date(new Date().setFullYear(new Date().getFullYear() - i))
      daysCount += date.getFullYear() % 4 === 0 ? 366 : 365
      console.log(daysCount)
    }
    return daysCount
  }
}

export const makeStickySidebar = function ({component, sidebarSelector, footerSelector, sidebarTopMargin, sidebarBottomMargin}) {
  const sidebarEl = $(sidebarSelector)
  const footerEl = $(footerSelector)
  const sidebarTopPos = parseInt(sidebarEl.css('top'))
  const sidebarBottomPos = parseInt(sidebarEl.css('bottom'))

  let windowHeight = $(window).height()
  let docScrollTop = $(document).scrollTop()

  $(window).on('resize', function () {
    windowHeight = $(window).height() //recalc win height
    _updateSidebarPosition()
  })

  $(window).on('scroll', function () {
    docScrollTop = $(document).scrollTop() //recalc scroll top
    _updateSidebarPosition()
  })

  _updateSidebarPosition()

  function _updateSidebarPosition () {
    const footerTop = footerEl.offset().top
    const windowBottomPos = docScrollTop + windowHeight
    // check if document scrollTop position cross sidebar top position with margin
    //if so we set sidebar top position to its margin value
    if (docScrollTop < sidebarTopPos - sidebarTopMargin) {
      sidebarEl.css('top', sidebarTopPos - docScrollTop)
    } else {
      sidebarEl.css('top', sidebarTopMargin)
    }
    //fixing overlapping on footer
    if (windowBottomPos >= footerTop + sidebarBottomMargin) {
      sidebarEl.css('bottom', sidebarBottomPos - footerTop + windowBottomPos)
    } else {
      sidebarEl.css('bottom', sidebarBottomPos)
    }
  }

  component.componentWillUnmount = function () {
    $(window).off('resize')
    $(window).off('scroll')
  }

  return _updateSidebarPosition
}

//default handler - returns errors field from server response or throw error with given text
const defaultApiErrorHandler = (jqXHR, transKey = 'unknown', message = 'Unknown error') => {
  if (jqXHR.status === 402) {
    return []
  }

  if (jqXHR.responseJSON && jqXHR.responseJSON.errors && jqXHR.responseJSON.errors.length) {
    return jqXHR.responseJSON.errors
  } else {
    return [{type: 'error', transKey: transKey, message: message}]
  }
}

export const createApi = (httpMethod, url,
  {
    urlData = false,
    inputData = (payload) => JSON.stringify(payload),
    resolveData = (response) => response,
    rejectData = (defHandler, jqXHR) => { return defHandler(jqXHR) }
  } = {}
) => {
  return (token, payload, ...args) => {
    let requestUrl = url
    if (typeof urlData === 'function') {
      const urlParams = urlData(payload, ...args)
      console.log('%c urlParams=' + JSON.stringify(urlParams), 'color: green')
      requestUrl = url.replace(/\{(.*?)\}/g, function (match, field) {
        return urlParams[field]
      })
    }

    return new Promise((resolve, reject) => {
      let ajaxOptions = {
        type: httpMethod,
        url: config.apiUrl + requestUrl,
        dataType: 'json',
        contentType: 'application/json',
        data: inputData(payload),
        success: function (data) {
          resolve(resolveData(data))
        },
        error: function (jqXHR, textStatus, errorThrown) {
          console.log(`%c [API Error] HTTP ${jqXHR.status}, ${errorThrown}`, 'background: red; color: yellow')
          reject(rejectData(defaultApiErrorHandler, jqXHR, textStatus, errorThrown))
        }
      }

      if (token) {
        ajaxOptions.headers = {
          Authorization: 'Bearer ' + token
        }
      }

      // Used for backend debugging :)
      if (__DEV__) {
        ajaxOptions['xhrFields'] = {
          withCredentials: true
        }
      }

      $.ajax(ajaxOptions)
    })
  }
}

export const mockApi = (fakeData, timeout = 2000) => () => {
  return new Promise((resolve) => {
    setTimeout(() => {
      resolve(fakeData)
    }, timeout)
  })
}
