<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/03/14 18:42:03 $
File Versie					: $Revision: 1.4 $

$Log: RapportPERFD_L80.php,v $
Revision 1.4  2020/03/14 18:42:03  rvv
*** empty log message ***

Revision 1.3  2019/02/03 17:52:57  rvv
*** empty log message ***

Revision 1.2  2019/01/30 16:47:26  rvv
*** empty log message ***

Revision 1.1  2018/10/03 15:42:01  rvv
*** empty log message ***



*/
include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/Zorgplichtcontrole.php");
include_once("rapport/include/ATTberekening_L80.php");
include_once($__appvar["basedir"]."/html/rapport/rapportSDberekening.php");
include_once("rapport/include/RapportATT_L80.php");
include_once("rapport/include/RapportPERF_L80.php");

class RapportPERFD_L80
{

	function RapportPERFD_L80($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		global $__appvar;
		$this->__appvar = $__appvar;
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "PERFD";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		$this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->underlinePercentage=1;
    $this->portefeuille = $portefeuille;
    $this->rapportageDatumVanaf = $rapportageDatumVanaf;
    $this->rapportageDatum = $rapportageDatum;

    $this->perf = new RapportPERF_L80($this->pdf,$portefeuille, $this->rapportageDatumVanaf, $this->rapportageDatum);
    $this->pdf->rapport_titel = "Performancemeting over de portefeuilles";




		$RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));
	  $RapStopJaar = date("Y", db2jul($this->rapportageDatum));

	  if(strval($pdf->rapport_ATT_decimaal) != '')
	    $this->bedragDecimalen=$pdf->rapport_ATT_decimaal;
	  else
	    $this->bedragDecimalen=0;

	  $this->periodeId = substr(jul2db(db2jul($this->rapportageDatumVanaf)),0,10)."-".substr(jul2db(db2jul($this->rapportageDatum)),0,10);
	  $this->db = new DB();

	 if ($RapStartJaar != $RapStopJaar)
	 {
     echo "Attributie start- en einddatum moeten in hetzelfde jaar liggen.";
     flush();
     exit;
	 }

    $this->checkImg=base64_decode('iVBORw0KGgoAAAANSUhEUgAAADAAAAAwCAMAAABg3Am1AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoV2luZG93cykiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6OTdDQTYzMjZDNTNGMTFFMkJGM0ZFNzdBN0M4NjZENEIiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6OTdDQTYzMjdDNTNGMTFFMkJGM0ZFNzdBN0M4NjZENEIiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDo5N0NBNjMyNEM1M0YxMUUyQkYzRkU3N0E3Qzg2NkQ0QiIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDo5N0NBNjMyNUM1M0YxMUUyQkYzRkU3N0E3Qzg2NkQ0QiIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PqN37VQAAAMAUExURQAAAP///wCQAACMAACLAACIAACHAACEAACDAACAAAB+AAB6AAB5AAB2AAB0AABzAABxAABvAABtAABoAABlAABfAABdAABbAABZAABWAABUAABRAABNAAAoAAAnAAAfAAASAAaRAgaOAgaKAwaJAweHAwiOBAeMBAeLBAyLBQqHBROSCRaYCxWWCxOZBxSSCRiXChiZCxeXCxqZDBmYDBubDRybDB+dDh2dDiCgDx6fDySsESKhECWtEiWrEiOiESKkDSKiDSGiDSGhDSOlDiKjDiKiDyKhDyasESaqESWqESatEiirEiarEiWkESeuEyiuEyesEyisEyWjEimvFCmtFCqtFCelEyywFSuwFSimFCuvFiqnFS2wF3HKYnnLa3vMbSuvEiutEyuuFCytFC+xFS6vFSyuFSyqFSypFS2vFi6vFi6rFjCxFy6wFzGxGDKxGC+wGDOyGTSxHTezHj63JT22JV3BSWDETF7BTGPET2TGUWPEUGXFU3bLZ3rMaXnMaXnLaHrMa4PPdYXQdonSe4vTfYzTfovTfozUf4/UgY/VgpDVgi6rFC+sFTazGTSyGTWzGjazGji0Gze0GzizGzm1HDm2HTazHDqyHjy3IUq8L17DSF/ESWPGTWPGTmLETWTGT47Ufzq1HDu2HTy2HT23Hj63HkC4Hz+4H0G5IEK4IkO7HUG3H0K5IEa7IUS6IUO6IUW7Ik2/KlPAMVbDNWHEQmnITG/KUofTcHDMT3DMUYDRZoXTaYnVb6LejqLdjaLdj6PdkHrQWQAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAbA7uAAAAKNSURBVHjaYmAkETCMasANuLj5SNLAZe6nyUOCBi732XNmmXETrYHLeW5PT0upMS+RGric5/U0lSUW52rJEKWB021BT11qdnZ6tqkwMRo4HaHqo2P1RYnQwOlY0dOQm52VHh1hzCdPWAOX00KI+hSgern/BDUA1Xc3pmanp4dFQtQT0MDlNL+rMS07PRmuHr8GiPosoPpkEz7Z/4iYZmJhYcXmX9f5XfW5WelhYSEmvFD1YA0sejNXK/Jgmu9Qtag+LyU9LASoXu4/IrUy9x0+sH+bEi+6escFixrygM4JQ1YP1MDSt/fQni2b16nxo7l/QSdQfRiaekYGVr1dB3esWb9h01p1flT17Qmx6WGhQajqGRnYevftXLNpw8Z1G5drCCCpr25vyksGqg9FVQ/U4Ld7+8YN69atWLlyqTJMB6dTdUd9YVhYUFCAAT+KeqAfWHO2bly3asXK5UuXTVURhIZnFVB9UliQf4ABH6p6UCixZqzfuGL58qVLFk+fpCoIVd8UGxoKVi/7H6MQEGPP3Lh8GVD95ClTJqgK8XE51HQkFALV+2BRD444cfaYVUsXT5s8ZdKEibnami61bUD1QYG+/kbo7oElDQmOqGXTJ0+aNKGooCR1RlxbfGEALvXQxCfBGb5k0kSg+vyJ5a3NlWFA9T6BOtjUw1KrBGfwFKD63Nzs5OSAgKAgHzsPHUz3IydvCU77/oLcvGxQ2gQ6x9Ybh3pEfpDgsikoBGUtkHc9capHykCS3Ja56aDE4OPrZYtTPXKOk+S2igUmBl87L1tdrP7FKFuluC3C/IHq7XT5cZmPlqeleEwDPKytdfCoRy8E+LR0DLXwqccoNaRFRGQU/o/WooNeA0CAAQAWlObj4AgonwAAAABJRU5ErkJggg==');
	  $this->deleteImg=base64_decode('iVBORw0KGgoAAAANSUhEUgAAADAAAAAwCAMAAABg3Am1AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAA2ZpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtcE1NOk9yaWdpbmFsRG9jdW1lbnRJRD0ieG1wLmRpZDpDOTFCRkQ2ODM5QzVFMjExOTZBRThFRDMyQTE4OUYwRSIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpFOEY4RkE4NUM1M0YxMUUyODAxM0VGRkE5NEM4QUM5NCIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpFOEY4RkE4NEM1M0YxMUUyODAxM0VGRkE5NEM4QUM5NCIgeG1wOkNyZWF0b3JUb29sPSJBZG9iZSBQaG90b3Nob3AgQ1M2IChXaW5kb3dzKSI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkNBMUJGRDY4MzlDNUUyMTE5NkFFOEVEMzJBMTg5RjBFIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkM5MUJGRDY4MzlDNUUyMTE5NkFFOEVEMzJBMTg5RjBFIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+Si4argAAAwBQTFRFAAAA/////7CA/61//55w/4RS/49h/5Vo/206/3E//3JB/3VC/3ZF/3VF/3hG/3lJ/3pJ/3pK/3lK/3xL/35M/31O/n5R/4BS/4NU/4RW/oZZ/4ha/14p/18u/2Iv/2Av/2Ew/2Ix/2My/2Uz/WMz/2Q0/2g3/2c3/2o5/2g5/2k6/2s7/2o8/2w9/24+/20+/29A+mw//3BB+m1A/3FC/3ND/3NE/3VG/3ZH/3dI+nNH/3pM8jwL9j8N90IR+EMS90MS9UIS+EQT+kYU+EUU90QU+0kV+UYV+kcW+kgX+UgX+UcX90gX60MW4kEV+0kY9kkY8UYX/00Z/UsZ/EkZ+0sZ+EkZ/0wa/Usa80cZ7kYZyzwV/08b/kwb/U0b+0sb+kwb+Esb90sb9kka8Uka/k0c+U0c/k8d/U0d/E4d+04d+k0d9Eoc80wc80oc7kkb/08e+E4e9kwd/1Af+08f/1Eg/FAg+1Eg/1Uh/1Ih/1Mi/lQi/1Qj/lYj/1Uk/1gl/1Yl/1cm/1gn/1ko/1op/1kq/1oq/1wr/10s/14t+lks910y/2M0/WY49WA29GQ792xC2DAHvSoG0DAI6jcK4TUJ8DkL5jYK8jsM7ToM9DwN8z0N8TwN9D0O8z0O8DwO9T8P9UAQ8z8Q9kER+EMU6jwT+kUVwzYSvTMR+EUYwzgT0z0V+0ka+Uka3kAX5UQZ3UEY6kUa71Qu73JStSYG4TIM3S8M5zYP0zQQty4PsyoPty4R6ksp61o56WZI3zkd1SYKyyEKviUMsCAL2TIbqxUEsBsJ1S4b2Tkk3T4rxhcJ0CUV1CoZogwBrA4EyRsRoAcBsAoDwgoFuwsFxBAIvg8J0AAAygAAwgAAtwAAtAAAsgAAsAAArwAArQAAqwAAqQAApwAApQAAowMAowAAoAAAngIAngAAnQIAnAAAmAAAkAAAiwAAgwAAeQAAcgAAaAAAWgAARwAALAAAJAAAGAAACwAABQAAAQAAvQEBsAIBrQMBqwQBpwIBpwUCvgYEaHny+gAABK1JREFUeNpiYCQRMNBCw008Gq7fuIGp/tSlezg0XL9xQurkczT1N64oyyy9cvs+poYb5zezsLCJ3viIquG/inxdaenKAzfvomm4cZKZhZ1NSnTPXVQHdSu11RSnhgd5HryPouHaCZB6aQlrwVPvkNTf2qQm21SZHhsa6XfoFYqG6/tY2KWlJcVFhfqQQuXWHg65trri1LWBfjvvfkF10mmg+VIS4mL8fGdewcRun+BU6misLIiP9F1+8QMDqoafbGzSkhJiooLmej+h/r51nFtdtqWmNCM5avWxl9/RPP1zL1CDuJiotbnpHkgY3jrNp6XY3lhVnJaybuedLxjBep5NCmiBtbWlKef51yD15/l0VTub66oyczauuvjhB4aGn2LS4mLW1kLmJga9Nz8y3j4voK8u39ZYW5yEcBCKhtsnpcVFrYUsLUwMOPbcf3pewEBdsb25oSo7ZcPOe1+wJY3/QA1CluZmXPo6HEfOCxtpKna0NDWUgR3EgE3D3b3iQAeZmRjoa2vomRppKXW2NddXF+VHH3v9HXviO89qbclrYmSgq62hoaOpKt8GtKAwZcOuB19xpNbbEwTNzYxAFqipqyvLtwMtKM9dv+L3JwYcGh6eETEHO0hdVVVRvrOtpbm6aGP0uXcMODPQLWMzIz2weiUFoPqWhsL81Yeff8Od4x4c5zHQhaiXbW9pbirP27D7/hcGPFn0jqGRrhpEPSiECvOj/31kwFMI3Lu1l1tbXbUHpL6lqa4yLffg0294So3bV60EDDRUFSHqG2pKsxKSTr3GreHOCVEeTi1Q+EDVp8dFhCaceoVDw+3L/cKmQA8oyHa2t7QC1VcWADUEe4YefIlVw+0TgrwGGupA4zuA4V9fV1NakJ4aEbHG09vtwEtMDXeu9vMbAL0rL9vRBlTfWFcFUR/i6bHMxWXb03doGm6fEOACKleQ7QC6vrmpoRah3t3J3sFh+983KBpu93PrApUD/doGdH1jbWVJVmZMHES9i5PD4oV28y++RtZwp1ddCaS8BWh6Y31NWVFOTnx8RIg/WL293cIFc2fbwgILrOHmHiVgSmhpBoZlXU1lSU7++qjE4GCIegew+lk2NgceI9mwX64daHZDXV1NdWl2zsbVhx4tDfXygJs/b9aMaVOmnvsE1/DgvFxLY11NTVVlaXpa3sbow08//FnqCVQOMX8uUP30qZOef4VpuHl3vyww4ZSWFhdkJKWsjz729AvDh4dL3Zc42C2Gqp8ydfLRN7CS7+7VLrmWusqCgvT0+OQov1VHX30Fir57uNTVAa5++sQtLz7D/PDktExdVXF6ampsUmTUuhXnXn8DG/Xu0XZHoPpZc6bNBKqfdAFacIDIXxUF6bGxoaEBkb6rd/57Bysh3j/ZvmjenDlA5UD1Z99+RwTro+0x4aFhAYG+ftGHH378AU8F759unTdn5vSpEycj1ENC6XKMv0+gr+/qFedefv6BlNDeP982c/rkSUdfXHj7DSVpPD0Q4Ou3evmxv+++oebAD88nTj76/O3nT9/QEt+jgzsOHXv45jNmrf3y2VushcDbl6/eo7gGDtANYSC1DcEwKBonKAAgwADHDRsbQcugwwAAAABJRU5ErkJggg==');

	}

  function getDoorkijk($portefeuille)
{
  $this->att = new RapportATT_L80($this->pdf,$portefeuille, $this->rapportageDatumVanaf, $this->rapportageDatum);
  $this->perf = new RapportPERF_L80($this->pdf,$portefeuille, $this->rapportageDatumVanaf, $this->rapportageDatum);
  $this->pdf->rapport_type = "PERFD";
  $this->pdf->rapport_titel = "Performancemeting over de portefeuilles";

  if(substr($this->rapportageDatumVanaf,5,5)=='01-01')
  {
    $renteStart=(substr($this->rapportageDatumVanaf,0,4)-1)."-12-31";
  }
  else
    $renteStart=$this->rapportageDatumVanaf;


  //$renteBegin = $this->att->getWaarden($renteStart ,$renteStart ,$portefeuille);
//
  //$renteOpStartDatum=$renteBegin[1]['rente'];
  //$renteOpEindDatum=0;

  $indexDataReal = $this->att->getWaarden($this->rapportageDatumVanaf ,$this->rapportageDatum ,$portefeuille);
  $somVelden=array('stortingen','onttrekkingen','gerealiseerd','ongerealiseerd','opbrengsten','kosten','rente','resultaatVerslagperiode');

  $jaarPerf=1;
  foreach ($indexDataReal as $index=>$data)
  {
    if($data['datum'] != '0000-00-00')
    {

        $jaarPerf=($jaarPerf * ($data['performance']+100)/100);
        $data['performance']=$jaarPerf*100;

        if(!isset($indexJaren))
          $indexJaren['waardeBegin']=$data['waardeBegin'];
        $indexJaren['waardeHuidige']=$data['waardeHuidige'];
        $indexJaren['performance']=$data['performance']-100;
        $indexJaren['index']=$data['index'];
        foreach($somVelden as $veld)
          $indexJaren[$veld]+=$data[$veld];

        foreach($data['opbrengstenPerGrootboek'] as $omschrijving=>$waarde)
          $indexJaren['opbrengstenPerGrootboek'][$omschrijving]+=$waarde;
        foreach($data['kostenPerGrootboek'] as $omschrijving=>$waarde)
          $indexJaren['kostenPerGrootboek'][$omschrijving]-=$waarde;

  //    $renteOpEindDatum=$data['rente'];
    }
  }
//echo "$renteOpEindDatum-$renteOpStartDatum <br>\n";exit;
  $waarden=$indexJaren;
  $waarden['ongerealiseerdeKoersResultaat']=$indexJaren['ongerealiseerd'];
  $waarden['waardeEind']=$indexJaren['waardeHuidige'];
  $waarden['waardeBegin']=$indexJaren['waardeBegin'];
  $waarden['waardeMutatie']=$indexJaren['waardeHuidige']-$indexJaren['waardeBegin'];
  $waarden['stortingen']=$indexJaren['stortingen'];
  $waarden['onttrekkingen']=$indexJaren['onttrekkingen'];
  $waarden['resultaatVerslagperiode']=$indexJaren['resultaatVerslagperiode'];
  $waarden['rendementProcent']=$indexJaren['performance'];
  $waarden['rendementBenchmark']='';//$indexJaren['ongerealiseerd'];
  $waarden['gerealiseerdeKoersResultaat']=$indexJaren['gerealiseerd'];
  $waarden['opgelopenRente']=$indexJaren['rente'];//$renteOpEindDatum-$renteOpStartDatum;
  $waarden['totaalOpbrengst']=$indexJaren['ongerealiseerd']+$indexJaren['gerealiseerd']+$indexJaren['rente'];

  $benchmarkRendement= $this->getBenchmarkRendement($portefeuille);

  $waarden['rendementBenchmark']=$benchmarkRendement;


  return $waarden;
}

