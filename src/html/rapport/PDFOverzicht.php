<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/04/22 15:40:13 $
File Versie					: $Revision: 1.101 $

$Log:
*/
class PDFOverzicht extends FPDF
{
  var $legends;
  var $wLegend;
  var $sum;
  var $NbVal;
  var $rowHeight = 4;

	var $tablewidths;
	var $marge;
	var $widths;
	var $aligns;

  function Header()
  {
		switch ($this->rapport_type)
		{
		  case "Fondsmutaties":
		    if($this->rapport_layout == 13)
          $this->MultiCell(195,4,"Transactieverwerking ".date("j-n-Y H:i"),0,'C');
        else
          $this->MultiCell(195,4,date("j/n/Y H:i"),0,'R');

        $this->lMargin=0;
        $this->ln();
        $veldArray = array("R","Client","Portefeuille","T.","Aantal","Fonds","Fondskoers","Totaal","V.","Vk.");
        $this->SetWidths(array(12,29,21,8,20,35,26,26,10,15));
        $this->SetAligns(array('R','L','R','R','R','L','R','R','L','R'));
        $this->Row($veldArray);
        $this->ln();
		  break;
		  case "Fondsmutaties2":
		    if($this->rapport_layout == 13)
          $this->MultiCell(282,4,"Transactieverwerking ".date("j-n-Y H:i"),0,'C');
        else
          $this->MultiCell(282,4,date("j/n/Y H:i"),0,'R');

        $this->lMargin=1;
        $this->ln();
        $veldArray = array("R","Client","Portefeuille",'Datum',"T.","Aantal","Fonds","aant.na.trans.","Fondskoers","Vk.","Totaal","Rente","V.");
        $this->SetWidths(array(9,30,30,16,8,23,40,26,15,30,23,20,20));

        $this->SetAligns(array('R','L','R','R','R','R','L','R','R','R','R','R','R'));
        $this->Row($veldArray);
        $this->ln();

		  break;
			case "managementoverzicht" :
				$this->SetFont("Times","b",16);
  			$this->SetX($this->marge);
  			if($this->title)
				  $this->Cell(200,4, vertaalTekst($this->title, $this->rapport_taal) ,0,0,"L");
				else
				  $this->Cell(200,4, vertaalTekst("Overzicht portefeuille-opbouw", $this->rapport_taal) ,0,0,"L");
				$this->SetX(250);
				$this->SetFont("Times","",10);
				$this->MultiCell(40,4, vertaalTekst("Pagina",$this->rapport_taal)." ".$this->PageNo()."\n\n".vertaalTekst("Rapportagedatum",$this->rapport_taal).":\n".date("j",$this->tmdatum)." ".vertaalTekst($this->__appvar["Maanden"][date("n",$this->tmdatum)],$this->taal)." ".date("Y",$this->tmdatum),0,'R');
				$this->ln();
			break;
			case "vermogensverloop" :
				$this->SetFont("Times","b",16);
  			$this->SetX($this->marge);
  			if($this->title)
				  $this->Cell(200,4, vertaalTekst($this->title, $this->rapport_taal) ,0,0,"L");
				else
				  $this->Cell(200,4, vertaalTekst("Vermogensverloop", $this->rapport_taal) ,0,0,"L");
				$this->SetX(250);
				$this->SetFont("Times","",10);
				$this->MultiCell(40,4, vertaalTekst("Pagina",$this->rapport_taal)." ".$this->PageNo()."\n\n".vertaalTekst("Rapportagedatum",$this->rapport_taal).":\n".date("j",$this->tmdatum)." ".vertaalTekst($this->__appvar["Maanden"][date("n",$this->tmdatum)],$this->taal)." ".date("Y",$this->tmdatum),0,'R');
				$this->ln();
			break;
			case "doorkijkFondsselectie" :
				$this->SetFont("Times","b",16);
				$this->SetX($this->marge);
				$this->Cell(200,4, vertaalTekst("Doorkijk fondsselectie", $this->rapport_taal) ,0,0,"L");
				$this->SetX(250);
				$this->SetFont("Times","",10);
				$this->MultiCell(40,4, vertaalTekst("Pagina",$this->rapport_taal)." ".$this->PageNo()."\n\n".vertaalTekst("Rapportagedatum",$this->rapport_taal).":\n".date("j",$this->tmdatum)." ".vertaalTekst($this->__appvar["Maanden"][date("n",$this->tmdatum)],$this->taal)." ".date("Y",$this->tmdatum),0,'R');
				$this->ln();
				break;
			case "Rendementsverdeling" :
				$this->SetFont("Verdana","b",14);
  				$this->SetX($this->marge);
//				$this->Cell(200,4, vertaalTekst("Overzicht portefeuille-opbouw ~ Hartfort & Co Asset Management B.V. ~", $this->rapport_taal) ,0,0,"L");
				$this->Cell(200,4, vertaalTekst($this->title, $this->rapport_taal) ,0,0,"L");
				$this->SetX(210);
				$this->SetFont("Verdana","",10);
				$this->MultiCell(80,4, vertaalTekst("Pagina",$this->rapport_taal)." ".$this->PageNo()."\n\n".vertaalTekst("Rapportageperiode",$this->rapport_taal).":\n"
				.date("j",$this->vandatum)." ".vertaalTekst($this->__appvar["Maanden"][date("n",$this->vandatum)],$this->taal)." ".date("Y",$this->vandatum)." - "
				.date("j",$this->tmdatum)." ".vertaalTekst($this->__appvar["Maanden"][date("n",$this->tmdatum)],$this->taal)." ".date("Y",$this->tmdatum),0,'R');
				$this->ln();

				$this->SetFont("Verdana","b",16);
				$this->Cell(10 , 4 , $this->risicoprofiel , 0, 1, "R");
				$this->ln();
				$this->SetFont("Verdana","b",10);

        if ($this->userLayout == 12)
        {
  				$this->Cell(10 , 4 , "" , 0, 0, "R");
          $this->Cell(40 , 4 , "naam" , 0, 0, "L");
  		    $this->Cell(45 , 4 , "soort overeenkomst" , 0, 0, "L");
				  $this->Cell(20 , 4 , "rekening" , 0, 0, "L");
				  $this->Cell(20 , 4 , "" , 0, 0, "L");
				  $this->Cell(25 , 4 , "vermogen" , 0, 0, "R");
				  $this->Cell(20 , 4 , "perf.", 0, 0, "R");
			  	$this->Cell(20 , 4 , "aand.", 0, 0, "R");
				  $this->Cell(20 , 4 , "alter.", 0, 0, "R");
			    $this->Cell(20 , 4 , "obl.", 0, 0, "R");
			  	$this->Cell(20 , 4 , "liq.", 0, 1, "R");
          $this->Line($this->marge ,$this->GetY(), $this->marge + 280,$this->GetY());
				  $this->Line($this->marge + 185,$this->GetY()-4, $this->marge + 185, 190);
        }
        else
        {
  				$this->Cell(10 , 4 , "" , 0, 0, "R");
  		    $this->Cell(25 , 4 , "client" , 0, 0, "L");
  		  	$this->Cell(60 , 4 , "naam" , 0, 0, "L");
    			$this->Cell(20 , 4 , "rekening" , 0, 0, "L");
		  		$this->Cell(20 , 4 , "" , 0, 0, "L");
			  	$this->Cell(25 , 4 , "vermogen" , 0, 0, "R");
				  $this->Cell(20 , 4 , "perf.", 0, 0, "R");
			  	$this->Cell(20 , 4 , "afw. R.", 0, 0, "R");
			  	$this->Cell(20 , 4 , "aand.", 0, 0, "R");
				  $this->Cell(20 , 4 , "o.g.", 0, 0, "R");
			    $this->Cell(20 , 4 , "obl.", 0, 0, "R");
		  		$this->Cell(20 , 4 , "liq.", 0, 1, "R");
			  	$this->Line($this->marge ,$this->GetY(), $this->marge + 280,$this->GetY());
				  $this->Line($this->marge + 200,$this->GetY()-4, $this->marge + 200, 190);
        }          
          
				$this->SetFont("Verdana","",10);
			break;
			case "valutarisicooverzicht" :
				$this->SetFont("Times","b",16);
  			$this->SetX($this->marge);
				$this->Cell(200,4, vertaalTekst("Overzicht Valuta Risico", $this->rapport_taal) ,0,0,"L");
				$this->SetX(250);
				$this->SetFont("Times","",10);
				$this->MultiCell(40,4, vertaalTekst("Pagina",$this->rapport_taal)." ".$this->PageNo()."\n\n".vertaalTekst("Rapportagedatum",$this->rapport_taal).":\n".date("j",$this->tmdatum)." ".vertaalTekst($this->__appvar["Maanden"][date("n",$this->tmdatum)],$this->taal)." ".date("Y",$this->tmdatum),0,'R');
				$this->ln();
				$this->SetFont("Times","b",10);
				$this->SetWidths(array(25,70,20,25,25,25,25,25,25));
				$this->SetAligns(array("L","L","L","L","R","R","R","L","R"));
				$this->row(array("Portefeuille", "Client", "Valuta", "Totaal stukken", "Totaal Cash", "Totale waarde", "Valuta hedge", "Hedge-verschil", "Hedge-ratio"));
				$this->Line($this->marge ,$this->GetY(), $this->marge + 265,$this->GetY());
				$this->SetFont("Times","",10);
			break;
			case "geaggregeerd" :
				$this->SetFont("Times","b",16);
  			$this->SetX($this->marge);
				$this->Cell(200,4, vertaalTekst("Geaggregeerd portefeuille overzicht", $this->rapport_taal) ,0,0,"L");
				$this->SetX(250);
				$this->SetFont("Times","",10);
				$this->MultiCell(40,4, vertaalTekst("Pagina",$this->rapport_taal)." ".$this->PageNo()."\n\n".vertaalTekst("Rapportagedatum",$this->rapport_taal).":\n".date("j",$this->tmdatum)." ".vertaalTekst($this->__appvar["Maanden"][date("n",$this->tmdatum)],$this->taal)." ".date("Y",$this->tmdatum),0,'R');
				$this->ln();
				$this->SetFont("Times","b",10);
				$this->Cell(10 , 4 , "" , 0, 0, "R");
				$this->Cell(160 , 4 , "Fondsomschrijving" , 0, 0, "L");
				$this->Cell(20, 4 , "Aantal" , 0, 0, "L");
				$this->Cell(20 , 4 , "Fondskoers" , 0, 0, "L");
				$this->Cell(25 , 4 , "Fondstotaal" , 0, 0, "L");
				$this->Cell(25 , 4 , "Fondstotaal EUR" , 0, 0, "R");
				$this->Cell(20 , 4 , "Perc. %", 0, 1, "R");

				$this->Line($this->marge ,$this->GetY(), $this->marge + 280,$this->GetY());
				$this->SetFont("Times","",10);
			break;
      case "mutatievoorstelOptiesAantal" :
        if($this->selectieData['verkoopFonds'] !='')
          $verkoop=$this->selectieData['verkoopFonds']."/";
	      $this->SetFont("Times","b",16);	$this->SetX($this->marge);$this->Cell(200,4, "Mutatievoorstel fondsen",0,0,"L");$this->SetX(250);$this->SetFont("Times","",10);	$this->MultiCell(40,4, "Pagina ".$this->PageNo(),0,'R');$this->ln();
	     	$this->SetFont("Times","b",10);	$this->SetX($this->marge);$this->Cell(70,4, "Optie: ",0,0,"R");$this->Cell(50,4, $verkoop.$this->selectieData['optie'],0,0,"L");$this->Cell(50,4, "Depotbank: ",0,0,"R");$this->SetFont("Times","",10);$this->Cell(50,4, $this->selectieData['depotbank'],0,1,"L");
  	    $this->SetFont("Times","b",10);	$this->SetX($this->marge);$this->Cell(70,4, "Fondskoers: ",0,0,"R");$this->SetFont("Times","",10);$this->Cell(50,4, $this->selectieData['koersWaarde'],0,0,"L");$this->SetFont("Times","b",10);$this->Cell(50,4, "Afrondingseenheid: ",0,0,"R");$this->SetFont("Times","",10);$this->Cell(50,4, $this->selectieData['afronding'],0,1,"L");
  	    $this->SetFont("Times","b",10);$this->SetX($this->marge);$this->Cell(70,4, "Datum: ",0,0,"R");	$this->SetFont("Times","",10);$this->Cell(50,4, date("j",$this->selectieData['datumTm'])." ".$this->__appvar["Maanden"][date("n",$this->selectieData['datumTm'])]." ".date("Y",$this->selectieData['datumTm']),0,0,"L");$this->SetFont("Times","b",10);$this->Cell(50,4, "Berekeningswijze: ",0,0,"R");$this->SetFont("Times","",10);$this->Cell(50,4, $this->selectieData['berekeningswijze']." ".$this->selectieData['fondsOptie'],0,1,"L");
		    $this->SetFont("Times","b",10);$this->SetX($this->marge);$this->Cell(70,4, "Gewenst percentage op totale portefeuille: ",0,0,"R");$this->SetFont("Times","",10);
		    if($this->selectieData['transactieType']!='switch')
		     $this->Cell(50,4, $this->selectieData['percentage']." %",0,1,"L");
		   else
		     $this->ln();
       $this->ln();
		   $this->SetWidths(array(30,25,67,25,22,25,8,22,18,25));
		   $this->SetAligns(array("L","L","L","R","R","R","L","R","R","R"));
    	 $this->Row(array("Inleesdatum","Portefeuille","Naam","Aantal in fonds","Aantal in optie","Totale waarde liquide middelen","Zp","","Aan te kopen aantal","Waarde liq. middelen indien negatief","Restricties"));
      break;
			case "mutatievoorstel" :
				$this->SetFont("Times","b",16);
  			$this->SetX($this->marge);
				$this->Cell(200,4, vertaalTekst("Mutatievoorstel fondsen", $this->rapport_taal) ,0,0,"L");
				$this->SetX(250);
				$this->SetFont("Times","",10);
				$this->MultiCell(40,4, vertaalTekst("Pagina",$this->rapport_taal)." ".$this->PageNo(),0,'R');
				$this->ln();

				$this->SetFont("Times","b",10);
				$this->SetX($this->marge);
				// rij 1
				$this->Cell(70,4, "Fonds: ",0,0,"R");
				if($this->selectData['transactieType']=='switch')
			  	$this->Cell(50,4, $this->selectData['verkoopFonds'].' / '.$this->selectData['aankoopFonds'],0,0,"L");
				else
				  $this->Cell(50,4, $this->selectData['fonds'],0,0,"L");
				$this->Cell(50,4, "Depotbank: ",0,0,"R");
				$this->SetFont("Times","",10);
				$this->Cell(50,4, $this->selectData['depotbank'],0,1,"L");
				//rij 2
				$this->SetFont("Times","b",10);
				$this->Cell(70,4, "Fondskoers: ",0,0,"R");
				$this->SetFont("Times","",10);
				if($this->selectData['transactieType']=='switch')
					$this->Cell(50,4, $this->selectData['verkoopFondsDetails']['Koers']." / ".$this->selectData['koersWaarde'],0,0,"L");
				else
			  	$this->Cell(50,4, $this->selectData['koersWaarde'],0,0,"L");
				$this->SetFont("Times","b",10);
				$this->Cell(50,4, "Afrondingseenheid: ",0,0,"R");
				$this->SetFont("Times","",10);
				$this->Cell(50,4, $this->selectData['afronding'],0,1,"L");
				//rij 3
				$this->SetFont("Times","b",10);
				$this->Cell(70,4, "Datum: ",0,0,"R");
				$this->SetFont("Times","",10);
				$this->Cell(50,4, date("j",$this->selectData['datumTm'])." ".vertaalTekst($this->__appvar["Maanden"][date("n",$this->tmdatum)],$this->taal)." ".date("Y",$this->selectData['datumTm']),0,0,"L");
				$this->SetFont("Times","b",10);

					$this->Cell(50, 4, "Berkeneningswijze: ", 0, 0, "R");
					$this->SetFont("Times", "", 10);
					$this->Cell(50, 4, $this->selectData['berekeningswijze'], 0, 1, "L");

				$this->SetFont("Times","b",10);
				if($this->selectData['aankoopFonds']=='')
				{
					$this->Cell(70, 4, "Gewenst percentage op totale portefeuille: ", 0, 0, "R");
					$this->SetFont("Times", "", 10);
					$tmp = '';
					if ($this->selectData['berekeningswijzeViaNorm'] == 1)
					{
						$tmp .= " (via norm)";
					}
         	$this->Cell(50, 4, $this->selectData['percentage'] . " %" . $tmp, 0, 1, "L");
				}
				$this->ln();
        
        if($this->rapport_titel == "Mutatievoorstel opties")//optie header
        {
       	  $this->SetWidths(array(20,25,50,25,22,25,25,8,22,18,25,10));
		      $this->SetAligns(array("L","L","L","R","R","R","R","L","R","R","R"));
      		if($this->selectData['transactieType']=='switch')
		      {
		        $bestaandeWaardeVeldOmschrijving='Totaal aantal te verkopen';
		        $totaalAantalOmschrijving='Positie in nieuw fonds';
		      }
		      else
		      {
		        $bestaandeWaardeVeldOmschrijving="Totale waarde geselecteerd";
		        $totaalAantalOmschrijving='Totaal aantal geselecteerd';
		      }
        }
        else //normale header
        {
					if($this->selectData['transactieType']=='switch')
						$geslecteerd="Te verkopen aantal";
					else
					  $geslecteerd="Totale waarde geselecteerd";

  				$this->SetWidths(array(30,25,67,25,22,25,8,22,18,25));
  				$this->SetAligns(array("L","L","L","R","R","R","L","R","R","R"));
  				$this->Row(array("Client",
												 "Portefeuille",
												 "Naam",
												 "Totale waarde portefeuille",
											   $geslecteerd,
												 "Totale waarde liquide middelen",
												 "Zp",
												 "Aan te kopen waarde",
												 "Aan te kopen aantal",
												 "Waarde liq. middelen indien negatief"));
        }


				$this->Line($this->marge ,$this->GetY(), $this->marge + 280,$this->GetY());
				$this->SetFont("Times","",10);
			break;
      case "MutatievoorstelMeervoudig" :
        global $__appvar;
        if($this->selectieData['verkoopFonds'] !='')
          $verkoop=$this->selectieData['verkoopFonds']."/";
	      $this->SetFont("Times","b",16);	$this->SetX($this->marge);$this->Cell(200,4, "Mutatievoorstel fondsen",0,0,"L");$this->SetX(250);$this->SetFont("Times","",10);	$this->MultiCell(40,4, "Pagina ".$this->PageNo(),0,'R');$this->ln();
    	  $this->SetFont("Times","",10);
	      $this->SetX($this->marge);$this->Cell(70,4, "Client: ",0,0,"R");$this->Cell(50,4,$this->pdata['Client'].' / '.$this->pdata['Naam'],0,0,"L");$this->ln();
      	$this->SetX($this->marge);$this->Cell(70,4, "Portefeuille: ",0,0,"R");$this->Cell(50,4, $this->pdata['Portefeuille'],0,0,"L");$this->ln();
      	$this->SetX($this->marge);$this->Cell(70,4, "Datum: ",0,0,"R");	$this->Cell(50,4, date("j",$this->rapport_datum)." ".$__appvar["Maanden"][date("n",$this->rapport_datum)]." ".date("Y",$this->rapport_datum),0,0,"L");$this->ln();
        $this->SetX($this->marge);$this->Cell(70,4, "Restricties: ",0,0,"R");$this->Cell(50,4, $this->pdata['Memo'],0,0,"L");$this->ln();
  	    $this->ln();
				$this->SetWidths(array(60,25,25,25,25,25,25,27,25));
				$this->SetAligns(array("L","R","R","R","R","R","R","R","R","R","R","R"));
				$this->Row(array("Fonds","Gewenste Percentage","Werkelijk Percentage","Afwijking","Kopen","Verkopen","Overschrijding waarde EUR","Gewenste waarde","Koers in locale valuta"));
				$this->Line($this->marge ,$this->GetY(), $this->marge + 280,$this->GetY());
				$this->SetFont("Times","",10);
			break;
			case "fondsen" :
			  if($this->selectData['portraitVersie'] == 1)
				{
				  $this->SetFont("Times","b",16);
  			  $this->SetX($this->marge);
			  	$this->Cell(100,8, vertaalTekst("Totaaloverzicht fondsen in portefeuille", $this->rapport_taal) ,0,1,"L");
				  $this->SetFont("Times","b",10);
			  	$this->Cell(100,4, vertaalTekst("Naam Fonds: ".$this->fonds, $this->rapport_taal).' ('.$this->fondsISIN.')' ,0,1,"L");
			  	$this->SetX(160);
			  	$this->SetFont("Times","",10);
			  	$this->MultiCell(40,4, vertaalTekst("Pagina",$this->rapport_taal)." ".$this->PageNo()."\n\n".vertaalTekst("Rapportagedatum",$this->rapport_taal).":\n".date("j",$this->tmdatum)." ".vertaalTekst($this->__appvar["Maanden"][date("n",$this->tmdatum)],$this->taal)." ".date("Y",$this->tmdatum),0,'R');
			  	$this->ln();
			  	$this->SetWidths(array(10,30,80,50,20));
			  	$this->SetAligns(array("L","L","L","L","R"));
			  	$this->Row(array("nr",
												 "Portefeuille",
												 "Naam",
												 "",
												 "Aantal"));
				  $this->Line($this->marge ,$this->GetY(), $this->marge + 190,$this->GetY());
				}
			  else
			  {
				  $this->SetFont("Times","b",16);
  			  $this->SetX($this->marge);
			  	$this->Cell(200,8, vertaalTekst("Totaaloverzicht fondsen in portefeuille", $this->rapport_taal) ,0,1,"L");
				  $this->SetFont("Times","b",10);
				  $this->Cell(200,4, vertaalTekst("Naam Fonds: ".$this->fonds, $this->rapport_taal).' ('.$this->fondsISIN.')'  ,0,1,"L");
				  $this->SetX(250);
				  $this->SetFont("Times","",10);
				  $this->MultiCell(40,4, vertaalTekst("Pagina",$this->rapport_taal)." ".$this->PageNo()."\n\n".vertaalTekst("Rapportagedatum",$this->rapport_taal).":\n".date("j",$this->tmdatum)." ".vertaalTekst($this->__appvar["Maanden"][date("n",$this->tmdatum)],$this->taal)." ".date("Y",$this->tmdatum),0,'R');
				  $this->ln();
				  $this->SetWidths(array(30,25,20,25,55,40,20,25,20,20));
				  $this->SetAligns(array("L","L","L","L","L","L","R","R","R","R"));
					if($this->selectData['fondsenOpBewaarder']==1)
						$depot='Bewaarder';
					else
						$depot='Depotbank';
				  $this->Row(array("Client",
												 "Portefeuille",
											   $depot,
												 "Acc.mgr.",
												 "Naam",
												 "",
												 "Kostprijs",
												 "Aandeel ".$this->selectData[berekeningswijze],
												 "Aantal",
                         "Waarde"));

												 $this->Line($this->marge ,$this->GetY(), $this->marge + 280,$this->GetY());
			  }
				$this->SetFont("Times","",10);
			break;
			case "risicometing" :
				$this->SetFont("Times","b",16);
  			$this->SetX($this->marge);
				$this->Cell(200,8, vertaalTekst("Risicometing portefeuilles", $this->rapport_taal) ,0,1,"L");

				$this->SetX(250);
				$this->SetFont("Times","b",10);
				$this->MultiCell(40,4, vertaalTekst("Pagina",$this->rapport_taal)." ".$this->PageNo()."\n\n".vertaalTekst("Controledatum",$this->rapport_taal).":\n".date("j",$this->tmdatum)." ".vertaalTekst($this->__appvar["Maanden"][date("n",$this->tmdatum)],$this->taal)." ".date("Y",$this->tmdatum),0,'R');
				$this->ln();

				$this->Cell(30 , 4 , "Portefeuille" , 0, 0, "R");
				$this->Cell(80,  4 , "Naam" , 0, 0, "L");
				$this->Cell(30 , 4 , "Risicoklasse" , 0, 0, "R");
				$this->Cell(30 , 4 , "TotaalWaarde" , 0, 0, "R");
				$this->Cell(27 , 4 , "Rendement", 0, 0, "R");
				$this->Cell(27 , 4 , "afm-#", 0, 0, "R");
				$this->Cell(27 , 4 , "standaarddev.", 0, 0, "R");
				$this->Cell(27 , 4 , "benchmark", 0, 1, "R");



				$this->Line($this->marge ,$this->GetY(), $this->marge + 280,$this->GetY());
				$this->SetFont("Times","",10);
			break;
			case "zorgplichtcontrole" :
				$this->SetFont("Times","b",16);
  			$this->SetX($this->marge);
				$this->Cell(200,8, vertaalTekst("Zorgplichtcontrole portefeuilles", $this->rapport_taal) ,0,1,"L");

				$this->SetX(250);
				$this->SetFont("Times","b",10);
				$this->MultiCell(40,4, vertaalTekst("Pagina",$this->rapport_taal)." ".$this->PageNo()."\n\n".vertaalTekst("Controledatum",$this->rapport_taal).":\n".date("j",$this->tmdatum)." ".vertaalTekst($this->__appvar["Maanden"][date("n",$this->tmdatum)],$this->taal)." ".date("Y",$this->tmdatum),0,'R');
				$this->ln();


				$this->Cell(30 , 4 , "Client" , 0, 0, "L");
				$this->Cell(55,  4 , "Naam" , 0, 0, "L");
				$this->Cell(20 , 4 , "Depotbank" , 0, 0, "L");
				$this->Cell(30 , 4 , "Portefeuille" , 0, 0, "L");
				$this->Cell(25 , 4 , "TotaalWaarde" , 0, 0, "R");
				$this->Cell(25 , 4 , "Risicoklasse" , 0, 0, "L");
				$this->Cell(20 , 4 , "Conclusie", 0, 0, "R");
				$this->Cell(50 , 4 , "Zorgplicht %", 0, 0, "L");
        $this->Cell(35 , 4 , "Norm %", 0, 1, "L");

				$this->Line($this->marge ,$this->GetY(), $this->marge + 280,$this->GetY());
				$this->SetFont("Times","",10);
			break;
      case "zorgplichtcontroleDetail" :
				$this->SetFont("Times","b",16);
  			$this->SetX($this->marge);
				$this->Cell(200,8, vertaalTekst("Zorgplichtcontrole portefeuille ".$this->rapport_kop, $this->rapport_taal) ,0,1,"L");

				$this->SetX(250);
				$this->SetFont("Times","b",10);
				$this->MultiCell(40,4, vertaalTekst("Pagina",$this->rapport_taal)." ".$this->PageNo()."\n\n".vertaalTekst("Controledatum",$this->rapport_taal).":\n".date("j",$this->tmdatum)." ".vertaalTekst($this->__appvar["Maanden"][date("n",$this->tmdatum)],$this->taal)." ".date("Y",$this->tmdatum),0,'R');
				$this->ln();
				$this->Row(array('Fonds','Aantal','Koers',"Portefeuille\nwaarde EUR",'Percentage','ZorgWaarde'));




				$this->Line($this->marge ,$this->GetY(), $this->marge + 280,$this->GetY());
				$this->SetFont("Times","",10);
			break;
			case "restrictiecontrole" :
				$this->SetFont("Times","b",16);
				$this->SetX($this->marge);
				$this->Cell(200,8, "Restrictiecontrole portefeuille ".$this->rapport_kop,0,1,"L");
				$this->SetX(250);
				$this->SetFont("Times","b",10);
				$this->MultiCell(40,4, vertaalTekst("Pagina",$this->rapport_taal)." ".$this->PageNo()."\n\n".vertaalTekst("Controledatum",$this->rapport_taal).":\n".date("j",$this->tmdatum)." ".vertaalTekst($this->__appvar["Maanden"][date("n",$this->tmdatum)],$this->taal)." ".date("Y",$this->tmdatum),0,'R');
				$this->ln();
				$this->row(array("\n"."Fondsomschrijving",
													"Aantal",
													"Per stuk \nin valuta",
													"Portefeuille \nin valuta",
													"Portefeuille \nin EUR",
													"",
													"Per stuk \nin valuta",
													"Portefeuille \nin valuta",
													"Portefeuille \nin EUR",
													""));

				$this->Line($this->marge ,$this->GetY(), $this->marge + 280,$this->GetY());
				$this->SetFont("Times","",10);
				break;
			case "mandaatcontrole" :
				$this->SetFont("Times","b",16);
				$this->SetX($this->marge);
				$this->Cell(200,8, "Mandaat portefeuille ".$this->rapport_kop,0,1,"L");
				$this->SetX(250);
				$this->SetFont("Times","b",10);
				$this->MultiCell(40,4, vertaalTekst("Pagina",$this->rapport_taal)." ".$this->PageNo()."\n\n".vertaalTekst("Controledatum",$this->rapport_taal).":\n".date("j",$this->tmdatum)." ".vertaalTekst($this->__appvar["Maanden"][date("n",$this->tmdatum)],$this->taal)." ".date("Y",$this->tmdatum),0,'R');
				$this->ln();
				$this->row(array("\n"."Client",
										 "Portefeuille",
										 "\n"."Categorie",
										 "Waarde Tot.\nin EUR",
										 "Waarde Cat.\nin EUR",
										 "% \nCat.",
										 "Min.\nBedrag",
										 "Min.\n%",
										 "Max.\n%",
										 "% Cat.\n>max.",
										 "Boven\nmin.",
										 "Min.\nbedrag",
										 "Onder\nmax.",
										 "Max.\nBedrag"));

				$this->Line($this->marge ,$this->GetY(), $this->marge + 280,$this->GetY());
				$this->SetFont("Times","",10);
				break;
			case "cash" :
				$this->SetFont("Times","b",16);
  			$this->SetX($this->marge);
				$this->Cell(200,8, vertaalTekst("Cash positie", $this->rapport_taal) ,0,1,"L");

				$this->SetX(250);
				$this->SetFont("Times","b",10);
				$this->MultiCell(40,4, vertaalTekst("Pagina",$this->rapport_taal)." ".$this->PageNo()."\n\n".vertaalTekst("Rapportagedatum",$this->rapport_taal).":\n".date("j",$this->tmdatum)." ".vertaalTekst($this->__appvar["Maanden"][date("n",$this->tmdatum)],$this->taal)." ".date("Y",$this->tmdatum),0,'R');
				$this->ln();


				$this->Cell(25 , 4 , "Client"        , 0, 0, "L");
				$this->Cell(60,  4 , "Naam"          , 0, 0, "L");
				$this->Cell(20 , 4 , "Portefeuille"  , 0, 0, "L");
				$this->Cell(10 , 4 , "Depot"         , 0, 0, "L");
				$this->Cell(20 , 4 , "EUR (loc)"     , 0, 0, "R");
			  $this->Cell(20 , 4 , "EUR (ter)"     , 0, 0, "R");
			  $this->Cell(20 , 4 , "USD (loc)"     , 0, 0, "R");
			  $this->Cell(20 , 4 , "USD (eur)"     , 0, 0, "R");
			  $this->Cell(20 , 4 , "USD (ter)"     , 0, 0, "R");
			  $this->Cell(20 , 4 , "USD (terEUR)"     , 0, 0, "R");
//			  $this->Cell(20 , 4 , "JPY (loc)"     , 0, 0, "R");
//			  $this->Cell(20 , 4 , "JPY (eur)"     , 0, 0, "R");
//			  $this->Cell(20 , 4 , "JPY (ter)"     , 0, 0, "R");
//			  $this->Cell(20 , 4 , "JPY (terEUR)"     , 0, 0, "R");

				$this->Line($this->marge ,$this->GetY(), $this->marge + 280,$this->GetY());
				$this->SetFont("Times","",10);
			break;

			case "modelcontrole" :
				$this->SetFont("Times","b",16);
  			$this->SetX($this->marge);
				$this->Cell(200,8, vertaalTekst("Modelcontrole", $this->rapport_taal) ,0,1,"L");
				$this->SetX(250);

				$this->SetFont("Times","b",10);
				if($this->overigeBeperkingen<>'')
        {
        	$y=$this->getY();
          $this->SetX(100);
          $this->Cell(70, 4, "Overige beperkingen: ", 0, 0, "R");
          $this->SetFont("Times", "", 10);
          $this->MultiCell(100,4, $this->overigeBeperkingen,0,'L');
          $this->setY($y);
        }
				$this->SetX($this->marge);
				//rij 3
				$this->SetFont("Times","b",10);
				$this->Cell(70,4, "Controledatum: ",0,0,"R");
				$this->SetFont("Times","",10);
				$this->Cell(50,4, date("j",$this->selectData['datumTm'])." ".vertaalTekst($this->__appvar["Maanden"][date("n",$this->tmdatum)],$this->taal)." ".date("Y",$this->selectData['datumTm']),0,1,"L");

				$this->SetFont("Times","b",10);
				$this->Cell(70,4, "Modelportefeuille: ",0,0,"R");
				$this->SetFont("Times","",10);
				$this->Cell(50,4, $this->selectData['modelcontrole_portefeuille_naam'],0,1,"L");
				$this->SetFont("Times","b",10);

				if($this->selectData['modelcontrole_rapport'] == "vastbedrag")
				{
					$extraTekst =" Vast bedrag: ".$this->selectData['modelcontrole_vastbedrag'];
				}
        elseif($this->selectData["modelcontrole_filter"] != "gekoppeld")
			  {
					$extraTekst = " : niet gekoppeld depot";
				}
				else
					$extraTekst = "";

					$this->Cell(70,4, "Client: ",0,0,"R");
					$this->SetFont("Times","",10);
					$this->Cell(50,4, $this->clientOmschrijving.' / € '.$this->formatgetal($this->portefwaardeHeader).'',0,1,"L");

					$this->SetFont("Times","b",10);
					$this->Cell(70,4, "Naam: ",0,0,"R");
					$this->SetFont("Times","",10);
					$this->Cell(50,4, $this->naamOmschrijving.$extraTekst,0,1,"L");



				$this->ln();
				$this->SetWidths(array(60,20,20,20,25,25,25,30,25,25));
				$this->SetAligns(array("L","R","R","R","R","R","R","R","R","R","R","R"));
				$this->Row(array("Fonds",
												 "Model Percentage",
												 "Werkelijk Percentage",
												 "Afwijkings Percentage",
									    	 "Afwijkings in EUR",
												 "Kopen",
												 "Verkopen",
									    	 "Waarde volgens model",
					               "Koers in locale valuta",
									   		 "Geschat orderbedrag"));

				$this->Line($this->marge ,$this->GetY(), $this->marge + 280,$this->GetY());
				$this->SetFont("Times","",10);
			break;

			case "KostprijsMutatieverloop" :
			  $this->SetFont("Times","",10);
			  $this->SetWidths(array(55,25,21,21,21,25,21));
			  $this->SetAligns(array("L","L","R","R","C","R","R"));
			  $this->SetX(145);
			  $this->MultiCell(40,4, vertaalTekst("Pagina:",$this->rapport_taal)."\nPortefeuille: \n".vertaalTekst("Raportagedatum",$this->rapport_taal).":\n\nKoers:",0,'L');
	      $this->SetY(10);
	   		$this->SetX(160);
				$this->MultiCell(40,4, $this->PageNo()."\n".$this->portefeuille."\n".date("j",$this->tmdatum)." ".vertaalTekst($this->__appvar["Maanden"][date("n",$this->tmdatum)],$this->taal)." ".date("Y",$this->tmdatum)."\n\n".$this->koersWaarde,0,'R');
        $this->SetY(10);
			  $this->MultiCell(90,4,$this->rapport_koptext,0,'L');
  		  $this->ln();
			  $this->Line($this->marge ,$this->GetY(), $this->marge + 190,$this->GetY());
			  if ($this->selectData['FondsBeginpositie'])
			    $this->MultiCell(190,4, " Positiemutatieverslag vanaf beginpositie ",0,'C');
			  else
			    $this->MultiCell(190,4, " Positiemutatieverslag vanaf ".date("j",$this->selectData['datumVan'])." ".vertaalTekst($this->__appvar["Maanden"][date("n",$this->selectData['datumVan'])],$this->taal)." ".date("Y",$this->selectData['datumVan']),0,'C');
			  $this->Line($this->marge ,$this->GetY(), $this->marge + 190,$this->GetY());
			  $this->ln();
			  if ($this->selectData['FondsKosten'] == 1 )
		      $this->row(array('','datum','aantal','verk.koers','valuta','verkr.waarde €','kosten €'));
		    else
		      $this->row(array('','datum','aantal','verk.koers','valuta','verkr.waarde €'));
			  $this->Line($this->marge ,$this->GetY(), $this->marge + 190,$this->GetY());
			break;
			case "Fondsverloop" :
			  $this->SetFont("Times","",10);
			  $this->SetWidths(array(25,55,19,19,19,19,19,19,19,19,15,17,15));
			  $this->SetAligns(array("L","L","R","R","R","R","R","R","R","R","R","R","R"));
			  $this->SetX(230);
			  $this->MultiCell(40,4, vertaalTekst("Pagina:",$this->rapport_taal)."\nPortefeuille: \n".vertaalTekst("Raportagedatum",$this->rapport_taal),0,'L');
	      $this->SetY(10);
	   		$this->SetX(245);
				$this->MultiCell(40,4, $this->PageNo()."\n".$this->portefeuille."\n".date("j",$this->tmdatum)." ".vertaalTekst($this->__appvar["Maanden"][date("n",$this->tmdatum)],$this->taal)." ".date("Y",$this->tmdatum),0,'R');
        $this->SetY(10);
			  $this->MultiCell(90,4,$this->rapport_koptext,0,'L');
  		  $this->ln();
			  $this->Line($this->marge ,$this->GetY(), $this->marge + 280,$this->GetY());
			  if ($this->selectData['FondsBeginpositie'])
			    $this->MultiCell(280,4, " Fondsverloop vanaf beginpositie ",0,'C');
			  else
			    $this->MultiCell(280,4, " Fondsverloop vanaf ".date("j",$this->selectData['datumVan'])." ".vertaalTekst($this->__appvar["Maanden"][date("n",$this->selectData['datumVan'])],$this->taal)." ".date("Y",$this->selectData['datumVan']),0,'C');
			  $this->Line($this->marge ,$this->GetY(), $this->marge + 280,$this->GetY());
			  //$this->ln();//,'Vr.Val.',
        $this->setX($this->marge);
        $this->row(array('datum','','aantal','Koers','Kostprijs aankopen','Opbrengst verkopen','Kostprijs','Resultaat','Saldi aantallen','Hist. kostprijs','Kostprijs per stuk','Ultimo vorig jaar','Huidige koers'));//,'Koers','Beurs ult.','Koers Rapportage datum'
			  $this->Line($this->marge ,$this->GetY(), $this->marge + 280,$this->GetY());
			break;      
			case "remisiervergoeding":

			  //
		    $db=new DB();
			  $query="SELECT Vermogensbeheerders.Logo,Vermogensbeheerders.Layout FROM Vermogensbeheerders Join VermogensbeheerdersPerGebruiker ON VermogensbeheerdersPerGebruiker.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder WHERE VermogensbeheerdersPerGebruiker.Gebruiker ='".$_SESSION['USR']."' ";
			  $db->SQL($query);
			  $data = $db->lookupRecord();
			  if($data['Layout'] == 8)
			  {
			    global $__appvar;
			    if(is_file($__appvar['basedir']."/html/rapport/logo/".$data['Logo']))
		      {
			      $this->Image($__appvar['basedir']."/html/rapport/logo/".$data['Logo'], 18, 3.5, 52, 20.6);//43 15
		      }
        	$factor = 1.0388;
	  	    $kop=array(86*$factor,43*$factor,18.5*$factor,18.5*$factor,36.5*$factor,68*$factor);
	  	    $marge =8;

		      $this->SetFillColor(104,109,156);
		      $this->Rect($marge, 23, $kop[0], 2, 'F');
		      $this->SetFillColor(144,127,94);
		      $this->Rect($marge+$kop[0], 23, $kop[1], 2, 'F');
		      $this->SetFillColor(226,198,160);
		      $this->Rect($marge+$kop[0]+$kop[1], 23, $kop[2], 2, 'F');
		      $this->SetFillColor(166,146,139);
		      $this->Rect($marge+$kop[0]+$kop[1]+$kop[2], 23, $kop[3], 2, 'F');
		      $this->SetFillColor(131,72,90);
		      $this->Rect($marge+$kop[0]+$kop[1]+$kop[2]+$kop[3], 23, $kop[4], 2, 'F');
		      $this->SetFillColor(200,72,69);
		      $this->Rect($marge+$kop[0]+$kop[1]+$kop[2]+$kop[3]+$kop[4], 23, $kop[5], 2, 'F');

		      $this->SetX(250);
			  	$this->SetFont("Times","",10);
			  	$this->MultiCell(40,4, vertaalTekst("Pagina",$this->rapport_taal)." ".$this->PageNo()."\n".vertaalTekst("Productiedatum",$this->rapport_taal).":\n".date("j")." ".vertaalTekst($this->__appvar["Maanden"][date("n")],$this->taal)." ".date("Y"),0,'R');


		      $this->SetFont("Times","b",16);
    			$this->SetXY(100,14);
  	  		if($this->title)
			  	  $this->Cell(85,4, vertaalTekst($this->title, $this->rapport_taal) ,0,0,"L");
			  	else
				    $this->Cell(85,4, vertaalTekst("Remisiervergoeding", $this->rapport_taal) ,0,0,"L");
			  	$this->SetFont("Times","",10);

				  $this->Cell(100,4, "Vanaf ".date("j",$this->vandatum)." ".$this->__appvar["Maanden"][date("n",$this->vandatum)]." ".date("Y",$this->vandatum).
				  " t/m ".date("j",$this->tmdatum)." ".$this->__appvar["Maanden"][date("n",$this->tmdatum)]." ".date("Y",$this->tmdatum) ,0,0,"L");

		      $this->ln(16);
			  }
			  else
			  {
  				$this->SetFont("Times","b",16);
    			$this->SetX($this->marge);
  	  		if($this->title)
			  	  $this->Cell(100,4, vertaalTekst($this->title, $this->rapport_taal) ,0,0,"L");
			  	else
				    $this->Cell(100,4, vertaalTekst("Remisiervergoeding", $this->rapport_taal) ,0,0,"L");
			  	$this->SetFont("Times","",10);

				  $this->Cell(100,4, "Vanaf ".date("j",$this->vandatum)." ".$this->__appvar["Maanden"][date("n",$this->vandatum)]." ".date("Y",$this->vandatum).
				  " t/m ".date("j",$this->tmdatum)." ".$this->__appvar["Maanden"][date("n",$this->tmdatum)]." ".date("Y",$this->tmdatum) ,0,0,"L");

			  	$this->SetX(250);
			  	$this->SetFont("Times","",10);
			  	$this->MultiCell(40,4, vertaalTekst("Pagina",$this->rapport_taal)." ".$this->PageNo()."\n\n".vertaalTekst("Productiedatum",$this->rapport_taal).":\n".date("j")." ".vertaalTekst($this->__appvar["Maanden"][date("n")],$this->taal)." ".date("Y"),0,'R');

  				$this->ln(8);
	   	  }

				$this->SetFont("Times","",10);
				$this->SetWidths(array(25,25,30,25,25,25,25,25,25,25,25));
				$this->SetAligns(array("L","L","L","R","R","R","R","R","R","R","R"));
				if($data['Layout'] == 8)
			  	$this->Row(array("Remisier","Portefeuille","Client","Gemiddelde waarde €","Te betalen fee €",'Percentage',"Remisier- vergoeding €","Bodem- vermogen €",'Netto € ',"BTW %","Netto € incl"));
			  elseif ($data['Layout'] == 2)
			  {
			    $this->SetWidths(array(23,20,23,30,23,23,23,23,23,23,23,23));
			    $this->SetAligns(array("L","L","L","L","R","R","R","R","R","R","R","R"));
			    $this->Row(array("Remisier","Startdatum","Portefeuille","Client","Gemiddelde waarde","Te betalen fee",'Percentage',"Remisier- vergoeding","Bodem- vermogen",'Netto',"BTW","Netto incl"));
			  }
			  else
			  	$this->Row(array("Remisier","Portefeuille","Client","Gemiddelde waarde","Te betalen fee",'Percentage',"Remisier- vergoeding","Bodem- vermogen",'Netto',"BTW","Netto incl"));

				$this->Line($this->marge ,$this->GetY(), $this->marge + 280,$this->GetY());
				$this->SetFont("Times","",10);
				$this->ln(2);
			break;
      case "VkmOpbouw" :
        $this->SetFont("Times","b",16);
        $this->SetX($this->marge);
        $this->Cell(200,4, vertaalTekst("VKM-opbouw", $this->rapport_taal) ,0,0,"L");
        $this->SetX(250);
        $this->SetFont("Times","",10);
        $this->MultiCell(40,4, vertaalTekst("Pagina",$this->rapport_taal)." ".$this->PageNo()."\n\n".vertaalTekst("Rapportagedatum",$this->rapport_taal).":\n".date("j",$this->tmdatum)." ".vertaalTekst($this->__appvar["Maanden"][date("n",$this->tmdatum)],$this->taal)." ".date("Y",$this->tmdatum),0,'R');
        $this->ln();
        $this->SetFont("Times","b",10);
        $this->SetWidths(array(53,20,20,25,15,20,20,20,17,17,17,18,17));
        $this->SetAligns(array("L","L","L","L","R","R","R","R","R","R","R","R","R"));
        $this->row(array("Naam","Portefeuille", "Profiel", "Soort overeenkomst", "Depot\nbank", "Gemiddeld vermogen","Rendement","Kosten","Doorl. Kosten","Trans. Kosten",'Perf. Fee','Totaal Kosten','VKM'));
        $this->Line($this->marge ,$this->GetY(), 297-$this->marge,$this->GetY());
        $this->SetFont("Times","",10);
        break;
			case "orderValidatie":
				$this->ln();
				$veldArray = $this->orderHeader;
				$this->Row($veldArray);
				$this->ln();
				break;
			default :
			break;
		}
  }

