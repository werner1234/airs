<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2012/12/30 14:25:57 $
 		File Versie					: $Revision: 1.3 $

 		$Log: blankFondsKoppeling.php,v $
 		Revision 1.3  2012/12/30 14:25:57  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2010/08/04 15:25:56  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2010/02/10 17:54:22  rvv
 		*** empty log message ***


*/
?>

<script type="text/javascript">
parent.VermogensbeheerderChanged();
</script>

<div id="FondskoersenDiv" style="display:none"> test</div>
<?
if($_GET['message'])
{
  echo urldecode($_GET['message']);
}


?>