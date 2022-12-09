import React, { useEffect } from 'react'
import PropTypes from 'prop-types'
import Quill from 'quill'
import 'quill/dist/quill.core.css'
import 'quill/dist/quill.snow.css'

function QuillEditor({ id, children, reference, className }) {
  const editorRef = reference

  useEffect(() => {
    // all custom font-sizes and font-families should set up in the whitelist first
    const size = Quill.import('attributors/style/size')
    const font = Quill.import('formats/font')

    size.whitelist = ['10px', '13px', '16px', '18px', '24px', '32px', '48px']
    font.whitelist = [
      'roboto',
      'lato',
      'times',
      'arial',
      'courier',
      'georgia',
      'trebuchet',
      'verdana'
    ]

    Quill.register(size, true)
    Quill.register(font, true)

    //all custom labels and font-families are setting up via css, library works that way, in our case we setting up font-families and font-sizes
    const toolbarOptions = [
      [
        {
          font: [
            'roboto',
            'lato',
            'times',
            'arial',
            'courier',
            'georgia',
            'trebuchet',
            'verdana'
          ]
        },
        {
          size: ['10px', '12px', '14px', '16px', '18px', '24px', '32px', '48px']
        }
      ],
      ['bold', 'italic', 'underline', 'strike'],
      [{ script: 'sub' }, { script: 'super' }],
      [{ align: [] }],
      [{ indent: '-1' }, { indent: '+1' }],
      [{ list: 'ordered' }, { list: 'bullet' }],
      ['link', 'image'],
      [{ color: [] }, { background: [] }],
      ['clean']
    ]

    editorRef.current = new Quill(`#${id}`, {
      theme: 'snow',
      modules: {
        toolbar: toolbarOptions
      }
    })

    editorRef.current.focus()
  }, [])

  return (
    <div id={id} className={className}>
      {children}
    </div>
  )
}

QuillEditor.propTypes = {
  id: PropTypes.string,
  children: PropTypes.any,
  reference: PropTypes.any,
  className: PropTypes.string
}

export default QuillEditor
