 ajaxSubmit('submit-form', 'conversie-form');

var templateHolder = $('#conversieTable').clone(true); 
 
$('#addtestbutton').on("click", function(){
  event.preventDefault();
  var str = $('#deposits table').length,
  newValue = parseInt(str,10) + 1;

  var newTemplate = templateHolder.clone();
  newTemplate.find('input')
    .each(function(){
      this.name = this.name.replace(/\[(\d+)\]/,'['+ newValue +']');
    })
    .end()
    .appendTo('#deposits');
});


$('#deposits').on('click', '.removeRow', function(event){
  event.preventDefault();
 $(this).parent().parent().parent().parent().remove();
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