/*
  function getBenchmarkVerdeling($datum='')
  {
    if($datum=='')
      $datum=$this->rapportageDatum;
    $DB = new DB();

    $this->index['SpecifiekeIndex']=getSpecifiekeIndex($this->portefeuille,$this->rapportageDatum);

    $benchmarkVerdeling=array();
    $herIndex=false;
    if(count($this->pdf->portefeuilles) > 1)
    {
      foreach($this->pdf->portefeuilles as $cPortefeuille)
      {
        $query="SELECT aandeel*100 as aandeel,datum FROM tempVerdeling WHERE hoofdPortefeuille='" . $this->portefeuille . "' AND  portefeuille='$cPortefeuille' AND datum <='" . $datum. "' ORDER BY Datum desc limit 1";
        $DB->SQL($query);
        $DB->Query();
        $aandeel = $DB->lookupRecord();
        if(round($aandeel['aandeel'],1) <> 100 || $aandeel['aandeel'] > 100)
          $herIndex=true;

        $index['SpecifiekeIndex']=getSpecifiekeIndex($cPortefeuille,$this->rapportageDatum);
//echo $this->rapportageDatum." $cPortefeuille ".$index['SpecifiekeIndex']." ".$aandeel['aandeel']."<br>\n";
        if(isset($benchmarkVerdeling[$index['SpecifiekeIndex']]))
          $benchmarkVerdeling[$index['SpecifiekeIndex']] += $aandeel['aandeel'];
        else
          $benchmarkVerdeling[$index['SpecifiekeIndex']] = $aandeel['aandeel'];
        // echo $aandeel['datum']." ".$index['SpecifiekeIndex']." $cPortefeuille  += ".$aandeel['aandeel'].";<br>\n";
      }
      $benchmarkFonds=$benchmarkVerdeling;
    }
    else
    {
      $benchmarkFonds[$this->index['SpecifiekeIndex']]=100;
    }

    if($herIndex==true)
    {
      $sum=0;
      foreach($benchmarkFonds as $fonds=>$percentage)
        $sum+=abs($percentage);
      foreach($benchmarkFonds as $fonds=>$percentage)
        $benchmarkFonds[$fonds]=abs($percentage)/$sum*100;
      // echo array_sum($benchmarkFonds);
    }

    $verdeling = array();
    foreach($benchmarkFonds as $fonds=>$aandeel)
    {

      $query = "SELECT fonds,percentage FROM benchmarkverdeling WHERE benchmark='$fonds'";
      $DB->SQL($query);
      $DB->Query();

      if ($DB->records() == 0)
        $verdeling[$fonds] = $aandeel;
      else
      {
        while ($data = $DB->nextRecord())
        {
        // echo  $data['percentage']."*$aandeel/100 <br>\n";
        // listarray($data);
        if(isset($verdeling[$data['fonds']]))
          $verdeling[$data['fonds']] += $data['percentage']*$aandeel/100;
        else
          $verdeling[$data['fonds']] = $data['percentage']*$aandeel/100;
        }
      }
    }
    //echo $herIndex;ob_flush();
//listarray($verdeling);exit;
    return $verdeling;
  }
*/

  function formatGetalKoers($waarde, $dec , $start = false)
	{
	  if ($start == false)
	    $waarde = $waarde / $this->pdf->ValutaKoersEind;
	  else
	    $waarde = $waarde / $this->pdf->ValutaKoersBegin;

	  return number_format($waarde,$dec,",",".");
  }

	function formatGetal($waarde, $dec)
	{
		  return number_format($waarde,$dec,",",".");
	}

	function printSubTotaal($title, $totaalA, $totaalB)
	{
		// geen subtotaal!
		return true;
	}

	function tweedeStart()
	{
	  $RapStartJaar = date("Y", db2jul($this->rapportageDatumVanaf));
	  if(db2jul($this->pdf->PortefeuilleStartdatum) > db2jul($this->rapportageDatumVanaf))
	    $this->tweedePerformanceStart = $this->pdf->PortefeuilleStartdatum;
	  elseif(db2jul($this->pdf->PortefeuilleStartdatum) > db2jul("$RapStartJaar-01-01"))
	    $this->tweedePerformanceStart = $this->pdf->PortefeuilleStartdatum;
	  else
	   $this->tweedePerformanceStart = "$RapStartJaar-01-01";
	}

	function getAttributieCategorien($realCategorie)
	{
	  $this->AttCategorien=array('Totaal');
    $categorieOmschrijving['Totaal'] = 'Totaal';
 		$query="SELECT KeuzePerVermogensbeheerder.waarde,
KeuzePerVermogensbeheerder.categorie,
KeuzePerVermogensbeheerder.vermogensbeheerder,
KeuzePerVermogensbeheerder.Afdrukvolgorde,
AttributieCategorien.Omschrijving
FROM
KeuzePerVermogensbeheerder
INNER JOIN AttributieCategorien ON KeuzePerVermogensbeheerder.waarde = AttributieCategorien.AttributieCategorie
WHERE KeuzePerVermogensbeheerder.vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."' AND categorie='AttributieCategorien'
ORDER BY Afdrukvolgorde";
		$this->db->SQL($query);
    $this->db->Query();
		while($categorie = $this->db->nextRecord())
		{
		  $categorieOmschrijving[$categorie['waarde']]=$categorie['Omschrijving'];
      $this->AttCategorien[]=$categorie['waarde'];
		}
  
	  $query = "SELECT  BeleggingssectorPerFonds.AttributieCategorie,  AttributieCategorien.Omschrijving
              FROM BeleggingssectorPerFonds  ,AttributieCategorien
              WHERE BeleggingssectorPerFonds.Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."' AND
              BeleggingssectorPerFonds.AttributieCategorie =  AttributieCategorien.AttributieCategorie
              GROUP BY BeleggingssectorPerFonds.AttributieCategorie
              ORDER By AttributieCategorien.Afdrukvolgorde";
		$this->db->SQL($query);
		$this->db->Query();
		//$this->categorien[] = 'Totaal';
		
		while($categorie = $this->db->nextRecord())
		{
		  $categorieOmschrijving[$categorie['AttributieCategorie']]=$categorie['Omschrijving'];
		  //$this->categorien[]=$categorie['AttributieCategorie'];
		}
		if(!in_array('Liquiditeiten',$this->categorien))
		{
		  $categorieOmschrijving['Liquiditeiten']='Liquiditeiten';
		 // $this->categorien[]='Liquiditeiten';
		}

    $this->pdf->ln();
    $y=$this->pdf->GetY();
		$kopRegel = array();
	  array_push($kopRegel,"");
	  array_push($kopRegel,"");
		foreach ($realCategorie as $categorie)
		{
		  array_push($kopRegel,vertaalTekst($categorieOmschrijving[$categorie],$this->pdf->rapport_taal));
		  array_push($kopRegel,"");
		}
    $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
		$this->pdf->row($kopRegel);
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    $this->pdf->SetY($y+8);
		return $realCategorie;
	}
