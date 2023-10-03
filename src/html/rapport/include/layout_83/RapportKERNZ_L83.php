<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/03/31 12:17:58 $
File Versie					: $Revision: 1.1 $

$Log: RapportKERNZ_L83.php,v $
Revision 1.1  2019/03/31 12:17:58  rvv
*** empty log message ***

Revision 1.1  2019/03/02 18:23:01  rvv
*** empty log message ***

Revision 1.1  2018/12/22 16:15:52  rvv
*** empty log message ***

Revision 1.1  2018/10/23 06:20:02  rvv
*** empty log message ***

Revision 1.6  2018/10/20 18:05:20  rvv
*** empty log message ***

Revision 1.5  2018/10/13 17:18:13  rvv
*** empty log message ***

Revision 1.4  2018/10/10 15:50:56  rvv
*** empty log message ***

Revision 1.3  2018/10/07 10:19:56  rvv
*** empty log message ***

Revision 1.2  2018/10/06 17:20:57  rvv
*** empty log message ***

Revision 1.1  2018/05/20 10:39:24  rvv
*** empty log message ***



*/


include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");


class RapportKERNZ_L83
{
	function RapportKERNZ_L83($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
    $this->pdf = &$pdf;
    $this->pdf->rapport_type = "KERNZ";
    $this->pdf->rapport_datum = db2jul($rapportageDatum);
    $this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
    $this->pdf->rapport_titel = "Kwartaal verslag";
    $this->portefeuille = $portefeuille;
    $this->rapportageDatumVanaf = $rapportageDatumVanaf;
    $this->rapportageDatum = $rapportageDatum;
  }

	function writeRapport()
  {
    $gebruikteCrmVelden=array('KwartaalVerslag','Naam');
    $query = "DESC CRM_naw";
    $DB=new DB();
    $DB->SQL($query);
    $DB->Query();
    $crmVelden=array();
    while($data=$DB->nextRecord())
    {
      $crmVelden[]=strtolower($data['Field']);
    }
  
    $nawSelect='';

    foreach($gebruikteCrmVelden as $veld)
    {
      if(in_array(strtolower($veld),$crmVelden))
      {
        $nawSelect.=",CRM_naw.$veld ";
      }
    }

    $query="
      SELECT CRM_naw.id
        $nawSelect
      FROM
        CRM_naw
      WHERE
        CRM_naw.portefeuille='".$this->portefeuille."'";
    
    $DB->SQL($query);
    $crmData=$DB->lookupRecord();
    /*
    $crmData['KwartaalVerslag']='Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras tincidunt sem quis quam dictum sagittis. Maecenas sit amet aliquet nibh. Nullam ac tellus mi. Cras ut turpis sed leo sollicitudin lacinia. Fusce maximus, mi quis congue ullamcorper, nulla mi malesuada felis, eu mollis magna massa quis diam. Nullam fringilla nisi arcu, in ultricies justo pharetra quis. Quisque maximus massa arcu, quis elementum enim auctor eget. Quisque ut tempus risus. Sed tortor ex, pellentesque in maximus in, condimentum in ligula. Pellentesque mattis felis ut mollis porttitor.

Mauris vulputate magna libero. Duis feugiat nunc venenatis velit maximus, eget viverra mauris dignissim. Interdum et malesuada fames ac ante ipsum primis in faucibus. Aliquam ultricies, quam quis viverra aliquam, nulla magna dapibus ex, vel fringilla enim urna placerat ante. Ut eget risus ut turpis ultrices tincidunt. Proin in congue urna. Cras auctor felis sapien, eget varius nisi placerat ut. Etiam id tortor at lorem egestas sodales rhoncus a ipsum. Aliquam fermentum neque ac ipsum sodales, vitae posuere quam porta. Vivamus venenatis mattis ipsum. Donec auctor semper sapien, a lacinia odio imperdiet nec. Sed at ligula feugiat, eleifend eros in, tristique est.

Interdum et malesuada fames ac ante ipsum primis in faucibus. Sed ac dolor mi. Mauris eget enim varius lectus ultricies scelerisque. Mauris vel diam dui. Sed ullamcorper metus et malesuada condimentum. Curabitur iaculis iaculis turpis in vulputate. Integer eleifend, justo id hendrerit pharetra, arcu justo vestibulum purus, at volutpat arcu purus vel enim. Maecenas quam magna, venenatis et molestie fringilla, malesuada vel lorem. Integer congue, tortor eget pharetra tincidunt, lorem ex sagittis nisi, fermentum maximus purus arcu non sem. Pellentesque aliquet velit a lectus eleifend, in aliquam massa tincidunt. Curabitur gravida consequat sollicitudin. Pellentesque finibus nibh in lacus sollicitudin sagittis. Curabitur accumsan turpis vel finibus finibus. Nullam vel dui nisl. Integer consectetur, quam et dictum placerat, erat orci placerat magna, non gravida augue tellus vel leo. Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere cubilia Curae;

Nullam ut urna ligula. Fusce egestas semper finibus. Sed non ex nisl. Morbi eu ipsum auctor, congue augue a, eleifend ligula. Quisque vel lobortis purus. Nunc commodo sem quis fermentum efficitur. Maecenas sollicitudin libero quam, at malesuada leo eleifend ac. Fusce ac cursus lorem. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Suspendisse potenti. Nunc sed lacus eu neque iaculis bibendum. Sed ac lectus lobortis, convallis augue ullamcorper, dignissim elit. Integer lobortis eros nunc, at accumsan sapien interdum id. Maecenas sit amet ante non orci fringilla aliquam.

Morbi sed risus porta, pharetra nibh vitae, vehicula diam. Curabitur ultrices ultrices porta. Pellentesque in diam tincidunt, bibendum nisi id, ornare neque. Aliquam eget libero accumsan turpis lobortis dictum eget in nunc. Nullam viverra risus sed nisi varius, sed mollis velit aliquet. Aliquam lobortis, erat a consequat vulputate, tortor velit elementum massa, vel placerat urna est a erat. Mauris euismod dapibus neque nec venenatis.';
    */
    if($crmData['KwartaalVerslag']<>'')
    {
      $this->pdf->addPage();
      $this->pdf->templateVars[$this->pdf->rapport_type.'Paginas']=$this->pdf->page;
      $this->pdf->templateVarsOmschrijving[$this->pdf->rapport_type.'Paginas']=$this->pdf->rapport_titel;
  
      $blokken=explode("\n",$crmData['KwartaalVerslag']);
      $width=(297-$this->pdf->marge*3)/2;
      $this->pdf->SetWidths(array($width,$this->pdf->marge,$width));
      $this->pdf->SetAligns(array('L'));
      $col=0;
      $beginY=$this->pdf->getY();
      foreach($blokken as $blok)
      {
        if(trim($blok)=='')
          continue;
        $stringWidth=$this->pdf->GetStringWidth($blok);
        $regels=ceil($stringWidth/$width);
        $newY=$this->pdf->getY() + ($regels+1)*$this->pdf->rowHeight;
        //echo "$newY=". $this->pdf->getY()." + $regels*".$this->pdf->rowHeight." $col <br>\n";
        if($newY > 180)
        {
          $col=$col+1;
          if($col==3)
          {
            $col=0;
            $this->pdf->addPage();
          }
          $this->pdf->setY($beginY);
        }
        $row=array();
        for($i=0;$i<$col;$i++)
        {
          $row[] = '';
          $row[] = '';
        }
        $row[]=$blok."\n\n";
        //listarray($row);
        $this->pdf->row($row);
      }
 
    }
  //  exit;
  }
}
?>
