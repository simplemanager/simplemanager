<template>
  <section class="content-header" v-if="data && (data.title || (data.links && data.links.length))">
    <h1>{{ data.title }}<small v-if="data.subtitle">{{ data.subtitle }}</small></h1>
    <ol class="breadcrumb hidden-xs hidden-sm" v-if="data.links && data.links.length">
      <li><i class="fa fa-home"></i>&nbsp;&nbsp;<router-link to="/">{{ data.linkstitle ? data.linkstitle : 'Accueil' }}</router-link></li>
      <li v-for="link in page.links"><router-link :to="link.url">{{ link.label }}</router-link></li>
    </ol>
  </section>
</template>

<script>
  import { mapGetters } from 'vuex'
  export default {
    name: 'pagetitle',
    computed: {
      ...mapGetters([
        'page'
      ]),
      data () {
        // Validate here
//        console.log(JSON.stringify(this.page.links))
        return this.page
      }
    }
  }
</script>