function updateScript()
{
  screenUpdate();
}

function berekenBedrag()
{
     
  document.getElementById('brutoBedrag').value = round(document.getElementById('aantal').value * document.getElementById('fondsEenheid').value * document.getElementById('fondsKoers').value,2);

  var kosten=parseFloat(document.getElementById('kosten').value) + parseFloat(document.getElementById('brokerkosten').value);
  
  var transaktieCodeCheck = document.getElementById('transactieSoort').value.substring(0,1);
  if(transaktieCodeCheck == 'A')
  {
    document.getElementById('nettoBedrag').value = round(((parseFloat(document.getElementById('brutoBedrag').value)+parseFloat(document.getElementById('opgelopenRente').value))*document.getElementById('valutakoers').value)+kosten,2);
  }
  else
  {
   document.getElementById('nettoBedrag').value = round(((parseFloat(document.getElementById('brutoBedrag').value)+parseFloat(document.getElementById('opgelopenRente').value))*document.getElementById('valutakoers').value)-kosten,2);
  }
}

function initScript()  // wat te doen onLoad
{
  screenUpdate();
}

function screenUpdate()   // algemene schermupdates
{

  try
  {
    document.getElementById('clientNaam').innerHTML = document.editForm.client.value;
    try
    { //alert(document.editForm.Depotbank.value);
      document.getElementById('DepotbankHTML').innerHTML = document.editForm.Depotbank.value;
      document.getElementById('ProfielHTML').innerHTML = document.editForm.Risicoklasse.value;
    }

  catch(e){}
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

    if(nKey==13)
    {
      if(document.activeElement.name == 'fondsCode')
      {
        select_fonds(document.editForm.fondsCode.value,600,400);
      }
      if( document.activeElement.name == 'portefeuille')
      {
        select_fonds(document.editForm.portefeuille.value,600,400);
      }
    }

  }
  catch(e){}
}

function getRek(myValue)
{
  //alert(myValue);
}

function lookupPort()
{
  select_port(document.editForm.portefeuille.value,600,400);


}

function testAndSubmit()
{
  if (document.getElementById('portefeuille') == "" || document.getElementById('aantal').value == "")
  {
    alert("portefeuille en aantal zijn verplichte velden");
  }
  else
  {
    document.editForm.adding.value=1;
    document.editForm.submit();
  }
}

function checkDepotbank()
{
  if(document.editForm.DepotbankOld.value == '')
  {
    document.editForm.DepotbankOld.value = document.editForm.Depotbank.value;
  }

  if(document.editForm.DepotbankOld.value != document.editForm.Depotbank.value)
  {
   alert("Per depotbank moet een nieuwe order aangemaakt worden. Depotbank van deze order is: "+document.editForm.DepotbankOld.value+". Nu geselecteerde depotbank: "+document.editForm.Depotbank.value );
   document.getElementById('portefeuille').value = '';
   document.editForm.client.value = '';
   document.editForm.rekeningnr.value = '';
   screenUpdate();
   return false;
  }
  else
  {
    return true;
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


function preSearch(theQuery,field,defaultValue)
{
  checkDepotbank();
	if(theQuery !== "")
	{
		var url = 'orderRegelsAjaxBackend.php?fld='+field+'&q=' + theQuery;
		xmlhttp.open('GET', url, true);
		xmlhttp.onreadystatechange = function()
		{
	    if(xmlhttp.readyState == 4 && xmlhttp.status == 200)
	    {
	      var outp = xmlhttp.responseText;
        removeOptions(field);
	      if (outp != "")
	      {
	        myData = outp.split("$");
	        myRow = myData[0].split("~");
	        var myItem = myRow[0].split('#');
          for (x=0;x<myRow.length;x++)
          {
            var myItem = myRow[x].split('#');
            appendOptionLast(myItem[0],myItem[1],field);
            if(defaultValue == myItem[1])
              document.getElementById(field).value=myItem[1];
          }
	      }
			}
		}
		xmlhttp.send(null);
	}
	screenUpdate();
}

function preSearchNew(theQuery,field,extra)
{
  checkDepotbank();
	if(theQuery !== "")
	{ 
		var url = 'orderRegelsAjaxBackend.php?fld='+field+'&q=' + theQuery + '&extra=' + extra;
		xmlhttp.open('GET', url, true);
		xmlhttp.onreadystatechange = function()
		{
	    if(xmlhttp.readyState == 4 && xmlhttp.status == 200)
	    {
	      var outp = xmlhttp.responseText;
	      if (outp != "")
	      {
	        myData = outp.split("$");
	        myRow = myData[0].split("~");
	        var myItem = myRow[0].split('#');
	        document.getElementById(field).value=myItem[1];
          //alert('opgehaald ' + field + ' ' + myItem[1]);
	      }
			}
		}
		xmlhttp.send(null);
	}
  screenUpdate();

}

function getKoersen()
{
		var url = 'orderRegelsAjaxBackend.php?fld=koersen&q=' + document.getElementById('handelsdag').value + "|" + document.getElementById('fonds').value+ "|" + document.getElementById('aantal').value + "|" + document.getElementById('transactieSoort').value;
		xmlhttp.open('GET', url, true);
		xmlhttp.onreadystatechange = function()
		{
	    if(xmlhttp.readyState == 4 && xmlhttp.status == 200)
	    {
	      var outp = xmlhttp.responseText;
	      if (outp != "")
	      {
	        myData = outp.split("$");
	        myRow = myData[0].split("~");
	        var myItem = myRow[0].split('#');
	        document.getElementById('fondsKoers').value = document.getElementById('gemiddeldePrijs').value;
	        document.getElementById('valutaKoers').value=myItem[1];
	        //if(document.getElementById('opgelopenRente').value == 0)
	        document.getElementById('opgelopenRente').value=myItem[2];
	      }
			}
		}
		xmlhttp.send(null);
	berekenBedrag();
	screenUpdate();
}

function appendOptionLast(key,value,field)
{
  var elOptNew = document.createElement('option');
  elOptNew.text = value;
  elOptNew.value = key;
  var elSel = document.getElementById(field);

  try
  {
    elSel.add(elOptNew, null); // standards compliant; doesn't work in IE
  }
  catch(ex)
  {
    elSel.add(elOptNew); // IE only
  }
}

function removeOptions(field)
{
  var elSel = document.getElementById(field);
  while (elSel.length > 0)
  {
    elSel.remove(0);
  }
}

/*
**  STOP AJAX filler voor selectbox
*/





