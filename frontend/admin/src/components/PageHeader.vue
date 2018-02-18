<template>
  <header class="main-header">
    <router-link to="/" class="logo" style="padding: 0 0 0 3px; margin: 0">
      <span class="logo-mini"><img class="logo-img" src="/static/favicon.png" :alt="headerTitleFiltered[1]" :title="headerTitleFiltered[0]" /></span><!-- {{ headerTitleFiltered[1] }} -->
      <span class="logo-lg"><strong>{{ headerTitleFiltered[0] }}</strong></span>
    </router-link>
    <nav class="navbar navbar-static-top" role="navigation">
      <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a>
      <search></search>
      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          <debug v-if="isDebug"></debug>
          <dropdownbutton v-for="button in headerButtons" v-bind:content="button"></dropdownbutton>
          <test v-if="isLogged"></test>
          <user></user>
          <!-- li class="hidden-xs">
            <a href="#" data-toggle="control-sidebar" data-controlsidebar="control-sidebar-open"><i class="fa fa-gears"></i></a>
          </li -->
        </ul>
      </div>
    </nav>
  </header>
</template>

<script>
  import search from './PageHeader/Search.vue'
  import dropdownbutton from './PageHeader/DropdownButton.vue'
  import debug from './PageHeader/Debug.vue'
  import test from './PageHeader/Test.vue'
  import user from './PageHeader/User.vue'
  import { mapGetters } from 'vuex'
  export default {
    name: 'pageheader',
    components: { search, dropdownbutton, user, debug, test },
    computed: {
      ...mapGetters([
        'headerButtons',
        'headerTitle',
        'headerUser'
      ]),
      headerTitleFiltered () {
        var htitle = [
          this.headerTitle && this.headerTitle[0] ? this.headerTitle[0] : '',
          this.headerTitle && this.headerTitle[1] ? this.headerTitle[1] : ''
        ]
        return htitle
      },
      isDebug () {
        return window.location.hostname === 'dev-www.simplemanager.fr' || window.location.hostname === 'localhost'
      },
      isLogged () {
        return this.headerUser && this.headerUser.username
      }
    }
  }
</script>

<style>
</style>
