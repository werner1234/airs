/*
    AE-ICT source module
    Author  						: $Author: rm $
 		Laatste aanpassing	: $Date: 2020/05/08 14:53:56 $
 		File Versie					: $Revision: 1.10 $

 		$Log: fondsEdit.js,v $
 		Revision 1.10  2020/05/08 14:53:56  rm
 		8620 Toevoegen Turbo (uitg. instelling)
 		
 		Revision 1.9  2017/04/23 13:15:59  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2016/05/04 07:33:50  rm
 		4920
 		
 		Revision 1.7  2016/02/17 13:47:45  rm
 		Update call 4711
 		
 		Revision 1.6  2016/01/22 15:32:22  rm
 		fonds
 		
 		Revision 1.5  2015/06/05 15:03:34  rm
 		no message
 		
 		Revision 1.4  2015/05/01 14:15:56  rm
 		toevoegen turbo symbolen en jquery modal
 		
 		Revision 1.3  2015/01/26 07:57:49  rm
 		3335
 		
*/





function openModal (modalUrl) {
  $('#modelContent').load(modalUrl);

  $('#uiModalDiv').dialog({
    width: 700,
    autoOpen: false,
    dialogClass: "test",
    modal: true,
    responsive: true
  });

  $('#uiModalDiv').dialog("open");
}




function closeAllForms (formType) {

  optionFormOpen = 1;
  toggleOption (1);

  turboFormOpen = 1;
  toggleTurbo (1);
}


optionFormOpen = 0;
function toggleOption (speed) {
  if ( speed  === undefined){speed = 1000;}
  if (optionFormOpen == 0) {
    closeAllForms ();
    $('#showOptionForm').addClass('open');
    $('#showOptionForm').find('.openText').addClass('hideItem');
    $('#showOptionForm').find('.closeText').removeClass('hideItem');
    $('#optionForm').show(speed);
    $('input[name=fondsInputType]').val('fondsOption');//setcurrentForm
    setFieldsOptions (true);

    makeFonds ();
    makeDescription ();
    makeImportCode ();

    optionFormOpen = 1;
  } else {
    $('#showOptionForm').removeClass('open');
    $('#showOptionForm').find('.openText').removeClass('hideItem');
    $('#showOptionForm').find('.closeText').addClass('hideItem');

    $('input[name=fondsInputType]').val('');//setcurrentForm
    $('#optionForm').hide(speed);

    setFieldsOptions (false);
    optionFormOpen = 0;
  };
}


turboFormOpen = 0;
function toggleTurbo (speed) {
  if ( speed  === undefined){speed = 1000;}

  if (turboFormOpen == 0) {
    closeAllForms ('toggleTurbo');
    $('#showTurboForm').addClass('open');
    $('#showTurboForm').find('.openText').addClass('hideItem');
    $('#showTurboForm').find('.closeText').removeClass('hideItem');
    $('#turbo_issuer').trigger('change');
    $('#turboForm').show(speed);
    $('input[name=fondsInputType]').val('fondsTurbo');//setcurrentForm
    setFieldsTurbo (true);
    turboFormOpen = 1;
  } else {
    $('#showTurboForm').removeClass('open');

    $('#showTurboForm').find('.openText').removeClass('hideItem');
    $('#showTurboForm').find('.closeText').addClass('hideItem');

    $('input[name=fondsInputType]').val('');//setcurrentForm
    $('#turboForm').hide(speed);
    setFieldsTurbo (false);
    turboFormOpen = 0;
  };
}

function setFieldsOptions (status) {
  $("#Fonds").prop("readonly",status);
  $("#Valuta").prop("readonly",status);
  $("#fondssoort").prop("readonly",status);
  $("#Fondseenheid").prop("readonly",status);
  $("#Omschrijving").prop("readonly",status);
  $("#FondsImportCode").prop("readonly",status);

  $("#OptieType").prop("readonly",status);
  $("#expiratieMaand").prop("readonly",status);
  $("#expiratieJaar").prop("readonly",status);
  $("#OptieUitoefenPrijs").prop("readonly",status);
  $("#OptieBovenliggendFonds").prop("readonly",status);

  $('#HeeftOptie').prop('disabled', status);

  /** if we close the optie tab also clear the values **/
  if (status == false) {
    $("#Fonds").val('');
    $("#Omschrijving").val('');
    $("#FondsImportCode").val('');
    $("#Valuta").val('');
    $("#fondssoort").val('');
    $("#Fondseenheid").val('');

    $("#OptieType").val('');
    $("#expiratieMaand").val('');
    $("#expiratieJaar").val('');
    $("#OptieUitoefenPrijs").val('');
    $("#OptieBovenliggendFonds").val('');

    $('#fondsOptieSymbolen').val('');
    $('#optieOptieType').val('');
    $('#optieexpiratieMaand').val('');
    $('#optieexpiratieJaar').val('');
    $('#optieOptieUitoefenPrijs').val('');

    $('#identifierVWD').val('');
    $('#optieIdentifierVWD').val('');

    $('#optieVWDSuffix').val('');
    $('#optieVWDFactor').val('');
  }
}

