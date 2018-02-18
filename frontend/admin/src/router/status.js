import $ from 'jquery'

export default function (id, st, stk, lb, lt, url, mob) {
  var stElt = '#st' + id
  $(stElt).attr('class', 'label label-default').html('<i class="fa fa-refresh"></i>')
  $.ajax({
    url: url,
    type: 'POST',
    data: { id: id, st: st },
    success: function (d, s) {
      if (d === '1') {
        $(stElt).attr('class', 'label label-' + stk)
                .removeAttr('aria-expanded')
                .removeAttr('data-toggle')
                .removeAttr('aria-haspopup')
                .attr('data-toggle', 'tooltip')
                .attr('title', 'Mise à jour OK')
                .tooltip()
                .html(mob ? '<span class="hidden-xs l100">' + lb + '</span><span class="visible-xs-inline">' + lt + '</span>' : lb)
                .parent('div').removeClass('open').removeClass('dropdown')
        $(stElt).siblings('.dropdown-menu').remove()
        $('#stl' + lt + id).addClass('hidden').parent('li').siblings('li').each(function (i) {
          $(this).children('a').removeClass('hidden')
        })
        $('#sti' + id).attr('class', 'fa fa-refresh text-gray')
      } else {
        $(stElt).attr('class', 'label label-danger').html('<i class="fa fa-warning"></i>')
//        alert("Erreur lors du changement de statut. Le problème est reporté à notre équipe de développement.")
      }
    },
    error: function () {
      $(stElt).attr('class', 'label label-danger').html('<i class="fa fa-warning"></i>')
//      alert("L'opération n'a pas aboutit. Vérifiez votre connexion internet.")
    }
  })
}
