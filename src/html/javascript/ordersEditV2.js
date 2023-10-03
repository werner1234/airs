/** functies geupdate naar V2 **/

$(function () {
  //kleine vertraging zodat de datepicker disabled kan worden.
  setTimeout(function() {
    tijdsSoortChanged();
  }, 100);

});


function fondsOmschrijvingChange()
	{
	  alert('Bij het handmatig opgeven van een fondsomschrijving is geen automatische controle mogelijk.');
	  $('#fonds').val('');
	  $('#fondsCode').val('');
	}


function tijdslimietChange()
{
  if ( $('#tijdsLimiet').val() ) {
    var now = new Date();
    var d1 = (Math.floor(now.getTime()/86400000));
    var nowAndSixMonthsDate = new Date(new Date(now).setMonth(now.getMonth()+6));
    var nowAndSixMonthsInt = Math.floor(nowAndSixMonthsDate.getTime()/86400000);

    var dateParts=$('#tijdsLimiet').val().split("-");
    var formDate = new Date(dateParts[2], dateParts[1]-1, dateParts[0],12);
    var d2=(Math.floor(formDate.getTime()/86400000));

    if ( d1 > d2 ) {
      alert("Datum mag niet in het verleden liggen.");
      $('#tijdsLimiet').val('');
      return false;
    } else {
      if (d2 > getDateFromFormat("01-02-"+(now.getFullYear()+1),'dd-MM-yyyy') && d1 < getDateFromFormat("01-12-"+now.getFullYear(),'dd-MM-yyyy')) {
        alert('Datum moet voor 01-02-'+(now.getFullYear()+1)+' liggen.');
        $('#tijdsLimiet').val('');
        return false;
      } else if (d2 > nowAndSixMonthsInt) {
        alert('Datum moet voor '+nowAndSixMonthsDate.getDate()+'-'+(nowAndSixMonthsDate.getMonth()+1)+'-'+nowAndSixMonthsDate.getFullYear()+' liggen.');
        $('#tijdsLimiet').val('');
        return false;
      }
      tijdsSoortChanged();
      return true;
    }
  }
}

/**
 * @todo wordt dit nog gebruikt?
 * @returns {undefined}
 */
function tijdsSoortChanged()
{
  if ( $('#originalOrderStatus').val() < 1) {
    if( $('#tijdsSoort').val() == 'GTC')
    {
      $('#tijdsLimiet').val('');
      $('#tijdsLimiet').prop('readonly', true);
      $('#tijdsLimiet').datepicker('disable');
      $('#tijdsLimiet').addClass('notEditable');
    }
    else
    {
      $("#tijdsLimiet").prop("readonly", false);
      $("#tijdsLimiet").prop("disabled", false);
      $('#tijdsLimiet').datepicker('enable');
      $('#tijdsLimiet').removeClass('notEditable');
    }
  } else {
    $('#tijdsLimiet').datepicker('disable');
    $('#tijdsLimiet').addClass('notEditable');
  }
}


//function aantalChanged()
//{
//  if ( isNumber ($('#aantal').val()) ) {
//    if ( $('#giraleOrder').is(':checked') ) {
//      $('#orderWaarde').html("Orderbedrag:<b>" + round($('#aantal').val(), 2) + "</b> EUR");
//    } else {
//      $('#orderWaarde').html("Geschat orderbedrag: <b>"+
//              round(
//              ( $('#fondseenheidHidden').val() *
//              $('#valutaKoersHidden').val() *
//              $('#koersLimietHidden').val() *
//              $('#aantal').val()),2)
//              +"</b> EUR");
//    }
//  }
//}


function koersLimietChange()
{
  if( $('#koersLimietHidden').val() != '') {
    if( isNumber ($('#koersLimiet').val()) && isNumber ($('#koersLimietHidden').val()))
    {
     tmp = ($('#koersLimiet').val() / $('#koersLimietHidden').val()) *100;
     if(tmp < 90 || tmp > 110)
       alert("Limiet wijkt meer dan 10% van de laatst bekende koers af.");
    }
    else {
      alert('Geen getal opgegeven.');
    }
  }
}




/** oude functions voor v2 **/



function changeTransaction()
{
   tijdsSoortChanged();
  /*
 if(editForm.transactieType.value == 'B')
 {
   editForm.koersLimiet.value = '';
   editForm.koersLimiet.readOnly = true;
   document.getElementById("koersLimiet").style.background = '#DDDDDD';
 }
 else
 {
   editForm.koersLimiet.readOnly = false;
   document.getElementById("koersLimiet").style.background = '#FBFBFB';
 }
 if(editForm.transactieType.value == 'L')
 {
   getKoers();
 }
 */
}





