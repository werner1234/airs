<?
include_once("wwwvars.php");
include_once("../classes/AE_lib_ajax_server.php");

function remote_storeRapportSelection($formVars)
{
	$formVars = filterVars($formVars);
	$selected = explode("|",$formVars[selected]);
	session_start();
	$_SESSION['rapportSelection'] = array();
	for($a=0; $a < count($selected); $a++)
	{
		if(!empty($selected[$a]))
		{
			$_SESSION['rapportSelection'][] = $selected[$a];
		}
	}
	session_write_close();
	return $result;
}

function remote_storeRapportSelectionBack($formVars)
{
	$formVars = filterVars($formVars);
	$selected = explode("|",$formVars[selected]);
	session_start();
	$_SESSION['rapportSelectionBack'] = array();
	for($a=0; $a < count($selected); $a++)
	{
		if(!empty($selected[$a]))
		{
			$_SESSION['rapportSelectionBack'][] = $selected[$a];
		}
	}
	session_write_close();
	return $result;
}

function remote_storeDate($formVars)
{
	$formVars = filterVars($formVars);
	session_start();

	$_SESSION['rapportDateFrom'] 	= $formVars[datum_van];
	$_SESSION['rapportDateTm'] 		= $formVars[datum_tot];

	session_write_close();
	return true;
}

?>