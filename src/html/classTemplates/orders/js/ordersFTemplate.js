/** functies voor enkelvoudig **/
var fxform = 'form#fxForm';
var $orderStatusElement = '<input type="hidden" name="orderStatus" class="" id="orderStatus" value="0">';

function portefeuilleChanged (data) {
  //FondsAantal
}

function isinChanged (data) {
  $('#fondsValutaDisplay').html($('#fondsValuta').val());
  // $('#ISINCode').val(data.Fonds);
}


$(function () {
  $("#showFxFonds").bind('click', function(){
    if($('#ISINCode').data('is_open')) {
      $('#ISINCode').autocomplete('close');
    } else {
      $('#ISINCode').autocomplete("search", '');
    }
  });

  $('#ISINCode').bind('autocompleteopen', function(event, ui) {
    $(this).data('is_open',true);
  });

  $('#ISINCode').bind('autocompleteclose', function(event, ui) {
    $(this).data('is_open',false);
  });

  if ( $('#orderStatus').val() >= 1 ) {
    $('#showFxFonds').hide();
  }


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

    if ( $('#id').val() == "" ) {
      $('.statusField').hide();
    }
  });

  if ($('input[name=id]').val() > 0 || $('input[name=copyFrom]').val() > 0)
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
    $('#transactieType').val('B');
    $('.fixButton').hide();
    $('.fixOrderInput').hide();
  }

  console.log($('input[name=id]').val());
  if ( $('input[name=id]').val() > 0 ) {
    // $('#controllesHolder').show();
    $('.tab-controlles').show();

    $('#orderSoortForm').hide();
    $('#extraFondsOpties').show();
  }





    $('#transactieType').on('change', function (event) {
      pane = $(".orderTabHolder .ui-tabs-panel:visible").attr('id');
      if ( pane !== 'tab-extraOpties' ) {
        $('.orderTabHolder').tabs({ active: 0 });
      }
    });





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
  $('#sendOrder, #sendOrderNew').on('click', function(event) {
    $('#verzenden').val(1);
    submitForm(event);
  });

  $('#saveOrder, #saveOrderNew, #saveOrderBack').on('click', function(event) {
    $('#verzenden').val(0);
    submitForm(event);
  });


  $('#cancelOrder').on('click', function () {

    var confirmStatus = confirm("Weet u zeker dat u order {orderIdentificatie} wilt annuleren?");
    if (confirmStatus == true) {
      if ( $('input[type=hidden][name=fixOrder]').val() == 0 ) {
        $('#cancel').val(1);
        $('#orderStatus').val(6);
        $( Eform ).submit();
      } else if ( $('input[type=hidden][name=fixOrder]').val() == 1 ) {
        var formURL = $(fxform).attr("action");
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
  
  setTimeout(
    function()
    {
      $('.btn-new').removeAttr("disabled");
      $('.stopSave').remove();
    }, 1500);
  
//  setTimeout(function(){
    showLoading ('Valideren');
//  }, 1000);

//$('#saveOrder').on('click', function() {
  event.preventDefault();
  Evalidate = false;

  $.when(validateEForm()).done(function(a1){
    if ( Evalidate == true ) {
      showLoading ('Opslaan');
      if ( $('input[name=id]').val() === '' || $('input[name=id]').val() === 0 || $('input[name=copyFrom]').val() > 0 ) {
        if ($('#verzenden').val() === 1) {
          $('#orderStatus').val(1)
        } else {
          $('#orderStatus').val(0)
        }
      }
      // document.getElementById("fxForm").submit();
      $(document).find(fxform).submit();
      //$( Eform ).submit();
    } else{removeLoading();}
  });

//});
}

function validateEForm() {

  $(fxform + ' .help-block.with-errors').remove();
  $(fxform).find(':input').removeClass('input_error');

  var postData = $(fxform).serializeArray();
  var formURL = $(fxform).attr("action");
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
            $(fxform + ' [name=' + field + ']').toggleClass('input_error');
            $(fxform + ' [name=' + field + ']').parent().append('<span class="help-block with-errors"><br />' + fieldData.message + '</span>');
          } else {
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
          $('#controllesForm').slideDown();
          $('.tab-controlles').show();

          /** Wanneer er geen fouten in het extra opties veld zitten de controlles tonen **/
          if ( extraOptionError == 0 && pane !== 'tab-controlles' )
          {
            // $(".orderTabHolder").tabs('select', 'tab-controlles');
            $('.orderTabHolder').tabs({ active: 3 });
          }

          $('.insertOrderButtonGroup').slideUp();
          $('#resetControle').val(0);
        }
      } else {
        Evalidate = true;
      }

    }
  });
}