Array.prototype.sum = function()
{
  if((!this.length))
  return 0;
  var totaal=0;
  for (i=0; i<=this.length; i++)
  {
     if(typeof this[i] == 'string')
     {
       this[i] = parseFloat(this[i]);
       if(isNumber(this[i]))
       {
        totaal = totaal + this[i];
       }
     }
  }
  return totaal;
};



function addUitvoering()
{
  newAantal= document.getElementById("UitoefenAantal").value;
  newPrijs = document.getElementById("UitoefenPrijs").value;
  newDatum = document.getElementById("UitoefenDatum").value;

  aantalString=document.getElementById("uitvoeringsAantal").value;
  aantalArray=aantalString.split("\n");
  huidigTotaalAantal=aantalArray.sum();

  if((huidigTotaalAantal) > document.getElementById("aantal").value - newAantal)
  {
    alert(huidigTotaalAantal + " + " + newAantal +' = meer dan '+ document.getElementById("aantal").value);
    document.getElementById("UitoefenAantal").value = document.getElementById("aantal").value - huidigTotaalAantal;
    return 0;
  }

  document.getElementById("uitvoeringsAantal").value = newAantal + "\n" + document.getElementById("uitvoeringsAantal").value ;
  document.getElementById("uitvoeringsPrijs").value = newPrijs + "\n" + document.getElementById("uitvoeringsPrijs").value ;
  document.getElementById("uitvoeringsDatum").value = newDatum + "\n" + document.getElementById("uitvoeringsDatum").value;



}




function strpos (haystack, needle, offset)
{
  var i = (haystack+'').toLowerCase().indexOf( (needle+'').toLowerCase(), offset ); // returns -1
  return i >= 0 ? i : false;
}


function strlen(strVar)
{
  return(strVar.length)
}


function removeUitvoering()
{
  aantalString=document.getElementById("uitvoeringsAantal").value;
  prijsString=document.getElementById("uitvoeringsPrijs").value;
  datumString=document.getElementById("uitvoeringsDatum").value;

  aantalString=aantalString.substring(strpos(aantalString,"\n")+1,strlen(aantalString)) ;
  document.getElementById("uitvoeringsAantal").value = aantalString;

  prijsString=prijsString.substring(strpos(prijsString,"\n")+1,strlen(prijsString)) ;
  document.getElementById("uitvoeringsPrijs").value=prijsString;

  datumString=datumString.substring(strpos(datumString,"\n")+1,strlen(datumString)) ;
  document.getElementById("uitvoeringsDatum").value=datumString;
}


function checkPage()
{
  if(editForm.laatsteStatus.value == 2)
  {
    return true; //Tijdelijk de controlle uitgezet.
    if(document.getElementById("uitvoeringsPrijs"))
    {
      if(!isNumber(editForm.uitvoeringsPrijs.value))
      {
        alert("Uitvoeringsprijs moet een getal zijn.");
        return false;
      }
      if(editForm.uitvoeringsPrijs.value <= 0.0)
      {
        alert("Uitvoeringsprijs moet gevuld zijn.");
        return false;
      }
      if(editForm.uitvoeringsDatum.value == '')
      {
        alert("Uitvoeringsdatum moet gevuld zijn.");
        return false;
      }
    }
  }
  return true;

}



function isNumber( value )
{
return isFinite( (value * 1.0) );
}


function fondsSelected()
{
  getKoers();
}



function setClass()
{
  switch(document.getElementById("transactieSoort").value)
  {
  case "V":
  case "AS":
  case "VS":
//    document.getElementById("transAct").className="rood";
//    $('#transAct').removeClass('label-success');
//    $('#transAct').addClass('label-danger');
    break;
  default:
//    document.getElementById("transAct").className="groen";
//    $('#transAct').addClass('label-success');
//    $('#transAct').removeClass('label-danger');

  }
}


/*
**  START AJAX filler voor selectbox
*/

var xmlhttp = false;

if (window.XMLHttpRequest)
{
  xmlhttp = new XMLHttpRequest();
  xmlhttp.overrideMimeType('text/xml');
}
else if (window.ActiveXObject)
{
  xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
}



/*
**  STOP AJAX filler voor selectbox
*/


