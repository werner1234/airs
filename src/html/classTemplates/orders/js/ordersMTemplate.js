/** functies voor meervoudige **/
var Mform = 'form#meervoudigForm';
var $orderStatusElement = '<input type="hidden" name="orderStatus" class="" id="orderStatus" value="0">';

var $BankDepotCodes = {BankDepotCodes};
var $fonds = {fondsData};
$('.tab-controlles').hide();

$(".rekeningField").hide();
$('#orderregelsHolder').hide();

$('.fixButton').hide();
$(".initialCare").hide();
clientChanged();


function loadUitvoeringen() {
  $('#uitvoeringen').load(encodeURI('orderuitvoeringListV2.php?orderid=' + $('#orderId').val()));
}

$(document).on('change', '#initialFix', function () {
  
  changeInitialFix ();
});

function changeInitialFix () {
  if ($("#initialFix").prop('checked') === true) {
    $('#isFixOrder').val(1);
    $("#fixOrder").prop('checked', true);
  } else {
    $('#isFixOrder').val(0);
    $("#fixOrder").prop('checked', false);
  }
  
  $('#fixOrder').trigger('change');
  
  
  if ($("#careOrder").prop('checked') === true) {
    $("#initialCare").prop('checked', true);
  } else {
    $("#initialCare").prop('checked', false);
  }
  
  if ($('#fixOrder').is(':checked'))
  {
    $('.initialCare').show();
  } else {
    $('.initialCare').hide();
  }
  
  $('.careOrderInput').hide();
}

$(document).on('change', '#initialCare', function () {
  if ($("#initialCare").prop('checked') === true) {
    $("#careOrder").prop('checked', true);
  } else {
    $("#careOrder").prop('checked', false);
  }
});

$('#orderRowSaveBtn').hide();
$('#orderRowDeleteBtn').hide();
$(document).on('change', '#PortefeuilleSelectie', function () {
  $('#orderRowSaveBtn').hide();
});
if ( $('#orderregelId').val() > 0 ) {
  $('#orderRowSaveBtn').show();
  $('#orderRowDeleteBtn').show();
}

/** Wanneer we de order opnieuw gaan inleggen **/
$(document).on('click', '#copyOrder', function () {
  showLoading('Valideren');
});
/** einde wanneer we de order opnieuw inleggen **/

//
function portefeuilleChanged(data) {
  $('#clientExists').hide();
  $('#orderRowSaveBtn').show();
  $('#fixMessage').hide();
  $('#fixMessageCurrent').html('');
  
  
  $.ajax({
    type: 'GET',
    url: 'ordersEditV2.php',
    dataType: 'json',
    // async: false,
    data: {
      type: 'clientInrows',
      portefeuille: data.data.Portefeuilles.Portefeuille,
      orderId: $('#orderid').val(),
    },
    success: function(data, textStatus, jqXHR) {
      if ( data.clientInrows > 0 ) {
        AEConfirm(
          'Opgegeven Portefeuille reeds aanwezig. Wilt u doorgaan met invoeren?',
          'Portefeuille reeds aanwezig',
          function () {
          
          },
          function () {
            $('#PortefeuilleSelectie').val('');
            
            $('#depobankValue').html('');
            $('#profileValue').html('');
            $('#client').val('');
            $('#Risicoklasse').val('');
            $('input[name=portefeuille_id]').val('');
            $('.rekeningField').hide();
            $('#rekening').val(0);
            $('#portefeuille').val('');
            $('#fondsOwnedInfo').html('')
            return false;
          }
        );
      }
    },
    error: function(jqXHR, textStatus, errorThrown) {
      result = jqXHR;
    }
  });
  
  
  // fullRekeningenList
  // if ( $('#OrderuitvoerBewaarder').val() == 1 ) {
  //   if( jQuery.type(fullRekeningenList[$('#rekening').val()]['Rekening_Depotbank']) === "undefined" )
  //   {
  //     if (fullRekeningenList[$('#rekening').val()]['Rekening_Depotbank'].length > 0)
  //     {
  //       $('#DepotbankOld').val($('#depotbank').val());
  //       $('#depotbank').val(fullRekeningenList[$('#rekening').val()]['Rekening_Depotbank']);
  //     }
  //   }
  // }
  
  
  if ( $('#orderdepotbank').val() !== undefined && $('#orderdepotbank').val() !== '' && ($('#OrderuitvoerBewaarder').val() == undefined || $('#OrderuitvoerBewaarder').val() == 0 )) {
    if ( $('#orderdepotbank').val() != $('#Depotbank').val() ) {
      triggerWrongDepotbank();
    } else {
      $('#depotbankMessage').hide();
    }
  }
  
  
  var fonds = $('#fonds').val();
  var portefeuille = $('#portefeuille').val();
  $('#fondsOwnedInfo').html('');
  
  $.ajax({
    type: 'GET',
    url: 'lookups/rekeningAfschriften.php',
    dataType: 'json',
    // async: false,
    data: {
      type: 'FondsAantal',
      portefeuille: portefeuille,
      fondsId: fonds,
    },
    success: function(data, textStatus, jqXHR) {
//      if ( data.aantal >= 0 )
//      {
      $('#fondsOwnedInfo').html('In portefeuille: <strong>(' + data.aantal + ')</strong>');
//      }
    },
    error: function(jqXHR, textStatus, errorThrown) {
      result = jqXHR;
    }
  });
  
  
  bankDepotbankChanged();
}


