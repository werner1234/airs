fullRekeningenList = '';
$orderStatus = $('#originalOrderStatus').val();



var controler = {
  0:0,    // Geen checkbox
  1:0,    // Checkbox maar niet aangevinkt
  2:0     // Checkbox en aangevinkt
};

$('#controllesForm > tbody  > tr').each(function() {
  var textval = $(this).find("td").eq(0).find('input:checkbox');
  if(typeof textval.attr('id') == 'undefined') {
    controler[0] = controler[0] + 1;
  } else if ( textval.is(':checked') ) {
    controler[2] = controler[2] + 1;
  } else {
    controler[1] = controler[1] + 1;
  }
});

if ( controler[1] > 0 ) {
  $('.tab-controlles a').append( ' <i style="color:red;" class="fa fa-circle"></i> ' );
} else if ( controler[2] > 0 ) {
  $('.tab-controlles a').append( ' <i style="color:green;" class="fa fa-circle"></i> ' );
}

function bankDepotbankChanged () {
  $('#BankDepotCodes').html('');
  $('#fondsBankcode').val('');
  if ( $fonds !== '' && $('#Depotbank').val() !== '' )
  {
    $code = $BankDepotCodes[$('#Depotbank').val()];
    
    
    if ( typeof $fonds[$code] !== "undefined" && $fonds[$code] != "" ) {
      $('#BankDepotCodes').html('Fondscode depotbank: ' + $fonds[$code]);
      $('#fondsBankcode').val($fonds[$code]);
    }
  }
}

function changeRekening($type, $isChanged) {


  if ( $('#OrderuitvoerBewaarder').val() == 1 ) {
    fondsChanged('fonds');
    
    if ( $("input[name=orderSelectieType]:checked", ".orderForm").val() === "C" )
    {
      
      if ( jQuery.type($type) !== "undefined" && $type == 'tempRekening' ) {
        $rekNr = $('#tempRekening').val();
      } else {
        $rekNr = $('#rekening').val();
      }
      
    } else {
      $rekNr = $('#rekening').val();
    }
    if( typeof(fullRekeningenList[$rekNr]) !== "undefined" )
    {
      if (fullRekeningenList[$rekNr]['Rekening_Depotbank'].length > 0)
      {
        $('#DepotbankOld').val($('#Depotbank').val());
        $('#Depotbank').val(fullRekeningenList[$rekNr]['Rekening_Depotbank']);
        $('#depobankValue').html(fullRekeningenList[$rekNr]['Rekening_Depotbank']);
      }
    }
    
    $fixCheckStatus = $('#fixOrder').is(':checked');
    $careCheckStatus = $('#careOrder').is(':checked');

    /** Check Fix  **/
    $.ajax({
      url: 'ordersEditV2.php?OrderuitvoerBewaarderFix=1&rekening=' + $rekNr + '&depotbank=' + $('#Depotbank').val() + '&portefeuille=' + $('#portefeuille').val() + '&orderSelectieType=' + $("input[name=orderSelectieType]:checked", ".orderForm").val(),
      type: "GET",
      dataType: 'json',
      async: false,
      success: function (data, textStatus, jqXHR) {
        if ( $isChanged == false ) {

          if ( currentRekeningValue  != "" ) {

            $fixChecked = $("#fixOrder").prop("checked");
            $careChecked = $("#careOrder").prop("checked");

            if (data.showfix === 1) {
              fixOrder(true, $fixChecked);

              if ( $careChecked == true ) {
                careOrder(1);
              } else {
                careOrder(0);
              }

            } else {
              fixOrder(false, false);
              careOrder(0);
            }
          }
        } else {
        
          fixDefaultAan = false;
          if ( data.fixDefaultAan == 1 ) {
            fixDefaultAan = true;
          }

          if ( $("input[name=orderSelectieType]:checked", ".orderForm").val() === "C" && $('#initialValuesSet').val() == 1 ) {
            if ( data.showfix === 1 ) {
              fixOrder(true,$('#tempFixOrder').is(':checked'));

              if ( data.careOrderVerplicht != null  && data.careOrderVerplicht != "") {
                careOrder(data.careOrderVerplicht);
              }
            } else {
              fixOrder(false,$('#tempFixOrder').is(':checked'));

              if ( data.careOrderVerplicht != null  && data.careOrderVerplicht != "") {
                careOrder(data.careOrderVerplicht);
              }
            }
          } else {
            if ( data.showfix === 1 ) {
              fixOrder(true,fixDefaultAan);

              if ( data.careOrderVerplicht != null  && data.careOrderVerplicht != "") {
                careOrder(data.careOrderVerplicht);
              }
            } else {
              fixOrder(false,fixDefaultAan);

              if ( data.careOrderVerplicht != null  && data.careOrderVerplicht != "") {
                careOrder(data.careOrderVerplicht);
              }
            }
          }
        }
        // if ( $isChanged == false && $('#orderregelId').val() > 0 ) {
        //   fixOrder($fixCheckStatus,fixDefaultAan);
        //   careOrder($careCheckStatus);
        //   $("#fixOrder").prop('checked', $fixCheckStatus);
        //   $("#careOrder").prop('checked', $careCheckStatus);
        // }
        //
        //
        // $('#fixOrder').change();
      }
    });
    
    
  }
}
function missingEMail () {
  if ( '{relationHasEmail}' === '0' ) {
    $('#missingEMail').show();
  }
}

