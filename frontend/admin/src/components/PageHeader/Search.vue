<template>
  <form class="navbar-form-custom" role="search" v-if="data && data.username">
    <div class="form-group">
      <input type="text" ref="search" class="form-control navbar-search-input" 
             id="ddms" autocomplete="off"
             :placeholder="placeholder" v-model="value" 
             @keydown.tab.prevent="tab()" 
             @keydown.down.prevent="jumpToList()" 
             @keydown.enter.prevent="go()" 
             @click.stop.prevent="click()" 
             @keyup="change" 
             @blur="reset()" 
             @focus="display()">
      <div id="ddmsh" data-toggle="dropdown"></div>
      <div class="navbar-form-back" v-if="active"><span class="invisible">{{ value }}</span>{{ suggested }}<i class="fa" :class="suggestType" v-if="suggestType"></i></div>
      <ul class="dropdown-menu dm-back" aria-labelledby="ddmsh" id="ddmsm">
        <li v-for="(item, index) in filteredList" 
            :role="typeof item === 'string' ? 'separator' : null" 
            :class="{ divider: typeof item === 'string' }"
            @click="clean()"
            @keydown.enter="clean()"
            >
          <router-link 
              v-if="typeof item === 'object'"
              :id="index === 0 ? 'ddmsl' : null" 
              :tabindex="index === 0 ? 1 : null" 
              :to="typeof item === 'object' && item[1] ? item[1] + '/frsh/' + Math.floor(Math.random() * 10000) : ''">
            <i class="fa" :class="[ 'fa-' + (item[2] ? item[2] : 'file-text-o'), item[3] ? 'text-' + item[3] : null ]"></i>{{ item[0] + (item.hasOwnProperty(4) ? ' (' + item[4] + ')' : '') }}</router-link>
        </li>
      </ul>
    </div>
  </form>
</template>

