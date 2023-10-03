var getUrl = 'lookups/rekeningAfschriften.php';
var valutaIgnore = ['EUR', 'NLG', 'DEM'];

var focusveld = '';
var ajax = new Array();

/**
 * Build array from form
 * @param {type} theFormName
 * @returns {Object|document@arr;forms.elements.value}
 * 
 */
function buildQueryArray(theFormName) {
  var theForm = document.forms[theFormName];
  var qs = new Object();
  for (e = 0; e < theForm.elements.length; e++) {
    if (theForm.elements[e].name != '') {
      qs[theForm.elements[e].name] = theForm.elements[e].value;
    }
  }
  return qs;
}


/**
 * Get accounts for selected client
 * Fill accounts dropdown with values
 * 
 * @author RM
 * @since 16-9-2014
 */
function clientChanged()
{
  $.ajax({
    type: "GET",
    url: getUrl,
    dataType: "json",
    async: false,
    data: {
      type: 'fetchRekeningenList',
      form: buildQueryArray('editForm')
    },
    success: function(data, textStatus, jqXHR)
    {
      /** clear dropdown values **/
      $('select[name="Rekening"]').html('');
      /** loop result set and append to dropdown **/
      $.each(data.fullAccounts, function(index, value) {
        $('select[name="Rekening"]').append($('<option>').text(value.Rekening + ' (' + value.Depotbank + ')').attr('value', value.Rekening));
      });
    },
    error: function(jqXHR, textStatus, errorThrown)
    {
    }
  });
  /** old function **/
  //  setTimeout(function() {
  //    jsrsExecute("selectRS.php", populateRekening, "getRekeningen", buildQueryArray('editForm'), false);
  //  }, 10);
}

/**
 * Get account data if account changed sets saldo and Afschriftnummer
 * 
 * @author RM
 * @since 16-9-2014
 * 
 * @param {bool} voorlopigeRekeningmutaties
 */
function rekeningChanged(voorlopigeRekeningmutaties)
{
  if ( $('input[name=id]').val() > 0 ) {
    
  } else {
    if (voorlopigeRekeningmutaties == true)
    {
      $.ajax({
        type: "GET",
        url: getUrl,
        dataType: "json",
        async: false,
        data: {
          type: 'getVoorlopigeSaldo',
          form: buildQueryArray('editForm')
        },
        success: function(data, textStatus, jqXHR)
        {
          $('#Afschriftnummer').val(data.afschriftNummer);
          $('input[name="Saldo_tmp"]').val(data.saldo);
          $('input[name="Saldo"]').val(data.saldo);
        },
        error: function(jqXHR, textStatus, errorThrown)
        {
        }
      });
      /** old function **/
      //jsrsExecute("selectRS.php", populateSaldo, "getVoorlopigeSaldo", buildQueryArray('editForm'), false);
    }
    else
    {
      $.ajax({
        type: "GET",
        url: getUrl,
        dataType: "json",
        async: false,
        data: {
          type: 'getSaldo',
          form: buildQueryArray('editForm')
        },
        success: function(data, textStatus, jqXHR)
        {
          $('#Afschriftnummer').val(data.afschriftNummer);
          $('input[name="Saldo_tmp"]').val(data.nieuwSaldo);
          $('input[name="Saldo"]').val(data.nieuwSaldo);
        },
        error: function(jqXHR, textStatus, errorThrown)
        {
        }
      });
      /** old function **/
      //jsrsExecute("selectRS.php", populateSaldo, "getSaldo",buildQueryArray('editForm'), false);
    }
    checkDatum();
  }
}

