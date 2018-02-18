function clickStar(id, st) {
  var starElt = '#st' + id
  var starVal = '#stv' + id
  $(starElt).attr('class', 'fa fa-refresh')
  $.ajax({
    url: '/ticket/poll',
    type: 'POST',
    data: { id: id, st: st },
    success: function(d, s) {
      $(starElt).removeAttr('data-original-title')
      if (d === '1') {
        $(starElt).attr('class', 'fa fa-star')
        $(starElt).css('color', 'orange')
        $(starElt).attr('onclick', 'clickStar(' + id + ',0)')
        $(starVal).html(parseInt($(starVal).text()) + 1)
      } else if (d === '0') {
        $(starElt).attr('class', 'fa fa-star')
        $(starElt).css('color', 'gray')
        $(starElt).attr('onclick', 'clickStar(' + id + ',1)')
        $(starVal).html(parseInt($(starVal).text()) - 1)
      } else {
        $(starElt).attr('class', 'fa fa-warning')
        $(starElt).css('color', 'red')
        alert("Erreur lors du vote. Le problème est reporté à notre équipe de développement.")
      }
    },
    error: function () {
      $(starElt).attr('class', 'fa fa-warning')
      $(starElt).css('color', 'red')
      alert("Le vote n'a pas aboutit. Vérifiez votre connexion internet.")
    }
  })
}

function dispTick(num) {
  $.router.push('/ticket/detail/id/' + num)
}
