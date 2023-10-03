


function screenUpdate()   // algemene schermupdates
{
  try
  {
  var trigger = "";
  if(document.editForm.memo.value)
  {
  if (document.editForm.memo.value.length != 0)
  {
    trigger = trigger + "Memo " + document.editForm.memo.value.length + " tekens.";
  }
  else
  {
    trigger = "Memo";
  }
  var button=document.getElementById('tabbutton3');
  if(button.value.substring(0, 4) == 'Memo')
  {
    replaceButtonText('tabbutton3', trigger);
  }
  }
  //document.getElementById('relatieInfo').innerHTML = document.editForm.naam.value  + ', ' +
  //                                                   document.editForm.plaats.value;
  } catch(e){}
}

function huidigeTotaal()
{
  document.editForm.huidigesamenstellingTotaal.value = round(eval(document.editForm.huidigesamenstellingAandelen.value) +
                                                             eval(document.editForm.huidigesamenstellingObligaties.value) +
                                                             eval(document.editForm.huidigesamenstellingOverige.value) +
                                                             eval(document.editForm.huidigesamenstellingLiquiditeiten.value));
}
function toonProspectStatus()
{
  try
  {
    var theForm = document.editForm.elements, z = 0;
    var counter = 0;
    var setChecked = false;
    for(z=0; z<theForm.length;z++)
    {
     if(theForm[z].type == 'checkbox')
     {
       if(theForm[z].name == 'prospect')
       {
         document.getElementById('prospectStatusSpan').style.zIndex = -1;
         if(theForm[z].checked)
         {
           document.getElementById('prospectStatusSpan').style.position='static';
           document.getElementById('prospectStatusSpan').style.visibility='visible';
         }
         else
         {
           document.getElementById('prospectStatusSpan').style.position='absolute';
           document.getElementById('prospectStatusSpan').style.visibility='hidden';
         }
       }
     }
    }
  } catch(e){}
}

function geboortedatumChange(fieldname)
{
  var theForm = document.editForm.elements, z = 0;
  for(z=0; z<theForm.length;z++)
  {
   if(theForm[z].type == 'checkbox')
   {
     if((fieldname == 'geboortedatum' && theForm[z].name == 'verjaardagLijst') || (fieldname == 'part_geboortedatum' && theForm[z].name == 'part_verjaardagLijst'))
     {
       theForm[z].checked = true;
     }
   }
  }
}

function overlijdensdatumChange(fieldname)
{
  var theForm = document.editForm.elements, z = 0;
  for(z=0; z<theForm.length;z++)
  {
   if(theForm[z].type == 'checkbox')
   {
     if((fieldname == 'overlijdensdatum' && theForm[z].name == 'verjaardagLijst') || (fieldname == 'part_overlijdensdatum' && theForm[z].name == 'part_verjaardagLijst'))
     {
       theForm[z].checked = false;
     }
   }
  }
}

function hideProspect()
{
  document.getElementById('prospectStatusSpan').style.visibility='hidden';
}

function buildQueryArray(theFormName)
{
  var theForm = document.forms[theFormName];
  var qs = new Object();
  for (e=0;e<theForm.elements.length;e++) {
    if (theForm.elements[e].name!='') {
    	qs[theForm.elements[e].name] = theForm.elements[e].value;
      }
    }
  return qs;
}

function vermogensbeheerderChanged()
{
  jsrsExecute("selectRS.php", populateAccountmanager, "getAccountmanager",buildQueryArray('editForm'), false);
  jsrsExecute("selectRS.php", populateRisicoklasse, "getRisicoklasse",buildQueryArray('editForm'), false);
	jsrsExecute("selectRS.php", populateRemisier, "getRemisier",buildQueryArray('editForm'), false);
  jsrsExecute("selectRS.php", populateSoortOvereenkomst, "getSoortOvereenkomst",buildQueryArray('editForm'), false); 
}

function populateSoortOvereenkomst (valueTextStr)
{
  valueTextStr= '---~ |' + valueTextStr;
	populateDropDown(document.editForm.SoortOvereenkomst,valueTextStr);
}

