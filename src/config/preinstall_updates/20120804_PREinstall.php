<?php

include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();

$db = new DB();

$tables['CRM_naw_RtfTemplates']="CREATE TABLE `CRM_naw_RtfTemplates` (
  `id` int(11) NOT NULL auto_increment,
  `naam` varchar(255),
  `standaard` tinyint(4),
  `template` mediumblob,
  `categorie` varchar(100),
  `change_user` varchar(10) default NULL,
  `change_date` datetime default NULL,
  `add_user` varchar(10) default NULL,
  `add_date` datetime default NULL,
  PRIMARY KEY  (`id`)
)  ";


foreach($tables as $table=>$query)
{
  if($db->QRecords("SHOW TABLE STATUS  LIKE '$table'") < 1)
  {
    $db->SQL($query);
    $db->Query();
  }
}

$cfg=new AE_config();
$standaardbrief = $cfg->getData('standaardbrief');
$templateDir = $__appvar["basedir"]."/html/RTF_templates/";
$hiddenFiles = array(".","..");
$db=new DB();
$dir = @opendir($templateDir);
while($file = readdir($dir))
{
	if(is_file($templateDir.$file) && !in_array($file,$hiddenFiles))
	{
    if(strtolower(substr($file,-4)) == ".rtf")
    {
      $filesize = filesize($templateDir.$file);
      $fileHandle = fopen($templateDir.$file, "r");
      $template = fread($fileHandle, $filesize);
      $query="SELECT id FROM CRM_naw_RtfTemplates WHERE naam ='$file'";
      if($db->QRecords($query) < 1)
      {
        if($standaardbrief==$file)
          $standaard=",standaard=1";
        else
          $standaard='';
        $query="INSERT INTO CRM_naw_RtfTemplates SET naam='$file',template=unhex('".bin2hex($template)."'),add_date=NOW(),add_user='sys' $standaard ";
        $db->SQL($query);
        $db->Query();
      }
    }
	}
}
closedir($dir);

?>