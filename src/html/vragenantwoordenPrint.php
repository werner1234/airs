<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2018/08/18 12:40:14 $
 		File Versie					: $Revision: 1.11 $

 		$Log: vragenantwoordenPrint.php,v $
 		Revision 1.11  2018/08/18 12:40:14  rvv
 		php 5.6 & consolidatie
 		
*/

include_once("wwwvars.php");
include_once($__appvar["basedir"]."/classes/AE_cls_fpdf.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/classes/AE_cls_html2fpdfRapport.php");
include_once("rapport/PDFRapport.php");

class vragenPDF extends html2fpdfRapport
{
        function vragenPDF($pdf)
        {
          $this->pdf = &$pdf;
          $this->html2fpdfRapport('P','mm','A4');
          
          $this->pdf->rapport_type = "BRIEF";
          $this->pdf->excelData=array();
          
          $db=new DB();
          $query="SELECT Portefeuille,vermogensbeheerder FROM Portefeuilles WHERE eindDatum > now() AND vermogensbeheerder <> ''";
          $db->SQL($query);
          $port=$db->lookupRecord();
          $this->vermogensbeheerder=$port['vermogensbeheerder'];
          
          loadLayoutSettings($this->pdf, $port['Portefeuille']);
          $this->logo=$this->pdf->rapport_logo;
          $this->pdf->rapport_logo = '';
          $this->pdf->rapport_factuurHeader = '';
          $this->pdf->portefeuilledata['Logo'] = '';
          $this->pdf->rapport_voettext = '';
  
          if (file_exists('rapport/include/PDFRapport_headers_L'.$this->pdf->rapport_layout.".php"))
          {
            include_once('rapport/include/PDFRapport_headers_L'.$this->pdf->rapport_layout.".php");
          }
          elseif (file_exists('rapport/include/layout_'.$this->pdf->rapport_layout.'/PDFRapport_headers_L'.$this->pdf->rapport_layout.".php"))
          {
            include_once('rapport/include/layout_'.$this->pdf->rapport_layout.'/PDFRapport_headers_L'.$this->pdf->rapport_layout.".php");
          }
        }

     
        function addVragenlijst($antwoordId,$score)
        {
          global $USR;

          
          $db=new DB();
          $query="SELECT id,vragenlijstId,relatieId,date(add_date) as add_date FROM VragenIngevuld WHERE id = '$antwoordId' limit 1";
          $db->SQL($query);
          $tmp=$db->lookupRecord();
          $vragenlijstId=$tmp['vragenlijstId'];
          $relatieId=$tmp['relatieId'];
          $add_date=$tmp['add_date'];

          $query="SELECT CRM_naw.naam,CRM_naw.portefeuille,Portefeuilles.Vermogensbeheerder FROM CRM_naw LEFT JOIN Portefeuilles ON CRM_naw.portefeuille=Portefeuilles.portefeuille WHERE CRM_naw.id = '$relatieId' limit 1";
          $db->SQL($query);
          $crmRecord=$db->lookupRecord();
          $standaardHeader=true;
  
          if($this->vermogensbeheerder=='LBC' || function_exists('HeaderVRAGEN_L'.$this->pdf->portefeuilledata['Layout']) )
          {
            loadLayoutSettings($this->pdf, $crmRecord['portefeuille'],'',$relatieId);
            $this->pdf->rapport_type = "VRAGEN";
            $standaardHeader=false;
          }
  
          $this->pdf->AddPage('P');
          
          $query="SELECT * FROM VragenVragenlijsten WHERE id = '$vragenlijstId' ";
          $db->SQL($query);
          $vragenlijst=$db->lookupRecord();
          $query="SELECT VragenIngevuld.relatieId,VragenIngevuld.vragenlijstId,VragenIngevuld.vraagId,VragenIngevuld.antwoordId,VragenIngevuld.antwoordOpen,VragenVragen.omschrijving,
VragenVragen.volgorde,VragenVragen.vraagNummer,VragenVragen.vraag,VragenVragen.factor,VragenVragen.CRM_trekveld
FROM VragenIngevuld INNER JOIN VragenVragen ON VragenIngevuld.vraagId = VragenVragen.id
WHERE VragenIngevuld.vragenlijstId='$vragenlijstId' AND date(VragenIngevuld.add_date)='$add_date' AND VragenIngevuld.relatieId='$relatieId' ORDER BY VragenVragen.volgorde";
          $db->SQL($query);
          $db->Query();
          while($data=$db->nextRecord())
          {
            $vragen[]=$data;
          }
          foreach($vragen as $id=>$vraagData)
          {
            $vragen[$id]['antwoorden']=array();
            if($vraagData['antwoordOpen']<>'')
            {
              $antwoord=array();
              $antwoord['gekozen']=1;
              $antwoord['geenPunten']=1;
              $antwoord['antwoord']=$vraagData['antwoordOpen'];
              $vragen[$id]['antwoorden'][]=$antwoord;
            }
            elseif($vraagData['CRM_trekveld'])
            {
              $query="SELECT * FROM CRM_selectievelden WHERE module='".$vraagData['CRM_trekveld']."' order by id";
              $db->SQL($query);
              $db->Query();
              while($data=$db->nextRecord())
              { 

                $tmp=unserialize($data['extra']);
                $antwoord['punten']=$tmp['punten'];
                $antwoord['antwoord']=$data['omschrijving'];

                if($vraagData['antwoordId']==$data['id'])
                  $antwoord['gekozen']=1;
                else
                  $antwoord['gekozen']=0;
                $vragen[$id]['antwoorden'][]=$antwoord;  
              }
            }
            else
            {
              $query="SELECT * FROM VragenAntwoorden WHERE vraagId='".$vraagData['vraagId']."' order by id";
              $db->SQL($query);
              $db->Query();
              while($data=$db->nextRecord())
              {
                $antwoord=array();
                $antwoord['punten']=$data['punten'];
                $antwoord['antwoord']=$data['omschrijving'];
                if($vraagData['antwoordId']==$data['id'])
                  $antwoord['gekozen']=1;
                else
                  $antwoord['gekozen']=0;
                $vragen[$id]['antwoorden'][]=$antwoord;  
              }
              
            }
            
          }
        //  listarray($vragenlijst);
         // listarray($vragen);
          $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
          $this->pdf->setWidths(array(150,45));
          $this->pdf->setAligns(array('L','R'));
          if($crmRecord['portefeuille'] <> '')
            $portefeuilleKop=' ('.$crmRecord['portefeuille'].')';
          else
            $portefeuilleKop='';
          if($standaardHeader==true)
          {
            $this->pdf->row(array($vragenlijst['omschrijving'] . ' ' . $crmRecord['naam'] . $portefeuilleKop, 'Vastgelegd op: ' . date('d-m-Y', db2jul($add_date))));
            $this->pdf->Ln();
          }
          if($vragenlijst['titel']<>'')
          {
            $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize+2);
            $this->pdf->setWidths(array($this->pdf->w - $this->pdf->marge * 2));
            $this->pdf->setAligns(array('C'));
            $this->pdf->row(array($vragenlijst['titel']));
          }
          $punten=0;
          $this->pdf->setWidths(array(15,160,15));
          $this->pdf->setAligns(array('C','L','R'));
          $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
          if($score==1)
            $header='punten';
          else 
            $header='';
          $this->pdf->row(array('#',"Vraag/Antwoorden",$header));
          $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
          