function triggerWrongDepotbank () {
  $('#depotbankMessageCurrent').html($('#orderdepotbank').val());
  $('#depotbankMessage').show();
  
  $('#PortefeuilleSelectie').val('');
  $("#Depotbank").val($("#DepotbankOld").val());
  $('#depobankValue').html('');
  $('#profileValue').html('');
  $('#client').val('');
  $('#Risicoklasse').val('');
  $('input[name=portefeuille_id]').val('');
  $('.rekeningField').hide();
  $('#rekening').val(0);
  $('#portefeuille').val('');
}


function isinChanged(data) {
  if ( $('input[name=fonds]').val() == '' || $('#PortefeuilleSelectie').val() == '') {
    $('#fondsOwnedInfo').html('')
  }
  
  
  if ( data !== undefined )
  {
    $fonds = data;
  }
  bankDepotbankChanged();
}

var editOrder = function( event ) {
  $('#orderSoortForm').slideUp();
 
  $('#orderregelsHolder').slideDown();
  $('#controllesHolder').hide();
  $('#orderId').val($('#id').val());
  $('#orderid').val($('#id').val());
  //if ($('#orderStatus').val() > 0) {
  //  $('.statusField').show();
  // }
  loadUitvoeringen();
  
  
  if ($('#orderStatus').val() >= 1 && $('#orderregelId').val() == "") {
    $('#orderRegel').hide();
  }
  
  statusChanged($('#orderStatus'));
  $('#transactieSoort').prop("readonly", true);
  // $('#ISINCode').prop("readonly", true);
  // $('#transactieSoort').after($("#transactieSoort option:selected").text());
  $('#transactieSoort').after('<span style="font-size: 1.2em" id="transactieSoortMsg" class="label">' + $("#transactieSoort option:selected").text() + '</span>');
  $('#transactieSoort').hide();
};
var newOrder = function ( event ) {
};


/** hide fix buttons **/
$(document).on('change', '#fixOrder', function () {
  
  changeMfixOrder ();
});


function changeMfixOrder () {
  if ($("#fixOrder").prop('checked') === true) {
    $('.fixButton').slideDown();
    $(".fixNietNodig").hide();
    $(".fixNodig").show();
  //   $('#orderStatus').val(0);
  } else {
    $('.fixButton').slideUp();
    $(".fixNietNodig").show();
    $(".fixNodig").hide();
  }

  if ( $("#rekeningNrTonen").val() == 1 ) {
    $(".rekeningField").show();
  } else {
    $(".rekeningField").hide();
  }

  careOrderChange ();
}




if ( $('#id').val() > 0 ) {
  editOrder();
} else {
  newOrder();
}

if ( $('input[name=id]').val() > 0 || $('input[name=copyFrom]').val() > 0) {
  $('#extraOption').hide();

  /** set fixorder button state **/
  $('#fixOrder').trigger('change');
  if ( $('#canFixOrder').val() == 1 ) {
    $('.initialFix').hide();
    $('.GlobalFixorder').show();

    if ( $('input[name=fixOrder]').val() == 1 || $('#isFixOrder').val() == 1) {
      $('.fixButton').show();
    } else {
      $('.fixButton').hide();
    }
  }
  if ( $('#fixTonen').val() == 0 ) {
    $('.fixOrderInput').hide();
  }

  $('#fixOrder').trigger('change');
} else {
  $('.fixButton').hide();
  $('.fixOrderInput').hide();
}

