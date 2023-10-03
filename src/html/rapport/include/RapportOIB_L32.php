<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2019/11/09 16:39:39 $
File Versie					: $Revision: 1.10 $

$Log: RapportOIB_L32.php,v $
Revision 1.10  2019/11/09 16:39:39  rvv
*** empty log message ***

Revision 1.9  2018/08/18 12:40:14  rvv
php 5.6 & consolidatie

Revision 1.8  2018/07/07 17:35:19  rvv
*** empty log message ***

Revision 1.7  2017/10/25 15:59:31  rvv
*** empty log message ***

Revision 1.6  2017/07/05 16:06:40  rvv
*** empty log message ***

Revision 1.5  2017/06/18 09:18:24  rvv
*** empty log message ***

Revision 1.4  2017/06/11 09:46:12  rvv
*** empty log message ***

Revision 1.3  2017/06/10 18:09:58  rvv
*** empty log message ***

Revision 1.2  2017/05/25 14:35:58  rvv
*** empty log message ***

Revision 1.1  2017/05/20 18:16:29  rvv
*** empty log message ***

Revision 1.6  2017/01/07 16:23:16  rvv
*** empty log message ***

Revision 1.5  2015/07/15 14:10:22  rvv
*** empty log message ***

Revision 1.4  2013/07/04 15:40:04  rvv
*** empty log message ***

Revision 1.3  2013/06/15 15:55:18  rvv
*** empty log message ***

Revision 1.2  2013/06/12 18:46:36  rvv
*** empty log message ***

Revision 1.1  2013/05/26 13:54:49  rvv
*** empty log message ***



*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once($__appvar["basedir"]."/html/rapport/Zorgplichtcontrole.php");
class RapportOIB_L32
{
	function RapportOIB_L32($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;
		$this->pdf->rapport_type = "OIB";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
		if($this->pdf->rapport_OIB_titel)
			$this->pdf->rapport_titel = $this->pdf->rapport_OIB_titel;
		else
			$this->pdf->rapport_titel = "Onderverdeling in beleggingscategorie";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
		$this->pdf->pieData = array();
    $this->checkImg=base64_decode('iVBORw0KGgoAAAANSUhEUgAAADAAAAAwCAMAAABg3Am1AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoV2luZG93cykiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6OTdDQTYzMjZDNTNGMTFFMkJGM0ZFNzdBN0M4NjZENEIiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6OTdDQTYzMjdDNTNGMTFFMkJGM0ZFNzdBN0M4NjZENEIiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDo5N0NBNjMyNEM1M0YxMUUyQkYzRkU3N0E3Qzg2NkQ0QiIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDo5N0NBNjMyNUM1M0YxMUUyQkYzRkU3N0E3Qzg2NkQ0QiIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PqN37VQAAAMAUExURQAAAP///wCQAACMAACLAACIAACHAACEAACDAACAAAB+AAB6AAB5AAB2AAB0AABzAABxAABvAABtAABoAABlAABfAABdAABbAABZAABWAABUAABRAABNAAAoAAAnAAAfAAASAAaRAgaOAgaKAwaJAweHAwiOBAeMBAeLBAyLBQqHBROSCRaYCxWWCxOZBxSSCRiXChiZCxeXCxqZDBmYDBubDRybDB+dDh2dDiCgDx6fDySsESKhECWtEiWrEiOiESKkDSKiDSGiDSGhDSOlDiKjDiKiDyKhDyasESaqESWqESatEiirEiarEiWkESeuEyiuEyesEyisEyWjEimvFCmtFCqtFCelEyywFSuwFSimFCuvFiqnFS2wF3HKYnnLa3vMbSuvEiutEyuuFCytFC+xFS6vFSyuFSyqFSypFS2vFi6vFi6rFjCxFy6wFzGxGDKxGC+wGDOyGTSxHTezHj63JT22JV3BSWDETF7BTGPET2TGUWPEUGXFU3bLZ3rMaXnMaXnLaHrMa4PPdYXQdonSe4vTfYzTfovTfozUf4/UgY/VgpDVgi6rFC+sFTazGTSyGTWzGjazGji0Gze0GzizGzm1HDm2HTazHDqyHjy3IUq8L17DSF/ESWPGTWPGTmLETWTGT47Ufzq1HDu2HTy2HT23Hj63HkC4Hz+4H0G5IEK4IkO7HUG3H0K5IEa7IUS6IUO6IUW7Ik2/KlPAMVbDNWHEQmnITG/KUofTcHDMT3DMUYDRZoXTaYnVb6LejqLdjaLdj6PdkHrQWQAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAbA7uAAAAKNSURBVHjaYmAkETCMasANuLj5SNLAZe6nyUOCBi732XNmmXETrYHLeW5PT0upMS+RGric5/U0lSUW52rJEKWB021BT11qdnZ6tqkwMRo4HaHqo2P1RYnQwOlY0dOQm52VHh1hzCdPWAOX00KI+hSgern/BDUA1Xc3pmanp4dFQtQT0MDlNL+rMS07PRmuHr8GiPosoPpkEz7Z/4iYZmJhYcXmX9f5XfW5WelhYSEmvFD1YA0sejNXK/Jgmu9Qtag+LyU9LASoXu4/IrUy9x0+sH+bEi+6escFixrygM4JQ1YP1MDSt/fQni2b16nxo7l/QSdQfRiaekYGVr1dB3esWb9h01p1flT17Qmx6WGhQajqGRnYevftXLNpw8Z1G5drCCCpr25vyksGqg9FVQ/U4Ld7+8YN69atWLlyqTJMB6dTdUd9YVhYUFCAAT+KeqAfWHO2bly3asXK5UuXTVURhIZnFVB9UliQf4ABH6p6UCixZqzfuGL58qVLFk+fpCoIVd8UGxoKVi/7H6MQEGPP3Lh8GVD95ClTJqgK8XE51HQkFALV+2BRD444cfaYVUsXT5s8ZdKEibnami61bUD1QYG+/kbo7oElDQmOqGXTJ0+aNKGooCR1RlxbfGEALvXQxCfBGb5k0kSg+vyJ5a3NlWFA9T6BOtjUw1KrBGfwFKD63Nzs5OSAgKAgHzsPHUz3IydvCU77/oLcvGxQ2gQ6x9Ybh3pEfpDgsikoBGUtkHc9capHykCS3Ja56aDE4OPrZYtTPXKOk+S2igUmBl87L1tdrP7FKFuluC3C/IHq7XT5cZmPlqeleEwDPKytdfCoRy8E+LR0DLXwqccoNaRFRGQU/o/WooNeA0CAAQAWlObj4AgonwAAAABJRU5ErkJggg==');
	  $this->deleteImg=base64_decode('iVBORw0KGgoAAAANSUhEUgAAADAAAAAwCAMAAABg3Am1AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAA2ZpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtcE1NOk9yaWdpbmFsRG9jdW1lbnRJRD0ieG1wLmRpZDpDOTFCRkQ2ODM5QzVFMjExOTZBRThFRDMyQTE4OUYwRSIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpFOEY4RkE4NUM1M0YxMUUyODAxM0VGRkE5NEM4QUM5NCIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpFOEY4RkE4NEM1M0YxMUUyODAxM0VGRkE5NEM4QUM5NCIgeG1wOkNyZWF0b3JUb29sPSJBZG9iZSBQaG90b3Nob3AgQ1M2IChXaW5kb3dzKSI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkNBMUJGRDY4MzlDNUUyMTE5NkFFOEVEMzJBMTg5RjBFIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkM5MUJGRDY4MzlDNUUyMTE5NkFFOEVEMzJBMTg5RjBFIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+Si4argAAAwBQTFRFAAAA/////7CA/61//55w/4RS/49h/5Vo/206/3E//3JB/3VC/3ZF/3VF/3hG/3lJ/3pJ/3pK/3lK/3xL/35M/31O/n5R/4BS/4NU/4RW/oZZ/4ha/14p/18u/2Iv/2Av/2Ew/2Ix/2My/2Uz/WMz/2Q0/2g3/2c3/2o5/2g5/2k6/2s7/2o8/2w9/24+/20+/29A+mw//3BB+m1A/3FC/3ND/3NE/3VG/3ZH/3dI+nNH/3pM8jwL9j8N90IR+EMS90MS9UIS+EQT+kYU+EUU90QU+0kV+UYV+kcW+kgX+UgX+UcX90gX60MW4kEV+0kY9kkY8UYX/00Z/UsZ/EkZ+0sZ+EkZ/0wa/Usa80cZ7kYZyzwV/08b/kwb/U0b+0sb+kwb+Esb90sb9kka8Uka/k0c+U0c/k8d/U0d/E4d+04d+k0d9Eoc80wc80oc7kkb/08e+E4e9kwd/1Af+08f/1Eg/FAg+1Eg/1Uh/1Ih/1Mi/lQi/1Qj/lYj/1Uk/1gl/1Yl/1cm/1gn/1ko/1op/1kq/1oq/1wr/10s/14t+lks910y/2M0/WY49WA29GQ792xC2DAHvSoG0DAI6jcK4TUJ8DkL5jYK8jsM7ToM9DwN8z0N8TwN9D0O8z0O8DwO9T8P9UAQ8z8Q9kER+EMU6jwT+kUVwzYSvTMR+EUYwzgT0z0V+0ka+Uka3kAX5UQZ3UEY6kUa71Qu73JStSYG4TIM3S8M5zYP0zQQty4PsyoPty4R6ksp61o56WZI3zkd1SYKyyEKviUMsCAL2TIbqxUEsBsJ1S4b2Tkk3T4rxhcJ0CUV1CoZogwBrA4EyRsRoAcBsAoDwgoFuwsFxBAIvg8J0AAAygAAwgAAtwAAtAAAsgAAsAAArwAArQAAqwAAqQAApwAApQAAowMAowAAoAAAngIAngAAnQIAnAAAmAAAkAAAiwAAgwAAeQAAcgAAaAAAWgAARwAALAAAJAAAGAAACwAABQAAAQAAvQEBsAIBrQMBqwQBpwIBpwUCvgYEaHny+gAABK1JREFUeNpiYCQRMNBCw008Gq7fuIGp/tSlezg0XL9xQurkczT1N64oyyy9cvs+poYb5zezsLCJ3viIquG/inxdaenKAzfvomm4cZKZhZ1NSnTPXVQHdSu11RSnhgd5HryPouHaCZB6aQlrwVPvkNTf2qQm21SZHhsa6XfoFYqG6/tY2KWlJcVFhfqQQuXWHg65trri1LWBfjvvfkF10mmg+VIS4mL8fGdewcRun+BU6misLIiP9F1+8QMDqoafbGzSkhJiooLmej+h/r51nFtdtqWmNCM5avWxl9/RPP1zL1CDuJiotbnpHkgY3jrNp6XY3lhVnJaybuedLxjBep5NCmiBtbWlKef51yD15/l0VTub66oyczauuvjhB4aGn2LS4mLW1kLmJga9Nz8y3j4voK8u39ZYW5yEcBCKhtsnpcVFrYUsLUwMOPbcf3pewEBdsb25oSo7ZcPOe1+wJY3/QA1CluZmXPo6HEfOCxtpKna0NDWUgR3EgE3D3b3iQAeZmRjoa2vomRppKXW2NddXF+VHH3v9HXviO89qbclrYmSgq62hoaOpKt8GtKAwZcOuB19xpNbbEwTNzYxAFqipqyvLtwMtKM9dv+L3JwYcGh6eETEHO0hdVVVRvrOtpbm6aGP0uXcMODPQLWMzIz2weiUFoPqWhsL81Yeff8Od4x4c5zHQhaiXbW9pbirP27D7/hcGPFn0jqGRrhpEPSiECvOj/31kwFMI3Lu1l1tbXbUHpL6lqa4yLffg0294So3bV60EDDRUFSHqG2pKsxKSTr3GreHOCVEeTi1Q+EDVp8dFhCaceoVDw+3L/cKmQA8oyHa2t7QC1VcWADUEe4YefIlVw+0TgrwGGupA4zuA4V9fV1NakJ4aEbHG09vtwEtMDXeu9vMbAL0rL9vRBlTfWFcFUR/i6bHMxWXb03doGm6fEOACKleQ7QC6vrmpoRah3t3J3sFh+983KBpu93PrApUD/doGdH1jbWVJVmZMHES9i5PD4oV28y++RtZwp1ddCaS8BWh6Y31NWVFOTnx8RIg/WL293cIFc2fbwgILrOHmHiVgSmhpBoZlXU1lSU7++qjE4GCIegew+lk2NgceI9mwX64daHZDXV1NdWl2zsbVhx4tDfXygJs/b9aMaVOmnvsE1/DgvFxLY11NTVVlaXpa3sbow08//FnqCVQOMX8uUP30qZOef4VpuHl3vyww4ZSWFhdkJKWsjz729AvDh4dL3Zc42C2Gqp8ydfLRN7CS7+7VLrmWusqCgvT0+OQov1VHX30Fir57uNTVAa5++sQtLz7D/PDktExdVXF6ampsUmTUuhXnXn8DG/Xu0XZHoPpZc6bNBKqfdAFacIDIXxUF6bGxoaEBkb6rd/57Bysh3j/ZvmjenDlA5UD1Z99+RwTro+0x4aFhAYG+ftGHH378AU8F759unTdn5vSpEycj1ENC6XKMv0+gr+/qFedefv6BlNDeP982c/rkSUdfXHj7DSVpPD0Q4Ou3evmxv+++oebAD88nTj76/O3nT9/QEt+jgzsOHXv45jNmrf3y2VushcDbl6/eo7gGDtANYSC1DcEwKBonKAAgwADHDRsbQcugwwAAAABJRU5ErkJggg==');
  }

