<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2018/08/18 12:40:14 $
 		File Versie					: $Revision: 1.12 $

 		$Log: fondsFondskoersen.php,v $
 		Revision 1.12  2018/08/18 12:40:14  rvv
 		php 5.6 & consolidatie
 		
 		Revision 1.11  2017/07/09 07:42:58  rvv
 		*** empty log message ***
 		
 		Revision 1.10  2017/07/09 07:38:55  rvv
 		*** empty log message ***
 		
 		Revision 1.9  2016/12/03 19:23:15  rvv
 		*** empty log message ***
 		
 		Revision 1.8  2015/10/28 15:45:40  rm
 		4140
 		
 		Revision 1.6  2013/12/21 18:30:19  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2013/02/06 19:04:06  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2013/02/03 09:03:11  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2013/01/27 13:59:54  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2013/01/09 17:06:28  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2012/12/30 14:25:57  rvv
 		*** empty log message ***
 		
 	
*/

include_once("wwwvars.php");
include_once("../classes/editObject.php");

$__funcvar['listurl']  = "fondsList.php";
$__funcvar['location'] = "fondsEdit.php";

if( requestType('ajax') ) {
    /** selecteer ajax templates **/
    $__appvar['templateContentHeader'] = 'templates/ajax_head.inc';
    $__appvar['templateRefreshFooter'] = 'templates/ajax_voet.inc';
    
    $content['script_voet'] = "
      $('#addFondsKoers').on('click', function (event) {
        event.preventDefault();
        loadToDiv('modelContent', $(this).attr('href'));
      });
      
      $('#fondskoersenList .list_button  a').on('click', function (event) {
        event.preventDefault();
        loadToDiv('modelContent', $(this).attr('href'));
      });

    ";
}



echo template($__appvar["templateContentHeader"],$content);

$object = new Fonds();

$editObject = new editObject($object);
$editObject->__funcvar = $__funcvar;
$editObject->__appvar = $__appvar;

$object->getById($_GET['id']);

