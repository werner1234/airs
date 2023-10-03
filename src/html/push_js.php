<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2019/07/22 09:11:57 $
 		File Versie					: $Revision: 1.1 $

 		$Log: push_js.php,v $
 		Revision 1.1  2019/07/22 09:11:57  cvs
 		call 7675
 		

*/
$save = 0;

?>

<script type="text/javascript">
    function pushpdf(file,save)
    {
        var width='800';
        var height='600';
        var target = '_pdf';
        var location = 'pushFile.php?file=' + file;
        if(save == 1)
        {
            // opslaan als bestand
            document.location = location + '&action=attachment';
        }
        else
        {
            // pushen naar PDF reader
            var doc = window.open("",target,'toolbar=no,status=yes,scrollbars=yes,location=no,menubar=yes,resizable=yes,directories=no,width=' + width + ',height= ' + height);
            doc.location = location;
          <?
          if ($_returnUrl)
          {
            echo "window.location = '".$_returnUrl."'";
          }

          if ($_closeWindow)
          {
            echo "$_closeWindow";
          }


          ?>
        }
    }
    pushpdf('<?=$filename?>',<?=$save?>);
</script>
