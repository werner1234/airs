var getUrl = 'lookups/rekeningAfschriften.php';

ajaxSubmit('submit-form', 'kostenboeking-form');

$('#Valuta').on('change', function () {
  changeValuta();
});
changeValuta();

$('#recalculate').on('click', function () {
  $('#Valuta').trigger('change');
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

function changeValuta() {
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

      //remove info field
      //deleteValutaDateInfo('valuta-koers-info');
      //setValutaDateInfo('valuta-koers-info', boekDate, valutaDate);
      deleteValutaDateInfo('valuta-koers-info');
      if( $.inArray($('#Valuta').val(), valutaIgnore) == -1 ) {
        setValutaDateInfo('valuta-koers-info', boekDate, valutaDate);
      }
      //recalculate totals
      mutationsum();
    },
    error: function(jqXHR, textStatus, errorThrown) {
      result = jqXHR;
    }
  });
}


$('#value').on('change', function() {mutationsum();});
$('#Valutakoers').on('change', function() {mutationsum();});

function mutationsum() {
  var waarde = $('#value').val();
  
  if ( $('input[name=RekeningValuta]').val() == 'EUR' && $('#Valuta').val() != 'EUR' ) {
    var Valutakoers = $('#Valutakoers').val();
    var total = Number(waarde) * Number(Valutakoers);
  } else {
    var total = Number(waarde);
  }
  $('#Bedrag').val(Math.round(total * 100) / 100);
};

/** inverse valutakoers **/
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
/** end inverse valutakoers **/