/*
  function bepaalCategorieWaarden()
  {
    foreach ($this->categorien as $categorie)
    {
      if ($categorie == 'Totaal')
        $attributieQuery = '';
      elseif ($categorie == 'Liquiditeiten')
        $attributieQuery = " TijdelijkeRapportage.AttributieCategorie = '' AND ";
      else
        $attributieQuery = " TijdelijkeRapportage.AttributieCategorie = '".$categorie."' AND";

		  if ($categorie == 'Totaal' || $this->pdf->debug)
		  {

		    $gerealiseerdKoersresultaat[$categorie] = gerealiseerdKoersresultaat($this->portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum,$this->pdf->rapportageValuta,true,$categorie);

	 		  $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersEind." AS totaalB, ".
 						 "SUM(beginPortefeuilleWaardeEuro) / ".$this->pdf->ValutaKoersBegin." AS totaalA ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$this->rapportageDatum."' AND ".
						 " portefeuille = '".$this->portefeuille."' AND ".
						 $attributieQuery
						 ." type = 'fondsen' ".$this->__appvar['TijdelijkeRapportageMaakUniek'];
		    debugSpecial($query,__FILE__,__LINE__);
		    $this->db->SQL($query);
		    $this->db->Query();
		    $totaal = $this->db->nextRecord();
        $ongerealiseerdeKoersResultaaten[$categorie] = ($totaal[totaalB] - $totaal[totaalA]) ;

        $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
						     "FROM TijdelijkeRapportage WHERE ".
						     " rapportageDatum ='".$this->rapportageDatum."' AND ".
						     " portefeuille = '".$this->portefeuille."' AND ".$attributieQuery.
						     " type = 'rente' ".$this->__appvar['TijdelijkeRapportageMaakUniek'];
		    debugSpecial($query,__FILE__,__LINE__);
		    $this->db->SQL($query);
		    $this->db->Query();
		    $totaalA = $this->db->nextRecord();
    		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
				    		 "FROM TijdelijkeRapportage WHERE ".
						     " rapportageDatum ='".$this->rapportageDatumVanaf."' AND ".
						     " portefeuille = '".$this->portefeuille."' AND ". $attributieQuery.
						     " type = 'rente' ". $this->__appvar['TijdelijkeRapportageMaakUniek'] ;
		    debugSpecial($query,__FILE__,__LINE__);
		    $this->db->SQL($query);
		    $this->db->Query();
		    $totaalB = $this->db->nextRecord();
    		$opgelopenRentes[$categorie] = ($totaalA[totaal] - $totaalB[totaal]) / $this->pdf->ValutaKoersEind;
      }
    }
    $waarden=array('gerealiseerdKoersresultaat'=>$gerealiseerdKoersresultaat,
                   'ongerealiseerdeKoersResultaaten'=>$ongerealiseerdeKoersResultaaten,
                   'opgelopenRentes'=>$opgelopenRentes);
    $this->waarde = $waarden;
  return $waarden;
  }
*/

  function createRows()
  {
    $row['waardeVanaf'] = array("",vertaalTekst("Waarde portefeuille per",$this->pdf->rapport_taal)." ".date("j",db2jul($this->rapportageDatumVanaf))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatumVanaf))],$this->pdf->rapport_taal)." ".date("Y",db2jul($this->rapportageDatumVanaf)));
    $row['waardeTot'] = array("",vertaalTekst("Waarde portefeuille per",$this->pdf->rapport_taal)." ".date("j",db2jul($this->rapportageDatum))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatum))],$this->pdf->rapport_taal)." ".date("Y",db2jul($this->rapportageDatum)));
    $row['mutatiewaarde'] = array("",vertaalTekst("Mutatie waarde portefeuille",$this->pdf->rapport_taal));
    $row['totaalStortingen'] = array("",vertaalTekst("Totaal stortingen verslagperiode",$this->pdf->rapport_taal));
    $row['totaalOnttrekkingen'] = array("",vertaalTekst("Totaal onttrekkingen verslagperiode",$this->pdf->rapport_taal));
    $row['directeOpbrengsten'] = array("",vertaalTekst("Directe opbrengsten",$this->pdf->rapport_taal));
    $row['toegerekendeKosten'] = array("",vertaalTekst("Toegerekende kosten",$this->pdf->rapport_taal));
    $row['resultaatVerslagperiode'] = array("",vertaalTekst("Resultaat over verslagperiode",$this->pdf->rapport_taal));
    $row['rendementProcent'] = array("",vertaalTekst("Rendement over verslagperiode",$this->pdf->rapport_taal));
    $row['rendementProcentJaar'] = array("",vertaalTekst("Rendement over lopende jaar",$this->pdf->rapport_taal));
    $row['gerealiseerdKoersresultaat'] = array("",vertaalTekst("gerealiseerdKoersresultaat",$this->pdf->rapport_taal));
    $row['ongerealiseerdeKoersResultaaten'] = array("",vertaalTekst("ongerealiseerdeKoersResultaaten",$this->pdf->rapport_taal));
    $row['opgelopenRentes'] = array("",vertaalTekst("opgelopenRentes",$this->pdf->rapport_taal));
    $row['totaal'] = array("",vertaalTekst("Totaal Performance",$this->pdf->rapport_taal));
    

    foreach ($this->categorien as $categorie)
    {
      $resultaatVerslagperiode[$categorie] = $this->waarden['rapportagePeriode']['mutatie'][$categorie] - $this->waarden['rapportagePeriode']['stortingen'][$categorie] + $this->waarden['rapportagePeriode']['onttrekkingen'][$categorie] + $this->waarden['rapportagePeriode']['opbrengsten'][$categorie] - $this->waarden['rapportagePeriode']['kosten'][$categorie];
      if ($categorie == 'Totaal')
      {
        $resultaatCorrectie = $resultaatVerslagperiode['Totaal'] - $this->waarde['opgelopenRentes'][$categorie] - $this->waarde['ongerealiseerdeKoersResultaaten'][$categorie] -
                                                               ($this->waardenPerGrootboek['totaalOpbrengst'] - $this->waardenPerGrootboek['totaalKosten']);
        if(round($resultaatCorrectie,1) != round($this->waarde['gerealiseerdKoersresultaat'][$categorie],1))//correctie vreemde valuta
        {
          $this->waarde['gerealiseerdKoersresultaat'][$categorie]  = $resultaatCorrectie ;
        }
      }

      array_push($row['waardeVanaf'],$this->formatGetal($this->waarden['rapportagePeriode']['beginWaarde'][$categorie],$this->bedragDecimalen,true));
      array_push($row['waardeVanaf'],"");

      array_push($row['waardeTot'],$this->formatGetal($this->waarden['rapportagePeriode']['eindWaarde'][$categorie],$this->bedragDecimalen));
      array_push($row['waardeTot'],"");

      array_push($row['mutatiewaarde'],$this->formatGetal($this->waarden['rapportagePeriode']['mutatie'][$categorie],$this->bedragDecimalen));
      array_push($row['mutatiewaarde'],"");

      if ($categorie == 'Liquiditeiten')
      {
        array_push($row['totaalStortingen'],$this->formatGetal($this->waarden['rapportagePeriode']['stortingen'][$categorie],$this->bedragDecimalen));
        array_push($row['totaalOnttrekkingen'],$this->formatGetal($this->waarden['rapportagePeriode']['onttrekkingen'][$categorie],$this->bedragDecimalen));
        array_push($row['rendementProcent'],' ');
        array_push($row['rendementProcent'],' ');
        array_push($row['rendementProcentJaar'],' ');
        array_push($row['rendementProcentJaar'],' ');
      }
      else
      {
        array_push($row['totaalStortingen'],$this->formatGetal($this->waarden['rapportagePeriode']['stortingen'][$categorie],$this->bedragDecimalen));
        array_push($row['totaalOnttrekkingen'],$this->formatGetal($this->waarden['rapportagePeriode']['onttrekkingen'][$categorie],$this->bedragDecimalen));
        array_push($row['rendementProcent'],$this->formatGetal($this->waarden['rapportagePeriode']['performance'][$categorie],2));
        array_push($row['rendementProcent'],'%');
        array_push($row['rendementProcentJaar'],$this->formatGetal($this->waarden['lopendeJaar']['performance'][$categorie],2));
        array_push($row['rendementProcentJaar'],'%');
      }

      array_push($row['totaalStortingen'],"");
      array_push($row['totaalOnttrekkingen'],"");

      if ($categorie == 'Totaal')
      {
        array_push($row['directeOpbrengsten'],'0');
        array_push($row['toegerekendeKosten'],'0');
      }
      else
      {
        array_push($row['directeOpbrengsten'],$this->formatGetal($this->waarden['rapportagePeriode']['opbrengsten'][$categorie],$this->bedragDecimalen));
        array_push($row['toegerekendeKosten'],$this->formatGetal($this->waarden['rapportagePeriode']['kosten'][$categorie],$this->bedragDecimalen));
      }
      array_push($row['directeOpbrengsten'],"");
      array_push($row['toegerekendeKosten'],"");

      array_push($row['resultaatVerslagperiode'],$this->formatGetal($resultaatVerslagperiode[$categorie],$this->bedragDecimalen));
      array_push($row['resultaatVerslagperiode'],"");
   }
  return $row;
  }


  function waardenPerGrootboek()
  {

	  if ($this->pdf->rapportageValuta != "EUR" && $this->pdf->rapportageValuta != '')
      $koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$this->pdf->rapportageValuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
	  else
	    $koersQuery = "";

		$query = "SELECT Grootboekrekeningen.Omschrijving,Grootboekrekeningen.Grootboekrekening,
		Grootboekrekeningen.Kosten ,Grootboekrekeningen.Opbrengst,".
		"SUM(ABS(Rekeningmutaties.Credit) * Rekeningmutaties.Valutakoers $koersQuery ) -  ".
		"SUM(ABS(Rekeningmutaties.Debet) * Rekeningmutaties.Valutakoers $koersQuery )AS waarde ".
		"FROM Rekeningmutaties, Rekeningen, Portefeuilles, Grootboekrekeningen ".
		"WHERE ".
		"Rekeningmutaties.Rekening = Rekeningen.Rekening AND ".
		"Rekeningen.Portefeuille = '".$this->portefeuille."' AND ".
		"Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
		"Rekeningmutaties.Verwerkt = '1' AND ".
		"Rekeningmutaties.Boekdatum > '".$this->rapportageDatumVanaf."' AND ".
		"Rekeningmutaties.Boekdatum <= '".$this->rapportageDatum."' AND ".
		"Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.GrootboekRekening AND ".
		"  (Grootboekrekeningen.Kosten = '1' || Grootboekrekeningen.Opbrengst ='1') ".
		"GROUP BY Rekeningmutaties.Grootboekrekening ".
		"ORDER BY Grootboekrekeningen.Afdrukvolgorde ";

		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$waardenPerGrootboek = array();
		while($grootboek = $DB->nextRecord())
		{
		  if($grootboek['Opbrengst']=='1')
		  {
		    $waardenPerGrootboek['opbrengst'][$grootboek['Grootboekrekening']]['omschrijving'] = $grootboek['Omschrijving'];
		    $waardenPerGrootboek['opbrengst'][$grootboek['Grootboekrekening']]['bedrag'] += $grootboek['waarde'];
		    $waardenPerGrootboek['totaalOpbrengst'] += $grootboek['waarde'];
		  }
		  else
		  {
		  	if($grootboek[Grootboekrekening] == "KNBA")
		  	{
		  	  $waardenPerGrootboek['kosten'][$grootboek['Grootboekrekening']]['omschrijving'] = "Bankkosten en provisie";
		  	  $waardenPerGrootboek['kosten'][$grootboek['Grootboekrekening']]['bedrag'] -= $grootboek['waarde'];
			  }
			  else if($grootboek[Grootboekrekening] == "KOBU")
		  	{
				  $waardenPerGrootboek['kosten']['KOST']['bedrag'] -= $grootboek['waarde'];
				  $waardenPerGrootboek['kosten']['KOST']['omschrijving'] = "Transactiekosten";
		  	}
		  	else
			  {
		  		$waardenPerGrootboek['kosten'][$grootboek['Grootboekrekening']]['omschrijving'] = $grootboek['Omschrijving'];
			  	$waardenPerGrootboek['kosten'][$grootboek['Grootboekrekening']]['bedrag'] -= $grootboek['waarde'];
		  	}
        $waardenPerGrootboek['totaalKosten'] -= $grootboek['waarde'];
		  }
		}

		return $waardenPerGrootboek;
  }

  function getCRMnaam($portefeuille)
  {
    $db = new DB();
    $query="SELECT naam FROM CRM_naw WHERE portefeuille='$portefeuille'";
    $db->SQL($query);
    $crmData=$db->lookupRecord();
    $naamParts=explode('-',$crmData['naam'],2);
    $naam=trim($naamParts[1]);
    if($naam<>'')
      return $naam;
    else
      return $portefeuille;
  }


	function writeRapport()
	{
	  $this->tweedeStart();
	  $DB = new DB();
		$this->pdf->SetLineWidth($this->pdf->lineWidth);
		$kopStyle = "u";

	  if ($this->pdf->rapportageValuta != "EUR" && $this->pdf->rapportageValuta != '')
      $koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$this->pdf->rapportageValuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
	  else
	    $koersQuery = "";

	  if($this->pdf->portefeuilledata['PerformanceBerekening'] == 6)
	    $periodeBlok = 'kwartaal';
	  else
	    $periodeBlok = 'maand';

	  $query =  "SELECT Portefeuilles.Vermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Portefeuille, Portefeuilles.Startdatum, ".
		" Portefeuilles.Einddatum, Portefeuilles.Client, Portefeuilles.Depotbank, Portefeuilles.RapportageValuta, Vermogensbeheerders.attributieInPerformance, ".
		" Clienten.Naam, Portefeuilles.ClientVermogensbeheerder FROM (Portefeuilles, Clienten ,Vermogensbeheerders)  WHERE ".
		" Portefeuilles.Client = Clienten.Client AND Vermogensbeheerders.Vermogensbeheerder = Portefeuilles.Vermogensbeheerder".
		" AND Portefeuilles.Portefeuille = '$this->portefeuille' ";
		$DB->SQL($query);
		$pdata = $DB->lookupRecord();

    $this->indexberekening=new indexHerberekening();
    $this->perioden=$this->indexberekening->getMaanden(db2jul($this->rapportageDatumVanaf),db2jul($this->rapportageDatum));

	
  $realCategorie=array();
  foreach($this->berekening->categorien as $categorie)
  {
    if($this->waarden['lopendeJaar']['eindWaarde'][$categorie] <> 0 || $this->waarden['lopendeJaar']['beginWaarde'][$categorie] <> 0 || $this->waarden['lopendeJaar']['stortingen'][$categorie] <> 0 || $this->waarden['lopendeJaar']['onttrekkingen'][$categorie] <> 0)
    {
      $realCategorie[]=$categorie;
    }
  }
  
    $tmpCat=array();
    foreach($realCategorie as $categorie)
    {
      if($categorie <> 'Totaal' && $categorie <> 'Liquiditeiten')
        $tmpCat[]=$categorie;
    }

    if(count($realCategorie) > 6)
      $x=185/count($realCategorie)-3;
    else
      $x=23;  

    $this->pdf->widthA = array(0,115,$x,3,$x,3,$x,3,$x,3,$x,3,$x,3,$x,3,$x,3,$x,3,$x,3);
		$this->pdf->alignA = array('L','L','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R','R');
    $this->pdf->widthB = array(0,115,30,10,30,116);
		$this->pdf->alignB = array('L','L','R','R','R','R','R','R');
    $this->pdf->templateVars['PERFDPaginas']=$this->pdf->page+1;
    $this->pdf->templateVarsOmschrijving['PERFDPaginas']=$this->pdf->rapport_titel;


  //
  // {

 
      $fillPortefeuilles=$this->pdf->portefeuilles;
      if(is_array($this->pdf->__appvar['consolidatie']))
        $fillPortefeuilles[]=$this->portefeuille;
      if(count($fillPortefeuilles)==0)
        $fillPortefeuilles=array($this->portefeuille);
      if(!is_array($this->pdf->portefeuilles))
        $this->pdf->portefeuilles=$fillPortefeuilles;

    //listarray($fillPortefeuilles);exit;
      foreach($fillPortefeuilles as $portefeuille)
      {
        if(!isset($this->perfWaarden[$portefeuille]))
          $this->perfWaarden[$portefeuille]=$this->getWaarden($portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum);
      }

      $backup=$this->pdf->portefeuilles;
      $this->allPortefeuilles=$backup;
      $aantalPortefeuilles=count($this->pdf->portefeuilles);
      $this->portefeuillesList=array();
      if($aantalPortefeuilles>6)
      {
        $n=1;
        $p=0;
        $verdeling=array();
        $tmp=array();
        foreach($this->pdf->portefeuilles as $index=>$portefeuille)
        {
          //echo "$n $p $aantalPortefeuilles $portefeuille <br>\n";
          $tmp[]=$portefeuille;
          if($n%6==0 || $n == $aantalPortefeuilles)
          {
            $verdeling[$p]=$tmp;
            $tmp=array();
            $p++;
           // $n=0;
          }
          
          $n++;
        }
       // listarray($verdeling);exit;
        foreach($verdeling as $pagina=>$portefeuilles)
        {
          $this->portefeuillesList=$portefeuilles;
          $this->addconsolidatie();
        }
        $this->pdf->portefeuilles=$backup;
      }
      else
      {

        $this->portefeuillesList=$this->pdf->portefeuilles;
        $this->addconsolidatie();
      }

  // }
  
	 if($this->pdf->debug)
	 {
	  // listarray($this->berekening->performance);flush();
	  // exit;
   }
	}

  
  

 function addconsolidatie()
 {
  

  //$this->pdf->doubleHeader=true;
  $this->pdf->addPage();


  $startPeriodeTxt=date("j",db2jul($this->rapportageDatumVanaf))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatumVanaf))],$this->pdf->taal)." ".date("Y",db2jul($this->rapportageDatumVanaf));
    $startJaarTxt=date("j",db2jul($this->rapportageDatumVanaf))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatumVanaf))],$this->pdf->taal)." ".date("Y",db2jul($startDatum));
    $eindPeriodeTxt=date("j",db2jul($this->rapportageDatum))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatum))],$this->pdf->taal)." ".date("Y",db2jul($this->rapportageDatum));

	//	$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
  //  $this->pdf->SetDrawColor($this->pdf->kopkleur[0],$this->pdf->kopkleur[1],$this->pdf->kopkleur[2]);
  //  $this->pdf->SetFillColor($this->pdf->kopkleur[0],$this->pdf->kopkleur[1],$this->pdf->kopkleur[2]);
 // listarray($this->pdf->portefeuilles);
  $fillArray=array(0,1);
  $subOnder=array('','');
  $volOnder=array('U','U');
  $subBoven=array('','');
  $header=array("",vertaalTekst(" \nResultaat verslagperiode",$this->pdf->rapport_taal));
  $samenstelling=array("",vertaalTekst("Samenstelling resultaat over verslagperiode",$this->pdf->rapport_taal));
  
  $db=new DB();

  if(isset($this->pdf->__appvar['consolidatie']) && !in_array($this->portefeuille,$this->portefeuillesList))
    $portefeuilles[]=$this->portefeuille;
  else
    $portefeuilles=array();

  foreach($this->portefeuillesList as $portefeuille)
    $portefeuilles[]=$portefeuille;
  $longName=false;

  $perfWaarden=array();
  foreach($portefeuilles as $portefeuille)
  {
    $kop=$this->getCRMnaam($portefeuille);
    if(strlen($kop)>15)
      $longName=true;
    $query="SELECT Depotbanken.omschrijving,Portefeuilles.ClientVermogensbeheerder FROM Depotbanken JOIN Portefeuilles ON Portefeuilles.Depotbank=Depotbanken.Depotbank WHERE Portefeuilles.Portefeuille='".$portefeuille."'";
    $db->SQL($query);
    $depotbank=$db->lookupRecord();
    $volOnder[]='U';
    $volOnder[]='U';
    $subOnder[]='U';
    $subOnder[]='';
    $subBoven[]='T';
    $subBoven[]='';    
    $fillArray[]=1;
    $fillArray[]=1;
    if($portefeuille==$this->portefeuille)
      $header[]=vertaalTekst("Totaal",$this->pdf->rapport_taal);
    else
    {
      if($portefeuille<> $kop)
        $header[] = $kop;
      elseif($depotbank['ClientVermogensbeheerder']<>'')
        $header[] = $depotbank['ClientVermogensbeheerder'] . "\n" . $depotbank['omschrijving'];
      else
        $header[] = $portefeuille. "\n" . $depotbank['omschrijving'];
    }
    $header[]='';
    $samenstelling[]='';
    $samenstelling[]='';
    if(!isset($this->perfWaarden[$portefeuille]))
      $this->perfWaarden[$portefeuille]=$this->getWaarden($portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum);
      
    $perfWaarden[$portefeuille]=$this->perfWaarden[$portefeuille];
  }

  foreach($perfWaarden as $port=>$waarden)
  {
    foreach($waarden['opbrengstenPerGrootboek'] as $categorie=>$waarde)
      if(round($waarde,2)!=0.00)
       $opbrengstCategorien[$categorie]=$categorie;
    foreach($waarden['kostenPerGrootboek'] as $categorie=>$waarde)
      if(round($waarde,2)!=0.00)
        $kostenCategorien[$categorie]=$categorie;   
  }
  
  $perbegin=array("",vertaalTekst("Waarde portefeuille per",$this->pdf->rapport_taal)." ".date("j",db2jul($this->rapportageDatumVanaf))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatumVanaf))],$this->pdf->taal)." ".date("Y",db2jul($this->rapportageDatumVanaf)));
  $waardeRapdatum=array("",vertaalTekst("Waarde portefeuille per",$this->pdf->rapport_taal)." ".date("j",db2jul($this->rapportageDatum))." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",db2jul($this->rapportageDatum))],$this->pdf->taal)." ".date("Y",db2jul($this->rapportageDatum)));
  $mutwaarde=array("",vertaalTekst("Mutatie waarde portefeuille",$this->pdf->rapport_taal));
  $stortingen=array("",vertaalTekst("Totaal stortingen verslagperiode",$this->pdf->rapport_taal));
  $onttrekking=array("",vertaalTekst("Totaal onttrekkingen verslagperiode",$this->pdf->rapport_taal));
  $resultaat=array("",vertaalTekst("Resultaat over verslagperiode",$this->pdf->rapport_taal));
  $rendement=array("",vertaalTekst("Rendement over verslagperiode",$this->pdf->rapport_taal));
  $rendementBenchmark=array("",vertaalTekst("Rendement benchmark",$this->pdf->rapport_taal));
  $ongerealiseerd=array("",vertaalTekst("Ongerealiseerde koersresultaten",$this->pdf->rapport_taal)); //
