var kaHttpRequest = false;
var kaOldSessionId = '';
if (typeof kaDebug == 'undefined')
{
  var kaDebug = false;
}
if (typeof kaServerPage == 'undefined')
{
  var kaServerPage = 'http://127.0.0.1/keepalive.php';
}
if (typeof kaInterval == 'undefined')
{
  var kaDefaultInterval = 25;
  var kaInterval = kaDefaultInterval;
  var lastKaInterval = kaInterval;
  var kaIntervalSetTime = new Date();
}
if (typeof kaOkMessage == 'undefined')
{
  var kaOkMessage = '<span style="color: #41930a;">Session alive</span>';
}
if (typeof kaExpiredMessage == 'undefined')
{
  var kaExpiredMessage = '<span style="color: #b82c06;">Sessie verlopen</span>';
}
if (typeof kaErrorMessage == 'undefined')
{
  var kaErrorMessage = '<span style="color: #b82c06;">Verbinding verbroken</span>';
}
if (typeof kaStatusElementID == 'undefined')
{
  var kaStatusElementID = 'sessionstatus';
}

kaAjax('GET', kaServerPage, '', kaStatusElementID);
var keepAliveSession=setInterval("kaAjax('GET', kaServerPage, '', kaStatusElementID)", kaInterval * 1000);

function kaAjax(httpRequestMethod, url, parameters, target)
{
  kaHttpRequest = false;
  //document.getElementById(target).innerHTML = 'Wait...'
  if (window.XMLHttpRequest)
  { // For Mozilla, Safari, Opera, IE7+
    kaHttpRequest = new XMLHttpRequest();
    if (kaHttpRequest.overrideMimeType)
    {
      kaHttpRequest.overrideMimeType('text/plain');
      //Change MimeType to match the data type of the server response.
      //Examples: "text/xml", "text/html", "text/plain"
    }
  }
  else if (window.ActiveXObject)
  { // For IE6
    try
    {
      kaHttpRequest = new ActiveXObject("Msxml2.XMLHTTP");
    }
    catch (e)
    {
      try
      {
        kaHttpRequest = new ActiveXObject("Microsoft.XMLHTTP");
      }
      catch (e)
      {}
    }
  }
  if (!kaHttpRequest)
  {
    alert('Giving up :( Cannot create an XMLHTTP instance');
    return false;
  }
  kaHttpRequest.onreadystatechange = function() {updateElement(target);};
  if (httpRequestMethod == 'GET')
  {
    try
    {
      if(navigator.appName=="Microsoft Internet Explorer")
      {
        var tableId='&tableId=' + parent.frames['content'].editForm.id.value;
      }
      else
      {
        var tableId='&tableId=' + tableIdFF;
      }
    } catch(err) {var tableId='&tableId=NoId'; console.alert(err)}

    //kaHttpRequest.open('GET', url + '?' + parameters + '&random=' + ser + tableId, true);
    var ser = Math.round(Math.random()*1000000); // Anti-caching random number
    kaHttpRequest.open('GET', url + '?' + parameters + '&random=' + ser + tableId, true);
    kaHttpRequest.send(null);
  }
  else if (httpRequestMethod == 'POST')
  {
    kaHttpRequest.open('POST', url, true);
    kaHttpRequest.setRequestHeader('Content-Type',
      'application/x-www-form-urlencoded');
    kaHttpRequest.send(parameters);
  }
  else
  {
    alert('Sorry, unsupported HTTP method');
  }
}

function updateElement(target)
{

  if (kaHttpRequest.readyState == 4)
  {
    if (kaDebug == true)
    {
      alert(kaHttpRequest.responseText);
    }
    if (kaHttpRequest.status == 200)
    {
      var response = kaHttpRequest.responseText.split('|');

      if(response.length > 3)
      {
        document.getElementById(target).innerHTML = kaExpiredMessage;
        return '';
      }

      if (kaOldSessionId == '')
      {
        kaOldSessionId = response[0];
      }

      if (response[0] == kaOldSessionId)
      {

        if(kaOldSessionId.length > 50)
        {
          kaOldSessionId='';
        }

        var currentTime = new Date()
		    var seconds = currentTime.getSeconds();
		    var minutes = currentTime.getMinutes();
		    var hours = currentTime.getHours();
		    var month = (currentTime.getMonth()+1);
		    var day = currentTime.getDate();
	    	if (seconds < 10){seconds = "0" + seconds};
        if (minutes < 10){minutes = "0" + minutes};
	    	if (hours < 10){hours = "0" + hours};
	    	if (month < 10){month = "0" + month};
	    	if (day < 10){day = "0" + day};
        document.getElementById(target).innerHTML = kaOldSessionId + " " + day +"-" + month +"-" + currentTime.getFullYear() + " " + hours +":" + minutes + ":" + seconds;

        if(parseInt((currentTime.getTime()-kaIntervalSetTime.getTime())/1000) > 300)
        {
          kaInterval=kaDefaultInterval;
        }
      }
      else
      {
        document.getElementById(target).innerHTML = kaExpiredMessage;
      }

      try
      {
        parent.frames['submenu'].document.getElementById('subMenuDiv').innerHTML = response[1];
        // statusLights
        if(response[2].length < 500)
        {
          if (response[2].trim() == "")
          {
            document.getElementById("statusLights").innerHTML = "&#183;";
          }
          else
          {
            document.getElementById("statusLights").innerHTML = response[2];
          }
        }
      }
      catch (e){}


    }
    else
    {
      document.getElementById(target).innerHTML = kaErrorMessage;
    }
    checkForSpeedup();
  }
}

function checkForSpeedup()
{
  if(kaInterval != lastKaInterval)
  {
    clearInterval(keepAliveSession);
    keepAliveSession=setInterval("kaAjax('GET', kaServerPage, '', kaStatusElementID)", kaInterval * 1000);
    kaIntervalSetTime = new Date()
  }
  lastKaInterval=kaInterval;
}