	//Page footer
	function Footer()
	{
	}

	function SetWidths($w)
	{
	    //Set the array of column widths
	    $this->widths=$w;
	}

	function SetAligns($a)
	{
	    //Set the array of column alignments
	    $this->aligns=$a;
	}

	function Sector($xc, $yc, $r, $a, $b, $style='FD', $cw=true, $o=90)
	{
		if($cw){
			$d = $b;
			$b = $o - $a;
			$a = $o - $d;
		}else{
			$b += $o;
			$a += $o;
		}
		$a = ($a%360)+360;
		$b = ($b%360)+360;
		if ($a > $b)
			$b +=360;
		$b = $b/360*2*M_PI;
		$a = $a/360*2*M_PI;
		$d = $b-$a;
		if ($d == 0 )
			$d =2*M_PI;
		$k = $this->k;
		$hp = $this->h;
		if($style=='F')
			$op='f';
		elseif($style=='FD' or $style=='DF')
			$op='b';
		else
			$op='s';
		if (sin($d/2))
			$MyArc = 4/3*(1-cos($d/2))/sin($d/2)*$r;
		//first put the center
		$this->_out(sprintf('%.2f %.2f m',($xc)*$k,($hp-$yc)*$k));
		//put the first point
		$this->_out(sprintf('%.2f %.2f l',($xc+$r*cos($a))*$k,(($hp-($yc-$r*sin($a)))*$k)));
		//draw the arc
		if ($d < M_PI/2){
			$this->_Arc($xc+$r*cos($a)+$MyArc*cos(M_PI/2+$a),
									$yc-$r*sin($a)-$MyArc*sin(M_PI/2+$a),
									$xc+$r*cos($b)+$MyArc*cos($b-M_PI/2),
									$yc-$r*sin($b)-$MyArc*sin($b-M_PI/2),
									$xc+$r*cos($b),
									$yc-$r*sin($b)
			);
		}else{
			$b = $a + $d/4;
			$MyArc = 4/3*(1-cos($d/8))/sin($d/8)*$r;
			$this->_Arc($xc+$r*cos($a)+$MyArc*cos(M_PI/2+$a),
									$yc-$r*sin($a)-$MyArc*sin(M_PI/2+$a),
									$xc+$r*cos($b)+$MyArc*cos($b-M_PI/2),
									$yc-$r*sin($b)-$MyArc*sin($b-M_PI/2),
									$xc+$r*cos($b),
									$yc-$r*sin($b)
			);
			$a = $b;
			$b = $a + $d/4;
			$this->_Arc($xc+$r*cos($a)+$MyArc*cos(M_PI/2+$a),
									$yc-$r*sin($a)-$MyArc*sin(M_PI/2+$a),
									$xc+$r*cos($b)+$MyArc*cos($b-M_PI/2),
									$yc-$r*sin($b)-$MyArc*sin($b-M_PI/2),
									$xc+$r*cos($b),
									$yc-$r*sin($b)
			);
			$a = $b;
			$b = $a + $d/4;
			$this->_Arc($xc+$r*cos($a)+$MyArc*cos(M_PI/2+$a),
									$yc-$r*sin($a)-$MyArc*sin(M_PI/2+$a),
									$xc+$r*cos($b)+$MyArc*cos($b-M_PI/2),
									$yc-$r*sin($b)-$MyArc*sin($b-M_PI/2),
									$xc+$r*cos($b),
									$yc-$r*sin($b)
			);
			$a = $b;
			$b = $a + $d/4;
			$this->_Arc($xc+$r*cos($a)+$MyArc*cos(M_PI/2+$a),
									$yc-$r*sin($a)-$MyArc*sin(M_PI/2+$a),
									$xc+$r*cos($b)+$MyArc*cos($b-M_PI/2),
									$yc-$r*sin($b)-$MyArc*sin($b-M_PI/2),
									$xc+$r*cos($b),
									$yc-$r*sin($b)
			);
		}
		//terminate drawing
		$this->_out($op);
	}

