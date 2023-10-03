var getUrl = 'lookups/rekeningAfschriften.php';

ajaxSubmit('submit-form', 'aanverkoop-form');

function valutaChange (fieldId, type, koers) {
//  $('#' + fieldId).change(function() {
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

      $('#' + koers).val(Number(data.valuta.Koers));
      var boekDatum = $('[name=Boekdatum]').val().split('-').reverse().join('-');
      var boekDate = new Date(boekDatum);
      var valutaDate = new Date(data.valuta.Datum);

      //set date info on valuta fields

      deleteValutaDateInfo(type + '-valuta-koers-info');
      if( $.inArray($('#' + fieldId).val(), valutaIgnore) == -1 ) {
        setValutaDateInfo(type + '-valuta-koers-info', boekDate, valutaDate);
      }

    },
    error: function(jqXHR, textStatus, errorThrown) {
      result = jqXHR;
    }
  });
//});
}


$(function() {
  
  $('#recalculate').on('click', function () {
    if ( $('#Fonds').val().length > 0 ) {
      fondsChanged('Fonds');
    }
    $('#Valuta').trigger('change');
  });


  $('#Valuta').change(function() {
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

        $('#Valutakoers').val(Number(data.valuta.Koers));
        $('#RekeningValutakoers').val(data.rekeningValuta.Koers);
        var boekDatum = $('[name=Boekdatum]').val().split('-').reverse().join('-');
        var boekDate = new Date(boekDatum);
        var valutaDate = new Date(data.valuta.Datum);

        //set date info on valuta fields

        deleteValutaDateInfo('valuta-koers-info');
        deleteValutaDateInfo('kosten-valuta-koers-info');
        deleteValutaDateInfo('kosten1-valuta-koers-info');
        deleteValutaDateInfo('kostenBuitenland-valuta-koers-info');
        deleteValutaDateInfo('rente-valuta-koers-info');
        if( $.inArray($('#Valuta').val(), valutaIgnore) == -1 ) {
          setValutaDateInfo('valuta-koers-info', boekDate, valutaDate);
          setValutaDateInfo('kosten-valuta-koers-info', boekDate, valutaDate);
          setValutaDateInfo('kosten1-valuta-koers-info', boekDate, valutaDate);
          setValutaDateInfo('kostenBuitenland-valuta-koers-info', boekDate, valutaDate);
          setValutaDateInfo('rente-valuta-koers-info', boekDate, valutaDate);
        }

        //set valuta to sub fields
        $('#Kosten_Valuta').val(data.valuta.Valuta);
        $('#Kosten1_Valuta').val(data.valuta.Valuta);
        $('#kostenBuitenland_Valuta').val(data.valuta.Valuta);
        $('#rente_Valuta').val(data.valuta.Valuta);

        //set valutakoers to sub fields
        $('#Kosten_Valutakoers').val(Number(data.valuta.Koers));
        $('#Kosten1_Valutakoers').val(Number(data.valuta.Koers));
        $('#kostenBuitenland_Valutakoers').val(Number(data.valuta.Koers));
        $('#rente_Valutakoers').val(Number(data.valuta.Koers));

        mutatieSum();
        
        kostenSum();
        kosten1Sum();
        kostenBuitenlandSum();
        renteSum();
      },
      error: function(jqXHR, textStatus, errorThrown) {
        result = jqXHR;
      }
    });
  });


  $('#Valutakoers').change(function() {
    manuelValutaDateInfo('valuta-koers-info')

    var currentValuta = $('#Valuta').val();
    var currentValutaKoers = $('#Valutakoers').val();
    /** check other valuta lines **/
    if ( $('#Kosten_Valuta').val() === currentValuta ) {
      $('#Kosten_Valutakoers').val(currentValutaKoers);
      $('#Kosten_Valutakoers').change();
      manuelValutaDateInfo('kosten-valuta-koers-info')
    }
    if ( $('#Kosten1_Valuta').val() === currentValuta ) {
      $('#Kosten1_Valutakoers').val(currentValutaKoers);
      $('#Kosten1_Valutakoers').change();
      manuelValutaDateInfo('kosten1-valuta-koers-info')
    }
    if ( $('#kostenBuitenland_Valuta').val() === currentValuta ) {
      $('#kostenBuitenland_Valutakoers').val(currentValutaKoers);
      $('#kostenBuitenland_Valutakoers').change();
      manuelValutaDateInfo('kostenBuitenland-valuta-koers-info');
    }
    if ( $('#rente_Valuta').val() === currentValuta ) {
      $('#rente_Valutakoers').val(currentValutaKoers);
      $('#rente_Valutakoers').change();
      manuelValutaDateInfo('rente-valuta-koers-info');
    }    
  });

  $('#Fondskoers').on('change', function() {manuelValutaDateInfo('fonds-koers-info')});


  //waarde change
  $('#Fonds').on('change', function() {waardesum();});
  $('#Aantal').on('change', function() {waardesum(); setDescription (); checkShortPositions("Fonds"); getRente();});
  $('#Fondskoers').on('change', function() {waardesum();});
  $('#fondseenheid').on('change', function() {waardesum()});


  //mutatiebedrag
  $('#value-input').on('change', function() {mutatieSum()});
  $('#Valutakoers').on('change', function() {mutatieSum()});

  //kosten sum
  $('#Kosten_Input').on('change', function() {kostenSum()});
  $('#Kosten_Valutakoers').on('change', function() {kostenSum()});
  $('#Kosten_Valutakoers').on('change', function() {manuelValutaDateInfo('kosten-valuta-koers-info')});

  $('#Kosten_Valuta').on('change', function() {
    $.when(valutaChange('Kosten_Valuta', 'kosten', 'Kosten_Valutakoers')).done(function(a1){
      kostenSum ();
    });
  });

  //kosten1 sum
  $('#Kosten1_Input').on('change', function() {kosten1Sum()});
  $('#Kosten1_Valutakoers').on('change', function() {kosten1Sum()});
  $('#Kosten1_Valutakoers').on('change', function() {manuelValutaDateInfo('kosten1-valuta-koers-info')});

  $('#Kosten1_Valuta').on('change', function() {
    $.when(valutaChange('Kosten1_Valuta', 'kosten1', 'Kosten1_Valutakoers')).done(function(a1){
      kosten1Sum ();
    });
  });


  //kostenBuitenland sum
  $('#kostenBuitenland_Input').on('change', function() {kostenBuitenlandSum()});
  $('#kostenBuitenland_Valutakoers').on('change', function() {kostenBuitenlandSum()});
  $('#kostenBuitenland_Valutakoers').on('change', function() {manuelValutaDateInfo('kostenBuitenland-valuta-koers-info')});

  $('#kostenBuitenland_Valuta').on('change', function() {
    $.when(valutaChange('kostenBuitenland_Valuta', 'kostenBuitenland', 'kostenBuitenland_Valutakoers')).done(function(a1){
      kostenBuitenlandSum ();
    });
  });


  //rente sum
  $('#rente_Input').on('change', function() {renteSum()});
  $('#rente_Valutakoers').on('change', function() {renteSum()});
  $('#rente_Valutakoers').on('change', function() {manuelValutaDateInfo('rente-valuta-koers-info')});

  $('#rente_Valuta').on('change', function() {
    $.when(valutaChange('rente_Valuta', 'rente', 'rente_Valutakoers')).done(function(a1){
      renteSum ();
    });
  });

});


