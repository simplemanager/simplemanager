<template>
  <transition name="fade" mode="out-in">
    <li class="dropdown" :class="btn.type + '-menu'" v-if="btn.icon && btn.content.length">
      <!-- Menu Toggle Button -->
      <a href="#" class="dropdown-toggle" data-toggle="dropdown">
        <i class="fa" :class="'fa-' + btn.icon"></i>
        <span class="label" :class="'label-' + btn.labelStatus" v-if="btn.label">{{ btn.label }}</span>
      </a>
      <ul class="dropdown-menu">
        <li class="header" v-if="btn.title">{{ btn.title }}</li>
        <li>
          <ul class="menu">
            <li v-for="item in btnItems">
              <i :id="'nit' + item.id" class="fa fa-times text-red" @click.prevent.stop="suppr(item.id)"></i>
              <a :href="item.url ? item.url : '#'" :class="item.url ? '' : 'disabled'" :target="item.url.substring(0, 4) == 'http' ? '_blank' : ''">
                <i v-if="item.icon" :class="'fa fa-' + item.icon + ' text-' + item.status"></i>
                <div class="hbtntxt">{{ item.data }}</div>
              </a>
            </li>
          </ul>
        </li>
        <li class="footer" v-if="btn.footLinkLabel">
          <!-- @task: voir si on peut enlever name -->
          <a name="footer" :href="btn.footLinkUrl">{{ btn.footLinkLabel }}</a>
        </li>
      </ul>
    </li>
  </transition>
</template>

<script>
import $ from 'jquery'
import { mapActions } from 'vuex'
export default {
  name: 'dropdownbutton',
  props: {
    content: {
      type: Object, // Json data used to populate button
      default () {
        return {
          id: null,
          type: 'notifications', // messages | tasks | notifications
          title: 'Notifications',
          icon: 'bell-o',
          label: '!',
          labelStatus: 'warning',
          content: [],
          footLinkUrl: '#',
          footLinkLabel: 'See All Notifications'
        }
      }
    }
  },
  methods: {
    ...mapActions(['updateNotifs']),
    suppr (id) {
      $('#nit' + id).removeAttr('class').addClass('fa fa-refresh text-gray')
      $('#nit' + id).siblings('a').children('i').removeClass(function (index, className) {
        return (className.match(/(^|\s)text-\S+/g) || []).join(' ')
      }).addClass('text-gray')
      $('#nit' + id).siblings('a').children('div').addClass('text-gray').css('text-decoration', 'line-through')
      var ok = false
      var self = this
      $.ajax({
        url: '/event/rmnot/id/' + id,
        type: 'GET'
      }).done(function (d) {
        if (d === '1') {
          ok = true
          self.updateNotifs(self.content.content.filter(function (elt) {
            return elt.id !== id
          }))
        }
      }).always(function () {
        if (!ok) {
          $('#nit' + id).removeAttr('class').addClass('fa fa-warning text-orange')
        } else {
          $('#nit' + id).removeAttr('class').addClass('fa fa-times text-red')
          $('#nit' + id).siblings('a').children('div').removeClass('text-gray').css('text-decoration', 'none')
        }
      })
    }
  },
  computed: {
    btn () {
      if (typeof this.content.content === 'object') {
        this.content.content.forEach(function (el) {
          el.icon = el.icon ? el.icon : 'circle-o'
          el.status = el.status ? el.status : 'blue'
        })
      }
      return this.content
    },
    btnItems () {
      return this.content.content
    }
  }
}
</script>

<style>
.fade-enter-active, .fade-leave-active {
  transition: opacity .3s
}
.fade-enter, .fade-leave-to /* .fade-leave-active in <2.1.8 */ {
  opacity: 0
}
</style>