if ( $('input[name=id]').val() > 0 && $('input[name=orderregelId]').val() > 0 ) {
  $('.tab-extraOpties').show();
  pane = $(".orderTabHolder .ui-tabs-panel:visible").attr('id');
  if ( pane !== 'tab-extraOpties' ) {
    $('.orderTabHolder').tabs({ active: 0 });
  }
  $('.tab-controlles').show();
  
  $('#controllesHolder').show();
  $('#orderSoortForm').hide();
  $('#extraFondsOpties').show();
  
  $('#orderRegelOptionsBlock').show();
} else {
  $('#orderRegelOptionsBlock').hide();
  
  
  $('#transactieType').on('change', function (event) {
  });
  pane = $(".orderTabHolder .ui-tabs-panel");
  
  // var index = $('.orderTabHolder a[href="#tab-statusList"]').parent().index();
  // $(".orderTabHolder").tabs("option", "active", index);
  
  // $(".orderTabHolder").tabs('select', 'tab-statusList');
}


$('#cancelOrder, #cancelOrderRegel').on('click', function (event) {
  var cancelBtnPressed = $(this).attr('id');
  event.preventDefault();
  
  if ( $('#orderregelId').val() > 0  && cancelBtnPressed === 'cancelOrderRegel' )
  {
    var confirmStatus = confirm("Weet u zeker dat u de order regel op positie {orderRegelPositie} wilt verwijderen?");
  }
  else
  {
    var confirmStatus = confirm("Weet u zeker dat u order {orderIdentificatie} wilt annuleren?");
  }
  if (confirmStatus == true) {
    
    if ( $('#orderregelId').val() > 0 && cancelBtnPressed === 'cancelOrderRegel' ) {
      $('#cancelOrderregel').val(1);
      $( Mform ).submit();
    } else {
      $('#cancel').val(1);
      $('#orderStatus').val(6);
      $( Mform ).submit();
    }
  } else {
    return false;
  }
});


//change state based on button
$(document).on('click', '#saveOrderNew, #sendOrderNew', function() {
  $('input[name=action]').val('update');
  $('input[name=redirect]').val('saveNew');
});

$(document).on('click', '#saveOrder, #sendOrder', function() {
  $('input[name=action]').val('update');
  $('input[name=redirect]').val('saveStay');
});



//set status based on button
$('#sendOrder, #sendOrderNew').on('click', function(event) {
  event.preventDefault();
  AEConfirm(
    'Weet u zeker dat u deze order wilt verzenden, <br /> u kunt hierna niet verder met invoeren.',
    'Order verzenden',
    function () {
      $('#verzenden').val(1);
      preSave(event);
    },
    function () {
      event.preventDefault();
      return false;
    }
  );
});


//change state based on button
$('#saveOrderBack').on('click', function() {
  $('input[name=action]').val('update');
  $('input[name=redirect]').val('saveBack');
});

$('#saveOrder, #saveOrderNew, #saveOrderBack').on('click', function(event) {
  $('#verzenden').val(0);
  
  if ($(this).hasClass('pressed')) {
    return;
  }
  
  
  $('.btn-new').prepend('<i class="stopSave fa fa-spinner fa-spin fa-fw"></i>');
  showLoading ('Valideren');
  $('.btn-new').addClass('pressed');
  $('.btn-save').hide();
  
  
  
  preSave(event, $(this));
});


/** on save order save the order and orderline if not empty **/
function preSave (event, element) {
  event.preventDefault();
  
  
  
  if ( $('input[name=id]').val() === '' || $('input[name=id]').val() === 0 || $('input[name=copyFrom]').val() > 0 ) {
    if ($('#verzenden').val() === 1) {
      $('#orderStatus').val(1)
    } else {
      //   $('#orderStatus').val(-1)
    }
  }
  
  
  Mvalidate = false;
  showLoading('Valideren');
  
  $.when(validateMForm()).done(function (a1) {
    $('.btn-new').attr('disabled', 'disabled');
    setTimeout(
      function()
      {
        $('.btn-new').removeAttr("disabled");
        $('.btn-new').removeClass('pressed');
        $('.stopSave').remove();
        $('.btn-save').show();
      }, 1500);
    if (Mvalidate == true) {
      removeLoading();
      /** kijken of we doorgaan met invoeren status = -1 **/
      if ( $('#orderStatus').val() == -1 && ( $(element).attr('id') == 'saveOrderNew' || $(element).attr('id') == 'saveOrderBack' ) )
      {
  
  if ( $(element).attr('id') == 'saveOrderBack') {
        $('input[name=action]').val('update');
        $('input[name=redirect]').val('saveBack');
      }
        
        event.preventDefault();
        $('<div style="padding: 10px; max-width: 500px; word-wrap: break-word;">Wilt u de status doorzetten naar ingevoerd?' +
          '</div>').dialog({
          draggable: false,
          modal: true,
          resizable: false,
          width: 'auto',
          title: 'Status wijzigen',
          minHeight: 75,
          buttons: {
            "Ja": function ()
            {
              $('#orderStatus').val(0);
              preSubmit(event);
              $(this).dialog('destroy');
            },
            "Nee": function ()
            {
              preSubmit(event);
              $(this).dialog('destroy');
            }
          }
        });
        
      } else {
        preSubmit(event);
      }
    } else {
      removeLoading();
    }
  });
  saved = false;
  // }
};