function populateRemisier (valueTextStr)
{
  valueTextStr= '---~ |' + valueTextStr;
	populateDropDown(document.editForm.Remisier,valueTextStr);
}

function populateRisicoklasse (valueTextStr)
{
  valueTextStr= '---~ |' + valueTextStr;
	populateDropDown(document.editForm.Risicoklasse,valueTextStr);
}

function populateAccountmanager (valueTextStr)
{
	populateDropDown(document.editForm.Accountmanager,valueTextStr);
	populateDropDown(document.editForm.tweedeAanspreekpunt,valueTextStr,1);
}

function clearDropDown (selField)
{
  while (selField.options.length > 0)
    selField.options[0] = null;
}

function populateDropDown (field, valueTextStr,addEmpty)
{
  var selField = field;
  clearDropDown(selField);

	// options in form "value~displaytext|value~displaytext|..."
  var aOptionPairs = valueTextStr.split('|');
  
  if(addEmpty==1)
  {
    oItem = new Option;
    oItem.value = '';
    oItem.text = '---';
    selField.options[selField.options.length] = oItem;
  }

  for( var i = 0; i < aOptionPairs.length; i++ ){
    if (aOptionPairs[i].indexOf('~') != -1) {
      var aOptions = aOptionPairs[i].split('~');
      oItem = new Option;
      oItem.value = aOptions[1];
      oItem.text = aOptions[0];
      selField.options[selField.options.length] = oItem;
    }
  }

  selField.options.selectedIndex = 0;
}



function copyAdres(force)
{
  with (document.editForm)
  {
    if (verzendAdres.value  == "" || force ) verzendAdres.value  = adres.value;
    if (verzendPc.value     == "" || force ) verzendPc.value     = pc.value;
    if (verzendPlaats.value == "" || force ) verzendPlaats.value = plaats.value;
    if (verzendLand.value   == "" || force ) verzendLand.value   = land.value;
  }
}
function copyAanhef(type)
{

  if(typeof(type)==='undefined') type = 0;
  
  var enofChecked=false;
  var theForm = document.editForm.elements, z = 0;
  for(z=0; z<theForm.length;z++)
  {
   if(theForm[z].type == 'checkbox')
   {
      if(theForm[z].name == 'enOfRekening')
      {
        if(theForm[z].checked==true)
        {
          enofChecked=true;
        }
      }
    }
  }    

  with (document.editForm)
  {

    if (geslacht.value  == "man" && enofChecked == false)
     txt="heer";
    else if (geslacht.value  == "vrouw" && enofChecked == false)
     txt="mevrouw";
    else if (geslacht.value  == "man" && part_geslacht.value == "vrouw")
     txt="heer en mevrouw";
    else if (geslacht.value  == "man" && part_geslacht.value == "man")
     txt="heren";
    else if (geslacht.value  == "vrouw" && part_geslacht.value == "vrouw")
     txt="dames";
    else
     txt="";

    if((txt != "" && part_achternaam.value == achternaam.value) || (txt != "" && part_achternaam.value == "") || type == 1)
    {
      if(tussenvoegsel.value != "")
        txt += " " + tussenvoegsel.value.substr(0, 1).toUpperCase() + tussenvoegsel.value.substr(1);
      if(achternaam.value != "")
        txt +=" " + achternaam.value;
    }
    txt = txt.replace( /\s\s+/g, ' ' );
    verzendAanhef.value = "Geachte " + txt;

    if(ondernemingsvorm.value != "")
    {
      verzendAanhef.value = naam.value;
    }
    else
    {
      if (geslacht.value  == "man")
       txt="De heer " + titel.value + " "+ voorletters.value + " " + tussenvoegsel.value + " " + achternaam.value + " " + achtervoegsel.value;
      else if (geslacht.value  == "vrouw")
       txt="Mevrouw " + titel.value + " " + voorletters.value + " " + tussenvoegsel.value + " " + achternaam.value + " " + achtervoegsel.value;

      if(enofChecked == true)
      {
        txt += " en/of";
      }
      txt = txt.replace( /\s\s+/g, ' ' );
      naam.value= txt;

      if(enofChecked)
      {
        if(type == 0 && enofChecked == false)
          txt ="en ";
        else
          txt="";  
        if (part_geslacht.value  == "man")
         txt +="de heer " + part_titel.value + " "+ part_voorletters.value + " " + part_tussenvoegsel.value + " " + part_achternaam.value + " " + part_achtervoegsel.value;
        else if (part_geslacht.value  == "vrouw")
         txt +="mevrouw " + part_titel.value + " " + part_voorletters.value + " " + part_tussenvoegsel.value + " " + part_achternaam.value + " " + part_achtervoegsel.value;
        else
         txt="";

        txt = txt.replace( /\s\s+/g, ' ' );
        naam1.value= txt;
      }
    }
  }
}