function getDateFromFormat(val,format)
{
	val=val+"";
	format=format+"";
	var i_val=0;
	var i_format=0;
	var c="";
	var token="";
	var token2="";
	var x,y;
	var now=new Date();
	var year=now.getYear();
	var month=now.getMonth()+1;
	var date=1;
	var hh=now.getHours();
	var mm=now.getMinutes();
	var ss=now.getSeconds();
	var ampm="";


	while (i_format < format.length) {
		// Get next token from format string
		c=format.charAt(i_format);
		token="";
		while ((format.charAt(i_format)==c) && (i_format < format.length)) {
			token += format.charAt(i_format++);
			}
		// Extract contents of value based on format token
		if (token=="yyyy" || token=="yy" || token=="y") {
			if (token=="yyyy") { x=4;y=4; }
			if (token=="yy")   { x=2;y=2; }
			if (token=="y")    { x=2;y=4; }
			year=_getInt(val,i_val,x,y);
			if (year==null) { return 0; }
			i_val += year.length;
			if (year.length==2) {
				if (year > 70) { year=1900+(year-0); }
				else { year=2000+(year-0); }
				}
			}
		else if (token=="MMM"||token=="NNN"){
			month=0;
			for (var i=0; i<MONTH_NAMES.length; i++) {
				var month_name=MONTH_NAMES[i];
				if (val.substring(i_val,i_val+month_name.length).toLowerCase()==month_name.toLowerCase()) {
					if (token=="MMM"||(token=="NNN"&&i>11)) {
						month=i+1;
						if (month>12) { month -= 12; }
						i_val += month_name.length;
						break;
						}
					}
				}
			if ((month < 1)||(month>12)){return 0;}
			}
		else if (token=="EE"||token=="E"){
			for (var i=0; i<DAY_NAMES.length; i++) {
				var day_name=DAY_NAMES[i];
				if (val.substring(i_val,i_val+day_name.length).toLowerCase()==day_name.toLowerCase()) {
					i_val += day_name.length;
					break;
					}
				}
			}
		else if (token=="MM"||token=="M") {
			month=_getInt(val,i_val,token.length,2);
			if(month==null||(month<1)||(month>12)){return 0;}
			i_val+=month.length;}
		else if (token=="dd"||token=="d") {
			date=_getInt(val,i_val,token.length,2);
			if(date==null||(date<1)||(date>31)){return 0;}
			i_val+=date.length;}
		else if (token=="hh"||token=="h") {
			hh=_getInt(val,i_val,token.length,2);
			if(hh==null||(hh<1)||(hh>12)){return 0;}
			i_val+=hh.length;}
		else if (token=="HH"||token=="H") {
			hh=_getInt(val,i_val,token.length,2);
			if(hh==null||(hh<0)||(hh>23)){return 0;}
			i_val+=hh.length;}
		else if (token=="KK"||token=="K") {
			hh=_getInt(val,i_val,token.length,2);
			if(hh==null||(hh<0)||(hh>11)){return 0;}
			i_val+=hh.length;}
		else if (token=="kk"||token=="k") {
			hh=_getInt(val,i_val,token.length,2);
			if(hh==null||(hh<1)||(hh>24)){return 0;}
			i_val+=hh.length;hh--;}
		else if (token=="mm"||token=="m") {
			mm=_getInt(val,i_val,token.length,2);
			if(mm==null||(mm<0)||(mm>59)){return 0;}
			i_val+=mm.length;}
		else if (token=="ss"||token=="s") {
			ss=_getInt(val,i_val,token.length,2);
			if(ss==null||(ss<0)||(ss>59)){return 0;}
			i_val+=ss.length;}
		else if (token=="a") {
			if (val.substring(i_val,i_val+2).toLowerCase()=="am") {ampm="AM";}
			else if (val.substring(i_val,i_val+2).toLowerCase()=="pm") {ampm="PM";}
			else {return 0;}
			i_val+=2;}
		else {
			if (val.substring(i_val,i_val+token.length)!=token) {return 0;}
			else {i_val+=token.length;}
			}
		}


	// If there are any trailing characters left in the value, it doesn't match
	if (i_val != val.length) { return 0; }
	// Is date valid for month?
	if (month==2) {
		// Check for leap year
		if ( ( (year%4==0)&&(year%100 != 0) ) || (year%400==0) ) { // leap year
			if (date > 29){ return 0; }
			}
		else { if (date > 28) { return 0; } }
		}
	if ((month==4)||(month==6)||(month==9)||(month==11)) {
		if (date > 30) { return 0; }
		}
	// Correct hours value
	if (hh<12 && ampm=="PM") { hh=hh-0+12; }
	else if (hh>11 && ampm=="AM") { hh-=12; }
	var newdate=new Date(year,month-1,date,hh,mm,ss);
	return newdate.getTime();
	}

	function _isInteger(val) {
	var digits="1234567890";
	for (var i=0; i < val.length; i++) {
		if (digits.indexOf(val.charAt(i))==-1) { return false; }
		}
	return true;
	}
function _getInt(str,i,minlength,maxlength) {
	for (var x=maxlength; x>=minlength; x--) {
		var token=str.substring(i,i+x);
		if (token.length < minlength) { return null; }
		if (_isInteger(token)) { return token; }
		}
	return null;
	}







