import $ from 'jquery'
import store from '../store'
import * as types from '../store/mutation-types'

// Ajax actions:
// w : Write (initialize/replace data)
// u : Update (updates)
// a : Append (append list of items or data containers)
// d : Delete (delete items)

function ScriptContainer () {}

export default function (urlOrForm, target, replace, waitTarget) {
  // Traitement des paramètres
  var isForm = typeof urlOrForm === 'object'
  let requestType = isForm && urlOrForm.attr('method') ? urlOrForm.attr('method') : 'GET'
  let url = isForm ? urlOrForm.attr('action') : urlOrForm
  let data = isForm ? urlOrForm.serialize() : null
  let baseUrl = process.env.NODE_ENV === 'production' ? '' : '/api'

  // Conteneur des données générées
  var contentTarget = typeof target === 'object' ? target : $(target || '#content')

//  console.log('urlOrForm', urlOrForm)
//  console.log('url', url)
//  console.log('requestType', requestType)
//  console.log('isForm', isForm)
//  console.log('data', data)
//  console.log('target', target)

  // Lancement de la requête AJAX, récupération des données du serveur
  let promise = $.ajax({
    type: requestType,
    url: baseUrl + url,
    cache: true,
    data: data
//    dataType: 'json'
  })

  var loaded = false
  if (waitTarget) {
    waitTarget = waitTarget === true ? contentTarget : (typeof waitTarget === 'object' ? waitTarget : $(waitTarget || '#content'))
    setTimeout(function () {
      if (loaded === false) {
        waitTarget.html('<div class="loadingbox"><i class="fa fa-hourglass-1"></i><br />Veuillez patienter</div>')
        setTimeout(function () {
          if (loaded === false) {
            waitTarget.children('div').children('i').attr('class', 'fa fa-hourglass-2')
            setTimeout(function () {
              if (loaded === false) {
                waitTarget.children('div').children('i').attr('class', 'fa fa-hourglass-3')
                setTimeout(function () {
                  if (loaded === false) {
                    waitTarget.children('div').html('<i class="fa fa-warning"></i><br />Le réseau est lent, vérifiez votre connexion.')
                  }
                }, 5000)
              }
            }, 2500)
          }
        }, 1000)
      }
    }, 500)
  }

  // Traitement de la promesse une fois le résultat de la requête disponible
  promise.done(function (data, status, request) {
//    console.log('data', data)
//    console.log('status', status)
//    console.log('target', target)
//    console.log('contentTarget', contentTarget)
//    console.log('request', request.getResponseHeader('content-type'))

    loaded = true

    // Aucune donnée
    if (!data) { return }

    // Données de type HTML : afficher tel quel
    if (request.getResponseHeader('content-type').indexOf('text/html') === 0) {
      if (replace) {
        contentTarget.replaceWith(data)
      } else {
        contentTarget.html(data)
      }
      return
    }

    // Données qui ne sont pas JSON : ecrire les données dans le document
    if (request.getResponseHeader('content-type').indexOf('application/json') !== 0) {
      document.write(data)
      return
    }

    // Données JSON : mutation du store VueX
    if (data.w) {
      store.commit(types.WRITE, data.w)
    } else if (data.u) {
      store.commit(types.EXTEND, data.u)
    }
    let pageData = data.w ? data.w : data.u

    // On court-circuite VueJS pour les données principales de la requête,
    // pour des raisons de performances. C'est Jquery qui gère les données
    // AJAX via le DOM réel, tandis que le layout est géré par VueJS avec son
    // DOM virtuel.
    if (pageData.page && pageData.page.content !== null) {
      // Ecriture des données de la page (remplacement ou nom de la balise cible)
      if (replace) {
        contentTarget.replaceWith(pageData.page.content)
      } else {
        contentTarget.html(pageData.page.content)
      }

      // S'il y a une balise script avec un attribut SRC, charger le fichier JS
      $('#content').find('script').each(function () {
        if ($(this).attr('src')) {
          var options = {
            dataType: 'script',
            cache: true,
            url: $(this).attr('src')
          }
          $.ajax(options)
        }
      })
    }

    // Traitement des scripts JS embarqués : ajout dans le conteneur de scripts
    // ceux de la page. Ce conteneur stock les scripts exécutables à tout
    // moment via des déclancheurs (clic sur les liens, post de formulaires...)
    if (pageData.page && 'scripts' in pageData.page) {
//      console.log('set', pageData.page.scripts)
      ScriptContainer.scripts = pageData.page.scripts
    }

    // Pour chaque appel ajax, on exécute les scripts du conteneurs
    if (ScriptContainer.scripts) {
//      console.log('eval', ScriptContainer.scripts)
      $.globalEval(ScriptContainer.scripts)
    }

    // Données à ajouter au contenu courant : store + conteneur ajax principal
    if (data.a) {
      store.extend(data.a)
      if (data.a.page && data.a.page.content) { $('#content').html($('#content').html() + data.a.page.content) }
    }

    // Données à supprimer : store (deprecated) + conteneur ajax principal
    if (data.d) {
      store.destroy(data.d)
      if (data.d.page && data.d.page.content) { $('#content').text('') }
    }
  })

  // En cas d'erreur au niveau de la requête ajax, on mute le store avec une
  // erreur à afficher.
  promise.fail(function (e) {
    store.error = 'Une erreur est survenue, veuillez vérifier votre connexion à internet.'
    $('#content').html(e.responseText)
  })

  // On retourne la promesse que l'on peut ainsi personnaliser
  return promise
}

//  promise.progress(function () { $('#rprogress').html($('#result').html() + '.') })
