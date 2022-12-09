export const BarToolbox = {
  feature: {
    saveAsImage: {
      name: 'Socialhose Chart',
      show: true,
      title: 'Save as Image',
      // name: 'Results Over Time', // need to set dynamic
      type: 'jpeg',
      backgroundColor: '#FFFFFF',
      pixelRatio: 2
    },
    dataZoom: {
      show: true,
      title: {
        zoom: 'Zoom',
        back: 'Restore Zoom'
      }
    },
    dataView: {
      show: true,
      title: 'View Data',
      readOnly: true,
      lang: ['View Data', 'Close', 'Refresh']
    },
    magicType: {
      show: true,
      title: {
        line: 'Line Chart',
        bar: 'Bar Chart',
        tiled: 'Tiled Chart'
      },
      type: ['line', 'bar', 'tiled']
    },
    restore: { show: true, title: 'Restore' }
  }
}

export const PieToolbox = {
  feature: {
    saveAsImage: {
      name: 'Socialhose Chart',
      show: true,
      title: 'Save as Image',
      // name: 'Share of Topics', // need to set dynamic
      type: 'jpeg',
      backgroundColor: '#FFFFFF',
      pixelRatio: 2
    },
    dataView: {
      show: true,
      readOnly: true,
      title: 'View Data',
      lang: ['View Data', 'Close', 'Refresh']
    },
    restore: { show: true, title: 'Restore' }
  }
}

export function getBarOptions(data, labels) {
  return {
    tooltip: {
      show: true
    },
    toolbox: BarToolbox,
    xAxis: {
      type: 'category',
      data: labels
    },
    yAxis: {
      type: 'value'
    },
    series: data,
    legend: {
      y: 'bottom',
      show: true
    }
  }
}

export function getPieOptions(data) {
  return {
    tooltip: {
      show: true
    },
    toolbox: PieToolbox,
    series: {
      type: 'pie',
      data: data,
      label: {
        position: 'outer',
        alignTo: 'none',
        bleedMargin: 5
      },
      top: '10%',
      bottom: '10%'
    },
    legend: {
      top: 'bottom',
      show: true
    }
  }
}

export const WordCloudOptions = {
  type: 'wordCloud',
  shape: 'circle',
  sizeRange: [12, 35],
  rotationRange: [0, 0],
  width: '100%',
  height: '100%',
  top: '10%',
  bottom: '10%',
  drawOutOfBound: false,
  gridSize: 8,
  textStyle: {
    normal: {
      fontFamily: 'sans-serif',
      fontWeight: 'bold',
      // Color can be a callback function or a color string
      color: function () {
        // Random color
        return (
          'rgb(' +
          [
            Math.round(Math.random() * 160),
            Math.round(Math.random() * 160),
            Math.round(Math.random() * 160)
          ].join(',') +
          ')'
        )
      }
    },
    emphasis: {
      shadowBlur: 1,
      shadowColor: '#333'
    }
  }
}
