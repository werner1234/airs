<?php
/* 	
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2014/05/03 15:46:30 $
 		File Versie					: $Revision: 1.5 $
 		
 		$Log: 20100106_PREinstall.php,v $
 		Revision 1.5  2014/05/03 15:46:30  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2013/09/18 08:28:36  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2013/09/18 08:21:21  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2010/01/06 15:24:35  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2010/01/06 11:42:16  rvv
 		*** empty log message ***
 		
 
 		
 	
*/
include_once("wwwvars.php");
if(function_exists("get_defined_constants"))
{
  $constants=get_defined_constants();
  if($constants['PHP_VERSION'] !='')
    $logdata['phpVersion']=$constants['PHP_VERSION'];
  else 
    $logdata['phpVersion']='none';
  if($constants['PHP_OS'] !='')
    $logdata['os']=$constants['PHP_OS'];
  else 
    $logdata['os']='none';
  if($constants['OPTIMIZER_VERSION'] !='')
    $logdata['optimizer']=$constants['OPTIMIZER_VERSION'];
  else 
    $logdata['optimizer']='none';    
}

$db = new DB;
$query="select version() as versie";
$db->SQL($query);
$data=$db->lookupRecord();
$logdata['mysqlVersion']=$data['versie'];

if(isset($_SERVER['SERVER_SOFTWARE']))
 $logdata['serverSoftware']=$_SERVER['SERVER_SOFTWARE'];

if(function_exists("gd_info"))
{
  $gdInfo=gd_info();
  $logdata['gdVersion']=$gdInfo['GD Version'];
}
else 
 $gdVersion='none';
 
 
$db=new DB();
$query="show TABLES like 'CRM_naw'";
if($db->QRecords($query)>0)
{
  $query="SELECT count(id) as aantal,portefeuille FROM CRM_naw GROUP BY portefeuille having aantal > 1";
  $aantal=$db->QRecords($query);
  $logdata['extraInfo']="Aantal dubbelgekoppelde portefeulles=(".$aantal.")";
}

ob_start();
phpinfo();
$phpinfo = array('phpinfo' => array());
if(preg_match_all('#(?:<h2>(?:<a name=".*?">)?(.*?)(?:</a>)?</h2>)|(?:<tr(?: class=".*?")?><t[hd](?: class=".*?")?>(.*?)\s*</t[hd]>(?:<t[hd](?: class=".*?")?>(.*?)\s*</t[hd]>(?:<t[hd](?: class=".*?")?>(.*?)\s*</t[hd]>)?)?</tr>)#s', ob_get_clean(), $matches, PREG_SET_ORDER))
    foreach($matches as $match)
        if(strlen($match[1]))
            $phpinfo[$match[1]] = array();
        elseif(isset($match[3]))
            $phpinfo[end(array_keys($phpinfo))][$match[2]] = isset($match[4]) ? array($match[3], $match[4]) : $match[3];
        else
            $phpinfo[end(array_keys($phpinfo))][] = $match[2];

if(function_exists("gzcompress") && function_exists("serialize"))
  $logdata['phpInfo']=gzcompress(serialize($phpinfo));
else 
  $logdata['phpInfo']='none';
  



  $log = new  DB(2);
  $query="SELECT id FROM serverInformatie WHERE  bedrijf = '".$__appvar['bedrijf']."'";
  $log->SQL($query);
  $data=$log->lookupRecord();
  if(isset($data['id']))
  {
    $query = "UPDATE serverInformatie SET ";
    $where=" WHERE id='".$data['id']."'";
  }
  else 
  {
    $query = "INSERT INTO serverInformatie SET ";
    $where='';
  }
  $query  .= "  datum = NOW()";
  if(function_exists("addslashes"))
  {
    foreach ($logdata as $key=>$data)
    {
      $query  .= ", $key = '".addslashes($data)."'";
    }
  }  
  $query  .= ", bedrijf = '".$__appvar['bedrijf']."' $where";
  $log->SQL($query);
  $log->query();
?>