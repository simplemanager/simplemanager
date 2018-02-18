<template>
  <li :class="data && data.username ? 'dropdown user user-menu' : ''" v-if="data && data.username">
    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
      <!-- span class="hidden-xs">{{ data.username }}&nbsp;</span -->
      <i class="fa fa-power-off"></i>
    </a>
    <ul class="dropdown-menu" v-if="data && data.username && (data.imgsrc || data.title || data.body || data.footer)">
      <li class="user-header">
        <img :src="data.imgsrc" :alt="data.imgalt" v-if="data.imgsrc">
        <p v-if="data.title && data.subtitle">
          {{ data.title }}
          <small>{{ data.subtitle }}</small>
        </p>
        <p v-if="!data.title && data.subtitle">
          {{ data.subtitle }}
        </p>
      </li>
      <li class="user-body" v-html="data.body" v-if="data.body"></li>
      <li class="user-footer" v-html="data.footer" v-if="data.footer"></li>
    </ul>
    <!-- router-link to="/account/login" v-if="!data || !data.username">
      <i class="fa fa-power-off"></i>
    </router-link -->
  </li>
</template>

<script>
  import { mapGetters } from 'vuex'
  export default {
    name: 'user',
    computed: {
      ...mapGetters([
        'headerUser'
      ]),
      data () {
        // Validate here
        return this.headerUser
      }
    }
  }
</script>

<style>
</style>