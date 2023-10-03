<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/04/01 16:52:24 $
File Versie					: $Revision: 1.19 $

$Log: Factuur_L42.php,v $
Revision 1.19  2020/04/01 16:52:24  rvv
*** empty log message ***

Revision 1.18  2020/03/28 15:44:38  rvv
*** empty log message ***

Revision 1.17  2020/03/19 06:11:33  rvv
*** empty log message ***

Revision 1.16  2020/03/18 17:56:19  rvv
*** empty log message ***

Revision 1.15  2019/04/20 17:01:27  rvv
*** empty log message ***

Revision 1.14  2019/01/23 07:46:29  rvv
*** empty log message ***

Revision 1.13  2018/12/12 16:18:12  rvv
*** empty log message ***

Revision 1.12  2018/04/14 17:22:16  rvv
*** empty log message ***

Revision 1.11  2017/12/20 17:01:30  rvv
*** empty log message ***

Revision 1.10  2017/07/19 19:28:56  rvv
*** empty log message ***

Revision 1.9  2016/07/20 16:13:27  rvv
*** empty log message ***

Revision 1.8  2016/01/23 17:52:21  rvv
*** empty log message ***

Revision 1.7  2015/05/10 08:03:14  rvv
*** empty log message ***

Revision 1.6  2015/05/03 13:01:46  rvv
*** empty log message ***

Revision 1.5  2015/02/18 17:09:30  rvv
*** empty log message ***

Revision 1.4  2014/12/13 19:25:54  rvv
*** empty log message ***

Revision 1.3  2014/12/10 16:59:10  rvv
*** empty log message ***

Revision 1.2  2014/09/24 15:51:15  rvv
*** empty log message ***

Revision 1.1  2014/09/20 17:22:46  rvv
*** empty log message ***

Revision 1.6  2014/09/20 10:41:55  rvv
*** empty log message ***

Revision 1.5  2013/12/14 17:15:32  rvv
*** empty log message ***

Revision 1.4  2013/11/23 17:24:47  rvv
*** empty log message ***

Revision 1.3  2013/04/27 16:28:55  rvv
*** empty log message ***

Revision 1.2  2013/04/15 13:23:11  rvv
*** empty log message ***

Revision 1.1  2013/03/24 09:42:03  rvv
*** empty log message ***

Revision 1.10  2012/12/16 10:37:29  rvv
*** empty log message ***

Revision 1.9  2012/06/30 14:45:30  rvv
*** empty log message ***

Revision 1.8  2012/06/09 13:44:27  rvv
*** empty log message ***

Revision 1.7  2012/06/06 18:17:47  rvv
*** empty log message ***

Revision 1.6  2012/06/03 09:55:37  rvv
*** empty log message ***

Revision 1.5  2012/04/13 06:46:29  rvv
*** empty log message ***

Revision 1.4  2012/04/11 08:07:53  rvv
*** empty log message ***

Revision 1.3  2012/04/04 16:10:05  rvv
*** empty log message ***

Revision 1.2  2012/01/04 16:37:28  rvv
*** empty log message ***

Revision 1.1  2011/08/11 15:39:22  rvv
*** empty log message ***

Revision 1.7  2011/04/11 19:49:19  rvv
*** empty log message ***

*/
global $__appvar;
		$this->pdf->rapport_type = "FACTUUR";

		$font='Arial';
    $font='dinot';
    /*
    if(file_exists(FPDF_FONTPATH.'garmond.php'))
    {
  	    if(!isset($this->pdf->fonts['garmond']))
	      {
		      $this->pdf->AddFont('garmond','','garmond.php');
		      $this->pdf->AddFont('garmond','B','garmondb.php');
		      $this->pdf->AddFont('garmond','BI','garmondi.php');
	      }
        $font = 'garmond';
	   }
     */
		$this->pdf->AddPage('P');

//logo_aur.png
//$logo=$this->pdf->rapport_logo;
$logo=$__appvar['basedir'] . "/html/rapport/logo/logo_aur.png";

