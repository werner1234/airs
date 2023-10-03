var requester = null; 
function failure(requester)
{
	alert("failed: response was: " + requester.responseText);
}

function stateHandler(requester, returnFunction, formName)
{
	/* If XMLHR object has finished retrieving the data */
	if (requester != null)
	{
		if (requester.readyState == 4)
		{
			/* If the data was retrieved successfully */
			if (requester.status == 200)
			{
				returnFunction(requester,formName);
			}
	   	/* IE returns a status code of 0 on some occasions, so ignore this case */
			else if (requester.status != 0)
			{
				failure(requester);
			}
		}
	}
	else
	{
		failure(requester);
	}
 	return true;
}

function executeRequest(remoteServer, formName, remoteFunction, returnFunction) 
{

	var requester = null; 
	/* Check for running connections */
	if (requester != null && requester.readyState != 0 && requester.readyState != 4)
	{
	 requester.abort();
	}
	
	try
	{
		requester = new XMLHttpRequest();
	}
	catch (error)
	{
		try
		{
			requester = new ActiveXObject("Microsoft.XMLHTTP");
		}
		catch (error)
		{
			requester = null;
			return false;
		}
	} 
	
	var queryString = '?__remoteFunction=' + remoteFunction + buildQueryString(formName)
	requester.onreadystatechange = function() { stateHandler(requester, returnFunction, formName) }
	requester.open("GET", remoteServer + queryString, true);
	requester.send(null);

	return true; 
}

function buildQueryString(formName) 
{
  var theForm = document.forms[formName];
  var qs = ''
  for (e=0;e<theForm.elements.length;e++) 
  {
    if (theForm.elements[e].name!='') 
    {
      qs+='&';
      qs+='pre_'+theForm.elements[e].name+'='+escape(theForm.elements[e].value)
		}
	}
  return qs;
}