function waardesum () {
  var aantal = $('#Aantal').val();
  var fondsKoers = $('#Fondskoers').val();
  var fondsEenheid = $('#fondseenheid').val();
  var total = Number(aantal) * Number(fondsKoers) * Number(fondsEenheid);
   
  $('#value-input').val(Math.round(total * 100) / 100);
  mutatieSum();
};

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


function mutatieSum () {
  var value = $('#value-input').val();
  
  if ( $('input[name=RekeningValuta]').val() == 'EUR' && $('#Valuta').val() != 'EUR' ) {
    var valutakoers = $('#Valutakoers').val();
    var total = Number(value) * Number(valutakoers);
  } else {
    var total = Number(value);
  }
  
    
  if ( $('input[name=RekeningValuta]').val() != 'EUR' && $('input[name=RekeningValuta]').val() != $('#Valuta').val() ) {
    $exchangeRate = '';
    getExchangeRates($('input[name=RekeningValuta]').val(), $('#Valuta').val());
    var total = total * $exchangeRate.valuta.Koers / $exchangeRate.rekeningValuta.Koers;
  }
  
  $('#total').val(Math.round(total * 100) / 100);
  
  setTotalCalculation();
};


function kostenSum () {
  var value = $('#Kosten_Input').val();
  if ( $('input[name=RekeningValuta]').val() == 'EUR' && $('#Kosten_Valuta').val() != 'EUR' ) {
    var valutakoers = $('#Kosten_Valutakoers').val();
    var total = Number(value) * Number(valutakoers);
  } else {
    var total = Number(value);
  }
  
  if ( $('input[name=RekeningValuta]').val() != 'EUR' && $('input[name=RekeningValuta]').val() != $('#Kosten_Valuta').val() ) {
    $exchangeRate = '';
    getExchangeRates($('input[name=RekeningValuta]').val(), $('#Kosten_Valuta').val());
    var total = total * $exchangeRate.valuta.Koers / $exchangeRate.rekeningValuta.Koers;
  }
  
  $('#Kosten_Bedrag').val(Math.round(total * 100) / 100);
  
  setTotalCalculation();
};

