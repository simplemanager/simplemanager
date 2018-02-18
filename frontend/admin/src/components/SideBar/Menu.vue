<template>
  <ul class="sidebar-menu" v-if="typeof menu === 'object'">
    <li v-for="item in menu" 
        :class="[ typeof item === 'object' ? 'treeview' : 'header', isActive(item) ? 'active' : '' ]">
      {{ typeof item === 'string' ? item : null }}
      <router-link :to="item.params && !item.items ? { name: 'default', params: item.params } : ''" v-if="typeof item === 'object'"><i class="fa" :class="[ 'fa-' + item.icon, item.color ? 'text-' + item.color : null ]"></i>&nbsp;
        <span>{{ item.label }}</span>
        <span class="pull-right-container" v-if="item.items && !item.badges">
          <i class="fa fa-angle-left pull-right"></i>
        </span>
        <span class="pull-right-container" v-if="item.badges">
          <small v-for="badge in item.badges" class="label pull-right" :class="'bg-' + (badge[1] ? badge[1] : 'primary')">{{ badge[0] }}</small>
        </span>
      </router-link>
      <ul class="treeview-menu" v-if="item.items">
        <li v-for="subitem in item.items" :class="isActive(subitem) ? 'active' : ''">
          <router-link :to="{ name: 'default', params: subitem.params, query: subitem.query }">
            <i class="fa fa-angle-right" :class="subitem.color ? 'text-' + subitem.color : (item.color ? 'text-' + item.color : null)"></i>{{ subitem.label }}
          </router-link>
        </li>
      </ul>
    </li>
  </ul>
</template>

<script>
  import { mapGetters } from 'vuex'
  export default {
    name: 'sidebarmenu',
    methods: {
      isActive (item) {
        if (!item.params || !item.params.controller) {
          return false
        }
        var ctrlMatch = this.$route.params.controller === item.params.controller
        var actionMatch = !item.params.action || (this.$route.params.action === item.params.action)
        return ctrlMatch && actionMatch
      }
    },
    computed: {
      ...mapGetters([
        'sidebarMenu'
      ]),
      menu () {
        return this.sidebarMenu
      }
    }
  }
</script>

<style>
</style>