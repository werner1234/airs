

<div id="dwindow" style="position:absolute;background-color:#DDDDDD;cursor:hand;left:0px;top:0px;display:none" onMousedown="initializedrag(event)" onMouseup="stopdrag()" onSelectStart="return false">
	<div align="right" style="width:100%;background-color:Silver;border-top: 1px solid black;border-right: 1px solid black;border-left: 1px solid black;">
		<img src="images/16/window_resize.gif" id="maxname" onClick="maximize()"><img src="images/16/window_close.gif" onClick="closeit()">
	</div>
	<div id="dwindowcontent" style="height:100%; width:100%;" >
		<iframe id="cframe" src="" width="100%" height="100%" style="border:1px solid black;"></iframe>
	</div>
</div>

<script language="JavaScript" type="text/javascript">

function placeFocus()
{
	if (document.forms.length > 0)
	{
		var field = document.forms[0];
		for (i = 0; i < field.length; i++)
		{
			if(field.elements[i].type)
			{
				if ((field.elements[i].type == "text") || (field.elements[i].type == "textarea") || (field.elements[i].type.toString().charAt(0) == "s"))
				{
				  try{document.forms[0].elements[i].focus();} catch(err) { }
					break;
				}
			}
   	}
	}
}
setTimeout('placeFocus();', 800);

if(parent.frames['submenu'])
{
  parent.frames['submenu'].location = '{initial_submenu}';
}
if(parent.frames['navigatie'])
{
  setTimeout('parent.frames[\'navigatie\'].location.reload()', 300);
}





</script>
<!--
  coding  : AE-ICT http://www.aeict.nl
  versie  : {title} {versie}
-->
</body>
</html>