<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2018/12/14 16:41:01 $
 		File Versie					: $Revision: 1.43 $

 		$Log: CRM_naw_rtfMergeList.php,v $
 		Revision 1.43  2018/12/14 16:41:01  rvv
 		*** empty log message ***
 		
 		Revision 1.42  2017/03/27 06:30:48  rvv
 		*** empty log message ***
 		
 		Revision 1.41  2015/05/29 12:45:05  rvv
 		*** empty log message ***
 		
 		Revision 1.40  2015/05/27 11:52:51  rvv
 		*** empty log message ***
 		
 		Revision 1.39  2014/07/02 16:03:16  rvv
 		*** empty log message ***
 		
 		Revision 1.38  2014/02/22 18:42:25  rvv
 		*** empty log message ***
 		
 		Revision 1.37  2014/02/05 15:56:39  rvv
 		*** empty log message ***
 		
 		Revision 1.36  2013/09/28 14:42:13  rvv
 		*** empty log message ***
 		
 		Revision 1.35  2013/07/25 14:00:54  rvv
 		*** empty log message ***
 		
 		Revision 1.34  2013/07/24 15:47:04  rvv
 		*** empty log message ***
 		
 		Revision 1.33  2013/06/15 15:54:02  rvv
 		*** empty log message ***
 		
 		Revision 1.32  2013/06/01 16:14:14  rvv
 		*** empty log message ***
 		
 		Revision 1.31  2012/08/11 13:05:20  rvv
 		*** empty log message ***
 		
 		Revision 1.30  2012/08/08 15:38:48  rvv
 		*** empty log message ***

 		Revision 1.29  2012/08/05 10:45:21  rvv
 		*** empty log message ***

 		Revision 1.28  2012/01/08 10:15:17  rvv
 		*** empty log message ***

 		Revision 1.27  2011/05/25 17:27:47  rvv
 		*** empty log message ***

 		Revision 1.26  2011/05/09 12:53:14  rvv
 		*** empty log message ***

 		Revision 1.25  2011/04/30 16:23:58  rvv
 		*** empty log message ***

 		Revision 1.24  2011/03/02 20:11:35  rvv
 		*** empty log message ***

 		Revision 1.23  2011/02/28 12:49:37  rvv
 		*** empty log message ***

 		Revision 1.22  2011/02/26 16:00:39  rvv
 		*** empty log message ***

 		Revision 1.21  2010/11/27 16:15:25  rvv
 		*** empty log message ***

 		Revision 1.20  2010/11/14 10:49:32  rvv
 		*** empty log message ***

 	*/

include_once("wwwvars.php");
$cfg=new AE_config();
$standaardbrief = $cfg->getData('standaardbrief');
$rtfDateFormat=$cfg->getData('rtfDateFormat');
$rtfGetalFormat=$cfg->getData('rtfGetalFormat');
if($rtfDateFormat=='')
  $rtfDateFormat='%d-%m-%Y';

$postdata = array_merge($_POST,$_GET);
$dateConversion=array('%d-%m-%Y'=>'%d-%m-%Y','%d %M %Y'=>'%d %B %Y');
$phpDateFormat = $dateConversion[$rtfDateFormat];

$customTemplate = new AE_CustomTemplate('crmSjabloon');
$content = $editcontent;
//setlocale(LC_ALL, 'nl_NL');
$postData = array_merge($_POST, $_GET);
$_POST = $postData;
$_GET = $postData;