$(function () {
  
  $('#showAdviseMailSendDate').hide();
  $('#viewAdviseMail').hide();
  $('#sendAdviseMail').hide();
  $('#missingEMail').hide();
  $('#ignoreAdviseMail').hide();
  $('#adviseMailIsIgnored').hide();
  
  $('#adviesMailHolder').hide();
  
  
  
  // Advies is uit
  if ( '{adviesStatus}' == '0' ) {
  
  }
  //advies is aan
  else {
    
    // Advies mail is verzonden
    // 0 = nee 1 = ja
    if ( '{adviesEmailVerzonden}' === '1' ) {
      $('#showAdviseMailSendDate').show();
      $('#viewAdviseMail').show();
      $('#adviesMailHolder').show();
      $('#adviseOrderSendDate').html('{adviseOrderSendDate}');
    }
    
    //Advies mail is genegeerd
    else if ( '{adviesEmailGenegeerd}' == '1' ) {
      $('#adviesMailHolder').show();
      $('#adviseMailIsIgnored').show();
      $('#adviseOrderIgnoreDate').html('{adviseOrderSendDate}');
    }
    
    //advies status = 1
    //Alleen bij advies = J
    else if ( '{adviesStatus}' == '1' ) {
      if ( '{adviesRelatie}' === '1' ) {
        missingEMail ();
        $('#sendAdviseMail').show();
        $('#adviesMailHolder').show();
      }
    }
    
    //advies status = 2
    // alleen bij advies - J + knop Negeern
    else if ( '{adviesStatus}' == '2' ) {
      if ( '{adviesRelatie}' === '1' ) {
        missingEMail ();
        $('#sendAdviseMail').show();
        $('#ignoreAdviseMail').show();
        $('#adviesMailHolder').show();
      }
    }
    
    //advies status = 3
    // Altijd tonen alleen verplicht bij advies = J
    else if ( '{adviesStatus}' == '3' ) {
      
      // adviesrelatie = 0
      if ( '{adviesRelatie}' === '0' ) {
        missingEMail ();
        $('#sendAdviseMail').show();
      }
      // adviesrelatie = 1
      else {
        missingEMail ();
        $('#sendAdviseMail').show();
      }
      $('#adviesMailHolder').show();
    }
    
    //advies sttus = 4
    // Altijd tonen + negeer knop bij advies = J
    else if ( '{adviesStatus}' == 4 ) {
      //adviesrelatie = 0
      if ( '{adviesRelatie}' === '0' ) {
        missingEMail ();
        $('#sendAdviseMail').show();
      }
      //adviesrelatie = 1
      else {
        missingEMail ();
        $('#sendAdviseMail').show();
        $('#ignoreAdviseMail').show();
      }
      $('#adviesMailHolder').show();
    }
    
    //advies status = 5
    // Altijd tonen
    else if ( '{adviesStatus}' == '5' ) {
      //adviesrelatie = 0
      if ( '{adviesRelatie}' === '0' ) {
        missingEMail ();
        $('#sendAdviseMail').show();
      }
      //adviesrelatie = 1
      else {
        missingEMail ();
        $('#sendAdviseMail').show();
      }
      $('#adviesMailHolder').show();
    }
  }
  
  //
  // if ( {adviesStatus} > 0 ) {
  //   $('#adviesMailHolder').show();
  //
  //   if ( '{relationHasEmail}' === '0' ) {
  //     $('#missingEMail').show();
  //   }
  //
  //   if ( '{adviesRelatie}' === '1' && '{relationHasEmail}' === '0' && ({adviesStatus} === 1 || {adviesStatus} === 3) ) {
  //     $('#missingEMail').show();
  //   } else if ( '{adviesRelatie}' === '0' && '{relationHasEmail}' === '0' &&  {adviesStatus} === 3 ) {
  //     $('#missingEMail').show();
  //   } else if ( '{adviesRelatie}' === '1' && '{relationHasEmail}' === '0' &&  {adviesStatus} === 4 ) {
  //     $('#ignoreAdviseMail').show();
  //   } else if ( '{adviesRelatie}' === '1' && {adviesStatus} === 2 && '{relationHasEmail}' === '0' ) {
  //     $('#missingEMail').show();
  //     $('#ignoreAdviseMail').show();
//     }
// else {
//       if ( '{adviesEmailVerzonden}' === '1' ) {
//         $('#showAdviseMailSendDate').show();
//         $('#viewAdviseMail').show();
//         $('#adviseOrderSendDate').html('{adviseOrderSendDate}');
//       }
//       else if ( '{adviesEmailGenegeerd}' == '1' ) {
//
//         $('#adviseMailIsIgnored').show();
//         $('#adviseOrderIgnoreDate').html('{adviseOrderSendDate}');
//       }
//       else {
//         if ( '{relationHasEmail}' === '1' )
//         {
//           $('#sendAdviseMail').show();
//         }
//
//         if ( '{adviesRelatie}' === '1' && {adviesStatus} === 2 || {adviesStatus} === 4 )
//           $('#ignoreAdviseMail').show();
//         }
//       }
//
//   }
  
  
  
  
  if ( '{adviesRelatie}' == '1' ) {
  
  } else {
    $('.tab-orderadvies').remove();
    $('#tab-orderadvies').remove();
  }
  
  
  $( ".orderTabHolder" ).tabs({
    collapsible: true,
    select: function(event, ui) {
      if ( ui.panel.id === 'tab-notas' ) {
        $('.tab-notas').click();
        return false;
      }
    }
  });
  
  $( ".orderRegelTabHolder" ).tabs({
    collapsible: true,
    active: 1,
    select: function(event, ui) {
      if ( ui.panel.id === 'tab-notas' ) {
        $('.tab-notas').click();
      }
    }
  });
  
  notavisibility ();
  extraOptionFields ();
  
  $('.tab-uitvoeringen').hide();
  
  /** wanneer het veld transactie soort wordt gewijzigd ook de kleur wijzigen **/
  
  changeTransactionColor ();
  $('#transactieSoort').on('change', function () {
    changeTransactionColor ();
  });
  function changeTransactionColor () {
    /** Groen **/
    if ( jQuery.inArray($('#transactieSoort').val(), ['A', 'AO', 'VO', 'I']) >= 0 ) { // was 'A', 'AO', 'AS'
      //background-color:#5cb85c
      $('#transactieSoort, #transactieSoortMsg').css('background-color', '#5cb85c');
    }
    
    /** Rood **/
    if ( jQuery.inArray($('#transactieSoort').val(), ['V', 'AS', 'VS']) >= 0 ) { // was 'V', 'VO', 'VS'
      $('#transactieSoort, #transactieSoortMsg').css('background-color', '#ce4844');
    }
    
    $('#transactieSoort option').css('background-color', 'white');
  }
  
  
  
  $(document).on('click', '#addNewFonds', function () {
    $('#newFondsHolder').dialog({
      width: 700,
      autoOpen: false,
      dialogClass: "test",
      modal: true,
      responsive: true,
      title: "Nieuw fonds inleggen"
    });
    $('#newFondsHolder').dialog("open");
  });
  
  
  $(document).on('click', '.tab-notas', function (event) {
    event.preventDefault();
    $('#notaEditHolder').dialog({
      width: 700,
      autoOpen: false,
      dialogClass: "test",
      modal: true,
      responsive: true,
      title: "Nota's"
    });
    $('#notaEditHolder').dialog("open");
  });
  
  
  $(document).on('change', '#transactieType', function () {
    var curVal = $('#transactieType').val();
    if(curVal == 'B') {
      $('#tijdsSoort').val('GTC');
      $('#koersLimiet').val('0.00000');
      $('#tijdsLimiet').val('');
      $('#koersLimiet').prop('readonly', true);
      $('#koersLimiet').addClass('readOnlyField');
      
      $('#tijdsLimiet').datepicker('disable');
      $('#tijdsLimiet').addClass('notEditable');
    } else if(curVal == 'L') {
      $('#tijdsSoort').val('DAT');
      $('#koersLimiet').val('0.00000');
      $('#tijdsLimiet').val('');
      
      $('#koersLimiet').prop('readonly', false);
      $('#koersLimiet').removeClass('readOnlyField');
      tijdsSoortChanged();
    }
  });
  
  /**
   * initieel bepalen welke status de velden krijgen
   */
  transactieTypeInit();
  function transactieTypeInit() {
    var curVal = $('#transactieType').val();
    if(curVal == 'B') {
      $('#koersLimiet').prop('readonly', true);
      $('#koersLimiet').addClass('readOnlyField');
    } else if(curVal == 'L') {
      $('#koersLimiet').prop('readonly', false);
      $('#koersLimiet').removeClass('readOnlyField');
      tijdsSoortChanged();
    }
  }
  
  $(document).on('change', '#koersLimiet', function () {
    var curVal = $('#koersLimiet').val();
    
    var ttVal = $('#transactieType').val();
    if(ttVal == 'L' && (curVal == 0 || curVal == '') ) {
      alert('KoersLimiet mag niet leeg zijn.');
    }
  });
  
  $(document).on('change', '#koersLimiet', function () {
    var curVal = $('#koersLimiet').val();
    
    var ttVal = $('#transactieType').val();
    if(ttVal == 'L' && (curVal == 0 || curVal == '') ) {
      alert('KoersLimiet mag niet leeg zijn.');
    }
  });
  
  $(document).on('change', '#PortefeuilleSelectie, #rekening, #ISINCode, #transactieSoort, #aantal, #koersLimiet',function () {
    if ( $('#orderStatus').val() < 1 ) {
      clearControlle();
    };
    
    if ( $(this).attr('id') === 'rekening' ) {
      changeRekening()
    }
  });
  
  
  
  
  
  
  $(document).on('change', '#aantal, #transactieSoort', function() {
    aantalChanged();
  });
  
  $('#ISINCode').on('input', function() {
    $('#fondsOmschrijving').val('');
    $('#fondsOmschrijvingHidden').val('');
    
    $('#fonds').val('');
    $('#fonds_id').val('');
    $('input[name=fonds_id]').val('');
    
    $('#fonds-info').removeClass();
    $('#fonds-info').html('');
    
    $('#fonds-koers-info').removeClass();
    $('#fonds-koers-info').html('');
    
    $('#koersInfo').html('');
    $('#BankDepotCodes').html('');
  });
  
  
  
  //status changed
  statusChanged($('#orderStatus'));
  $('#orderStatus').on('change', function () {
    statusChanged($(this));
  });

  $('#toOrderlist, #toOrderlistTop').on('click', function () {
    if ($('input[name=id]').val() > 0) {
      $.ajax({
        url: 'keepalive.php?delete=1&random=' + Math.round(Math.random() * 1000000) + '&tableId=' + $('input[name=id]').val(),
        type: "GET",
        success: function (data, textStatus, jqXHR) {
          parent.frames['content'].location = $('input[name=return]').val();
        }
      });
    } else {
      parent.frames['content'].location = $('input[name=return]').val();
    }
  });
  
  
  if ($('input[name=id]').val() > 0 && $('#orderStatus').val() < 2 || $('#copyFrom').val() > 0) {
    $("#fondsOmschrijving").prop("readonly", true);
    
    
    valutaChanged($('#fondsValuta').val());
    fillTransactionType($("#fondssoort").val());
    isinChanged ();
    aantalChanged();
    
    fondsChanged("fonds");
  }
  
  
});

