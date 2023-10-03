/** functies voor combinatie order **/
var Cform = '#combinatieForm';
var $orderStatusElement = '<input type="hidden" name="orderStatus" class="" id="orderStatus" value="0">';


$(document).on('click', '.switchFix', function () {
  
  $orderId = $(this).data('orderid');
  $msg = 'Weet u zeker dat u fix wil uitzetten voor deze order?';
  $newFixStatus = 0;
  if ( $(this).data('fix') == 0 ) {
    $newFixStatus = 1;
  }
  
  if ( $newFixStatus == 1 ) {
    $msg = 'Weet u zeker dat u fix wil aanzetten voor deze order?';
  }
  
  AECustomMessage(
    $msg,
    'Fix status',
    {
      "Ja": function ()
      {
        $.ajax({
          url: 'ordersEditV2.php?updateFix=1',
          type: "POST",
          dataType: 'json',
          data : {
            orderId: $orderId,
            fixStatus: $newFixStatus
          },
          success: function (data, textStatus, jqXHR) {
            location.reload();
          }
        });
        
        $(this).dialog('destroy');
      },
      "Nee": function ()
      {
        $(this).dialog('destroy');
      }
    }
  );
  
  
 
  
  
});

$(document).on('click', '#copyOrder', function (event) {
  event.preventDefault();
  var $href = $(this).attr("href");
  var $status = $('#orderStatus').val();

  if ( $status == 6 || $status == 7 )
  {

    if ($status == 6) {
      $statusName = 'geannuleerde';
    } else if ($status == 7) {
      $statusName = 'geweigerde';
    }

    AECustomMessage(
      'Wilt u alle ' + $statusName + ' orders opnieuw inleggen of alleen deze?',
      'Order verzenden',
      {
        "Deze": function ()
        {
          $href = $href + '&copyBatch=0';

          window.location.href = $href;
          $(this).dialog('destroy');
        },
        "Alle": function ()
        {
          $href = $href + '&copyBatch=' + $('#batchId').val() + '&withStatus=' + $status;
          window.location.href = $href;
          $(this).dialog('destroy');
        },
        "Annuleren": function ()
        {
          $(this).dialog('destroy');
        }
      }
    );
  }
  $href = $href + '&copyBatch=' + $('#batchId').val();
  // window.location.href = $href;
  // $("a.directions-link").attr("href", _href + '&saddr=50.1234567,-50.03452');
});


$('.fixButton').hide();
var $BankDepotCodes = {BankDepotCodes};
var $fonds = {fondsData};


if ( ( typeof $(document).find('#totalBatchOrders').val() === 'undefined' || $(document).find('#totalBatchOrders').val() == 0 ) ) {
  $('#saveSendButtons').hide();
}

function portefeuilleChanged(clientData)
{
  $('.defaultValues').hide();
  if (clientData.data.fixDepotbankenPerVermogensbeheerder.depotbank != null && clientData.data.fixDepotbankenPerVermogensbeheerder.depotbank != "")
  {
    $('.isFix').show();
  } else
  {
    $('.isFix').hide();
  }

  if ($('.isFix').is(':visible') || $('.isFix').is(':visible'))
  {
    $('.defaultValues').show();
  }
  bankDepotbankChanged();
}

function isinChanged(data) {
  if ( data !== undefined )
  {
    $fonds = data;
  }
  bankDepotbankChanged();
}


