import React, { useEffect, useRef, useState } from 'react'
import PropTypes from 'prop-types'
import echarts from 'echarts'
import cx from 'classnames'

function ECharts(props) {
  const { options, style, className, loading, message } = props
  const [chart, setChart] = useState(null)
  const chartRef = useRef()

  useEffect(() => {
    const chart = echarts.init(chartRef.current, 'westeros')
    chart.setOption({ ...options, resizeObserver }, true) // second param is for 'noMerge'
    setChart(chart)
    if (resizeObserver) resizeObserver.observe(chartRef.current)
  }, [options])

  useEffect(() => {
    if (!chart) {
      return
    }
    if (loading) {
      chart.showLoading()
      return
    }

    chart.hideLoading()
  }, [chart, loading])

  useEffect(() => {
    if (chart && options && message) {
      chart.clear()
    }
  }, [message])

  const newStyle = {
    height: 350,
    ...style
  }

  return (
    <div className="echarts-parent position-relative">
      <div
        ref={chartRef}
        style={newStyle}
        className={cx('echarts-react', className)}
      />
      {message ? <div className="no-data">{message}</div> : null}
    </div>
  )
}

ECharts.propTypes = {
  loading: PropTypes.bool,
  options: PropTypes.any,
  className: PropTypes.string,
  style: PropTypes.object,
  message: PropTypes.any
}

const resizeObserver = new window.ResizeObserver((entries) => {
  entries.map(({ target }) => {
    const instance = echarts.getInstanceByDom(target)
    if (instance) {
      instance.resize()
    }
  })
})

export default React.memo(ECharts)
