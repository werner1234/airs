var getUrl = 'lookups/rekeningAfschriften.php';
var focusveld = '';
var ajax = new Array();

/** Clear radio messages on load**/
$('#radio-messages').html('');



function checkKey(e){
  switch (e.keyCode) {
    case 45:
      if( $('input[name=mutatieVerschil]').val() == 0) {
        //insert afschrift
        document.location = "rekeningafschriften_v2_Edit.php?action=new&type=" + $('input[name=mutationType]').val + "&memoriaal=" + $('#isMemoriaal').val();
      } else {
        alert("Er is nog een mutatie verschil van " + $('input[name=mutatieVerschil]').val());
      }
      break;
  }      
}

if (navigator.userAgent.indexOf("Firefox") > 0) {
    $(document).keypress (checkKey);
} else {
    $(document).keydown (checkKey);
}


/**
 * Sets valuta info to the word manual if someone enters a value
 * @param {type} fieldId
 * @returns {undefined}
 */
function manuelValutaDateInfo(fieldId)
{
  $('#' + fieldId).removeClass();
  $('#' + fieldId).addClass('label label-info');
  $('#' + fieldId).html('Handmatig');
}

function deleteValutaDateInfo(fieldId)
{
  $('#' + fieldId).removeClass();
  $('#' + fieldId).html('');
}

function setValutaDateInfo(fieldId, bookDate, valutaDate)
{
  $('#' + fieldId).removeClass();

  if ($.datepicker.formatDate('dd-mm-yy', new Date(bookDate)) == $.datepicker.formatDate('dd-mm-yy', new Date(valutaDate)))
  {
    $('#' + fieldId).addClass('label label-success');
  } else {
    $('#' + fieldId).addClass('label label-warning');
  }
  $('#' + fieldId).html($.datepicker.formatDate('dd-mm-yy', new Date(valutaDate)));
}
;

//end on valuta change functions
function fondsChanged(fieldId)
{
  $('#fonds-koers-info').removeClass();

  var grootboek = $('#Grootboekrekening').val();
  var mutationType = $('input[name=mutation_type]').val();
  

  /**
   * Wanneer we in het wijzig of overige formulier zitten alleen uitvoeren wanneer het grootboek FONDS is
   *
   * Bij de andere formulieren deze functie gewoon uitvoeren
   */

  if ( ( (mutationType == 'overige' || mutationType == 'editForm') && grootboek == 'FONDS') || mutationType != 'overige' && mutationType != 'editForm' ) {


  // if ( grootboek == 'FONDS' ) {
    var bookDate = $('input[name=Boekdatum]').val();
    var inputDate = bookDate.split('-').reverse().join('-');

    var inputDate = $.datepicker.formatDate('dd-mm-yy', new Date(inputDate));

    $.ajax({
      type: "GET",
      url: getUrl,
      dataType: "json",
      async: false,
      data: {
        type: 'getFondskoers',
        fonds: $('#' + fieldId).val(),
        date: bookDate
      },
      success: function(data, textStatus, jqXHR)
      {
        var fondsDate = $.datepicker.formatDate('dd-mm-yy', new Date(data.datum));


        if (inputDate == fondsDate) {
          $('#fonds-koers-info').addClass('label label-success');
        } else {
          $('#fonds-koers-info').addClass('label label-warning');
        }
        $('#fonds-koers-info').html($.datepicker.formatDate('dd-mm-yy', new Date(data.datum)));
        $('#Fondskoers').val(data.Koers);
      },
      error: function(jqXHR, textStatus, errorThrown)
      {
      }
    });
  }
};
function ajaxSubmit(formSubmit, formId)
{
  $('#' + formSubmit).click(function(event) {
    event.preventDefault();
    
    if ( checkVastzetDatum () == false ) {
      return false;
    }
    
    var form = $('#' + formId);
    $('#' + formId).find(':input').css('border-color', ''); //reset inputfield style
    var json = ConvertFormToJSON(form);
    
    if ( formSubmit == 'submitCounterRule') {
      json['createRule'] = 'true';
    }
    
    $.ajax({
      url: 'rekeningmutaties_v2_process.php',
      type: 'POST',
      data: json,
      dataType: "json",
      success: function(data) {
        $('#radio-messages').html(''); //clear messages

        if (data.success == 1) {
          $('#radio-messages').html('<div class="alert alert-success" role="alert">' + data.message + '</div>');

          document.getElementById('rules').contentWindow.location.reload();

          $('input[name=select]').focus();
          //document.getElementsByName('select')[0].focus();

          $('#radio-result').html('');
          getMutationdifference();
          
          if ( data.reopen.length > 0 ) {
            $( "#radio-result" ).load( "rekeningmutaties_v2_" + data.reopen + ".php?afschrift_id=" + $('#isAfschriftId').val() + "&type=" + $('input[name=type]').val());
          }
          
          if ( data.return.length > 0 ) {
            console.log(data.return);
            window.location.href = data.return;
          }
          
          if ($('#isMemoriaal').val() == 1) {
            $('#radio-select').hide();
            $( "#radio-result" ).load( "rekeningmutaties_v2_memoriaal.php?afschrift_id=" + $('#isAfschriftId').val() + "&type=" + $('input[name=type]').val());
          }
        } else {
          $.each(data.errors, function(field, fieldData) {
            $('#radio-messages').prepend('<div class="alert alert-error" role="alert">' + fieldData.description + ': ' + fieldData.message + '</div>');
            $('[name=' + field + ']').css('border-color', 'red');
          });

          $('#radio-messages').prepend('<div class="alert alert-error" role="alert">' + data.message + '</div>');
        }
      },
      error: function(data) {
      },
      cache: false
    });
  });
};