function showLoading(text) {
  // add the overlay with loading image to the page
  $('#overlay').remove();
  var over = '<div id="overlay"><div id="loading-box">' +
    '<div id="loading-txt">' + text + '</div>' +
    '<img id="loading-img" src="images/ajax-loader.gif">' +
    '</div></div>';
  $(over).appendTo('body');
}
;
function removeLoading() {
  $('#overlay').remove();
}

function clearControlle()
{
  //  $('#controllesForm').slideUp();
  $('#controllesForm').find('input[type=checkbox]:checked').removeAttr('checked');
  pane = $(".orderTabHolder .ui-tabs-panel:visible").attr('id');
  if (pane !== 'tab-extraOpties')
  {
    $('a[href="#tab-extraOpties"]').get(0).click();
  }
  $('.tab-controlles').hide();
  
  if ( $("input[name=orderSelectieType]:checked", ".orderForm").val() === "M" ||  $("input[name=orderSelectieType]:checked", ".orderForm").val() === "O" ) {
    $('#orderRegelOptionsBlock').hide();
  }
  $('.insertOrderButtonGroup').slideDown();
  $('#resetControle').val(1);
}

function aantalChanged()
{
  if ( $.isNumeric($('#aantal').val()) || $.isNumeric($('#bedrag').val()) )
  {
    if ( $('#giraleOrder').is(':checked') ) {
      $('#orderWaarde').html("Orderbedrag:<b>" + round($('#aantal').val(), 2) + "</b> EUR");
    } else
    {
      var $orderValue=0.00;
      if($.isNumeric($('#bedrag').val()))
      {
        $orderValue = round($('#bedrag').val() * $('#valutaKoersHidden').val()  ,2);
      }
      else
      {
        if($('#koersLimiet').val() != 0)
        {
          $orderValue = round(( $('#fondseenheidHidden').val() * $('#valutaKoersHidden').val() * $('#koersLimiet').val() * $('#aantal').val()), 2);
        }
        else
        {
          
          $orderValue = round(( $('#fondseenheidHidden').val() * $('#valutaKoersHidden').val() * $('#koersLimietHidden').val() * $('#aantal').val()), 2);
        }
      }
      
      if(jQuery.inArray( $('#transactieSoort').val() , ['A', 'AO', 'AS', 'I']) !== -1)
      {
        $orderValue = -Math.abs($orderValue);
      }
      
      
      $('#orderWaarde').html("");
      if ( $orderValue != '0.00' ) {
        $('#orderbedrag').val($orderValue);
        $('#orderWaarde').html("Geschat orderbedrag: <b>"+ $orderValue +"</b> EUR");
      }
    }
  }
}

