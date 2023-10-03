<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2020/04/22 15:39:13 $
 		File Versie					: $Revision: 1.7 $

 		$Log: CRM_naw_historie.php,v $
 		Revision 1.7  2020/04/22 15:39:13  rvv
 		*** empty log message ***
 		
 		Revision 1.6  2019/09/28 17:17:48  rvv
 		*** empty log message ***
 		
 		Revision 1.5  2019/09/22 08:42:35  rvv
 		*** empty log message ***
 		
 		Revision 1.4  2019/09/21 16:53:54  rvv
 		*** empty log message ***
 		
 		Revision 1.3  2019/09/21 16:30:12  rvv
 		*** empty log message ***
 		
 		Revision 1.2  2019/09/07 16:06:15  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2019/08/10 17:26:32  rvv
 		*** empty log message ***
 		
 		Revision 1.1  2007/10/09 06:27:49  cvs
 		CRM update DGC
 		


*/


//$AEPDF2=true;
include_once("wwwvars.php");
define('FPDF_FONTPATH',$__appvar["basedir"]."/html/font/");
include_once($__appvar["basedir"]."/classes/AE_cls_fpdf.php");

if (!$_GET["rel_id"] )
{
  echo "foute aanroep";
  exit();
}


$formdata=array_merge($_GET,$_POST);

$relatieId = $formdata["rel_id"];
$oudeDatum = $formdata["datum"];//'2010-01-01';

$crmHist=new crm_naw_historie($relatieId,$oudeDatum);
$data=$crmHist->getData();
if($formdata["uitvoer"]=='pdf')
{
  $crmHist->createPDF($data);
}
elseif($formdata["uitvoer"]=='xls')
{
  $crmHist->createXLS($data);
}
else
{
  echo template($__appvar["templateContentHeader"],$content);
  echo $crmHist->createHtml($data);
  echo template($__appvar["templateContentFooter"],$content);
}


class crm_naw_historie
{
  
  function crm_naw_historie($relatieId,$oudeDatum)
  {
    $this->relatieId=$relatieId;
    $this->oudeDatum=$oudeDatum;
    $this->mutatieDagen=array();
  
    $query = "SELECT date(add_date) as datum FROM trackAndTrace WHERE tabel='CRM_naw' AND recordId='".$this->relatieId."'  GROUP BY datum ORDER BY datum desc";
    $db = new DB();
    $db->SQL($query);
    $db->query();
    while ($data = $db->nextRecord())
    {
      $this->mutatieDagen[]=$data['datum'];
    }
  }