<script>
  import { mapGetters } from 'vuex'
  import $ from 'jquery'
  import router from '../../router'
  import transliterate from './../../js/transliterate.js'
  export default {
    name: 'search',
    data () {
      return {
        placeholder: 'Rechercher',
        value: '',
        commands: [
          ['nf', '/invoice/edit/type/invoice', 'arrow-right', 'black', 'Nouvelle Facture'],
          ['nc', '/invoice/edit/type/order', 'arrow-right', 'black', 'Nouvelle Commande'],
          ['nd', '/invoice/edit/type/quote', 'arrow-right', 'black', 'Nouveau Devis'],
          ['nl', '/document/letter', 'arrow-right', 'black', 'Nouvelle Lettre'],
          ['np', '/product/list/do/add', 'arrow-right', 'black', 'Nouveau Produit'],
          ['nco', '/recipient/list/do/add', 'arrow-right', 'black', 'Nouveau COntact'],
          ['no', '/recipient/list/do/add', 'arrow-right', 'black', 'Nouveau cOntact'],
          ['nm', '/document/template', 'arrow-right', 'black', 'Nouveau Modèle'],
          ['cf', '/invoice/edit/type/invoice', 'arrow-right', 'black', 'Créer Facture'],
          ['cc', '/invoice/edit/type/order', 'arrow-right', 'black', 'Créer Commande'],
          ['cd', '/invoice/edit/type/quote', 'arrow-right', 'black', 'Créer Devis'],
          ['cl', '/document/letter', 'arrow-right', 'black', 'Créer Lettre'],
          ['cp', '/product/list/do/add', 'arrow-right', 'black', 'Créer Produit'],
          ['cco', '/recipient/list/do/add', 'arrow-right', 'black', 'Créer COntact'],
          ['co', '/recipient/list/do/add', 'arrow-right', 'black', 'Créer cOntact'],
          ['lf', '/invoice/list/type/invoice', 'arrow-right', 'black', 'Liste des Factures'],
          ['lc', '/invoice/list/type/order', 'arrow-right', 'black', 'Liste des Commandes'],
          ['ld', '/invoice/list/type/quote', 'arrow-right', 'black', 'Liste des Devis'],
          ['ll', '/document/index', 'arrow-right', 'black', 'Liste des Lettres'],
          ['lp', '/product/list', 'arrow-right', 'black', 'Liste des Produits'],
          ['lco', '/recipient/list', 'arrow-right', 'black', 'Liste des COntacts'],
          ['lo', '/recipient/list', 'arrow-right', 'black', 'Liste des cOntacts'],
          ['lm', '/document/templates', 'arrow-right', 'black', 'Liste des Modèles'],
          ['opt', '/account/company', 'arrow-right', 'black', 'Options'],
          ['pro', '/account/login', 'arrow-right', 'black', 'Mon profil'],
          ['moi', '/account/login', 'arrow-right', 'black', 'Mon profil'],
          ['par', '/account/features', 'arrow-right', 'black', 'Paramètres'],
          ['nt', '/ticket/add', 'ticket', 'black', 'Ajouter un ticket'],
          ['bug', '/ticket/add', 'ticket', 'black', 'Signaler un bug, ajouter un ticket'],
          ['lt', '/ticket/list', 'ticket', 'black', 'Liste des tickets'],
          ['help', '/info/book', 'book', 'black', 'Documentation'],
          ['doc', '/info/book', 'book', 'black', 'Documentation'],
          ['aide', '/info/book', 'book', 'black', 'Documentation']
        ],
        suggested: '',
        suggestType: '',
        suggestList: [],
        oldList: [],
        oldValue: '',
        suggestFor: ['', '', ''],
        active: false
      }
    },
    methods: {
      tab () {
        // console.log('tab')
        // this.value += this.suggested
        // this.suggested = ''
        // this.suggestType = ''
      },
      click () {
        // console.log('click')
        // this.suggested = ''
      },
      reset () {
        // console.log('reset')
        $.searched = ''
        this.suggested = ''
        this.suggestType = ''
        this.placeholder = 'Rechercher'
        this.active = false
      },
      clean () {
        // console.log('clean')
        this.reset()
        this.value = ''
        this.closeMenu()
      },
      display () {
        // console.log('display')
        this.clean()
        this.active = true
        this.openMenu()
      },
      go () {
        // console.log('go')
        let url = this.suggested ? this.suggestFor[3] : (typeof this.suggestList[0] !== 'undefined' ? this.suggestList[0][1] : null)
        this.clean()
        this.$refs.search.blur()
        if (url) {
          router.push(url + '/frsh/' + Math.floor(Math.random() * 10000))
        }
      },
      change (e) {
        // console.log('change')
        if (this.value === this.oldValue) {
          return
        }
        if (this.value.length > 0) {
          this.oldValue = this.value
          this.suggested = ''
          this.suggestType = ''
          var self = this
          // console.log('change length > 0')
          this.updateSuggestion(this.commands)
          setTimeout(function () {
            // console.log('$.searched', $.searched, 'value', self.value)
            if (self.value && ($.searched !== self.value)) {
              $.searched = self.value
              // console.log('json query')
              let url = '/event/search/query/' + self.value
              $.getJSON(url).done(function (data) {
                // console.log('suggest list update')
                // console.log('data', self.commands.concat(data))
                self.updateSuggestion(self.commands.concat(data))
                self.oldList = self.suggestList
                // console.log('Suggest List après json request', self.suggestList)
                self.openMenu()
              })
            }
            if (self.value && ($.searched === self.value) && ($.searched === self.suggestFor[0])) {
              self.suggested = self.suggestFor[1]
              self.suggestType = self.suggestFor[2]
              self.suggestList = self.oldList
              self.updateSuggestion(self.oldList)
              // console.log('restablished ajax')
              // $('#ddms').dropdown()
              self.openMenu()
            }
          }, 500)
          this.placeholder = ''
        } else {
          this.suggested = ''
          this.suggestType = ''
//          this.suggestList = []
          this.placeholder = 'Rechercher'
          // console.log('length = 0 : reset')
          this.closeMenu()
        }
      },
      updateSuggestion (newList) {
        // console.log('updateSuggestion')
        let suggestList = newList
        let i = 0

        // Valeurs par défaut si on ne trouve pas de complétion dans la liste
        let suggestFor = [ $.searched, '', '', '' ]

        // Cherche un élément qui match avec la valeur recherchée
        let defaultSuggest = 0
        while (i in suggestList) {
          if (!suggestList[i]) {
            i++
            continue
          }
          let filteredItem = transliterate(suggestList[i][0])
          let liIndex = filteredItem.indexOf(transliterate(this.value))

          // Si un élément match, on extrait le contenu de la complétion
          if (liIndex !== -1) {
            let suggested = suggestList[i][0].toLowerCase().substring(liIndex + this.value.length)
            if (suggestList[i].hasOwnProperty(4)) {
              suggested = suggested + ' (' + suggestList[i][4] + ')'
            }
//                    let nextSpace = this.suggested.indexOf(' ', 1)
//                    if (nextSpace !== -1) {
//                      this.suggested = this.suggested.substring(0, nextSpace)
//                    }

            // On enregistre la complétion et l'icone à afficher
            let suggestType = 'fa-' + suggestList[i][2]
            suggestFor = [$.searched, suggested, suggestType, suggestList[i][1]]
            break
          }

          // Si l'élément n'est pas une commande et qu'il n'y à pas de suggestion par défaut,
          // on définit comme suggestion par défaut
          if (!defaultSuggest && !suggestList[i].hasOwnProperty(4)) {
            defaultSuggest = i
          }

          i++
        }

        // Si on ne trouve pas, on prend le premier
        if (!suggestFor[1] && defaultSuggest) {
          suggestFor = [$.searched, ' ► ' + suggestList[defaultSuggest][0], suggestList[defaultSuggest][2], suggestList[defaultSuggest][1]]
        }

        // Mise à jour des valeurs de complétion et de la liste
        this.suggested = suggestFor[1]
        this.suggestType = suggestFor[2]
        this.suggestFor = suggestFor
        this.suggestList = suggestList
      },
      jumpToList () {
        // console.log('jump')
        // $(this).next('ul').focus()
        // $('#ddms').dropdown()
        $('#ddmsl').focus()
//        $('#ddmsm').trigger('click.bs.dropdown')
//      $('#ddmsm').dropdown('toggle')
        this.openMenu()
      },
      openMenu () {
        // console.log('open menu', this.suggestList)
        if ($('#ddmsm').is(':hidden')) {
          $('#ddmsh').dropdown('toggle')
        }
        // $('#ddms').attr('aria-expanded', 'true').parent().addClass('open')
      },
      closeMenu () {
        // console.log('close menu')
        // console.log(!$('#ddmsm').is(':hidden'))
        if (!$('#ddmsm').is(':hidden')) {
          $('#ddmsh').dropdown('toggle')
        }
        // $('#ddms').attr('aria-expanded', 'false').parent().removeClass('open')
      }
    },
    computed: {
      ...mapGetters([
        'headerUser'
      ]),
      data () {
        // Validate here
        return this.headerUser
      },
      filteredList () {
        // console.log('filteredList')
        if (this.suggestList.length) {
          var vm = this
          var filteredItems = this.suggestList.filter(function (e) {
            if (typeof e === 'object') {
              return !e.hasOwnProperty(4) || e[0].toLowerCase().indexOf(vm.value.toLowerCase()) !== -1
            }
            return false
          })
//          if (filteredItems.length) {
//            var filteredItem = filteredItems[0][0].toLowerCase()
//            this.suggested = filteredItem.substring(filteredItem.indexOf(vm.value.toLowerCase()) + vm.value.length)
//            this.suggestType = filteredItems[0][2]
//          } else {
//            this.suggested = ''
//            this.suggestType = ''
//          }
          this.suggestList = filteredItems
          return filteredItems
        }
        return null
      }
    }
  }