function saveOrderStatus () {
  var postData = $(document).find('.orderForm').serializeArray();
  var formURL = $('.orderForm').attr("action");
  
  return $.ajax({
    url : formURL + '?changeStatus=true',
    type: "POST",
    dataType: 'json',
    data : postData,
    success:function(data, textStatus, jqXHR) {
      if ( data.saved == false ) {
        $('#messageHolder').html('<div class="alert alert-warning">Order status kon niet worden aangepast!</div>');
        changedOrderStatus = 0;
        return false;
      } else {
        $('#messageHolder').html('<div class="alert alert-success">Order status is aangepast!</div>');
        changedOrderStatus = 1;
        return true;
      }
    }
  });
}



newPrevValue = null;

function statusChanged(element) {
  
  changedOrderStatus = 0;
  prevValue = newPrevValue;
  if (element.val() >= 2 ) {
    
    
    /** gaan we naar status 2 opslaan voor de uitvoeringen **/
    if ( element.val() == 2 && element.val() != $('#originalOrderStatus').val() ) {
      
      /** controlleer of we rechten hebben op uitvoeringen **/
      if ( $('#handmatig_uitvoeringenMuteren').val() == 1 )
      {
        /** we mogen uitvoeringen toevoegen hier een bericht met mogelijk om uitvoeringen toe te voegen tonen **/
        $('<div style="padding: 10px; max-width: 500px; word-wrap: break-word;">Let op! De gewijzigde status wordt automatisch opgeslagen.' +
          ' <br /> Wilt u verder naar het invoeren van uitvoeringen ?</div>').dialog({
          draggable: false,
          modal: true,
          resizable: false,
          width: 'auto',
          title: 'Status wijzigen',
          minHeight: 75,
          buttons: {
            "Ja": function ()
            {
              $.when(saveOrderStatus()).done(function (a1)
              {
                if (changedOrderStatus == 1)
                {
                  $('#uitvoeringen').load(encodeURI('orderuitvoeringListV2.php?orderid=' + $('#id').val() + '&autoOpenNew=true&selectedStatus=' + element.val()));
                  $("#uitvoeringen").slideDown("slow");
                  $('.tab-uitvoeringen').show();
                  $("#extraFondsOpties").slideDown("slow");
                  newPrevValue = element.val();
                } else
                {
                }
              });
              $(this).dialog('destroy');
              
              pane = $(".orderTabHolder .ui-tabs-panel:visible").attr('id');
              if ( pane !== 'uitvoeringen' )
              {
                $(".orderTabHolder").tabs('select', 'uitvoeringen');
              }
              
              $("#orderStatus option[value='1']").prop('disabled', true);
              $("#orderStatus option[value='6']").prop('disabled', true);
              $("#orderStatus option[value='7']").prop('disabled', true);
              notavisibility ();
              extraOptionFields ();
            },
            "Nee": function ()
            {
              $.when(saveOrderStatus()).done(function (a1)
              {
                
                if (changedOrderStatus == 1)
                {
                  $('#uitvoeringen').load(encodeURI('orderuitvoeringListV2.php?orderid=' + $('#id').val() + '&selectedStatus=' + element.val()));
                  $("#uitvoeringen").slideDown("slow");
                  $('.tab-uitvoeringen').show();
                  $("#extraFondsOpties").slideDown("slow");
                  
                  newPrevValue = element.val();
                } else
                {
                  newPrevValue = prevValue;
                  element.val(prevValue);
                }
              });
              $(this).dialog('destroy');
              
              pane = $(".orderTabHolder .ui-tabs-panel:visible").attr('id');
              if ( pane !== 'uitvoeringen' )
              {
                $(".orderTabHolder").tabs('select', 'uitvoeringen');
              }
              
              $("#orderStatus option[value='1']").prop('disabled', true);
              $("#orderStatus option[value='6']").prop('disabled', true);
              $("#orderStatus option[value='7']").prop('disabled', true);
              notavisibility ();
              extraOptionFields ();
            },
            "Annuleren": function ()
            {
              newPrevValue = prevValue;
              element.val(prevValue);
              $(this).dialog('destroy');
              
            }
          }
        });
      } else {
        /** we hebben geen rechten om uitvoeringen toe te voegen **/
        $('<div style="padding: 10px; max-width: 500px; word-wrap: break-word;">Let op! De gewijzigde status wordt automatisch opgeslagen.' +
          '</div>').dialog({
          draggable: false,
          modal: true,
          resizable: false,
          width: 'auto',
          title: 'Status wijzigen',
          minHeight: 75,
          buttons: {
            "Doorgaan": function ()
            {
              $.when(saveOrderStatus()).done(function (a1)
              {
                if (changedOrderStatus == 1)
                {
                  $('#uitvoeringen').load(encodeURI('orderuitvoeringListV2.php?orderid=' + $('#id').val() + '&autoOpenNew=false&selectedStatus=' + element.val()));
                  $("#uitvoeringen").slideDown("slow");
                  $('.tab-uitvoeringen').show();
                  $("#extraFondsOpties").slideDown("slow");
                  newPrevValue = element.val();
                } else
                {
                }
              });
              $(this).dialog('destroy');
              
              $("#orderStatus option[value='1']").prop('disabled', true);
              $("#orderStatus option[value='6']").prop('disabled', true);
              $("#orderStatus option[value='7']").prop('disabled', true);
              notavisibility ();
              extraOptionFields ();
            },
            "Annuleren": function ()
            {
              newPrevValue = prevValue;
              element.val(prevValue);
              $(this).dialog('destroy');
            }
          }
        });
      }
      
      
    }
    else
    {
      $('#uitvoeringen').load(encodeURI('orderuitvoeringListV2.php?orderid=' + $('#id').val() + '&selectedStatus=' + element.val()));
      // $("#uitvoeringen").slideDown("slow");
      $('.tab-uitvoeringen').show();
      $("#extraFondsOpties").slideDown("slow");
      newPrevValue = element.val();
      notavisibility ();
      extraOptionFields ();
    }
  } else {
    $("#uitvoeringen").slideUp("slow");
    $('.tab-uitvoeringen').hide();
    newPrevValue = element.val();
  }
  
}