if ($postData["action"] == "print")
{
  $useTemplate = null;

  if ( isset ($postData['sjabloonType']) && $postData['sjabloonType'] === 'pdf-tab' && ! empty ($postData['template']) ) {
    $template = $customTemplate->getTemplate((int) $postData['template']);
    $useTemplate = ! empty ($template) ? $template : null;
  }

  include("../classes/AE_cls_rtfBase.php");

  $tables = array('CRM_naw',"Portefeuilles","laatstePortefeuilleWaarde");//'CRM_naw_cf'
  $db = new DB();

/*
  $db->SQL("SELECT version() as version");
  $mysqlVersion=$db->lookupRecord();
  $mysqlVersion = ereg_replace("[^0-9\.]", "", $mysqlVersion['version']);
  $versionParts=explode(".",$mysqlVersion);
  if(($versionParts[0] >= 5) || ( $versionParts[0] == 4 && $versionParts[1] > 1) || ( $versionParts[0] == 4 && $versionParts[1] == 1 && $versionParts[2] >= 21))
    $MySQLDateformat=true;
  else
    $MySQLDateformat=false;
*/
//$MySQLDateformat=false;
  $dateFields=array();
  foreach ($tables as $table)
  {
     $query = "SHOW fields FROM $table ";
     $db->SQL($query);
     $db->Query();
     while($data = $db->nextRecord())
     {
     
       $type=substr($data['Type'],0,4);

       switch ($type)
       {
         case 'date':
           if($rtfDateFormat=='%d-%m-%Y')
             $fields[$data['Field']] = array('select'=>"if($table".'.'.$data['Field']." <> '0000-00-00',DATE_FORMAT($table".'.'.$data['Field'].",\"$rtfDateFormat\"),'') as ".$data['Field']);
           else
           {
             $fields[$data['Field']] = array('select'=>"$table".'.'.$data['Field']." as ".$data['Field']);
             $dateFields[$data['Field']]=$data['Field'];
           }
         break;
         case 'char':
            if($data['Type']=='char(1)')
               $fields[$data['Field']] = array('select'=>"replace(replace($table.".$data['Field'].",'N','Nee'),'J','Ja')  as ".$data['Field']);
            else
              $fields[$data['Field']] = array('select'=>$table.'.'.$data['Field']);   
         break;
         case 'doub':
         case 'int(':
           if($rtfGetalFormat=='1000')
             $fields[$data['Field']] = array('select'=>"replace(round(`$table`.`".$data['Field']."`, 0),'.',',')  as `".$data['Field']."`");
           elseif($rtfGetalFormat=='1000,00')  
             $fields[$data['Field']] = array('select'=>"replace(round(`$table`.`".$data['Field']."`, 2),'.',',')  as `".$data['Field']."`");
           elseif($rtfGetalFormat=='1.000')  
             $fields[$data['Field']] = array('select'=>"replace(replace(replace(format(convert(replace(`$table`.`".$data['Field']."`, ',', '.'), decimal(10,0)), 0), ',', 'x'), '.', ','), 'x', '.')  as `".$data['Field']."`");
           elseif($rtfGetalFormat=='1.000,00')   
             $fields[$data['Field']] = array('select'=>"replace(replace(replace(format(convert(replace(`$table`.`".$data['Field']."`, ',', '.'), decimal(12,2)), 2), ',', 'x'), '.', ','), 'x', '.')  as `".$data['Field']."`");
           else
             $fields[$data['Field']] = array('select'=>'`'.$table.'`.`'.$data['Field'].'`');
         break;
         default:
           $fields[$data['Field']] = array('select'=>'`'.$table.'`.`'.$data['Field'].'`');
       }
     }
  }

  $unsetFields = array('add_date','change_date','add_user','change_user');
  foreach ($unsetFields as $field)
    unset($fields[$field]);
  ksort($fields);
  $n=0;


  $query = "SELECT (SELECT Naam FROM Gebruikers WHERE Gebruiker ='$USR') as Gebruiker, ";
  foreach ($fields as $key=>$waarden)
  {
    if($n >0)
      $query .= ",\n ";
    $query .= $waarden['select'];
    $n++;
  }
  $query .= " FROM CRM_naw ".
                 //  LEFT JOIN CRM_naw_cf    ON CRM_naw.id          = CRM_naw_cf.rel_id
                 " LEFT JOIN Portefeuilles ON CRM_naw.portefeuille = Portefeuilles.Portefeuille 
                   LEFT JOIN laatstePortefeuilleWaarde ON CRM_naw.portefeuille = laatstePortefeuilleWaarde.portefeuille ";
  $query .= " WHERE CRM_naw.id = '".$postdata['deb_id']."'";


  $rtf = new rtfBase($postData['file']);
  $NAW = new db();

/*
  if($MySQLDateformat==true)
  {
    $NAW->SQL("SET lc_time_names = 'nl_NL'");
    $NAW->Query();
  }
*/

  $NAW->SQL($query);
  $nawRec = $NAW->lookupRecord();

  foreach ($dateFields as $dateField)
  {
    if($nawRec[$dateField] == '0000-00-00')
    {
      $nawRec[$dateField]='';
    }
    else
    {
      $nawRec[$dateField]=adodb_db2jul($nawRec[$dateField]);
      $nawRec[$dateField]=adodb_date('d',$nawRec[$dateField])." ".$__appvar["Maanden"][adodb_date('n',$nawRec[$dateField])]." ".adodb_date('Y',$nawRec[$dateField]); //$nawRec[$dateField]=strftime ($phpDateFormat,$nawRec[$dateField]);
    }
  }
  if($postData['contact'] >0)
  {
    $nawRec=array();
    $query="SELECT naam,naam1,functie,tel1,tel1_oms,tel2,tel2_oms,email,adres,pc,plaats,land,fax_nr,memo FROM CRM_naw_kontaktpersoon WHERE id='".$postData['contact']."'";
    $NAW->SQL($query);
    $contact = $NAW->lookupRecord();
    foreach ($contact as $key=>$value)
      $nawRec[$key]=$value;
  }
  if($postData['adres'] >0)
  {
    $nawRec=array();
    $query="SELECT naam,naam1,adres,pc,plaats,land,memo,evenement FROM CRM_naw_adressen WHERE id='".$postData['adres']."'";
    $NAW->SQL($query);
    $contact = $NAW->lookupRecord();
    foreach ($contact as $key=>$value)
      $nawRec[$key]=$value;
  }
  if(is_array($contact))
  {
    $nawRec['verzendAanhef']="";//$contact['naam'];  //Moet het verzendAanhef veld ook worden gevuld?
    $nawRec['verzendAdres']=$contact['adres'];
    $nawRec['verzendPc']=$contact['pc'];
    $nawRec['verzendPlaats']=$contact['plaats'];
    $nawRec['verzendLand']=$contact['land'];
  }

  $conversieVelden = array('ondernemingsvorm'=>'Rechtsvorm');
  foreach ($conversieVelden as $key=>$value)
  {
    $conversieData[$key] = GetSelectieVelden($value);
  }

  foreach ($conversieVelden as $key=>$value)
  {
    if(key_exists($key,$nawRec))
    {
      $nawRec[$key] =  $conversieData[$key][$nawRec[$key]];
    }
  }

  if($nawRec['Vermogensbeheerder'] != '')
  {
    $query= "SELECT CrmExtraSpatie FROM Vermogensbeheerders WHERE Vermogensbeheerder = '".$nawRec['Vermogensbeheerder']."'";
    $NAW->SQL($query);
    $vermogensbeheerder = $NAW->lookupRecord();
    $extraSpatie = $vermogensbeheerder['CrmExtraSpatie'];
  }
  if($extraSpatie > 0)
  {
    foreach ($nawRec as $key=>$value)
    {
      if($value != '')
        $nawRec[$key] = "$value ";
    }
  }

  $nawRec=getAllFields($nawRec);

  $db->SQL("SELECT verplichteVelden FROM CRM_naw_RtfTemplates WHERE naam ='".$postData['file']."'");
  $db->Query();
  $tmp = $db->nextRecord();
  $verplichteVelden=unserialize($tmp['verplichteVelden']);
 
  $melding='';
  foreach($verplichteVelden as $object=>$velden)
  {
    foreach($velden as $veld)
    {
      if($nawRec[$veld]=='')
      {
        $melding.="'Verplicht veld (".addslashes($veld).") is leeg.  '+'\\n'+";
      }
    }
  }
 // $melding='abc';
  if($melding <>'')
  {
    $melding .="''";
    echo "<script >alert(".($melding).");</script>";
    echo "Niet alle verplichte velden zijn gevuld.";
    exit;
  }


  if($postData['test'])
  {
    $data = createTestFile($nawRec);
    header("Content-Type: application/rtf");
    header("Content-Length: ".strlen($data));
    header("Content-disposition: attachment; filename=test.rtf");
    echo $data;
    exit;
  }

  if(trim($nawRec['Portefeuille']) != '')
    $outFilename=trim($nawRec['Portefeuille'])."_".trim($postData['file']);
  else
    $outFilename="id".$postdata['deb_id']."_".trim($postData['file']);


  if ( $useTemplate ) {
    // Include the main TCPDF library (search for installation path).
    require_once( $__appvar['basedir'] . '/classes/TCPDF/tcpdf_include.php');

    class MyTCPDF extends TCPDF {

      var $htmlHeader;

      function setHtmlHeader($htmlHeader1, $htmlHeader2) {
        $this->htmlHeader1 = $htmlHeader1;
        $this->htmlHeader2 = $htmlHeader2;
      }

      function setHtmlFooter($htmlFooter1, $htmlFooter2) {
        $this->htmlFooter1 = $htmlFooter1;
        $this->htmlFooter2 = $htmlFooter2;
      }

      function Header() {

        if($this->page == 1){
          //print header 1 and whatever the header 2 is
        $this->writeHTMLCell(
          $w = 0, $h = 0, $x = '', $y = '',
          $this->htmlHeader1, $border = 0, $ln = 1, $fill = 0,
          $reseth = true, $align = 'top', $autopadding = true);

        }else{
          $this->writeHTMLCell(
            $w = 0, $h = 0, $x = '', $y = '',
            $this->htmlHeader2, $border = 0, $ln = 1, $fill = 0,
            $reseth = true, $align = 'top', $autopadding = true);
        }
      }

      function Footer()
      {

        if ($this->page == 1) {

          $this->writeHTML($this->htmlFooter1, false, true, false, true);
        } else {
          $this->writeHTML($this->htmlFooter2, false, true, false, true);
        }
      }

    }


    $useTemplate['headerp1'] = $postData['headerp1'];
    $useTemplate['headerp2'] = $postData['headerp2'];
    $useTemplate['tekstblok'] = $postData['tekstblok'];
    $useTemplate['footerp1'] = $postData['footerp1'];
    $useTemplate['footerp2'] = $postData['footerp2'];

// create new PDF document
    $pdf = new MyTCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, false, 'ISO-8859-1', false);

// set document information
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('');
    $pdf->SetTitle('');
    $pdf->SetSubject('');
    $pdf->SetKeywords('');


    // set default monospaced font
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
    $pdf->SetFont('helvetica', '', 11, '', true);

// set margins
//    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
    // set default header data

    $pdf->setHtmlHeader($useTemplate['headerp1'], $useTemplate['headerp2']);
    $pdf->setHtmlFooter($useTemplate['footerp1'], $useTemplate['footerp2']);

    // set margins
    $pdf->SetMargins(PDF_MARGIN_LEFT, 50, PDF_MARGIN_RIGHT);
//    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(30);
    // add a page
    $pdf->AddPage();


    $outData =  $useTemplate['tekstblok'];


    $outData = str_replace( "{nieuwePagina}", '<div style="page-break-before:always">&nbsp;</div>', $outData);

//  debug($nawRec);

    foreach ( $nawRec as $key => $val )
    {
//      $val=str_replace("  ",'',$val);
      $outData = str_replace( "<<".$key.">>", $val, $outData);
      $outData = str_replace( "\{".$key."\}", $val, $outData);
      $outData = str_replace( "{".$key."}", $val, $outData);
    }
    $outData = eregi_replace( "\<<[a-zA-Z0-9_-]+\>>", "", $outData);   // delete empty tags
//    $outData = str_replace("  "," ",$outData);
    $leegNietTonen=0;
    $leegNietTonen=strpos($outData,'leegNietTonen');
    if($leegNietTonen > 0)
    {
      $parts = explode("\par ", $outData);
      foreach ($parts as $nr => $line)
      {
        $strippedLine = trim($line, chr(160) . chr(32));
        $strippedLine = trim($strippedLine);

        if (strpos($strippedLine, 'leegNietTonen') >= 1)
        {
          $lines = explode("\line ", $strippedLine);
          foreach ($lines as $lineNr => $linePart)
          {
            if (strpos($linePart, 'leegNietTonen') == 3)
            {
              unset($lines[$lineNr]);
            }
          }
          $parts[$nr] = implode("\line ", $lines);
        }
        if (strpos($strippedLine, 'leegNietTonen') == 1 || strpos($strippedLine, 'leegNietTonen') == 2)
        {
          unset($parts[$nr]);
        }
      }
      $outData = implode("\par ", $parts);
      $outData = str_replace('{leegNietTonen}', '', $outData);
      $outData = str_replace('<<leegNietTonen>>', '', $outData);

    }









//    foreach ( $nawRec as $key => $val ) {
////      $val=str_replace("  ",'',$val);
//      $outData = str_replace( "{".$key."}", $val, $outData);
//    }
//    debug($outData);
    // output the HTML content
    $pdf->writeHTML(($outData), true, false, true, false, '');
    $pdf->lastPage();

    //Close and output PDF document
    $pdf->Output('test.pdf', 'D');



    exit();
  }


  $rtf->addItem($nawRec);
  $rtf->outputFilename = $outFilename;
  $rtf->getRTF();
  exit();
}