	function formatGetal($waarde, $dec)
	{
		return number_format($waarde,$dec,",",".");
	}

  function formatGetalKoers($waarde, $dec , $start = false)
	{
	  if ($start == false)
	    $waarde = $waarde / $this->pdf->ValutaKoersEind;
	  else
	    $waarde = $waarde / $this->pdf->ValutaKoersStart;

	  return number_format($waarde,$dec,",",".");
  }


	function printSubTotaal($title, $totaalA, $totaalB)
	{
		// geen subtotaal!
		return true;
	}

	function printTotaal($title, $totaalA, $procent, $grandtotaal)
	{
		$this->pdf->SetLineWidth($this->pdf->lineWidth);

		$this->pdf->SetWidths($this->pdf->widthB);
		$this->pdf->SetAligns($this->pdf->alignB);

		$actueel = $this->pdf->marge + $this->pdf->widthB[0] + $this->pdf->widthB[1] + $this->pdf->widthB[2];

		if(!empty($totaalA))
		{
			if($this->pdf->rapport_OIB_specificatie == 1)
				$this->pdf->Line($actueel+2,$this->pdf->GetY(),$actueel + $this->pdf->widthB[3],$this->pdf->GetY());
			$totaalAtxt = $this->formatGetalKoers($totaalA,$this->pdf->rapport_OIB_decimaal);
		}

		if(!empty($procent))
			$totaalprtxt = $this->formatGetal($procent,1);

		$this->pdf->SetX($actueel);

		$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize);
		// color + font
		$this->pdf->SetTextColor($this->pdf->rapport_totaal_omschr_fontcolor['r'],$this->pdf->rapport_totaal_omschr_fontcolor['g'],$this->pdf->rapport_totaal_omschr_fontcolor['b']);
		$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_totaal_omschr_fontstyle,$this->pdf->rapport_fontsize);

		$this->pdf->Cell($this->pdf->widthB[3],4,$title, 0,0, "R");

		// color + font
		$this->pdf->SetTextColor($this->pdf->rapport_totaal_fontcolor['r'],$this->pdf->rapport_totaal_fontcolor['g'],$this->pdf->rapport_totaal_fontcolor['b']);
		$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_totaal_fontstyle,$this->pdf->rapport_fontsize);

  	$this->pdf->Cell($this->pdf->widthB[4],4,$totaalAtxt, 0,0, "R");
		$this->pdf->Cell($this->pdf->widthB[5],4,$totaalprtxt, 0,1, "R");


		if($grandtotaal)
		{
			$this->pdf->Line($actueel+2,$this->pdf->GetY(),$actueel + $this->pdf->widthB[3],$this->pdf->GetY());
			$this->pdf->Line($actueel+2,$this->pdf->GetY()+1,$actueel + $this->pdf->widthB[3],$this->pdf->GetY()+1);
		}

		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$this->pdf->ln(2);

		return $totaalA;
	}

	function printKop($title, $type="default")
	{
		switch($type)
		{
			case "b" :
				$font = $this->pdf->rapport_font;
				$fontsize = $this->pdf->rapport_fontsize;
				$fonttype = 'b';
			break;
			case "bi" :
				$font = $this->pdf->rapport_font;
				$fontsize = $this->pdf->rapport_fontsize;
				$fonttype = 'bi';
			break;
			case "i" :
				$font = $this->pdf->rapport_font;
				$fontsize = $this->pdf->rapport_fontsize;
				$fonttype = 'i';
			break;
			default :
				$font = $this->pdf->rapport_font;
				$fontsize = $this->pdf->rapport_fontsize;
				$fonttype = '';
			break;
		}

		if(($this->pdf->GetY() + 12) >= $this->pdf->pagebreak) {
			$this->pdf->AddPage();
			$this->pdf->ln();
		}
		$this->pdf->SetFont($font,$fonttype,$fontsize);
		$this->pdf->SetTextColor($this->pdf->rapport_kop3_fontcolor['r'],$this->pdf->rapport_kop3_fontcolor['g'],$this->pdf->rapport_kop3_fontcolor['b']);
		$this->pdf->SetX($this->pdf->marge);
		$y = $this->pdf->getY();

	  $this->pdf->MultiCell($this->pdf->widthB[0],4, $title, 0, "L");


	  $this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor['r'],$this->pdf->rapport_fonds_fontcolor['g'],$this->pdf->rapport_fonds_fontcolor['b']);
		$this->pdf->SetY($y);
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
		if (isset($this->pdf->__appvar['consolidatie']))
		{
			$this->writeRapportConsolidatie();
		}
		else
		{
			$this->writeRapportSingle();
		}
	}

	function writeRapportSingle()
	{
		global $__appvar;
		$query = "SELECT Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->pdf->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$portefeuilledata = $DB->nextRecord();


		// voor data
		$this->pdf->widthB = array(40,35,25,25,25,15,115);
		$this->pdf->alignB = array('L','L','R','R','R','R','R');

		// voor kopjes
		$this->pdf->widthA = array(40,35,25,25,25,15,115);
		$this->pdf->alignA = array('L','L','R','R','R','R','R');



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
		$totaalWaarde = $totaalWaarde[totaal];
    
    $this->totaalWaarde=$totaalWaarde;
		if($this->totaalWaarde==0)
			return 0;

		$this->pdf->AddPage();
		$this->pdf->templateVars['OIBPaginas']=$this->pdf->page;
		$this->pdf->templateVarsOmschrijving['OIBPaginas']=$this->pdf->rapport_titel;


		$actueleWaardePortefeuille = 0;

		$query = "SELECT TijdelijkeRapportage.BeleggingscategorieOmschrijving as Omschrijving,
		 TijdelijkeRapportage.hoofdcategorieOmschrijving as HOmschrijving,".
			" TijdelijkeRapportage.valutaOmschrijving AS ValutaOmschrijving, ".
			" TijdelijkeRapportage.valuta, TijdelijkeRapportage.actueleValuta, TijdelijkeRapportage.beleggingscategorie, ".
			" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta) AS subtotaalactueelvaluta, ".
			" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) AS subtotaalactueel ".
			" FROM TijdelijkeRapportage ".
			" WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
			" TijdelijkeRapportage.type = 'fondsen' AND ".
			" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."'"
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			" GROUP BY TijdelijkeRapportage.beleggingscategorie, TijdelijkeRapportage.valuta ".
			" ORDER BY TijdelijkeRapportage.beleggingscategorieVolgorde asc,  TijdelijkeRapportage.valutaVolgorde asc";
		debugSpecial($query,__FILE__,__LINE__);


		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();

		while($categorien = $DB->NextRecord())
		{
			// print categorie headers
			$this->pdf->SetWidths($this->pdf->widthA);
			$this->pdf->SetAligns($this->pdf->alignA);

			if($this->pdf->rapport_OIB_rentebijobligaties && strtolower($categorien['Omschrijving']) == "obligaties")
			{
				// selecteer rente
				$query = "SELECT TijdelijkeRapportage.valuta, ".
				" TijdelijkeRapportage.valutaOmschrijving as ValutaOmschrijving, ".
				" TijdelijkeRapportage.beleggingscategorie, ".
				" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta) subtotaalactueelvaluta, ".
				" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) subtotaalactueel FROM ".
				" TijdelijkeRapportage ".
				" WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
				" TijdelijkeRapportage.valuta = '".$categorien['valuta']."' AND ".
				" TijdelijkeRapportage.type = 'rente'  ".
				" AND TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
				.$__appvar['TijdelijkeRapportageMaakUniek'].
				" GROUP BY TijdelijkeRapportage.valuta ".
				" ORDER BY TijdelijkeRapportage.valutaVolgorde asc , TijdelijkeRapportage.Lossingsdatum asc";
				debugSpecial($query,__FILE__,__LINE__);
				$DBx = new DB();
				$DBx->SQL($query);
				$DBx->Query();
				$rentedata = $DBx->nextRecord();
				$categorien['subtotaalactueelvaluta'] = $categorien['subtotaalactueelvaluta'] + $rentedata['subtotaalactueelvaluta'];
				$categorien['subtotaalactueel'] = $categorien['subtotaalactueel'] + $rentedata['subtotaalactueel'];
			}

			// print totaal op hele categorie.
			if($lastCategorie <> $categorien['Omschrijving'] && !empty($lastCategorie) )
			{
				$percentageVanTotaal = $totaalactueel / ($totaalWaarde/100);
				$actueleWaardePortefeuille += $this->printTotaal("", $totaalactueel, $percentageVanTotaal);
				$totaalbegin = 0;
				$totaalactueel = 0;
				// voor Pie
				$this->pdf->pieData[vertaalTekst($lastCategorie,$this->pdf->rapport_taal)] = $percentageVanTotaal;
				$grafiekCategorien[$lastCat]=$percentageVanTotaal; //toevoeging voor kleuren.
			}

			if($lastCategorie != $categorien['Omschrijving'])
			{
			  if($this->pdf->rapport_layout == 14 && empty($lastCategorie))
		    {
		  	$this->pdf->row(array(""));
		    }
				$categorieTekst = $categorien['Omschrijving'];
			  $this->printKop(vertaalTekst($categorieTekst,$this->pdf->rapport_taal), $this->pdf->rapport_kop3_fontstyle);
			}
			$lastCategorie = $categorien['Omschrijving'];

			$percentageVanTotaal = $categorien['subtotaalactueel'] / ($totaalWaarde/100);

			// print valutaomschrijving appart ivm met apparte fontkleur
			$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
			$this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor['r'],$this->pdf->rapport_fonds_fontcolor['g'],$this->pdf->rapport_fonds_fontcolor['b']);
			$this->pdf->setX($this->pdf->marge);
				if($this->pdf->rapport_layout != 14)
			  {
			   $this->pdf->Cell($this->pdf->widthB[0],4,"");
			   $this->pdf->Cell($this->pdf->widthB[1],4,vertaalTekst($categorien['ValutaOmschrijving'],$this->pdf->rapport_taal));
			  }
			$this->pdf->setX($this->pdf->marge);

			$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);

			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
			// print categorie footers

			if($this->pdf->rapport_OIB_specificatie == 1)
			{

					$this->pdf->row(array("",
											"",
											$this->formatGetal($categorien['subtotaalactueelvaluta'],$this->pdf->rapport_OIB_decimaal),
											$this->formatGetalKoers($categorien['subtotaalactueel'],$this->pdf->rapport_OIB_decimaal),
											"",
											$this->formatGetal($percentageVanTotaal,1).""));
			
			}
			else
			{
				$this->pdf->row(array("",
											"",
											"",
											"",
											"",
											$this->formatGetal($percentageVanTotaal,1).""));
			
			}


			// totaal op categorie tellen
			$totaalinvaluta += $categorien['subtotaalactueelvaluta'];
			$totaalactueel += $categorien['subtotaalactueel'];
      $lastCat       = $categorien['beleggingscategorie'];
			$lastCategorie = $categorien['Omschrijving'];
		}

		// totaal voor de laatste categorie


		$percentageVanTotaal = $totaalactueel / ($totaalWaarde/100);
		$actueleWaardePortefeuille += $this->printTotaal("", $totaalactueel, $percentageVanTotaal);
		// voor Pie
		$this->pdf->pieData[vertaalTekst($lastCategorie,$this->pdf->rapport_taal)] = $percentageVanTotaal;
		$grafiekCategorien[$lastCat]=$percentageVanTotaal; //toevoeging voor kleuren.

		if(!$this->pdf->rapport_OIB_rentebijobligaties)
		{
			// selecteer rente
			$query = "SELECT TijdelijkeRapportage.valuta, ".
			" TijdelijkeRapportage.valutaOmschrijving AS ValutaOmschrijving, ".
			" TijdelijkeRapportage.beleggingscategorie, ".
			" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta) subtotaalactueelvaluta, ".
			" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) subtotaalactueel FROM ".
			" TijdelijkeRapportage ".
			" WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
			" TijdelijkeRapportage.type = 'rente'  ".
			" AND TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			" GROUP BY TijdelijkeRapportage.valuta ".
			" ORDER BY TijdelijkeRapportage.valutaVolgorde asc";
			debugSpecial($query,__FILE__,__LINE__);
			$DB = new DB();
			$DB->SQL($query);
			$DB->Query();

			if($DB->records() > 0)
			{
				$this->printKop(vertaalTekst("Opgelopen Rente",$this->pdf->rapport_taal),$this->pdf->rapport_kop3_fontstyle);

				$totaalRenteInValuta = 0 ;

				while($categorien = $DB->NextRecord())
				{

					$subtotaalRenteInValuta = 0;

					$percentageVanTotaal = $categorien['subtotaalactueel'] / ($totaalWaarde/100);

					// print valutaomschrijving appart ivm met apparte fontkleur
					$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
					$this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor['r'],$this->pdf->rapport_fonds_fontcolor['g'],$this->pdf->rapport_fonds_fontcolor['b']);
					$this->pdf->setX($this->pdf->marge);
					$this->pdf->Cell($this->pdf->widthB[0],4,"");
					$this->pdf->Cell($this->pdf->widthB[1],4,vertaalTekst($categorien['ValutaOmschrijving'],$this->pdf->rapport_taal));
					$this->pdf->setX($this->pdf->marge);

					$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
					$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
					if($this->pdf->rapport_OIB_specificatie == 1)
					{
							$this->pdf->row(array("",
													"",
													$this->formatGetal($categorien['subtotaalactueelvaluta'],$this->pdf->rapport_OIB_decimaal),
													$this->formatGetal($categorien['subtotaalactueel'],$this->pdf->rapport_OIB_decimaal),
													"",
													$this->formatGetal($percentageVanTotaal,1).""));
					}
					else
					{
						$this->pdf->row(array("",
													"",
													"",
													"",
													"",
													$this->formatGetal($percentageVanTotaal,1).""));
					}
					// print subtotaal
					$totaalRente += $categorien['subtotaalactueel'];
				}

				// totaal op rente
				$percentageVanTotaal = $totaalRente / ($totaalWaarde/100);
				$actueleWaardePortefeuille += $this->printTotaal(" ", $totaalRente, $percentageVanTotaal);
				$this->pdf->pieData[vertaalTekst("Opgelopen Rente",$this->pdf->rapport_taal)] = $percentageVanTotaal;
				$grafiekCategorien['Opgelopen Rente']=$percentageVanTotaal; //toevoeging voor kleuren.
			}
		}


		// Liquiditeiten
		$liqtitel = "Liquiditeiten";

		if($this->pdf->rapport_layout == 1 || $this->pdf->rapport_layout == 12)
			$liqtitel = strtoupper($liqtitel);

		$this->printKop(vertaalTekst($liqtitel,$this->pdf->rapport_taal),$this->pdf->rapport_kop3_fontstyle);

		$query = "SELECT TijdelijkeRapportage.fondsOmschrijving, ".
			" TijdelijkeRapportage.valutaOmschrijving AS ValutaOmschrijving, ".
			" TijdelijkeRapportage.actueleValuta , ".
			" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeInValuta) AS actuelePortefeuilleWaardeInValuta, ".
			" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) AS actuelePortefeuilleWaardeEuro, ".
			" TijdelijkeRapportage.valuta, TijdelijkeRapportage.portefeuille ".
			" FROM TijdelijkeRapportage, Valutas WHERE ".
			" TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
			" TijdelijkeRapportage.type = 'rekening'  AND ".
			" TijdelijkeRapportage.valuta = Valutas.valuta AND ".
			" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."' "
			.$__appvar['TijdelijkeRapportageMaakUniek'].
			" GROUP BY TijdelijkeRapportage.valuta ".
			" ORDER BY TijdelijkeRapportage.valutaVolgorde asc";
		debugSpecial($query,__FILE__,__LINE__);
		$DB1 = new DB();
		$DB1->SQL($query);
		$DB1->Query();

		$totaalLiquiditeitenInValuta = 0;

		while($data = $DB1->NextRecord())
		{
			$totaalLiquiditeitenEuro += $data['actuelePortefeuilleWaardeEuro'];

			$percentageVanTotaal = $data['actuelePortefeuilleWaardeEuro'] / ($totaalWaarde/100);

			// print valutaomschrijving appart ivm met apparte fontkleur
			$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_fontstyle,$this->pdf->rapport_fontsize);
			$this->pdf->SetTextColor($this->pdf->rapport_fonds_fontcolor['r'],$this->pdf->rapport_fonds_fontcolor['g'],$this->pdf->rapport_fonds_fontcolor['b']);
			$this->pdf->setX($this->pdf->marge);

			$this->pdf->Cell($this->pdf->widthB[0],4,"");
			$this->pdf->Cell($this->pdf->widthB[1],4,vertaalTekst($data['ValutaOmschrijving'],$this->pdf->rapport_taal));
			$this->pdf->setX($this->pdf->marge);

			$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);
			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

			if($this->pdf->rapport_OIB_specificatie == 1)
			{
					$this->pdf->row(array("",
											"",
											$this->formatGetal($data['actuelePortefeuilleWaardeInValuta'],$this->pdf->rapport_OIB_decimaal),
											$this->formatGetalKoers($data['actuelePortefeuilleWaardeEuro'],$this->pdf->rapport_OIB_decimaal),
											"",
											$this->formatGetal($percentageVanTotaal,1).""));
			}
			else
			{
				$this->pdf->row(array("",
											"",
											"",
											"",
											"",
											$this->formatGetal($percentageVanTotaal,1).""));
			}

		}
		// totaal liquiditeiten
		$percentageVanTotaal = $totaalLiquiditeitenEuro / ($totaalWaarde/100);
		$grafiekCategorien['Liquiditeiten']=$percentageVanTotaal; //toevoeging voor kleuren.
		$actueleWaardePortefeuille += $this->printTotaal("", $totaalLiquiditeitenEuro, $percentageVanTotaal);
		$this->pdf->pieData[vertaalTekst($liqtitel,$this->pdf->rapport_taal)] = $percentageVanTotaal;


		// print grandtotaal
		$this->pdf->ln();

		$actueel = $this->pdf->marge + $this->pdf->widthB[0] + $this->pdf->widthB[1] + $this->pdf->widthB[2] + $this->pdf->widthB[3];
		$proc = $this->pdf->marge + $this->pdf->widthB[0] + $this->pdf->widthB[1] + $this->pdf->widthB[2] + $this->pdf->widthB[3] + $this->pdf->widthB[4];
		$extra =0;
		$this->pdf->Line($actueel+2,$this->pdf->GetY(),$actueel + $this->pdf->widthB[4],$this->pdf->GetY());
		$this->pdf->Line($proc+2+$extra,$this->pdf->GetY(),$proc + $this->pdf->widthB[5]+$extra,$this->pdf->GetY());

		$this->pdf->setX($this->pdf->marge);
		$this->pdf->SetTextColor($this->pdf->rapport_totaal_omschr_fontcolor['r'],$this->pdf->rapport_totaal_omschr_fontcolor['g'],$this->pdf->rapport_totaal_omschr_fontcolor['b']);
		$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_totaal_omschr_fontstyle,$this->pdf->rapport_fontsize);


		$this->pdf->Cell($this->pdf->widthB[0],4,"", 0,0, "L");
		$this->pdf->Cell($this->pdf->widthB[1],4,vertaalTekst("Totale actuele waarde portefeuille",$this->pdf->rapport_taal), 0,0, "L");
		$this->pdf->Cell($this->pdf->widthB[2],4,"", 0,0, "R");
	  $this->pdf->Cell($this->pdf->widthB[3],4,"", 0,0, "L");
		// color + font
		$this->pdf->SetTextColor($this->pdf->rapport_totaal_fontcolor['r'],$this->pdf->rapport_totaal_fontcolor['g'],$this->pdf->rapport_totaal_fontcolor['b']);
		$this->pdf->SetFont($this->pdf->rapport_font,$this->pdf->rapport_totaal_fontstyle,$this->pdf->rapport_fontsize);


		// check op totaalwaarde!
		if(round(($totaalWaarde - $actueleWaardePortefeuille),2) <> 0)
		{
			echo "<script>
			alert('Fout : Fout in rapport ".$this->portefeuille.", totale waarde (".round($totaalWaarde,2).") komt niet overeen met afgedrukte totaal (".round($actueleWaardePortefeuille,2).") in rapport ".$this->pdf->rapport_type."');
			</script>";
			ob_flush();
		}

		$this->pdf->Cell($this->pdf->widthB[4],4,$this->formatGetalKoers($actueleWaardePortefeuille,$this->pdf->rapport_OIB_decimaal), 0,0, "R");
		$this->pdf->Cell($this->pdf->widthB[5],4,$this->formatGetal(100,1), 0,1, "R");
	
		$this->pdf->Line($actueel+2,$this->pdf->GetY(),$actueel + $this->pdf->widthB[4],$this->pdf->GetY());
		$this->pdf->Line($actueel+2,$this->pdf->GetY()+1,$actueel + $this->pdf->widthB[4],$this->pdf->GetY()+1);
		$this->pdf->Line($proc+2+$extra,$this->pdf->GetY(),$proc + $this->pdf->widthB[5]+$extra,$this->pdf->GetY());
		$this->pdf->Line($proc+2+$extra,$this->pdf->GetY()+1,$proc + $this->pdf->widthB[5]+$extra,$this->pdf->GetY()+1);

		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);

		if($this->pdf->rapport_OIB_valutaoverzicht == 1)
		{
			$this->pdf->ln(2);
			// in PDFRapport.php
			$this->pdf->printValutaoverzicht($this->portefeuille, $this->rapportageDatum);
		}
		elseif($this->pdf->rapport_OIB_valutaoverzicht == 2)
		{
			$this->pdf->ln(2);
			// in PDFRapport.php
			$this->pdf->printValutaPerformanceOverzicht($this->portefeuille, $this->rapportageDatum, $this->rapportageDatumVanaf);
		}

		if($this->pdf->rapport_OIB_rendement == 1)
		{
			$this->pdf->printRendement($this->portefeuille, $this->rapportageDatum, $this->rapportageDatumVanaf, $this->pdf->rapport_OIB_rendementKort);
		}
		$yPage=$this->pdf->getY();

		$beheerder = $this->pdf->portefeuilledata['Vermogensbeheerder'];
		$q="SELECT grafiek_kleur FROM Vermogensbeheerders WHERE Vermogensbeheerder = '".$beheerder."'";
		$DB = new DB();
		$DB->SQL($q);
		$DB->Query();
		$kleuren = $DB->LookupRecord();
		$kleuren = unserialize($kleuren['grafiek_kleur']);
		$kleuren = $kleuren['OIB'];
		$q = "SELECT Beleggingscategorie, omschrijving FROM Beleggingscategorien";
		$DB->SQL($q);
		$DB->Query();
		$kleurdata = array();

		$dbBeleggingscategorien = array();
		$dbBeleggingscategorien['Opgelopen Rente']='Opgelopen Rente'; //Voorkomen dat Opgelopen rente leeg blijft wanneer vermogensbeheerder kleuren niet geset.

		while($categorie = $DB->NextRecord())
		{
			$dbBeleggingscategorien[$categorie['Beleggingscategorie']] = $categorie['omschrijving'];
		}

    foreach ($grafiekCategorien as $cat=>$percentage)
    {
      $groep=$dbBeleggingscategorien[$cat];
      $groep=	vertaalTekst($groep,$this->pdf->rapport_taal);
      $kleurdata[$groep]['kleur'] = $kleuren[$cat];
      $kleurdata[$groep]['percentage'] = $percentage;
    }
		//$this->pdf->printPie($this->pdf->pieData,$kleurdata);

		getTypeGrafiekData_L32($this,'Beleggingscategorie');
    foreach ($this->pdf->grafiekData['Beleggingscategorie']['grafiek'] as $omschrijving=>$waarde)
    {
      $grafiekData['OIB']['Omschrijving'][]=$omschrijving." (".$this->formatGetal($waarde,1)."%)";
      $grafiekData['OIB']['Percentage'][]=$waarde;
    }
    $diameter = 34;
    $hoek = 30;
    $dikte = 10;
    $Xas= 75;
    $yas= 68;
    $onderY=80;
    $xRechts=160;
    //$this->pdf->set3dLabels($grafiekData['OIB']['Omschrijving'],$Xas+$xRechts+10,$yas,$this->pdf->grafiekData['Beleggingscategorie']['grafiekKleur']);
    //$this->pdf->Pie3D($grafiekData['OIB']['Percentage'],$this->pdf->grafiekData['Beleggingscategorie']['grafiekKleur'],$Xas+$xRechts,$yas,$diameter,$hoek,$dikte,vertaalTekst("Beleggingscategorie",$this->pdf->rapport_taal) ,'titel');
    $this->pdf->setXY($xRechts+20,$yas-15);
    $this->PieChart(65, 65, $this->pdf->grafiekData['Beleggingscategorie']['grafiek'], '%l (%p)',$this->pdf->grafiekData['Beleggingscategorie']['grafiekKleur']);

		getTypeGrafiekData_L32($this,'Valuta');
    foreach ($this->pdf->grafiekData['Valuta']['grafiek'] as $omschrijving=>$waarde)
    {
      $grafiekData['OIV']['Omschrijving'][]=$omschrijving." (".$this->formatGetal($waarde,1)."%)";
      $grafiekData['OIV']['Percentage'][]=$waarde;
    }
    
    $yas= 145;
    //$this->pdf->set3dLabels($grafiekData['OIV']['Omschrijving'],$Xas+$xRechts+10,$yas,$this->pdf->grafiekData['Valuta']['grafiekKleur']);
   // $this->pdf->Pie3D($grafiekData['OIV']['Percentage'],$this->pdf->grafiekData['Valuta']['grafiekKleur'],$Xas+$xRechts,$yas,$diameter,$hoek,$dikte,vertaalTekst("Valuta",$this->pdf->rapport_taal),'titel');
    $this->pdf->setXY($xRechts+20,$yas-15);
    $this->PieChart(65, 65, $this->pdf->grafiekData['Valuta']['grafiek'], '%l (%p)',$this->pdf->grafiekData['Valuta']['grafiekKleur']);
    

