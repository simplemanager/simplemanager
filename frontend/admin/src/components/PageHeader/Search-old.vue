<template>
  <form class="navbar-form-custom" role="search">
    <div class="form-group">
      <input type="text" class="form-control navbar-search-input" 
             id="ddms" data-toggle="dropdown" autocomplete="off"
             :placeholder="placeholder" v-model="value" 
             @keydown.tab.prevent="tab()" 
             @keydown.down.prevent="jumpToList()" 
             @click="click()" 
             @keyup="change" 
             @blur="reset()" 
             @focus="display()">
      <div class="navbar-form-back" v-if="active"><span class="invisible">{{ value }}</span>{{ suggested }}<i class="fa" :class="suggestType" v-if="suggestType"></i></div>
      <ul class="dropdown-menu dm-back" aria-labelledby="ddms" id="ddmsm" v-show="filteredList">
        <li v-for="(item, index) in filteredList" 
            :role="typeof item === 'string' ? 'separator' : null" 
            :class="{ divider: typeof item === 'string' }">
          <router-link :id="index === 0 ? 'ddmsl' : null" :tabindex="index === 0 ? 1 : null" 
              :to="item[1]" v-if="typeof item === 'object'">
            <i class="fa" :class="[ 'fa-' + (item[2] ? item[2] : 'file-text-o'), item[3] ? 'text-' + item[3] : null ]"></i>{{ item[0] }}</router-link>
        </li>
      </ul>
    </div>
  </form>
</template>

<script>
  import $ from 'jquery'
  export default {
    name: 'search',
    data () {
      return {
        placeholder: 'Rechercher',
        value: '',
        oldValue: '',
        suggested: '',
        suggestType: '',
        suggestList: [],
        active: false
      }
    },
    mounted () {
      $('#ddmsm').on('hide.bs.dropdown', function () {
        return false
      })
    },
    methods: {
      tab () {
        this.value += this.suggested
        this.suggested = ' le monde'
        this.suggestType = 'fa-user'
        console.log('tab')
      },
      click () {
        console.log('click')
        this.suggested = ''
      },
      reset () {
        this.suggested = ''
        this.placeholder = 'Rechercher'
        this.active = false
      },
      display () {
        this.active = true
      },
      change (e) {
        if (this.value.length > 0 && this.value.length >= this.oldValue.length) {
          console.log('change')
          if (!this.suggested) {
            this.suggestList = [
              [ 'M. Germain Ponçon', '/account/login', 'user', 'red' ],
              [ 'Foire aux questions', '/info/faq', 'file-text-o', 'blue' ],
              [ 'Sécurité et vie privée', '/info/conditions' ],
              [ 'Rapport mensuel', '/info/faq', 'file-pdf-o', 'orange' ],
              '',
              [ 'Bonjour', '/info/faq', 'bar-chart', 'green' ],
              [ 'Bonjour le monde', '/info/faq', 'bar-chart', 'green' ],
              [ 'Mes statistiques', '/info/faq', 'bar-chart', 'green' ]
            ]
          }
          this.placeholder = ''
        } else {
          this.suggested = ''
          this.suggestType = ''
          this.placeholder = 'Rechercher'
        }
        this.oldValue = this.value
      },
      jumpToList () {
        console.log('jump')
        // $(this).next('ul').focus()
        $('#ddmsl').focus()
      }
    },
    computed: {
      filteredList () {
        if (this.suggestList.length) {
          var vm = this
          var filteredItems = this.suggestList.filter(function (e) {
            if (typeof e === 'object') {
              return e[0].toLowerCase().indexOf(vm.value.toLowerCase()) !== -1
            }
            return false
          })
          if (filteredItems.length) {
            var lowerItem = filteredItems[0][0].toLowerCase()
            this.suggested = lowerItem.substring(lowerItem.indexOf(vm.value.toLowerCase()) + vm.value.length)
            this.suggestType = filteredItems[0][2]
          } else {
            this.suggested = ''
            this.suggestType = ''
          }
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
</style>