<?php
/* 	
    AE-ICT CODEX source module versie 1.6, 11 september 2013
    Author              : $Author: rvv $
    Laatste aanpassing  : $Date: 2016/05/07 14:54:16 $
    File Versie         : $Revision: 1.17 $
 		
    $Log: tijdelijkebulkordersList.php,v $
    Revision 1.17  2016/05/07 14:54:16  rvv
    *** empty log message ***

    Revision 1.16  2015/08/30 11:42:24  rvv
    *** empty log message ***

    Revision 1.15  2015/02/18 17:08:08  rvv
    *** empty log message ***

    Revision 1.14  2014/07/30 15:33:10  rvv
    *** empty log message ***

    Revision 1.13  2014/05/10 13:53:42  rvv
    *** empty log message ***

    Revision 1.12  2014/03/16 11:16:20  rvv
    *** empty log message ***

    Revision 1.11  2014/03/12 15:11:31  rvv
    *** empty log message ***

    Revision 1.10  2014/03/10 17:25:28  rvv
    *** empty log message ***

    Revision 1.9  2014/03/08 17:02:09  rvv
    *** empty log message ***

    Revision 1.8  2014/01/29 17:00:21  rvv
    *** empty log message ***

    Revision 1.7  2013/11/06 15:52:25  rvv
    *** empty log message ***

    Revision 1.6  2013/10/01 16:06:20  rvv
    *** empty log message ***

    Revision 1.5  2013/10/01 14:48:38  rvv
    *** empty log message ***

    Revision 1.4  2013/09/28 14:42:13  rvv
    *** empty log message ***

    Revision 1.3  2013/09/25 15:58:39  rvv
    *** empty log message ***

    Revision 1.2  2013/09/22 15:23:37  rvv
    *** empty log message ***

    Revision 1.1  2013/09/18 15:37:28  rvv
    *** empty log message ***

 	
*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
include_once("orderControlleRekenClass.php");
session_start();

$subHeader     = "";
$mainHeader    = " overzicht";

$editScript = "tijdelijkebulkordersEdit.php";
$allow_add  = false;
$__appvar['rowsPerPage']=1000;

$list = new MysqlList();
$list->idField = "id";
$list->editScript = $editScript;
$list->perPage = $__appvar['rowsPerPage'];


$selectedPaginas=array();
foreach($_GET as $key=>$value)
{
  if(substr($key,0,7)=='pagina_')
  {
    $selectedPaginas[]=substr($key,7);
  }
}

$DB = new DB();
if($DB->QRecords("SELECT id FROM TijdelijkeBulkOrders WHERE change_user='$USR'"))
  $selectedUser=$USR;
if($_GET['user'])
  $selectedUser=$_GET['user'];
if($_GET['user'] == 'alles')  
  $selectedUser='';
  
if($selectedUser <> '')  
  $changeUserWhere=" AND change_user='$selectedUser'";
  
if($_GET['maakOrders']==1)
{
  $verwerk = new bulkOrderRegelsAanmaken();
  if(checkOrderAcces('verwerkenBulk_genereren') === true)
  {
    $ids=$verwerk->getIds($selectedPaginas,$selectedUser);

    foreach ($ids as $id)
    {
      $verwerk->verzamel($id);
    }
    if(count($ids)>0)
    {
      $verwerk->makeOrders();
      $regelInfo = $verwerk->counter." regels verwerkt. <br>\n";
    }
  }
  else
    $regelInfo = "Geen rechten om orders te verwerken. <br>\n";
}



$list->addColumn("TijdelijkeBulkOrders","id",array("list_width"=>"100","search"=>false));
$list->addColumn("TijdelijkeBulkOrders","pagina",array("list_width"=>"30","search"=>false));
$list->addColumn("TijdelijkeBulkOrders","portefeuille",array("list_width"=>"100","search"=>false));
$list->addColumn("TijdelijkeBulkOrders","client",array("list_width"=>"100","search"=>false));
$list->addColumn("TijdelijkeBulkOrders","ISINCode",array("list_width"=>"100","search"=>false));
$list->addColumn("TijdelijkeBulkOrders","fonds",array("list_width"=>"200","search"=>false));
$list->addColumn("TijdelijkeBulkOrders","aantal",array("list_width"=>"100","search"=>false));
$list->addColumn("TijdelijkeBulkOrders","transactieSoort",array("list_width"=>"100","search"=>false));
$list->addColumn("TijdelijkeBulkOrders","koersLimiet",array("list_width"=>"100","search"=>false));
$list->addColumn("TijdelijkeBulkOrders","checkResult",array("list_width"=>"100","search"=>false,'list_invisible'=>true));
$list->addColumn("TijdelijkeBulkOrders","checkResultRegels",array("list_width"=>"100","search"=>false,'list_invisible'=>true));

$DB->SQL("SELECT pagina FROM TijdelijkeBulkOrders WHERE 1 $changeUserWhere GROUP BY pagina ORDER BY change_date ASC");
$DB->Query();
$maxPagina=1;
while($pagina = $DB->NextRecord())
{
	//$paginaOptions .= "<option value=\"".$pagina['pagina']."\"  ".($selectedPagina==$pagina['pagina']?"selected":"")." >".$pagina['pagina']."</option>\n";
  
  $paginaOptions.="<input type='checkbox' onClick='document.editForm.submit();' name='pagina_".$pagina['pagina']."' value='1' ".($_GET['pagina_'.$pagina['pagina']]==1?"checked":"").">".$pagina['pagina']."<br>\n";
}
$DB = new DB();
$DB->SQL("SELECT add_user FROM TijdelijkeBulkOrders GROUP BY add_user ORDER BY add_user ASC");
$DB->Query();
while($pagina = $DB->NextRecord())
	$userOptions .= "<option value=\"".$pagina['add_user']."\"  ".($selectedUser==$pagina['add_user']?"selected":"")." >".$pagina['add_user']."</option>\n";

$content['pageHeader'] = "<br><div class='edit_actionTxt'><b>$mainHeader</b> $subHeader</div>".'
<form name="editForm"  method="GET">
<input type="hidden" name="maakOrders" value="">
<input type="hidden" name="checkOrders" value="">
<input type="hidden" name="xls" value="">
<div class="form">
<br>
<table><tr>
<td valign=top>
Pagina :
</td>
<td>
'.$paginaOptions.' 
</td>
</table>
</div>

<div class="form">
<br>
Gebuiker :
<select name="user" id="user" onChange="document.editForm.submit();">
<option value="alles">alles</option>
'.$userOptions.'
</select>
</div>

'.$regelInfo.'

';

$order = new orderControlleBerekening(true);
// set default sort
// $_GET['sort']      = "tablename.field";
// $_GET['direction'] = "ASC";
// set sort 
$list->setOrder($_GET['sort'],$_GET['direction']);
// set searchstring
$list->setSearch($_GET['selectie']);

$paginaWhere='';
if(count($selectedPaginas)>0)
{
  $paginaWhere=" AND pagina IN('".implode("','",$selectedPaginas)."')";
}

 $list->setWhere("1 $changeUserWhere  $paginaWhere ");
 
 
$list->setSelect();
// select page
$list->selectPage($_GET['page']);

$_SESSION['NAV'] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
$_SESSION['NAV']->addItem(new NavList($_GET['page'], $list->records(), $__appvar['rowsPerPage'],$allow_add));
$_SESSION['NAV']->addItem(new NavSearch($_GET['selectie']));


$content['javascript'] .= "
function addRecord() 
{
	parent.frames['content'].location = '".$editScript."?action=new';
}
";

echo template($__appvar["templateContentHeader"],$content);
?>


<table class="list_tabel" cellspacing="0">
<?=$list->printHeader();?>
<?php
$db=new DB();
$verwerkenOk=true;
while($data = $list->getRow())
{
	// $list->buildRow($data,$template="",$options="");
  if($_GET['checkOrders']==1)
  {
    if($data['id']['value'] != "" && $data['portefeuille']['value'] !="")
    {
      $order->setdata($data['id']['value'],$data['portefeuille']['value'],'EUR',$data['aantal']['value'],	true);
      if($data['checkResultRegels']['value'] <> '')
      {
       // $order->setchecks();
       	$order->setregels(unserialize($data['checkResultRegels']['value']));
      }
      else
      {
        $query = "SELECT Vermogensbeheerder FROM Portefeuilles 	WHERE portefeuille = '".$data['portefeuille']['value']."'";
        $db->SQL($query);
  	    $vermogenbeheerder = $db->lookupRecord();
  	    $vermogenbeheerder = $vermogenbeheerder['Vermogensbeheerder'];
        $checks = $order->getchecks($vermogenbeheerder);
        $checks = unserialize($checks[$vermogenbeheerder]);
        $order->setchecks($checks);
      }
      $resultaat = $order->check();
      $maxCheck=$order->checkmax();
      $query="UPDATE TijdelijkeBulkOrders SET checkResult='$maxCheck' WHERE id='".$data['id']['value']."'";
      $db->SQL($query);
      $db->Query();

    }
  }
  if($maxCheck > 0 || $data['checkResult']['value'] > 0)
  {
    $data["tr_class"] = "list_dataregel_rood";
    $verwerkenOk=false;
  }
  
	echo $list->buildRow($data);
}
?>
</table>
<?

$_SESSION['submenu'] = New Submenu();
$_SESSION['submenu']->addItem("Order check","#",array('target'=>'_self','onclick'=>"javascript:parent.content.location.href='tijdelijkebulkordersList.php?checkOrders=1&&user='+parent.frames['content'].document.editForm.user.value"));
$_SESSION['submenu']->addItem("<br>","");
//$_SESSION['submenu']->addItem("Naar XLS old","#",array('target'=>'_self','onclick'=>"javascript:parent.content.location.href='bulkordersXLS.php?xls=1&pagina='+parent.frames['content'].document.editForm.pagina.value+'&user='+parent.frames['content'].document.editForm.user.value")); 
$_SESSION['submenu']->addItem("Naar XLS","#",array('target'=>'_self','onclick'=>"javascript:parent.frames['content'].document.editForm.xls.value=1;parent.frames['content'].document.editForm.action='bulkordersXLS.php'; parent.frames['content'].document.editForm.submit();parent.frames['content'].document.editForm.xls.value=0; parent.frames['content'].document.editForm.action='tijdelijkebulkordersList.php';")); 

if($__appvar["bedrijf"]=='RCN')
{
  $_SESSION['submenu']->addItem("<br>","");
  $_SESSION['submenu']->addItem("SNS XLS","#",array('target'=>'_self','onclick'=>"javascript:parent.frames['content'].document.editForm.xls.value=2;parent.frames['content'].document.editForm.action='bulkordersXLS.php'; parent.frames['content'].document.editForm.submit();parent.frames['content'].document.editForm.xls.value=0; parent.frames['content'].document.editForm.action='tijdelijkebulkordersList.php';")); 
}

$_SESSION['submenu']->addItem("<br>","");
if($verwerkenOk==true)
 $_SESSION['submenu']->addItem("Order verwerk","#",array('target'=>'_self','onclick'=>"javascript:parent.frames['content'].document.editForm.maakOrders.value=1; parent.frames['content'].document.editForm.submit(); parent.frames['content'].document.editForm.maakOrders.value=0;"));



logAccess();
if($__debug) 
{
	echo getdebuginfo();
}
echo template($__appvar["templateRefreshFooter"],$content);



class bulkOrderRegelsAanmaken
{
  function bulkOrderRegelsAanmaken()
  {
    global $USR;
    $this->USR=$USR;
    $this->db = new DB();
    $this->db2 = new DB();
    $this->counter=0;
    $this->log = array();
    $this->orderData = array();
    $this->portefeuilles = array();
    $this->fondsen = array();
    
    


    
     $query="SELECT  Vermogensbeheerders.OrderStandaardTransactieType
     FROM Vermogensbeheerders
     Inner Join VermogensbeheerdersPerGebruiker ON VermogensbeheerdersPerGebruiker.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder
     WHERE VermogensbeheerdersPerGebruiker.Gebruiker='$USR' limit 1";

    $this->db->SQL($query);
    $this->standaard=$this->db->lookupRecord();
  }
  
  function verwijderId($id)
  {
     $query="DELETE FROM TijdelijkeBulkOrders WHERE id='$id'";
     $this->db->SQL($query);
     $this->db->Query();
     $this->counter++;
  }

  
  function getIds($paginas=array(),$user='')
  {
    $ids=array();
    $paginaWhere='';
    $userWhere='';
    if(count($paginas)>0)
      $paginaWhere.=" AND pagina IN ('".implode("','",$paginas)."')";
    if($user <> '')
      $userWhere.=" AND add_user='$user'";
    $query="SELECT id FROM TijdelijkeBulkOrders WHERE checkResult=0 $userWhere $paginaWhere";
    $this->db->SQL($query);
    $this->db->Query();
    while($data = $this->db->NextRecord())
      $ids[]=$data['id'];

    return $ids;
  }

  function verzamel($id)
  {
    $query="SELECT * FROM TijdelijkeBulkOrders WHERE  id='$id'"; //AND add_user='".$this->USR."'
    $this->db->SQL($query);
    $orderData=$this->db->lookupRecord();
    $query="SELECT depotbank FROM Portefeuilles WHERE portefeuille='".$orderData['portefeuille']."'";
    $this->db->SQL($query);
    $portData=$this->db->lookupRecord();
    $orderData['depotbank']=$portData['depotbank'];
    
    $this->portefeuilles[$orderData['portefeuille']] = $orderData['portefeuille'];
    $this->fondsen[$orderData['fonds']]=$orderData['fonds'];
    $this->orderData[$orderData['fonds']][$portData['depotbank']][$orderData['transactieSoort']][''.$orderData['koersLimiet']][]=$orderData;
  }
/*
  function verzamelFonds($fonds)
  {
    $query="SELECT * FROM TijdelijkeBulkOrders WHERE add_user='".$this->USR."' AND fonds='$fonds'";
    $this->db->SQL($query);
    $this->db->Query();
    while($orderData = $this->db->NextRecord())
    {
      $query="SELECT depotbank FROM Portefeuilles WHERE portefeuille='".$orderData['portefeuille']."'";
      $this->db2->SQL($query);
      $portData=$this->db2->lookupRecord();
      $orderData['Depotbank']=$portData['depotbank'];
      $this->orderData[$orderData['fonds']][$portData['depotbank']][$orderData['transactieSoort']][''.$orderData['koersLimiet']][]=$orderData;
    }
  }
*/
  function makeOrders()
  {
    foreach ($this->orderData as $fonds=>$depotbanken)
    {
      foreach ($depotbanken as $depotbank=>$typen)
      {
        foreach ($typen as $type=>$koersData)
        {
          foreach ($koersData as $koers=>$orderRegels)
             $this->makeOrder($type,$orderRegels);
        }
      }
    }
  }


  function makeOrder($type,$data)
  {
  global $db, $USR, $__appvar;

  $query="SELECT * FROM Fondsen WHERE Fonds='".$data[0]['fonds']."'";
  $this->db->SQL($query);
  $fonds=$this->db->lookupRecord();
  
  $query="SELECT OrderStandaardTijdsSoort FROM Vermogensbeheerders WHERE vermogensBeheerder = '".$__appvar['bedrijf']."'";
  $this->db->SQL($query);
  $vermogensbeheerder=$this->db->lookupRecord();
  
  if($this->standaard['OrderStandaardTransactieType'] <> '')
   $transactieType=$this->standaard['OrderStandaardTransactieType'];
  else
   $transactieType='L';
  
  if($transactieType=='L')
  {
    if($vermogensbeheerder['OrderStandaardTijdsSoort']=='GTC')
    {
      $tijdsSoort='GTC';
      $tijdsLimiet="''";
    }
    else
    {
      $tijdsSoort='DAT';
      $tijdsLimiet='NOW()';  
    }
  }
  else
  {
    $tijdsSoort='';
    $tijdsLimiet="''";
  }

  $query  = "INSERT INTO Orders SET ";
  $query .= "  vermogensBeheerder = '".$__appvar['bedrijf']."'";
  $query .= ", fondsCode          = '".$fonds["ISINCode"]."' ";
  $query .= ", fonds              = '".mysql_escape_string($fonds["Fonds"])."' ";
  $query .= ", fondsOmschrijving  = '".mysql_escape_string($fonds["Omschrijving"])."' ";
  $query .= ", transactieType     = '$transactieType' ";
  $query .= ", koersLimiet        = '".$data[0]["koersLimiet"]."' ";
  $query .= ", tijdsLimiet        = $tijdsLimiet ";
  $query .= ", tijdsSoort         = '$tijdsSoort' ";
  $query .= ", transactieSoort    = '$type' ";
  $query .= ", laatsteStatus      = 0";
  $query .= ", Depotbank          = '".$data[0]["depotbank"]."' ";
  $query .= ", status             = '".date("Ymd_Hi")."/".$data[0]["add_user"]." - aangemaakt via bulkorders.\n'";
  $query .= ", add_user           = '".$data[0]["add_user"]."' ";
  $query .= ", add_date           = NOW() ";

  $this->db->SQL($query);
  $this->db->Query();
  $orderIdent = $this->db->last_id();
  $orderid = $__appvar['bedrijf'].$orderIdent;

  $this->db->SQL("UPDATE Orders SET orderid ='".$orderid."'  WHERE id = '".$orderIdent."' ");
  $this->db->Query();

  $aantalTotaal=0;
  $x=1;
  foreach ($data as $orderRegel)
  {
    $query="SELECT * FROM Portefeuilles WHERE Portefeuille='".$orderRegel['portefeuille']."'";
    $this->db->SQL($query);
    $portefeuille=$this->db->lookupRecord();

    $query="SELECT * FROM Clienten WHERE Client='".$portefeuille['Client']."'";
    $this->db->SQL($query);
    $client=$this->db->lookupRecord();

    $this->db->SQL("SELECT * FROM Rekeningen WHERE Portefeuille = '".$orderRegel["portefeuille"]."' AND Valuta = 'EUR' AND Memoriaal = 0 AND Termijnrekening = 0 AND Inactief=0");
    $rekeningRec = $this->db->lookupRecord();
    $rekNr = ereg_replace("[^0-9]","",$rekeningRec['Rekening']);

    $aantal = $orderRegel['aantal'];
    $aantalTotaal += $aantal;

    $queryf = "SELECT koers,Fonds,Datum FROM Fondskoersen WHERE Fonds = '".$fonds['Fonds']."' ORDER BY Datum DESC LIMIT 1";
    $this->db->SQL($queryf);
    $fondsKoers = $this->db->lookupRecord();

    $queryf = "SELECT koers,Valuta,Datum FROM Valutakoersen WHERE Valuta = '".$fonds['Valuta']."' ORDER BY Datum DESC LIMIT 1";
    $this->db->SQL($queryf);
    $valutaKoers = $this->db->lookupRecord();

    $order = new orderControlleBerekening();
    $order->setdata($orderid,$orderRegel["portefeuille"],'EUR',abs($aantal),true);
    $squery = "SELECT Vermogensbeheerder FROM Portefeuilles WHERE portefeuille = '".$orderRegel["portefeuille"]."'";
    $checks = $order->getchecks();
    $this->db->SQL($squery);
	  $vermogenbeheerder = $this->db->lookupRecord();
	  $vermogenbeheerder = $vermogenbeheerder['Vermogensbeheerder'];
    $checks = unserialize($checks[$vermogenbeheerder]);
    $order->setchecks($checks);
    $resultaat = $order->check();

    //echo abs($aantal)."*".$fonds['Fondseenheid']."*".$fondsKoers['koers']."<br>\n";
    $tmp["brutoBedragValuta"]=abs($aantal)*$fonds['Fondseenheid']*$fondsKoers['koers'];
    $tmp["brutoBedrag"]=$tmp["brutoBedragValuta"]*$valutaKoers['koers'];

    $ordQ  = "INSERT INTO OrderRegels SET ";
    $ordQ .= "  orderid      = '".$orderid."' ";
    $ordQ .= ", positie      = '".$x."' ";
    $ordQ .= ", portefeuille = '".$orderRegel["portefeuille"]."'";
    $ordQ .= ", rekeningnr   = '".$rekNr."'";
    $ordQ .= ", brutoBedragValuta   = '". $tmp["brutoBedragValuta"]."'";
    $ordQ .= ", brutoBedrag   = '". $tmp["brutoBedrag"]."'";
    $ordQ .= ", valuta       = 'EUR'";
    $ordQ .= ", controle     = '".$order->checkmax()."'";
    $ordQ .= ", aantal       = '".abs($aantal)."'";
    $ordQ .= ", client       = '".mysql_escape_string($client["Naam"])."'";
    $ordQ .= ", status       = 0";
    $ordQ .= ", add_user     = '".$orderRegel["add_user"]."' ";
    $ordQ .= ", add_date     = NOW() ";

    $this->db->SQL($ordQ);
    $this->db->Query();
    $this->verwijderId($orderRegel['id']);
    $x++;
  }
  
  if(count($data) > 1)
    $orderSoort='M';
  else
  {
    if(count($this->portefeuilles)==1 && count($this->fondsen) > 1)
      $orderSoort='C';
    else  
      $orderSoort='E';
  }
  if($this->lastPortefeuille==$orderRegel["portefeuille"] && $orderSoort=='C')
  {
    $newBatchId=$this->lastBatchId;
  }
  else
  {
    $cfg=new AE_config();
    $newBatchId=$cfg->getData('lastOrderBatchId')+1;
    $cfg->addItem('lastOrderBatchId',$newBatchId);
  }   

  $query  = "UPDATE Orders SET orderid ='".$orderid."' , OrderSoort='$orderSoort', BatchId='$newBatchId'  ";
  $query .= ", aantal = '".abs($aantalTotaal)."'";
  //$query .= ", koersLimiet = '".$fondsKoers['koers']."'";
  $query .= " WHERE id = '".$orderIdent."'";
  $this->db->SQL($query);
  $this->db->Query();
  $this->lastPortefeuille=$orderRegel["portefeuille"];
  $this->lastBatchId=$newBatchId;  
  
  return true;
  }

}

?>