if($object->get('Fonds') <> '')
{
  include_once("../classes/mysqlList.php");
  $list = new MysqlList();
$list->idField = "id";
$list->editScript = 'fondskoersenEdit.php';
$list->perPage = $__appvar['rowsPerPage'];
$list->noExport=true;


	$_GET['sort'] = array("Datum");
	$_GET['direction'] = array("DESC");

  if($__appvar['bedrijf']=='BOX' || $__appvar['bedrijf']=='TEST')
    $toonKoersDatum=true;
  else
    $toonKoersDatum=false;

$list->addField("Fondskoersen","id",array("width"=>100,"search"=>false));
$list->addField("Fondskoersen","Fonds",array("list_width"=>150,"search"=>true));
$list->addField("Fondskoersen","Datum",array("search"=>false));
$list->addField("Fondskoersen","Koers",array("list_width"=>100,"search"=>false,"align"=>"right"));
if($toonKoersDatum)
  $list->addField("Fondskoersen", "oorspKrsDt", array("list_width" => 100, "search" => false, "align" => "right"));

  $list->setWhere(" Fonds = '".$object->get('Fonds')."' ");
$list->setOrder($_GET['sort'],$_GET['direction']);
$list->setSearch(isset ($_GET['selectie']) ? $_GET['selectie'] : null);
$list->selectPage(isset ($_GET['page']) ? $_GET['page'] : null);

$koersenList='<table class="list_tabel" cellspacing="0">';
//$koersenList.=$list->printHeader();

$koersenList.='<table class="list_tabel" cellspacing="0"><colgroup><col /><col width="150"/><col width="150"/><col width="100"/></colgroup>
<tr class="list_kopregel">
<td class="list_button">&nbsp;</td>
<td class="list_kopregel_data">&nbsp;' . vt('Fonds') . '</td>
<td class="list_kopregel_data">&nbsp;' . vt('Datum') . '</td>
<td class="list_kopregel_data">&nbsp;' . vt('Koers') . '</td>
 '.(($toonKoersDatum==true)?'<td class="list_kopregel_data">&nbsp;' . vt('oorspKrsDt') . '</td>':'').'
</tr>';

$template='<tr class="list_dataregel" onmouseover="this.className=\'list_dataregel_hover\'" onmouseout="this.className=\'list_dataregel\'" title="Klik op de knop links om de details te zien/muteren">
<td class="list_button"><div class="icon">
<a href="fondskoersenEdit.php?action=edit&id={id_value}&frame=1\';"><img src="images//16/muteer.gif" width="16" height="16" border="0" alt="" align="absmiddle">&nbsp;</a></div></td>
<td class="listTableData" width="150"  align="left" >{Fonds_value} &nbsp;</td>
<td class="listTableData"   align="left" >{Datum_value} &nbsp;</td>
<td class="listTableData" width="100"  align="right" >{Koers_value} &nbsp;</td>
 '.(($toonKoersDatum==true)?'<td class="listTableData" width="100"  align="right" >{oorspKrsDt_value} &nbsp;</td>':'').'
</tr>';

while($data = $list->getRow())
{
	$koersenList.= $list->buildRow($data,$template,"");
}

$koersenList.= '</table>';
  
  
  
	$db=new DB();
	$query="SELECT year(Datum) as jaar FROM Fondskoersen WHERE Fonds='".$object->get('Fonds')."' GROUP BY jaar ORDER By jaar";
	$db->SQL($query);
	$db->Query();
	$jaren=array();
	while($data= $db->nextRecord())
	  $jaren[]=$data['jaar'];

	$koersenHtml="<table border=1>\n<tr class=\"list_kopregel\"><td class=\"list_kopregel_data\" width=100>" . vt('Datum') . "</td><td class=\"list_kopregel_data\" width=100 align=right>" . vt('Koers') . "</td></tr>\n";
	foreach ($jaren as $jaar)
	{
	  $query="SELECT Koers,Datum FROM Fondskoersen WHERE Fonds='".$object->get('Fonds')."' AND year(Datum)='$jaar' ORDER BY Datum desc limit 1";
	  $db->SQL($query);
	  $db->Query();
	  $data= $db->nextRecord();
	  $koersenHtml.="<tr><td>".dbdate2form($data['Datum'])."</td><td align=right>".$data['Koers']."</td></tr>\n";
	}
	$koersenHtml.="</table>\n";
  
    $laatsteKoers=getLaatsteValutadatum();
    $perioden=array('1-jaars'=>'date(\''.$laatsteKoers.'\')-interval 1 year',
                    '3-jaars'=>'date(\''.$laatsteKoers.'\')-interval 3 year',
                    '5-jaars'=>'date(\''.$laatsteKoers.'\')-interval 5 year');
  	$koersenHtml.="<table border=1>\n<tr class=\"list_kopregel\">
    <td class=\"list_kopregel_data\" width=100>" . vt('Periode') . "</td>
    <td class=\"list_kopregel_data\" width=100 align=right>" . vt('STDDEV') . "</td>
    <td class=\"list_kopregel_data\" width=100 align=right>" . vt('Aantal') . "</td></tr>\n";
 	  
    foreach($perioden as $periode=>$wherePeriode) 
    {
      $query="select Datum FROM Fondskoersen WHERE Fonds='".$object->get('Fonds')."' AND Datum < $wherePeriode limit 1";
      $db->SQL($query); 
      $beschikbaar=$db->lookupRecord();
      
      $query="SELECT koers FROM Fondskoersen WHERE Fonds='".$object->get('Fonds')."' AND Datum > $wherePeriode";
	    $db->SQL($query); 
	    $db->Query();
      unset($laatsteKoers);
      $koersRendementen=array();
	    while($data= $db->nextRecord())
      {
        if(isset($laatsteKoers) && $laatsteKoers != 0)
          $koersRendementen[]=($data['koers']/$laatsteKoers)*100;
        $laatsteKoers=$data['koers'];
      }
      $sdtev=standard_deviation($koersRendementen);
     if($beschikbaar['Datum'] == '')
        $stddeviatieJaar='na';
      else
      {
        $jaren=substr($periode,0,1);
        $stddeviatieJaar=round($sdtev*pow((count($koersRendementen)/$jaren),0.5),2);
        //echo "$stddeviatieJaar=round($sdtev*pow((".count($koersRendementen)."/$jaren),0.5),2);<br>\n";
      }  
      $koersenHtml.="<tr><td>$periode</td>
                         <td align=right>$stddeviatieJaar</td>
                         <td align=right>".count($koersRendementen)."</td></tr>\n";
    }
    $koersenHtml.="</table>\n";
}  

if($_SESSION['usersession']['gebruiker']['Beheerder'])
echo "
<a id='addFondsKoers' href='fondskoersenEdit.php?action=new&frame=1&base64=1&Fonds=".base64_encode($object->get('Fonds'))."'>
<img src=\"icon/16/add.png\" class=\"simbisIcon\"> " . vt('Koers toevoegen') . "
</a>";

echo "<table><tr><td>$koersenList</td><td valign=\"top\">$koersenHtml</td></table>";
echo template($__appvar["templateRefreshFooter"],$content);

echo $editObject->getOutput();

if ($result = $editObject->result)
{
 	header("Location: ".$returnUrl);
}
else {
	echo $_error = $editObject->_error;
}
?>