var getUrl = 'lookups/rekeningAfschriften.php';

ajaxSubmit('submit-form', 'beginboeking-form');

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
  
  
});
  
  $('#recalculate').on('change', function () {
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
        var boekDate = $.datepicker.formatDate('yy-mm-dd', new Date(boekDatum))
        //
       // var valutaDate = new Date(data.valuta.Datum);
        var valutaDate = $.datepicker.formatDate('yy-mm-dd', new Date(data.valuta.Datum))

        var boekDate = new Date(boekDatum);
        //set date info on valuta fields
        deleteValutaDateInfo('valuta-koers-info');
        if( $.inArray($('#Valuta').val(), valutaIgnore) == -1 ) {
          setValutaDateInfo('valuta-koers-info', boekDate, valutaDate);
        }
        mutatieSum();

      },
      error: function(jqXHR, textStatus, errorThrown) {
        result = jqXHR;
      }
    });
  });

  //waarde change
  $('#Fonds').on('change', function() {waardesum();});
  $('#Aantal').on('change', function() {waardesum();});
  $('#Fondskoers').on('change', function() {waardesum()});
  $('#fondseenheid').on('change', function() {waardesum()});

  //mutatiebedrag
  $('#value_input').on('change', function() {mutatieSum()});
  $('#Valutakoers').on('change', function() {mutatieSum()});

  $('#Transactietype').val('');

  $("#Fonds").attr("disabled", "disabled");
  $("#Fondskoers").attr("disabled", "disabled");
  $("#Fondskoers").val(0);
  $("#Aantal").attr("disabled", "disabled");
  $("#Aantal").val(0);
  $("#Bewaarder").attr("disabled", "disabled");

  $('#Grootboekrekening').change(function() {
    if ( $('#Grootboekrekening').val() == 'FONDS' ) {
      $('#value_input').inputmask('remove');
      $('#value_input').inputmask("maskNumeric6Digits");
      
      $('#Transactietype').val('B');

      $("#Fonds").removeAttr("disabled");
      $("#Fondskoers").removeAttr("disabled");
      $("#Aantal").removeAttr("disabled");
      $("#Bewaarder").removeAttr("disabled");

    } else {
      $('#Transactietype').val('');
      
      //Verm mag waarde nagatief
      $('#value_input').inputmask('remove');
      $('#value_input').inputmask("maskRekeningMutatieAantalEdit");

      $("#Fonds").attr("disabled", "disabled");
      $("#Fondskoers").attr("disabled", "disabled");
      $("#Fondskoers").val(0);
      $("#Aantal").attr("disabled", "disabled");
      $("#Aantal").val(0);
      $("#Bewaarder").attr("disabled", "disabled");
      
      $('#Fonds').val('');
//      $('#value_input').val('');
      $('#Bewaarder').val('');
//      $('#total').val('');
      $('#Omschrijving').val('Inbreng');
      
      $('#fonds-info').html('');
      $('#fondsOwnedInfo').html('');
      $('#fonds-koers-info').html('');

    }
  
  
  setTotalCalculation();
 


  /** set valuta koers **/
  $("#Valuta").trigger("change");

  
  
});

function waardesum () {
  var aantal = $('#Aantal').val();
  var fondsKoers = $('#Fondskoers').val();
  var fondsEenheid = $('#fondseenheid').val();
  var total = Number(aantal) * Number(fondsKoers) * Number(fondsEenheid);
  
  $('#value_input').val(Math.round(total * 100) / 100);
  mutatieSum();
};



function mutatieSum () {
  var value = $('#value_input').val();
  
  if ( $('input[name=RekeningValuta]').val() == 'EUR' && $('#Valuta').val() != 'EUR' ) {
    var valutakoers = $('#Valutakoers').val();
    var total = Number(value) * Number(valutakoers);
  } else {
    var total = Number(value);
  }
  $('#total').val(Math.round(total * 100) / 100);
  setTotalCalculation();
};


function setTotalCalculation() {
  var total = $('#total').val();
  $('#totalMutation').html('<strong>Afschriftbedrag ' + Math.round(total * 100) / 100 + '</strong>');
}

