<script language="JavaScript" type="text/javascript">
if (typeof jQuery != 'undefined') {
  $(document).ready(function(){
    {script_voet}
    
    $( ".AIRSdatepicker" ).datepicker({
        showOn: "button",
        buttonImage: "javascript/calendar/img.gif",//"images/datePicker.png",
        buttonImageOnly: true,
        dateFormat: "dd-mm-yy",
        dayNamesMin: ["Zo", "Ma", "Di", "Wo", "Do", "Vr", "Za"],
        monthNames: ["januari","februari","maart","april","mei","juni","juli","augustus","september","oktober","november","december"],
        monthNamesShort: [ "Jan", "Feb", "Mrt", "Apr", "Mei", "Jun", "Jul", "Aug", "Sep", "Okt", "Nov", "Dec" ],
        nextText: "volgende maand",
        prevText: "vorige maand",
        currentText: "huidige maand",
        changeMonth: true, 
        changeYear: true,
        yearRange: '2000:2050',
        closeText: "sluiten",
        showAnim: "slideDown",
        showButtonPanel: true,
        showOtherMonths: true,
        selectOtherMonths: true,
        numberOfMonths: 2,
        showWeek: true,
        firstDay: 1
    });
    
    $( ".AIRSdatepickerPreviousMonth" ).datepicker( "option", "showCurrentAtPos", 1);
  }); 
}


function setRecordErrorsDialog (formId, errors) {
  $.each(errors, function(field, fieldData) {
    $('#' + formId + ' [name=' + field + ']').toggleClass('input_error');
    $('#' + formId + ' [name=' + field + ']').parent().append('<span class="help-block with-errors">' + fieldData.message + '</span>');
  });
}


</script>