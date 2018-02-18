<template>
  <router-link :to="data.url ? data.url : '#'" v-if="data && data.label">
    <div class="user-panel">
      <div class="pull-left image">
        <i class="fa" :class="data.icon ? 'fa-' + data.icon : 'fa-user'" v-if="!data.imgsrc"></i>
        <img :src="data.imgsrc" class="img-circle" :alt="data.imgalt" v-if="data.imgsrc">
      </div>
      <div class="pull-left info">
        <p>{{ data.label }}</p>
        <small><i class="fa fa-circle" :class="data.status ? 'text-' + data.status : ''"></i>&nbsp;&nbsp;{{ data.subLabel }}</small>
      </div>
    </div>
  </router-link>
</template>

<script>
  import { mapGetters } from 'vuex'
  export default {
    name: 'registration',
    computed: {
      ...mapGetters([
        'sidebarRegistration'
      ]),
      data () {
        // Validate here
        return this.sidebarRegistration
      }
    }
  }
</script>

<style>
</style>