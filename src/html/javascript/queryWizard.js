<script language="JavaScript">

function hasOptions(obj)
{
  if (obj!=null && obj.options!=null)
  {
    return true;
	}
	return false;
}

function swapOptions(obj,i,j)
{
	var o = obj.options;
	var i_selected = o[i].selected;
	var j_selected = o[j].selected;
	var temp = new Option(o[i].text, o[i].value, o[i].defaultSelected, o[i].selected);
	var temp2= new Option(o[j].text, o[j].value, o[j].defaultSelected, o[j].selected);
	o[i] = temp2;
	o[j] = temp;
	o[i].selected = j_selected;
	o[j].selected = i_selected;
}


function moveOptionUp(obj)
{
	if (!hasOptions(obj)) { return; }
	for (i=0; i<obj.options.length; i++)
	{
		if (obj.options[i].selected)
		{
			if (i != 0 && !obj.options[i-1].selected)
			{
				swapOptions(obj,i,i-1);
				obj.options[i-1].selected = true;
			}
		}
	}
}

function moveOptionDown(obj)
{
	if (!hasOptions(obj)) { return; }
	for (i=obj.options.length-1; i>=0; i--)
	{
		if (obj.options[i].selected)
		{
			if (i != (obj.options.length-1) && ! obj.options[i+1].selected)
			{
				swapOptions(obj,i,i+1);
				obj.options[i+1].selected = true;
			}
		}
	}
}


function moveItem(from,to)
{

  var tmp_text = new Array();
	var tmp_value = new Array();
 	for(var i=0; i < from.options.length; i++)
 	{
 		if(from.options[i].selected)
 		{
			var blnInList = false;
			for(j=0; j < to.options.length; j++)
			{
 				if(to.options[j].value == from.options[i].value)
				{
 					//alert("already in list");
 					blnInList = true;
 					break;
 				}
			}
			if(!blnInList)
 			{
				to.options.length++;
				to.options[to.options.length-1].text = from.options[i].text;
				to.options[to.options.length-1].value = from.options[i].value;
			}
 		}
		else
		{
			tmp_text.length++;
			tmp_value.length++;
			tmp_text[tmp_text.length-1] = from.options[i].text;
			tmp_value[tmp_text.length-1] = from.options[i].value;

		}
 	}
 	from.options.length = 0;
 	for(var i=0; i < tmp_text.length; i++)
 	{
 		from.options.length++;
		from.options[from.options.length-1].text = tmp_text[i];
		from.options[from.options.length-1].value = tmp_value[i];
 	}
 	from.selectedIndex = -1;
}

function submitForm()
{
	if(document.wizard['inFields[]'])
	{
		var inFields  = document.wizard['inFields[]'];
		var selectedFields = document.wizard['selectedFields[]'];

		for(j=0; j < inFields.options.length; j++)
		{
	 		inFields.options[j].selected = true;
		}

		for(j=0; j < selectedFields.options.length; j++)
		{
 			selectedFields.options[j].selected = true;
		}
	}
	document.wizard.submit();
}
</script>