var currentRekeningValue = '';

function clientChanged() {
  currentRekeningValue = $('#rekening').val(); //get current value
  var setMemoriaal = null;
  if (
    $('#OrderuitvoerBewaarder').val() == 1 &&
    (
      $("input[name=orderSelectieType]:checked", ".orderForm").val() === "F" ||
      $("input[name=orderSelectieType]:checked", ".orderForm").val() === "X"
    )
  ) {
    var setMemoriaal = 0;
  }
  
  
  $('#depotbankMessage').hide();
  $.ajax({
    type: "GET",
    url: 'lookups/rekeningAfschriften.php',
    dataType: "json",
    // async: false,
    data: {
      type: 'fetchRekeningenList',
      form: {
        Client: $('#PortefeuilleSelectie').val(),
        Portefeuille: $('#portefeuille').val(),
        OrderuitvoerBewaarder: $('#OrderuitvoerBewaarder').val(),
        fonds: $('#fonds').val(),
        Memoriaal: 0,
        deposito: 0,
        setMemoriaal: setMemoriaal
      }
    },
    
    success: function (data, textStatus, jqXHR)
    {
      /** clear dropdown values **/
      if ( data.length != 0 ) {
        if(!$('#rekening').is(':disabled'))
        {
          $('#rekening').html('');
        }
        fullRekeningenList = data.fullAccounts;
        
        if ( $('#OrderuitvoerBewaarder').val() == 1 )
        {
          if (typeof fullRekeningenList.fullRekeningenList  !== "undefined") {
            $("#depobankValue").html(fullRekeningenList.fullRekeningenList.Rekening_Depotbank);
            $("#Depotbank").val(fullRekeningenList.fullRekeningenList.Rekening_Depotbank);
          } else {
            $firstRecord = fullRekeningenList[Object.keys(fullRekeningenList)[0]];
            $("#depobankValue").html($firstRecord.Rekening_Depotbank);
            $("#Depotbank").val($firstRecord.Rekening_Depotbank);
          }
          
          $('.rekeningField').show();
          $('#rekeningNrTonen').val('1')
        }
        
        if(!$('#rekening').is(':disabled'))  {
          // Bij meervoudige hier een check op depotbank uitvoeren om te controlleren of deze gelijk is
          if ( $('#OrderuitvoerBewaarder').val() == 1 && typeof Mform  !== "undefined" )
          {
            meervoudigeAccounts = [];
            
            // 1e order van de meervoudige
            if ( $('#orderdepotbank').val() == "" ) {
              meervoudigeAccounts = data.accounts;
            } else {
              $.each(fullRekeningenList, function (index, value) {
                if ( $('#orderdepotbank').val() == value['Depotbank'] ) {
                  meervoudigeAccounts.push(value['Rekening']);
                }
              });
            }
            
            if ( meervoudigeAccounts.length == 0 ) {
              triggerWrongDepotbank ();
            } else {
              if (typeof Mform  !== "undefined") {
                /** loop result set and append to dropdown **/
                $.each(meervoudigeAccounts, function (index, value) {
                  $('#rekening').append($('<option>').text(value).attr('value', value));
                });
              }
            }
            
          }
          else {
            /** loop result set and append to dropdown **/
            $.each(data.accounts, function (index, value) {
              $('#rekening').append($('<option>').text(value).attr('value', value));
            });
          }
        }
        if($('#tempRekening').is(':disabled')) {
        
        
        } else {
          if ( $( "#tempRekening" ).length ) {
            $('#tempRekening').html('');
            $('#rekening option').clone().appendTo('#tempRekening');
          }
        }
        
        if(!$('#rekening').is(':disabled'))
        {
          
          if ($('#rekening option[value="' + currentRekeningValue + '"]').length != 0)
          {
            $('#rekening').val(currentRekeningValue);
          } else
          {
            $("#rekening").val($("#rekening option:first").val());
          }
        }
        
        if (typeof rekeningValutaLock !== 'undefined' && $.isFunction(rekeningValutaLock)) {
          rekeningValutaLock();
        }
        $isChanged = true;
        if ( currentRekeningValue == $('#rekening').val()) {
          $isChanged = false;
        }
        changeRekening(null, $isChanged);
        
      }
    },
    error: function (jqXHR, textStatus, errorThrown)
    {
    }
  });
  
  // $('#rekening').val(currentRekeningValue);
}