/**
 * Kosten 1 optelling
 */
function kosten1Sum () {
  var value = $('#Kosten1_Input').val();

  if ( $('input[name=RekeningValuta]').val() == 'EUR' && $('#Kosten1_Valuta').val() != 'EUR' ) {
    var valutakoers = $('#Kosten1_Valutakoers').val();
    var total = Number(value) * Number(valutakoers);
  } else {
    var total = Number(value);
  }

  if ( $('input[name=RekeningValuta]').val() != 'EUR' && $('input[name=RekeningValuta]').val() != $('#Kosten1_Valuta').val() ) {
    $exchangeRate = '';
    getExchangeRates($('input[name=RekeningValuta]').val(), $('#Kosten1_Valuta').val());
    var total = total * $exchangeRate.valuta.Koers / $exchangeRate.rekeningValuta.Koers;
  }

  $('#Kosten1_Bedrag').val(Math.round(total * 100) / 100);

  setTotalCalculation();
};



function kostenBuitenlandSum () {
  var value = $('#kostenBuitenland_Input').val();
  
  if ( $('input[name=RekeningValuta]').val() == 'EUR' && $('#kostenBuitenland_Valuta').val() != 'EUR' ) {
    var valutakoers = $('#kostenBuitenland_Valutakoers').val();
    var total = Number(value) * Number(valutakoers);
  } else {
    var total = Number(value);
  }
  
  $('#kostenBuitenland_Bedrag').val(Math.round(total * 100) / 100);
  
  setTotalCalculation();
};


function renteSum () {
  var value = $('#rente_Input').val();
  
  if ( $('input[name=RekeningValuta]').val() == 'EUR' && $('#rente_Valuta').val() != 'EUR' ) {
    var valutakoers = $('#rente_Valutakoers').val();
    var total = Number(value) * Number(valutakoers);
  } else {
    var total = Number(value);
  }
  
  $('#rente_Bedrag').val(Math.round(total * 100) / 100);
  
  setTotalCalculation();
};


$('#Transactietype').change(function() {setDescription(); setTotalCalculation(); checkShortPositions("Fonds");});


