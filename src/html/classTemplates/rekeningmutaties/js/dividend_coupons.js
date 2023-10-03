//ajax submit form
//var getUrl = 'lookups/rekeningAfschriften.php';

ajaxSubmit('submit-form', 'dividend-form');

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

  var dividendCalculatorShow = false;
  $('#value').data('title', 'Dividend waarde berekenen');
  $('#value').data('placement', 'auto');
  $('#value').data('html', true);
  $('#value').data('trigger', 'manual');

  $('#calculatorAantal').val('0');
  $('#calculatorPerStuk').val('0');

//  dollor 1800 styk 1800 waarde

//  dolar ten laste euro 0.1

  $(document).on("change", "#calculatorPerStuk, #calculatorAantal", function() {
      $("#calculatorTotal").val($("#calculatorPerStuk").val() * $("#calculatorAantal").val());
  });
  $(document).on('click', '#calculatorSetKoers', function () {
    $('#value').val($("#calculatorTotal").val());
    $('#value').trigger("change");
    createDividendPopover('hide');
    dividendCalculatorShow = false;
  });


  $('#value').popover({
    title: function(){
      return $(this).data('title')+'<span style="float: right; cursor: pointer;" class="close">&times;</span>';
    },
    content: function () {
      return $("#DividendValuCalculator");
    }}).on('hide.bs.popover', function () {
      $("#myPopoverContentContainer").html($("#DividendValuCalculator"));
    }).on('shown.bs.popover', function(e){
      var popover = $(this);
      $(this).parent().find('div.popover .close').on('click', function(e){
        createDividendPopover('hide');
        dividendCalculatorShow = false;
        $('#Fondskoers').focus();
      });
    });

  $(document).click(function(e) {
    if(e.target.id=="popovercloseid" ) {
      createDividendPopover('hide');
      dividendCalculatorShow = false;
    }
  });

  function createDividendPopover (type) {
    if ( type == undefined ) {type = 'show';}
    if ( type == 'show' ) {
      $('#calculatorValutaKoers').val($('#calculatorValutaKoers').val());
      $('#calculatorAantal').val($('#calculatorAantal').val());
    } else {
      $('#Fondskoers').focus();
    }


    $("#calculatorTotal").val($("#calculatorPerStuk").val() * $("#calculatorAantal").val());

    $('#value').popover(type);
  }

  $( "#value" ).keydown(function(e) {
    if ( e.keyCode == 120 ) {
      if ( dividendCalculatorShow == false ) {
        createDividendPopover('show');
        dividendCalculatorShow = true
      } else {
        $('#value').val($("#calculatorTotal").val());
        createDividendPopover('hide');
        dividendCalculatorShow = false;
      }
    }
  });

  $('#dividendCalculatorShow').on('click', function() {
    if ( dividendCalculatorShow == false ) {
      createDividendPopover('show');
      dividendCalculatorShow = true;
      $('#Fondskoers').focus();
    } else {
      createDividendPopover('hide');
      dividendCalculatorShow = false;
      $('#Fondskoers').focus();
    }
  });

  
  $('#recalculate').on('click', function () {
    if ( $('#Fonds').val().length > 0 ) {
      fondsChanged('Fonds');
    }
    $('#Valuta').trigger('change');
  });
  
  changeValuta();

  $('#Valuta').on('change', function () {
    changeValuta();
  });
  
  
  /** Hide required stars on not required rows **/
  DividendRequired('hide');
  CostsRequired('hide');


  //dividend changes
  $('#dividend_value').on('change', function() {
    calculateDividendSum();
    DividendRequired('show');
  });
  $('#dividend_Valutakoers').on('change', function() {
    calculateDividendSum();
  });
  // 
  //cost change
  $('#kosten_value').on('change', function() {
    calculateKostenSum();
    CostsRequired('show');
  });
  $('#kosten_Valutakoers').on('change', function() {
    calculateKostenSum();
  });


  //unset valuta koers
  $('#dividend_Valutakoers').on('change', function() {
    manuelValutaDateInfo('dividend-valuta-koers-info')
  });
  $('#kosten_Valutakoers').on('change', function() {
    manuelValutaDateInfo('kosten-valuta-koers-info')
  });
  $('#Valutakoers').on('change', function() {
    manuelValutaDateInfo('valuta-koers-info')
  });


  //dividend changes
  $('#value').on('change', function() {
    mutationsum();
  });
  $('#Valutakoers').on('change', function() {
    mutationsum();

    /** check other valuta lines **/
    var currentValuta = $('#Valuta').val();
    var currentValutaKoers = $('#Valutakoers').val();
    /** check other valuta lines **/      
    if ( $('#dividend_valuta').val() === currentValuta ) {
      $('#dividend_Valutakoers').val(currentValutaKoers);
      $('#dividend_Valutakoers').change();
      manuelValutaDateInfo('dividend-valuta-koers-info');
    }
    if ( $('#kosten_valuta').val() === currentValuta ) {
      $('#kosten_Valutakoers').val(currentValutaKoers);
      $('#kosten_Valutakoers').change();
      manuelValutaDateInfo('kosten-valuta-koers-info');
    }
  });

  /** empty field if no or wrong fonds is selected **/
  $('#Fonds').blur(function() {
    if ($('#fondseenheid').val().length == 0) {
      $('#Fonds').val('');

      if ($('#Fonds').val() === '') {
        $('#Fonds').focus();
        return false;
      }
    }
  });

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

  $( "#Kosten_Valutakoers, #dividend_Valutakoers" ).keydown(function(e) {
    if ( e.keyCode == 120 ) {
      var inputValue = $(this).val();
      $(this).val(1/inputValue);
      $('#' + $(this).attr('id')).trigger("change");
    }
  });


  $("#Grootboekrekening").on('change', function () {
    setDescription ();
  });
  
  
  
  
  $('#kosten_valuta').on('change', function() {

    if ($('#kosten_valuta').val() == 'EUR') {
      $('#kosten_Valutakoers').val('1.00');
      calculateKostenSum();
      return;
    }

    $.ajax({
      type: 'GET',
      url: getUrl,
      dataType: 'json',
      async: false,
      data: {
        type: 'getExchangeRate',
        rekeningValuta: $('input[name=RekeningValuta]').val(),
        valuta: $('#kosten_valuta').val(),
        date: $('input[name=Boekdatum]').val()
      },
      success: function(data, textStatus, jqXHR) {
        var boekDatum = $('input[name=Boekdatum]').val().split('-').reverse().join('-');
        var boekDate = new Date(boekDatum);
        var valutaDate = new Date(data.valuta.Datum);
        //set date info on valuta fields

        deleteValutaDateInfo('kosten-valuta-koers-info');
        if( $.inArray($('#kosten_valuta').val(), valutaIgnore) == -1 ) {
          setValutaDateInfo('kosten-valuta-koers-info', boekDate, valutaDate);
        }


        //set valuta to sub fields
        $('#kosten_valuta').val(data.valuta.Valuta);
        //set valutakoers to sub fields
        $('#kosten_Valutakoers').val(Number(data.valuta.Koers));
        //recalculate totals
        calculateKostenSum();
      },
      error: function(jqXHR, textStatus, errorThrown) {
        result = jqXHR;
      }
    });
  });
  
  $('#dividend_valuta').on('change', function() {
    $.ajax({
      type: 'GET',
      url: getUrl,
      dataType: 'json',
      async: false,
      data: {
        type: 'getExchangeRate',
        rekeningValuta: $('input[name=RekeningValuta]').val(),
        valuta: $('#dividend_valuta').val(),
        date: $('input[name=Boekdatum]').val()
      },
      success: function(data, textStatus, jqXHR) {
        var boekDatum = $('input[name=Boekdatum]').val().split('-').reverse().join('-');
        var boekDate = new Date(boekDatum);
        var valutaDate = new Date(data.valuta.Datum);
        //set date info on valuta fields

        deleteValutaDateInfo('dividend-valuta-koers-info');
        if( $.inArray($('#dividend_valuta').val(), valutaIgnore) == -1 ) {
          setValutaDateInfo('dividend-valuta-koers-info', boekDate, valutaDate);
        }
        //set valuta to sub fields
        $('#dividend_valuta').val(data.valuta.Valuta);
        //set valutakoers to sub fields
        $('#dividend_Valutakoers').val(Number(data.valuta.Koers));
        //recalculate totals
        calculateDividendSum();
      },
      error: function(jqXHR, textStatus, errorThrown) {
        result = jqXHR;
      }
    });
  });
  
  
});


