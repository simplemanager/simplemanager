<template>
  <div>
    <alert></alert>
    <div class="btn-group" role="group">
      <button type="button" class="btn btn-default" @click='initMenu()'>Initialiser les données du layout</button>
      <button type="button" class="btn btn-default" @click='updateAjax()'>Mise à jour Ajax</button>
      <button type="button" class="btn btn-default" @click='updateMenu()'>Ajouter des éléments au menu</button>
      <button type="button" class="btn btn-default" @click='updateTest()'>Faire des modifications</button>
    </div>
    <p>&nbsp;</p>
    <div id="rprogress" class="text-warning"></div>
    <div id="rdone" class="text-success"></div>
    <div id="rfail" class="text-danger"></div>
  </div>
</template>

<script>
// import $ from 'jquery'
import { mapActions } from 'vuex'
import ajaxCall from './router/ajaxCall.js'
import alert from './components/Bootstrap/Alert.vue'
export default {
  name: 'test',
  components: { alert },
  methods: {
    ...mapActions([
      'write',
      'extend'
    ]),
    initMenu () {
      var component = this
      ajaxCall('/test/layout').done(function (data) {
        component.write(data.w)
      })
    },
    updateAjax () {
      var component = this
      ajaxCall('/test/update').done(function (data) {
        component.extend(data.u)
      })
    },
    updateMenu () {
      this.updateLayout(JSON.parse(`
{
  "sidebar": {
    "menu": {
      "ulabel": "Gestion de mon compte",
      "user": {
        "label": "Mon compte perso",
        "icon": "fa-user",
        "color": "green",
        "params": {
          "controller": "user"
        },
        "items": {
          "overview": {
            "label": "Données privées",
            "color": "green",
            "params": {
              "action": "modify"
            }
          }
        }
      },
      "test": {
        "items": {
          "overview": {
            "label": "Recettage",
            "color": "red",
            "params": {
              "action": "recette"
            }
          }
        }
      }
    }
  }
}`))
    },
    updateTest () {
      this.updateLayout(JSON.parse(`
{
  "sidebar": {
    "registration": {
      "label": "Inscrivez-vous",
      "imgsrc": "",
      "status": "danger",
      "subLabel": "offline"
    }
  },
  "header": {
    "buttons": [{
        "type": "notifications",
        "title": "You have 12 notifications",
        "icon": "bell-o",
        "label": "12",
        "labelStatus": "danger",
        "content": [{
            "link": "#",
            "data": "<i class='fa fa-users text-aqua'></i> 5 new members joined today"
          }],
        "footLinkUrl": "#",
        "footLinkLabel": "View all"
      }
    ],
    "user": {
      "username": "Inscrivez-vous",
      "imgsrc": ""
    }
  },
  "page": {
    "title": "Modifications effectuées",
    "subtitle": "",
    "links": null
  }
}`))
    }
  }
}
</script>

<style>
</style>
