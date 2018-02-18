<template>
  <aside class="main-sidebar">
    <section class="sidebar">
      <registration></registration>
      <sbmenu></sbmenu>
    </section>
  </aside>
</template>

<script>
  import registration from './SideBar/Registration.vue'
  import sbmenu from './SideBar/Menu.vue'
  export default {
    name: 'sidebar',
    components: { registration, sbmenu }
  }
</script>
