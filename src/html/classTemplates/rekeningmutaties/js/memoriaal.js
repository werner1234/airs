$('#recalculate').on('click', function () {
  if ( $('#Fonds').val().length > 0 ) {
    fondsChanged('Fonds');
  }
  $('#Valuta').trigger('change');
  
  mutatieSum();
  kostenSum ();
  kostenBuitenlandSum ();
  renteSum ();
  waardesum();
});

$(function () {
  $('#Boekdatum').on('change', function () {
    $('#boekdatumMsg').hide();
    var boekDatumCheckDate = $("#boekDatumCheckDate").val().split('-'),
      boekDatumCheckDateYear = parseInt(boekDatumCheckDate[2], 10), // cast Strings as Numbers
      boekDatumCheckDateMo = parseInt(boekDatumCheckDate[1], 10),
      boekDatumCheckDateDay = parseInt(boekDatumCheckDate[0], 10);
    
    var boekDatumDate = $(this).val().split('-'),
      boekDatumDateYear = parseInt(boekDatumDate[2], 10), // cast Strings as Numbers
      boekDatumDateMo = parseInt(boekDatumDate[1], 10),
      boekDatumDateDay = parseInt(boekDatumDate[0], 10);
    
    var boekDatumDate = new Date( boekDatumDateYear + '-' + boekDatumDateMo + '-' + boekDatumDateDay );
    var boekDatumCheckDate = new Date( boekDatumCheckDateYear + '-' + boekDatumCheckDateMo + '-' + boekDatumCheckDateDay);
    
    if ( boekDatumDate < boekDatumCheckDate ) {
      $('#boekdatumMsg').show();
    };
  });
});

$('#Aantal, #Transactietype, input[name="Boekdatum"], #Fonds').on('change', function () {checkShortPositions("Fonds");});

/**
 * Change description based on transaction type
 * @returns {undefined}
 */
function setDescription () {
  var transactieType = $('#Transactietype').val();
  var av = 'Deponering';
  
  if ( transactieType == 'B' ) {
    var av = 'Inbreng';
  }
  if (jQuery.inArray(transactieType, ['D', 'A/O', 'A/S']) != -1) {
    var av = 'Deponering';
  }
  if (jQuery.inArray(transactieType, ['L', 'V/O', 'V/S']) != -1) {
    var av = 'Lichting';
  }
  
  var fondsOmschrijving = av + ' ' + $('#fonds_omschrijving').val();
  
  /** wanneer omschrijving groter/gelijk is dan 50 **/
  if(fondsOmschrijving.length >= 50) {
    var fondsOmschrijving = av + ' ' + $('#fonds_fonds').val();
  }
  var fondsOmschrijving = fondsOmschrijving.substring(0,50);

  $('#Omschrijving').val(fondsOmschrijving);

  //$('#Omschrijving').val(av + ' ' + $('#fonds_omschrijving').val());
}

//$('#Transactietype').on('change', function () {setDescription();});


var getUrl = 'lookups/rekeningAfschriften.php';

ajaxSubmit('submitCounterRule', 'memoriaal-form');
ajaxSubmit('submitNoCounterRule', 'memoriaal-form');

function valutaChange (fieldId, type, koers) {
//  $('#' + fieldId).on('change', function () {
  $.ajax({
    type: 'GET',
    url: getUrl,
    dataType: 'json',
    async: false,
    data: {
      type: 'getExchangeRate',
      rekeningValuta: $('input[name=RekeningValuta]').val(),
      valuta: $('#' + fieldId).val(),
      date: $('[name=Boekdatum]').val()
    },
    success: function(data, textStatus, jqXHR) {

      $('#' + koers).val(data.valuta.Koers);
      var boekDatum = $('[name=Boekdatum]').val().split('-').reverse().join('-');
      var boekDate = new Date(boekDatum);
      var valutaDate = new Date(data.valuta.Datum);

      //set date info on valuta fields
      setValutaDateInfo(type + '-valuta-koers-info', boekDate, valutaDate);

    },
    error: function(jqXHR, textStatus, errorThrown) {
      result = jqXHR;
    }
  });
//});
}

