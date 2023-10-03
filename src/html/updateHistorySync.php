<?php
include_once("wwwvars.php");

echo template($__appvar["templateContentHeader"],$content);

session_start();
$_SESSION[NAV] = "";
$_SESSION[submenu] = New Submenu();
$_SESSION[submenu]->addItem("Updatelog","updatehistoryList.php");
session_write_close();


echo "Bezig met ophalen van de queue log.<br>";

$query = "SELECT * FROM updates ";
$DB_queue = new DB(2);
$DB_queue->SQL($query);
$DB_queue->Query();
if($DB_queue->records() > 0)
{
	while($queue = $DB_queue->nextRecord())
	{

		$DB = new DB();

		$select = "SELECT exportId FROM UpdateHistory WHERE exportId = '".$queue['exportId']."' AND Bedrijf = '".$queue['Bedrijf']."' ";
		$DB->SQL($select);
		$DB->Query();
		$records = $DB->records();
		if($records >0)
		{
			$query = "UPDATE UpdateHistory SET ";
		}
		else
		{
			$query = "INSERT INTO UpdateHistory SET ";
		}

		$query .= "  Bedrijf = '".$queue[Bedrijf]."' ";
		$query .= ", exportId = '".$queue[exportId]."' ";
		$query .= ", type = '".$queue[type]."' ";
		$query .= ", filename = '".$queue[filename]."' ";
		$query .= ", filesize = '".$queue[filesize]."' ";
		$query .= ", server = '".$queue[server]."' ";
		$query .= ", username = '".$queue[username]."' ";
		$query .= ", password = '".$queue[password]."' ";
		$query .= ", complete = '".$queue[complete]."' ";
		$query .= ", terugmelding = '".mysql_escape_string($queue[terugmelding])."' ";
		$query .= ", tableDef = '".mysql_escape_string($queue['tableDef'])."' ";
		$query .= ", add_user = '".$queue[add_user]."' ";
		$query .= ", add_date = '".$queue[add_date]."' ";
		$query .= ", change_user = '".$queue[change_user]."' ";
		$query .= ", change_date = '".$queue[change_date]."' ";

		if($records >0)
		{
			$query .= " WHERE exportId = '".$queue[exportId]."' AND Bedrijf = '".$queue['Bedrijf']."' ";
		}
		//echo $query;

		$DB->SQL($query);
		if($DB->Query())
		{
			if($queue['complete'] == 1)
			{
				// remove from queue
				$DB_queue2 = new DB(2);
				$query = "DELETE FROM updates WHERE exportId = '".$queue[exportId]."' AND Bedrijf = '".$queue['Bedrijf']."' ";
				$DB_queue2->SQL($query);
				$DB_queue2->Query();
			}

			$t++;
		}

	}

	echo $t." records opgehaald.<br>";
}
else
{
	echo "Geen nieuwe data in queue log.";
}
echo template($__appvar["templateRefreshFooter"],$content);
?>