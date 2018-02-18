// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue'
import Css from './assets/css/app.less'
import App from './App'
import router from './router'
import store from './store'
import $ from 'jquery'

/* eslint-disable no-new */
new Vue({
  el: '#app',
  template: '<App/>',
  router,
  store,
  components: { Css },
  render: h => h(App)
})

// Touche du clavier : recherche par défaut
$(document).on('keypress', function (event) {
  // Si aucun élément possède le focus ou si le focus est sur un lien, on
  // déplace le focus sur le moteur de recherche
  if (!$(':focus').length || $(':focus')[0].nodeName === 'A') {
    $('#ddms').focus()
  }
})