$('#Valuta').on('change', function () {
  $.ajax({
    type: 'GET',
    url: getUrl,
    dataType: 'json',
    async: false,
    data: {
      type: 'getExchangeRate',
      rekeningValuta: $('input[name=RekeningValuta]').val(),
      valuta: $('#Valuta').val(),
      date: $('[name=Boekdatum]').val()
    },
    success: function(data, textStatus, jqXHR) {

      $('#Valutakoers').val(data.valuta.Koers);
      $('#RekeningValutakoers').val(data.rekeningValuta.Koers);
      var boekDatum = $('[name=Boekdatum]').val().split('-').reverse().join('-');
      var boekDate = new Date(boekDatum);
      var valutaDate = new Date(data.valuta.Datum);

        deleteValutaDateInfo('valuta-koers-info');
        deleteValutaDateInfo('kosten-valuta-koers-info');
        deleteValutaDateInfo('kostenBuitenland-valuta-koers-info');
        deleteValutaDateInfo('rente-valuta-koers-info');
        if( $.inArray($('#Valuta').val(), valutaIgnore) == -1 ) {
          setValutaDateInfo('valuta-koers-info', boekDate, valutaDate);
          setValutaDateInfo('kosten-valuta-koers-info', boekDate, valutaDate);
          setValutaDateInfo('kostenBuitenland-valuta-koers-info', boekDate, valutaDate);
          setValutaDateInfo('rente-valuta-koers-info', boekDate, valutaDate);
        }
      
      //set valuta to sub fields
      $('#Kosten_Valuta').val(data.valuta.Valuta);
      $('#kostenBuitenland_Valuta').val(data.valuta.Valuta);
      $('#rente_Valuta').val(data.valuta.Valuta);

      //set valutakoers to sub fields
      $('#Kosten_Valutakoers').val(data.valuta.Koers);
      $('#kostenBuitenland_Valutakoers').val(data.valuta.Koers);
      $('#rente_Valutakoers').val(data.valuta.Koers);
      
      mutatieSum();
      renteSum ();

    },
    error: function(jqXHR, textStatus, errorThrown) {
      result = jqXHR;
    }
  });
});


$('#Valutakoers').on('change', function () {
  manuelValutaDateInfo('valuta-koers-info');

  /** check other valuta lines **/
  var currentValuta = $('#Valuta').val();
  var currentValutaKoers = $('#Valutakoers').val();
  /** check other valuta lines **/      
  if ( $('#rente_Valuta').val() === currentValuta ) {
    $('#rente_Valutakoers').val(currentValutaKoers);
    $('#rente_Valutakoers').change();
    manuelValutaDateInfo('rente-valuta-koers-info');
  }

});
$('#Fondskoers').on('change', function () {manuelValutaDateInfo('fonds-koers-info')});


//waarde change
$('#Fonds').on('change', function () {waardesum();});
$('#Aantal').on('change', function () {waardesum(); setDescription ();getRente();});
$('#Fondskoers').on('change', function () {waardesum()});
$('#fondseenheid').on('change', function () {waardesum()});

function waardesum () {
  var aantal = $('#Aantal').val();
  var fondsKoers = $('#Fondskoers').val();
  var fondsEenheid = $('#fondseenheid').val();
  var total = Number(aantal) * Number(fondsKoers) * Number(fondsEenheid);
  $('#value-input').val(Math.round(total * 100) / 100);
  mutatieSum();
};

//mutatiebedrag
$('#value-input').on('change', function () {mutatieSum()});
$('#Valutakoers').on('change', function () {mutatieSum()});

function mutatieSum () {
  var value = $('#value-input').val();
  var valutakoers = $('#Valutakoers').val();
  
  var total = Number(value) * Number(valutakoers);
  $('#total').val(Math.round(total * 100) / 100);
  
  setTotalCalculation();
};

//kosten sum
$('#Kosten_Input').on('change', function () {kostenSum()});
$('#Kosten_Valutakoers').on('change', function () {kostenSum()});
$('#Kosten_Valutakoers').on('change', function () {manuelValutaDateInfo('kosten-valuta-koers-info')});

$('#Kosten_Valuta').on('change', function () {
  $.when(valutaChange('Kosten_Valuta', 'kosten', 'Kosten_Valutakoers')).done(function(a1){
    kostenSum ();
  });
});
function kostenSum () {
  var value = $('#Kosten_Input').val();
  var valutakoers = $('#Kosten_Valutakoers').val();
  
  var total = Number(value) * Number(valutakoers);
  $('#Kosten_Bedrag').val(Math.round(total * 100) / 100);
  
  setTotalCalculation();
};


//kostenBuitenland sum
$('#kostenBuitenland_Input').on('change', function () {kostenBuitenlandSum()});
$('#kostenBuitenland_Valutakoers').on('change', function () {kostenBuitenlandSum()});
$('#kostenBuitenland_Valutakoers').on('change', function () {manuelValutaDateInfo('kostenBuitenland-valuta-koers-info')});