$subHeader     = "";
$mainHeader    = "Sjablonen overzicht";

/*
if ($handle = opendir("RTF_templates"))
{
	while (false !== ($file = readdir($handle)))
		{
		  if (substr(strtolower($file),-4) == ".rtf")
      {
        $files[] = $file;
      }
		}
		closedir($handle);
	}
*/



if ($deb_id  > 0)
{
  $NAW = new db();
  $q = "SELECT * FROM CRM_naw WHERE id = '".$postdata['deb_id']."'";
  $NAW->SQL($q);
  $nawRec = $NAW->lookupRecord();
  $subHeader = " bij <b>".$nawRec['naam'].", ".$nawRec['a_plaats']."</b>";
}

  $_SESSION['NAV']='';
  $_SESSION['submenu'] = New Submenu();
  if($postData['contact'] >0)
    $_SESSION['submenu']->addItem("Terug naar Kontaktpersonen","CRM_naw_kontaktpersoonList.php?deb_id=".$postdata['deb_id']."&useSavedUrl=1");
  if($postData['adres'] >0)
    $_SESSION['submenu']->addItem("Terug naar Adressen","CRM_naw_adressenList.php?deb_id=".$postdata['deb_id']."&useSavedUrl=1");
  if($__appvar['master']==true)
  {
    $_SESSION['submenu']->addItem("<br>","");
    $_SESSION['submenu']->addItem("Test sjabloon ","CRM_naw_rtfMergeList.php?action=print&test=true&deb_id=".$postdata['deb_id']."");
  }