  function getData()
  {

  
    $NAWobject = new Naw();
    $NAWobject->getById($this->relatieId);
  
  
    $db = new DB();
    $query = "SELECT VermogensbeheerdersPerGebruiker.Vermogensbeheerder, Vermogensbeheerders.CrmPortefeuilleInformatie,Vermogensbeheerders.Layout,Vermogensbeheerders.CRM_eigenTemplate,Vermogensbeheerders.check_module_SCENARIO
        FROM Vermogensbeheerders Join VermogensbeheerdersPerGebruiker ON VermogensbeheerdersPerGebruiker.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder AND VermogensbeheerdersPerGebruiker.Gebruiker = '$USR' GROUP BY VermogensbeheerdersPerGebruiker.Vermogensbeheerder  limit 1";
    $db->SQL($query);
    $gebruikPortefeuilleInformatie = $db->lookupRecord();
    $nieuweVelden=array();
    $nieuweVolgorde=array();
    if ($gebruikPortefeuilleInformatie['CRM_eigenTemplate'] == 1)
    {
      $query = "SELECT veldenPerTab FROM `CRM_naw_templates` WHERE intake=0 order by change_date desc limit 1";
      $db->SQL($query);
      $customFields = $db->lookupRecord();
      $customFields = unserialize($customFields['veldenPerTab']);
      $naw = new Naw();
      foreach ($customFields as $tab => $tabdata)
      {
        if ($tabdata['naam'] <> '' && $tabdata['object'] == 'Naw')
        {
          $nieuweVolgorde[] = $tabdata['naam'];
          foreach ($tabdata['velden'] as $key => $waarden)
          {
            $nieuweVelden[$tabdata['naam']][$key] = $waarden;
          }
        }
      }
    }
    else
    {
      $tmp = array("Algemeen", "Adres", "Verzendadres", "Telefoon", "Internetgegevens", "Bedrijfinfo", "Persoonsinfo", "Legitimatie", "Informatie partner", "Legitimatie partner", "Adviseurs", "geen", 'Contract', 'Beleggen', 'Rapportage', 'Profiel', 'Relatie geschenk', 'Recordinfo');
      foreach($tmp as $categorie)
      {
        $nieuweVolgorde[$categorie] = $categorie;
      }
      foreach ($NAWobject->data['fields'] as $veld => $velddata)
      {
        if($velddata['categorie']=='hidden')
          continue;
        $nieuweVelden[$velddata['categorie']][$veld] = $velddata;
        $nieuweVolgorde[$velddata['categorie']] = $velddata['categorie'];
      }
    }
  
  
    $huidigeData = array();
    $historischieDataOpDatum = array();
    foreach ($NAWobject->data['fields'] as $key => $data)
    {
      $huidigeData[$key] = $data['value'];
    }
    $historischieData = $huidigeData;
    $recordInfoVelden = array('change_date', 'change_user');
    $query = "SELECT veld,date(add_date) as datum,oudeWaarde,nieuweWaarde,add_date as change_date,add_user as change_user
FROM trackAndTrace WHERE tabel='CRM_naw' AND recordId='".$this->relatieId."' AND add_date>'".$this->oudeDatum."' ORDER BY add_date desc";
    $db = new DB();
    $db->SQL($query);
    $db->query();
    $oudste = array();
    while ($data = $db->nextRecord())
    {
    
      if (!isset($laatsteDatum) || $laatsteDatum != $data['datum'])
      {
        $oudste = array();
      }
    
      $oudste[$data['veld']] = $data['oudeWaarde'];
      $historischieDataOpDatum[$data['datum']][$data['veld']] = $data['oudeWaarde'];
      $historischieData[$data['veld']] = $data['oudeWaarde'];
      foreach ($recordInfoVelden as $infoVeld)
      {
        $historischieData[$infoVeld] = $data[$infoVeld];
      }
    }
    
    return array('huidigeData'=>$huidigeData,'nieuweVolgorde'=>$nieuweVolgorde,'nieuweVelden'=>$nieuweVelden,'historischieData'=>$historischieData,'historischieDataOpDatum'=>$historischieDataOpDatum);
  }
  
  function createSelect($values,$selectedDatum)
  {
    $html="<select name='datum' id='datum' onchange=\"document.editForm.target='_self';document.editForm.uitvoer.value='html';document.editForm.submit();\">";

   foreach($values as $datum)
   {
     if($datum==$selectedDatum)
       $selected='selected';
     else
       $selected='';
     $html.="<option value='$datum' $selected >$datum </option>\n";
   }
  
    $html.="</select>";
     return $html;
  }


  function createPDF($data)
  {
  $huidigeData=$data['huidigeData'];
  $nieuweVolgorde=$data['nieuweVolgorde'];
  $nieuweVelden=$data['nieuweVelden'];
  $historischieData=$data['historischieData'];

    $pdf = new FPDF();
  $pdf->AddPage('P');
  $widths = array(65, 55, 55);
  $h = 4;
  $pdf->SetFont('Arial', 'b', 10);
  $pdf->Cell($widths[0], $h, 'Categorie/Veld', 0, 0, 'L');
  $pdf->Cell($widths[1], $h, 'Waarde op ' . $this->oudeDatum, 0, 0, 'L');
  $pdf->Cell($widths[2], $h, 'Huidige waarde (indien aangepast)', 0, 1, 'L');
  
  foreach ($nieuweVolgorde as $cateogorie)
  {
    $pdf->SetFont('Arial', 'b', 10);
    $pdf->Cell($widths[0], $h, $cateogorie, 0, 1, 'L');
    $pdf->SetFont('Arial', '', 10);
    foreach ($nieuweVelden[$cateogorie] as $veld => $veldData)
    {
      $pdf->Cell($widths[0], $h, $veld, 0, 0, 'L');
      $pdf->Cell($widths[1], $h, $historischieData[$veld], 0, 0, 'L');
      if ($huidigeData[$veld] <> $historischieData[$veld])
      {
        $pdf->SetFont('Arial', 'i', 10);
        $pdf->Cell($widths[2], $h, $huidigeData[$veld], 0, 0, 'L');
        $pdf->SetFont('Arial', '', 10);
      }
      $pdf->ln($h);
      
    }
    $pdf->ln($h);
  }
  
  $pdf->Output();
  }

