function updateScript()
{
  screenUpdate();
}

function initScript()  // wat te doen onLoad
{
  screenUpdate();

}

function screenUpdate()   // algemene schermupdates
{
//alert();
  //document.getElementById('clientNaam').innerHTML = document.editForm.client.value;
}

function getRek(myValue)
{
  //alert(myValue);
}

function changeTransaction()
{
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


}

function initPage()
{

  if(document.getElementById("transactieType"))
  {
   getKoers();
   tijdsSoortChanged();
  }
   laatsteStatusChange();

}

function tijdslimietChange()
{
  if(document.getElementById("f-calendar-field-1"))
  {
  var now=new Date();
  var d1=now.getTime();
  var nowAndSixMonthsDate=new Date(new Date(now).setMonth(now.getMonth()+6));
  var nowAndSixMonthsInt=nowAndSixMonthsDate.getTime();
  var d2=getDateFromFormat(document.getElementById("f-calendar-field-1").value,'dd-MM-yyyy');
  if (d1 > d2)
   {
     alert("Datum moet in de toekomst liggen.");
     document.getElementById("f-calendar-field-1").value = '';
     return false;
   }
   else
   {
     if(d2 > getDateFromFormat("01-02-"+(now.getFullYear()+1),'dd-MM-yyyy') && d1 < getDateFromFormat("01-12-"+now.getFullYear(),'dd-MM-yyyy'))
     {
       alert('Datum moet voor 01-02-'+(now.getFullYear()+1)+' liggen.');
       document.getElementById("f-calendar-field-1").value = '';
       return false;
     }
     else if(d2 > nowAndSixMonthsInt)
     {
       alert('Datum moet voor '+nowAndSixMonthsDate.getDate()+'-'+(nowAndSixMonthsDate.getMonth()+1)+'-'+nowAndSixMonthsDate.getFullYear()+' liggen.');
       document.getElementById("f-calendar-field-1").value = '';
        return false;
     }

     tijdsSoortChanged();
      return true;
   }
  }
}

function koersLimietChange()
{
  if(editForm.koersLimietHidden.value != '')
  {
    if(isNumber(editForm.koersLimiet.value ) && isNumber(editForm.koersLimietHidden.value))
    {
     tmp = (editForm.koersLimiet.value / editForm.koersLimietHidden.value) *100;
     if(tmp < 90 || tmp > 110)
       alert("Limiet wijkt meer dan 10% van de laatst bekende koers af.");
    }
    else
    alert('Geen getal opgegeven.');

  }

}

function laatsteStatusChange()
{
  if(document.getElementById("uitoefenAantalDi"))
  {
    if(editForm.laatsteStatus.value >= 2)
    {
       document.getElementById("uitoefenAantalDi").style.visibility="visible";
    }
    else
    {
       document.getElementById("uitoefenAantalDi").style.visibility="hidden";
    }
  }
}



function trim(value)
{
  value = value.replace(/^s+|s+$/,'');
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

function aantalChanged()
{
  if(isNumber(editForm.aantal.value)) 
  {
    document.getElementById('orderWaarde').innerHTML = "Geschat orderbedrag:<b>"+round((editForm.fondseenheidHidden.value*editForm.valutaKoersHidden.value*editForm.koersLimietHidden.value*editForm.aantal.value),2)+"</b> EUR";   
  }
}

function isNumber( value )
{
return isFinite( (value * 1.0) );
}


function fondsSelected()
{ 
  getKoers();
}

function tijdsSoortChanged()
{
  if(document.getElementById("f-calendar-field-1"))
  {
    if(document.getElementById("tijdsSoort").value == 'GTC')
    {
      document.getElementById("f-calendar-field-1").value = '';
      document.getElementById("f-calendar-field-1").readOnly = true;
      document.getElementById("f-calendar-field-1").style.background = '#DDDDDD';
    }
    else
    {
      document.getElementById("f-calendar-field-1").readOnly = false;
      document.getElementById("f-calendar-field-1").style.background = '#FBFBFB';
      document.getElementById("f-calendar-field-1").disabled = false;
    }
  }
  else if(document.getElementById("tijdsSoort").value == 'Tot annulering')
  {
    document.getElementById("tijdsLimiet").value = '';
    document.getElementById("tijdsLimiet").style.background = '#DDDDDD';
  }

}

function setClass()
{
  switch(document.getElementById("transactieSoort").value)
  {
  case "V":
  case "AS":
  case "VS":
    document.getElementById("transAct").className="rood";
    break;
  default:
    document.getElementById("transAct").className="groen";

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

function getKoers()
{
  theQuery = encodeURIComponent(document.editForm.fonds.value);
  try
  {
    theQuery = theQuery + '|' + document.getElementById('portefeuille').value;
  } catch(err) { }

	if(theQuery !== "")
	{
		var url = 'ordersAjaxBackend.php?q=' + theQuery;
    
		xmlhttp.open('GET', url, true);
		xmlhttp.onreadystatechange = function()
		{
	    if(xmlhttp.readyState == 4 && xmlhttp.status == 200)
	    {
	      var outp = xmlhttp.responseText;
	      if (outp != "")
	      {
	        myRow = outp.split("~");
          for (x=0;x<myRow.length;x++)
          {
            var myItem = myRow[x].split('#');

            var html= "Laatst bekende van d.d. :<b>"+myItem[1]+"</b> koers :<b>"+myItem[2]+ " "+myItem[3]+" ( "+myItem[0]+" )</b>";

            try
            {
              if(myItem[4])
              {
                html = html + ' en aantal in portefeuille :<b>' + myItem[5] + '</b>';
              }
            } catch(err) { }
            document.getElementById('koersInfo').innerHTML =html;
            editForm.koersLimietHidden.value=myItem[4];
            editForm.valutaKoersHidden.value=myItem[6];
            editForm.fondseenheidHidden.value=myItem[7];
            if(editForm.transactieType.value == 'L')
            {
              if(editForm.koersLimiet.value == '')
                editForm.koersLimiet.value=myItem[4];
            }
            aantalChanged();
          }
	      }
			}
		}
		xmlhttp.send(null);
	}
	screenUpdate();
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


	function fondsOmschrijvingChange()
	{
	  alert('Bij het handmatig opgeven van een fondsomschrijving is geen automatische controle mogelijk.');
	  editForm.fonds.value='';
	  editForm.fondsCode.value='';
	}




