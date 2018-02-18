import Vue from 'vue'
import VueRouter from 'vue-router'
import $ from 'jquery'
import ajax from './ajaxCall.js'
import ts from './status.js'

Vue.use(VueRouter)

var router = new VueRouter({
  mode: 'history',
  routes: [{
    path: '/:controller?/:action?/(.*)?',
    name: 'default'
  }]
})

$.router = router
$.ajaxCall = ajax
$.ts = ts
export default router