  function createXLS($data)
  {
    require_once("../classes/AE_cls_xls.php");

    $huidigeData=$data['huidigeData'];
    $nieuweVolgorde=$data['nieuweVolgorde'];
    $nieuweVelden=$data['nieuweVelden'];
    $historischieData=$data['historischieData'];

    $xls = new AE_xls();
    $xls->excelOpmaak['header']=array('setAlign'=>'centre','setBgColor'=>'22','setBorder'=>'1');
    $xls->excelOpmaak['kopl']=array('setAlign'=>'left','setBold'=>1);
    $xls->excelOpmaak['kopr']=array('setAlign'=>'left');
    $xls->setColumn[] = array(0,0,30);
    $xls->setColumn[] = array(1,1,30);
    $xls->setColumn[] = array(2,2,30);
    $xlsData[]=array(array("Categorie/Veld",'header'),array('Waarde op ' . $this->oudeDatum,'header'),array('Huidige waarde (indien aangepast)','header'));
    foreach ($nieuweVolgorde as $cateogorie)
    {
      $xlsData[]=array(array($cateogorie,'kopl'));
      foreach ($nieuweVelden[$cateogorie] as $veld => $veldData)
      {
        $row=array();
        $row[]=$veld;
        $row[]=$historischieData[$veld];
        if ($huidigeData[$veld] <> $historischieData[$veld])
        {
          $row[]=$huidigeData[$veld];
        }
        $xlsData[]=$row;

      }
      $xlsData[]=array('');
    }

    $xls->setData($xlsData);
    $xls->OutputXls();
  }
  
  function createHTML($data)
  {
    $huidigeData=$data['huidigeData'];
    $nieuweVolgorde=$data['nieuweVolgorde'];
    $nieuweVelden=$data['nieuweVelden'];
    $historischieData=$data['historischieData'];

  
    $body='<form name="editForm" method="post">
<input type="hidden" name="rel_id" value="'.$this->relatieId.'">
<input type="hidden" name="uitvoer" value="html">
<table>';
    $body.='<tr><td>
<button onclick="document.editForm.uitvoer.value=\'pdf\';document.editForm.target=\'_blank\';document.editForm.submit();">Create PDF</button>
<button onclick="document.editForm.uitvoer.value=\'xls\';document.editForm.target=\'_blank\';document.editForm.submit();">Create XLS</button>
</td></tr>';
    $body.='<tr><td>Categorie/Veld</td>';
    $body.='<td>Gegevens per '. $this->createSelect($this->mutatieDagen,$this->oudeDatum).'</td>';
    $body.='<td>Huidige waarde (indien aangepast)</td>';
    $body.='</tr>';
    foreach ($nieuweVolgorde as $cateogorie)
    {
      $body.="<tr><td><b>$cateogorie </b></td></tr>";
      
      
      foreach ($nieuweVelden[$cateogorie] as $veld => $veldData)
      {
        $body.="<tr><td>$veld</td>";
        $body.="<td>".$historischieData[$veld]."</td>";
        if ($huidigeData[$veld] <> $historischieData[$veld])
        {
          $body.="<td>".$huidigeData[$veld]."</td>";
        }
        $body.="</tr>\n";
      }
      
   
    }
    $body.='</table></form>';
    
    return $body;
  }

}
?>