//$this->toonZorgplicht();
		$this->pdf->setY($yPage);
		$this->printBenchmarkvergelijking();
	}
  
  function toonZorgplicht()
  {
   global $__appvar;
    $DB=new DB();
  
    $query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
      "FROM TijdelijkeRapportage WHERE ".
      " rapportageDatum ='".$this->rapportageDatum."' AND ".
      " portefeuille = '".$this->portefeuille."' "
      .$__appvar['TijdelijkeRapportageMaakUniek'];
    debugSpecial($query,__FILE__,__LINE__);
    $DB->SQL($query);
    $DB->Query();
    $totaalWaarde = $DB->nextRecord();
    $totaalWaarde = $totaalWaarde['totaal'];
    $this->totaalWaarde=$totaalWaarde;
    
		if(round($this->totaalWaarde,2) <> 0)
		{
      $this->pdf->ln();
			$query = "SELECT SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) / " . $this->totaalWaarde . " as percentage,
TijdelijkeRapportage.beleggingscategorieOmschrijving,
TijdelijkeRapportage.beleggingscategorie,
ZorgplichtPerBeleggingscategorie.Zorgplicht
FROM TijdelijkeRapportage
JOIN ZorgplichtPerBeleggingscategorie ON TijdelijkeRapportage.beleggingscategorie = ZorgplichtPerBeleggingscategorie.Beleggingscategorie AND ZorgplichtPerBeleggingscategorie.Vermogensbeheerder='" . $this->pdf->portefeuilledata['Vermogensbeheerder'] . "'
WHERE TijdelijkeRapportage.Portefeuille =  '" . $this->portefeuille . "' AND
 TijdelijkeRapportage.rapportageDatum = '" . $this->rapportageDatum . "' " . $__appvar['TijdelijkeRapportageMaakUniek'] . "
GROUP BY ZorgplichtPerBeleggingscategorie.Zorgplicht 
ORDER BY TijdelijkeRapportage.beleggingscategorieVolgorde";
			$DB->SQL($query);
			$DB->Query();
			while ($data = $DB->nextRecord())
			{
				$categorieWaarden[$data['Zorgplicht']] = $data['percentage'] * 100;
				$categorieOmschrijving[$data['Zorgplicht']] = $data['beleggingscategorieOmschrijving'];
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

		if(count($categorieWaarden)>0)
		{
    $this->pdf->SetAligns(array('L','R','R','R','R'));
    $beginY=$this->pdf->getY();

		$this->pdf->SetWidths(array(40,20,20,20,20));
  	$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
    $this->pdf->row(array(vertaalTekst("beleggingscategorie",$this->pdf->rapport_taal),vertaalTekst("minimaal",$this->pdf->rapport_taal),vertaalTekst("maximaal",$this->pdf->rapport_taal),vertaalTekst("werkelijk",$this->pdf->rapport_taal),vertaalTekst("conclusie",$this->pdf->rapport_taal)));







    	$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
    $this->pdf->SetAligns(array('L','R','R','R','R'));
  	//foreach ($tmp as $index=>$regelData)
    
    
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
        
      if($tmp[$cat][5]=='Voldoet')
        $this->pdf->MemImage($this->checkImg,120,$this->pdf->getY(),3.9,3.9);
      else
        $this->pdf->MemImage($this->deleteImg,120,$this->pdf->getY(),3.9,3.9);  
        
      
  	  $this->pdf->row(array($categorieOmschrijving[$cat],$min,$max,$this->formatGetal($categorieWaarden[$cat],1)."%"));//$risicogewogen
    }
    $this->pdf->Rect($this->pdf->marge,$beginY,120,count($categorieWaarden)*4+4);
		}
  }
	
	
	
	
	function writeRapportConsolidatie()
	{
		global $__appvar;
		$query = "SELECT Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->pdf->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$portefeuilledata = $DB->nextRecord();
		
		if(!is_array($this->pdf->grafiekKleuren))
		{
			$q="SELECT grafiek_kleur FROM Vermogensbeheerders WHERE Vermogensbeheerder = '".$this->pdf->portefeuilledata['Vermogensbeheerder']."'";
			$DB->SQL($q);
			$DB->Query();
			$kleuren = $DB->LookupRecord();
			$kleuren = unserialize($kleuren['grafiek_kleur']);
			$this->pdf->grafiekKleuren=$kleuren;
		}

		if(is_array($this->pdf->portefeuilles))
			$consolidatie=true;
		else
			$consolidatie=false;
		
		$aantalPortefeuilles=0;
		if($consolidatie)
		{
			$aantalPortefeuilles=count($this->pdf->portefeuilles);
			foreach($this->pdf->portefeuilles as $portefeuille)
			{
				$portefeuilleWaarden[$portefeuille]['belCatWaarde']=array();
				$gegevens=berekenPortefeuilleWaarde($portefeuille,$this->rapportageDatum);
				foreach($gegevens as $waarde)
				{
					//$n=rand(1,2);
					$n='';
					$waarde['beleggingscategorieVolgorde']=$waarde['beleggingscategorieVolgorde'].$n;


					$portefeuilleWaarden[$portefeuille]['belCatWaarde'][$waarde['beleggingscategorie']]+=$waarde['actuelePortefeuilleWaardeEuro'];
					$portefeuilleWaarden[$portefeuille]['totaleWaarde']+=$waarde['actuelePortefeuilleWaardeEuro'];
					$categorieVolgorde[$waarde['beleggingscategorieVolgorde']]=$waarde['beleggingscategorie'];
					$categorieOmschrijving[$waarde['beleggingscategorie']]=vertaalTekst($waarde['beleggingscategorieOmschrijving'],$this->pdf->rapport_taal);
					$totaalWaarde+=$waarde['actuelePortefeuilleWaardeEuro'];
          
          $hoofdcategorieOmschrijving[$waarde['beleggingscategorie']]=vertaalTekst($waarde['hoofdcategorieOmschrijving'],$this->pdf->rapport_taal);

				}
			}
			foreach($portefeuilleWaarden as $portefeuille=>$waarden)
			{
				foreach($waarden['belCatWaarde'] as $categorie=>$waardeEur)
				{
					$percentage=($waardeEur/$waarden['totaleWaarde']);
					$portefeuilleWaarden[$portefeuille]['belCatPercentage'][$categorie]=$percentage;
					$portefeuilleWaarden[$portefeuille]['totalePercentage']+=$percentage;
				}
			}
		}
		else
		{
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
			$totaalWaarde = $totaalWaarde['totaal'];
			$portefeuilleWaarden[$this->portefeuille]['totaleWaarde']=$totaalWaarde;
			
			
			$query = "SELECT TijdelijkeRapportage.BeleggingscategorieOmschrijving as Omschrijving, TijdelijkeRapportage.beleggingscategorieVolgorde, ".
				" TijdelijkeRapportage.valuta, TijdelijkeRapportage.actueleValuta, TijdelijkeRapportage.beleggingscategorie, TijdelijkeRapportage.hoofdcategorieOmschrijving as HOmschrijving, ".
				" SUM(TijdelijkeRapportage.actuelePortefeuilleWaardeEuro) AS actuelePortefeuilleWaardeEuro ".
				" FROM TijdelijkeRapportage ".
				" WHERE TijdelijkeRapportage.portefeuille = '".$this->portefeuille."' AND ".
				" TijdelijkeRapportage.rapportageDatum = '".$this->rapportageDatum."'"
				.$__appvar['TijdelijkeRapportageMaakUniek'].
				" GROUP BY TijdelijkeRapportage.beleggingscategorie".
				" ORDER BY TijdelijkeRapportage.beleggingscategorieVolgorde asc";
			debugSpecial($query,__FILE__,__LINE__);
			
			$DB->SQL($query);
			$DB->Query();
			
			while($categorien = $DB->NextRecord())
			{
				//$n=rand(1,2);
				$n='';
				$categorien['beleggingscategorieVolgorde']=$categorien['beleggingscategorieVolgorde'].$n;

				if($categorien['beleggingscategorie']=='')
				{
					$categorien['beleggingscategorie']='GeenCategorie';
					$categorien['Omschrijving']='Geen categorie';
				}
				$categorieOmschrijving[$categorien['beleggingscategorie']]=vertaalTekst($categorien['Omschrijving'],$this->pdf->rapport_taal);
        $hoofdcategorieOmschrijving[$categorien['beleggingscategorie']]=vertaalTekst($categorien['HOmschrijving'],$this->pdf->rapport_taal);
				$categorieVolgorde[$categorien['beleggingscategorieVolgorde']]=$categorien['beleggingscategorie'];
				$portefeuilleWaarden[$this->portefeuille]['belCatWaarde'][$categorien['beleggingscategorie']]+=$categorien['actuelePortefeuilleWaardeEuro'];
				$percentage=($categorien['actuelePortefeuilleWaardeEuro']/$totaalWaarde);
				$portefeuilleWaarden[$this->portefeuille]['belCatPercentage'][$categorien['beleggingscategorie']]=$percentage;
				$portefeuilleWaarden[$this->portefeuille]['totalePercentage']+=$percentage;
				
			}
		}
		
		
		// voor kopjes
		$pw=14;
		$portw=23;
		$tw=$pw+$portw;
		$this->pdf->widthA = array(60,$portw,$pw,$portw,$pw,$portw,$pw,$portw,$pw,$portw,$pw,$portw,$pw,$portw,$pw);
		$this->pdf->alignA = array('L','R','R','R','R','R','R','R','R','R','R','R','R','R');
		// voor data
		$this->pdf->widthB = array(65,$tw,$tw,$tw,$tw,$tw,$tw,$tw);
		$this->pdf->alignB = array('L','C','C','C','C','C','C','C','C');
		if(is_array($this->pdf->portefeuilles))
		{
			$query="SELECT Portefeuille,ClientVermogensbeheerder FROM Portefeuilles WHERE Portefeuille IN('".implode("','",$this->pdf->portefeuilles)."')";
			$DB->SQL($query);
			$DB->Query();
			while($portefeuille = $DB->NextRecord())
			{
				$this->pdf->clientVermogensbeheerder[$portefeuille['Portefeuille']]=$this->getCRMnaam($portefeuille['Portefeuille']);
			}
		}
		
		
		$this->pdf->AddPage();
		$this->pdf->templateVars['OIBPaginas']=$this->pdf->page;
		$this->pdf->templateVarsOmschrijving['OIBPaginas']=$this->pdf->rapport_titel;
		$this->pdf->SetTextColor($this->pdf->rapport_fontcolor['r'],$this->pdf->rapport_fontcolor['g'],$this->pdf->rapport_fontcolor['b']);

		// print categorie headers
		$this->pdf->SetWidths($this->pdf->widthA);
		$this->pdf->SetAligns($this->pdf->alignA);
		$this->pdf->SetFont($this->pdf->rapport_font,'', $this->pdf->rapport_fontsize);
		
		ksort($categorieVolgorde);
		$regelData=array();
		$regelDataTotaal=array();
		$totaalPercentage=0;
		$barGraph=false;
		foreach($categorieVolgorde as $categorie)
		{
			$regelTotaal=0;
			foreach($portefeuilleWaarden as $portefeuille=>$belCatData)
			{
				$regelData[$portefeuille][$categorieOmschrijving[$categorie]]=array('waarde'=>$this->formatGetal($belCatData['belCatWaarde'][$categorie],0),'percentage'=>$this->formatGetal($belCatData['belCatPercentage'][$categorie]*100,1));
				$regelTotaal+=$belCatData['belCatWaarde'][$categorie];
			}
			if($consolidatie)
			{
				$percentage=$regelTotaal/$totaalWaarde;
				$regelData['Totaal'][$categorieOmschrijving[$categorie]]=array('waarde'=>$this->formatGetal($regelTotaal,0),'percentage'=>$this->formatGetal($percentage*100,1));
				
				//echo "$portefeuille $percentage=$regelTotaal/$totaalWaarde; ->$totaalPercentage <br>\n";
				$totaalPercentage+=$percentage;
			}
			if($regelTotaal<0)
				$barGraph=true;
			$categorieVerdeling['omschrijving'][$categorieOmschrijving[$categorie]]=$categorieOmschrijving[$categorie]." (".round($regelTotaal/$totaalWaarde*100,1)."%)";
			$categorieVerdeling['percentage'][$categorieOmschrijving[$categorie]]=$regelTotaal/$totaalWaarde*100;
			$categorieVerdeling['kleur'][]=array($this->pdf->grafiekKleuren['OIB'][$categorie]['R']['value'],$this->pdf->grafiekKleuren['OIB'][$categorie]['G']['value'],$this->pdf->grafiekKleuren['OIB'][$categorie]['B']['value']);
			$categorieVerdeling['kleurBar'][$categorieOmschrijving[$categorie]]=array($this->pdf->grafiekKleuren['OIB'][$categorie]['R']['value'],$this->pdf->grafiekKleuren['OIB'][$categorie]['G']['value'],$this->pdf->grafiekKleuren['OIB'][$categorie]['B']['value']);
		}
		
		$regel=array('Totalen');
		foreach($portefeuilleWaarden as $portefeuille=>$belCatData)
			$regelDataTotaal[$portefeuille]=array('waarde'=>$this->formatGetal($belCatData['totaleWaarde'],0),'percentage'=>$this->formatGetal($belCatData['totalePercentage']*100,1));
		if($consolidatie)
			$regelDataTotaal['Totaal']=array('waarde'=>$this->formatGetal($totaalWaarde,0),'percentage'=>$this->formatGetal($totaalPercentage*100,1));
		
		$portefeuilleAantal=count($portefeuilleWaarden);
		$blokken=ceil($portefeuilleAantal/5);
		
		for($i=0;$i<$blokken;$i++)
		{
      $printed=array();
			//Kop regel
			$regel = array();
			array_push($regel, vertaalTekst("Beleggingscategorie",$this->pdf->rapport_taal));
			if($i==0 && $consolidatie==true)
				array_push($regel,  vertaalTekst('Totaal',$this->pdf->rapport_taal));
			else
				array_push($regel, '');
			//array_push($regel, '');
			$min=$i*5;
			$max=($i+1)*5;
			$n=0;
			$this->pdf->SetWidths($this->pdf->widthB);
			$this->pdf->SetAligns($this->pdf->alignB);
			foreach($portefeuilleWaarden as $portefeuille=>$belCatData)
			{
				if($n>=$min && $n<$max)
				{
					$kop=$this->getCRMnaam($portefeuille);
					array_push($regel, $kop);
					//array_push($regel,'');
				}
				$n++;
			}
			
			if($aantalPortefeuilles>5)
			{
				$this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
				$this->pdf->Row($regel);
				$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
			}
			//categorieen
			$this->pdf->SetWidths($this->pdf->widthA);
			$this->pdf->SetAligns($this->pdf->alignA);
			foreach($categorieVolgorde as $categorie)
			{
				$regel = array();
				if($i==0  && $consolidatie==true)
				{
					array_push($regel, $categorieOmschrijving[$categorie]);
					array_push($regel, $regelData['Totaal'][$categorieOmschrijving[$categorie]]['waarde']);
					array_push($regel, $regelData['Totaal'][$categorieOmschrijving[$categorie]]['percentage']);
				}
				else
				{
					array_push($regel, $categorieOmschrijving[$categorie]);
					if($consolidatie==true)
						$cols=2;
					else
						$cols=0;
					for($a=0;$a<$cols;$a++)
						array_push($regel,'');
				}
				$min=$i*5;
				$max=($i+1)*5;
				$n=0;
				foreach($portefeuilleWaarden as $portefeuille=>$belCatData)
				{
					if($n>=$min && $n<$max)
					{
						array_push($regel, $regelData[$portefeuille][$categorieOmschrijving[$categorie]]['waarde']);
						array_push($regel, $regelData[$portefeuille][$categorieOmschrijving[$categorie]]['percentage']);
					}
					$n++;
				}
				if(!isset($printed[$hoofdcategorieOmschrijving[$categorie]]))
        {
          if(count($printed)<>0)
            $this->pdf->ln();
          $this->pdf->SetFont($this->pdf->rapport_font, 'B', $this->pdf->rapport_fontsize);
          $this->pdf->Row(array($hoofdcategorieOmschrijving[$categorie]));
          $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
          //$this->pdf->ln();
          $printed[$hoofdcategorieOmschrijving[$categorie]]=1;
        }
				$this->pdf->Row($regel);
			}
			
			$this->pdf->SetWidths($this->pdf->widthA);
			$this->pdf->SetAligns($this->pdf->alignA);
			//Totaal regel
			$regel = array();
			if($i==0  && $consolidatie==true)
			{
				array_push($regel, vertaalTekst('Totalen',$this->pdf->rapport_taal));
				array_push($regel, $regelDataTotaal['Totaal']['waarde']);
				array_push($regel, $regelDataTotaal['Totaal']['percentage']);
			}
			else
			{
				if($consolidatie==true)
					$cols=3;
				else
					$cols=1;
				for($a=0;$a<$cols;$a++)
					array_push($regel,'');
			}
			$max=($i+1)*5;
			$n=0;
			foreach($portefeuilleWaarden as $portefeuille=>$belCatData)
			{
				if($n>=$min && $n<$max)
				{
					array_push($regel, $regelDataTotaal[$portefeuille]['waarde']);
					array_push($regel, $regelDataTotaal[$portefeuille]['percentage']);
				}
				$n++;
			}
			$this->pdf->SetFont($this->pdf->rapport_font,'B', $this->pdf->rapport_fontsize);
			$this->pdf->ln();
			$this->pdf->Row($regel);
			$this->pdf->SetFont($this->pdf->rapport_font,'', $this->pdf->rapport_fontsize);

			$this->pdf->ln();
		}


//    $this->pdf->SetFont($this->pdf->rapport_font,'B', $this->pdf->rapport_fontsize);
//    $this->pdf->Row($regel);

		if($this->pdf->getY() > 110)
		{
			$this->pdf->addPage();
			$grafiekY=$this->pdf->getY()+15;
		}
		else
			$grafiekY=120;



		if($barGraph==false)
		{
			//echo $grafiekY;exit;
			$this->pdf->setXY(20,$grafiekY);
		//	PieChart_L32($this->pdf,65, 65, $categorieVerdeling['percentage'], '%l (%p)',$categorieVerdeling['kleur']);

			$diameter = 34;
			$hoek = 30;
			$dikte = 10;
			$Xas= 80;
			$yas= $grafiekY+10;//130;
			$xRechts=0;

//listarray($categorieVerdeling);
			//$categorieVerdeling['omschrijving']=array_values($categorieVerdeling['omschrijving']);
			//$categorieVerdeling['percentage']=array_values($categorieVerdeling['percentage']);
			//$categorieVerdeling['kleur']=array_values($categorieVerdeling['kleur']);
			//$this->pdf->set3dLabels($categorieVerdeling['omschrijving'],$Xas+$xRechts+10,$yas,$categorieVerdeling['kleur']);
			//$this->pdf->Pie3D($categorieVerdeling['percentage'],$categorieVerdeling['kleur'],$Xas+$xRechts,$yas,$diameter,$hoek,$dikte,"",'titel');
      $this->PieChart(65, 65, $categorieVerdeling['percentage'], '%l (%p)',$categorieVerdeling['kleur']);

		}
		else
		{
			$this->pdf->setXY(50,$grafiekY);
			$this->BarDiagram(80, 100, $categorieVerdeling['percentage'], '%l (%p)',$categorieVerdeling['kleurBar']);//"Portefeuillewaarde € ".$this->formatGetal($this->portTotaal[$this->rapportageDatum],2)
		}
		
		if(isset($this->pdf->__appvar['consolidatie']))
		{
			$query = "SELECT
	            	if(Vermogensbeheerders.CrmPortefeuilleInformatie=1,CRM_naw.naam,Clienten.Naam) as Naam,
                if(Vermogensbeheerders.CrmPortefeuilleInformatie=1,CRM_naw.naam1,Clienten.Naam1) as Naam1,
                Clienten.Adres,
                Clienten.Woonplaats,
                Portefeuilles.Portefeuille,
                Portefeuilles.Depotbank,
                Portefeuilles.PortefeuilleVoorzet,
                Portefeuilles.kleurcode,
                Accountmanagers.Naam as accountManager,
                Vermogensbeheerders.Telefoon,
                Vermogensbeheerders.Fax,
                Vermogensbeheerders.Email,
                Depotbanken.Omschrijving as depotbankOmschrijving
		          FROM
		            Portefeuilles
		            LEFT JOIN Clienten ON Portefeuilles.Client = Clienten.Client
		            LEFT JOIN Accountmanagers ON Portefeuilles.Accountmanager = Accountmanagers.Accountmanager
		            LEFT JOIN Vermogensbeheerders ON Portefeuilles.Vermogensbeheerder = Vermogensbeheerders.Vermogensbeheerder
		            LEFT Join CRM_naw ON Portefeuilles.Portefeuille = CRM_naw.portefeuille
		            Join Depotbanken ON Portefeuilles.Depotbank = Depotbanken.Depotbank
		          WHERE
		            Portefeuilles.Portefeuille IN('".implode("','",$this->pdf->portefeuilles)."')
		            ORDER BY depotbankOmschrijving,Portefeuilles.Portefeuille";
			$DB->SQL($query);
			$DB->Query();
			while($tmp = $DB->nextRecord())
				$portefeuilledata[$tmp['Portefeuille']]=$tmp;
			
			$portefeuilleKleur=array();
			$portefeuilleKleurBar=array();
			$barGraph=false;
			foreach ($portefeuilleWaarden as $portefeuille=>$waarde)
			{
				//listarray($waarde);
				$kleur=unserialize($portefeuilledata[$portefeuille]['kleurcode']);
				if($kleur[0]==0 && $kleur[1]==0 && $kleur[2]==0)
					$kleur=array(rand(0,255),rand(0,255),rand(0,255));
				
				$kop=$this->getCRMnaam($portefeuille);
				$portefeuilleAandeel[$kop]=$waarde['totaleWaarde']/$totaalWaarde*100;
				$portefeuilleAandeelOmschrijving[$kop]=$kop." (".round($waarde['totaleWaarde']/$totaalWaarde*100,1)."%)";

				$portefeuilleKleur[]=$kleur;
				$portefeuilleKleurBar[$kop]=$kleur;
				if($waarde['totaleWaarde'] < 0)
					$barGraph=true;
			}

			/*

			getTypeGrafiekData_L32($this,'Beleggingscategorie');
			foreach ($this->pdf->grafiekData['Beleggingscategorie']['grafiek'] as $omschrijving=>$waarde)
			{
				$grafiekData['OIB']['Omschrijving'][]=$omschrijving." (".$this->formatGetal($waarde,1)."%)";
				$grafiekData['OIB']['Percentage'][]=$waarde;
			}
			$diameter = 34;
			$hoek = 30;
			$dikte = 10;
			$Xas= 75;
			$yas= 68;
			$onderY=80;
			$xRechts=160;
			$this->pdf->set3dLabels($grafiekData['OIB']['Omschrijving'],$Xas+$xRechts+10,$yas,$this->pdf->grafiekData['Beleggingscategorie']['grafiekKleur']);
			$this->pdf->Pie3D($grafiekData['OIB']['Percentage'],$this->pdf->grafiekData['Beleggingscategorie']['grafiekKleur'],$Xas+$xRechts,$yas,$diameter,$hoek,$dikte,"Beleggingscategorie",'titel');

			getTypeGrafiekData_L32($this,'Valuta');
			foreach ($this->pdf->grafiekData['Valuta']['grafiek'] as $omschrijving=>$waarde)
			{
				$grafiekData['OIV']['Omschrijving'][]=$omschrijving." (".$this->formatGetal($waarde,1)."%)";
				$grafiekData['OIV']['Percentage'][]=$waarde;
			}
			$yas= 145;
			$this->pdf->set3dLabels($grafiekData['OIV']['Omschrijving'],$Xas+$xRechts+10,$yas,$this->pdf->grafiekData['Beleggingscategorie']['grafiekKleur']);
			$this->pdf->Pie3D($grafiekData['OIV']['Percentage'],$this->pdf->grafiekData['Beleggingscategorie']['grafiekKleur'],$Xas+$xRechts,$yas,$diameter,$hoek,$dikte,"Valuta",'titel');
			*/


			$this->pdf->setY($grafiekY-10);
			$this->pdf->SetAligns(array('C','C'));
			$this->pdf->SetWidths(array(140,140));
			$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize+4);
			$this->pdf->row(array(vertaalTekst("Verdeling over categorieën",$this->pdf->rapport_taal),vertaalTekst("Verdeling over portefeuilles",$this->pdf->rapport_taal)));
			$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
			
			if($barGraph==false)
			{
				$this->pdf->setXY(160,$grafiekY);
				$this->PieChart(65, 65, $portefeuilleAandeel, '%l (%p)',$portefeuilleKleur);

				$diameter = 34;
				$hoek = 30;
				$dikte = 10;
				$Xas= 80;
				$yas= $grafiekY+10;
				$xRechts=140;
				//$this->pdf->set3dLabels(array_values($portefeuilleAandeelOmschrijving),$Xas+$xRechts+10,$yas, array_values($portefeuilleKleur));
				//$this->pdf->Pie3D(array_values($portefeuilleAandeel),array_values($portefeuilleKleur),$Xas+$xRechts,$yas,$diameter,$hoek,$dikte,"",'titel');
			}
			else
			{
				$this->pdf->setXY(190,$grafiekY);
				$this->BarDiagram(80, 100, $portefeuilleAandeel, '%l (%p)',$portefeuilleKleurBar);//"Portefeuillewaarde € ".$this->formatGetal($this->portTotaal[$this->rapportageDatum],2)
			}

			if($this->pdf->getY() > 100)
			{
				$this->pdf->addPage();
				$grafiekY=$this->pdf->getY()+70;
			}
			else
				$grafiekY+=80;

			$diameter = 34;
			$hoek = 30;
			$dikte = 10;
			$Xas= 80;
			$yas= $grafiekY+10;
			$xRechts=140;
			getTypeGrafiekData_L32($this,'Valuta');
			foreach ($this->pdf->grafiekData['Valuta']['grafiek'] as $omschrijving=>$waarde)
			{
				$grafiekData['OIV']['Omschrijving'][]=$omschrijving." (".$this->formatGetal($waarde,1)."%)";
				$grafiekData['OIV']['Percentage'][]=$waarde;
			}
      $this->PieChart(65, 65, $this->pdf->grafiekData['Valuta']['grafiek'] , '%l (%p)',$this->pdf->grafiekData['Valuta']['grafiekKleur']);
			
			//$this->pdf->set3dLabels($grafiekData['OIV']['Omschrijving'],$Xas+$xRechts+10,$yas,$this->pdf->grafiekData['Valuta']['grafiekKleur']);
			//$this->pdf->Pie3D($grafiekData['OIV']['Percentage'],$this->pdf->grafiekData['Valuta']['grafiekKleur'],$Xas+$xRechts,$yas,$diameter,$hoek,$dikte,vertaalTekst('Valuta',$this->pdf->rapport_taal),'titel');
      $this->pdf->setY($grafiekY+15);
			$this->toonZorgplicht();
		}
		$this->pdf->SetFillColor(0);
		unset($this->pdf->fillCell);
	}
	
	
	
	function SetLegends2($data, $format)
	{
		$this->pdf->legends=array();
		$this->pdf->wLegend=0;
		
		$this->pdf->sum=array_sum($data);
		
		$this->pdf->NbVal=count($data);
		foreach($data as $l=>$val)
		{
			//$p=sprintf('%.1f',$val/$this->sum*100).'%';
			$p=sprintf('%.1f',$val).'%';
			$legend=str_replace(array('%l','%v','%p'),array($l,$val,$p),$format);
			$this->pdf->legends[]=$legend;
			$this->pdf->wLegend=max($this->pdf->GetStringWidth($legend),$this->wLegend);
		}
	}
	
	function BarDiagram($w, $h, $data, $format,$colorArray,$titel)
	{
		$pdfObject = &$object;
		$this->pdf->SetFont($this->rapport_font, '', $this->rapport_fontsize);
		$this->SetLegends2($data,$format);
		
		
		$XPage = $this->pdf->GetX();
		$YPage = $this->pdf->GetY();
		$margin = 0;
		$nbDiv=5;
		$legendWidth=10;
		$YDiag = $YPage;
		$hDiag = floor($h);
		$XDiag = $XPage +  $legendWidth;
		$lDiag = floor($w - $legendWidth);
		if($color == null)
			$color=array(155,155,155);
		if ($maxVal == 0) {
			$maxVal = max($data)*1.1;
		}
		if ($minVal == 0) {
			$minVal = min($data)*1.1;
		}
		if($minVal > 0)
			$minVal=0;
		$maxVal=ceil($maxVal/10)*10;
		
		$offset=$minVal;
		$valIndRepere = ceil(round(($maxVal-$minVal) / $nbDiv,2)*100)/100;
		$bandBreedte = $valIndRepere * $nbDiv;
		$lRepere = floor($lDiag / $nbDiv);
		$unit = $lDiag / $bandBreedte;
		$hBar = 5;//floor($hDiag / ($this->pdf->NbVal + 1));
		$hDiag = $hBar * ($this->pdf->NbVal + 1);
		
		//echo "$hBar <br>\n";
		$eBaton = floor($hBar * 80 / 100);
		$legendaStep=$unit;
		
		$legendaStep=$unit/$nbDiv*$bandBreedte;
		//if($bandBreedte/$legendaStep > $nbDiv)
		//  $legendaStep=$legendaStep*5;
		// if($bandBreedte/$legendaStep > $nbDiv)
		//  $legendaStep=$legendaStep*2;
		// if($bandBreedte/$legendaStep > $nbDiv)
		//   $legendaStep=$legendaStep/2*5;
		$valIndRepere=round($valIndRepere/$unit/5)*5;
		
		
		$this->pdf->SetLineWidth(0.2);
		$this->pdf->Rect($XDiag, $YDiag, $lDiag, $hDiag);
		$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
		$this->pdf->SetFillColor($color[0],$color[1],$color[2]);
		$nullijn=$XDiag - ($offset * $unit);
		
		$i=0;
		$nbDiv=10;
		
		$this->pdf->SetFont($this->pdf->rapport_font, '', 5);
		if(round($legendaStep,5) <> 0.0)
		{
			//for($x=$nullijn;$x<$XDiag; $x=$x-$legendaStep)
			for($x=$nullijn;$x>$XDiag; $x=$x-$legendaStep)
			{
				$this->pdf->Line($x, $YDiag, $x, $YDiag + $hDiag);
				$this->pdf->setXY($x,$YDiag + $hDiag);
				$this->pdf->Cell(0.1, 5, round(($x-$nullijn)/$unit,0),0,0,'C');
				$i++;
				if($i>100)
					break;
			}
			
			$i=0;
			//for($x=$nullijn;$x>($XDiag+$lDiag); $x=$x+$legendaStep)
			for($x=$nullijn;$x<($XDiag+$lDiag); $x=$x+$legendaStep)
			{
				$this->pdf->Line($x, $YDiag, $x, $YDiag + $hDiag);
				$this->pdf->setXY($x,$YDiag + $hDiag);
				$this->pdf->Cell(0.1, 5, round(($x-$nullijn)/$unit,0),0,0,'C');
				
				$i++;
				if($i>100)
					break;
			}
		}
		
		$i=0;
		
		$this->pdf->SetXY($XDiag-$legendWidth, $YDiag);
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize+4);
		$this->pdf->Cell($lDiag, $hval-5, $titel,0,0,'C');
		$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize-2);