	function _Arc($x1, $y1, $x2, $y2, $x3, $y3 )
	{
		$h = $this->h;
		$this->_out(sprintf('%.2f %.2f %.2f %.2f %.2f %.2f c',
												$x1*$this->k,
												($h-$y1)*$this->k,
												$x2*$this->k,
												($h-$y2)*$this->k,
												$x3*$this->k,
												($h-$y3)*$this->k));
	}
	function Rect($x, $y, $w, $h, $style = '', $border_style = null, $fill_color = null) {
		if (!(false === strpos($style, 'F')) && $fill_color) {
			list($r, $g, $b) = $fill_color;
			$this->SetFillColor($r, $g, $b);
		}
		switch ($style) {
			case 'F':
				$border_style = null;
				parent::Rect($x, $y, $w, $h, $style);
				break;
			case 'DF': case 'FD':
			if (!$border_style || isset($border_style['all'])) {
				if (isset($border_style['all'])) {
					$this->SetLineStyle($border_style['all']);
					$border_style = null;
				}
			} else
				$style = 'F';
			parent::Rect($x, $y, $w, $h, $style);
			break;
			default:
				if (!$border_style || isset($border_style['all'])) {
					if (isset($border_style['all']) && $border_style['all']) {
						$this->SetLineStyle($border_style['all']);
						$border_style = null;
					}
					parent::Rect($x, $y, $w, $h, $style);
				}
				break;
		}
		if ($border_style) {
			if (isset($border_style['L']) && $border_style['L'])
				$this->Line($x, $y, $x, $y + $h, $border_style['L']);
			if (isset($border_style['T']) && $border_style['T'])
				$this->Line($x, $y, $x + $w, $y, $border_style['T']);
			if (isset($border_style['R']) && $border_style['R'])
				$this->Line($x + $w, $y, $x + $w, $y + $h, $border_style['R']);
			if (isset($border_style['B']) && $border_style['B'])
				$this->Line($x, $y + $h, $x + $w, $y + $h, $border_style['B']);
		}
	}
	