if(is_file($logo))
{
  $w=48;
  $this->pdf->Image($logo, $this->pdf->w/2-$w/2, 10, 48);
  //   $pdfObject->Image($pdfObject->rapport_logo,3,180, 48);
}
$this->pdf->SetFont($font,"",$this->pdf->rapport_fontsize);
/*
$beeld='iVBORw0KGgoAAAANSUhEUgAAAtwAAABFCAMAAABOgcH5AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoV2luZG93cykiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6QjhCNzNGQTRDRjI0MTFFOUE5NDZCOEY0N0JFODFDQTUiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6QjhCNzNGQTVDRjI0MTFFOUE5NDZCOEY0N0JFODFDQTUiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDpCOEI3M0ZBMkNGMjQxMUU5QTk0NkI4RjQ3QkU4MUNBNSIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDpCOEI3M0ZBM0NGMjQxMUU5QTk0NkI4RjQ3QkU4MUNBNSIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PmX2fbgAAAMAUExURf///8nO0La9wFphbufo6uzt7qSddlBYZcrO0cXIzKiss01VYuTl55CWnmFodNDS1VVdakRNW6ywtl1lcaqutM7Q1EhQXsnLz66yuNTW2X6EjkBJV292gD9IVo2SmqKmrUpTYEFKWHJ5g4eMlUVOW3R7hfT09XZ8hoyEU9XY2tHV1/7+/vn5+vDx8vn5+Z+jqvX19pOYoPf3+P39/b66nvv7+/b29/z8/MXBqfj4+fPz9PLy8/r6+re+wfr6+/j4+OXn6Pv7/L/Fx/P09Pz8/dnc3uLk5tDU1vf39+3u79LW2N/h47zCxfHx8r3Dxv39/ri/wtrd3+7v8Pb29tbZ29jb3dPX2dzf4eLk5e/w8eDi5MDGyevs7fDw8d7g4sTKzPj39MXKzbm/wrnAwurr7MzR08fMz+Hj5c3R1Ojp6rvBxLnAw8jNz7rAw83S1L7Ex8HHyunr7Ojp6+vs7rrBxNXZ2+Pl5ujq68vQ0sbLzvX19cLIyszQ0s7S1Nfa3NTY2s/T1ePl58PIy8TJzObo6b/FyMfMzvHy8+Dj5Nve4PLy9MLIy8fKzuTm593f4dfZ3O/v8bzDxcHHyenq66Sor+nq7Nfb3MzQ08jN0Li/wcTHy/7+/87S1a6xt3qAisLHyt/i47S4vd3g4f3+/urr7dna3c3R0/Hx89PW2eHj5OXm6PT19s/T1qOnru/v8I6TnM7T1cDGyMXLzebn6VNbaNba3MvN0Z+kq77Bxt7h49PV2eXn6fj5+ZmdpXd+iL7ExmVsd4GHkNvd3/r7++7u8ICGj2hvet/cztze4Nze4cHGycfJzY+UnKGlrLW5vra5v8bLzWlwe9bY29ve393g4t7f4rvCxb3AxcHEyc3Q0+Pk5srP0eLj5Xl/iZugp/Lz9HB3gdLU17y/xJ2iqff4+Li8wWxzfsXKzPv7+uLg1ImOl9zf4HyCjENMWvv7+be7wJ6iqtrYyKClq4uQmc3P09ja3ZSZobS3vXN6hE9XZGNqdmtyffX29m50f5ygqIOJki2nWw4AABMbSURBVHja7J15YBvFFcaFm5Q2FEiBQktQIUAfSLtWlhVaoZVlWY7PxE7sGOdwYqc5HZOLXA65bxICDgRICCSBEO67nC0ttJRCuVtK76b3faX3Qekpn5o3O7M7u9ZqRaP5K/GORqOd386+ee97Mz5foYiUUfxy48FPffqFPOpLbWG0CsVW8VuU4y8Y9kq+9OWEt2+8S7LdqhSVY2qTAt1FUWpickp6148amBRF0ZIlUYHfKFMflOx8C7OETNs3LZLNX2n6zaJAdZeTTtPyAu7u8p43iu00WRFKU60PLXu6sjkQCOytbLt/mZ4e/1jU/tiHHFRRafAMTaQM4xShq0ToGlEfVAU5JRCoPNy5LF1JK5EsfyD6ZNwIN/db2CUeMmvfrOxgw13l8JvtAJWeM4dF8wRuv//0Zx8QbG9/iQLxluCWhiKyJDZXjy0GRbY79iEHVWQYQ1YYaxxDeSc1Tq0xQxUdVWhPNwKBItPSsGVDGYAatviB6DPFRrgtvoUuxSGz9s1KkA13wOE32wPK739nV77A7fcPP0OouYgCnUcamDcjseCQDrLP1tiHHFRJweVkhSuhgq5Rs5XqWpXhLZlchipMUsSGfdyGKZCUCnALTZjX5A3cfv9D1ialpMKm20zuR/lhUCrchjsMixBvRpsDmql+rQC6ijYZVViqig77zEBciRbgFikjbskfuP0XWbUVVurnWdyRLcug1GW4fVCJahgMlzAcoXq1wDC7r0ZtJHRZfNi3rTQ+TgW4WeX8t8Q5vThT/ugG3P6zrdiunWp5S2a1Q4nLcNeUoRorVep6KYyjOnUZ3ScJqsnrl0PKxrA3tPPpLsCNPBXiTpMLM+ViV+AecbKpTaJMKBe5KW02JjZHcIfqUY2xTbQ9XWvo09Ak5fCBueTlRTDezrAn2iFagFuk/C1/4PYPf8akpaS+WeyuTIawq3BHoNzUoFbGGro0VqGbWExerlxtD7uG2YpUgFugHB2SP3D7H+I3FIWJgndlYa3mKtyUTb2WepYkGGPo0hhqoGU9QV4uq7GJ3fV6rAB3VtZxOYT7hGf4RsnKhOhtuYlrmGQFbh9cStZYQ9kIUdhi6NF0qk7yTnS5PmQXu0rO2+kYg5tkV55y7SlfMQAl5wxuqi8HDX3x386fc68WH5Ehiqtwa1XIOUetFmX9ZqMdoeObrLUjD2bPw2gLu+XxWAFuxsR8xhMUULd4Aje7L2dxJ26jITtz+tpgMDhnxnzDleO6vQ/uwR2bgKrUYs5qhjBGinKpYG/ikZ5pGA/7xECmBOdcb2jwZSY6AnB3BajyGHKl0lcN4fcAtzyGe3ipANwT+a0F4k7g9s0ejoH6qHdwG/oy8gc8KikvYGJemd4ny9C3bqQtlmk1bsJdCnVklS7NzA3eW65STDyBl4Jx2Iux9GRoMEEbOilncBtKAE+3zoVTeE4uj6s+S7iLBymcYpjUHxmBapzqIdy+lnOEXiMKjugVTV0CmhwNS5IUjsoaTFtDL+DGuwg35chrVPBy8woG3AdQhyrgSmN0nhp24nulcKQGtl6GW7wu5ghuC9rYtoRIUePb0FeXsR06/F+ZJaB8r2P3suoh3L7fY7h/wnGVHIe9BcUoBh1V4ngRt5sTyckO3BJ23GAgjCGcnocRrXEpT+CEmPWwR2APNr8OKXkFd4TyEQU5i3r34e4Yiapc6yXcd5yKqtzK9nE/jN7KiWnUtCA11WI1VZfmItyUJxtLpyiDvL/cQ7Yj7yQv1fUqBiyGPQqTTL2L3sItKZ8RMUpyAbfvVlTlW17C7fsTViqyrRI8sNWGCF2Y8oKPEVtvOYQ7OY0vnWpqIR7CzD/vJ582dTb5+bm9D4fVsMs4drSA6Qz0Cm41Xi5ilOQE7oOoyoc9hftD2DHJqlKBwyYNE4zrRXXKTKzWiLgIdwnM4kmnUAiHsL73AtcTOLEXKKthl2ADNnRS+QN3BIJCRklO4H4eVfmkp3B3YKObyaSO7M05DGlFGDsgBNdbDuGOwnSenw9dKqPdff0j3MyIzVsOO3avL2fKH72BW1LKxIySnMBdj6r8wVO4R2O4WZIg9QZ07zY1MepoQyzyA7IIN+XKI6VTsp4x/ucTa8txBIsSbER+y6TYsMdwggNzzewN3MJGSU7gTjkJwLsEt4ThHs0yuX9D3pH5usw0FeqMvmOX4KbWAKR0SiWesVWkNTUhRszuq0iRbh+llsMemoLhlvMF7gj11gya6DJzADeeLUcxW3npYqoQcL+Xvva7rM3czIlyI44/srJtwjCHrPQ+ofWWU7jVpTzpFDSSxvTezH/aNQKGcQzdiTXcxfkJt6R0iRolOYF7CKoyjNnKJRfaKB933pc9OPONaeLOIO/Ieuak7FvdaH+95RRurOojpFNhWJv5ewt0GZaNPZ+OY9eP5GTmbsgbs0S9Z7GoUZITuP8i4C3JFdzfRlWeYL72UFykk+3DVrfaH3uncOMkYUI6hcIztVBPesMHngB1D50cLDTs2AGZNwtKO0ZJTuAehqo85Sncp6EqP2SN2E6c1sIWxIVai4xhP5fgppKEM9IpMoRTBxqhiFmYeQK0HXRysJi3pD0fXYG2jBLDr4xJzDIouM9FVTo8hfttVOVEy5hJghNaLwWkvhiiugg3pY7KxEOb7sv8dR9EgEhonqYyPYGJ/uWxtZ97DF555EcQR60fJ26UiAqnpEHA/V2knDr9Di/h/sAIS0E3Njg+x3ntpeB6S19g1uDGScID0ikJ7kUOG4VYB7RlKn2CTg4WgTuk1+Vh+D2CfoyVUUJ/3YM29qoSBeoCATlHruDGSgDmWwRv8rGFkx0bxlq9tiY34cZJwgNIoKVv+vEiHYPVmUpv0snBAnCH4SrUsZa8EE5J0GnHKBFLVggOAu7wI36R6Htu4D4b9+U5pld5F/nT93EyrCitaaXiJtw4SXhAOkWGcIqmxNKrhQTxUKb6P7ubTg62hjuqLFuIOtaaF5JXm0aJ63BX/ZvaUGG2d3CP/jWViXMKE+5K7FUez5lENpompWcVbpwkPCCdIv3fi9N/TMFmo3Ed2m5IDrZYaoUjKtyJ/W1bHCYrZBfuUptGSbbhfmQ0UbZ//We/HUnx9FlfzuBGfRlddssjVKaC/xywhnsR56dLWLwTcBVuKkm4XzpF2thXpN8wKE7fv8TFYoL6kMhS64UAtUHiJIdpZlmFW4LJ9oySbMNtWU7OHdyWhS3hgvVCPx2rXje4CzdWMfVJp1CUtBmoOH1jnwGitRiSgy2WWnMMGyQudJognFW4a4p32zNKcg03b+L2Au7hPx3M/Reolj24cU5Cn3QKWeI99gap3L67b7GAntWMWtBWXnrA6dYO2YS7FObZNEpyDPfpj/Na+dUlVCG1JfS1X2YF7qd87xq4cZJwn4Efe5j42/buT5boGeX3tt6xl2AdU+BlB+5x25M+z+Eeb98oyTHcXxJ/UF3flOc/vBvypAOzZKK7ZglOEu6TTpE+y1d7UEa+weKYUSmTccfbgbsTJO/hrmm1bZTkFu7XffkD95l3cNrAC8p1ebGgxEnCvdIp9Ld1PU4dFFbcpBnn/IxKwAbcG4S31HIR7lK4ybZRklO4/xvOH7i/9wWfENxzuHBX5xBunCTcK51COu2vKoZqT4Ih/lOXkT+Jw303xHyewx2G++wbJbmE+8922HYZ7vO4bPuUl61FFd1wo+VNs+Iu3Fjw0iOdknUij7Ojd6yTQ+meIy343Iw2XRjuAzofo9zBrU2ps2+UZFtbwi8jh9mTEbgJ99GDZvcRqehmcMPvaG+Twy7DjZOEe6RT6srMH27uC9mUEHKu3mm66VARU+UtCHeiGUymyJzBXeLIKDHArcnMMnigXvPlD9ymh/OoSKW0jbMRILW76mTNXbhxknCPdIoM4fQrYCpgATW/o/APsXG3INxVYKaBzhXcYWhxYpRkW8/NL6flEdymL5Ek2uZjIUfySu0J/7jqLtzYxO+GIkzaRQMyKXI0W5q6HWiLjMnBNuBuBCkP4Nauu8yJUZI7uE/KI7hN+0KlIbSGOC/KhGVKQxbhxknC3dKpCBC75X2tf0om3YP3ptFBM/4s4kEVhPtq0/d/juAuoc60EjVKsg33F8nj1j+ILv0113CjvpwvvKVbKSwn78hW9hswho6iSeiyy3CjhWG3dCpG5jje2d9JMrDzZtpWQTkV5Kb0/M19kZS3aInmOdxhOOTMKHE1zexMy6wXV+FGffk81lSZtBHFG89fxd5aXttkndJAjX3rYODGScLxEJKbzB+YkskE0Fl6Cc5hryZY4vsR8K4tvB1scwi3Y6PEVbjfjzV4JV7CfQ3ui2w2USCl9gG2LxBv47SK6VMpBbR11dCkpQ3EP5MYJwmvVNF2Z5mvR+LY2apP7WAkB5sOeynem7xOlz2G27lR4ircqRPQtW96CXf0eHTtx2Z3BHG7mJn3TR3DxB6lUkCK/w7G25Q6uJoPN14Zjm1CIRzSxbcXwYyCP0tVgWGXsCqyaLLiLdxhaHNqlLib/X4iuvYxL+H2XYSunWv2GsQJ1ktqmNMJelW2KWyPyjarVK0mZN3UmRxIjE4BXgEyoZHq8YswOr8RxpOeQLQw4A+7ig+2PAJRT+HWJix3apS4C/dbQkk4uYH7LtyXpSbuknrqNciwSzSURdxtADCt91UW5qtkflQZWVCS8FpQljA3e0CdXwMlcDcjOdh82FN4U6LElKSXcMvUQQB2jBJ34ZZOtXMgtbtw+3Bf3uA3ksLxmYVTjORWYGXJN9jO8PE4RF9urFUKa0Ri/YZHbg2QCbzkhB8hTOZEXCN/yiLy4TIZdio32MTV7T7cgzJKXN6U5yF8OLadvAtCvv1SVvpyo2hfqOmUsfl8+l05E6urwgLWe9p8pb50P3VCwAbg/zQUNZoJpEx7H7GdIYrtPA6kW3MgOdhi2GOtCUFXt/twa7WDMEpchrsDbxPyoM/lYtaXPSOE8t26LYCVWF9BHxviU2FfkbXJbbDet9HTjqrj4y5bNLM5jFzC1sKjrBQEH47KV0IrKznYYtjpI4GWaJ7BLZNygqLuA21LJV4RgDsmSbY+bpFxjnPfz/MSbt9Zon0pRW43w4FPFRq1X11DPUcUGorjE1CP6BoxxYdrUJZM7/YMJlyQ75OuVt6OQOTmJVdABys52GpOU1oEXd1uwx2GXbQ8lV+sz6HM9lF9t+MsM81LuL+M+/IvE7ukEd9T8qi+Eg1q8XSSZqiCFw+iaq6q7Wso3U4NXIdnSN4GCv2vATJJuLGK90yQm5eUkxZrOTIvzOAO7Zwv5up2G24Z+1KLim4L8soOAbgfDPKLk0NW4XynaWbZh/sB3Je/85uJ3UMdw0gesjqkmtr3oKiL6wpePYmqOnPMkv6pYtpEauTSVoTZWKMk4c0z8JYl5HJ4M0HPL1jJwVZwh+GAmKvbfbiF04U8OR77RXT5517CTYXg/2n2Omw2/Hr+8dhb+N7pUHy5oXrd3I3B4MYFuw0XGlqTZr+NOkkYb1lCvnaq2dXw6Q+mS62mLjFX97EO92tYj/cdL+HGIXh/lcksGV8snj87WdnPf0pWFNkYnwqz30adJExtWUJYzJM40mxNGO4SvEU519V9rMOd+ge6/n0v4aZC8M/y25GUTcK39TiTsKIvGR8n2s7yVnMXLnWSMMcLQh87yUoOtoR7PHVAL8/VfazDTYW9nwh7CLeNvkTI3YFNy8KhmimRnaK3twUs7g0+SZjasoSYdvVZrFpUaN/cA6zdIKTqPubhvhbbAmd4Cfdd4tlmSX2zoFFizmSEighxyxizF0Bvl6YxP/gqRR51pg8jOdgabkoayHN1H/NwU2HvF72Em+rLmWZTrjKhXOSmtFlpHWLUcbecsg6SVj+OOkkYb1lCvizGsKpNxKNvDjctDeS4ugtw4xD80e1ewo1D8EfrzQIISu1Uy1syvx0sVepJaExYNrQerDUT1EnC1JYlVubLWMVOYJqSBnJc3QW4qRD8QS/hpkLwp5iGx5T6eRZ3ZPoyS1uiZ4g6Ljdv59EyEJA9cJx8Bpk4uXkJMzlYAG5KGshxdRfgpkLwz3kJNxWCf8ccJhU23WZyP8oPg1Ih0quoojeaOE3qmnVFSMfJdPLdbJhUKa25MTlYAG5aGsh2dRfgpkLw/le8hBuH4P3PmzcXUaDzSAPzZiQWtOggC3ZLCoHediX7rj42KQ4xMakbPkl4IIQUNTjEFzBeMlQ16CJyggPG4HOslbweWM9cEsiAKsUF4EZfW2UJd0C0sJvCX2dWOOF3Msd8FMsZAqjGqB+5CLdlXx6w15f9JQrEdwS3YMATm6vH1oMi21BfSiEFJrQtmoqs78Sjc3bVghITbUfWGaNyn3FUodNYrZ2qZiUbiooIk2TxfcnYX2sJt40iCXydbeHU/3mpSGMJ+tCypyub04w0V7bdv0yHNJFRuw2lYt0NLencVbkiEFhROanqhni6nWTKRgvMUTFaw5pYtUIplPS0G5VjapPSy4hSE5NTkrOGwhFZ1RSltx1NlSNhr37S/wQYANmCOUbPQfz+AAAAAElFTkSuQmCC';
$this->pdf->MemImage(base64_decode($beeld),135,20, 60);
$this->pdf->Ln(15);
$this->pdf->SetWidths(array(160,50));
$this->pdf->SetAligns(array('L','L'));
$this->pdf->row(array('','Dorpstraat 162 A'));
$this->pdf->Ln(1);
$this->pdf->row(array('','5504 HM  VELDHOVEN'));
$this->pdf->Ln(1);
$this->pdf->row(array('','Postbus 251'));
$this->pdf->Ln(1);
$this->pdf->row(array('','5500 AG  VELDHOVEN'));
$this->pdf->Ln(1);
$this->pdf->row(array('','Nederland'));
$this->pdf->Ln(6);
$this->pdf->row(array('','tel. +31 (0)40 255 74 42'));
$this->pdf->Ln(1);
$this->pdf->row(array('','fax +31 (0)40 255 44 17'));
$this->pdf->Ln(6);
$this->pdf->row(array('','www.ritzerenrouw.nl'));
$this->pdf->Ln(1);
$this->pdf->row(array('','info@ritzerenrouw.nl'));
*/
		
    if(isset($this->waarden['periodeDagen']['periode']) && $this->waarden['periodeDagen']['periode'] <> '')
    {
      $parts=explode('->',$this->waarden['periodeDagen']['periode']);
      $vanjul=db2jul($parts[0]);
      $totjul=db2jul($parts[1]);
    }
    else
    {
      $vanjul=db2jul($this->waarden['datumVan']);
      $totjul=db2jul($this->waarden['datumTot']);
      if(substr($this->waarden['datumVan'],5,5) != '01-01')
		    $vanjul+=86400;
    }
   	$vanDatum=date("j",$vanjul)." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",$vanjul)],$this->pdf->rapport_taal)." ".date("Y",$vanjul);
    $totDatum=date("j",$totjul)." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n",$totjul)],$this->pdf->rapport_taal)." ".date("Y",$totjul);
    $nu=date("j")." ".vertaalTekst($this->pdf->__appvar["Maanden"][date("n")],$this->pdf->rapport_taal)." ".date("Y");

   $this->DB = new DB();
   $extraVeldSelect='';
