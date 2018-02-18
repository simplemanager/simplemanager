<template>
  <footer class="main-footer">
    <div class="pull-right hidden-xs" v-if="data && data.links && data.links.length">
      <ul>
        <li v-for="link in data.links">
          <router-link :to="link.url">{{ link.label }}</router-link>
        </li>
      </ul>
    </div>
    <span v-html="data.content" v-if="data && data.content"></span>
  </footer>
</template>

<script>
  import { mapGetters } from 'vuex'
  export default {
    name: 'pagefooter',
    computed: {
      ...mapGetters([
        'footer'
      ]),
      data () {
        // @task [VUEJS] validate footer
        return this.footer
      }
    }
  }
</script>