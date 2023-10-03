/** functies voor enkelvoudig **/
var Eform = 'form#enkelvoudigForm';
var $orderStatusElement = '<input type="hidden" name="orderStatus" class="" id="orderStatus" value="0">';
var $BankDepotCodes = {BankDepotCodes};
var $fonds = {fondsData};


function portefeuilleChanged (data) {
  //FondsAantal
  bankDepotbankChanged();
}

function isinChanged (data) {
  if ( data !== undefined )
  {
    $fonds = data;
  }
  bankDepotbankChanged();
}

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

$(function () {
  
  $('.fixNietNodig').hide();
  $('.fixNodig').hide();
  // $('#controllesHolder').hide();
  $('.tab-controlles').hide();
  
  $('#extraFondsOpties').hide();
  $('#uitvoeringenList').hide();
  //
  
  $('.notEditable').prop('disabled', true);
  
  
  /** hide fix buttons **/
  $('#fixOrder').on('change', function () {
    
    if ($("#fixOrder").prop('checked') === true) {
      $('.fixButton').slideDown();
      $(".fixNietNodig").hide();
      $(".fixNodig").show();
      $('#orderStatus').val(0);
      //$('#fondsOmschrijving').prop('readonly', true);
      //$('#fondsOmschrijving').val($('#fondsOmschrijvingHidden').val());
      //$('#fondsOmschrijving').addClass('readOnlyField');
    } else {
      $('.fixButton').slideUp();
      $(".fixNietNodig").show();
      $(".fixNodig").hide();
      //$('#fondsOmschrijving').prop('readonly', false);
      //$('#fondsOmschrijving').removeClass('readOnlyField');
    }
    
    if ( $("#rekeningNrTonen").val() == 1 ) {
      $(".rekeningField").show();
    } else {
      $(".rekeningField").hide();
    }
    
    if ( $('#id').val() == "" ) {
      $('.statusField').hide();
    }
    
    if ( $('#orderStatus').val() >= 2 ) {
      $('.orderStatusField').show();
    }
    careOrderChange();
  });
  
  
  if ($('#fromRapport').val() > 0) {
    $('#orderSoortForm').hide();
    
    bankDepotbankChanged();
    
    portefeuilleChanged();
    isinChanged();
    
    valutaChanged($('#fondsValuta').val());
    
    aantalChanged();
    setFixFields();
    careOrderChange ();
    
    rekeningNrTonen($('#portefeuille').val());
    
    fondsChanged("fonds");
  }
  
  
  
  
  if ($('input[name=id]').val() > 0 || $('input[name=copyFrom]').val() > 0 || $('#fromRapport').val() == 1 )
  {
    if ($('input[name=id]').val() > 0)
    {
      $('#extraOption').hide();
    }
    /** set fixorder button state **/
    $('#fixOrder').trigger('change');
    
    if ( $('#fixTonen').val() == 0 ) {
      $('.fixOrderInput').hide();
    }
  } else {
    $('.fixButton').hide();
    $('.fixOrderInput').hide();
  }
  
  if ( $('input[name=id]').val() > 0 ) {
    // $('#controllesHolder').show();
    $('.tab-controlles').show();
    
    $('#orderSoortForm').hide();
    $('#extraFondsOpties').show();
  }
  $('#transactieType').on('change', function (event) {
    pane = $(".orderTabHolder .ui-tabs-panel:visible").attr('id');
    if ( pane !== 'tab-extraOpties' ) {
      $(".orderTabHolder").tabs('select', 'tab-extraOpties');
    }
  });
  
  
  
  
  
  //, #transactieSoort, #aantal, #koersLimiet
//  $('#PortefeuilleSelectie, #rekening, #ISINCode, #aantal').on('change', function () {
//    if ( $('#orderStatus').val() == 0 ) {
//      clearControlle();
//    };
//  });
  
  
  if ( $('#fromRapport').val() > 0 ) {
    $('#toOrderlist').html('Terug naar rapportage');
    $('#toOrderlistTop').html('Terug naar rapportage');
  }
  
  //change state based on button
  $('#saveOrderNew, #sendOrderNew').on('click', function() {
    $('input[name=action]').val('update');
    $('input[name=redirect]').val('saveNew');
  });
  
  //change state based on button
  $('#saveOrderBack').on('click', function() {
    $('input[name=action]').val('update');
    $('input[name=redirect]').val('saveBack');
  });
  
  $('#saveOrder, #sendOrder').on('click', function() {
    $('input[name=action]').val('update');
    $('input[name=redirect]').val('saveStay');
  });
  
  
  //set status based on button
  $('#sendOrder, #sendOrderNew, #sendOrderBackReport').on('click', function(event) {
    event.preventDefault();
    if (
      (
        '{adviesRelatie}' === '1'
        && ( '{adviesStatus}' != '5' && '{adviesStatus}' != '0' )
      )
      // 1 = versonden 0 = niet verzonden
      && ('{adviesEmailVerzonden}' == '0' && '{adviesEmailGenegeerd}' != '1' ) ) {
      
      AEMessage("Order kan niet worden verstuurd. Maak eerst e-mail voor deze Adviesrelatie, de order wordt opgeslagen!", "Advies relatie", function () {
        $('#verzenden').val(0);
        submitForm(event);
      });
    } else {
      $('#verzenden').val(1);
      submitForm(event);
    }
  });
  
  $('#saveOrder, #saveOrderNew, #saveOrderBack, #saveOrderBackReport').on('click', function(event) {
    $('#verzenden').val(0);
    submitForm(event);
  });


  $('#cancelOrder').on('click', function (event) {
    event.preventDefault();
    var confirmStatus = confirm("Weet u zeker dat u order {orderIdentificatie} wilt annuleren?");
    if (confirmStatus == true) {
      if ( $('input[type=hidden][name=fixOrder]').val() == 0 ) {
        $('#cancel').val(1);
        $('#orderStatus').val(6);
        $( Eform ).submit();
      } else if ( $('input[type=hidden][name=fixOrder]').val() == 1 ) {
        var formURL = $(Eform).attr("action");
        $.ajax({
          url : formURL + '?cancel=1',
          type: "POST",
          dataType: 'json',
          data : {
            orderId: $('#id').val()
          },
          success:function(data, textStatus, jqXHR) {
            if ( data.success === true ) {
              if ( data.cancel === true) {
                $('#cancel').val(1);
                $( Eform ).submit();
              } else {
                $('#cancel').val(0);
                $('#saveForm').before(data.message);
              }
            }
          }
        });
      }
    } else {
      return false;
    }
  });
  
});



