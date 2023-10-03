var getUrl = 'lookups/rekeningAfschriften.php';

ajaxSubmit('submit-form', 'overige-form');

setFondsenStatus();

$('#recalculate').on('click', function () {
  if ( $('#Fonds').val().length > 0 ) {
    fondsChanged('Fonds');
  }
  $('#Valuta').trigger('change');
});

$('#Grootboekrekening').on('change', function () {
  setFondsenStatus();

  if ( $('#Fonds').val() != '' ) {
    fondsChanged('Fonds');
  }
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

function setFondsenStatus() {
 
  if( $('#Grootboekrekening').val() == 'FONDS' ) {
    $("#Transactietype").removeAttr("disabled");
    $("#Fonds").removeAttr("disabled");
    $("#Fondskoers").removeAttr("disabled");
    $("#Aantal").removeAttr("disabled");
    $("#Bewaarder").removeAttr("disabled");
	} else {
    $('#fonds-koers-info').removeClass();
    $('#fonds-koers-info').html('');

    $("#Transactietype").attr("disabled", "disabled");
    $("#Fonds").attr("disabled", "disabled");
    $("#Fondskoers").attr("disabled", "disabled");
    $("#Fondskoers").val(0);
    $("#Aantal").attr("disabled", "disabled");
    $("#Aantal").val(0);
    $("#Bewaarder").attr("disabled", "disabled");
    
    $('.mutationUnknown').html('');
    $('.mutationUnknownField').val(0);

//    $("#Fonds").val('');
//    $("#Fonds").html('');
    
    if ( $('#Grootboekrekening').val() ) {
      var	GrootboekFondsGebruik = ledgerFondsUse($('#Grootboekrekening').val());
      if(GrootboekFondsGebruik == 1) {
        $("#Fonds").removeAttr("disabled");
      } else {
        $('.mutationUnknown').html('');
        $('.mutationUnknownField').val(0);
      
        $("#Fonds").val('');
        $("#Fonds").html('');
        $("#Fonds").attr("disabled", "disabled");
      }
    }
  }
}

function ledgerFondsUse (grootboek) {
  var returnValue = $.ajax({
    type: 'GET',
    url: getUrl,
    dataType: 'json',
    async: false,
    data: {
      type: 'ledgerFondsUse',
      grootboek: grootboek,
    }
  });
  var returnString = $.parseJSON(returnValue.responseText)
  return returnString.FondsGebruik;
}

$('#Fonds, #Transactietype, #Aantal').on('change', function() {
  checkShortPositions("Fonds");
  setDebetCredet();
});

$('#Fondskoers, #Valutakoers').on('change', function() {
  setDebetCredet();
});

function setDebetCredet() {
  var calculatedValue = 0;
  var grootboek = $('#Grootboekrekening').val();
  var aantal = $('#Aantal').val();
  var fondskoers = $('#Fondskoers').val();
  var Transactietype = $('#Transactietype').val();
  var Fondskoerseenheid = $('input[name="Fondskoerseenheid"]').val();

  if ( fondskoers == '' ) {
    fondskoers = 0;
  }

  $('.mutationUnknown').html('');

  if(grootboek == 'FONDS') {
    if( aantal !== '' && fondskoers !== '') {
  
      if( $.inArray(Transactietype, ['V', 'V/S', 'L', 'V/O', 'V/S']) != -1 ) {
        if (aantal > 0) {
          var aantal = -1*Math.abs(aantal);
          $('#Aantal').val(aantal);
        }

        var calculatedValue = aantal * fondskoers * Fondskoerseenheid;
        var debetcreditVal = Math.abs(calculatedValue);
        
        if ( $('input[name="RekeningValuta"]').val() === 'EUR' && $('#Valuta').val() != 'EUR' ) {
          var calculatedValue = calculatedValue * $('#Valutakoers').val();
        }
        
        $('input[name="Bedrag"]').val(Math.abs(parseFloat(calculatedValue).toFixed(2)));
        $('#Credit').val(Math.abs(debetcreditVal));
        $('#Debet').val('');
      } else if( $.inArray(Transactietype, ['A', 'A/O', 'A/S', 'D', 'B']) != -1 ) {
        var aantal = Math.abs(aantal);
        $('#Aantal').val(aantal);
        var calculatedValue = aantal * fondskoers * Fondskoerseenheid;
        var debetcreditVal = -1*Math.abs(calculatedValue);
        
        if ( $('input[name="RekeningValuta"]').val() === 'EUR' && $('#Valuta').val() != 'EUR' ) {
          var calculatedValue = calculatedValue * $('#Valutakoers').val();
        }
        
        $('input[name="Bedrag"]').val(-Math.abs(parseFloat(calculatedValue).toFixed(2)));
        $('#Debet').val(Math.abs(debetcreditVal));
        $('#Credit').val('');
      } else {
        var calculatedValue = aantal * fondskoers * Fondskoerseenheid;
        var debetcreditVal = calculatedValue;
        if ( $('input[name="RekeningValuta"]').val() === 'EUR' && $('#Valuta').val() != 'EUR' ) {
          var calculatedValue = calculatedValue * $('#Valutakoers').val();
        }
        $('#Debet').val('');
        $('#Credit').val('');
        $('input[name="Bedrag"]').val('');
        
        if ( debetcreditVal > 0 ) {
          $('#Debet').val(0);
          $('#Credit').val(debetcreditVal);
        } else {
          $('#Debet').val(debetcreditVal);
          $('#Credit').val(0);
        }
        
      }

      $('.mutationUnknown').html('Berekend Bedrag: ' + parseFloat(debetcreditVal).toFixed(2));
      $('.mutationUnknownField').val(parseFloat(debetcreditVal).toFixed(2));
    }
  }
  mutationsum();
}


$('#Debet, #Credit, #Fonds, #Valutakoers').on('change', function() {
	if(	$(this).attr('id') == 'Debet' && $(this).val() != '0' && $(this).val() != '' ) {
      $('#Credit').val('');
      mutationsum();
	} else if($(this).attr('id') == 'Credit' && $(this).val() != '0' && $(this).val() != '' ) {
      $('#Debet').val('');
      mutationsum();
	}
});


$('#Debet, #Credit').keydown(function(e) {
  if ( e.keyCode == 120 ) {
    var tempValue = $('.mutationUnknownField').val();
    
    if ( tempValue != 0 && tempValue != '' ) {
      if(	$(this).attr('id') == 'Debet' ) {
        $('#Credit').val('');
        $(this).val(tempValue);
      } else if($(this).attr('id') == 'Credit' ) {
        $('#Debet').val('');
        $(this).val(tempValue);
      }
    } else {
      $(this).val(0);
    }
    mutationsum();
  }
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

      deleteValutaDateInfo('valuta-koers-info');
      if( $.inArray($('#Valuta').val(), valutaIgnore) == -1 ) {
        setValutaDateInfo('valuta-koers-info', boekDate, valutaDate);
      }
      setDebetCredet();
      mutationsum();
    },
    error: function(jqXHR, textStatus, errorThrown) {
      result = jqXHR;
    }
  });
});