	function Row($data)
	{
	    //Calculate the height of the row
	    $nb=0;
	    for($i=0;$i<count($data);$i++)

	   if($this->forceOneRow == true)
	     $nb = 1;
	   else
	     $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));

	    $h=$this->rowHeight*$nb;
	    //Issue a page break first if needed
	    $this->CheckPageBreak($h);
	    //Draw the cells of the row
	    for($i=0;$i<count($data);$i++)
	    {
	        $w=$this->widths[$i];
	        $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
	        //Save the current position
	        $x=$this->GetX();
	        $y=$this->GetY();
	        //Draw the border
	        //$this->Rect($x,$y,$w,$h);
	        //Print the text
	        $lines = $this->NbLines($this->widths[$i],$data[$i]);
	        // fill lines

	        //$this->MultiCell($w,4,$data[$i],$line,$a);
          $this->MultiCell($w,$this->rowHeight,$data[$i],$line,$a,$this->fillCell[$i]);
          
          if($this->CellBorders[$i])
	        {
	          $borders = array();
	          if(is_array($this->CellBorders[$i]))
	            $borders = $this->CellBorders[$i];
	          else
	            $borders[] = $this->CellBorders[$i];
            foreach ($borders as $border)
	          {
	            if(isset($this->underlinePercentage) && $this->underlinePercentage != 1)
	              $shrink = $w-$w*$this->underlinePercentage;
	            else
	              $shrink=0;
	            if($border == 'U')
	              $this->Line($x,$y+$h,$x+$w,$y+$h);
	            elseif($border == 'US')
	              $this->Line($x+$shrink,$y+$h,$x+$w,$y+$h);
	            elseif($border == 'SUB')
	            {
	              $this->Line($x+$shrink,$y,$x+$w,$y);
	              $this->setDash(1,1);
	              $this->Line($x+$shrink,$y+$h,$x+$w,$y+$h);
	              $this->setDash();
	            }
	            elseif($border == 'T')
	              $this->Line($x,$y,$x+$w,$y);
	            elseif($border == 'TS')
	              $this->Line($x+$shrink,$y,$x+$w,$y);
	            elseif($border == 'L')
	              $this->Line($x,$y,$x,$y+$h);
	            elseif($border == 'R')
	              $this->Line($x+$w,$y,$x+$w,$y+$h);
	            elseif($border == 'UU')
	            {
	              $this->Line($x+$shrink,$y+$h,$x+$w,$y+$h);
	              $this->Line($x+$shrink,$y+$h+1,$x+$w,$y+$h+1);
	            }
	          }
          }
          
	        //Put the position to the right of the cell
	        $this->SetXY($x+$w,$y);
	    }
	    //Go to the next line
	    $this->Ln($h);
	}

	function CheckPageBreak($h)
	{
	    //If the height h would cause an overflow, add a new page immediately
	    if($this->GetY()+$h>$this->PageBreakTrigger)
	        $this->AddPage($this->CurOrientation);
	}

	function NbLines($w,$txt)
	{
	    //Computes the number of lines a MultiCell of width w will take
	    $cw=&$this->CurrentFont['cw'];
	    if($w==0)
	        $w=$this->w-$this->rMargin-$this->x;
	    $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
	    $s=str_replace("\r",'',$txt);
	    $nb=strlen($s);
	    if($nb>0 and $s[$nb-1]=="\n")
	        $nb--;
	    $sep=-1;
	    $i=0;
	    $j=0;
	    $l=0;
	    $nl=1;
	    while($i<$nb)
	    {
	        $c=$s[$i];
	        if($c=="\n")
	        {
	            $i++;
	            $sep=-1;
	            $j=$i;
	            $l=0;
	            $nl++;
	            continue;
	        }
	        if($c==' ')
	            $sep=$i;
	        $l+=$cw[$c];
	        if($l>$wmax)
	        {
	            if($sep==-1)
	            {
	                if($i==$j)
	                    $i++;
	            }
	            else
	                $i=$sep+1;
	            $sep=-1;
	            $j=$i;
	            $l=0;
	            $nl++;
	        }
	        else
	            $i++;
	    }
	    return $nl;
	}

  function setDash($black=false,$white=false)
  {
      if($black and $white)
          $s=sprintf('[%.3f %.3f] 0 d',$black*$this->k,$white*$this->k);
      else
          $s='[] 0 d';
      $this->_out($s);
  }

  function OutputCSV($filename, $type)
	{
		if($fp = fopen($filename,"w+"))
		{
			$csvdata = generateCSV($this->excelData);
			fwrite($fp,$csvdata);
			fclose($fp);
		}
		else
		{
			echo "Fout: kan niet schrijven naar ".$filename;
		}

	}

 	function OutputXls($filename,$type="S",$fileFormat='xls')
	{
		global $__appvar;
    if($fileFormat=='xlsx')
    {
			writeXlsx($this->excelData,$filename);
    }
    else
    {
	  include_once('../classes/excel/Writer.php');
    if($type=='S')
      $workbook = new Spreadsheet_Excel_Writer();
    else
		  $workbook = new Spreadsheet_Excel_Writer($filename);
    $worksheet =& $workbook->addWorksheet($this->excelSheetName);
    $this->excelOpmaak['date']=array('setNumFormat'=>'DD-MM-YYYY');
    while(list($opmaakSleutel,$eigenschappen)=each($this->excelOpmaak))
    {
        $opmaak[$opmaakSleutel] =& $workbook->addFormat();
        while(list($eigenschap,$value)=each($eigenschappen))
        {
          $opmaak[$opmaakSleutel]->$eigenschap($value);
        }
    }
    
	  for($regel = 0; $regel < count($this->excelData); $regel++ )
	  {
		  for($col = 0; $col < count($this->excelData[$regel]); $col++)
		  {
		    if (is_array($this->excelData[$regel][$col]))
		    {
		      $celOpmaak = $this->excelData[$regel][$col][1]; //1=opmaak
		      $worksheet->write($regel, $col, $this->excelData[$regel][$col][0],$opmaak[$celOpmaak]);	//0=waarde
		    }
		    else
		    {
		      $waarde=$this->excelData[$regel][$col];
		      $worksheet->write($regel, $col, $waarde);
		    }
		  }
	  }
    if($type=='S')
      $workbook->send($filename);
	  $workbook->close();
    }
	}
  
  	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}



 	function fillXlsSheet($worksheet)
	{
    while(list($opmaakSleutel,$eigenschappen)=each($this->excelOpmaak))
    {
        $opmaak[$opmaakSleutel] =& $workbook->addFormat();
        while(list($eigenschap,$value)=each($eigenschappen))
        {
          $opmaak[$opmaakSleutel]->$eigenschap($value);
        }
    }

	   for($regel = 0; $regel < count($this->excelData); $regel++ )
	   {
		   for($col = 0; $col < count($this->excelData[$regel]); $col++)
		   {
		     if (is_array($this->excelData[$regel][$col]))
		     {
		       //$opmaak[$opmaakSleutel]
		       $celOpmaak = $this->excelData[$regel][$col][1]; //1=opmaak
		       $worksheet->write($regel, $col, $this->excelData[$regel][$col][0],$opmaak[$celOpmaak]);	//0=waarde
		     }
		     else
		     {
		       $worksheet->write($regel, $col, $this->excelData[$regel][$col]);
		     }
		   }
	   }
	}


}
?>