if ($('#PortefeuilleSelectie').val() != '' && typeof $('#PortefeuilleSelectie').val() != 'undefined' ) {
  clientChanged();
}


function fillTransactionType(fondsType) {
  //$("#transactieSoort").val('');//leeg
  $("#transactieSoort option").prop('disabled', true);
  $("#transactieSoort option:first-child").prop('disabled', false);
  
  if (fondsType == 'OPT' || fondsType == 'HAND_OPT') {
    $("#transactieSoort option[value='AO']").prop('disabled', false);
    $("#transactieSoort option[value='VO']").prop('disabled', false);
    $("#transactieSoort option[value='AS']").prop('disabled', false);
    $("#transactieSoort option[value='VS']").prop('disabled', false);
  } else {
    $("#transactieSoort option[value='A']").prop('disabled', false);
    $("#transactieSoort option[value='V']").prop('disabled', false);
  }
}

function fondsChanged(fieldId)
{
  var inputDate = new Date();
  if($('#' + fieldId).val()  != '')
  {
    $.ajax({
      type: "GET",
      url: 'lookups/rekeningAfschriften.php',
      dataType: "json",
      // async: false,
      data: {
        type: 'getFondskoers',
        fonds: $('#' + fieldId).val(),
        date: $.datepicker.formatDate("dd-mm-yy", new Date())
      },
      success: function(data, textStatus, jqXHR)
      {
        var fondsDate = new Date(data.datum);
        valutaDate = new Date(fondsDate);
        
        $('#fonds-info').html('Eenheid: '+ data.Fondseenheid+' Valuta: '+ data.Valuta).addClass('label label-info');
        
        
        //var html= "Laatst bekende van d.d. :<b>"+myItem[1]+"</b> koers :<b>"+myItem[2]+ " "+myItem[3]+" ( "+myItem[0]+" )</b>";
        $fondsKoers = '-';
        if ( data.Koers ) {
          $fondsKoers = data.Koers;
        }
        $fondsData = '-';
        if ( data.datum ) {
          $fondsData = $.datepicker.formatDate('dd-mm-yy', new Date(data.datum));
        }
        
        $('#koersInfo').html('Laatst bekende van d.d. :<b> ' + $fondsData + '</b> koers :<b> ' + $fondsKoers + ' ' + data.Valuta + ' ( ' + $('#fonds').val() + ' )</b>');
        $('#fonds-koers-info').removeClass();
        if (inputDate.getTime() == fondsDate.getTime()) {
          $('#fonds-koers-info').addClass('label label-success');
        } else {
          $('#fonds-koers-info').addClass('label label-warning');
        }
        $('#fonds-koers-info').html($.datepicker.formatDate('dd-mm-yy', new Date(data.datum)));
        $('#Fondskoers').val(data.Koers);
        
        
        $("#koersLimietHidden").val(data.Koers);
        $("#fondseenheidHidden").val(data.Fondseenheid);
        
        $("#fondsValuta").val(data.Valuta)
        aantalChanged();
        
      },
      error: function(jqXHR, textStatus, errorThrown)
      {
      }
    });
    
    
    
    
    var fonds = $('#fonds').val();
    var portefeuille = $('#portefeuille').val();
    $('#fondsOwnedInfo').html('');
    $.ajax({
      type: 'GET',
      url: 'lookups/rekeningAfschriften.php',
      dataType: 'json',
      // async: false,
      data: {
        type: 'FondsAantal',
        portefeuille: portefeuille,
        fondsId: fonds,
        OrderuitvoerBewaarder: $('#OrderuitvoerBewaarder').val()
      },
      success: function(data, textStatus, jqXHR) {
        $subAantal = 0;
        
        if ( $('#OrderuitvoerBewaarder').val() == 1 ) {
          
          if ( typeof  (data.subAantal) != 'undefined'  && typeof  (data.subAantal[$('#Depotbank').val()]) != 'undefined' ) {
            $subAantal = data.subAantal[$('#Depotbank').val()]['aantal'];
          }
          
          $('#fondsOwnedInfo').html('In portefeuille : <strong>(Totaal: ' + parseFloat(data.totaalAantal) + ' | Bewaarder: ' + parseFloat($subAantal) + ')</strong>');
          
        } else {
          //      if ( data.aantal >= 0 )
//      {
          $('#fondsOwnedInfo').html('In portefeuille: <strong>(' + data.aantal + ')</strong>');
//      }
        }
        
        
      },
      error: function(jqXHR, textStatus, errorThrown) {
        result = jqXHR;
      }
    });
  }
  
  
};

function valutaChanged(valuta) {
  
  $('#fondsValuta').val(valuta);
  $.ajax({
    type: 'GET',
    url: 'lookups/rekeningAfschriften.php',
    dataType: 'json',
    data: {
      type: 'getExchangeRate',
      rekeningValuta: valuta,
      valuta: valuta,
      date: $.datepicker.formatDate("dd-mm-yy", new Date())
    },
    success: function(data, textStatus, jqXHR) {
      
      $('#valutaKoersHidden').val(data.valuta.Koers);
      
    },
    error: function(jqXHR, textStatus, errorThrown) {
      result = jqXHR;
    }
  });
};