function portefeuilleChange()
{
  var theForm = document.editForm.elements, z = 0;
  for(z=0; z<theForm.length;z++)
  {
   if(theForm[z].type == "text")
   {
     if(theForm[z].name == "Portefeuille")
     {
       theForm[z].value = "";
       theForm[z].disabled=true;
     }
   }
  }
}


function updateScript()
{
  screenUpdate();
}

function initScript()  // wat te doen onLoad
{
  tabOpen('0');
  screenUpdate();
}


function replaceButtonText(buttonId, text)
{
  if (document.getElementById)
  {
    var button=document.getElementById(buttonId);
    if (button)
    {
      if (button.childNodes[0])
      {
        button.childNodes[0].nodeValue=text;
      }
      else if (button.value)
      {
        button.value=text;
      }
      else //if (button.innerHTML)
      {
        button.innerHTML=text;
      }
    }
  }
}

function setTriggerDate()
{
  var d=new Date()
  txt = d.getDate()+"-"+eval(d.getMonth()+1)+"-"+d.getFullYear();
  document.editForm.trigger_date.value = txt;
}


function mailTo(value)
{
  ml = 'mailto:'+value;
  window.location = ml;
}

function mailZTo()
{
  ml = 'mailto:'+document.editForm.emailZakelijk.value;
  window.location = ml;
}

function wwwTo(value)
{
  if(value){ml = value;}
  else {ml = document.editForm.website.value;}
    

  if (ml.search(/http:/i) < 0)   // -1 is not found
  ml = "http://"+ml;

  window.open(ml,'nieuwVenster');
}

function getWaarden (sel,tabel,veld)
{
  var oldValue = document.getElementById(veld).value;
  if(sel.length>0){
		var index = ajax.length;
		ajax[index] = new sack();
		ajax[index].element = Veld;
		ajax[index].requestFile = 'lookups/ajaxLookup.php?module=crmLookups&query='+sel+'|'+tabel;	// Specifying which file to get
		ajax[index].onCompletion = function(){ setWaarden(index,veld,oldValue) };	// Specify function that will be executed after file has been found
		ajax[index].onError = function(){ alert('Ophalen beleggingscategorien uit BeleggingscategorienPerVermogensbeheerder mislukt.') };
		ajax[index].runAJAX();		// Execute AJAX function
  }
}

function setWaarden(index,veld,oldValue)
{
 	var	Waarden = ajax[index].response;
 	var elements = Waarden.split('\\t\\n');
 	if(elements.length > 1)
 	{
 	  document.getElementById(veld).options.length=0;
 	  AddName('editForm',veld,'---','');
      for(var i=0;i<elements.length;i++)
   	  {
   	    if(elements[i] != '')
   	    {
 	      AddName('editForm',veld,elements[i],elements[i])
 	    }
      }
 	}
 	document.getElementById(veld).value = oldValue;
}

function AddName(p_FormName,p_SelectName,p_OptionText,p_OptionValue)
{
  document.forms[p_FormName].elements[p_SelectName].options[document.forms[p_FormName].elements[p_SelectName].length] = new Option(p_OptionText,p_OptionValue);
}

function openTaak(url)
{//taakId,frame
   //var url='takenEdit.php?action=edit&id='+taakId+'&frame='+frame;
   
   
   tabOpen('9');
   window.frames.extraFrame.location.href = url;
}