function checkDatum()
{
  var boekJaar='';
  var afschfriftJaar='';
  var datum=$('[name="Datum"]').val();
  if(datum != '')
  {
    var datumParts = datum.split("-");
    if (datumParts.length == 3)
    {
      var boekJaar = datumParts[2];
    }
  }
  afschfriftJaar=$('#Afschriftnummer').val().substring(0, 4);
  //console.log('Afschriftnummer '+boekJaar+' '+afschfriftJaar+' '+datumParts.length);
  if(boekJaar != afschfriftJaar)
  {
    //alert("Het boekjaar komt niet overeen met het afschriftjaar");
    $('<div style="padding: 10px; max-width: 500px; word-wrap: break-word;">Het boekjaar komt niet overeen met het afschriftjaar.</div>').dialog({
      draggable: false,
      modal: true,
      resizable: false,
      width: 'auto',
      minHeight: 75,
      buttons: [{text: "OK",click: function() {$( this ).dialog( "close" );}}]
    });
    return false;
  }
  else
    return true;
}

function verzendFormulier()
{
  if(checkDatum()==true)
  {
    document.editForm.submit();
  }
}

function checkShortPositions (fieldId) {
  var transactieAantal = 0;
  var aantal = $('#fondsOwnedAmount').val();
  var calculatedAmount = 0;


  if ( $('#' + fieldId).val() != '' && $('#Aantal').val() > 0 ) {
    //check if we are buying or selling
    if ( jQuery.inArray($('#Transactietype').val(), ['L', 'V', 'V/O', 'V/S' ]) >= 0 ) { //verkoop
      var transactieAantal = Math.abs($('#Aantal').val());
      var transactieAantal = Number(transactieAantal) * -1;
      var calculatedAmount = (Number(aantal) + Number(transactieAantal));
      if ( calculatedAmount < 0 ) {
        
        if ($('#shortPositionDialog').length <= 0) { 
          var holdDiv = $('<div></div>').attr('id', 'shortPositionDialog');
          holdDiv.appendTo('body');
        }
        $("#shortPositionDialog").html("");
        $("#shortPositionDialog").append("In portefeuille: " + aantal + '<br />');
        $("#shortPositionDialog").append("Transactie: " + transactieAantal + '<br />');
        $("#shortPositionDialog").append("Na transactie: " + calculatedAmount + '<br />');
        
        $("#shortPositionDialog").dialog({
          resizable: false,
          modal: true,
          title: "Shortposities",
          height: 250,
          width: 400,
          buttons: {
            "Doorgaan": function () {
              $(this).dialog('close');
              shortPositioncallback(true);
            },
              "Afbreken": function () {
              $(this).dialog('close');
              shortPositioncallback(false);
            }
          }
        });
      }
    } else if (jQuery.inArray($('#Transactietype').val(), ['D', 'A', 'A/O', 'A/S']) === -1 ) { //aantkoop
    }
  }
}


function shortPositioncallback(value) {
  if ( ! value) {
    $('#Aantal').val('');
  }
}


$(".requiredField").each(function() {
  var rowIndex = $(this).parent().parent().index('tr');
  var tdIndex = $(this).parent().index('tr:eq('+rowIndex+') td');
  $(this).parent().parent().parent().find('td:eq('+tdIndex+')').append('<span class="required" style="color:red;font-size: 16px;position: absolute;margin: -2px 0px 0px 5px;">*</span>');
});

$('#submit-form').bind('keypress keydown keyup', function(e){
    if(e.keyCode == 13) { $('#radio-result').submit(); }
});
$('#radio-result input').bind('keypress keydown keyup', function(e){
    if(e.keyCode == 13) { e.preventDefault(); }
});


$('input[name=\"Boekdatum\"]').blur(function() {
    if ( this.value === '' ) {
        this.focus();
        return false;
    }
});


$(function () {
  
  $('#Omschrijving').on('keyup', function () {
    if ($(this).val().length >= 50  ) {
      if ( ! $('.field_' + $(this).attr('id'))[0] ) {
        $(this).after('<span class="alert alert-error field_' + $(this).attr('id') + '" style="padding: 4px 11px;margin-top: 3px;     margin-left: 10px;" >Mag niet niet langer zijn dan 50 tekens.</span>');
      }
    } else {
      $('.field_' + $(this).attr('id')).remove();
    }
  })

});