function setDescription () {
  var transactieType = $('#Transactietype').val();
  var transactieType = transactieType.charAt(0);
  
  var av = 'Aan/verkoop';
  if ( transactieType == 'V') {
    var av = 'Verkoop';
  } else if ( transactieType == 'A') {
    var av = 'Aankoop';
  }
  var fondsOmschrijving = av + ' ' + $('#fonds_omschrijving').val();
  
  /** wanneer omschrijving groter/gelijk is dan 50 **/
  if(fondsOmschrijving.length >= 50) {
    var fondsOmschrijving = av + ' ' + $('#fonds_fonds').val();
  }
  var fondsOmschrijving = fondsOmschrijving.substring(0,50);

  $('#Omschrijving').val(fondsOmschrijving);
}


/**
 * Bij een aankoop is het: mutatiebedrag (is negatief) - kosten - kosten buitenland - opgelopen rente
 * Bij een verkoop is het: mutatiebedrag (is positief) - kosten - kosten buitenland + opgelopen rente
 * @returns {undefined}
 */
function setTotalCalculation() {
  var bedrag = $('#total').val();
  var kostenBuitenland_Bedrag = $('#kostenBuitenland_Bedrag').val();
  var kosten_bedrag = $('#Kosten_Bedrag').val();
  var kosten1_bedrag = $('#Kosten1_Bedrag').val();
  var rente_Bedrag = $('#rente_Bedrag').val();  
  
  var transactieType = $('#Transactietype').val();
  var transactieType = transactieType.charAt(0);
  
  if ( transactieType == 'V') {
    var total = Math.abs(Number(bedrag)) - Math.abs(Number(kostenBuitenland_Bedrag)) - Math.abs(Number(kosten_bedrag)) - kosten1_bedrag + Number(rente_Bedrag);
  } else if ( transactieType == 'A') {
    var total = -Math.abs(Number(bedrag)) - Math.abs(Number(kostenBuitenland_Bedrag)) - Math.abs(Number(kosten_bedrag)) - kosten1_bedrag - Number(rente_Bedrag);
  }
  
  $('#totalMutation').html('<strong>Afschriftbedrag: ' + Math.round(total * 100) / 100 + '</strong>');
}

//$(document).keydown(function (e) {
//    if (e.keyCode == 120) {
//        console.log($(this));
//    }
//});

$( "#Valutakoers" ).keydown(function(e) {
  if ( e.keyCode == 120 ) {
    var inputValue = $(this).val();
    $(this).val(1/inputValue);
    $('#Valutakoers').trigger("change");
  }
});

$('.inverseField').on('click', function() {
  var inputField = $(this).parent().parent().find('input');
  var inputValue = inputField.val();
  inputField.val(1/inputValue);
  $('#Valutakoers').trigger("change");
});

$( "#Kosten_Valutakoers, #Kosten1_Valutakoers, #kostenBuitenland_Valutakoers, #rente_Valutakoers" ).keydown(function(e) {
  if ( e.keyCode == 120 ) {
    var inputValue = $(this).val();
    $(this).val(1/inputValue);
    $('#' + $(this).attr('id')).trigger("change");
  }
});


/** set valuta koers **/
$("#Valuta").trigger("change");


$('#Fondskoers').data('title', 'Fonds koers berekenen');
$('#Fondskoers').data('placement', 'auto');
$('#Fondskoers').data('html', true);
$('#Fondskoers').data('trigger', 'manual');

$('#calculatorAantal').val('0');
$('#calculatorWaarde').val('0');
$('#calculatorTotal').val('0');

//  dollor 1800 styk 1800 waarde 
  
//  dolar ten laste euro 0.1 

$(document).on("change", "#calculatorWaarde, #calculatorValutaKoers, #calculatorAantal", function() {
  if ( $('input[name=RekeningValuta]').val() == $('#Valuta').val() && $('#Valuta').val() != 'EUR' ) {
    $("#calculatorTotal").val($("#calculatorWaarde").val() / $("#calculatorAantal").val());
  } else {
    $("#calculatorTotal").val($("#calculatorWaarde").val() / $("#calculatorValutaKoers").val() / $("#calculatorAantal").val());
  }
});
$(document).on('click', '#calculatorSetKoers', function () {
  $('#Fondskoers').val($("#calculatorTotal").val());
  $('#Fondskoers').trigger("change");
  createFondsPopover('hide');
  fondsCalculatorShow = false;
});