$content[pageHeader] = "<br><div class='edit_actionTxt'>
  <b>$mainHeader</b> $subHeader
</div>";

$content['javascript'] .= "
function openDiv(field)
{
		$('#kop_'+field).toggle();
}
";

echo template($__appvar["templateContentHeader"],$content);
?>
<br>
<table><tr><td></td></tr>
<?
sort($files);

if($standaardbrief=='')
  $selected = " CHECKED";

$db=new DB();
$query="SELECT id,naam,categorie FROM CRM_naw_RtfTemplates ORDER BY categorie,naam";
$db->SQL($query);
$db->Query();

$lastcategorie='';
$output='';
while($data=$db->nextRecord())
{
  if($data['categorie'] != $lastcategorie)
  {

    if($lastcategorie <> '')
    {
      $output .= "</div>\n";
      $output .= "<a href=\"javascript:openDiv('".$data['id']."')\"><b>".$data['categorie']."</b> </a><br>\n";
    }
    else
      $output .= "<a href=\"javascript:openDiv('".$data['id']."')\"><b>".$data['categorie']."</b> </a><br>\n";
    $output .= "<div style='display: none' id='kop_".$data['id']."'>\n";
  }
  if($standaardbrief==$data['naam'])
    $selected = " CHECKED";

  $output .= "<input type=\"radio\" name=\"file\" value=\"".$data['naam']."\" $selected>".$data['naam']."<br>\n";
  $selected = "";
  $lastcategorie=$data['categorie'];
}
if($lastcategorie <> '')
  $output .= "</div>\n";





