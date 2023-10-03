var focusveld = '';
var ajax = new Array();

function checkFondsAantal()
{
  fonds=document.editForm.Fonds.value;
  rekening=document.editForm.Rekening.value;
  boekDatum=document.editForm.Boekdatum.value;
  transactieAantal=parseFloat(document.editForm.Aantal.value)+0;
  Transactietype=document.editForm.Transactietype.value;
  if(fonds.length>0)
  {
	  var index = ajax.length;

	  ajax[index] = new sack();
	  ajax[index].returnValue='FondsAantal';
	  ajax[index].requestFile = 'lookups/ajaxLookup.php?module=FondsAantal&query='+rekening+'|'+fonds+'|'+boekDatum+'';	// Specifying which file to get
	  ajax[index].onCompletion = function()
    {
      var tmp=ajax[index].response.split('\t');
      var aantal=parseFloat(tmp[0])+0;
      var koers=parseFloat(stripCharacter(tmp[1],'\n'))+0;
      html='Aantal in portefeuille:(<b>'+aantal+'</b>). Laatste koers:(<b>'+koers+'</b>)';

      if(Transactietype != 'V/O')
      {
 	     if((Transactietype == 'V'||Transactietype=='V/S')&&transactieAantal >0){transactieAantal=transactieAantal*-1;  }
	       aantal=aantal+transactieAantal;
	     if(aantal < 0) {alert('Nieuwe fondsaantal:'+round(aantal,4))};
      }
      
      
      document.getElementById('koersInfo').innerHTML =html;
	  };
	  ajax[index].onError = function(){ alert('Ophalen fondsaantal mislukt.') };
    ajax[index].runAJAX();		// Execute AJAX function
	  }
}

function getGrootboekFondsGebruik(sel)
{
	var grootboek = sel;//sel.options[sel.selectedIndex].value;
	if(grootboek.length>0){
		var index = ajax.length;
		ajax[index] = new sack();
		ajax[index].returnValue='GrootboekFondsGebruik';
		ajax[index].requestFile = 'lookups/ajaxLookup.php?module=FondsGebruik&query='+grootboek;	// Specifying which file to get
		ajax[index].onCompletion = function(){ setFondsenStatus(index) };	// Specify function that will be executed after file has been found
		ajax[index].onError = function(){ alert('Ophalen fondsgebruik uit grootboekrekeningen mislukt.') };
		ajax[index].runAJAX();		// Execute AJAX function
	}
}

function stripCharacter(words,character)
{
//documentation for this script at http://www.shawnolson.net/a/499/
  var spaces = words.length;
  for(var x = 1; x<spaces; ++x){
   words = words.replace(character, "");
 }
return words;
}

function setFondsenStatus(index)
{
 	var	GrootboekFondsGebruik = stripCharacter(ajax[index].response,'\t\n');

  if(document.editForm.Grootboekrekening.value == 'FONDS')
	{
		document.editForm.Transactietype.disabled = false;
		document.editForm.Fonds.disabled 					= false;
		document.editForm.Fondskoers.disabled			= false;
		document.editForm.Aantal.disabled 				= false;
		document.editForm.Bewaarder.disabled 			= false;
		//document.editForm.Fonds.focus();
    document.getElementById('fondsLookup').focus();
	}
  else
  {
    document.editForm.Transactietype.disabled = true;
		document.editForm.Fondskoers.disabled 		= true;
		document.editForm.Fondskoers.value        = 0;
		document.editForm.Aantal.disabled 				= true;
		document.editForm.Aantal.value 				    = 0;
		document.editForm.Bewaarder.disabled 			= true;

    if(GrootboekFondsGebruik == '1')
    {
      document.editForm.Fonds.disabled = false;
      //document.editForm.Fonds.focus();
      document.getElementById('fondsLookup').focus();
    }
    else
    {
      document.editForm.Fonds.value='';
      document.editForm.Fonds.disabled 	= true;
    }
  }
}