var careOrderStatus = 0;
function careOrder (status) {
  careOrderStatus = status;
  
  $("#careOrder").removeAttr('checked');
  $("#initialCare").removeAttr('checked');
  if ( status == 1 ) {
    $("#careOrder").prop("checked", true);
    $("#initialCare").prop("checked", true);
  }
}

function careOrderChange () {
  
  if ($('#fixOrder').is(':checked')) {
    
    if ( ( $("input[name=orderSelectieType]:checked", ".orderForm").val() === "M" || $("input[name=orderSelectieType]:checked", ".orderForm").val() === "O" ) && $('#orderId').val() === '' ) {
      // $('.careOrderInput').show();
    } else {
      $('.careOrderInput').show();
    }
    if ( careOrderStatus == 1 || $('#careOrder').is(':checked') ) {
      $("#careOrder").prop("checked", true);
    } else {
      $("#careOrder").removeAttr('checked');
    }
  } else {
    $("#careOrder").removeAttr('checked');
    $('.careOrderInput').hide();
  }
}

function fixOrder (status,fixDefaultAan)
{
  if ( $("input[name=orderSelectieType]:checked", ".orderForm").val() === "M" || $("input[name=orderSelectieType]:checked", ".orderForm").val() === "O" )
  {
    
    if ($('#orderId').val() === '') {
      if (status === true) {
        if (fixDefaultAan == true) {
          $("#initialFix").prop("checked", true);
        } else {
          $("#initialFix").removeAttr('checked');
        }
        
        $("#initialFix").change();
        
        $('#canFixOrder').val(1);
        $(".fixOrderInput").show();
        //  $(".fixNietNodig").hide();
        $(".fixNodig").show();
      } else {
 
        $('#canFixOrder').val(0);
        $(".fixOrderInput").hide();
        $("#initialFix").prop("checked", false);
        $("#fixOrder").prop("checked", false);
        
        changeMfixOrder ();
        changeInitialFix ();
        
      }
    }
  }
  else
  {
    if (status === true)
    {
      if (fixDefaultAan == true)
      {
        $('.fixButton').show();
        $("#fixOrder").prop("checked", true);
        
        if($('#tempFixOrder').is(':disabled') || $('#initialValuesSet').val() == 1 ) {
        
        } else {
          $("#tempFixOrder").prop("checked", true);
        }
      }
      else
      {
        $("#fixOrder").removeAttr('checked');
        $('.fixButton').hide();
        if($('#tempFixOrder').is(':disabled') || $('#initialValuesSet').val() == 1 ) {
        
        } else {
          $("#tempFixOrder").removeAttr('checked');
        }
        
      }
     
      $(".fixOrderInput").show();
      $(".fixNietNodig").hide();
      $(".fixNodig").show();
      
      if ( $('#OrderuitvoerBewaarder').val() == 1 ) {
        $('.rekeningField').show();
      }
    }
    else
    {
      $("#fixOrder").prop("checked", false);
      if($('#tempFixOrder').is(':disabled') || $('#initialValuesSet').val() == 1) {
      } else {
        $("#tempFixOrder").prop("checked", false);
      }
      
      $('.fixButton').hide();
      $(".fixOrderInput").hide();
      $(".fixNietNodig").show();
      $(".fixNodig").hide();
    }
  }
}



/**
 * Bepalen of het rekening nr veld getoond wordt of niet
 * @param {var} Portefeuille
 */
function rekeningNrTonen(Portefeuille) {
  $('.rekeningField').hide();
  $('#rekeningNrTonen').val('0')
  $.ajax({
    url: 'ordersEditV2.php?getRekeningFieldStatus=1&portefeuille=' + Portefeuille + '&orderSelectieType=' + $("input[name=orderSelectieType]:checked", ".orderForm").val(),
    type: "GET",
    dataType: 'json',
    async: false,
    success: function (data, textStatus, jqXHR) {
      
      if (data.rekeningNrTonen == 1 || $('#OrderuitvoerBewaarder').val() == 1 ) {
        $('.rekeningField').show();
        $('#rekeningNrTonen').val('1')
      }
      
      $('#isAdviesRelatie').val(0);
      
      if (data.adviesRelatie == true) {
        $('#PortefeuilleSelectieLabel').parent().find('.adviceMsg').remove();
        $('#PortefeuilleSelectieLabel').parent().append(' <div class="adviceMsg label label-warning">Advies relatie</div> ');
        $('#isAdviesRelatie').val(1);
      } else {
        $('#PortefeuilleSelectieLabel').parent().find('.adviceMsg').remove();
        $('#isAdviesRelatie').val(0);
      }
      
      
      $('#fixTonen').val(data.fixTonen);
      // if( data.fixDefaultAan == true) {$("#fixOrder").prop("checked", true);}
      // else {  $("#fixOrder").removeAttr('checked');}
    }
  });
}


/**
 * Toggle display hidden boxes
 * @returns {undefined}
 *
 */
function toggleFormHolder() {
  $('.toggleFormHolder').addClass('floatR');
  
  var toggleFormContentDiv = $('.toggleFormHolder').parent().parent().find('.formContent');
  if ( toggleFormContentDiv.is(':visible') && ! $('.toggleFormHolder').hasClass('formHolderHide') ) {
    $('.toggleFormHolder').html('<img src="images/16/navigate_open.png" class="toggleClose" />');
  } else {
    $('.toggleFormHolder').html('<img src="images/16/navigate_close.png" class="toggleOpen" />');
  }
  
  if ($('.toggleFormHolder').hasClass('formHolderHide')) {
    $('.formHolderHide').parent().parent().parent().find('.formContent').slideUp();
  }
}
function setToggleFormHolderState (state) {
  $('.toggleFormHolder').addClass('floatR');
  
  if ( state == 'visible' ) {
    $('.toggleFormHolder').html('<img src="images/16/navigate_open.png" class="toggleClose" />');
  } else {
    $('.toggleFormHolder').html('<img src="images/16/navigate_close.png" class="toggleOpen" />');
  }
}