$('#fixOrder').on('change', function () {

  if ($("#fixOrder").prop('checked') === true) {
    $('.fixButton').slideDown();
    $(".fixNietNodig").hide();
    $(".fixNodig").show();

    var options = $('#orderStatus option');
    var values = $.map(options ,function(option) {
      return option.value;
    });
    if ( values.length > 0 ) {
      $('#orderStatus').val(values[0]);
    }
    // $('#orderStatus').val(0);
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

  if ( $('#id').val() == "" ) {
    $('.statusField').hide();
  }

  careOrderChange();
});

function setFixFields()
{

  if ($("#fixOrder").prop('checked') === true)
  {
   $('.fixButton').slideDown();
    $(".fixNietNodig").hide();
    $(".fixNodig").show();
   // $('#orderStatus').val(0);
    
    $('#fondsOmschrijving').prop('readonly', true);
    $('#fondsOmschrijving').addClass('readOnlyField');
  } else
  {
    $(".fixNietNodig").show();
    $(".fixNodig").hide();

    $('#fondsOmschrijving').prop('readonly', false);
    $('#fondsOmschrijving').removeClass('readOnlyField');
  }

  if ($('#id').val() == "")
  {
    $('.statusField').hide();
  }

  if ($('#fixTonen').val() == 0)
  {
    $('.fixOrderInput').hide();
  } else
  {
    $('.fixOrderInput').show();
  }

  $(".rekeningField").hide();
  if ($("#rekeningNrTonen").val() == 1)
  {
    $(".rekeningField").show();
  }

  if ( $('#orderStatus').val() >= 2 ) {
    $('.orderStatusField').show();
  }
}


var editOrder = function (event)
{
  $('#initialValuesSet').val(1);
  $('#orderSoortForm').slideUp();
  $('#saveSendButtons').show();
  $('#PortefeuilleSelectie').attr('readonly', true);
  $('#PortefeuilleSelectie').addClass('readOnlyField');

  $('#portefeuille').addClass('readOnlyField');

  $('#tempRekening').readonly();
  $('#rekening').val($('#tempRekening').val());

  $('#ISINCode').focus();

  setFixFields();

  $('.order1form').slideUp();

  $('#controllesHolder').show();
  $('.insertOrderButtonGroup').hide();
  $('#controlActions').hide();
  
};

var newOrder = function (event)
{
  $('.orderSubForm').slideUp();

  // $('#controllesHolder').hide();
  // $('#extraFondsOpties').hide();
  // $('#uitvoeringenList').hide();
  $('#orderOptionsBlock').hide();
  $('.tab-controlles').hide();

  // $('#saveSendButtons').hide();
  $('.fixButton').hide();
  $('#continueOrder').hide();
  $('#sendOrder').hide();
  $('#sendOrderNew').hide();
};

var showOrder = function (event)
{

  if ($('#PortefeuilleSelectie').val() != '')
  {
    if (  typeof $(document).find('#totalBatchOrders').val() !== 'undefined' || $(document).find('#totalBatchOrders').val() > 0 )
    {
      $('#saveSendButtons').show();
    }

    
    $('#initialValuesSet').val(1);
    
    $('#orderPreMessages').html('');
    $('#orderSoortForm').slideUp();
    $('.order1form').slideUp();
    $('.orderSubForm').slideDown();

    $('#orderOptionsBlock').slideDown();


    $('#PortefeuilleSelectie').attr('readonly', true);
    $('#PortefeuilleSelectie').addClass('readOnlyField');

    $('#portefeuille').addClass('readOnlyField');

    $('#tempRekening').readonly();
    
    $("#tempFixOrder").attr('onclick', 'return false');
    $("#tempFixOrder").attr('onkeydown', 'return false');

    if ($("#tempFixOrder").is(':checked'))
    {
      $("#fixOrder").prop('checked', true);
    } else
    {
      $("#fixOrder").prop('checked', false);
    }
    

    $('#rekening').val($('#tempRekening').val());
    $('#rekening').change();
    $("#fixOrder").change();
    $('#ISINCode').focus();

    pane = $(".orderTabHolder .ui-tabs-panel:visible").attr('id');
    if ( pane !== 'tab-extraOpties' ) {
      $('.orderTabHolder').tabs({ active: 0 });
    }

   setFixFields();
   careOrderChange ();
    changeRekening();
  } else
  {
    $('#orderPreMessages').html('<div class="alert alert-error">U heeft nog geen client geselecteerd!</div>');
  }
};


if ($('#id').val() > 0)
{
  editOrder();
} else
{
  newOrder();
}
$('.notEditable').prop('disabled', true);

$('.defaultSelection').hide();


// $('#generateOrder').on('click', function (event)
// {
//   $('.orderSubForm').slideUp();
//   $('#orderRegelsHolder').removeClass('box6');
//   $('#orderRegelsHolder').addClass('box12');
//   $('#saveSendButtons').slideDown();
//   $('.fixButton').slideDown();
// });
//   $('#saveSendButtons').slideDown();
//   $('.fixButton').slideDown();

$(':checkbox[readonly=readonly]').click(function ()
{
  return false;
});


$('#showOrder').on('click', showOrder);


if ($('#id').val() === '')
{
  $('#orderregelsHolder').hide();
}

/*
wordt bij combinatie order aangeropen wanneer er nog geen records zijn vastgelegd.
$('#orderregels').load(encodeURI('orderregelsListV2.php?orderid=' + $('input[name=id]').val() + '&orderegelId=' + $('input[name=orderregelId]').val() + '&batchId=' + $('input[name=batchId]').val()), function ()
{
  if ($('#totalFixOrders').val() > 0)
  {
    $('#sendOrder').show();
    $('#sendOrderNew').show();
    $('.fixButton').slideDown();
  }
});
*/


  $('#transactieType').on('change', function (event) 
  {
    pane = $(".orderTabHolder .ui-tabs-panel:visible").attr('id');
    if ( pane !== 'tab-extraOpties' ) 
    {
      $('.orderTabHolder').tabs({ active: 0 });
      
    }
  });


//change state based on button
$('#saveOrderNew, #sendOrderNew').on('click', function ()
{
  $('input[name=action]').val('update');
  $('input[name=redirect]').val('saveNew');
});
//change state based on button
$('#saveOrderBack').on('click', function() {
  $('input[name=action]').val('update');
  $('input[name=redirect]').val('saveBack');
});
$('#saveOrder, #sendOrder').on('click', function ()
{
  $('input[name=action]').val('update');
  $('input[name=redirect]').val('saveStay');
});

//set status based on button
$('#sendOrder, #sendOrderNew').on('click', function (event) {
  event.preventDefault();
  
  /** show stukkenlijst **/
  $('#orderregels').load(encodeURI('orderregelsListV2.php?orderid=' + $('input[name=id]').val() + '&orderegelId=' + $('input[name=orderregelId]').val() + '&batchId=' + $('input[name=batchId]').val()), function ()
  {
    if (  typeof $(document).find('#totalBatchOrders').val() !== 'undefined' || $(document).find('#totalBatchOrders').val() > 0 )
    {
      $('#saveSendButtons').show();
    }
    
    if ($('#totalFixOrders').val() > 0)
    {
      $('.fixButton').show();
      $('#sendOrder').show();
      $('#sendOrderNew').show();
    } else
    {
      $('#sendOrder').hide();
      $('#sendOrderNew').hide();
      $('.fixButton').hide();
    }
  
  // wanneer er validatie fouten in de combinatie orders zitten
  if ( $(document).find('#orderValidatieFouten').val() > 0 ) {
    AEMessage("Order kan niet worden verstuurd. In deze combinatie zijn een of meerdere controles niet voltooid!", "Order Controles", function () {
      $('#verzenden').val(0);
    });
  } else if ( $(document).find('#notsendFix').val() > 0 ) {
    AEMessage("Order kan niet worden verstuurd. Maak eerst e-mail voor deze Adviesrelatie, de order wordt opgeslagen!", "Advies relatie", function () {
      $('#verzenden').val(0);
      $(Cform).submit();
    });
  } else {
    
    $orderVierOgenCheck = $('#orderVierOgenCheck').val();
    
    $aantalInAanmaak = $(document).find("[data-statusid='-1']").val();
    // $orderVierOgenCheck == 1 && @info: wanneer we dit aan 4 ogen willen hangen.
    if ( $aantalInAanmaak > 0 ) {
      AEMessage("Order kan niet worden verstuurd. Er zijn nog " + $aantalInAanmaak + " order(s) met de status in aanmaak", "Verzenden", function () {
      
      });
    } else {
    
      
      $ordersLocked = $('#orderLock').val();
      $orderLockedTxt = '';
      if ( $ordersLocked > 0 ) {
        $orderLockedTxt = '<br /> Let op: er is (zijn) ' + $ordersLocked + ' order(s) geopend.<br />';
      }
      
      AEConfirm(
        'Weet u zeker dat u ' + $('#totalFixOrders').val() + ' (van ' + $('#totalBatchOrders').val() + ') orders wilt verzenden. <br />' +
        $orderLockedTxt +
        '<br />' + $('#orderStatustxt').html(),
        'Orders verzenden',
        function () { //Ja
          $('#verzenden').val(1);
          $('#sendToFix').val(1);
          $(Cform).submit();
        },
        function () { //Nee
          return false;
        }
      );
    
    }
  }
});
});

$('#saveOrder, #saveOrderNew, #insertOrder, #saveOrderBack').on('click', function (event)
{
  buttonId = $(this).attr('id');
  /** controlleren of we terug gaan of de order willen opslaan  **/
  var saveThis = 0;
  if (  $('#id').val() > 0 ||
        $(this).attr('id') == 'insertOrder' ||
      ( $('input[name=portefeuille_id]').val() != ''  && ( $('input[name=fonds_id]').val() != '' || $('input[name=fondsOmschrijving]').val() != '' ) ) ||
      ( $('input[name=portefeuille_id]').val() == '' ) ||
      ( $('input[name=portefeuille_id]').val() != ''  && ( typeof $(document).find('#totalBatchOrders').val() === 'undefined' || $(document).find('#totalBatchOrders').val() == 0 )))
  {
    var saveThis = 1;
  }

// return ;

  if ( saveThis === 1)
  {
    $('#verzenden').val(0);
    submitForm(event, $(this));
  } else {
    /** check of er orders zijn met status -1 **/
    var formURL = $(Cform).attr("action");

    $.ajax({
      url: formURL + '?checkForStatusInAanmaak=true&batchId=' + $('#batchId').val() + '',
      type: "POST",
      dataType: 'json',
      success: function (data, textStatus, jqXHR) {
        preStatus = data.hasStatus;

        if ( preStatus > 0 && ( buttonId == 'saveOrderNew' || buttonId == 'saveOrderBack' ) ) {
          removeLoading();

          var dialogText = 'Wilt u de status van alle orders binnen deze batch doorzetten naar ingevoerd?';

          $('<div style="padding: 10px; max-width: 500px; word-wrap: break-word;">' + dialogText + '</div>').dialog({
            draggable: false,
            modal: true,
            resizable: false,
            width: 'auto',
            title: 'Status wijzigen',
            minHeight: 75,
            buttons: {
              "Ja": function ()
              {
                $.ajax({
                  url: formURL + '?klaarMetInAanmaak=combinationBatch&batchId=' + $('#batchId').val() + '',
                  type: "POST",
                  dataType: 'json',
                  success: function (data, textStatus, jqXHR) {

                    if ( $('input[name=redirect]').val() == 'saveNew') {
                      parent.frames['content'].location = 'ordersEditV2.php?action=new&returnUrl=ordersList.php?status=ingevoerd';
                    } else if ( $('input[name=redirect]').val() == 'saveBack') {
                      parent.frames['content'].location = 'ordersListV2.php?resetFilter=1';
                    } else if ( $('input[name=redirect]').val() == 'saveStay') {
                      showLoading('Opslaan');
                      setTimeout( function() {removeLoading();}, 1000);
                      // parent.frames['content'].location = $('input[name=return]').val();
                    }
                  }
                });
              },
              "Nee": function ()
              {
                if ( $('input[name=redirect]').val() == 'saveNew') {
                  parent.frames['content'].location = 'ordersEditV2.php?action=new&returnUrl=ordersList.php?status=ingevoerd';
                } else if ( $('input[name=redirect]').val() == 'saveBack') {
                  parent.frames['content'].location = 'ordersListV2.php?resetFilter=1';
                } else if ( $('input[name=redirect]').val() == 'saveStay') {
                  // parent.frames['content'].location = $('input[name=return]').val();
                  showLoading('Opslaan');
                  setTimeout( function() {removeLoading();}, 1000);
                }
              }
            }
          });
        } else {
          if ( $('input[name=redirect]').val() == 'saveNew') {
            parent.frames['content'].location = 'ordersEditV2.php?action=new&returnUrl=ordersList.php?status=ingevoerd';
          } else if ( $('input[name=redirect]').val() == 'saveBack') {
            parent.frames['content'].location = 'ordersListV2.php?resetFilter=1';
          } else if ( $('input[name=redirect]').val() == 'saveStay') {
            // parent.frames['content'].location = $('input[name=return]').val();
            showLoading('Opslaan');
            setTimeout( function() {removeLoading();}, 1000);
          }
        }
      }
    });

  }

});


function submitForm(event, element)
{
  $('.btn-new').attr('disabled', 'disabled');
  $('.btn-new').prepend('<i class="stopSave fa fa-spinner fa-spin fa-fw"></i>');
  showLoading ('Valideren');
  setTimeout(
    function()
    {
      $('.btn-new').removeAttr("disabled");
      $('.stopSave').remove();
    }, 1500);

  event.preventDefault();
  Cvalidate = false;



  $.when(validateCForm()).done(function (a1)
  {
    if (Cvalidate == true)
    {
      var preStatus = 0;

      if ($('input[name=id]').val() === '' || $('input[name=id]').val() === 0)
      {
        if ($('#verzenden').val() == 1)
        {
          $('#orderStatus').val(1)
        }
      }


      if ( $('#orderStatus').val() == -1 && ( $(element).attr('id') == 'saveOrderNew' || $(element).attr('id') == 'saveOrderBack' ) ) {
        removeLoading();

        /** check of er orders zijn met status -1 **/
        var formURL = $(Cform).attr("action");

        $.ajax({
          url: formURL + '?checkForStatusInAanmaak=true&batchId=' + $('#batchId').val() + '',
          type: "POST",
          dataType: 'json',
          success: function (data, textStatus, jqXHR) {
            preStatus = data.hasStatus;

            var dialogText = '';
            if ( preStatus == 1 ) {
              var dialogText = 'Wilt u de status doorzetten naar ingevoerd?';
            } else if ( preStatus > 1 ) {
              var dialogText = 'Wilt u de status van alle orders binnen deze batch doorzetten naar ingevoerd?';
            }

            $('<div style="padding: 10px; max-width: 500px; word-wrap: break-word;">' + dialogText + '</div>').dialog({
              draggable: false,
              modal: true,
              resizable: false,
              width: 'auto',
              title: 'Status wijzigen',
              minHeight: 75,
              buttons: {
                "Ja": function () {
                  $('#orderStatus').val(0);
                  showLoading('Opslaan');
                  submitCform(true);
                  $( this ).dialog( "close" );
                },
                "Nee": function () {
                  showLoading('Opslaan');
                  submitCform(false);
                  $( this ).dialog( "close" );
                }
              }
            });
          }
        });
      } else {
        showLoading('Opslaan');
        submitCform(false);
      }
    } else {
      removeLoading();
    }
  });
}

function validateCForm()
{
  $('#checksMessageBox').html('');
  $(Cform + ' .help-block.with-errors').remove();
  $(Cform).find(':input').removeClass('input_error');

  var postData = $(Cform).serializeArray();
  var formURL = $(Cform).attr("action");
  return $.ajax({
    url: formURL + '?validate=true',
    type: "POST",
    dataType: 'json',
    data: postData,
    success: function (data, textStatus, jqXHR)
    {
      extraOptionError = 0;
      controleStatus = 0;

      //Bepalen welke tab actief is
      var pane = $(".orderTabHolder .ui-tabs-panel:visible").attr('id');

      if (data.saved == false)
      {
        $.each(data.error, function (field, fieldData)
        {
          if ( jQuery.inArray(field, ['tijdsLimiet', 'koersLimiet', 'tijdsSoort', 'memo']) !== -1 )
          {
            extraOptionError = 1;
          }

          if (field != 'controleStatus')
          {
            controleStatus = 1;
            $(Cform + ' [name=' + field + ']').toggleClass('input_error');
            $(Cform + ' [name=' + field + ']').parent().append('<span class="help-block with-errors"><br />' + fieldData.message + '</span>');
          }
          else
          {
            $('#checksMessageBox').html('<div class="alert alert-warning">U heeft nog niet alle controles gedaan!</div>');
          }
        });

        if ( extraOptionError == 1 ) {
          if ( pane !== 'tab-extraOpties' )
          {
            $('.orderTabHolder').tabs({ active: 0 });
          }
        }

        if ( data.CheckResult !== '' && controleStatus == 0 ) {
          $('#controllesForm').html(data.CheckResult);
          // $('#controllesHolder').slideDown();
          $('.tab-controlles').show();
          $('#tab-controlles').show();

          /** Wanneer er geen fouten in het extra opties veld zitten de controlles tonen **/
          if ( extraOptionError == 0 && pane !== 'tab-controlles' )
          {
            $('.orderTabHolder').tabs({ active: $('.orderTabHolder ul').index($('#tab-controlles')) });
          }

          $('.insertOrderButtonGroup').slideUp();
          $('#controlActions').show();
  
          $('#resetControle').val(0);
        }




      } else
      {
        Cvalidate = true;
      }
    }
  });
}


function submitCform(updateBatchStatus)
{
  $('.fixButton').hide();

  $(Cform + ' .help-block.with-errors').remove();
  $(Cform).find(':input').css('border-color', ''); //reset inputfield style
  var postData = $(Cform).serializeArray();
  var formURL = $(Cform).attr("action");
  if ( updateBatchStatus == true ) {
    formURL = formURL  + '?klaarMetInAanmaak=true';
  }
  return $.ajax({
    url: formURL,
    type: "POST",
    dataType: 'json',
    data: postData,
    success: function (data, textStatus, jqXHR)
    {
      removeLoading();
      $('#BankDepotCodes').html('');
      if (data.saved == true)
      {
        if ($('#id').val() > 0)
        {
          if ( $('input[name=redirect]').val() == 'saveNew') {
            parent.frames['content'].location = 'ordersEditV2.php?action=new&returnUrl=ordersList.php';
          } else if ( $('input[name=redirect]').val() == 'saveBack') {
            parent.frames['content'].location = 'ordersListV2.php?resetFilter=1';
          }
          else if ( $('input[name=redirect]').val() == 'saveStay' && $('#orderStatus').val() >= 0) {
            parent.frames['content'].location = 'ordersEditV2.php?action=edit&id=' + data.id;
          }
        }

        /** hide controller **/
        $('#controllesForm').html('');
        $('#tab-controlles').hide();
        $('.tab-controlles').hide();

        pane = $(".orderTabHolder .ui-tabs-panel:visible").attr('id');
        if ( pane !== 'tab-extraOpties' ) {
          $('.orderTabHolder').tabs({ active: 0 });
          // $('a[href="#tab-extraOpties"]').get(0).click();
        }
        /** show stukkenlijst **/
        $('#orderregels').load(encodeURI('orderregelsListV2.php?orderid=' + data.id + '&batchId=' + $('#batchId').val() + '&orderregelId=' + $('#orderregelId').val()), function ()
        {
          if (  typeof $(document).find('#totalBatchOrders').val() !== 'undefined' || $(document).find('#totalBatchOrders').val() > 0 )
          {
            $('#saveSendButtons').show();
          }

          if ($('#totalFixOrders').val() > 0)
          {
            $('.fixButton').show();
            $('#sendOrder').show();
            $('#sendOrderNew').show();
          } else
          {
            $('#sendOrder').hide();
            $('#sendOrderNew').hide();
            $('.fixButton').hide();
          }
        });

        //$('#orderregels').load(encodeURI('orderregelsListV2.php?orderid=' + data.id + '&batchId=' + $('#batchId').val() + '&orderregelId=' + $('#orderregelId').val()));
        $('#orderregelsHolder').slideDown();
//                //data: return data from server
        $('#ISINCode').val('');
        $('#fondsOmschrijving').val('');
        $('#transactieSoort').val('');
        $('#aantal').val('');
        $('#orderSoortForm').slideUp();
        $('#koersInfo').html('');
        $('#orderWaarde').html('');
        $('input[name=fonds_id]').val('');

        $('#koersLimiet').val('0');
        $('#tijdsSoort').val('GTC');
        $('#tijdsLimiet').val('');
        $('#transactieSoort').css('background-color', 'white');
        
        
        if ($("#tempFixOrder").is(':checked'))
        {
          $("#fixOrder").prop('checked', true);
        } else
        {
          $("#fixOrder").prop('checked', false);
        }



        $('#rekening').val($('#tempRekening').val());


        tijdsSoortChanged();

        $('#controllesForm').html('');
        $('#controllesHolder').slideUp();

        $('#transactieSoort').css('background-color', 'white');
        $('.tab-controlles').hide();
        $('.tab-statusList').hide();

        pane = $(".orderTabHolder .ui-tabs-panel:visible").attr('id');
        if ( pane !== 'tab-extraOpties' ) {
          $('.orderTabHolder').tabs({ active: 0 });
          // $('a[href="#tab-extraOpties"]').get(0).click();
        }


        $('#ISINCode').val('');
        $('#fondsOmschrijving').val('');
        $('#transactieSoort').val('');
        $('#aantal').val('');
        $('#orderSoortForm').slideUp();
        $('#koersInfo').html('');
        $('#orderWaarde').html('');

        $('#koersLimiet').val('0');
        $('#tijdsSoort').val('GTC');
        $('#tijdsLimiet').val('');
        tijdsSoortChanged()

        $('#extraFondsOpties').slideUp();

        $('#fonds-koers-info').hide();
        $('#fonds-info').hide();

        $('.insertOrderButtonGroup').slideDown();
        $('#ISINCode').focus();

        $('#statusList').hide();

        $('#id').val('');
        $('#orderReden').val('');
        $('*[name="orderReden"]').val('');
        $('#orderid').val('');
        $('#orderregelId').val('');
        $('input [name="orderregelId"]').val('');
        $('#fondseenheidHidden').val('');
        $('#valutaKoersHidden').val('');
        $('#fondsValuta').val('');
        $('#fonds_id').val('');
        $('#fondsOmschrijvingHidden').val('');
        $('#transactieSoort').val('');

        $('#controlActions').show();
        // $('#saveSendButtons').hide();
        $('.fixButton').hide();
        $('#continueOrder').hide();

        $('#fonds-koers-info').hide();
        $('#fonds-info').hide();

        $('.insertOrderButtonGroup').slideDown();
        $('#ISINCode').focus();


        $("#fonds").val('');//leeg
        $("#transactieSoort").val('');//leeg
        $("#transactieSoort option").prop('disabled', false);
        $("#transactieSoort option:first-child").prop('disabled', false);

        $('#ISINCode').prop('readonly', false);
        $('#fondsOmschrijving').prop('readonly', false);

        //nieuw fonds toevoegen leeg maken
        $("#newFondsHolder").find('input:text').val('');
        $("#newFondsHolder").find('select').val('');
  
  
        setFixFields();
        careOrderChange ();
        changeRekening();
      } else
      {
        $.each(data.errors, function (field, fieldData)
        {
          $('#orderForm [name=' + field + ']').css('border-color', 'red');
          $('#orderForm [name=' + field + ']').parent().append('<span class="help-block with-errors">' + fieldData.message + '</span>');
        });
      }
    }
  })
};


$('#cancelOrder').on('click', function ()
{

  var confirmStatus = confirm("Weet u zeker dat u order {orderIdentificatie} wilt annuleren?");
  if (confirmStatus == true)
  {
    if ($('input[type=hidden][name=fixOrder]').val() == 0)
    {
      $('#cancel').val(1);
      $('#orderStatus').val(6);
      $(Cform).submit();
    } else if ($('input[type=hidden][name=fixOrder]').val() == 1)
    {
      var formURL = $(Cform).attr("action");
      $.ajax({
        url: formURL + '?cancel=1',
        type: "POST",
        dataType: 'json',
        data: {
          orderId: $('#id').val()
        },
        success: function (data, textStatus, jqXHR)
        {
          if (data.success === true)
          {
            if (data.cancel === true)
            {
              $('#cancel').val(1);
              $(Cform).submit();
            } else
            {
              $('#cancel').val(0);
              $('#saveForm').before(data.message);
            }
          }
        }
      });
    }
  } else
  {
    return false;
  }
});


$(document).on('change', '#tempRekening', function () {
  changeRekening($(this).attr('id'));
});

$(document).on('click', '#continueOrder', function ()
{
  $('#controllesForm').html('');
  $('#controllesHolder').slideUp();

  $('#transactieSoort').css('background-color', 'white');
  $('.tab-controlles').hide();
  $('.tab-statusList').hide();

  pane = $(".orderTabHolder .ui-tabs-panel:visible").attr('id');
  if ( pane !== 'tab-extraOpties' ) {
    $('.orderTabHolder').tabs({ active: 0 });
    // $('a[href="#tab-extraOpties"]').get(0).click();
  }

  $('#adviesMailHolder').hide();
  $('#previewAdviseMail #orderregelId').val('');
  $('#previewAdviseMail #orderid').val('');


  $('#ISINCode').val('');
  $('#fondsOmschrijving').val('');
  $('#transactieSoort').val('');
  $('#aantal').val('');
  $('#orderSoortForm').slideUp();
  $('#koersInfo').html('');
  $('#orderWaarde').html('');

  $('#koersLimiet').val('0');
  $('#tijdsSoort').val('GTC');
  $('#tijdsLimiet').val('');
  tijdsSoortChanged()
  $('#orderReden').val('');
  $('#extraFondsOpties').slideUp();

  $('#fonds-koers-info').hide();
  $('#fonds-info').hide();

  $('.insertOrderButtonGroup').slideDown();
  $('#ISINCode').focus();

  $('#statusList').hide();

  $('#id').val('');
  $('#orderid').val('');
  $("*[name=orderregelId]").val('');
  $('*[name="orderReden"]').val('');
  $('#orderregelId').val('');
  $('#fondseenheidHidden').val('');
  $('#valutaKoersHidden').val('');
  $('#fondsValuta').val('');
  $('#fonds_id').val('');
  $('#fondsOmschrijvingHidden').val('');
  $('#transactieSoort').val('');

  $('#controlActions').show();
  // $('#saveSendButtons').hide();
  $('.fixButton').hide();
  $('#continueOrder').hide();
});