/*
   $query = "desc CRM_naw";
   $this->DB->SQL($query);
   $this->DB->query();
   $extraVelden=array('IBANAAB');
   while($data=$this->DB->nextRecord('num'))
   {
     if(in_array($data[0],$extraVelden))
        $extraVeldSelect.=','.$data[0];
   }

*/
   $query = "SELECT
CRM_naw.naam,
CRM_naw.naam1,
CRM_naw.adres,
CRM_naw.pc,
CRM_naw.plaats,
CRM_naw.land,
CRM_naw.verzendPaAanhef,
Portefeuilles.selectieveld1,
Portefeuilles.BetalingsinfoMee $extraVeldSelect
FROM CRM_naw Join Portefeuilles on CRM_naw.Portefeuille= Portefeuilles.portefeuille WHERE CRM_naw.Portefeuille = '".$this->portefeuille."'  ";
	  $this->DB->SQL($query);
	  $crmData = $this->DB->lookupRecord();
    $portefeuilleData=$crmData;
    $selectieVeld=substr($portefeuilleData['selectieveld1'],0,3);

    $extraMarge=20;
		$this->pdf->SetY(55-8);
		$this->pdf->SetWidths(array($extraMarge,100,80));
    $this->pdf->SetFont($font,"",10);
		$this->pdf->SetAligns(array("L","L","L"));
		$this->pdf->row(array('',$crmData['naam']));
    $this->pdf->ln(1);
		if (trim($crmData['naam1']) !='')
    {
		  $this->pdf->row(array('',$crmData['naam1']));
      $this->pdf->ln(1);
		}
		if (trim($crmData['verzendPaAanhef']) !='')
    {
		  $this->pdf->row(array('',$crmData['verzendPaAanhef']));
      $this->pdf->ln(1);
		}
    $this->pdf->row(array('',$crmData['adres']));
    $this->pdf->ln(1);
		$plaats='';
    $plaats=$crmData['pc'];
    if($crmData['plaats'] != '')
      $plaats.="  ".$crmData['plaats'];
    $this->pdf->row(array('',$plaats));
    $this->pdf->ln(1);
    $this->pdf->row(array('',$crmData['land']));

    $extraMarge=40-$this->pdf->marge;
    $this->pdf->SetY(105);
		$this->pdf->SetFont($font,"",8);
    $this->pdf->row(array('    datum'));
    $this->pdf->ln(1);
    $this->pdf->row(array('    betreft'));
    $this->pdf->SetY(105);
    $this->pdf->SetFont($font,"",10);
    $this->pdf->row(array('',$nu));
    $this->pdf->ln(1);
    $this->pdf->row(array('',"factuurnummer ".sprintf("%06d",$this->waarden['factuurNummer'])));

		$this->pdf->SetY(125);
    $this->pdf->Rect($this->pdf->marge+$extraMarge,122,210-2*($this->pdf->marge+$extraMarge),12,'F',null,array(240,240,240));
    $this->pdf->Rect($this->pdf->marge+$extraMarge,155,210-2*($this->pdf->marge+$extraMarge),85,'F',null,array(240,240,240));
    $extraMarge=42-$this->pdf->marge;
    $this->pdf->SetWidths(array($extraMarge,210-($this->pdf->marge+$extraMarge)*2));
		$this->pdf->SetFont($font,"B",11);
    $this->pdf->SetAligns(array('L','C'));
    $this->pdf->row(array('',"Factuur"));
    $extraH=2;
    $this->pdf->ln(4);
    $this->pdf->SetFont($font,"",10);
    $this->pdf->SetWidths(array($extraMarge,210-2*($this->pdf->marge+$extraMarge)));
    $this->pdf->SetAligns(array("L","L",'L','L'));
    //$this->pdf->Rect($this->pdf->marge+11,$this->pdf->GetY()-1,165+2,4+2);
    $beginY=$this->pdf->GetY();
    $this->pdf->ln(6);
    $this->pdf->row(array('',"Vergoeding voor het door ons gevoerde vermogensbeheer over de periode"));
    $this->pdf->ln(2);
    $this->pdf->row(array('',"$vanDatum tot en met $totDatum."));
    $this->pdf->ln(6);
    $this->pdf->SetWidths(array($extraMarge,210-2*($this->pdf->marge+$extraMarge)-40,5,25));
    $this->pdf->SetAligns(array("L","L",'R','R'));
    $this->pdf->ln(2);
    //$liqTotaal=$this->waarden['waardeVerdeling'][$this->portefeuille]['eindWaarde']['totaal']-$this->waarden['portefeuilleVerdeling']['eindWaarde'][$this->portefeuille];
    if($this->waarden['waardeVerdeling'][$this->portefeuille]['eindWaarde']['totaal'] <> 0)
      $this->waarden['totaalWaarde']=$this->waarden['waardeVerdeling'][$this->portefeuille]['eindWaarde']['totaal'];
    
    $this->pdf->row(array("","Omvang beheerd vermogen per $totDatum:","€",$this->formatGetal($this->waarden['totaalWaarde'],2)));
    $this->pdf->ln(8);
    $this->pdf->SetWidths(array($extraMarge,210-2*($this->pdf->marge+$extraMarge)));
    $this->pdf->row(array("","Vergoeding conform afspraak overeenkomst tot vermogensbeheer."));
    $this->pdf->ln(4);


    //    listarray($this->waarden);
    $restWaarde=$this->waarden['totaalWaarde'];    
    $minBedragGebruikTxt='';   
    if($this->waarden['MinJaarbedragGebruikt'])
    {
      $this->pdf->SetWidths(array($extraMarge,210-2*($this->pdf->marge+$extraMarge)-45,10,25));
      $this->pdf->SetAligns(array("L","L",'R','R','R'));
      $minimumBedragGebruikTxt='Minimum tarief van toepassing';
      $this->pdf->row(array("","$minimumBedragGebruikTxt","€",$this->formatGetal($this->waarden['beheerfeeBetalen'],2)));//+($this->waarden['huisfondsFeeJaar']*$this->waarden['periodeDeelVanJaar'])
      $this->pdf->ln(2);  
    }
    elseif($this->waarden['BeheerfeeMethode']==3 && $this->waarden['BeheerfeePercentageVermogen']==0)
    {
       $this->pdf->SetWidths(array($extraMarge,210-2*($this->pdf->marge+$extraMarge)-45,10,25));
      $this->pdf->SetAligns(array("L","L",'R','R','R'));
      $this->pdf->row(array("","Vaste afspraak van toepassing.","€",$this->formatGetal($this->waarden['beheerfeeBetalen']-$this->waarden['administratieBedrag'],2)));//+($this->waarden['huisfondsFeeJaar']*$this->waarden['periodeDeelVanJaar'])
      $this->pdf->ln(2);       
    }
    else
    {
      $this->pdf->SetWidths(array($extraMarge,210-2*($this->pdf->marge+$extraMarge)-90,5,25,20,5,25));    
      $this->pdf->SetAligns(array("L","L",'R','R','R','R','R'));
    //listarray($this->waarden);
      foreach($this->waarden['staffelWaarden'] as $regel=>$staffelData) 
      {
        $deel=$staffelData['feeDeel']/$staffelData['waarde'];
        
        if(round($deel,5)==round($deel,4))
          $decimalen=2;
        else
          $decimalen=3;  
        $this->pdf->row(array("","        ".$this->formatGetal($deel*100,$decimalen)."% over:","€",$this->formatGetal($staffelData['waarde'],2),'',"€",$this->formatGetal($staffelData['feeDeel'],2)));
        $this->pdf->ln(2);  
        $restWaarde-=$staffelData['waarde'];
      }

      if($this->waarden['BeheerfeeLiquiditeitenPercentage'] <> 0)
      {
        $deel=$this->waarden['periodeDeelVanJaar'];
        $this->pdf->row(array("","        ".
        $this->formatGetal($this->waarden['BeheerfeeLiquiditeitenPercentage']*$deel,2)."% over:",
        "€",$this->formatGetal($this->waarden['rekenvermogenLiquiditeiten'],2),
        '',
        "€",$this->formatGetal($this->waarden['rekenvermogenLiquiditeiten']*$this->waarden['BeheerfeeLiquiditeitenPercentage']*$deel/100,2)));
        $restWaarde-=$this->waarden['rekenvermogenLiquiditeiten'];
      }
      if(round($restWaarde) <> 0)
      {
         $this->pdf->row(array("","        ".$this->formatGetal(0,2)."% over:","€",$this->formatGetal($restWaarde,2),'',"€",$this->formatGetal(0,2)));
      }      
      
    }
   
    $this->pdf->SetWidths(array($extraMarge,210-2*($this->pdf->marge+$extraMarge)-45,10,25));

    foreach($this->waarden['extraFactuurregels']['regels'] as $regel)
    {
       $this->pdf->row(array('',"        ".vertaalTekst($regel['omschrijving'],$this->pdf->rapport_taal), "€", $this->formatGetal($regel['bedrag'], 2)));
    }

    if($this->waarden['administratieBedrag'] <> 0)
    {
      $this->pdf->ln($extraH);
      $this->pdf->row(array("","        Administratievergoeding","€",$this->formatGetal($this->waarden['administratieBedrag'],2)));
if($this->waarden['MinJaarbedragGebruikt'])
{
  $this->waarden['beheerfeeBetalen'] += $this->waarden['administratieBedrag'];
  $this->waarden['beheerfeeBetalenIncl'] = $this->waarden['beheerfeeBetalen'] * (1 + $this->waarden['btwTarief'] / 100);
  $this->waarden['btw'] = $this->waarden['beheerfeeBetalenIncl'] - $this->waarden['beheerfeeBetalen'];
}
    }

  
   $this->pdf->SetAligns(array("L","R",'R','R','R'));
   $this->pdf->ln($extraH);
   $this->pdf->CellBorders = array('','','','T');
   $this->pdf->row(array("","","€",$this->formatGetal($this->waarden['beheerfeeBetalen'],2)));//+($this->waarden['huisfondsFeeJaar']*$this->waarden['periodeDeelVanJaar'])
   unset($this->pdf->CellBorders);   
   $this->pdf->ln($extraH);
   $this->pdf->CellBorders = array('','','','U');
   $this->pdf->row(array('',"BTW (".$this->formatGetal($this->waarden['btwTarief'],1)."%)",'€',$this->formatGetal($this->waarden['btw'],2)));
   $this->pdf->ln(6);
   //$this->pdf->SetY(185);
   //$this->pdf->Rect($this->pdf->marge+11,$this->pdf->GetY()-1,165+2,4+2);
   unset($this->pdf->CellBorders);
   $this->pdf->row(array('',"Factuurbedrag",'€',$this->formatGetal($this->waarden['beheerfeeBetalenIncl'],2)));
   
    $this->pdf->ln($extraH);
    $this->pdf->SetAligns(array("L","L",'R','R','R'));
    
    $this->pdf->SetWidths(array($extraMarge,210-2*($this->pdf->marge+$extraMarge)));
   if($this->waarden['FactuurMemo'] <> '')
   {
    $this->pdf->row(array('',$this->waarden['FactuurMemo']));
    $this->pdf->ln(6);
   } 
   $this->pdf->SetY(250);

   if($this->waarden['BetalingsinfoMee']==1)
     $this->pdf->row(array('',"Gelieve het factuurbedrag binnen 14 dagen op ons rekeningnummer\nNL59 INGB 0683 8522 56 over te maken."));
   else
     $this->pdf->row(array('',"Het factuurbedrag wordt automatisch na 5 werkdagen van uw rekeningnummer ".$this->waarden['IBAN']." geïncasseerd."));

   $this->pdf->ln(3);
   //$this->pdf->Rect($this->pdf->marge+$extraMarge,$beginY,210-($extraMarge+$this->pdf->marge)*2,$this->pdf->GetY()-$beginY);