?>
</table>


  <form name="editForm" method="post" action="<?=$PHP_SELF?>">
    <input type="hidden" name="action" value="print">
    <input type="hidden" name="noForce" value="1">
    <input type="hidden" name="deb_id" value="<?=$postdata['deb_id']?>">
    <input type="hidden" name="contact" value="<?=$postData['contact']?>">
    <input type="hidden" name="adres" value="<?=$postData['adres']?>">
    <input type="hidden" id="sjabloonType" name="sjabloonType" value="rtf-tab">

  <div class="formHolder">
    <div class="formTabGroup ">
      <ul class="nav nav-tabs btn-group btn-group-toggle"  id="myTab" role="tablist">

        <li class="nav-item " role="presentation">
          <a class="btn btn-hover btn-default   active" id="rtf-tab" data-toggle="tab" href="#rtf" role="tab" aria-controls="rtf" aria-selected="true">Rtf</a>
        </li>
        <li class="nav-item" role="presentation">
          <a class="btn btn-hover btn-default  " id="pdf-tab" data-toggle="tab" href="#pdf" role="tab" aria-controls="pdf" aria-selected="false">Pdf</a>
        </li>
      </ul>
    </div>
    <div class="formTitle textB">Selectie</div>
    <div  id="myTabContent" class="formContent tab-content formContentForm pl-1 pt-2 PB-2" id="">

      <div class="tab-pane fade show active" id="rtf" role="tabpanel" aria-labelledby="rtf-tab">
        <?=$output?>


      </div>
      <div class="tab-pane fade" id="pdf" role="tabpanel" aria-labelledby="pdf-tab">

        <div class="formblock">
          <div class="formlinks"><label for="factuurnr">Templates</label></div>
          <div class="formrechts">
            <select name="template" id="templateSelect"><?=$customTemplate->getTemplateSelect();?></select>
          </div>
        </div>
        <br><br>
        <br><br>

        <div id="templateHolder" style="padding-left: 25px;">

          <?php
          echo $customTemplate->buildForm ('crmSjabloon', false);
          ?>

        </div>
        <?=$customTemplate->getTemplateHtmlEditorAjax();?>


      </div>


    </div>

    <div class="formTabFooter">
      <input class="btn btn-hover btn-default submitBtn" id="" type="submit" value="Document samenvoegen">
    </div>

  </div>







