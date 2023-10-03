var orderForm='bulkorders';

var $BankDepotCodes = {BankDepotCodes};
var $fonds = '';

function portefeuilleChanged () {
  bankDepotbankChanged();
}

function isinChanged (data) {
  if ( data !== undefined )
  {
    $fonds = data;
  }
  bankDepotbankChanged();
  aantalChanged();
}

$(function () {
  $('.fixOrderInput').hide();
  $('#extraFondsOpties').hide();

  $('#formulierOpslaan').on('click', function () {
    formulierOpslaan();
  });
});


$(document).on('change', '#aantal, #transactieSoort', function() {
  aantalChanged();
});


function formulierOpslaan()
{
  aantalChanged();
  document.editForm.submit();
}