function mutationsum() {
  var mutatie = 0;
  if ( $('#Credit').val() > 0 ) {
    var mutatie = $('#Credit').val();
  } else if ( $('#Debet').val() > 0 ) {
    var mutatie = -1 * $('#Debet').val();
  }
  
  if ( $('input[name=RekeningValuta]').val() == 'EUR' && $('#Valuta').val() != 'EUR' ) {
    var Valutakoers = $('#Valutakoers').val();
    var total = Number(mutatie) * Number(Valutakoers);
  } else {
    var total = Number(mutatie);
  }
  $('input[name=Bedrag]').val(Math.round(total * 100) / 100);
};


$('#Valutakoers').change(function() {
  manuelValutaDateInfo('valuta-koers-info');
});

/** start valutakoers inverse **/
$( "#Valutakoers" ).keydown(function(e) {
  if ( e.keyCode == 120 ) {
    var inputValue = $(this).val();
    $( "#Valutakoers" ).val(1/inputValue);
    $('#Valutakoers').trigger("change");
    mutationsum();
  }
});

$('.inverseField').on('click', function(){
  var inputValue = $( "#Valutakoers" ).val();
  $( "#Valutakoers" ).val(1/inputValue);
  $('#Valutakoers').trigger("change");
  mutationsum();
});
/** end  valutakoers inverse **/


$("#Valuta").trigger("change");