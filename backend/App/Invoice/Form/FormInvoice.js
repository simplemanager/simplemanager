// Focus sur le choix du destinataire
//$(document).ready(function() {
//  $('#recipient')[0].selectize.focus();
//});

// Propose l'ajout d'un nouveau produit à chaque ajout de produit
function updateInvoiceForm(eltNum, options) {
  
  // Div conteneur (racine)
  var $root = typeof eltNum !== 'number' ? false : $('#root' + eltNum);
  
  // Ajout depuis le dernier élément généré par ce script si $elt est défini
  var $pd = typeof eltNum !== 'number' ? false : $root.find('[name="pd' + eltNum + '"]');
  
  // Recherche des options de base
  if (typeof options !== 'object') {
    if (typeof $.pdopts === 'object' && $.pdopts.length) {
      options = $.pdopts;
    }
  }
  
  // On propose un nouveau produit si le dernier select produit est plein
  if ($pd === false || $pd.val()) {
    
    // Numéro du dernier select produit
    var num = typeof eltNum === 'number' ? eltNum + 1 : 0;
    
    // Si l'élément n'existe pas, on le crée
    if ($('#root' + num).length === 0) {
        
      // Editer et minimiser FormInvoiceTpl
      var newPdp = '<div id="root[NUM]"><div class="row"><div class="col-xs-12 col-sm-12 col-lg-12"><div class="form-group input-group"><div class="input-group-addon" onclick="toggleSubform([NUM])"><i id="card[NUM]" class="fa fa-chevron-down clickable"></i><i id="caru[NUM]" class="fa fa-chevron-up clickable" style="display: none"></i></div><select class="selectized" name="pd[NUM]" style="display: none"></select><div class="input-group-addon" style="width: 10%; min-width: 50px"><input type="number" name="pq[NUM]" placeholder="Qté" value="" class="form-control" style="border: 0; height: 30px;"></div><div class="input-group-addon" data-toggle="tooltip" data-original-title="" title="Supprimer" style="border-left: 0" onclick="delPr([NUM])"><i id="pdel[NUM]" class="fa fa-times text-gray psuppr clickable"></i></div><input type="hidden" name="ph[NUM]"></div></div></div><div class="row" id="compl[NUM]" style="display: none"><div class="col-xs-12 col-sm-3 col-lg-3"><div class="form-group"><div class="input-group"><label class="input-group-addon" for="price"><i class="fa fa-eur"></i></label><input step="0.01" type="number" required="" placeholder="Prix" name="pp[NUM]" data-toggle="tooltip" data-placement="bottom" data-original-title="" class="form-control" title="Laisser vide pour utiliser le prix par défaut (sauf création de produit). Fixer un prix différent pour ce document ne modifiera pas le prix par défaut de votre produit."></div></div></div><div class="col-xs-12 col-sm-3 col-lg-3" style="display: block;"><div class="form-group"><div class="input-group"><div class="input-group-addon"><i class="fa fa-percent"></i></div><input type="number" placeholder="Remise" name="pr[NUM]" data-toggle="tooltip" class="form-control" data-placement="bottom" data-original-title="" title="Remise exceptionnelle pour ce document uniquement"></div></div></div><div class="col-xs-12 col-sm-6 col-lg-6"><div class="form-group"><textarea id="pdd[NUM]" name="pdd[NUM]" placeholder="Informations complémentaires" data-toggle="tooltip" data-placement="top" class="form-control" data-original-title="" title="Informations à insérer à côté du produit (dans ce document uniquement)" rows="1"></textarea></div></div></div></div>';
      
      // Substitution du [NUM]
      var $newPdp = $(newPdp.replace(/\[NUM\]/g, num));
      var $select = $newPdp.find('select[name="pd' + num + '"]');
      
      // Création de l'élément select
      $select.selectize({
        allowEmptyOption: true,
        valueField: 'id',
        labelField: 'title',
        searchField: 'search_content',
        createOnBlur: true,
        selectOnTab: true,
        options: [],
        placeholder: 'Ajouter un produit',
        create: function (input, callback) {
          sfOpen(num);
          $('[name="ph' + num + '"]').val(input);
          let row = {title: input, uid: -1, id: -1, code: "NEW", price: 0, price_type: ""};
          this.addOption(row);
          let result = callback(row);
//          console.log(input);
          //$('[name="pp' + num + '"]').focus().select();
          return result;
        },
        render: {
          option_create: function(data, escape) {
            return '<div class="create">Ajouter <strong>' + escape(data.input) + '</strong>&hellip;</div>';
          },
          option: function(item,escape){
//            return '<div>' + (item.uid > 0 ? item.uid + '. ' : '') + '<strong>' + escape(item.code) + '</strong> ' + '<span class="pull-right">' + escape(item.price) + ' ' + escape(item.price_type) + '</span>' + '<br /></span>' + escape(item.title) + '</span>' + '</div>';
            return '<div>' + '<strong>' + escape(item.code) + '</strong> ' + '<span class="pull-right">' + escape(item.price) + ' ' + escape(item.price_type) + '</span>' + '<br /></span>' + escape(item.title) + '</span>' + '</div>';
          }
        },
        load: function (query, callback) {
          if(!query.length) { return callback(); }
          $.ajax({
            url: '/event/ac/product/' + encodeURIComponent(query),
            type: 'GET',
            dataType: 'json',
            error: function() { callback(); },
            success: function(res) { callback(res); }
          });
        },
        onChange: function () { 
          let snum = parseInt(this.$input.attr('name').match(/\d+/g), 10);
          updateInvoiceForm(snum);
          if (!$.pdinitialization) {
            let $input = $('input[name="pq' + snum + '"]');
            if ($input.val() === '') {
              $input.val(1);
            }
            $input.focus().select();
          }
        }
      });

      // Ajout des options du premier select dans le select créé
      if (typeof options === 'object' && options.length) {
        var selectize = $select[0].selectize;
        $.each(options, function(index, value){
            selectize.addOption(value);
        });
        $.pdopts = options;
      }
      
      // Mise à jour du niveau de visibilité (boutons plus ou moins de champs)
      $mdBefore = $('textarea#md_before');
      
      // Mise à jour des styles
      $newPdp.find('div.selectize-input').css('border-right', 0);
      
      // Affichage des nouveaux éléments dans le formulaire
      var $pdp = $root !== false ? $root : $mdBefore.parent().parent().parent();
      $pdp.after($newPdp);
      
      // On active l'autosize sur le textarea
      autosize($('textarea[name="pdd' + num + '"]'));
      
      $('.psuppr').removeClass('text-gray').addClass('text-danger');
      $('#pdel' + num).removeClass('text-danger').addClass('text-gray');
      
      // On retourne le nouvel élément pour qu'il soit éventuellement manipulé
      return $newPdp;
    }
  }
  
  // Si on a pas créé un nouvel élément, on retourne l'élément appelant
  return $root;
}