function setFieldsTurbo (status) {
  $("#Fonds").prop("readonly",status);
  $("#Valuta").prop("readonly",status);
  $("#fondssoort").prop("readonly",status);
//    $("#Fondseenheid").prop("readonly",status);
  $("#Omschrijving").prop("readonly",status);
  $("#FondsImportCode").prop("readonly",status);

  $("#OptieType").prop("readonly",status);
  $("#expiratieMaand").prop("readonly",status);
  $("#expiratieJaar").prop("readonly",status);
  $("#OptieUitoefenPrijs").prop("readonly",status);
  $("#OptieBovenliggendFonds").prop("readonly",status);

  $('#HeeftOptie').prop('disabled', status);

  /** if we close the optie tab also clear the values **/
  if (status == false) {
    $("#Fonds").val('');
    $("#Omschrijving").val('');
    $("#FondsImportCode").val('');
    $("#Valuta").val('');
    $("#fondssoort").val('');
    $("#Fondseenheid").val('');

    $("#fondsTurboSymbolen").val('');
    $("#turbo_isinCode").val('');
    $("#turbo_issuer").val('');
    $("#turbo_kind").val('');
    $("#turbo_longShort").val('');
    $("#turbo_stopLoss").val('');

    $("#turboLong").val('');
    $("#turboShort").val('');
    $("#TurboBovenliggendFonds").val('');
    $("#standaardSector").val('');

    $('#identifierVWD').val('');

    $('#ISINCode').val('');
  }
}




function GetMonthShortName(monthNumber) {
  var months = ['Jan', 'Feb', 'Mrt', 'Apr', 'Mei', 'Jun', 'Jul', 'Aug', 'Sep', 'Okt', 'Nov', 'Dec'];
  return months[monthNumber-1];
}

function makeFonds () {
  var symbol = '';
  var putCall = '';
  var month = '';
  var year = '';
  var price = '';

  if ( $('#fondsOptieSymbolen').val() != '') {
    var symbol = $('#fondsOptieSymbolen').val();
  }

  if ( $('#optieOptieType').val() != '' ) {
    var putCall = $('#optieOptieType').val();
  }

  if ( $('#optieexpiratieMaand').val() != '' ) {
    var month = GetMonthShortName($('#optieexpiratieMaand').val());
  }

  if ( $('#optieexpiratieJaar').val() != '' ) {
    var year = $('#optieexpiratieJaar').val();
    var year = year.substr(year.length - 2);
  }

  if ( $('#OptieUitoefenPrijs').val() > 0 ) {
    var price = $('#OptieUitoefenPrijs').val();

    if (price.split('.')[1] == 0 || price.split('.')[1] == undefined) {
      var price = price.split('.')[0];
    } else {
      var priceFloat = parseFloat(price);

      var priceString = priceFloat.toString();
      priceDecimals = 2;
      if ( priceString.split('.')[1]  != undefined && priceString.split('.')[1].length > 2 ) {
        priceDecimals = priceString.split('.')[1].length;
        Math.round(priceFloat + "e+"+priceDecimals)  + "e-"+priceDecimals;
      }
      var priceFloat = parseFloat(priceFloat);
      var priceString = priceFloat.toFixed(parseInt(priceDecimals));

      var price = priceString.toString();
    }






  }

  $('#Fonds').val(symbol + ' ' + putCall + ' ' + month + '' + year + ' ' + price);
}