$Y=289;
//$breakHeight=$this->pdf->PageBreakTrigger;
//$this->pdf->PageBreakTrigger=297;
/*
$this->pdf->setXY(130,$Y);
$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_voetfontsize);
$this->pdf->Cell(100,4,$this->pdf->rapport_voettext,0,0,'L');
$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
$this->pdf->setXY($this->pdf->marge,$Y);
$this->pdf->Cell(80,4,$this->pdf->portefeuilledata['Client'],0,0,'L');
$this->pdf->setXY(0,$Y);
*/

/*
$this->pdf->SetY(257);
$this->pdf->SetWidths(array(160,50));
$this->pdf->SetAligns(array('L','L'));
$this->pdf->SetFont($font,"",7);
$this->pdf->SetTextColor(140,132,83);

$this->pdf->row(array('','KVK Eindhoven 17130897'));
$this->pdf->Ln(1);
$this->pdf->row(array('','BTW NL 809575759B01'));
$this->pdf->Ln(1);
$this->pdf->row(array('','IBAN NL59INGB0683852256'));
$this->pdf->Ln(1);
$this->pdf->row(array('','BIC INGBNL2A'));
*/

$this->pdf->AutoPageBreak=false;
$this->pdf->SetY(277);
$this->pdf->SetWidths(array(10,200));
$this->pdf->SetAligns(array('L','L','L','L','L'));
$this->pdf->SetFont($this->pdf->brief_font,'B',8);
$this->pdf->SetTextColor(151,151,151);
$this->pdf->SetWidths(array(15,60,55,55));
$this->pdf->rowHeight=4;
$this->pdf->row(array('','Auréus Group BV','Website',''));
$this->pdf->SetFont($this->pdf->brief_font,'',8);
$this->pdf->ln(-4);
if($selectieVeld=='LAN')
{
  $this->pdf->row(array('', '', '', 'IBAN: BE20735028350256'));
  $this->pdf->row(array('', 'Europaplein 13', 'www.aureus.eu', 'BTW: BE 0842.091.840'));
  $this->pdf->row(array('', 'BE-3620  Lanaken', 'info@aureus.eu', 'Vergunninghouder FSMA/AFM'));
}
else
{
  $this->pdf->row(array('', '', '', 'IBAN: NL61ABNA0421423528'));
  $this->pdf->row(array('', 'Piet Heinkade 55', 'www.aureus.eu', 'BTW: NL811109343B01'));
  $this->pdf->row(array('', '1019 GM Amsterdam', 'info@aureus.eu', 'KvK: 14073764'));
}
$this->pdf->row(array('','','','',));
$this->pdf->AutoPageBreak=true;
$this->pdf->SetFillColor(82,83,90);
$this->pdf->rect(0,$this->pdf->h-5,$this->pdf->w/2,5,'F');
$this->pdf->SetFillColor(132,149,164);
$this->pdf->rect($this->pdf->w/2,$this->pdf->h-5,$this->pdf->w/2,5,'F');
$this->pdf->SetTextColor(0,0,0);


?>
