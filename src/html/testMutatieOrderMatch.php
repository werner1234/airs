<?php
include_once("wwwvars.php");

echo "Mutaties ophalen uit TijdelijkeRekeningmutaties<br>\n";
$db=new DB();
$query="SELECT * FROM TijdelijkeRekeningmutaties WHERE Grootboekrekening='FONDS'";
$db->SQL($query);
$db->Query();
$aantal=$db->records();
echo "($aantal) records gevonden met $query<br>\n";
if($aantal > 0)
{
  while($regel=$db->nextRecord())
    $mutaties[]=$regel;
  $debugLog = orderCheck($mutaties, true);
  $htmlEmail==

  listarray($debugLog['data']);
  foreach($debugLog['html'] as $html)
    echo "<hr> $html";
}


?>