function makeDescription () {
  var fonds = '';
  var putCall = '';
  var month = '';
  var year = '';
  var price = '';

  if ( $('#OptieBovenliggendFonds').val() != '') {
    var fonds = $('#OptieBovenliggendFonds').val();
  }

  if ( $('#optieOptieType').val() != '' ) {
    var putCall = $('#optieOptieType').val();
    if (putCall == 'P') {
      var putCall = 'Put';
    } else if ( putCall == 'C' ) {
      var putCall = 'Call';
    }
  }

  if ( $('#optieexpiratieMaand').val() != '' ) {
    var month = GetMonthShortName($('#optieexpiratieMaand').val());
  }

  if ( $('#optieexpiratieJaar').val() != '' ) {
    var year = $('#optieexpiratieJaar').val();
    var year = year.substr(year.length - 2);
  }

  if ( $('#OptieUitoefenPrijs').val() > 0 ) {


    var priceValue = $('#OptieUitoefenPrijs').val();
    var priceFloat = parseFloat(priceValue);

    var priceString = priceFloat.toString();
    priceDecimals = 2;
    if ( priceString.split('.')[1]  != undefined && priceString.split('.')[1].length > 2 ) {
      priceDecimals = priceString.split('.')[1].length;
      Math.round(priceFloat + "e+"+priceDecimals)  + "e-"+priceDecimals;
    }
    var priceFloat = parseFloat(priceFloat);
    var priceString = priceFloat.toFixed(parseInt(priceDecimals));

    var price = priceString.toString().replace('.', ',');

//    var price = $('#OptieUitoefenPrijs').val();
//    var price = parseFloat(price);
//    var price = Math.round(price + "e+2")  + "e-2";
//    var price = parseFloat(price);
//    var price = price.toString().replace('.', ',');
  }

  $('#Omschrijving').val(fonds + ' ' + putCall + ' ' + month + '' + year + ' ' + price);
}

function makeImportCode () {
  var symbol = '';
  var putCall = '';
  var month = '';
  var year = '';
  var price = '';

  if ( $('#fondsOptieSymbolen').val() != '') {
    var symbol = $('#fondsOptieSymbolen').val();
  }

  if ( $('#optieOptieType').val() != '' ) {
    var putCall = $('#optieOptieType').val();
  }

  if ( $('#optieexpiratieMaand').val() != '' ) {
    var month = GetMonthShortName($('#optieexpiratieMaand').val());
  }

  if ( $('#optieexpiratieJaar').val() != '' ) {
    var year = $('#optieexpiratieJaar').val();
    var year = year.substr(year.length - 2);
  }

  if ( $('#OptieUitoefenPrijs').val() > 0 ) {
    var price = $('#OptieUitoefenPrijs').val();
    if (price.split('.')[1] == 0 ) {
      var price = price.split('.')[0];
    }
  }

  $('#FondsImportCode').val(symbol + putCall + month + year + price);
//  Importcode*: <Symbool><C/P><MM><JJ><Uitoefenprijs>
}

function makeVWD () {
  var vwd = '';
  var expData = '';
  var price =  '';
  var suffix = '';

  if ($("#optieIdentifierVWD").val() != '') {
    var vwd = $("#optieIdentifierVWD").val();
    var expData = makeExpData ();

    if ( $('#optieVWDSuffix').val() != '' ) {
      var suffix = $('#optieVWDSuffix').val();
    }

    if ( $('#OptieUitoefenPrijs').val() > 0 ) {
      var price = $('#OptieUitoefenPrijs').val();

      /** 3335 if vwd factor > 1 multiply price * factor **/
      if ( $('#optieVWDFactor').val() > 1 ) {
        var price = price * Number($('#optieVWDFactor').val());
      }

      var price = parseFloat(price);
      var price = price.toString().replace('.', '_');
    }

    $('#identifierVWD').val(vwd + '.' + price + '.' + expData + '' + suffix);
  }
}

function makeExpData () {
  var month = '';
  var year = '';

  if ( $('#optieexpiratieJaar').val() != '' ) {
    var year = $('#optieexpiratieJaar').val();
    var year = year.substr(year.length - 1);
  }

  if ( $('#optieOptieType').val() != '' ) {
    var month = $('#optieexpiratieMaand').val();
    if ( $('#optieOptieType').val() == 'P' ) {
      if ( $('#optieexpiratieMaand').val() != '' ) {
        var monthConvert = [
          'M',
          'N',
          'O',
          'P',
          'Q',
          'R',
          'S',
          'T',
          'U',
          'V',
          'W',
          'X'
        ];
        var month = monthConvert[month-1];
      }
    } else if ( $('#optieOptieType').val() == 'C' ) {
      if ( $('#optieexpiratieMaand').val() != '' ) {
        var monthConvert = [
          'A',
          'B',
          'C',
          'D',
          'E',
          'F',
          'G',
          'H',
          'I',
          'J',
          'K',
          'L'
        ];
        var month = monthConvert[month-1];
      }
    }
  }
  return year + month;
}




/**
 * Maak turbo fonds
 * @returns {undefined}
 */