function mutationsum() {
  var waarde = $('#value').val();
  
  if ( $('input[name=RekeningValuta]').val() == 'EUR' && $('#Valuta').val() != 'EUR' ) {
    var Valutakoers = $('#Valutakoers').val();
    var total = Number(waarde) * Number(Valutakoers);
  } else {
    var total = Number(waarde);
  }
  $('#Bedrag').val(Math.round(total * 100) / 100);
  
  setTotalCalculation();
};
function calculateDividendSum() {
  var dividend_value = $('#dividend_value').val();
  
  if ( $('input[name=RekeningValuta]').val() == 'EUR' && $('#dividend_valuta').val() != 'EUR' ) {
    var dividend_Valutakoers = $('#dividend_Valutakoers').val();
    var total = Number(dividend_value) * Number(dividend_Valutakoers);
  } else {
    var total = Number(dividend_value);
  }
  
  $('#dividend_bedrag').val(Math.round(total * 100) / 100);
  
  setTotalCalculation();
};

function calculateKostenSum() {
  var kosten_value = $('#kosten_value').val();
  
  if ( $('input[name=RekeningValuta]').val() == 'EUR' && $('#kosten_valuta').val() != 'EUR' ) {
    var kosten_Valutakoers = $('#kosten_Valutakoers').val();
    var total = Number(kosten_value) * Number(kosten_Valutakoers);
  } else {
    var total = Number(kosten_value);
  }
  $('#kosten_bedrag').val(Math.round(total * 100) / 100);
  
  setTotalCalculation();
};



