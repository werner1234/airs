<?php

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once("rapport/include/layout_102/ATTberekening_L102.php");

class RapportAFM_L102
{
	function RapportAFM_L102($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
    $this->pdf->rapport_titel = "Compliance rapportage";
    $this->pdf->rapport_type = "AFM";
    $this->portefeuille=$portefeuille;
    $this->rapportageDatum=$rapportageDatum;
	}
  
  
  function formatGetal($waarde, $dec)
  {
    return number_format($waarde,$dec,",",".");
  }
  
  function printBenchmarkvergelijking()
  {
    global $__appvar;
  
    $this->checkImg=base64_decode('iVBORw0KGgoAAAANSUhEUgAAADAAAAAwCAMAAABg3Am1AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyZpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNi1jMDY3IDc5LjE1Nzc0NywgMjAxNS8wMy8zMC0yMzo0MDo0MiAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENDIDIwMTUgKFdpbmRvd3MpIiB4bXBNTTpJbnN0YW5jZUlEPSJ4bXAuaWlkOkY3QkI0NkM0OTU5MTExRThCNUQ4QTdFOTc2OENEOEExIiB4bXBNTTpEb2N1bWVudElEPSJ4bXAuZGlkOkY3QkI0NkM1OTU5MTExRThCNUQ4QTdFOTc2OENEOEExIj4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6RjdCQjQ2QzI5NTkxMTFFOEI1RDhBN0U5NzY4Q0Q4QTEiIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6RjdCQjQ2QzM5NTkxMTFFOEI1RDhBN0U5NzY4Q0Q4QTEiLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz5IXOqgAAADAFBMVEUzsRn29vZFtjIAjQIAmACl1aJ7e3tNiE3d3d35+fkAbSz+/v5Zd1lyy1MAbDP7+/tsxVwXkBfC4cHH1cc6sij09PS7u7sAlAANkgdZwEOd05Ty8vIUogqIiIgAcytKqUqEk4TV1dVhyTG65KZXtFZGd0aR2m08ripvum/w8PAAkQDg4OCoqKgwmzBra2suph9jxEvt8u1dv01zy1uo2ZlTp1OD02UkqhEqmirt7e1KvSRFuiEXdBd5tnkKnQSb3XoqrRQAcSQAeiNUwinr7OuKxYfm5uZeaF5SwSgfgB+9071CuSA6oTqN2GlkaWR6pHoxdjF60k7k5OSL1mxyz0dcxi1ZxSwAawUVbhWazJo+tx4Abxt80F06tRwAghVcxDeam5ojqRECfQICcwMEhgQEmgFMviXq7urT3dNtyVTa2tro6OjAwMCwy7AAghourxaWlpZkyzJUvTxpmmlpyUUMngbq6uri4uKA01wppRsAchVkyzSS2XKUzI1KujEkoxmU23Edpw54zlwAfRvN5crD6bANlwcAbQ1vzj+JoYkapQ0AeRMAhg7Ly8tgw0QAdAyJ1mYAigl4zWAXmg2Y3HYHnAMCihMGkhDx9PB+0GMAdhdrx1ZmxVIBkAoSgBLE6rHn6uaW3HPd4d0LkwrG7LSG1GwCZwJ2hXaV3HIAkwfv8+8AbzAPoAdowVppzThWwyoaog1IvCPI7LUAfAhAuB83tBtexy4IkQYKfgoAdycnqxIOjA78/PxmzDP9/f1QwCdHvCNYxCtPwCc/uB8XpAsorBMwsBcgqA8vsBcRoQhgyC8fqBAInAO4uLid2IUIlgOlxKX4+/genRc+gz7b7ts1rxpEszDj5uMFjwNkcGQxqyEPhg8NkAdopmgfoxPe8N53zlWqsqomiiYnqhxozDZHvCQ3sh1Ctynx+PHA5q+AxICXtpfg4+CGzXmNsI2ZrJnT49PT69Pa59bj7t4mqxVxm3Fjul+2u7YAgCEDihkhcCE/tDMeoA/p6elRvEH///9r2XbkAAABAHRSTlP///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////8AU/cHJQAABaxJREFUeNqUlnlUE1cUhzMEAQMhiEQKSNBIgmHYFCHEKIaAwRbBFAQiCkIWXEAW27iACwiCoIIWRHA5Wqt4MCBalJh0CIIsIlpb2lJEamMXba3dW2u1St+bCQHEpf39mfN99907mffekAb/Z0hjfvl03HSXhZ6eC12mL/kF0b1K+PJeQF+Zt1uvSa+bd9mAPGDuyvEvE351VW9dq9+wYdk2PXWVj4+DycYBud9E5EXCd9P7+vXh4Xv2+PtXLfOl+pQ4ODzd0j8g/+LY+OcK+QFnqGw8Ufq3l7deDIuLP0+6Y3fnlvyrNRzdWGGcujecnZPzbvmexY0twa3JhJAu0P7RPXDhpxDsWWGJ2oEN6PLwxZlXoluCm4FwiRCScosLpItEtNGCo9oH8PPK32xqymTiwlk+FC7jQluBtEiEjRRs1SWg/Dz2jqbnC13qCz+v0w0Lp3p62YAP/6YJCmNbapuslL/+F2dYcO1h54D6TTiPC8NDc7UCQWeXcr/UfCoyJNiqqezyeTmnCZ7ZGFO/oHU2nxC0M23zHbek1xZUyIoidQbBpT8cLLBjiDeMAARSh7buKI/H+60k/erfDZ+k0gkhv48atal8QyYe5pVouIBhhPa6FRyY/OauigrZFFSHCw+2+kdt2sTEc6UR8GACoiPIO8Fw9Nz9aQ3mqQgUPp6wFgiLoxthomNa6kFDgL8Ud15QN8OJjsdJrz38MGFOYgoUbPuo/lFRMS0xMC319QsAf5F/DvA2M+h0BM+0aGXNwzTZydU0INwr2+Z/Qh8cXA8THAz6BzxoKN3mFGLI90yuouZhXoMV6Ik06Oe9rerE8tbmBRc/u9Pa3Jps5GcgyHg8gN9nWiMUlrJu84AQ4OZbVZWcfPYfd88eJT95NnigOF+J4zQatp3ZflVhelgo3CnOEAFhQq/vO1R+WIHnG/ePudZe4vPDzg3xNAzTWR5htgfuA4JGs3OOMwqED0xW+ZaElS1ciaLr77u2xZ2Li4u/bFMJqgPa0vJIpkAJFjCt0GgOSMwsoNB70Hdt+nsTLdD1kaLPH3TGx5/vgDyN4LMFSriAfRoDCqthS26HDm4542eBQiHlaF17Rwfsh4ZZEnytEvCm9kKjEODtc+juGZdjKBopEj0JCRn3oyPsn+AzAQ8bsndnMBhZYlxw6Znpc1egmI8vsC6Ex3Giw3lhP+/D+oH4AhUqBqOUhQ89d+DpTLt0bu18QoC8oaERvLtQpWLEWmWsB8Ia+WMHO4GA27YZdsQzCLpRvD0Z8EGxNzzgH7fi5sandlptOzdpc8qToY7AAsdH8O55KhUlSxIBdgRpkOY3YGKXxAUROBpH0I3mySpriiqWZTYVvnyDa6T9j3O7uzs7uSRHHiHQdMez25VG3l1obU0Jqo5IFOEbiOP5ml13cXFubi43bhYQ4AhHsrXKwKsKnLcn7wI8pVDsBfcoEHQfSW993YYn6dtZuLA9m0uUx+vvVQFhaXWos2GLDlaay7dOxtOV1Pzh7/TKadlcgBt4MhnylFiW1yQnw6mBTbypbqslkjubueN0S6cRJ+pbUwoloRkoNnSQIT9IryuJBAZ2FU8mcJwn78J50JDZbmT4bOUtkl7fR0ShIGiiPDnPmuCtvK7xRhzGWORbUneFkcWfDcQTGBDHeQ+RbuRxT0P/lJHtjbARpwCcUlhtFeGB0kZfKDS0aI507+GaIXhvAnjdVICmLI2VhHoZ+eEriya6bS5rSEhLSEhLyxNqwOvPoAQBvLCaFWp2zXgBjbgUsZDdU8Sy0p15eUKhRqNhBIEAXBzqlbGbgz33nkbQSVPMJTJZaemBA1lZWYWx1RJWhJfzJBR50cWOOVmkJp60YokfSSSPxCyrGxFmiakoHXvJp4MOEU1NvZ3hbAbinOGRujrl2c+NsR8nGMIToRYgqIiHYP/ha+YV+VeAAQBsubMyVT2e/wAAAABJRU5ErkJggg==');
    $this->deleteImg=base64_decode('iVBORw0KGgoAAAANSUhEUgAAADAAAAAwCAMAAABg3Am1AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyZpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNi1jMDY3IDc5LjE1Nzc0NywgMjAxNS8wMy8zMC0yMzo0MDo0MiAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENDIDIwMTUgKFdpbmRvd3MpIiB4bXBNTTpJbnN0YW5jZUlEPSJ4bXAuaWlkOjA0RjcxMTA2OTU5MjExRThBRDAyRkYzMjNGRThERUQxIiB4bXBNTTpEb2N1bWVudElEPSJ4bXAuZGlkOjA0RjcxMTA3OTU5MjExRThBRDAyRkYzMjNGRThERUQxIj4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6MDRGNzExMDQ5NTkyMTFFOEFEMDJGRjMyM0ZFOERFRDEiIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6MDRGNzExMDU5NTkyMTFFOEFEMDJGRjMyM0ZFOERFRDEiLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz6gm9BkAAADAFBMVEX1+/3l6eqcnJy8EQDt7e3o2Nb/qYplX1+yMShoZmbnNhHd3d31fWXcxcL/fFL4TiTUHQDaIQK0DQCaJyb5//+td3P+4dr7LQCsVE2qqqrRvbz+jWv9a0B6enr4+Pjs8vTm5ub/ekz6w7PBFAD7+/vKu7ruHADjo5X/YTHzWTP/Xi7/gVn/gFLZ5Ob/aTn9MQD38e+NjY3/lHH/pIXacWH/NAH6+vr/ZjaoCAH/OAWXZ2SFhYXz8/P/bj7/kG3qKAD1LgD/nn3n6Ojk8vX/WSnWjoXuRib/uaT/ckb5MAD5Yz3yLACCW1vr7e3tKgDoxLrnl4f9flybDw/BNif/ViXj4+P/Th3U5Or19fXqjnb/UiHgIwD/PQrtMRD/imbNGgD/QA3oJgD/SRbg4OD/aTzr6+vkJQDrQCP4YTr/w6zbPijJycn/PgyXPTxua2va2trNzc3/NwT/d0nwKwD/RBL29vbjUz3i5OT96eXrdl//UR7iHwD/cUL/QhD59/b6VSj/dkb/mXj/hWDe1NLq5uX/SxjinJL4n4bFVUf/l3XYTz7/ooTw9ff6QRH3q5b/poj/Ogi2HRH0UCT/ooL/RxXzOQzkKQTh7vHb6e38o5LvRRzq0s///f3/XCv+WS//dUy3DwCVU1GMTk7/TRv/nXv9dEv/YC+xpaTphW32dE7p9vmjmZj7YTHvPxXIFQD6PQ763NfxUiz/d1FwZWX/VSTdNh6UQUH////w8PD+/v7+//+ysrL/YzPp6en+7+z+5uH/q4/R0ND4ckzZ3d/qLwX9/f3vVCv8///nMArvYUnyzMP91szm4uLzIwD5IAD5+/z5+fnFIxKzFgrQJxD4WCj4WS//n3/f7O/0iGbl9vr5KQDu+fzNHQmPgYHn7O3/VyjS4OTvLgjgh3uQgIDGFwD/zr7d3+Db29zbsqz4kX+ZLi335+L06uf37+vh5+n1YDj09/n3Qxfo6uv0TR//eEje5+nqKQf28fDY2dnnIwDwpJfd0M/wY0nguK//pIP///9xnuFlAAABAHRSTlP///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////8AU/cHJQAABdNJREFUeNq0lntcU2UYx5GDgM7mNiagwBHc1MFwU8bitrExUEcCisCsbbINxlQuOtgEubpRqKQIKqGCywmId1BDx1Uxo+ymZKVFBWUzky4WUYoW9J6zsyRD/fTp0++P89fv+z7P8z7P+77HZvRfyua/Ayfv52annjmTmu2/qaao+2nAhtuL3tj53EE6Hi9cPXUyMfXGxJ4nAeenfOWdGKYv9XNzKy6OpZ8u2HiJ6F8DPw7Yt3fl6jDWtXq9Xl8aSscLlUqlu3v/pQ8XdlLHBc79fqK0fNeuEdbWUDy3o8EQES/Oq0sSicuIWx7A4wDOi+7Yj4yw7MvddFwuIwAQ/c1iUUmltrCwKm4G5x+A86rX7Vks+67QoSxdAVeJABEIkKQtlEp5cbM5jwDnFgF/eRcLP+SFHxJylQwMqEOAiqAxhAXY9/YdxF8/5OXrPrk6C4sQn9frIkeATDbR9QI8Fth7wn6kvEuvw98hEhcSlQUMC9C7edarTCkC8M3TcNSHwIaVfixW1zWd15uEb3eHL4sBAA0ALrFkMnmDvi4okx0p2XEo5SEwxZu11d4+y8ur8davyTnhkzcqA2g+hszV5CvU7rfeLQEAUyVzVfdYgfO/6a9t7aJneeHzn8lJTuhbOofRQWtIn0omd3eb2vqlQWxmpEJy9ZinFbh9KiyMVVqQleVV7X89IUFwNLcpgKYtI5OprW2mRG0FAvAI5mB1EQasitXXlwsLhEKdu2R3jqcgeuaS5sqmIuA3td4sqQA1M/kaAsFxup0FOFlVWhoWyuAWcAt0ij8meHambHvnMvB3m0ymfQa5FKmZr5ERjF/jLMD9aj+/sCwGV8nlcqdmhws6o1tsts8nU7tbTSZ6khTNaIlGJoOC02AUyPUO9dPTAhiIlHMm9qXYtXB+sumhggCL69KlaEYIQDnQ3oIC2e5uocUNHR0BQIyy3KPRAHgefhZUcL5Zno4G4GtiZI3mDFsBCqQeTAwV+jTQOmhADe+/hAGgBKVWjgWIUZjNjQMLElDgzP5itwCDwQcVzWXKTBSgdpvoJVKpNYCK0tjoFJJsAYT0xIj+iIgIg8EQ3+uNRQDAYgNIyFIBj0ihmJ08cJaUlLHF4ub4+Ph+Q9nlZZ8OplgB001GOtsSgEegUCgDGPDyx/jYOrFYHC/qPYXrEwhSsBrArjrrCl0iQQU8BYUCQRlYSv7Vp/ElInB4NXOv54BGIwAH7gG7ZDKdKxb1goQUEgiCjLVY0ZsuHTydVFInipy7B5ecgALb1q/vQRtnMoUdB1ukkAGAFGU7iAI1BOX+yqSkEvbheQBI8OyM/mz79zZoI4Ba1xwfVqkgIwQFvoY1rihOJEzSaivZ3vNuISEGj27OE961gbEQa34ZVhGMRiPktBYbjdEbVe4iuTbz5z8uIsCEcDdxZiV9vg2WlC9PIgF+IykqBBu+0Ymfi/vT5ezDK+6twyX/+B5eFBTExoi2Nr/qfIkZCbDcwTreo+v9q3wqKirkX967982epQGV7EzQLa3vD+BEO9fvzJcQSCSSMbDWQw1bz3TNB01asOoLK1a8+EleBZuNdKu3yfe7Wb7HgZ9oRIDlDsjoYQC8UCYHLvmRI+JCJpPNZEZG8vmaV3QfxeRb/YFRlgDYvdS5hZjJZDKDkA/qBuMTwxseVkkkBMRPCjw7Ca3AClx5EKcCRkR8xI3YeQpgl8hI6PoDDrZjLzJQ9wxHCeoE0mhiUDuyPIWEyskh5O9X5ejo3dmOBNSKmHkKYFcBO7L9YH3gT+M8+j5wZrtCEhXvLzdRBsbB4j87xj/mBeKop+1oJKACBx5MM5hPoMDlUZNsL3DGe+Ng3CHXq5AZnPZGigWAgL3WwaN9HTz+K0rtUx8LdiSRIAgBjKTAQKcoBw9bdTT1se80LFBP/yL4QMaAk9NARm3UpLUh09UC+Il/ArDdxbR22wUhQAts29Nwdj1P/9eAWzoTknG45JzBFvh/+Tl5iv4UYABojC5AyFTaQwAAAABJRU5ErkJggg==');
  
    
    $DB = new DB();
    $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind. " AS totaal ".
      "FROM TijdelijkeRapportage WHERE ".
      " rapportageDatum ='".$this->rapportageDatum."' AND ".
      " portefeuille = '".$this->portefeuille."' "
      .$__appvar['TijdelijkeRapportageMaakUniek'];
    debugSpecial($query,__FILE__,__LINE__);
    $DB->SQL($query);
    $DB->Query();
    $totaalWaarde = $DB->nextRecord();
    $totaalWaarde = $totaalWaarde['totaal'];
    
    $zorgplichtcategorien=array();
    $query="SELECT waarde as Zorgplicht FROM KeuzePerVermogensbeheerder WHERE Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."' AND categorie='Zorgplichtcategorien' ORDER BY Afdrukvolgorde";
    $DB->SQL($query);
    $DB->Query();
    while($data=$DB->nextRecord())
      $zorgplichtcategorien[$data['Zorgplicht']]=$data;
    
    
    $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind. " AS totaal,
              ZorgplichtPerBeleggingscategorie.Zorgplicht,
              beleggingscategorieOmschrijving ".
      "FROM TijdelijkeRapportage
             INNER JOIN ZorgplichtPerBeleggingscategorie ON TijdelijkeRapportage.beleggingscategorie = ZorgplichtPerBeleggingscategorie.Beleggingscategorie AND ZorgplichtPerBeleggingscategorie.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
             WHERE ".
      " rapportageDatum ='".$this->rapportageDatum."' AND ".
      " portefeuille = '".$this->portefeuille."' "
      .$__appvar['TijdelijkeRapportageMaakUniek']."
              GROUP BY Zorgplicht
              ORDER BY beleggingscategorieVolgorde";
    debugSpecial($query,__FILE__,__LINE__);
    $DB->SQL($query);
    $DB->Query();
    while($data=$DB->nextRecord())
    {
      $zorgplichtcategorien[$data['Zorgplicht']]=$data;
      $verdeling[$data['Zorgplicht']]['percentage'] = $data['totaal']/$totaalWaarde*100;
    }
    
    $query = "SELECT Portefeuilles.Portefeuille, Portefeuilles.Risicoklasse, ZorgplichtPerRisicoklasse.Zorgplicht,
    ZorgplichtPerRisicoklasse.Minimum,
ZorgplichtPerRisicoklasse.Maximum,
ZorgplichtPerRisicoklasse.norm
FROM Portefeuilles
INNER JOIN ZorgplichtPerRisicoklasse ON Portefeuilles.Risicoklasse = ZorgplichtPerRisicoklasse.Risicoklasse AND ZorgplichtPerRisicoklasse.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
WHERE Portefeuilles.Portefeuille='".$this->portefeuille."' ORDER BY Zorgplicht";
    $DB->SQL($query);
    $DB->Query();
    $zorgplichtPerPortefeuille=false;
    while($zorgplicht = $DB->nextRecord())
    {
      $zorgplichtPerPortefeuille=true;
      foreach ($zorgplicht as $key=>$value)
        $zorgplichtcategorien[$zorgplicht['Zorgplicht']][$key]=$value;
    }
    
    if($zorgplichtPerPortefeuille==false && count($this->pdf->portefeuilles)>1)
    {
      foreach($this->pdf->portefeuilles as $port)
      {
        $pWaarde=0;
        $precs=berekenPortefeuilleWaarde($port,$this->rapportageDatum, (substr($this->rapportageDatum,5,5)=='01-01')?true:false,$this->pdf->portefeuilledata['RapportageValuta'],$this->rapportageDatum);
        foreach($precs as $rec)
        {
          $pWaarde+=($rec['actuelePortefeuilleWaardeEuro']/$this->pdf->ValutaKoersEind);
        }
        $pAandeel=$pWaarde/$totaalWaarde;
        
        $query = "SELECT Portefeuilles.Portefeuille, Portefeuilles.Risicoklasse, ZorgplichtPerRisicoklasse.Zorgplicht,
    ZorgplichtPerRisicoklasse.Minimum,
ZorgplichtPerRisicoklasse.Maximum,
ZorgplichtPerRisicoklasse.norm
FROM Portefeuilles
INNER JOIN ZorgplichtPerRisicoklasse ON Portefeuilles.Risicoklasse = ZorgplichtPerRisicoklasse.Risicoklasse AND ZorgplichtPerRisicoklasse.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
WHERE Portefeuilles.Portefeuille='".$port."' ORDER BY Zorgplicht";
        $DB->SQL($query);
        $DB->Query();
        while($zorgplicht = $DB->nextRecord())
        {
          $zorgplichtcategorien[$zorgplicht['Zorgplicht']]['norm']+=$zorgplicht['norm']*$pAandeel;
        }
      }
    }
    
    $query="SELECT vanaf FROM ZorgplichtPerPortefeuille WHERE ZorgplichtPerPortefeuille.Portefeuille='".$this->portefeuille."' AND vanaf < '".$this->perioden['eind']."' ORDER BY vanaf desc limit 1";
    $DB->SQL($query);// echo $query;exit;
    $DB->Query();
    $datum=$DB->nextRecord();
    if($datum['vanaf'] <> '')
      $vanafWhere="AND vanaf='".$datum['vanaf'] ."'";
    else
      $vanafWhere='';
    
    $query="SELECT
ZorgplichtPerPortefeuille.Zorgplicht,
ZorgplichtPerPortefeuille.Portefeuille,
ZorgplichtPerPortefeuille.Vermogensbeheerder,
ZorgplichtPerPortefeuille.Minimum,
ZorgplichtPerPortefeuille.Maximum,
ZorgplichtPerPortefeuille.norm
FROM
ZorgplichtPerPortefeuille
WHERE ZorgplichtPerPortefeuille.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."' AND ZorgplichtPerPortefeuille.Portefeuille='".$this->portefeuille."' $vanafWhere
 ORDER BY Zorgplicht";
    $DB->SQL($query);
    $DB->Query();
    while($zorgplicht = $DB->nextRecord())
    {
      foreach ($zorgplicht as $key=>$value)
        $zorgplichtcategorien[$zorgplicht['Zorgplicht']][$key]=$value;
    }
    
    $zorgplcihtConversie=array('Zakelijke waarden'=>'H-Aand','Alternatieven'=>'H-AltBel','Vastrentende waarden'=>'H-Oblig','Liquiditeiten'=>'H-Liq');
    foreach($zorgplichtcategorien as $zorgplicht=>$zorgplichtData)
    {
      $query="SELECT IndexPerBeleggingscategorie.Fonds,Fondsen.Omschrijving FROM IndexPerBeleggingscategorie
      JOIN Fondsen ON IndexPerBeleggingscategorie.Fonds = Fondsen.Fonds
      WHERE Categoriesoort='Beleggingscategorien' AND Categorie='".$zorgplcihtConversie[$zorgplicht]."' AND Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
      AND (IndexPerBeleggingscategorie.Portefeuille='' OR IndexPerBeleggingscategorie.Portefeuille='".$this->portefeuille."') ORDER BY IndexPerBeleggingscategorie.Portefeuille desc limit 1";
      $DB->SQL($query);
      $DB->Query();
      $data = $DB->nextRecord();
      
      $zorgplichtcategorien[$zorgplicht]['fonds']=$data['Fonds'];
      $zorgplichtcategorien[$zorgplicht]['fondsOmschrijving']=$data['Omschrijving'];
    }
    
    //$index=new indexHerberekening();
    //$perioden=$index->getMaanden(db2jul($beginDatum),db2jul($eindDatum));
    
    foreach($zorgplichtcategorien as $zorgplicht=>$zorgplichtData)
    {
      
      
      $query="SELECT vanaf FROM benchmarkverdelingVanaf WHERE benchmark='".$zorgplichtData['fonds']."' AND vanaf < '".$this->perioden['eind']."' ORDER BY vanaf desc limit 1";
      $DB->SQL($query);
      $DB->Query();
      if($DB->records()>0)
      {
        $datum = $DB->nextRecord();
        $query = "SELECT benchmarkverdelingVanaf.fonds,benchmarkverdelingVanaf.percentage,Fondsen.Omschrijving FROM benchmarkverdelingVanaf
        JOIN Fondsen ON benchmarkverdelingVanaf.fonds = Fondsen.Fonds
        WHERE benchmark='".$zorgplichtData['fonds']."' AND vanaf = '" . $datum['vanaf'] . "'";
        
      }
      else
      {
        $query = "SELECT benchmarkverdeling.fonds,benchmarkverdeling.percentage,Fondsen.Omschrijving
        FROM benchmarkverdeling
        JOIN Fondsen ON benchmarkverdeling.fonds = Fondsen.Fonds
        WHERE benchmark='" . $zorgplichtData['fonds'] . "'";
      }
      $DB->SQL($query);
      $DB->Query();
      while ($data = $DB->nextRecord())
      {
        $zorgplichtcategorien[$zorgplicht]['fondsSamenselling'][$data['fonds']] = $data;
      }
    }
   
    $samengesteldeBenchmark=array();
    $fondsVerdeling=array();
    foreach($zorgplichtcategorien as $zorgplichtCategorie=>$zorgplichtData)
    {
      if(!isset($zorgplichtData['fondsSamenselling']))
        $zorgplichtData['fondsSamenselling']=array($zorgplichtData['fonds']=>array('fonds'=>$zorgplichtData['fonds'],
                                                                                   'percentage'=>100,
                                                                                   'Omschrijving'=>$zorgplichtData['fondsOmschrijving']));
      foreach($zorgplichtData['fondsSamenselling'] as $fonds=>$fondsData)
      {
        
        $indexData[$fonds]['performance'] =getFondsPerformanceGestappeld2($fonds,$this->portefeuille,$this->perioden['begin'],$this->perioden['eind'],'maanden',false,true);
        $indexData[$fonds]['performanceJaar'] =getFondsPerformanceGestappeld2($fonds,$this->portefeuille,$this->perioden['jan'],$this->perioden['eind'],'maanden',false, true);
        
        
        $fondsVerdeling[$zorgplichtData['fonds']][$fonds]+=$fondsData['percentage'];
      }
      $fonds=$zorgplichtData['fonds'];
      
      
      $samengesteldeBenchmark[$zorgplichtCategorie]['norm']=$zorgplichtData['norm'];
      $samengesteldeBenchmark[$zorgplichtCategorie]['periode']=$indexData[$fonds]['performance'];
      $samengesteldeBenchmark[$zorgplichtCategorie]['jaar']=$indexData[$fonds]['performanceJaar'];
      // echo "$fonds <br>\n"; ob_flush();
      $samengesteldeBenchmark[$zorgplichtCategorie]['periode']=getFondsPerformanceGestappeld2($fonds,$this->portefeuille,$this->perioden['begin'],$this->perioden['eind'],'maanden',false,true);
      $indexData[$zorgplichtData['fonds']]['performance']= $samengesteldeBenchmark[$zorgplichtCategorie]['periode'];
      $samengesteldeBenchmark[$zorgplichtCategorie]['jaar']=getFondsPerformanceGestappeld2($fonds,$this->portefeuille,$this->perioden['jan'],$this->perioden['eind'],'maanden',false,true);
      $indexData[$zorgplichtData['fonds']]['performanceJaar']= $samengesteldeBenchmark[$zorgplichtCategorie]['jaar'];
      //logscherm($fonds." ".$samengesteldeBenchmark[$zorgplichtCategorie]['jaar']."<br>\n");
    }
    //listarray($samengesteldeBenchmark);exit;
    
    //////////////////////////
    /*
    $this->pdf->ln();
    $this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
    $this->pdf->Rect($this->pdf->marge, $this->pdf->getY(), 297-$this->pdf->marge*2, 8, 'F');
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
    $this->pdf->ln(2);
    $this->pdf->Cell(100,4, vertaalTekst("Benchmark",$this->pdf->rapport_taal),0,0);
    $this->pdf->ln(2);
    $this->pdf->ln();
    $this->pdf->SetTextColor(0,0,0);
    
    
    $this->pdf->SetWidths(array(60,25,25,25,25,25,25,20,25));
    $this->pdf->SetAligns(array('L','R','R','R','R','R','R','R','R','R'));
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->row(array(vertaalTekst('Uw benchmark',$this->pdf->rapport_taal),vertaalTekst('Gewicht',$this->pdf->rapport_taal),vertaalTekst("Rendement",$this->pdf->rapport_taal)."\n".
      vertaalTekst("periode" ,$this->pdf->rapport_taal),vertaalTekst("Rendement",$this->pdf->rapport_taal)."\n".$this->RapStartJaar));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    
    
    
    $totalen=array();
    foreach($samengesteldeBenchmark as $zorgplichtCategorie=>$data)
    {
      $this->pdf->row(array(vertaalTekst($zorgplichtCategorie,$this->pdf->rapport_taal),
                        $this->formatGetal($data['norm'],1).'%',
                        $this->formatGetal($data['periode'],2).'%',
                        $this->formatGetal($data['jaar'],2).'%'));
      $totalen['norm']+= $data['norm'];
      //$totalen['periode']+=$data['norm']*$data['periode']/100;
      //$totalen['jaar']+=$data['norm']*$data['jaar']/100;
    }
    
    $query="SELECT specifiekeIndex  FROM Portefeuilles WHERE Portefeuilles.Portefeuille='".$this->portefeuille."'";
    $DB->SQL($query);
    $DB->Query();
    $specifiekeIndex = $DB->nextRecord();
    
    $totalen['periode']=getFondsPerformanceGestappeld2($specifiekeIndex['specifiekeIndex'],$this->portefeuille,$this->perioden['begin'],$this->perioden['eind'],'maanden',false,true,false,'benchmarkTot');
    $totalen['jaar']=getFondsPerformanceGestappeld2($specifiekeIndex['specifiekeIndex'],$this->portefeuille,$this->perioden['jan'],$this->perioden['eind'],'maanden',false,true,false,'benchmarkTot');
    
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->row(array(vertaalTekst('Overall benchmark',$this->pdf->rapport_taal),
                      $this->formatGetal($totalen['norm'],1).'%',
                      $this->formatGetal($totalen['periode'],2).'%',
                      $this->formatGetal($totalen['jaar'],2).'%'));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->ln();
    */
    ////////////////////////////////////////////
    
    /*
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->row(array(vertaalTekst('Opbouw benchmark',$this->pdf->rapport_taal),vertaalTekst('Gewicht',$this->pdf->rapport_taal),vertaalTekst("Rendement",$this->pdf->rapport_taal)."\n".
      vertaalTekst("periode" ,$this->pdf->rapport_taal),vertaalTekst("Rendement",$this->pdf->rapport_taal)."\n".$this->RapStartJaar));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    
    
    foreach($zorgplichtcategorien as $zorgplichtCategorie=>$zorgplichtData)
    {
      foreach($zorgplichtData['fondsSamenselling'] as $fonds=>$fondsData)
      {
        $this->pdf->row(array($fondsData['Omschrijving'],
                          $this->formatGetal($fondsData['percentage'],1).'%',
                          $this->formatGetal($indexData[$fonds]['performance'],2).'%',
                          $this->formatGetal($indexData[$fonds]['performanceJaar'],2).'%'));
      }
      $fonds=$zorgplichtData['fonds'];
      $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
      $this->pdf->row(array(vertaalTekst('Benchmark' ,$this->pdf->rapport_taal).' '.vertaalTekst($zorgplichtCategorie,$this->pdf->rapport_taal),
                        $this->formatGetal(100,1).'%',
                        $this->formatGetal($indexData[$fonds]['performance'],2).'%',
                        $this->formatGetal($indexData[$fonds]['performanceJaar'],2).'%'));
      $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
      $this->pdf->ln();
    }
    */
    
    /////////////////////////////////////////////
    if($this->pdf->getY()+(count($zorgplichtcategorien)+4)*$this->pdf->rowHeight+1 > $this->pdf->PageBreakTrigger)
    {
      $this->pdf->addPage();
    }
    
    $margeLinks=125;
    $this->pdf->ln();
    $this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
    $this->pdf->Rect($this->pdf->marge+$margeLinks, $this->pdf->getY(), 297-$this->pdf->marge*2-$margeLinks, 8, 'F');
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
    $this->pdf->ln(2);
    $this->pdf->setX($this->pdf->marge+$margeLinks);
    $this->pdf->Cell(100,4, vertaalTekst("Asset Allocatie per",$this->pdf->rapport_taal)." ".date("j",$this->pdf->rapport_datum)." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",$this->pdf->rapport_datum)],$this->pdf->rapport_taal)." ".date("Y",$this->pdf->rapport_datum),0,0);
    $this->pdf->ln(2);
    $this->pdf->ln();
    $this->pdf->SetTextColor(0,0,0);
    
    $this->pdf->SetWidths(array($margeLinks,45,15,15,15,15,30,20,20,20));
    $xVinkPlaatje=$margeLinks+45+15+15+15+15+30+20+2;
    $this->pdf->SetAligns(array('C','L','R','R','R','R','R','R','R','R','R'));
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->row(array('','',vertaalTekst('Min',$this->pdf->rapport_taal),vertaalTekst("Norm",$this->pdf->rapport_taal),vertaalTekst("Max" ,$this->pdf->rapport_taal),
                      vertaalTekst("Huidig",$this->pdf->rapport_taal),vertaalTekst("Tactische onder- /overweging",$this->pdf->rapport_taal),vertaalTekst("Mandaat controle",$this->pdf->rapport_taal)));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    foreach($zorgplichtcategorien as $zorgplichtCategorie=>$zorgData)
    {
      if(!isset($verdeling[$zorgplichtCategorie]['percentage']))
        $verdeling[$zorgplichtCategorie]['percentage']=0;
      
      if ($verdeling[$zorgplichtCategorie]['percentage']<=$zorgData['Maximum'] && $verdeling[$zorgplichtCategorie]['percentage']>=$zorgData['Minimum'] )
        $this->pdf->MemImage($this->checkImg, $xVinkPlaatje, $this->pdf->getY(), 3.5, 3.5);
      else
        $this->pdf->MemImage($this->deleteImg, $xVinkPlaatje, $this->pdf->getY(), 3.5, 3.5);

      $this->pdf->row(array('',vertaalTekst(($zorgData['beleggingscategorieOmschrijving']<>''?$zorgData['beleggingscategorieOmschrijving']:$zorgplichtCategorie),$this->pdf->rapport_taal),$this->formatGetal($zorgData['Minimum'],1).'%',
                        $this->formatGetal($zorgData['norm'],1).'%',
                        $this->formatGetal($zorgData['Maximum'],1).'%',
                        $this->formatGetal($verdeling[$zorgplichtCategorie]['percentage'],1).'%',
                        $this->formatGetal($verdeling[$zorgplichtCategorie]['percentage']-$zorgData['norm'],1).'%'));
      
    }
    
    
    // listarray($zorgplichtcategorien);
    
    
  }

	function writeRapport()
	{
    
    $this->pdf->addPage();
    $y=$this->pdf->getY();
    $db=new DB();
    $query="SELECT * FROM CRM_naw WHERE Portefeuille='".mysql_real_escape_string($this->portefeuille)."'";
    $db->SQL($query);
    $crmRecord=$db->lookupRecord();
    $this->pdf->SetWidths(array(110));
    $this->pdf->SetAligns(array('L'));
    $this->pdf->ln();
    $this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->Row(array(vertaalTekst("Compliance afspraken",$this->pdf->rapport_taal)));
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->Row(array($crmRecord['Compliance']));
    $this->pdf->setY($y);
    $this->printBenchmarkvergelijking();
    
  }
}
?>