function makeTurboFonds () {
  var issuer = '';

  var turboLong = '';
  var turboshort = '';

  var turbo_kind = '';
  var turbo_longShort = '';

  var stopLoss = '';

  if ( $('#fondsTurboSymbolen').val() != '' ) {
    var turboLong = $('#turboLong').val();
    var turboshort = $('#turboShort').val();
  }

  if ( $('#turbo_issuer').val() != '' ) {
    var issuer = $('#turbo_issuer').val();
  }

  if ( $('#turbo_kind').val() != '' ) {
    var turbo_kind = $('#turbo_kind').val();
  }

  if ( $('#turbo_longShort').val() != '' ) {
    var turbo_longShort = $('#turbo_longShort').val();
  }

  if ( $('#turbo_stopLoss').val() != '' && $('#turbo_stopLoss').val() != 0 ) {
    var stopLoss = $('#turbo_stopLoss').val();
    if (stopLoss.split('.')[1] == 0 ) {
      var stopLoss = stopLoss.split('.')[0];
    } else {
      var stopLossValue = $('#turbo_stopLoss').val();
      var stopLossFloat = parseFloat(stopLossValue);

      var stopLossString = stopLossFloat.toString();
      stopLossDecimals = 2;
      if ( stopLossString.split('.')[1]  != undefined && stopLossString.split('.')[1].length > 2 ) {
        stopLossDecimals = stopLossString.split('.')[1].length;
        Math.round(stopLossFloat + "e+"+stopLossDecimals)  + "e-"+stopLossDecimals;
      }
      var stopLossFloat = parseFloat(stopLossFloat);
      var stopLossString = stopLossFloat.toFixed(parseInt(stopLossDecimals));

      var stopLoss = stopLossString.toString();//.replace('.', ',');
    }

  }

  $('#Fonds').val(issuer + ' ' + turboshort + ' ' + turbo_kind + '' + turbo_longShort + '' + stopLoss);
}

/**
 * Maak de turbo omschrijving
 * @returns set omschrijving field
 */
function makeTurboDescription () {
  var issuer = '';

  var turboLong = '';
  var turboshort = '';

  var turbo_kind = '';
  var turbo_longShort = '';

  var stopLoss = '';

  if ( $('#fondsTurboSymbolen').val() != '' ) {
    var turboLong = $('#turboLong').val();
    var turboshort = $('#turboShort').val();
  }

  if ( $('#turbo_issuer').val() != '' ) {
    var issuer = $('#turbo_issuer').val();
  }

  if ( $('#turbo_kind').val() != '' ) {
    var turbo_kind = $('#turbo_kind').val();
  }

  if ( $('#turbo_longShort').val() != '' ) {
    var turbo_longShort = $('#turbo_longShort').val();
  }

  if ( $('#turbo_stopLoss').val() != '' && $('#turbo_stopLoss').val() != 0 ) {
    var stopLossValue = $('#turbo_stopLoss').val();
    var stopLossFloat = parseFloat(stopLossValue);

    var stopLossString = stopLossFloat.toString();
    stopLossDecimals = 2;
    if ( stopLossString.split('.')[1]  != undefined && stopLossString.split('.')[1].length > 2 ) {
      stopLossDecimals = stopLossString.split('.')[1].length;
      Math.round(stopLossFloat + "e+"+stopLossDecimals)  + "e-"+stopLossDecimals;
    }
    var stopLossFloat = parseFloat(stopLossFloat);
    var stopLossString = stopLossFloat.toFixed(parseInt(stopLossDecimals));

    var stopLoss = stopLossString.toString().replace('.', ',');
  }

  $('#Omschrijving').val(issuer + ' ' + turboLong + ' ' + turbo_kind + ' ' + turbo_longShort + ' ' + stopLoss);
}



function getFondskoersenList () {
  loadToDiv('fondskoersenList', 'fondsFondskoersen.php?id=' + $('input[name=id]').val());
}






