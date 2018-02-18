<template>
  <li class="dropdown notifications-menu">
  <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="true" @click="currentUrl = 'x'">
    <i class="fa fa-bug text-blue"></i>
  </a>
  <ul class="dropdown-menu">
    <li class="header">Edit quick links:</li>
    <li>
      <div class="slimScrollDiv" style="position: relative; overflow: hidden; width: auto; height: 200px;"><ul class="menu" style="overflow: hidden; width: 100%; height: 200px;">
        <li>
          <a href="#" @click.prevent="wopenc(controller)">
            <i class="fa fa-file-text-o text-orange"></i>
            <strong>Controller</strong>: {{ controller }}
            <span v-if="params[1]">/{{ params[1] }}</span>
          </a>
        </li>
        <li>
          <a href="#" @click.prevent="wopenv(controller, view)">
            <i class="fa fa-file-code-o text-green"></i> <strong>View</strong>: {{ view }}.phtml
          </a>
        </li>
        <li>
          <a href="#" @click.prevent="wopenm(controller)">
            <i class="fa fa-navicon text-aqua"></i>
            <strong>Menu</strong> (of component)
          </a>
        </li>
        <li>
          <a href="#" @click.prevent="wopend(controller)">
            <i class="fa fa-star-o text-aqua"></i>
            <strong>Manifest</strong> (app.yml)
          </a>
        </li>
        <li>
          <a href="#" @click.prevent="wopena(controller)">
            <i class="fa fa-unlock-alt text-red"></i>
            <strong>ACL</strong> (access control list)
          </a>
        </li>
      </ul><div class="slimScrollBar" style="background: rgb(0, 0, 0); width: 3px; position: absolute; top: 0px; opacity: 0.4; display: none; border-radius: 7px; z-index: 99; right: 1px; height: 195.122px;"></div><div class="slimScrollRail" style="width: 3px; height: 100%; position: absolute; top: 0px; display: none; border-radius: 7px; background: rgb(51, 51, 51); opacity: 0.2; z-index: 90; right: 1px;"></div></div>
    </li>
  </ul>
</li>
</template>

<script>
import router from '../../router'
export default {
  name: 'debug',
  data () {
    return {
      currentUrl: ''
    }
  },
  watch: {
    currentUrl () {
      this.currentUrl = router.history.current.path
    }
  },
  methods: {
    wopenc (controller) {
      window.open('http://localhost/edit.php?file=/web/apps/simplemanager.fr/backend/App/' + controller.charAt(0).toUpperCase() + controller.slice(1) + '/Controller.php&amp;line=1', 'edit', 'menubar=no, status=no, scrollbars=no, menubar=no')
    },
    wopenv (controller, view) {
      window.open('http://localhost/edit.php?file=/web/apps/simplemanager.fr/backend/App/' + controller.charAt(0).toUpperCase() + controller.slice(1) + '/View/' + view + '.phtml&amp;line=1', 'edit', 'menubar=no, status=no, scrollbars=no, menubar=no')
    },
    wopenm (controller, view) {
      window.open('http://localhost/edit.php?file=/web/apps/simplemanager.fr/backend/App/' + controller.charAt(0).toUpperCase() + controller.slice(1) + '/Config/menu.yml&amp;line=1', 'edit', 'menubar=no, status=no, scrollbars=no, menubar=no')
    },
    wopend (controller, view) {
      window.open('http://localhost/edit.php?file=/web/apps/simplemanager.fr/backend/App/' + controller.charAt(0).toUpperCase() + controller.slice(1) + '/Config/app.yml&amp;line=1', 'edit', 'menubar=no, status=no, scrollbars=no, menubar=no')
    },
    wopena (controller, view) {
      window.open('http://localhost/edit.php?file=/web/apps/simplemanager.fr/backend/App/' + controller.charAt(0).toUpperCase() + controller.slice(1) + '/Config/acl.yml&amp;line=1', 'edit', 'menubar=no, status=no, scrollbars=no, menubar=no')
    }
  },
  computed: {
    params () {
      var data = this.currentUrl.substring(1).split('/')
      return data
    },
    controller () {
      return this.params[0] ? this.params[0] : 'common'
    },
    view () {
      return this.params[1] ? this.params[1] : 'index'
    }
  }
}
</script>

<style>
</style>