function doOnload()
{
	grootboekChanged();
	selectAll(document.editForm.Volgnummer);
}

function setBedrag(field)
{

	if(	field == 'Debet' &&
			document.editForm.Debet.value != '0' &&
			document.editForm.Debet.value != '' )
	{
		if(checkNumber(document.editForm.Debet))
		{
			document.editForm.Bedrag.value = -1 * mutatieBedrag(document.editForm.Debet.value);
			document.editForm.Credit.value = '';
			document.editForm.Bedrag.value = round(document.editForm.Bedrag.value,2);
			document.editForm.Debet.value = round(document.editForm.Debet.value,2);
		}
	}
	else if(field == 'Credit' && document.editForm.Credit.value != '0' && document.editForm.Credit.value != '' )
	{
		if(checkNumber(document.editForm.Credit))
		{
			document.editForm.Bedrag.value = mutatieBedrag(document.editForm.Credit.value);
			document.editForm.Debet.value = '';
			document.editForm.Bedrag.value = round(document.editForm.Bedrag.value,2);
			document.editForm.Credit.value = round(document.editForm.Credit.value,2);
		}
	}

	//alert('setBedrag');
	//document.editForm.Credit.focus();
}

function calculate()
{
	var res = 0;
	if(document.editForm.Grootboekrekening.value == 'FONDS')
	{
		with(document.editForm) {
			if(checkNumber(Aantal) && checkNumber(Fondskoers))
			{
				var res = Aantal.value * Fondskoers.value * Fondskoerseenheid.value;
				var txt = 'Aantal (' + Aantal.value + ') * Fondskoers (' + Fondskoers.value + ') * Fondskoerseenheid (' + Fondskoerseenheid.value + ')  = ' + res;
				//alert(txt);
			}
			else
			{
				res = 0;
			}
		}
	}
	return res;
}

function mutatieBedrag(bedrag)
{
 	var totaal;
 	var valuta;
 	var rekeningValuta;

	if(checkNumber(document.editForm.Valutakoers))
	{
		if(document.editForm.RekeningValuta.value != document.editForm.Valuta.value)
		{
			valuta = document.editForm.Valutakoers.value;
			rekeningValuta = document.editForm.RekeningValutakoers.value;
		}
		else
		{
			valuta = 1;
			rekeningValuta = 1;
		}
		totaal = (valuta * bedrag) / rekeningValuta;
	}
	else
	{
		totaal = 0;
	}
	return round(totaal,2);
}

function buildQueryArray(theFormName) {
  var theForm = document.forms[theFormName];
  var qs = new Object();
  for (e=0;e<theForm.elements.length;e++) {
  	if(!theForm.elements[e].name)
  	{
  	}
  	else if(	theForm.elements[e].name!='' )
  	{
    		qs[theForm.elements[e].name] = theForm.elements[e].value;
    }
    }
  return qs;
}

function grootboekChanged()
{
  getGrootboekFondsGebruik(document.editForm.Grootboekrekening.value);
}


function dump(arr,level)
{
var dumped_text = "";
if(!level) level = 0;

//The padding given at the beginning of the line.
var level_padding = "";
for(var j=0;j<level+1;j++) level_padding += "    ";

if(typeof(arr) == 'object') { //Array/Hashes/Objects
 for(var item in arr) {
  var value = arr[item];

  if(typeof(value) == 'object') { //If it is an array,
   dumped_text += level_padding + "'" + item + "' ...\n";
   dumped_text += dump(value,level+1);
  } else {
   dumped_text += level_padding + "'" + item + "' => \"" + value + "\"\n";
  }
 }
} else { //Stings/Chars/Numbers etc.
 dumped_text = "===>"+arr+"<===("+typeof(arr)+")";
}
return dumped_text;
}



