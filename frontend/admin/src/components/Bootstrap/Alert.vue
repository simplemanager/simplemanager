<template>
  <transition name="fade">
    <div class="alert" :class="'alert-' + status" role="alert" v-if="(title || message) && !isClosed">
      <button type="button" class="close" aria-label="Close" v-if="closable" @click.stop="close"><span aria-hidden="true">&times;</span></button>
      <h4 v-if="title && message"><i class="icon fa" :class="'fa-' + icons[status]"></i> {{ title }}</h4>
      <strong v-if="title && !message"><i class="icon fa" :class="'fa-' + icons[status]"></i> {{ title }}</strong>
      <span v-if="message"> {{ message }}</span>
    </div>
  </transition>
</template>

<script>
// @task [VUEJS] bug: warning quand on change la propriété closed depuis le composant parent
export default {
  name: 'alert',
  props: {
    status: {
      type: String,
      default: function () { return 'info' },
      validator: function (value) { return ['info', 'success', 'warning', 'danger'].indexOf(value) !== -1 }
    },
    title: {
      type: String,
      default: ''
    },
    message: {
      type: String,
      default: ''
    },
    closed: { // Propriété mutée par le composant ET le sous-composant (warn vuejs), difficile de faire autrement pour l'instant
      type: Boolean,
      default: false
    },
    closable: {
      type: Boolean,
      default: true
    }
  },
  data () {
    return {
      icons: {
        info: 'info-circle',
        success: 'check',
        warning: 'bell-o',
        danger: 'exclamation-triangle'
      }
    }
  },
  methods: {
    close () {
      this.closed = true
    }
  },
  computed: {
    isClosed () {
      return this.closed
    }
  }
}
</script>

<style>
.fade-enter-active, .fade-leave-active {
  transition: opacity .3s
}
.fade-enter, .fade-leave-to /* .fade-leave-active in <2.1.8 */ {
  opacity: 0
}
</style>