$('#Fondskoers').popover({
	title: function(){
		return $(this).data('title')+'<span style="float: right; cursor: pointer;" class="close">&times;</span>';
	},
  content: function () {
  return $("#aanVerkoopFondsKoersCalculator");
}}).on('hide.bs.popover', function () {
  $("#myPopoverContentContainer").html($("#aanVerkoopFondsKoersCalculator"));
}).on('shown.bs.popover', function(e){
	var popover = $(this);
	$(this).parent().find('div.popover .close').on('click', function(e){
		createFondsPopover('hide');
    fondsCalculatorShow = false;
    $('#Fondskoers').focus();
	});
});

$(document).click(function(e) {
  if(e.target.id=="popovercloseid" ) {
    createFondsPopover('hide');
    fondsCalculatorShow = false;     
  }
});



function createFondsPopover (type) {
  if ( type == undefined ) {type = 'show';}
  if ( type == 'show' ) {
    $('#calculatorValutaKoers').val($('#Valutakoers').val());
    $('#calculatorAantal').val($('#Aantal').val());
  } else {
    $('#Fondskoers').focus();
  }
  if ( $('input[name=RekeningValuta]').val() == $('#Valuta').val() && $('#Valuta').val() != 'EUR' ) {
    $("#calculatorTotal").val($("#calculatorWaarde").val() / $("#calculatorAantal").val());
  } else {
    $("#calculatorTotal").val($("#calculatorWaarde").val() / $("#calculatorValutaKoers").val() / $("#calculatorAantal").val());
  }
  
  $('#Fondskoers').popover(type);
}

$('#calculatorValutaKoers').on('change', function () {
  $('#Valutakoers').val($('#calculatorValutaKoers').val());
  $('#Valutakoers').trigger('change');
  mutatieSum();
});

var fondsCalculatorShow = false;
$( "#Fondskoers" ).keydown(function(e) {
  if ( e.keyCode == 120 ) {
    if ( fondsCalculatorShow == false ) {
      createFondsPopover('show');
      fondsCalculatorShow = true
    } else {
      $('#Fondskoers').val($("#calculatorTotal").val());
      createFondsPopover('hide');
      fondsCalculatorShow = false;
    }
  }
});

$('#fondsCalculatorShow').on('click', function() {
  if ( fondsCalculatorShow == false ) {
    createFondsPopover('show');
    fondsCalculatorShow = true;
    $('#Fondskoers').focus();
  } else {
    createFondsPopover('hide');
    fondsCalculatorShow = false;
    $('#Fondskoers').focus();
  }
});

//$(document).on('keydown', function(e) {
//  clog(fondsCalculatorShow);
//  if ( fondsCalculatorShow == true && e.keyCode == 120 ) {
//    if ( $("#calculatorTotal").val() > 0 ) {
//      $('#Fondskoers').val($("#calculatorTotal").val());
//    }
////    if ( fondsCalculatorShow == true )
//    $('#Fondskoers').popover('hide');
//    fondsCalculatorShow = false;
//    $('#Fondskoers').focus();
//  }
//});



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


function getExchangeRates (rekeningValuta, valuta) {
  $.ajax({
    type: 'GET',
    url: getUrl,
    dataType: 'json',
    async: false,
    data: {
      type: 'getExchangeRate',
      rekeningValuta: rekeningValuta,
      valuta: valuta,
      date: $('[name=Boekdatum]').val()
    },
    success: function(data, textStatus, jqXHR) {
      $exchangeRate = data;
    },
    error: function(jqXHR, textStatus, errorThrown) {
      result = jqXHR;
    }
  });
}