// Ajout de produits (modification, validation)
function hydrateProducts(products) {
  
  // Mode initialisation (désactive les focus, select sur éléments lors du build)
  $.pdinitialization = true;
  
  // Pour chaque produit
  $.each(products, function (k, p) {
    
    // Récupération ou création de l'élément courant
    k !== 0 ? updateInvoiceForm(k - 1) : $('div#root0');
    var $elt = $('div#root' + k)
    let $pd = $elt.find('select[name="pd' + k + '"]');
    
    // Initialisation du selectize (produits)
    if (p.hasOwnProperty('pd')) {
//      console.log(p)
//      console.log($pd)
      let selectize = $pd[0].selectize;
      selectize.addOption(p.option);
      selectize.setValue(p.pd);
    } else {
      p.pd = '';
    }
    
    // Remplissage des champs complémentaires
    var num = k;
    var $row;
    
    // Pour chaque champ...
    $.each(p, function(pk, pv) {
      
      // On ne traite pas si ce n'est pas un champ
      if (['option', 'errors'].indexOf(pk) !== -1) {
        return;
      }
      
      // Affichage des erreurs.
      // On crée le $row afin de ne pas avoir à le recréer plus tard.
      $row = false;
      if (p.hasOwnProperty('errors') && p.errors.hasOwnProperty(pk)) {
//        console.log(p.errors);
//        console.log(pk);
        let err = p.errors[pk].join(' ');
        $row = $elt.find('[name="' + pk + num + '"]');
        let $prow = $row.parents('div.form-group').addClass('has-error');
        if (pk === 'pd') {
          $prow.after('<div style="color: #e74c3c; margin: -10px 0 15px 0">' + err + '</div>');
        } else {
          $prow.append('<span class="help-block">' + err + '</span>');
        }
        sfOpen(k);
      }
      
      // Affichage des messages d'information (insertion en base OK)
      else if (pk === 'pd' && p.hasOwnProperty('info')) {
        $row = $elt.find('[name="' + pk + num + '"]');
        let $prow = $row.parents('div.form-group').addClass('has-success');
        $prow.after('<div style="color: #00a65a; margin: -10px 0 15px 0">' + p.info + '</div>');
      }
      
      // Remplissage de toutes les valeurs qui ne sont pas le product ID (selectize)
      if (pk !== 'pd') {
        if (!$row) {
          $row = $elt.find('[name="' + pk + num + '"]');
        }
        $row.val(pv);
      }
    });
  });
  
  // On réactive le mode normal
  $.pdinitialization = false;
  
  // Mise à jour des textareas
  //console.log($('textarea'));
  
  //$('textarea').trigger('autosize.resize');
  //autosize.update($('textarea'));
}

// Gestion des produits

function toggleSubform(num)
{
  if ($('#card' + num).is(':visible')) {
    sfOpen(num);
    autosize.update($('#pdd' + num));
  } else {
    sfClose(num);
  }
}

function sfOpen(num)
{
  $('#root' + num).css('background', '#eee').css('margin', '0 -10px 15px -10px').css('padding', '15px 10px 0 10px');
  $('#compl' + num).fadeIn(250,function(){
    $('#compl' + num).show();
  });
  $('#card' + num).hide();
  $('#caru' + num).show();
}

function sfClose(num)
{
  $('#compl' + num).hide();
  $('#card' + num).show();
  $('#caru' + num).hide();
  $('#root' + num).css('background', '').css('margin', '0').css('padding', '0');
}

function delPr(num)
{
  // if ($('select[name=pd' + num + ']')[0].selectize.getValue()) {
  if ($('#pdel' + num).hasClass('text-danger')) {
    $('#root' + num).hide();
    $('input[name=ph' + num + ']').val(-1);
  }
}

// Gestion des destinataires

function addRecipientSelectOpt(id, title)
{
  let $select = $('select[name="recipient"]');
  let selectize = $select[0].selectize;
  let option = {id: id, title: title, tel: "(nouveau)"};
  selectize.addOption(option);
  selectize.setValue(id);
}