function preSubmit (event) {
  showLoading('Opslaan');
  submitMform().done(redirect); // submit and redirect
  removeLoading();
}

function redirect () {
  if ( $('#orderId').val() > 0 ) {
    if ( $('input[name=redirect]').val() == 'saveNew') {
      parent.frames['content'].location = 'ordersEditV2.php?action=new&returnUrl=ordersList.php?status=ingevoerd';
    } else if ( $('input[name=redirect]').val() == 'saveBack') {
      parent.frames['content'].location = 'ordersListV2.php?resetFilter=1';
    } else if ( $('input[name=redirect]').val() == 'saveStay') {
      parent.frames['content'].location = 'ordersEditV2.php?action=edit&id=' + $('#orderId').val();
    }
  }
}


function validateMForm() {
  
  $(Mform + ' .help-block.with-errors').remove();
  $(Mform).find(':input').removeClass('input_error');
  
  var postData = $(Mform).serializeArray();
  var formURL = $(Mform).attr("action");
  return $.ajax({
    url: formURL + '?validate=true',
    type: "POST",
    dataType: 'json',
    data: postData,
    success: function (data, textStatus, jqXHR) {
      extraOptionError = 0;
      controleStatus = 0;
      pane = $(".orderTabHolder .ui-tabs-panel:visible").attr('id');
      
      if (data.saved == false) {
        $.each(data.error, function (field, fieldData) {
          
          if ( jQuery.inArray(field, ['tijdsLimiet', 'koersLimiet', 'tijdsSoort', 'memo']) !== -1 )
          {
            extraOptionError = 1;
          }
  
          if ( field == 'orderStatus' ) {
            $('#orderregelsHolder').parent().prepend('<div class="formHolder box box12" ><div class="alert alert-error error-orderStatus">Order kan niet worden verzonden.<br />' + fieldData.message + '</div></div>');
            // $(Mform + ' [name=' + field + ']').toggleClass('input_error');
            // $(Mform + ' [name=' + field + ']').parent().append('<span class="help-block with-errors"><br />' + fieldData.message + '</span>');
          } else if ( field != 'controleStatus' ) {
            controleStatus = 1;
            $(Mform + ' [name=' + field + ']').toggleClass('input_error');
            $(Mform + ' [name=' + field + ']').parent().append('<span class="help-block with-errors"><br />' + fieldData.message + '</span>');
          } else {
            $('#checksMessageBox').html('<div class="alert alert-warning">U heeft nog niet alle controles gedaan!</div>');
          }
          $('#orderregels').load(encodeURI('orderregelsListV2.php?listonly=1&action=new&orderid=' + $('#orderId').val() + '&batchId=' + $('#batchId').val()));
          
          
          if ( data.CheckResult !== '' && controleStatus == 0 ) {
            $('#controllesForm').html(data.CheckResult);
            $('#controllesForm').slideDown();
            $('#orderRegelOptionsBlock').slideDown();
            $('.tab-controlles').show();
            
            /** Wanneer er geen fouten in het extra opties veld zitten de controlles tonen **/
            pane = $("#orderRegelOptionsBlock.orderRegelTabHolder .ui-tabs-panel:visible").attr('id');
            if ( extraOptionError == 0 && pane !== 'tab-controlles' )
            {
              $("#orderRegelOptionsBlock.orderRegelTabHolder").tabs('select', 'tab-controlles');
            }
            
            $('.insertOrderButtonGroup').slideUp();
            
            $('#resetControle').val(0);
          }
        });
        
        
        if ( extraOptionError == 1 ) {
          if ( pane !== 'tab-extraOpties' )
          {
            $(".orderTabHolder").tabs('select', 'tab-extraOpties');
          }
        }
        
      } else {
        Mvalidate = true;
      }
    }
  });
}

