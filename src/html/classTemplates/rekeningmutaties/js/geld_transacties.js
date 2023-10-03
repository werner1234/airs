var getUrl = 'lookups/rekeningAfschriften.php';

ajaxSubmit('submit-form', 'geldtransacties-form');

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

$(function() {
  valutaState();
  
  $('#recalculate').on('click', function () {
    $('#Valuta').trigger('change');
  });
  
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
    },
    error: function(jqXHR, textStatus, errorThrown) {
      result = jqXHR;
    }
  });

  $('#Valutakoers').on('change', function () {
    manuelValutaDateInfo('valuta-koers-info');
    mutationsum();
  });


  $('#value').on('change', function() {
    checkIfDebitOrCredit();
    mutationsum();
  });
  $('#Grootboekrekening').on('change', function() {
    checkIfDebitOrCredit();
    valutaState();
  });
  
  
  /** when we change the valuta**/
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

        $('#Valutakoers').val(Number(data.valuta.Koers));
        $('#RekeningValutakoers').val(data.rekeningValuta.Koers);
        var boekDatum = $('[name=Boekdatum]').val().split('-').reverse().join('-');
        var boekDate = new Date(boekDatum);
        var valutaDate = new Date(data.valuta.Datum);

        //set date info on valuta fields
        deleteValutaDateInfo('valuta-koers-info');
        if( $.inArray($('#Valuta').val(), valutaIgnore) == -1 ) {
          setValutaDateInfo('valuta-koers-info', boekDate, valutaDate);
        }
        mutationsum();
      },
      error: function(jqXHR, textStatus, errorThrown) {
        result = jqXHR;
      }
    });
  });



  
  $('#Grootboekrekening').on('change', function() {
    if ($.inArray($('#Grootboekrekening').val(), ['STORT', 'VKSTO']) !== -1) {
      $('#value').inputmask('remove');
      $('#value').inputmask("maskValuta2digitsPositive");
    } else {
      $('#value').inputmask('remove');
      $('#value').inputmask("maskValuta2digitsNegative");
    }
  });

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
  /** end  valutakoers inverse **/

  
});




function valutaState() {
  if ( $('#Grootboekrekening').val() == 'Kruis') {
    $('#Valuta').readonly(false);
  } else {
    $('#Valuta').val($('*[name=RekeningValuta]').val());
    $('#Valuta').change();
    $('#Valuta').readonly(true);
  }
}


function mutationsum() {
  var waarde = $('#value').val();
  
  if ( $('input[name=RekeningValuta]').val() == 'EUR' && $('#Valuta').val() != 'EUR' ) {
    var Valutakoers = $('#Valutakoers').val();
    var total = Number(waarde) * Number(Valutakoers);
  } else {
    var total = Number(waarde);
  }
  $('#total').val(Math.round(total * 100) / 100);
};

  

function checkIfDebitOrCredit() {
  if ($.inArray($('#Grootboekrekening').val(), ['ONTTR']) !== -1 && $('#value').val() < 0) {
    $('.testtest').attr('title', 'Grootboek: bedrag debet wegschrijven (invoer credit) !').bstooltip('show');
    setTimeout(function() {
      $('.testtest').bstooltip('destroy');
    }, 6000);
  }
}