function getMutationdifference() {
  $.ajax({
    type: "GET",
    url: getUrl,
    dataType: "json",
    async: false,
    data: {
      type: 'getMutationDifference',
      aAfschriftnummer: $('input[name="editAfschriftNummer"]').val(),
      aRekening: $('input[name="editRekening"]').val(),
      aTotaalMutatie: $('input[name="totaalMutatie"]').val(),
      mutationType : $('input[name="type"]').val(),
    },
    success: function(data, textStatus, jqXHR)
    {
      if (data.mutatieVerschil == 0) {
        $('#differenceMessage').hide();
        $('#mutationFieldset').removeClass('fieldsetWarningLeft');
        $('#mutationFieldset').addClass('fieldsetsuccessLeft');
      } else {
        $('#differenceMessage').show();
        $('#mutationFieldset').addClass('fieldsetWarningLeft');
        $('#mutationFieldset').removeClass('fieldsetsuccessLeft');
      }

      $('input[name=mutatieVerschil]').val(data.mutatieVerschil);
    },
    error: function(jqXHR, textStatus, errorThrown)
    {
    }
  });
}

function ConvertFormToJSON(form) {
  var array = jQuery(form).serializeArray();
  var json = {};

  jQuery.each(array, function() {
    json[this.name] = this.value || '';
  });

  return json;
}


function checkFondsAantal(fondsId)
{
  var fonds = $('#' + fondsId).val();
  var rekening = $('input[name=Rekening]').val();
  var boekDatum = $('input[name=Boekdatum]').val();

  var transactieAantal = $('#Aantal').val() + 0;
  var Transactietype = $('#Transactietype').val();

  $.ajax({
    type: 'GET',
    url: getUrl,
    dataType: 'json',
    async: false,
    data: {
      type: 'FondsAantal',
      rekening: rekening,
      fondsId: fonds,
      date: boekDatum
    },
    success: function(data, textStatus, jqXHR) {
      $('#fondsOwnedAmount').val(data.aantal);
      $('#fondsOwnedInfo').html('In portefeuille: (' + data.aantal + ')');
      $('#fondsOwnedInfo').addClass('label label-info');
    },
    error: function(jqXHR, textStatus, errorThrown) {
      result = jqXHR;
    }
  });
}



$(function() {
  $('#deleteAll').on('click', function () {
    return confirm('Dit afschrift bevat mogelijk nog afschriftregels weet u zeker dat u dit afschrift wil verwijderen!');
  });
})