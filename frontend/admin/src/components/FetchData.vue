<template>
  <div id="content"></div>
</template>

<script>
import ajaxCall from '../router/ajaxCall.js'
export default {
  name: 'ajax',
  mounted () {
    this.fetchData('/event/init?' + window.location.href.replace(/https?:\/\/[^/]+/, ''))
  },
  watch: {
    '$route': 'fetchData'
  },
  methods: {
    fetchData (url) {
      this.error = null
      this.content = null
      ajaxCall(typeof url === 'string' ? url : url.path, false, false, true)
    }
  }
}
</script>

<style>
</style>