//listarray($colorArray);listarray($data);
		foreach($data as $key=>$val)
		{
			$this->pdf->SetFillColor($colorArray[$key][0],$colorArray[$key][1],$colorArray[$key][2]);
			$xval = $nullijn;
			$lval = ($val * $unit);
			$yval = $YDiag + ($i + 1) * $hBar - $eBaton / 2;
			$hval = $eBaton;
			$this->pdf->Rect($xval, $yval, $lval, $hval, 'DF');
			$this->pdf->SetXY($XPage, $yval);
			$this->pdf->Cell($legendWidth , $hval, $this->pdf->legends[$i],0,0,'R');
			$i++;
		}
		
		//Scales
		$minPos=($minVal * $unit);
		$maxPos=($maxVal * $unit);
		
		$unit=($maxPos-$minPos)/$nbDiv;
		// echo "$minPos $maxPos -> $minVal $maxVal using $unit met null $nullijn";
		
		
	}

	function printBenchmarkvergelijking($addPage=false)
	{
		global $__appvar;
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
			$zorgplichtcategorien[$zorgplicht['Zorgplicht']]=$zorgplicht;
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
		if($DB->records()==0)
		  return 0;
		
		if($addPage==true)
      $this->pdf->addPage();
		
		while($zorgplicht = $DB->nextRecord())
		{
			$zorgplichtcategorien[$zorgplicht['Zorgplicht']]=$zorgplicht;
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
		// listarray($zorgplichtcategorien);exit;
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
/*
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
			//echo "$fonds <br>\n"; ob_flush();
			$samengesteldeBenchmark[$zorgplichtCategorie]['periode']=getFondsPerformanceGestappeld2($fonds,$this->portefeuille,$this->perioden['begin'],$this->perioden['eind'],'maanden',false,true);
			$indexData[$zorgplichtData['fonds']]['performance']= $samengesteldeBenchmark[$zorgplichtCategorie]['periode'];
			$samengesteldeBenchmark[$zorgplichtCategorie]['jaar']=getFondsPerformanceGestappeld2($fonds,$this->portefeuille,$this->perioden['jan'],$this->perioden['eind'],'maanden',false,true);
			$indexData[$zorgplichtData['fonds']]['performanceJaar']= $samengesteldeBenchmark[$zorgplichtCategorie]['jaar'];

		}


		//////////////////////////

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

		////////////////////////////////////////////


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


		$this->pdf->SetWidths(array(40,20,20,20,20,30,20,20,20));
		$xVinkPlaatje=40+20+20+20+20+30+20+2;

		$this->pdf->ln();
		$this->pdf->SetFillColor($this->pdf->rapport_kop_bgcolor['r'],$this->pdf->rapport_kop_bgcolor['g'],$this->pdf->rapport_kop_bgcolor['b']);
		$this->pdf->Rect($this->pdf->marge, $this->pdf->getY(), $xVinkPlaatje, 8, 'F');
		$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
		$this->pdf->SetTextColor($this->pdf->rapport_kop_fontcolor['r'],$this->pdf->rapport_kop_fontcolor['g'],$this->pdf->rapport_kop_fontcolor['b']);
		$this->pdf->ln(2);
		$this->pdf->Cell(100,4, vertaalTekst("Mandaat controle per",$this->pdf->rapport_taal)." ".date("j",$this->pdf->rapport_datum)." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",$this->pdf->rapport_datum)],$this->pdf->rapport_taal)." ".date("Y",$this->pdf->rapport_datum),0,0);
		$this->pdf->ln(2);
		$this->pdf->ln();
		$this->pdf->SetTextColor(0,0,0);


		$this->pdf->SetAligns(array('L','R','R','R','R','R','R','R','R','R'));
		$this->pdf->SetFont($this->pdf->rapport_font,'B',$this->pdf->rapport_fontsize);
		$this->pdf->row(array('',vertaalTekst('Min',$this->pdf->rapport_taal),vertaalTekst("Norm",$this->pdf->rapport_taal),vertaalTekst("Max" ,$this->pdf->rapport_taal),
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

			$this->pdf->row(array(vertaalTekst($zorgplichtCategorie,$this->pdf->rapport_taal),$this->formatGetal($zorgData['Minimum'],1).'%',
												$this->formatGetal($zorgData['norm'],1).'%',
												$this->formatGetal($zorgData['Maximum'],1).'%',
												$this->formatGetal($verdeling[$zorgplichtCategorie]['percentage'],1).'%',
												$this->formatGetal($verdeling[$zorgplichtCategorie]['percentage']-$zorgData['norm'],1).'%'));

		}
	}
  
  function PieChart( $w, $h, $data, $format, $colors = null)
  {
    
    
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    $this->pdf->SetLegends($data, $format);
    
    $XPage = $this->pdf->GetX();
    $YPage = $this->pdf->GetY();
    $margin = 2;
    $hLegend = 2;
    $radius = min($w - $margin * 4 - $hLegend, $h - $margin * 2); //
    $radius = floor($radius / 2);
    $XDiag = $XPage + $margin + $radius;
    $YDiag = $YPage + $margin + $radius;
    if ($colors == null)
    {
      for ($i = 0; $i < $this->pdf->NbVal; $i++)
      {
        $gray = $i * intval(255 / $this->pdf->NbVal);
        $colors[$i] = array($gray, $gray, $gray);
      }
    }
    
    //Sectors
    $this->pdf->SetLineWidth(0.2);
    $angleStart = 0;
    $angleEnd = 0;
    $i = 0;
    $this->pdf->setDrawColor(255,255,255);
    foreach ($data as $val)
    {
      $angle = floor(($val * 360) / doubleval($this->pdf->sum));
      if ($angle != 0)
      {
        $angleEnd = $angleStart + $angle;
        $this->pdf->SetFillColor($colors[$i][0], $colors[$i][1], $colors[$i][2]);
        $this->pdf->Sector($XDiag, $YDiag, $radius, $angleStart, $angleEnd);
        $angleStart += $angle;
      }
      $i++;
    }
    if ($angleEnd != 360)
    {
      $this->pdf->Sector($XDiag, $YDiag, $radius, $angleStart - $angle, 360);
    }
    $this->pdf->setDrawColor(0,0,0);
    //Legends
    $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
    
    $x1 = $XPage + $w + $radius * .5;
    $x2 = $x1 + $hLegend + $margin - 12;
    $y1 = $YDiag - ($radius) + $margin;
    
    for ($i = 0; $i < $this->pdf->NbVal; $i++)
    {
      $this->pdf->SetFillColor($colors[$i][0], $colors[$i][1], $colors[$i][2]);
      $this->pdf->Rect($x1 - 12, $y1, $hLegend, $hLegend, 'DF');
      $this->pdf->SetXY($x2, $y1);
      if(strpos($this->pdf->legends[$i],'||')>0)
      {
        $parts=explode("||",$this->pdf->legends[$i]);
        $this->pdf->Cell(0, $hLegend, $parts[1]);
      }
      else
        $this->pdf->Cell(0, $hLegend, $this->pdf->legends[$i]);
      $y1 += $hLegend + $margin;
    }
  }
}
?>