</script>

<style>
  .navbar-form-back {
    border: 0;
    box-shadow: none;
    box-sizing: border-box;
    color: #aaa;
    display: block;
    font-family: "Source Sans Pro", "Helvetica Neue", Helvetica, Arial, sans-serif;
    font-size: 18px;
    font-stretch: normal;
    font-style: normal;
    font-variant-caps: normal;
    font-variant-ligatures: normal;
    font-variant-numeric: normal;
    font-weight: normal;
    height: 50px;
    left: 0px;
    letter-spacing: normal;
    line-height: 25.7143px;
    margin: 0px;
    padding: 13px 20px 7px 20px;
    position: absolute;
    right: 0px;
    text-align: start;
    text-indent: 0px;
    text-rendering: auto;
    text-shadow: none;
    text-size-adjust: 100%;
    text-transform: none;
    top: 0px;
    width: auto;
    z-index: -10;
    background: rgba(255, 255, 255, 255);
  }
  .invisible {
    color: #fff;
  }
  .navbar-form-back > i {
      margin-left: 10px;
  }
  .dm-back {
      width: auto;
      left: 0px;
      right: 0px;
      z-index: 10000;
  }
  .dm-back li a {
      color: gray !important;
  }
  #ddmsh {
      display: none;
  }
</style>