function valutaChanged()
{
  if(document.editForm.RekeningValuta.value == 'EUR' && document.editForm.Valuta.value == 'EUR')
  {
    	document.editForm.Valutakoers.value = '1.000000';
	    document.editForm.RekeningValutakoers.value = '1.000000';
	    document.editForm.Valutakoers.readOnly = true;
	    document.editForm.RekeningValutakoers.readOnly = true;

  }
  else
  {
    document.editForm.Valutakoers.readOnly = false;
    document.editForm.RekeningValutakoers.readOnly = false;
	  jsrsExecute("selectRS.php", setValutakoers, "getValutakoers",	buildQueryArray('editForm'), false);
  }
  document.editForm.RekeningValutakoers.focus();
}

function fondsChanged()
{
  if(document.editForm.id.value < 1)
  {
    if(document.editForm.Grootboekrekening.value == 'FONDS')
    {
	   jsrsExecute("selectRS.php", setKoers, "getFondskoers",buildQueryArray('editForm'), false);
    }
  }

  checkFondsAantal();
}

function setValutakoers(valueTextStr)
{
	var aOptionPairs = valueTextStr.split('|');
	document.editForm.Valutakoers.value = aOptionPairs[0];
	document.editForm.RekeningValutakoers.value = aOptionPairs[1];
	selectAll(document.editForm.Valutakoers);
}

function setKoers(valueTextStr)
{
  var aOptionPairs = valueTextStr.split('|');
	document.editForm.Fondskoers.value = aOptionPairs[0];
	document.editForm.Valuta.value = aOptionPairs[1];
	document.editForm.Fondskoerseenheid.value = aOptionPairs[2];
	//selectAll(document.editForm.Fondskoers);
}

// Keys afvangen
var ie4 = (document.all)? true:false;
var ns4 = !ie4;

var keySet = false;
var ctrlKey = false;
function keyDown(e)
{
  if (ns4)
  {
  	var nKey=e.which;
  	ctrlKey = e.ctrlKey;
  }
  if (ie4)
  {
  	var nKey=event.keyCode;
  	ctrlKey = event.ctrlKey;
  }

  if(keySet || ie4)
  {
		command(nKey);
		keySet = false;
  }
  else
  {
      keySet = true;
  }
}

function f9pressed()
{
 
  
  	if(focusveld == 'Debet')
		{
				document.editForm.Debet.value = calculate();
				document.editForm.Bedrag.value = -1 * (document.editForm.Debet.value);
				setBedrag('Debet');
				selectAll(document.editForm.Debet);
		}
		else if(focusveld == 'Credit')
		{
			document.editForm.Credit.value = calculate();
			document.editForm.Bedrag.value = document.editForm.Credit.value;
			setBedrag('Credit');
			selectAll(document.editForm.Credit);
		}
		else if(focusveld == 'Valutakoers')
		{
			if(checkNumber(document.editForm.Valutakoers))
			{
				document.editForm.Valutakoers.value = 1 / document.editForm.Valutakoers.value;
			}
		}
    focusveld='';
}

function command(nKey)
{
	//alert(nKey);
	// control keys
	//alert(nKey);
	//F9 = 120
	// insert == 45

	if(nKey==45)
	{
		// volgende afschrift.
		// check of het afschrift klaar is.
		if(document.afschriftForm.mutatieVerschil.value == 0)
		{
			//insert afschrift
			document.location = document.editForm.rekeningafschriftenEdit.value + "?action=new&memoriaal=" + document.editForm.aMemoriaal.value;
		}
		else
		{
			alert("Er is nog een mutatie verschil van " + document.afschriftForm.mutatieVerschil.value);
		}
	}

	if(nKey==13)
	{
		return false;
	}

	if(nKey==120)
	{
		// ctrl-X
    f9pressed();
	}

	// normale Keys
  if(nKey==121)
  {
    submitForm();
  }
}



document.onkeydown = keyDown;
if (ns4) document.captureEvents(Event.KEYDOWN);