</form>



  <script type='text/javascript'>
    <?=$customTemplate->getTemplateSelectAjax();?>
    <?=$customTemplate->getTemplateHtmlEditorAjax();?>



    $(function () {
      $('.nav-tabs a').on('show.bs.tab', function(){
        $('#sjabloonType').val($(this).attr('id'));
        $('.submitBtn').attr('id', 'btn-'+$(this).attr('id'));
      });

      $(document).on('click', '#btn-pdf-tab', function (event) {
        event.preventDefault();
        console.log('test');
        $curAction = $('form[name=editForm]').attr('action');


        $('form[name=editForm]').attr('action', 'customTemplateOutput.php');
        $('form[name=editForm]').attr('target', '_blank');
        $('form[name=editForm]').submit();


        $('form[name=editForm]').attr('action', $curAction);
        $('form[name=editForm]').attr('target', '');

      });
    })
  </script>
<?
echo template($__appvar["templateRefreshFooterZonderMenu"],$content);


function createTestFile($rec)
{
  $tmp .= '{\rtf1\ansi\ansicpg1252\deff0{\fonttbl{\f0\fnil Calibri;}{\f1\fswiss\fcharset0 Arial;}}
{\colortbl ;\red0\green0\blue0;}
{\*\generator Msftedit 5.41.21.2500;}\viewkind4\uc1\trowd\trgaph30\trleft-30\trrh3010\trpaddl30\trpaddr30\trpaddfl3\trpaddfr3
\cellx4544\cellx9119\pard\intbl\cf1\lang1033\b\f0\fs20 Templatevariabele\cell Template variabele zonder << >>\cell\row\trowd\trgaph30\trleft-30\trrh259\trpaddl30\trpaddr30\trpaddfl3\trpaddfr3
'."\n";
  $n=0;
foreach ($rec as $key=>$value)
{
  if($n==0)
    $tmp .= '\cellx4544\cellx9119\pard\intbl\b0 <<'.$key.'>>\cell '.$key.'\cell\row\trowd\trgaph30\trleft-30\trrh259\trpaddl30\trpaddr30\trpaddfl3\trpaddfr3'."\n";
  else
    $tmp .= '\cellx4544\cellx9119\pard\intbl <<'.$key.'>>\cell '.$key.'\cell\row\trowd\trgaph30\trleft-30\trrh259\trpaddl30\trpaddr30\trpaddfl3\trpaddfr3'."\n";
  $n ++;
}
 $tmp .='\cellx4544\cellx9119\pard\intbl\qr\cell\cell\row\pard\cf0\f1\par}
'."";
 return $tmp;
}


	function getAllFields($keyValue)
	{
	  $db=new DB();
	  $data=array();
	  global $__appvar,$USR;
    $velden=array('Vermogensbeheerder','Client','Depotbank','Accountmanager','tweedeAanspreekpunt','Remisier','RapportageValuta','accountEigenaar');
    foreach($velden as $veld)
      $keyValue['*'.$veld]='';
	  if($keyValue['Vermogensbeheerder'])
	  {
	    $query="SELECT Naam as `*Vermogensbeheerder` FROM Vermogensbeheerders WHERE Vermogensbeheerder='".$keyValue['Vermogensbeheerder']."'";
	    $db->SQL($query);
	    $value=$db->lookupRecord();
	    if(is_array($value))
	      $keyValue = array_merge($keyValue,$value);
	  }
	  if($keyValue['Client'])
	  {
	    $query="SELECT Naam as `*Client` FROM Clienten WHERE Client='".$keyValue['Client']."'";
	    $db->SQL($query);
	    $value=$db->lookupRecord();
	    if(is_array($value))
	      $keyValue = array_merge($keyValue,$value);
	  }
	  if($keyValue['Depotbank'])
	  {
	    $query="SELECT Omschrijving as `*Depotbank` FROM Depotbanken WHERE Depotbank='".$keyValue['Depotbank']."'";
	    $db->SQL($query);
	    $value=$db->lookupRecord();
	    if(is_array($value))
	      $keyValue = array_merge($keyValue,$value);
	  }
	  if($keyValue['custodian'])
	  {
	    $query="SELECT Omschrijving as `*custodian` FROM Depotbanken WHERE Depotbank='".$keyValue['custodian']."'";
	    $db->SQL($query);
	    $value=$db->lookupRecord();
	    if(is_array($value))
	      $keyValue = array_merge($keyValue,$value);
	  }
 	  if($keyValue['accountEigenaar'])
	  {
	    $query="SELECT Naam as `*accountEigenaar` FROM Gebruikers WHERE Gebruiker='".$keyValue['accountEigenaar']."'";
	    $db->SQL($query);
	    $data=$db->lookupRecord();
	    $keyValue['*accountEigenaar']=$data['*accountEigenaar'];
	  }     
	  if($keyValue['Accountmanager'])
	  {
	    $query="SELECT Naam as `*Accountmanager` FROM Accountmanagers WHERE Accountmanager='".$keyValue['Accountmanager']."'";
	    $db->SQL($query);
	    $value=$db->lookupRecord();
	    if(is_array($value))
	      $keyValue = array_merge($keyValue,$value);
	  }
	  if($keyValue['tweedeAanspreekpunt'])
	  {
	    $query="SELECT Naam as `*tweedeAanspreekpunt` FROM Accountmanagers WHERE Accountmanager='".$keyValue['tweedeAanspreekpunt']."'";
	    $db->SQL($query);
	    $value=$db->lookupRecord();
	    if(is_array($value))
	      $keyValue = array_merge($keyValue,$value);
	  }
	  if($keyValue['Remisier'])
	  {
	    $query="SELECT Naam as `*Remisier` FROM Remisiers WHERE Remisier='".$keyValue['Remisier']."'";
	    $db->SQL($query);
	    $value=$db->lookupRecord();
	    if(is_array($value))
	      $keyValue = array_merge($keyValue,$value);
	  }
	  if($keyValue['RapportageValuta'])
	  {
	    $query="SELECT Omschrijving as `*RapportageValuta` FROM Valutas WHERE Valuta='".$keyValue['RapportageValuta']."'";
	    $db->SQL($query);
	    $value=$db->lookupRecord();
	    if(is_array($value))
	      $keyValue = array_merge($keyValue,$value);
	  }
	  $keyValue['huidigeDatum']=date("j")." ".$__appvar["Maanden"][date("n")]." ".date("Y");
	  $keyValue['huidigeGebruiker']=$USR;

	  return $keyValue;
	}

?>