//function clearControlle() {
//  $('#controllesHolder').slideUp();
//  $('#controllesHolder').find('input[type=checkbox]:checked').removeAttr('checked');
//}

function submitForm(event) {
  
  $('.btn-new').attr('disabled', 'disabled');
  $('.btn-new').prepend('<i class="stopSave fa fa-spinner fa-spin fa-fw"></i>');
  showLoading ('Valideren');
  setTimeout(
    function()
    {
      $('.btn-new').removeAttr("disabled");
      $('.stopSave').remove();
    }, 1500);
  
  console.log('submittrigger');
  event.preventDefault();
  
  setTimeout(function()
  {
//$('#saveOrder').on('click', function() {
    
    Evalidate = false;
    
    $.when(validateEForm()).done(function (a1)
    {
      console.log('validatedone ');
      if (Evalidate == true)
      {
        showLoading('Opslaan');
        if ($('input[name=id]').val() === '' || $('input[name=id]').val() === 0 || $('input[name=copyFrom]').val() > 0)
        {
          if ($('#verzenden').val() === 1)
          {
            $('#orderStatus').val(1)
          } else
          {
            $('#orderStatus').val(0)
          }
        }
        // document.getElementById("enkelvoudigForm").submit();
        setTimeout(function ()
        {
          $(document).find(Eform).submit();
        }, 25);
        //$( Eform ).submit();
      } else
      {
        removeLoading();
      }
    });
  },30);
//});
}

function validateEForm() {
  $(Eform + ' .help-block.with-errors').remove();
  $(Eform).find(':input').removeClass('input_error');
  
  var postData = $(Eform).serializeArray();
  var formURL = $(Eform).attr("action");
  return $.ajax({
    url : formURL + '?validate=true',
    type: "POST",
    dataType: 'json',
    data : postData,
    success:function(data, textStatus, jqXHR) {
      extraOptionError = 0;
      controleStatus = 0;
      //Bepalen welke tab actief is
      pane = $(".orderTabHolder .ui-tabs-panel:visible").attr('id');
      
      if ( data.saved == false ) {
        $.each(data.error, function(field, fieldData) {
          if ( jQuery.inArray(field, ['tijdsLimiet', 'koersLimiet', 'tijdsSoort', 'memo']) !== -1 )
          {
            extraOptionError = 1;
          }
          
          if ( field != 'controleStatus' ) {
            controleStatus = 1;
            $(Eform + ' [name=' + field + ']').toggleClass('input_error');
            $(Eform + ' [name=' + field + ']').parent().append('<span class="help-block with-errors"><br />' + fieldData.message + '</span>');
          } else {
            $('#checksMessageBox').html('<div class="alert alert-warning">U heeft nog niet alle controles gedaan!</div>');
          }
        });
        
        if ( extraOptionError == 1 ) {
          if ( pane !== 'tab-extraOpties' )
          {
            $(".orderTabHolder").tabs('select', 'tab-extraOpties');
          }
        }
        
        if ( data.CheckResult !== '' && controleStatus == 0 ) {
          $('#resetControle').val(0);
          $('#controllesForm').html(data.CheckResult);
          $('#controllesForm').slideDown();
          $('.tab-controlles').show();
          
          /** Wanneer er geen fouten in het extra opties veld zitten de controlles tonen **/
          if ( extraOptionError == 0 && pane !== 'tab-controlles' )
          {
            // $(".orderTabHolder").tabs('select', 'tab-controlles');
            $('a[href="#tab-controlles"]').get(0).click();
          }
          
          $('.insertOrderButtonGroup').slideUp();
        }
      } else {
        Evalidate = true;
      }
      
    }
  });
}