//
//$('.formHolderHide').html('<img src="images/16/navigate_close.png" class="toggleOpen" />');

$(document).on('click', '.toggleClose', function() {
  $(this).parent().parent().parent().find('.formContent').slideUp();
  $(this).parent().html('<img src="images/16/navigate_close.png" class="toggleOpen" />');
});

$(document).on('click', '.toggleOpen', function() {
  $(this).parent().parent().parent().find('.formContent').slideDown();
  $(this).parent().parent().find('.toggleFormHolder').html('<img src="images/16/navigate_open.png" class="toggleClose" />');
});

//init toggle form holder
toggleFormHolder();


function extraOptionFields () {
  $('.uitgevoerdFields').hide();
  $('#btnRecalculateNota').hide();
  if ( $('#orderStatus').val() >= 1 && $(document).find('#notaModule').val() == 1 ) {
    $('.uitgevoerdFields').show();
  }
  //$('#orderStatus').val() >= 2 &&
  if (  $(document).find('#notaModule').val() == 1 ) {
    $('#btnRecalculateNota').show();
  }
}

/** Nota knoppen tonen **/
function notavisibility () {
  $('.tab-notas').hide();
  
  if ( $('#orderStatus').val() >= 2 && $(document).find('#displayNotas').val() == 1 ) {
    $('.tab-notas').show();
  }
}




$(function() {
  
  $(document).on('click', '#btnCloseNota', function() {
    $('#notaEditHolder').dialog({});
    $('#notaEditHolder').dialog('close');
  });
  
  $(document).on('click', '#btnRecalculateNota', function () {
    //recalculateNota
    $.ajax({
      url: 'ordersEditV2.php?action=edit&' +
        'orderRegelId=' + $(document).find('#orderregelId').val() + '' +
        '&orderid=' + $(document).find('#orderid').val() + '' +
        '&notaValutakoers=' + $(document).find('#notaValutakoers').val() + '' +
        '&settlementdatum=' + $(document).find('#settlementdatum').val() + '' +
        '&voorkeursOrderReden=' + $(document).find('#voorkeursOrderReden').val() + '' +
        '&voorkeursPSET=' + $(document).find('#voorkeursPSET').val() + '' +
        '&voorkeursPSAF=' + $(document).find('#voorkeursPSAF').val() + '' +
        '&recalculateNota=1',
      type: "GET",
      success: function (data, textStatus, jqXHR) {
        var obj = JSON.parse(data);
        if (obj.calculatedData)
        {
          $.each(obj.calculatedData, function (field, fieldValue)
          {
            if(field=='notaMelding')
            {
              $('#notaMelding').html(fieldValue);
              $(document).find('#tab-extraOpties [id=btnRecalculateNota]').html(fieldValue);
              $('#btnRecalculateNota').html(fieldValue);
            }
            else if(field=='settlementdatum')
            {
              var currentvalue=$(document).find('#tab-extraOpties input[name=' + field + ']').val();
              
              if(currentvalue=='')
              {
                $(document).find('#tab-extraOpties input[name=' + field + ']').val(fieldValue);
              }
            }
            else
            {
              $(document).find('#notaEditHolder input[name=' + field + ']').val(fieldValue);
            }
          });
        }
      }
    });
  });
  
  var Notaform = 'form#notaForm';
  $(document).on('click', '#BtnSavenota', function () {
    var postData = $(Notaform).serializeArray();
    $.ajax({
      url: 'ordersEditV2.php?action=edit&orderRegelId=' + $(document).find('#orderregelId').val() + '&saveNota=1',
      type: "POST",
      dataType: 'json',
      data : postData,
      success: function (data, textStatus, jqXHR) {
        //var obj = JSON.parse(data);
        $('#notaEditHolder').dialog({});
        $('#notaEditHolder').dialog('close');
      }
    });
  });
  
  $(document).on('change', 'input[name=notaValutakoers], input[name=brutoBedrag], input[name=regelNotaValutakoers], input[name=kosten], input[name=brokerkosten], input[name=opgelopenRente]', function () {
    if ( $('input[name=id]').val() > 0 ) {
      berekenBedrag();
    }
  });
  
});

function berekenBedrag(bereken)
{
  if (typeof(bereken)==='undefined') bereken = true;
  //  document.getElementById('brutoBedrag').value = round(document.getElementById('aantal').value * document.getElementById('fondsFondseenheid').value * document.getElementById('notaFondskoers').value,2);
  
  if('{fondsValuta}'=='{rekValuta}')
  {
    valutaKoers=1;
    $('#regelNotaValutakoers').prop("readonly", true);
    
  }
  else if($('#regelNotaValutakoers').val() > 0)
  {
    valutaKoers= $('#regelNotaValutakoers').val();
  }
  else if($('#notaValutakoers').val() > 0)
  {
    valutaKoers=document.getElementById('notaValutakoers').value;
  }
  else
  {
    valutaKoers=$('#uitvoeringsValutaKoers').val();
  }
  $('#regelNotaValutakoers').val(valutaKoers);
  
  // $('#notaValutaKoersSpan').html(valutaKoers);
  $('#notaMelding').html('');
  if(bereken==true)
  {
    var kosten=parseFloat(document.getElementById('kosten').value) + parseFloat(document.getElementById('brokerkosten').value);
    var transaktieCodeCheck = document.getElementById('transactieSoort').value.substring(0,1);
    if(transaktieCodeCheck == 'A')
    {
      document.getElementById('nettoBedrag').value = round(((parseFloat(document.getElementById('brutoBedrag').value)+parseFloat(document.getElementById('opgelopenRente').value))*valutaKoers)+kosten,2);
    }
    else
    {
      document.getElementById('nettoBedrag').value = round(((parseFloat(document.getElementById('brutoBedrag').value)+parseFloat(document.getElementById('opgelopenRente').value))*valutaKoers)-kosten,2);
    }
  }
}

berekenBedrag(false);