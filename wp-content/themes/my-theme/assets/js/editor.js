import '../scss/editor.scss' // mandatory for Hot Module Reload

// Gutenberg ready
if (document.querySelector('.block-editor__container')) {
  let blockLoaded = false
  const blockLoadedInterval = setInterval(function () {
    const editorWrapper = document.querySelector('.editor-styles-wrapper')
    if (editorWrapper) {
      blockLoaded = true

      // DO CODE HERE

      console.log('editor script called')
    }

    if (blockLoaded) {
      clearInterval(blockLoadedInterval)
    }
  }, 500)
}
