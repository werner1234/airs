var Nform = '#nominaalForm';

var $BankDepotCodes = {BankDepotCodes};
var $fonds = {fondsData};

var orderaantalFieldEdit = 0;

$('.tab-controlles').hide();



$(function() {
  if ( $('input[name=id]').val() > 0 ) {
    orderaantalFieldEdit = 1; //set to 1 to ignore recalculation
    bedragChanged();
    $('.tab-controlles').show();
    $('#orderSoortForm').hide();
    $('#extraFondsOpties').show();
  } else {
    $('#transactieType').on('change', function (){
      if ( $(this).val() == 'L') {
        $('#extraFondsOpties').slideToggle();
      } else {
        $('#extraFondsOpties').slideUp();
      }
    });
  }



  $('.fixNietNodig').hide();
  $('.fixNodig').hide();
  $('.tab-controlles').hide();
  $('#extraFondsOpties').hide();
  $('#uitvoeringenList').hide();

  $('#orderregelsHolder').hide();

  $('.notEditable').prop('disabled', true);

  /** hide fix buttons **/
  $('#fixOrder').on('change', function () {
    console.log($("#rekeningNrTonen").val());
    if ($("#fixOrder").prop('checked') === true) {
      $('.fixButton').slideDown();
      $(".fixNietNodig").hide();
      $(".fixNodig").show();
      $('#orderStatus').val(0);
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

    if ( $('#orderStatus').val() >= 2 ) {
      $('.orderStatusField').show();
    }

    careOrderChange();
  });


  Inputmask.extendAliases({
    'maskValuta2digits': {
      alias: "numeric",
      allowPlus: false,
      allowMinus: true,
      placeholder: '',
      digits: 2,
      digitsOptional: true,
      autoGroup: false,
      rightAlign: true

    }
  });
  $('#bedrag').inputmask("maskValuta2digits");


  // $('#bedrag').inputmask({ "mask": "9", "digits": 2,"repeat": '*', "greedy": false, rightAlign: true });
  $(document).on('change', '#bedrag, #transactieSoort', function() {
    bedragChanged();
  });


  if ($('input[name=id]').val() > 0 || $('input[name=copyFrom]').val() > 0) {
    $('#extraOption').hide();

    /** set fixorder button state **/
    $('#fixOrder').trigger('change');

    if ( $('#fixTonen').val() == 0 ) {
      $('.fixOrderInput').hide();
    }

  } else {
    $('.fixButton').hide();
    $('.fixOrderInput').hide();
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
  $('#sendOrder, #sendOrderNew').on('click', function(event) {
    $('#verzenden').val(1);
    submitForm(event);
  });

  $('#saveOrder, #saveOrderNew, #saveOrderBack').on('click', function(event) {
    $('#verzenden').val(0);
    submitForm(event);
  });


  if ($('#fromRapport').val() > 0) {
    $('#orderSoortForm').hide();

    bankDepotbankChanged();

    portefeuilleChanged();
    isinChanged();

    valutaChanged($('#fondsValuta').val());

    aantalChanged();
    careOrderChange ();

    rekeningNrTonen($('#portefeuille').val());
  }



  $('#cancelOrder').on('click', function (event) {
    event.preventDefault();
    var confirmStatus = confirm("Weet u zeker dat u order {orderIdentificatie} wilt annuleren?");
    if (confirmStatus == true) {
      if ( $('input[type=hidden][name=fixOrder]').val() == 0 ) {
        $('#cancel').val(1);
        $('#orderStatus').val(6);
        $( Nform ).submit();
      } else if ( $('input[type=hidden][name=fixOrder]').val() == 1 ) {
        var formURL = $(Nform).attr("action");
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
                $( Nform ).submit();
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







function portefeuilleChanged() {
  fondsChanged("fonds");
  bankDepotbankChanged();
}

function isinChanged(data) {
  $('#fondsValutaDisplay').html($('#fondsValuta').val());
  if ( data !== undefined )
  {
    $fonds = data;
  }
  bankDepotbankChanged();
  bedragChanged();
}

const koersSleep = (delay) => new Promise((resolve) => setTimeout(resolve, delay))
function bedragChanged()
{
  console.log('orderaantalFieldEdit: '+orderaantalFieldEdit + ';');
  if ( orderaantalFieldEdit === 0 ) {
    const timeoutBedragChange = async () => {
      if ( $('input[name=fonds]').val() != '' ) {
        $valid = 0;
        do {
          if ( $('#koersLimietHidden').val() > 0 ) {
            $valid = 1;
            if ( $.isNumeric($('#bedrag').val()) ) {
              var $orderValue = round((
                $('#bedrag').val() /
                $('#koersLimietHidden').val() /
                $('#fondseenheidHidden').val()
              ),2);
              $('#orderaantalField').val(0);
              $('#orderAantal').html("");
              if ( $orderValue != '0.00' ) {
                $('#orderAantal').html("Geschat aantal: <b>"+ $orderValue +"</b> ");
                $('#orderaantalField').val($orderValue);
              }
            }
            aantalChanged();
          }
          await koersSleep(1000)
        }
        while ( $valid == 0);
      }
    }
    timeoutBedragChange();
  } else {
    orderaantalFieldEdit = 0;
    $('#orderAantal').html("Geschat aantal: <b>"+ $('#orderaantalField').val() +"</b> ");
  }
}

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
  
  event.preventDefault();
  Nvalidate = false;
  
  $.when(validateNForm()).done(function(a1){
    if ( Nvalidate == true ) {
      showLoading ('Opslaan');
      if ( $('input[name=id]').val() === '' || $('input[name=id]').val() === 0 || $('input[name=copyFrom]').val() > 0 ) {
        if ($('#verzenden').val() === 1) {
          $('#orderStatus').val(1)
        } else {
          $('#orderStatus').val(0)
        }
      }
      $( Nform ).submit();
    } else{removeLoading();}
  });
}

function validateNForm() {
  
  $(Nform + ' .help-block.with-errors').remove();
  $(Nform).find(':input').removeClass('input_error');
  
  var postData = $(Nform).serializeArray();
  var formURL = $(Nform).attr("action");
  return $.ajax({
    url : formURL + '?validate=true',
    type: "POST",
    dataType: 'json',
    data : postData,
    success:function(data, textStatus, jqXHR) {
      extraOptionError = 0;
      controleStatus = 0;
      pane = $(".orderTabHolder .ui-tabs-panel:visible").attr('id');
      
      if ( data.saved == false ) {
        $.each(data.error, function(field, fieldData) {

          if ( jQuery.inArray(field, ['tijdsLimiet', 'koersLimiet', 'tijdsSoort', 'memo']) !== -1 )
          {
            extraOptionError = 1;
          }

          if ( field != 'controleStatus' ) {
            controleStatus = 1;
            $(Nform + ' [name=' + field + ']').toggleClass('input_error');
            $(Nform + ' [name=' + field + ']').parent().append('<span class="help-block with-errors">' + fieldData.message + '</span>');
          } else {
            $('#checksMessageBox').html('<div class="alert alert-warning">U heeft nog niet alle controles gedaan!</div>');
          }


          if ( data.CheckResult !== '' && controleStatus == 0 ) {
            $('#controllesForm').html(data.CheckResult);
            $('#controllesForm').slideDown();
            $('.tab-controlles').show();

            /** Wanneer er geen fouten in het extra opties veld zitten de controlles tonen **/
            if ( extraOptionError == 0 && pane !== 'tab-controlles' )
            {
              var index = $('.orderTabHolder a[href="#tab-extraOpties"]').parent().index();
  
              $(".orderTabHolder").tabs("option", "active", 3);
            }

            $('.insertOrderButtonGroup').slideUp();
            $('#resetControle').val(0);
          }


        });


        if ( extraOptionError == 1 ) {
          if ( pane !== 'tab-extraOpties' )
          {
            $('.orderTabHolder').tabs({ active: 0 });
          }
        }

      } else {
        Nvalidate = true;
      }
      
    }
  });
}

