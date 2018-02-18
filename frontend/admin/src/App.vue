<template>
  <div class="app wrapper">
    <pageheader></pageheader>
    <sidebar></sidebar>
    <div class="content-wrapper">
      <pagetitle></pagetitle>
      <section class="content">
        <div id="alerts" v-if="alerts" v-for="alert in checkedAlerts">
          <alert :title="alert.title" 
                 :status="alert.status" 
                 :message="alert.message" 
                 :closed=false
                 :closable="alert.closable"></alert>
        </div>
        <test v-if="displayTest"></test>
        <ajax></ajax>
      </section>
    </div>
    <pagefooter></pagefooter>
    <!-- control></control -->
  </div>
</template>

<script>
import { mapGetters, mapActions } from 'vuex'
import pageheader from './components/PageHeader.vue'
import sidebar from './components/SideBar.vue'
import pagetitle from './components/PageTitle.vue'
import ajax from './components/FetchData.vue'
import pagefooter from './components/PageFooter.vue'
import alert from './components/Bootstrap/Alert.vue'
// import control from './components/ControlSidebar.vue'
import test from './Test.vue'
export default {
  name: 'app',
  components: { pageheader, sidebar, pagetitle, ajax, pagefooter, alert, test },
  data () {
    return {
      displayTest: false
    }
  },
  methods: {
    ...mapActions(['cleanAlerts'])
  },
  computed: {
    ...mapGetters(['alerts']),
    checkedAlerts () {
      let alerts = this.alerts // Récupération des alerts
      if (alerts !== null && typeof alerts === 'object') {
        let cache = {}
        alerts = alerts.filter(function (elem, index, array) {
          let value = cache[elem.message] ? 0 : 1
          cache[elem.message] = 1
          return value
        })
      }
      return alerts // Renvoi pour affichage
    }
  }
}
</script>

<style>
</style>