function CostsRequired($type) {
  if ($type == 'hide') {
    $('#kosten-table tr td').find('.required').css('display', 'none');
  } else if ($type == 'show') {
    $('#kosten-table tr td').find('.required').css('display', 'inline-block');
  }
}

function DividendRequired($type) {
  if ($type == 'hide') {
    $('#dividend-table tr td').find('.required').css('display', 'none');
  } else if ($type == 'show') {
    $('#dividend-table tr td').find('.required').css('display', 'inline-block');
  }
}

function setTotalCalculation() {
  var bedrag = $('#Bedrag').val();
  var dividend_bedrag = $('#dividend_bedrag').val();
  var kosten_bedrag = $('#kosten_bedrag').val();
  var total = Number(bedrag) - Number(dividend_bedrag) - Number(kosten_bedrag);
  $('#totalMutation').html('<strong>Afschriftbedrag ' + Math.round(total * 100) / 100 + '</strong>');
}


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
      deleteValutaDateInfo('valuta-koers-info');
      deleteValutaDateInfo('dividend-valuta-koers-info');
      deleteValutaDateInfo('kosten-valuta-koers-info');
      //set date info on valuta fields if not in array
      if( $.inArray($('#Valuta').val(), valutaIgnore) == -1 ) {
        setValutaDateInfo('valuta-koers-info', boekDate, valutaDate);
        setValutaDateInfo('dividend-valuta-koers-info', boekDate, valutaDate);
        setValutaDateInfo('kosten-valuta-koers-info', boekDate, valutaDate);
      }
      
      //set valuta to sub fields
      $('#dividend_valuta').val(data.valuta.Valuta);
      $('#kosten_valuta').val(data.valuta.Valuta);

      //set valutakoers to sub fields
      $('#dividend_Valutakoers').val(Number(data.valuta.Koers));
      $('#kosten_Valutakoers').val(Number(data.valuta.Koers));
      //recalculate totals
      mutationsum();
      calculateDividendSum();
      calculateKostenSum();
    },
    error: function(jqXHR, textStatus, errorThrown) {
      result = jqXHR;
    }
  });
}


function setDescription () {
    var fondsOmschrijving = '';
    //set variable for autocomplete 
    if ( $("#Grootboekrekening").val() == "DIV") {
      $('[name="fondssoortExclude"]').val("'OPT', 'OBL'");
    } else if ( $("#Grootboekrekening").val() == "RENOB") {
      $('[name="fondssoortExclude"]').val("'OPT', 'AAND'");
    } else {
      $('[name="fondssoortExclude"]').val("''");
    }
    var grootboek = "Dividend";
    if ( $("#Grootboekrekening").val() == "RENOB") {
      var grootboek = "Coupon";
    }
    var fondsOmschrijving = grootboek;
    
    var fonds = '';
    if ( $("#fonds_omschrijving").val() != '' ) {
      var fondsOmschrijving = grootboek + " " + $("#fonds_omschrijving").val();
      
      /** wanneer omschrijving groter/gelijk is dan 50 **/
      if(fondsOmschrijving.length >= 50) {
        var fondsOmschrijving = grootboek + " " + $('#fonds_fonds').val();
      }
    }
    
    /** omschrijving afkappen tot max 50 chars **/
    var fondsOmschrijving = fondsOmschrijving.substring(0,50);
    
    $("#Omschrijving").val(fondsOmschrijving);
  }