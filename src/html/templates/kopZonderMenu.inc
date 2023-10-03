<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <title>
       {title}
    </title>
    <!-- load the menu stylesheet -->
    <style type="text/css">
      @import url("hmenu/skin-xp-apps.css");
    </style>
    <!-- declare the menu location -->
    <script type="text/javascript">
      _dynarch_menu_url = "hmenu/";
    </script>
    <!-- load the menu program file -->
    <script type="text/javascript" src="hmenu/hmenu.js"></script>
    <!-- [ ... ] your HEAD declarations here -->
    {style}
    {meta}
    <script language="JavaScript" TYPE="text/javascript">
		function getWindowSize(type) {
		  var myWidth = 0, myHeight = 0;
		  if( typeof( window.innerWidth ) == 'number' ) {
		    //Non-IE
		    myWidth = window.innerWidth;
		    myHeight = window.innerHeight;
		  } else if( document.documentElement &&
		      ( document.documentElement.clientWidth || document.documentElement.clientHeight ) ) {
		    //IE 6+ in 'standards compliant mode'
		    myWidth = document.documentElement.clientWidth;
		    myHeight = document.documentElement.clientHeight;
		  } else if( document.body && ( document.body.clientWidth || document.body.clientHeight ) ) {
		    //IE 4 compatible
		    myWidth = document.body.clientWidth;
		    myHeight = document.body.clientHeight;
		  }
		  if (type == 'height')
		  	return myHeight;
		  else
		  	return myWidth;
		}

  	function setDiv()
  	{
  		// lala om div hoogte en breedte te berekenen
			var width  = getWindowSize('width');
			var height = getWindowSize('height');

			var new_width = width-180;
			var new_height = height-51;

			document.getElementById('content').style.height   = new_height + 'px';
			document.getElementById('main_all').style.height  = new_height + 'px';
			document.getElementById('main_all').style.width   = '100%';
			document.getElementById('right_all').style.width  = new_width + 'px';
			document.getElementById('right_all').style.height = new_height + 'px';

			return true;
  	}

    window.onbeforeunload = confirmExit;
    function confirmExit()
    {
      if(parent.frames['content'].fromChanged)
        return 'Are you sure you want to exit this page?';
    }

    function checkChange(url,target)
    {
      var confirmed = false;
      try 
      {
        if(parent.frames['content'].fromChanged)
        {
          if(confirm ('U verlaat het scherm. Wijzigingen worden niet opgeslagen. Weet u het zeker?'))
            confirmed = true;
        }
        else
          confirmed = true;
      }catch(e){}

      if(confirmed)
      {
        if(target ==  '_top')
          document.location = url;
        else if(target ==  '_blank')
          window.open(url)
        else
          parent.frames[target].location = url;
      }
    }

    {javascript}
    function OnLoadFunc()
    {

    }
    {openwindow}
    </script>



 </head>
<body onResize="setDiv();" text="#000000"  marginwidth="0" marginheight="0" leftmargin="0" topmargin="0" {body}>
<div id="main">
	<div class="top_navigatie"><iframe src="{initial_navigatie}" name="navigatie" id="navigatie" width="100%" height="25" marginwidth="0" marginheight="0" hspace="0" vspace="0" align="middle" frameborder="0" scrolling="no"></iframe></div>
	<div class="main_all" id="main_all">
		<div class="left_menu"><iframe src="" name="submenu" id="submenu" style="height: 100%" width="100%" marginwidth="0" marginheight="0" hspace="0" vspace="0" align="middle" frameborder="0"></iframe></div>
		<div class="right_all" id="right_all">
			<div class="content"><iframe src="{initial_content}" name="content" id="content" width="100%" marginwidth="0" marginheight="0" hspace="0" vspace="0" align="middle" frameborder="0"></iframe></div>