function submitMform() {
  
  $(Mform + ' .help-block.with-errors').remove();
  $(Mform).find(':input').css('border-color', ''); //reset inputfield style
  
  var postData = $(document).find(Mform).serializeArray();
  var formURL = $(Mform).attr("action");
  
  return $.ajax({
    url: formURL,
    type: 'POST',
    dataType: 'json',
    data: postData,
    success: function (data, textStatus, jqXHR) {
      removeLoading();
      
      if (data.saved == true) {
        
        $('#isFixOrder').val($('#fixOrder').val())
        // $('#fixOrder').prop("readonly", true);
        //
        // $('input[type="checkbox"][readonly]').on("click.readonly", function(event){event.preventDefault();}).css("opacity", "0.5");
        if(data.object.data.fields.orderStatus.form_options)
        {
          var orderStatus=$('#orderStatus').val();
          $('#orderStatus').empty();
          $.each(data.object.data.fields.orderStatus.form_options, function (indexValue, description)
          {
            $('#orderStatus').append($('<option/>', { value: indexValue, text : description }));
          })
          $('#orderStatus').val(orderStatus);
        }
        
        $('#orderActions').show();
        $('#changeOrder').html('Order wijzigen');
        $('#changeOrder').show();
        
        if ($('#orderId').val() === '') {
          $('#orderId').val(data.id);
          $('#orderid').val(data.id);
          
          if ( $('#canFixOrder').val() == 1 ) {
            $('.initialFix').hide();
            $('.initialCare').hide();
            $('.GlobalFixorder').show();
            $('.GlobalCareorder').show();
            
            if ( $('#fixOrder').is(":checked") ) {
              $('.fixButton').show();
            } else {
              $('.fixButton').hide();
            }
          }
        }
        /** hide controller **/
        $('#controllesForm').html('');
        $('#orderRegelOptionsBlock').slideUp();
        $('#controllesHolder').slideUp();
        $('.tab-controlles').hide();
        
        pane = $(".orderTabHolder .ui-tabs-panel:visible").attr('id');
        if ( pane !== 'tab-extraOpties' )
        {
          //$(".orderTabHolder").tabs('select', 'tab-extraOpties');
          var index = $('.orderTabHolder a[href="#tab-extraOpties"]').parent().index();
          
          $(".orderTabHolder").tabs("option", "active", index);
        }
        
        $('#orderregels').load(encodeURI('orderregelsListV2.php?listonly=1&action=new&orderid=' + $('#orderId').val() + '&batchId=' + $('#batchId').val()));
        $('#orderregelsHolder').slideDown();
        
        $('#saveOrderLine').html('Order regel toevoegen');
        $('#saveOrderLine').show();
        $linesaved = true;
        
        $('#fonds-koers-info').hide();
        $('#fonds-info').hide();
        
        $('#orderSoortForm').hide();
        
        /** Velden leegmaken voor nieuwe invoer **/
        $('#orderregelId').val(''); //order regel legen zodat we verder kunnen met invoeren
        
        $('#PortefeuilleSelectie').val('');
        $('#portefeuille').val('');
        $('#aantal').val('');
        $('#depobankValue').html('');
        $('#profileValue').html('');
        $('#client').val('');
        $('#rekening').find('option').remove();
        $('.rekeningField').hide();
        $('#orderWaarde').html();
        
        $('#PortefeuilleSelectie').focus();
        
        $('#orderWaarde').html('');
        
        
        $('#orderdepotbank').val($('#Depotbank').val());
        
        /** verbergen voor meervoudige ivm validatie **/
        $('#transactieSoort').prop("readonly", true);
        $('#ISINCode').prop("readonly", true);
        if ( ! $( '#transactieSoortMsg' ).length )
        {
          $('#transactieSoort').after('<span id="transactieSoortMsg">' + $("#transactieSoort option:selected").text() + '</span>');
        }
        $('#transactieSoort').hide();
        
      } else {
        $.each(data.errors, function (field, fieldData) {
          $('#orderForm [name=' + field + ']').css('border-color', 'red');
          $('#orderForm [name=' + field + ']').parent().append('<span class="help-block with-errors">' + fieldData.message + '</span>');
        });
      }
    }
  });
  
  return $.Deferred().resolve();
}







function buttonStatus() {
  if ($('#orderId').val() != '') {
  
  } else {
    $('#changeOrder').hide();
    $('#orderActions').hide();
    $('#saveOrderLine').hide();
    $('#controllesHolder').hide();
  }
}

buttonStatus();
$('.notEditable').prop('disabled', true);
