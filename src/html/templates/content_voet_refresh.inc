<div id="dwindow" style="position:absolute;background-color:#DDDDDD;cursor:hand;left:0px;top:0px;display:none" onMousedown="initializedrag(event)" onMouseup="stopdrag()" onSelectStart="return false">
	<div align="right" style="width:100%;background-color:Silver;border-top: 1px solid black;border-right: 1px solid black;border-left: 1px solid black;">
		<img src="images/16/window_resize.gif" id="maxname" onClick="maximize()"><img src="images/16/window_close.gif" onClick="closeit()">
	</div>
	<div id="dwindowcontent" style="height:100%; width:100%;" >
		<iframe id="cframe" src="" width="100%" height="100%" style="border:1px solid black;"></iframe>
	</div>
</div>

<script language="JavaScript" type="text/javascript">
if (typeof jQuery != 'undefined') {
  $(document).ready(function(){
    {script_voet}
    
    $( ".AIRSdatepicker" ).datepicker({
        showOn: "button",
       // buttonImage: "javascript/calendar/img.gif",//"images/datePicker.png",
  
        buttonText: "<i class='fa fa-calendar'></i>",
        // buttonImageOnly: true,
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
    $( ".AIRSdatepickerOneMonth" ).datepicker( "option", "numberOfMonths", 1);
  });
}


function placeFocus()
{
 try
 {
   
   if(parent.frames['navigatie'].document.searchForm.selectie.type == "text")
   {//alert('focus');
     parent.frames['navigatie'].document.searchForm.selectie.focus();
   }
 }
 catch(err)
 {
   if (document.forms.length > 0)
	 {
		var field = document.forms[0];
		for (i = 0; i < field.length; i++)
		{
      {eigenFocus}
			if(field.elements[i].type)
			{
				if ((field.elements[i].type == "text") || (field.elements[i].type == "textarea") || (field.elements[i].type.toString().charAt(0) == "s"))
				{
				  try{document.forms[0].elements[i].focus(); break;} catch(err) { }
				}
			}
   	}
	 }
 }
}

/** test voor refresh submenu en navigatie **/
if ( typeof everythingLoaded === 'undefined' || everythingLoaded === null )  {
  var everythingLoaded = setInterval(function() {
    if (/loaded|complete/.test(document.readyState)) {
      clearInterval(everythingLoaded);

      if (parent.frames['submenu']) {
        parent.frames['submenu'].location.href = "{path}submenu.php?random=" + Math.floor((Math.random() * 10000) + 1);
      }
      if (parent.frames['navigatie']) {
        parent.frames['navigatie'].location.href = "{path}navigatie.php?random=" + Math.floor((Math.random() * 10000) + 1);
      }
      setTimeout('placeFocus();', 800);
      return true;
    }
  }, 10 );
}




//try{parent.tableIdFF=editForm.id.value;}catch(err) { }
try{parent.tableIdFF=$("[name=id]").val();}catch(err) { }

</script>
<!--
  coding  : AE-ICT http://www.aeict.nl
  versie  : {title} {versie}
-->
</body>
</html>