$('#kostenBuitenland_Valuta').on('change', function () {
  $.when(valutaChange('kostenBuitenland_Valuta', 'kostenBuitenland', 'kostenBuitenland_Valutakoers')).done(function(a1){
    kostenBuitenlandSum ();
  });
});
function kostenBuitenlandSum () {
  var value = $('#kostenBuitenland_Input').val();
  var valutakoers = $('#kostenBuitenland_Valutakoers').val();
  
  var total = Number(value) * Number(valutakoers);
  $('#kostenBuitenland_Bedrag').val(Math.round(total * 100) / 100);
  
  setTotalCalculation();
};


//rente sum
$('#rente_Input').on('change', function () {renteSum()});
$('#rente_Valutakoers').on('change', function () {renteSum()});
$('#rente_Valutakoers').on('change', function () {manuelValutaDateInfo('rente-valuta-koers-info')});

$('#rente_Valuta').on('change', function () {
  $.when(valutaChange('rente_Valuta', 'rente', 'rente_Valutakoers')).done(function(a1){
    renteSum ();
  });
});
function renteSum () {
  var value = $('#rente_Input').val();
  var valutakoers = $('#rente_Valutakoers').val();
  
  var total = Number(value) * Number(valutakoers);
  $('#rente_Bedrag').val(Math.round(total * 100) / 100);
  
  setTotalCalculation();
};


$('#Transactietype').on('change', function () {setDescription(); setTotalCalculation();});

/**
 * Bij een aankoop is het: mutatiebedrag (is negatief) - kosten - kosten buitenland - opgelopen rente
 * Bij een verkoop is het: mutatiebedrag (is positief) - kosten - kosten buitenland + opgelopen rente
 * @returns {undefined}
 */
function setTotalCalculation() {
  var bedrag = $('#total').val();
  var rente_Bedrag = $('#rente_Bedrag').val();  
 
  if ( rente_Bedrag == 'undefined' ) {
    var rente_Bedrag = 0;
  }
  var total = Math.abs(Number(bedrag)) -  Number(rente_Bedrag);
  
  var transactieType = $('#Transactietype').val();
  
//  if ( $.inArray(transactieType, ['L', 'V/O', 'V/S']) != -1 ) {
//    var total = Math.abs(Number(bedrag))* -1 +  Number(rente_Bedrag);
//  }
  
  if ( $.inArray(transactieType, ['L', 'V/O', 'V/S']) != -1 ) {
    var total = Math.abs(Number(bedrag)) + Number(rente_Bedrag);
  } else if ( $.inArray(transactieType, ['D', 'A/O', 'A/S']) != -1 ) {
    var total = -Math.abs(Number(bedrag)) - Number(rente_Bedrag);
  }
  
  $('#totalMutation').html('<strong>Afschriftbedrag: ' + Math.round(total * 100) / 100 + '</strong>');
}


/** start valutakoers inverse **/
$( "#Valutakoers" ).keydown(function(e) {
  if ( e.keyCode == 120 ) {
    var inputValue = $(this).val();
    $(this).val(1/inputValue);
    $('#Valutakoers').trigger("change");
  }
});

$('.inverseField').on('click', function(){
  var inputField = $(this).parent().parent().find('input');
  var inputValue = inputField.val();
  inputField.val(1/inputValue);
  $('#Valutakoers').trigger("change");
});

$( "#rente_Valutakoers" ).keydown(function(e) {
  if ( e.keyCode == 120 ) {
    var inputValue = $(this).val();
    $(this).val(1/inputValue);
    $('#' + $(this).attr('id')).trigger("change");
  }
});
/** end  valutakoers inverse **/


/**
 * rente berekenen
 */
function getRente()
{
  $('#rentePerLabel').html('');
  $('#rentePerField').html('');

  if ( $("#Aantal").val() !== '' &&  $('#fonds_fonds').val() !== '' && $("#fondssoort").val() === 'OBL') {
    $.ajax({
      type: 'GET',
      url: 'lookups/getFondsRente.php',
      dataType: 'json',
      async: false,
      data: {
        aantal: $("#Aantal").val(),
        fonds: $('#fonds_fonds').val(),
        datum: $('[name=Boekdatum]').val()
      },
      success: function(data, textStatus, jqXHR) {
        if ( data ) {
          if ( data.settlementDatum != null && typeof data.settlementDatum !== "undefined" && data.settlementDatum != "" )
          {
            $('#rentePerLabel').html('<strong><u>Indicatie</u> opgelopen rente per: ' + $.datepicker.formatDate('dd-mm-yy', new Date(data.settlementDatum)) + '</strong>');
          }
    
          if ( typeof data.rentebedrag !== "undefined" && data.rentebedrag != "" )
          {
            $('#rentePerField').html(data.rentebedrag);
          }
        }
      },
      error: function(jqXHR, textStatus, errorThrown) {

      }
    });
  }
}