$gerealiseerd=array("",vertaalTekst("Gerealiseerde koersresultaten",$this->pdf->rapport_taal)); //
$valutaResultaat=array("",vertaalTekst("Koersresultaten valuta's",$this->pdf->rapport_taal)); //
$totaalResultaat=array("",vertaalTekst("Koersresultaat",$this->pdf->rapport_taal)); //
$rente=array("",vertaalTekst("Mutatie opgelopen rente",$this->pdf->rapport_taal));//
$totaalOpbrengst=array("",vertaalTekst("Totaal beleggingsresultaat",$this->pdf->rapport_taal));//totaalOpbrengst
$aandeel=array("",vertaalTekst("Percentage v/h vermogen",$this->pdf->rapport_taal));//

    $totaalKosten=array("",vertaalTekst("Totaal kosten",$this->pdf->rapport_taal));   //totaalKosten
    $totaal=array("",vertaalTekst("Resultaat over verslagperiode",$this->pdf->rapport_taal));   //totaalOpbrengst-totaalKosten

  foreach($perfWaarden as $portefeuille=>$waarden)
  { 
    $perbegin[]=$this->formatGetal($perfWaarden[$portefeuille]['waardeBegin'],0,true);
    $perbegin[]='';
    $waardeRapdatum[]=$this->formatGetal($perfWaarden[$portefeuille]['waardeEind'],0,true);
    $waardeRapdatum[]='';
    $mutwaarde[]=$this->formatGetal($perfWaarden[$portefeuille]['waardeMutatie'],0,true);
    $mutwaarde[]='';
    $stortingen[]=$this->formatGetal($perfWaarden[$portefeuille]['stortingen'],0);
    $stortingen[]='';
    $onttrekking[]=$this->formatGetal($perfWaarden[$portefeuille]['onttrekkingen']*-1,0);
    $onttrekking[]='';
    $resultaat[]=$this->formatGetal($perfWaarden[$portefeuille]['resultaatVerslagperiode'],0);
    $resultaat[]='';
    $rendement[]=$this->formatGetal($perfWaarden[$portefeuille]['rendementProcent'],2);
    $rendement[]='%';
/*
    if($portefeuille==$this->portefeuille)
    {
      $rendementBenchmark[] = '';
      $rendementBenchmark[] = '';
    }
    else
    {
*/

    $bechmarkOnderdrukken=false;
    if($this->pdf->lastPOST['doorkijk']==1||$portefeuille==$this->portefeuille)// && $portefeuille<>$this->portefeuille)
    {
     // $tmp=$this->perf->indexPerformance('totaal',$this->rapportageDatumVanaf ,$this->rapportageDatum);
    //  $perfWaarden[$portefeuille]['rendementBenchmark'] = $tmp['perf']*100;
     // listarray($perfWaarden[$portefeuille]['rendementBenchmark']);exit;
/*
      $stdev=new rapportSDberekening($portefeuille,$this->rapportageDatum,1);
      $stdev->settings['SdFrequentie']='m';
      $stdev->setStartdatum($this->rapportageDatumVanaf );
      $stdev->settings['gebruikHistorischePortefeuilleIndex']=false;
      $stdev->addReeks('benchmarkTot', '',true);
      $vanaf=$this->rapportageDatumVanaf;
      $perfWaarden[$portefeuille]['rendementBenchmark']=0;
      foreach($stdev->reeksen['benchmarkTot'] as $datum=>$perf)
      {
        if($portefeuille!=$this->portefeuille)
          $inputPortefeuille=$portefeuille;
        else
          $inputPortefeuille='';

        $tmp=$this->perf->benchmarkVerdelingOpDatum($datum,'totaal',$inputPortefeuille);
        if(count($tmp)==0)
          $bechmarkOnderdrukken=true;
        else
          $bechmarkOnderdrukken=false;

        //listarray($inputPortefeuille);
        //listarray($tmp);
        $perf=$this->perf->getFondsPerformance($tmp,$vanaf,$datum);
        //listarray($tmp);
        //echo  " $vanaf,$datum $perf <br>\n";
        //$perf=getFondsPerformanceGestappeld($fonds,$this->portefeuille,$van,$tot,'maanden');
//listarray($tmp);
//        echo "$datum $portefeuille!=".$this->portefeuille." $inputPortefeuille $perf<br>\n";
       // $tmp=$this->perf->indexPerformance('totaal',$vanaf ,$datum);
        $perf = $perf/100;

        $vanaf=$datum;
        $perfWaarden[$portefeuille]['rendementBenchmark'] = (((1 + $perfWaarden[$portefeuille]['rendementBenchmark']/ 100) * (1 + $perf)) - 1) * 100;

      }
*/
      $perfWaarden[$portefeuille]['rendementBenchmark'] = $this->getBenchmarkRendement($portefeuille);
    }
    else
    {
      if($portefeuille!=$this->portefeuille)
        $inputPortefeuille=$portefeuille;
      else
        $inputPortefeuille='';
      $this->perf = new RapportPERF_L80($this->pdf,$portefeuille, $this->rapportageDatumVanaf, $this->rapportageDatum);
      $this->pdf->rapport_titel = "Performancemeting over de portefeuilles";

      //if(isset($this->pdf->__appvar['consolidatie'])&& count($this->allPortefeuilles)>0)
      //  $this->pdf->portefeuilles=$this->allPortefeuilles;

  //    $tmp=$this->perf->benchmarkVerdelingOpDatum($this->rapportageDatum, 'totaal',$inputPortefeuille);

      //echo $this->rapportageDatum."('totaal')".$this->portefeuille." $inputPortefeuille<br>\n";
      //listarray($tmp);

      if(count($tmp)==0)
        $bechmarkOnderdrukken=true;
      else
        $bechmarkOnderdrukken=false;
    }
    
    if($bechmarkOnderdrukken==true)
    {
      $rendementBenchmark[] = '';
      $rendementBenchmark[] = '';
    }
    else
    {
      $rendementBenchmark[] = $this->formatGetal($perfWaarden[$portefeuille]['rendementBenchmark'], 2);
      $rendementBenchmark[] = '%';
    }
//    }
  //  listarray($perfWaarden[$portefeuille]);
    $ongerealiseerd[]=$this->formatGetal($perfWaarden[$portefeuille]['ongerealiseerdeKoersResultaat'],0);
    $ongerealiseerd[]='';
    $gerealiseerd[]=$this->formatGetal($perfWaarden[$portefeuille]['gerealiseerdeKoersResultaat'],0);
    $gerealiseerd[]='';
    $valutaResultaat[]=$this->formatGetal($perfWaarden[$portefeuille]['koersResulaatValutas'],0);
    $valutaResultaat[]='';
 
    $totaalResultaat[]=$this->formatGetal($perfWaarden[$portefeuille]['ongerealiseerdeKoersResultaat']+$perfWaarden[$portefeuille]['gerealiseerdeKoersResultaat']+$perfWaarden[$portefeuille]['koersResulaatValutas'],0);
    $totaalResultaat[]='';   
    
    $rente[]=$this->formatGetal($perfWaarden[$portefeuille]['opgelopenRente'],0);
    $rente[]='';
    $totaalOpbrengst[]=$this->formatGetal($perfWaarden[$portefeuille]['totaalOpbrengst'],0);
    $totaalOpbrengst[]='';
    $totaalKosten[]=$this->formatGetal($perfWaarden[$portefeuille]['totaalKosten'],0);
    $totaalKosten[]='';
    $totaal[]=$this->formatGetal($perfWaarden[$portefeuille]['totaalOpbrengst']-$perfWaarden[$portefeuille]['totaalKosten'],0);
    $totaal[]='';
    $aandeel[]=$this->formatGetal($perfWaarden[$portefeuille]['waardeEind']/$this->perfWaarden[$this->portefeuille]['waardeEind']*100,1);
    $aandeel[]='%';
    
  }     

 // if($longName==true && count($portefeuilles) < 8)
    $cols=7;
  //else
  //  $cols=9;  
  
    $w=(297-2*8-60-(9*3))/$cols;
    $w2=4.5;
  	$this->pdf->widthB = array(0,60,$w,$w2,$w,$w2,$w,$w2,$w,$w2,$w,$w2,$w,$w2,$w,$w2,$w,$w2,$w,$w2);
		$this->pdf->alignB = array('L','L','R','L','R','L','R','L','R','L','R','L','R','L','R','L','R','L','R');
    $this->pdf->widthA = $this->pdf->widthB;
		$this->pdf->alignA = $this->pdf->alignB;
  
 // $this->pdf->ln();

		$this->pdf->SetWidths($this->pdf->widthB);
		$this->pdf->SetAligns($this->pdf->alignB);
  
  // $this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
  // $this->pdf->Rect($this->pdf->marge, $this->pdf->getY(), 297-$this->pdf->marge*2, 8, 'F');
   
  		$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_kop_fontstyle,$this->pdf->rapport_fontsize);//$this->pdf->rapport_kop_fontsize
    //$this->pdf->fillCell=$fillArray;
   // $this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
    $ystart=$this->pdf->getY();
		$this->pdf->row($header);
  // $this->pdf->setY($ystart+8);
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
    $this->pdf->fillCell=array();
		$this->pdf->SetWidths($this->pdf->widthA);
		$this->pdf->SetAligns($this->pdf->alignA);

		$this->pdf->row($perbegin);
	//,$this->formatGetal($data['periode']['waardeBegin'],2,true),"",$this->formatGetal($data['ytm']['waardeBegin'],2,true),""));
    $this->pdf->CellBorders = $subOnder;
		$this->pdf->row($waardeRapdatum);//$this->formatGetal($data['periode']['waardeEind'],0),"",$this->formatGetal($data['ytm']['waardeEind'],0),""));
    $this->pdf->CellBorders = array();
			// subtotaal
		//$this->pdf->Line($posSubtotaal+$extraLengte  ,$this->pdf->GetY() ,$posSubtotaalEnd ,$this->pdf->GetY());
		$this->pdf->ln();
		$this->pdf->row($mutwaarde);//,$this->formatGetal($data['periode']['waardeMutatie'],0),"",$this->formatGetal($data['ytm']['waardeMutatie'],0),""));
		$this->pdf->row($stortingen);////,$this->formatGetal($data['periode']['stortingen'],0),"",$this->formatGetal($data['ytm']['stortingen'],0),""));
		$this->pdf->CellBorders = $subOnder;
    $this->pdf->row($onttrekking);//,$this->formatGetal($data['periode']['onttrekkingen'],0),"",$this->formatGetal($data['ytm']['onttrekkingen'],0),""));
    $this->pdf->ln();
		$this->pdf->row($resultaat);//,$this->formatGetal($data['periode']['resultaatVerslagperiode'],0),"",$this->formatGetal($data['ytm']['resultaatVerslagperiode'],0),""));
		$this->pdf->ln();
     unset( $this->pdf->CellBorders );
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
		$this->pdf->row($rendement);//,$this->formatGetal($data['periode']['rendementProcent'],0),"%",$this->formatGetal($data['ytm']['rendementProcent'],0),"%"));
    $this->pdf->CellBorders = $volOnder;
   $this->pdf->ln(-3);
    $this->pdf->row($rendementBenchmark);//,$this->formatGetal($data['periode']['rendementProcent'],0),"%",$this->formatGetal($data['ytm']['rendementProcent'],0),"%"));
    $this->pdf->ln(3);

    $this->pdf->row($aandeel);
    $this->pdf->ln(3);
    $this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->CellBorders = array();
	//	$ypos = $this->pdf->GetY()-5;
	//	$this->pdf->SetY($ypos);

		$this->pdf->SetWidths($this->pdf->widthB);
		$this->pdf->SetAligns($this->pdf->alignB);
		$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
    //$this->pdf->fillCell=$fillArray;
   // $this->pdf->SetTextColor(255,255,255);
    $YSamenstelling=$this->pdf->GetY();
		//$this->pdf->row($samenstelling);//,"","","",""));
		//$this->pdf->SetFont($this->pdf->rapport_font,$kopStyle,$this->pdf->rapport_fontsize);
    $this->pdf->fillCell=array();
    $this->pdf->SetTextColor(0,0,0);
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
		$this->pdf->row(array("",vertaalTekst("Beleggingsresultaat",$this->pdf->rapport_taal),"",""));
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);


		$this->pdf->SetWidths($this->pdf->widthA);
		$this->pdf->SetAligns($this->pdf->alignA);
		//$this->pdf->row($ongerealiseerd);//,$this->formatGetal($data['periode']['ongerealiseerdeKoersResultaat'],0),"",$this->formatGetal($data['ytm']['ongerealiseerdeKoersResultaat'],0),""));
    //$this->pdf->row($gerealiseerd);//,$this->formatGetal($data['periode']['gerealiseerdeKoersResultaat'],0),"",$this->formatGetal($data['ytm']['gerealiseerdeKoersResultaat'],0),""));
  //	if(round($data['periode']['koersResulaatValutas'],0) != 0.00 || round($data['ytm']['koersResulaatValutas'],0) != 0.00)
		//  $this->pdf->row($valutaResultaat);//,$this->formatGetal($data['periode']['koersResulaatValutas'],0),"",$this->formatGetal($data['ytm']['koersResulaatValutas'],0),""));
		$this->pdf->row($totaalResultaat);
   if(!in_array('Directe opbrengsten',$opbrengstCategorien))
     $this->pdf->row($rente);//,$this->formatGetal($data['periode']['opgelopenRente'],0),"",$this->formatGetal($data['ytm']['opgelopenRente'],0),""));

		$keys=array();
		foreach ($data['periode']['opbrengstenPerGrootboek'] as $key=>$val)
		  $keys[]=$key;

		foreach ($opbrengstCategorien as $categorie)
		{
		  $tmp=array("",vertaalTekst($categorie,$this->pdf->rapport_taal));
      foreach($perfWaarden as $port=>$waarden)
      {
        $tmp[]=$this->formatGetal($waarden['opbrengstenPerGrootboek'][$categorie],0);
        $tmp[]='';
      }
		  //if(round($data['periode']['opbrengstenPerGrootboek'][$key],0) != 0.00 || round($data['ytm']['opbrengstenPerGrootboek'][$key],0) != 0.00)
			  $this->pdf->row($tmp);//;array(,$this->formatGetal($data['periode']['opbrengstenPerGrootboek'][$key],0),"",$this->formatGetal($data['ytm']['opbrengstenPerGrootboek'][$key],0),""));
      if($categorie=='Directe opbrengsten')
        $this->pdf->row($rente);//,$this->formatGetal($data['periode']['opgelopenRente'],0),"",$this->formatGetal($data['ytm']['opgelopenRente'],0),""));

    }

    $this->pdf->CellBorders = $subBoven;
		$this->pdf->row($totaalOpbrengst);//array("","",$this->formatGetal($data['periode']['totaalOpbrengst'],0),"",$this->formatGetal($data['ytm']['totaalOpbrengst'],0)));
		$this->pdf->ln();
    $this->pdf->CellBorders = array();

		$this->pdf->SetWidths($this->pdf->widthB);
		$this->pdf->SetAligns($this->pdf->alignB);

		$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
		$this->pdf->row(array("",vertaalTekst("Kosten",$this->pdf->rapport_taal),"",""));
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->SetWidths($this->pdf->widthA);
		$this->pdf->SetAligns($this->pdf->alignA);
		foreach ($kostenCategorien as $categorie)
		{
		  
      $tmp=array("",vertaalTekst($categorie,$this->pdf->rapport_taal));
      foreach($perfWaarden as $port=>$waarden)
      {
        $tmp[]=$this->formatGetal($waarden['kostenPerGrootboek'][$categorie],0);
        $tmp[]='';
      }
      //		  if(round($data['periode']['kostenPerGrootboek'][$key],0) != 0.00 || round($data['ytm']['kostenPerGrootboek'][$key],0) != 0.00)
			$this->pdf->row($tmp);//array("",vertaalTekst($key,$this->pdf->rapport_taal),$this->formatGetal($data['periode']['kostenPerGrootboek'][$key],0),"",$this->formatGetal($data['ytm']['kostenPerGrootboek'][$key],0),""));
		}
    $this->pdf->CellBorders = $subBoven;
  	$this->pdf->row($totaalKosten);//$this->formatGetal($data['periode']['totaalKosten'],0),"",$this->formatGetal($data['ytm']['totaalKosten'],0)));

		$posTotaal = $this->pdf->marge + $this->pdf->widthA[0] + $this->pdf->widthA[1] + $this->pdf->widthA[2] + $this->pdf->widthA[3];
    $this->pdf->CellBorders = $volOnder;
     $this->pdf->ln();
    $this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
	  $this->pdf->row($totaal);//"","",$this->formatGetal($data['periode']['totaalOpbrengst']-$data['periode']['totaalKosten'],0),"",$this->formatGetal($data['ytm']['totaalOpbrengst']-$data['ytm']['totaalKosten'],0),''));
    $actueleWaardePortefeuille = 0;
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->CellBorders = array();

 }
 
 function getWaarden($portefeuille,$vanafDatum,$totDatum)
	{
	  global $__appvar;
  	// ***************************** ophalen data voor afdruk ************************ //

  	$waarden=array();
	  if ($this->pdf->rapportageValuta != "EUR" || $this->pdf->rapportageValuta != '')
	  {
	    $koersQuery =	" / (SELECT Koers FROM Valutakoersen WHERE Valuta='".$this->pdf->rapportageValuta."' AND Datum <= Rekeningmutaties.Boekdatum ORDER BY Datum DESC LIMIT 1 ) ";
	    $totRapKoers=getValutaKoers($this->pdf->rapportageValuta,$vanafDatum);
	    $vanRapKoers=getValutaKoers($this->pdf->rapportageValuta,$totDatum);
	  }
	  else
	  {
	    $koersQuery = "";
	    $totRapKoers=1;
	    $vanRapKoers=1;
	  }
    
    if(substr($vanafDatum,5,5)=='01-01')
      $beginJaar=true;
    else
      $beginJaar=false;

    if($this->pdf->lastPOST['doorkijk']==1)
    {
      $waarden=$this->getDoorkijk($portefeuille);

     //listarray($waarden);// $waarden['ongerealiseerd'] $waarden['gerealiseerd']
      $totaalOpbrengst=$waarden['totaalOpbrengst'] + $waarden['opbrengsten'];
      //listarray($totaalOpbrengst);exit;
      $totaalKosten=abs($waarden['kosten']);

    }
    else
    {
      $totaalKosten=0;
    $fondsen=berekenPortefeuilleWaarde($portefeuille,$vanafDatum,$beginJaar,$this->pdf->rapportageValuta,$vanafDatum);
    $totaal=array();
    $totaalWaardeVanaf['totaal']=0;
    foreach($fondsen as $id=>$regel)
    {
      $totaalWaardeVanaf['totaal']+=($regel['actuelePortefeuilleWaardeEuro']/$totRapKoers);
      if($regel['type']=='rente')
      {
        $totaalB['totaal']+=($regel['actuelePortefeuilleWaardeEuro']/$totRapKoers);
      } 
    }
 
    $totaalWaarde['totaal']=0;
    $fondsen=berekenPortefeuilleWaarde($portefeuille,$totDatum,false,$this->pdf->rapportageValuta,$vanafDatum);
    $totaal=array();
    foreach($fondsen as $id=>$regel)
    {
      $totaalWaarde['totaal']+=($regel['actuelePortefeuilleWaardeEuro']/$totRapKoers);
      if($regel['type']=='rente')
      {
        $totaalA['totaal']+=($regel['actuelePortefeuilleWaardeEuro']/$totRapKoers);
      }
      if($regel['type']=='fondsen')
      {
        $totaal['totaalB']+=($regel['actuelePortefeuilleWaardeEuro']/$totRapKoers);
        $totaal['totaalA']+=($regel['beginPortefeuilleWaardeEuro']/$totRapKoers);
      }
    }

    $ongerealiseerdeKoersResultaat = $totaal['totaalB'] - $totaal['totaalA'];
    $waarden['ongerealiseerdeKoersResultaat']=$ongerealiseerdeKoersResultaat;


    $DB=new DB();

		$waardeEind				  = $totaalWaarde['totaal'];
		$waardeBegin 			 	= $totaalWaardeVanaf['totaal'];
		$waardeMutatie 	   	= $waardeEind - $waardeBegin;
		$stortingen 			 	= getStortingen($portefeuille,$vanafDatum,$totDatum,$this->pdf->rapportageValuta);
		$onttrekkingen 		 	= getOnttrekkingen($portefeuille,$vanafDatum,$totDatum,$this->pdf->rapportageValuta);
		$resultaatVerslagperiode = $waardeMutatie - $stortingen + $onttrekkingen;
		$rendementProcent  	=  performanceMeting($portefeuille, $vanafDatum, $totDatum, $this->pdf->portefeuilledata['PerformanceBerekening'],$this->pdf->rapportageValuta);





  //  $this->werkelijkeBenchmarkVerdeling
   // echo $vanafDatum,$totDatum;
   // listarray($this->perioden);
      $benchmarkRendement = $this->getBenchmarkRendement($portefeuille);

    unset($tmp);
		$waarden['waardeEind']=$waardeEind;
		$waarden['waardeBegin']=$waardeBegin;
		$waarden['waardeMutatie']=$waardeMutatie;
		$waarden['stortingen']=$stortingen;
		$waarden['onttrekkingen']=$onttrekkingen;
		$waarden['resultaatVerslagperiode']=$resultaatVerslagperiode;
		$waarden['rendementProcent']=$rendementProcent;
    $waarden['rendementBenchmark']=$benchmarkRendement;

    $RapJaar = date("Y", db2jul($totDatum));
    $RapStartJaar = date("Y", db2jul($vanafDatum));
		$totaalOpbrengst += $ongerealiseerdeKoersResultaat;
		$gerealiseerdeKoersResultaat = gerealiseerdKoersresultaat($portefeuille, $vanafDatum, $totDatum,$this->pdf->rapportageValuta,true);
		$totaalOpbrengst += $gerealiseerdeKoersResultaat;
		$waarden['gerealiseerdeKoersResultaat']=$gerealiseerdeKoersResultaat;

		$opgelopenRente = ($totaalA['totaal'] - $totaalB['totaal']) / $totRapKoers;
		$totaalOpbrengst += $opgelopenRente;
		$waarden['opgelopenRente']=$opgelopenRente;

    }

    $query = "SELECT DISTINCT(Grootboekrekeningen.Grootboekrekening), Grootboekrekeningen.Omschrijving FROM Grootboekrekeningen WHERE Grootboekrekeningen.Opbrengst = '1' ORDER BY Grootboekrekeningen.Afdrukvolgorde";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
    $opbrengstenPerGrootboek=array();
		while($gb = $DB->nextRecord())
		{
			$query = "SELECT Rekeningmutaties.Grootboekrekening, ".
		  	"SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers $koersQuery) AS totaalcredit, ".
		  	"SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers $koersQuery) AS totaaldebet ".
		  	"FROM Rekeningmutaties, Rekeningen, Portefeuilles ".
		  	"WHERE ".
		  	"Rekeningmutaties.Rekening = Rekeningen.Rekening AND ".
		  	"Rekeningen.Portefeuille = '".$portefeuille."' AND ".
		  	"Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
		  	"Rekeningmutaties.Verwerkt = '1' AND ".
		  	"Rekeningmutaties.Boekdatum > '".$vanafDatum."' AND ".
		  	"Rekeningmutaties.Boekdatum <= '".$totDatum."' AND ".
			  "Rekeningmutaties.Grootboekrekening = '".$gb['Grootboekrekening']."' GROUP BY Rekeningmutaties.Grootboekrekening";

			$DB2 = new DB();
			$DB2->SQL($query);
			$DB2->Query();

   //   $directeOpbrengsten=array('DIV','DIVB','RENOB','RENTE','DIVBE','ROER','RENME');
    	while($opbrengst = $DB2->nextRecord())
			{
			//  if(in_array($gb['Grootboekrekening'],$directeOpbrengsten))
          $opbrengstenPerGrootboek['Directe opbrengsten'] +=  ($opbrengst['totaalcredit']-$opbrengst['totaaldebet']);
       // else
			//	  $opbrengstenPerGrootboek['Indirecte opbrengsten'] +=  ($opbrengst['totaalcredit']-$opbrengst['totaaldebet']);
				$totaalOpbrengst += ($opbrengst['totaalcredit'] - $opbrengst['totaaldebet']);
			}
		}


    if($this->pdf->lastPOST['doorkijk']==1)
    {
     // $directeOpbrengsten=array('DIV','DIVB','RENOB','RENTE','DIVBE','ROER','RENME');
      $opbrengstenPerGrootboek=$waarden['opbrengstenPerGrootboek'];
      $waarden['opbrengstenPerGrootboek']=array();
      foreach($opbrengstenPerGrootboek as $grootboek=>$waarde)
      {
       // if(in_array($grootboek,$directeOpbrengsten))
          $waarden['opbrengstenPerGrootboek']['Directe opbrengsten'] +=  $waarde;
       // else
       //   $waarden['opbrengstenPerGrootboek']['Indirecte opbrengsten'] += $waarde;
      }
    }
    else
    {
      $waarden['opbrengstenPerGrootboek']=$opbrengstenPerGrootboek;
    }
		$waarden['totaalOpbrengst']=$totaalOpbrengst;


	  $query = "SELECT Grootboekrekeningen.Omschrijving,Grootboekrekeningen.Grootboekrekening, ".
		  "SUM(Rekeningmutaties.Credit * Rekeningmutaties.Valutakoers $koersQuery) AS totaalcredit, ".
		  "SUM(Rekeningmutaties.Debet * Rekeningmutaties.Valutakoers $koersQuery) AS totaaldebet ".
		  "FROM Rekeningmutaties, Rekeningen, Portefeuilles, Grootboekrekeningen ".
		  "WHERE ".
		  "Rekeningmutaties.Rekening = Rekeningen.Rekening AND ".
		  "Rekeningen.Portefeuille = '".$portefeuille."' AND ".
		  "Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND ".
		  "Rekeningmutaties.Verwerkt = '1' AND ".
		  "Rekeningmutaties.Boekdatum > '".$vanafDatum."' AND ".
		  "Rekeningmutaties.Boekdatum <= '".$totDatum."' AND ".
		  "Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.GrootboekRekening AND ".
		  "Grootboekrekeningen.Kosten = '1' ".
		  "GROUP BY Rekeningmutaties.Grootboekrekening ".
		  "ORDER BY Grootboekrekeningen.Afdrukvolgorde ";

		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();

		$kostenPerGrootboek = array();
		while($kosten = $DB->nextRecord())
		{
		 // $kosten['Grootboekrekening']='kosten';
			$kostenPerGrootboek[$kosten['Grootboekrekening']]['Omschrijving'] = $kosten['Omschrijving'] ;//'Kosten';
  		$kostenPerGrootboek[$kosten['Grootboekrekening']]['Bedrag'] += ($kosten['totaaldebet'] - $kosten['totaalcredit']);
      if($this->pdf->lastPOST['doorkijk']<>1)
			  $totaalKosten += ($kosten['totaaldebet'] - $kosten['totaalcredit']);
   //   echo $totaalKosten."<br>\n";
		}

    

//listarray($waarden);
    if($this->pdf->lastPOST['doorkijk']==1)
    {

      $query = "SELECT DISTINCT(Grootboekrekeningen.Grootboekrekening) as Grootboekrekening, Grootboekrekeningen.Omschrijving FROM Grootboekrekeningen WHERE Grootboekrekeningen.kosten = '1' ORDER BY Grootboekrekeningen.Afdrukvolgorde";
      $DB = new DB();
      $DB->SQL($query);
      $DB->Query();
      $gbOmschrijvingen=array();
      while($gb = $DB->nextRecord())
      {

        $gbOmschrijvingen[$gb['Grootboekrekening']]=$gb['Omschrijving'];
      }
      $tmp=$waarden['kostenPerGrootboek'];
      $waarden['kostenPerGrootboek']=array();
      foreach($tmp as $gb=>$waarde)
      {
        $waarden['kostenPerGrootboek'][$gbOmschrijvingen[$gb]]=$waarde;
      }


    }
    else
    {
      $tmp=array();
      foreach ($kostenPerGrootboek as $data)
      {
        $tmp[$data['Omschrijving']]=$data['Bedrag'];
      }
      $waarden['kostenPerGrootboek']=$tmp;
    }

		$waarden['totaalKosten']=$totaalKosten;

		$kostenProcent = ($totaalKosten / $waardeEind) * 100;
		$koersResulaatValutas = $waarden['resultaatVerslagperiode'] - ($totaalOpbrengst  - $waarden['totaalKosten']);
   // echo "$koersResulaatValutas = ".$waarden['resultaatVerslagperiode']." - ($totaalOpbrengst  +  ".$waarden['totaalKosten'].");";exit;
		$totaalOpbrengst += $koersResulaatValutas;
		$waarden['kostenProcent']=$kostenProcent;
		$waarden['koersResulaatValutas']=$koersResulaatValutas;
		$waarden['totaalOpbrengst']=$totaalOpbrengst;

    if($this->pdf->lastPOST['doorkijk']==1)
    {
      $waarden['ongerealiseerdeKoersResultaat']=$waarden['ongerealiseerd']+$waarden['rente']-$waarden['opgelopenRente'];
      $waarden['gerealiseerdeKoersResultaat']=$waarden['gerealiseerd'];
      $waarden['koersResulaatValutas']=0;
    }
//listarray($waarden);
		return $waarden;
	}

  function getBenchmarkRendement($portefeuille)
  {
    $DB=new DB();


/*
    $jaar=date("Y",db2jul($this->rapportageDatum));
    if(db2jul("$jaar-01-01")<db2jul($this->pdf->PortefeuilleStartdatum))
      $beginJaar=$this->pdf->PortefeuilleStartdatum;
    else
      $beginJaar="$jaar-01-01";
*/
    $beginJaar=$this->rapportageDatumVanaf;

    $query="SELECT date(Startdatum) as Startdatum , SpecifiekeIndex FROM Portefeuilles WHERE Portefeuille='$portefeuille'";
    $DB->SQL($query);
    $start=$DB->lookupRecord();
    if(db2jul($start['Startdatum'])>db2jul($beginJaar))
    {
      $beginJaar = $start['Startdatum'];
//      $indexberekening=new indexHerberekening();
      $perioden=$this->indexberekening->getMaanden(db2jul($beginJaar),db2jul($this->rapportageDatum));

    }
    else
      $perioden=$this->perioden;
    //listarray($beginJaar);ob_flush();


    if($portefeuille==$this->portefeuille)
    {
      $benchmarkRendement=0;

      if(1)//count($this->pdf->portefeuilles) > 1)
      {
        $stdev = new rapportSDberekening($this->portefeuille, $this->rapportageDatum, 1);
        $stdev->settings['SdFrequentie'] = 'm';
        $stdev->setStartdatum($beginJaar);
        $stdev->settings['gebruikHistorischePortefeuilleIndex'] = false;
        if (count($this->pdf->portefeuilles) > 1)
        {
          $stdev->consolidatiePortefeuilles = $this->pdf->portefeuilles;
        }
        $stdev->addReeks('benchmarkTot', $start['SpecifiekeIndex'], true);//$this->index['SpecifiekeIndex']
        //  $stdev->berekenWaarden();
        $benchmarkRendement = $stdev->getReeksRendement('benchmarkTot');

       // echo $benchmarkRendement."<br>\n"; ob_flush();
      }
    }
    else
    {
      //$benchmarkRendement = getFondsPerformance($pRec['SpecifiekeIndex'], $vanafDatum, $totDatum);
      $benchmarkRendement=0;
     // $perioden=$this->indexberekening->getMaanden(db2jul($beginJaar),db2jul($this->rapportageDatum));
//listarray($perioden);
      foreach($perioden as $periode)
      {
        $pRec['SpecifiekeIndex']=getSpecifiekeIndex($portefeuille,$periode['stop']);
        if($pRec['SpecifiekeIndex']=='')
        {
          $query = "SELECT GeconsolideerdePortefeuilles.SpecifiekeIndex
    FROM GeconsolideerdePortefeuilles
    WHERE GeconsolideerdePortefeuilles.VirtuelePortefeuille = '$portefeuille'";
          $DB->SQL($query);
          $pRec = $DB->lookupRecord();
        }
        if($pRec['SpecifiekeIndex']=='')
        {
          $query="SELECT Portefeuille,Startdatum,PerformanceBerekening,ZpMethode, TijdelijkUitsluitenZp,Portefeuilles.Vermogensbeheerder,
    Vermogensbeheerders.check_module_SCENARIO, Portefeuilles.ModelPortefeuille, Portefeuilles.SpecifiekeIndex
    FROM Portefeuilles
    JOIN Vermogensbeheerders ON Vermogensbeheerders.Vermogensbeheerder=Portefeuilles.Vermogensbeheerder
    WHERE Portefeuille = '$portefeuille'";
          $DB->SQL($query);
          $pRec=$DB->lookupRecord();
        }

        $indexVerdeling=getFondsverdeling($pRec['SpecifiekeIndex']);
        //listarray($indexVerdeling);
        $perf = getFondsPerformance($indexVerdeling, $periode['start'], $periode['stop']);
        $tmp=1+($perf/100);
        $benchmarkRendement=(((1+($benchmarkRendement/100))*$tmp)-1)*100;
      //  echo "$portefeuille ".$pRec['SpecifiekeIndex']." ".$periode['start']." -> ".$periode['stop']." $perf <br>\n";// -> $benchmarkRendement <br>\n
      }
    }

    return $benchmarkRendement;
  }
  
  
   function toonZorgplicht()
  {
global $__appvar;
    $DB=new DB();
    
    		// haal totaalwaarde op om % te berekenen
		$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
						 "FROM TijdelijkeRapportage WHERE ".
						 " rapportageDatum ='".$this->rapportageDatum."' AND ".
						 " portefeuille = '".$this->portefeuille."' "
						 .$__appvar['TijdelijkeRapportageMaakUniek'];
		debugSpecial($query,__FILE__,__LINE__);
		$DB->SQL($query);
		$DB->Query();
		$totaalWaarde = $DB->nextRecord();
    if($totaalWaarde['totaal'] <> 0)
    {
        $query="SELECT SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) / ".$totaalWaarde['totaal']." as percentage,
TijdelijkeRapportage.beleggingscategorieOmschrijving,
TijdelijkeRapportage.beleggingscategorie,
ZorgplichtPerBeleggingscategorie.Zorgplicht
FROM TijdelijkeRapportage
LEFT JOIN ZorgplichtPerBeleggingscategorie ON TijdelijkeRapportage.beleggingscategorie = ZorgplichtPerBeleggingscategorie.Beleggingscategorie AND ZorgplichtPerBeleggingscategorie.Vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."'
WHERE TijdelijkeRapportage.Portefeuille =  '".$this->portefeuille."' AND
 TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."'
GROUP BY ZorgplichtPerBeleggingscategorie.Zorgplicht 
ORDER BY TijdelijkeRapportage.beleggingscategorieVolgorde";
    $DB->SQL($query); //echo $query;exit;
    $DB->Query();
		while($data= $DB->nextRecord())
		{
		  $categorieWaarden[$data['Zorgplicht']]=$data['percentage']*100;
      $categorieOmschrijving[$data['Zorgplicht']]=$data['beleggingscategorieOmschrijving'];
		}
    }
    $zorgplicht = new Zorgplichtcontrole();
  	$zpwaarde=$zorgplicht->zorgplichtMeting($this->pdf->portefeuilledata,$this->rapportageDatum);

    $tmp=array();
    foreach ($zpwaarde['conclusie'] as $index=>$regelData)
      $tmp[$regelData[0]]=$regelData;

    krsort($tmp);

//listarray($zpwaarde['conclusie']);
    //listarray($tmp);exit;

    $this->pdf->SetAligns(array('L','L','R','R','R','R'));
    $this->pdf->SetY(150);
    $beginY=$this->pdf->getY();
    $extraX=155;
   	
  	$this->pdf->SetWidths(array($extraX,40,16,16,16,16,16));
  	$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->row(array('',
    vertaalTekst("beleggingscategorie",$this->pdf->rapport_taal),
    vertaalTekst("minimaal",$this->pdf->rapport_taal),
    vertaalTekst("norm",$this->pdf->rapport_taal),
    vertaalTekst("maximaal",$this->pdf->rapport_taal),
    vertaalTekst("werkelijk",$this->pdf->rapport_taal),
    vertaalTekst("conclusie",$this->pdf->rapport_taal)));
    	$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetAligns(array('L','L','R','R','R','R'));
  	//foreach ($tmp as $index=>$regelData)
    
    /*
    - zorgplichtcategorie
- minimum
- neutraal (is norm)
- maximum
- werkelijk
*/
  //  $this->pdf->MemImage($this->checkImg,100,$this->pdf->getY(),10,10);
    foreach ($categorieWaarden as $cat=>$percentage)
    {
      if($tmp[$cat][2])
        $risicogewogen=$tmp[$cat][2]."%";
      else
        $risicogewogen=''; 
      //if($zpwaarde['categorien'][$cat]['Minimum'])   
        $min=$this->formatGetal($zpwaarde['categorien'][$cat]['Minimum'],0)."%";
      //else
     //   $min='';   
      //if($zpwaarde['categorien'][$cat]['Maximum'])  
        $max=$this->formatGetal($zpwaarde['categorien'][$cat]['Maximum'],0)."%";
     // else
     //   $max='';  
        $norm=$this->formatGetal($zpwaarde['categorien'][$cat]['Norm'],0)."%";
        
      if($tmp[$cat][5]=='Voldoet')
        $this->pdf->MemImage($this->checkImg,120+$extraX,$this->pdf->getY(),3.9,3.9);
      else
        $this->pdf->MemImage($this->deleteImg,120+$extraX,$this->pdf->getY(),3.9,3.9);  
        
      
  	  $this->pdf->row(array('',vertaalTekst($categorieOmschrijving[$cat],$this->pdf->rapport_taal),$min,$norm,$max,$this->formatGetal($categorieWaarden[$cat],1)."%"));//$risicogewogen
    }
    $this->pdf->Rect($this->pdf->marge+$extraX,$beginY,120,count($categorieWaarden)*4+4);
  }
  
  
  function bepaaldFondsWaardenVerdiept($portefeuille,$einddatum)
  {

    if(substr($einddatum,5,5)=='01-01')
      $startJaar=true;
    else
      $startJaar=false;
    /*
    $gegevens=berekenPortefeuilleWaarde($portefeuille,$einddatum,$startjaar,'EUR',substr($einddatum,0,4).'-01-01');
    listarray($gegevens);
    return $gegevens;
    */
    vulTijdelijkeTabel(berekenPortefeuilleWaarde($portefeuille,$einddatum), $portefeuille, $einddatum);

    $this->verdiept = new portefeuilleVerdiept($this->pdf,$portefeuille,$einddatum);
    $verdiepteFondsen = $this->verdiept->getFondsen();
   // listarray($portefeuille);listarray($verdiepteFondsen);ob_flush();
    foreach ($verdiepteFondsen as $fonds)
      $this->verdiept->bepaalVerdeling($fonds,$this->verdiept->FondsPortefeuilleData[$fonds],array('fonds'),$einddatum);
    
    
    $fondswaarden =  berekenPortefeuilleWaarde($portefeuille,$einddatum,$startJaar,'EUR',substr($einddatum,0,4).'-01-01');
    $correctieVelden=array('totaalAantal','ActuelePortefeuilleWaardeEuro','actuelePortefeuilleWaardeInValuta','beginPortefeuilleWaardeEuro','beginPortefeuilleWaardeInValuta');
    foreach($fondswaarden as $i=>$fondsData)
    {
      //
      if(isset($this->pdf->fondsPortefeuille[$fondsData['fonds']]))
      {
        //echo $fondsData['fonds'];ob_flush();exit;
        $fondsWaardeEigen=$fondsData['actuelePortefeuilleWaardeEuro'];
        $fondsWaardeHuis=$this->pdf->fondsPortefeuille[$fondsData['fonds']]['totaalWaarde'];
        $aandeel=$fondsWaardeEigen/$fondsWaardeHuis;
        //echo $fondsData['fonds'].	" $aandeel=$fondsWaardeEigen/$fondsWaardeHuis ";exit;
        unset($fondswaarden[$i]);
        foreach($this->pdf->fondsPortefeuille[$fondsData['fonds']]['verdeling'] as $type=>$details)
        {
          foreach ($details as $element => $emementDetail)
          {
            
            if(isset($emementDetail['overige']))
            {
              foreach($correctieVelden as $veld)
                $emementDetail['overige'][$veld]=$emementDetail['overige'][$veld]*$aandeel;
              unset($emementDetail['overige']['WaardeEuro']);
              unset($emementDetail['overige']['koersLeeftijd']);
              unset($emementDetail['overige']['FondsOmschrijving']);
              unset($emementDetail['overige']['Fonds']);
              $fondswaarden[] = $emementDetail['overige'];
            }
          }
        }
      }
    }
    $fondswaarden  = array_values($fondswaarden);
    $tmp=array();
    $conversies=array('ActuelePortefeuilleWaardeEuro'=>'actuelePortefeuilleWaardeEuro');
    foreach($fondswaarden as $mixedInstrument)
    {
      $instrument=array();
      foreach($mixedInstrument as $index=>$value)
      {
        if(isset($conversies[$index]))
          $instrument[$conversies[$index]] = $value;
        else
          $instrument[$index] = $value;
      }
      unset($instrument['voorgaandejarenactief']);
      
      $key='|'.$instrument['type'].'|'.$instrument['fonds'].'|'.$instrument['rekening'].'|';
      if(isset($tmp[$key]))
      {
        foreach($correctieVelden as $veld)
        {
          $veld=($veld);
          $tmp[$key][$veld] += $instrument[$veld];
        }
      }
      else
        $tmp[$key]=$instrument;
      //	listarray($instrument);
    }
    $fondswaarden  = array_values($tmp);
    //echo $portefeuille,$einddatum;listarray($fondswaarden);
    return $fondswaarden;
  }

  function getBenchmarkViaCategorie($portefeuille,$eindDatum)
  {
    //$eindJul=adodb_db2jul($eindDatum);
    $db=new DB();


    $query="SELECT IndexPerBeleggingscategorie.Beleggingscategorie,IndexPerBeleggingscategorie.Fonds FROM IndexPerBeleggingscategorie 
JOIN Portefeuilles ON IndexPerBeleggingscategorie.Vermogensbeheerder=Portefeuilles.Vermogensbeheerder AND Portefeuilles.Portefeuille='$portefeuille'
      WHERE (IndexPerBeleggingscategorie.Portefeuille='$portefeuille' or IndexPerBeleggingscategorie.Portefeuille='')
      ORDER BY IndexPerBeleggingscategorie.Portefeuille";
    $db->SQL($query);
    $db->Query();
    while($index=$db->nextRecord())
      $indexLookup[$index['Beleggingscategorie']]=$index['Fonds'];


    $verdeling=array();
    $verdelingBenchmark=array();
    $totaalWaarde=0;
    foreach($this->bepaaldFondsWaardenVerdiept($portefeuille,$eindDatum) as $index=>$fonds)
    {
      $verdeling[$fonds['hoofdcategorie']]+=$fonds['actuelePortefeuilleWaardeEuro'];
      $totaalWaarde+=$fonds['actuelePortefeuilleWaardeEuro'];

    }
    foreach($verdeling as $categorie=>$waardeEur)
    {
      $verdelingBenchmark[$indexLookup[$categorie]] += $waardeEur/$totaalWaarde*100;
    }
    return $verdelingBenchmark;
   // exit;
  }


}

?>