          foreach($vragen as $vraagData)
          {
            if($this->pdf->GetY() > 250)
              $this->pdf->AddPage();
            $this->pdf->setWidths(array(15,160,15));
            $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
            $this->pdf->row(array($vraagData['vraagNummer'],$vraagData['vraag']));
            $this->pdf->setWidths(array(20,160,10));
            $this->pdf->Ln(1);
            foreach($vraagData['antwoorden'] as $antwoord)
            {
              if($antwoord['gekozen']==1)
              {
                $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
                $punten+=$antwoord['punten']*$vraagData['factor'];
                $circleStyle='DF';
              }  
              else
              {
                $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
                $circleStyle='D';
              }
              if($this->pdf->GetY() > 270)
                $this->pdf->AddPage();
              $this->pdf->Circle($this->pdf->GetX()+7,$this->pdf->GetY()+2,1,0,360,$circleStyle,null,array(0,0,0));
              
              if($score==1 && $antwoord['geenPunten']<>1)
                $txt=$antwoord['punten'];
              else 
                $txt='';
                
              $this->pdf->row(array('',''.$antwoord['antwoord'],$txt));
            }
            $this->pdf->Ln(2);
            
          }
          $this->pdf->Ln();
          $profiel='';
/*      
          $this->pdf->row(array('','Punten totaal',round($punten)));
            if($this->pdf->GetY() > 250)
              $this->pdf->AddPage();       
*/
        $query="SELECT * FROM CRM_selectievelden WHERE module='risicoprofiel' order by id";
        $db->SQL($query);
        $db->Query();
//        $this->pdf->setWidths(array(10,60,15,15));
//        $this->pdf->setAligns(array('L','L','R','R'));
//        $this->pdf->Ln();
//        $this->pdf->row(array('','Riscoprofiel','min.','max.'));
        //$this->pdf->SetFont('','',10);
        while($data=$db->nextRecord())
        { 
          $tmp=unserialize($data['extra']);
          
          if($punten >= $tmp['min'] && $punten <= $tmp['max'])
          {
//            $this->pdf->SetFont('','b',10); 
            if($profiel=='')
              $profiel=$data['omschrijving'];
            else
              $profiel.=' of '.$data['omschrijving'];  
          }  
//          else
//            $this->pdf->SetFont('','',10);   
          
          //$this->pdf->row(array('',$data['omschrijving'],$tmp['min'],$tmp['max']));
                
        }
        $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
        if($vragenlijst['tekstRisicoprofiel']==1)
        {
          if ($score == 1)
          {
            $this->pdf->row(array('', 'Op basis van de door u gegeven antwoorden scoort uw risicobereidheid een totaal van ' . $punten . ' punten wat het best aansluit bij een ' . $profiel . ' risicoprofiel.', ''));
          }
          else
          {
            $this->pdf->row(array('', 'Op basis van de door u gegeven antwoorden sluit uw risiciobereidheid het best aan bij een ' . $profiel . ' risicoprofiel.', ''));
          }
        }
        $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
  
        if($vragenlijst['extraInfo']<>'')
        {
          $this->pdf->ln();
          $this->pdf->row(array('', $vragenlijst['extraInfo'], ''));
        }
       }


}

$pdf = new PDFRapport('P','mm');
$pdf->SetAutoPageBreak(true,15);
$pdf->pagebreak = 270;
$pdf->__appvar = $__appvar;

$vragenPDF = new vragenPDF($pdf);

if($_GET['id'])
{
  $vragenPDF->addVragenlijst($_GET['id'],$_GET['score']);
}
if($_GET['outputFilename'])
 $pdf->Output($_GET['outputFilename'],'F');
else
 $pdf->Output();

?>