$(function() {

  getFondskoersenList();
  try {
    renteFrame();
  }
  catch(err) {
    // console.log(err.message);
  }





  $('#optieOptieType').on('change',  function() {
    $('#OptieType').val($('#optieOptieType').val());
    makeFonds ();
    makeDescription ();
    makeImportCode ();
    makeVWD ();
  });

  $('#optieexpiratieMaand').on('change',  function() {
    $('#expiratieMaand').val($('#optieexpiratieMaand').val());
    makeFonds ();
    makeDescription ();
    makeImportCode ();
    makeVWD ();
  });

  $('#optieexpiratieJaar').on('change',  function() {
    $('#expiratieJaar').val($('#optieexpiratieJaar').val());
    makeFonds ();
    makeDescription ();
    makeImportCode ();
    makeVWD ();
  });

  $('#optieOptieUitoefenPrijs').on('change',  function() {
    $('#OptieUitoefenPrijs').val($('#optieOptieUitoefenPrijs').val());
    makeFonds ();
    makeDescription ();
    makeImportCode ();
    makeVWD ();
  });





  /** turbo **/
  $('#turbo_issuer').on('change', function() {
    $("#turbo_kind").val("");//maak soort turbo leeg

    makeTurboDescription();
    makeTurboFonds ();

    var issuerVal = $('#turbo_issuer').val();


    $("#turbo_kind option").prop('disabled',true);
    $("#turbo_kind option:first-child").prop('disabled',false);

    if( $.inArray(issuerVal, ['AAB', 'GS', 'BIN', 'RBS', 'BNP']) != -1 ) {
      $("#turbo_kind option[value='Tr']").prop('disabled',false);
      $("#turbo_kind option[value='T']").prop('disabled',false);
//      turbo_kind += '<option value="TR">Trader</option><option value="t">Turbo</option>';
    }

    if ( issuerVal == 'ING' ) {
      $("#turbo_kind option[value='Spr']").prop('disabled',false);
      $("#turbo_kind option[value='BSpr']").prop('disabled',false);
//      turbo_kind += '<option value="Spr">Sprinter</option><option value="BS">Best sprinter</option>';
    }

    if ( $.inArray(issuerVal, ['BNP', 'RBS']) != -1 ) {
      $("#turbo_kind option[value='B']").prop('disabled',false);
//      turbo_kind += '<option value="B">Booster</option>';
    }

    if ( $.inArray(issuerVal, ['BNP', 'SG']) != -1 ) {
      $("#turbo_kind option[value='mFut']").prop('disabled',false);
    }

    if ( issuerVal == 'CITI' ) {
      $("#turbo_kind option[value='S']").prop('disabled',false);
      $("#turbo_kind option[value='BSp']").prop('disabled',false);
      $("#turbo_kind option[value='BTb']").prop('disabled',false);
      $("#turbo_kind option[value='T']").prop('disabled',false);
    }

    //9402
//     if ( issuerVal == 'CB' ) {
//       $("#turbo_kind option[value='S']").prop('disabled',false);
//       $("#turbo_kind option[value='T']").prop('disabled',false);
//       $("#turbo_kind option[value='BSp']").prop('disabled',false);
//       $("#turbo_kind option[value='BTb']").prop('disabled',false);
// //      turbo_kind += '<option value="S">Speeder</option>';
//     }

    if ( issuerVal == 'SG' ) {
      $("#turbo_kind option[value='BTb']").prop('disabled',false);
      $("#turbo_kind option[value='T']").prop('disabled',false);
    }
  });

  $('#turbo_isinCode').on('change', function() {
    $('#FondsImportCode').val($('#turbo_isinCode').val());
    $('#ISINCode').val($('#turbo_isinCode').val());
  });

  $('#turbo_stopLoss, #turbo_longShort, #turbo_kind, #fondsTurboSymbolen').on('change', function() {
    makeTurboDescription();
    makeTurboFonds ();
  });







  $('#showTurboForm').on('click', function () {
    toggleTurbo ();
  });

  /** reopen turbo form if **/
  if ('{fondsTurboSymbolenDisplay}' == 'true' ) {
    $('#showTurboForm').addClass('open');
    $('#showTurboForm').find('.openText').addClass('hideItem');
    $('#showTurboForm').find('.closeText').removeClass('hideItem');
    $('#turbo_issuer').trigger('change');
    $('#turboForm').show();
    $('input[name=fondsInputType]').val('fondsTurbo');//setcurrentForm
    turboFormOpen = 1;
  }


  $('#showOptionForm').on('click', function () {
    toggleOption ();
  });

  /** reopen option form **/
  if ('{fondsOptieSymbolenDisplay}' == 'true' ) {
    console.log('test');
    $('#showOptionForm').addClass('open');
    $('#showOptionForm').find('.openText').addClass('hideItem');
    $('#showOptionForm').find('.closeText').removeClass('hideItem');
    $('#optionForm').show();
    $('input[name=fondsInputType]').val('fondsOption');//setcurrentForm
    setFieldsOptions (true);

    makeFonds ();
    makeDescription ();
    makeImportCode ();

    optionFormOpen = 1;
  }

});