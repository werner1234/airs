<?php
/*
Author  						: $Author: rvv $
Laatste aanpassing	: $Date: 2020/05/30 15:30:39 $
File Versie					: $Revision: 1.54 $

$Log: RapportHUIS_L68.php,v $
Revision 1.54  2020/05/30 15:30:39  rvv
*** empty log message ***

Revision 1.53  2020/04/22 15:40:47  rvv
*** empty log message ***

Revision 1.52  2020/04/01 16:54:36  rvv
*** empty log message ***

Revision 1.51  2020/03/28 15:45:39  rvv
*** empty log message ***

Revision 1.50  2020/03/04 16:40:47  rvv
*** empty log message ***

Revision 1.49  2019/12/14 17:46:24  rvv
*** empty log message ***

Revision 1.48  2019/11/20 16:19:15  rvv
*** empty log message ***

Revision 1.47  2019/09/28 17:20:17  rvv
*** empty log message ***

Revision 1.46  2019/09/18 14:52:23  rvv
*** empty log message ***

Revision 1.45  2019/08/21 10:41:17  rvv
*** empty log message ***

Revision 1.44  2019/07/13 17:49:46  rvv
*** empty log message ***

Revision 1.43  2019/05/26 09:48:00  rvv
*** empty log message ***

Revision 1.42  2019/05/25 16:22:07  rvv
*** empty log message ***

Revision 1.41  2019/05/15 15:32:37  rvv
*** empty log message ***

Revision 1.40  2019/05/11 16:48:39  rvv
*** empty log message ***

Revision 1.39  2019/05/01 15:53:25  rvv
*** empty log message ***

Revision 1.38  2019/02/09 18:40:17  rvv
*** empty log message ***

Revision 1.37  2019/01/23 16:27:16  rvv
*** empty log message ***

Revision 1.36  2019/01/19 13:54:10  rvv
*** empty log message ***

Revision 1.35  2019/01/05 18:38:35  rvv
*** empty log message ***

Revision 1.34  2018/12/23 08:53:45  rvv
*** empty log message ***

Revision 1.33  2018/12/21 17:49:27  rvv
*** empty log message ***

Revision 1.32  2018/12/15 17:51:04  rvv
*** empty log message ***

Revision 1.31  2018/12/15 17:49:14  rvv
*** empty log message ***

Revision 1.30  2018/12/14 16:43:21  rvv
*** empty log message ***

Revision 1.29  2018/12/12 16:19:08  rvv
*** empty log message ***

Revision 1.28  2018/12/01 19:51:30  rvv
*** empty log message ***

Revision 1.27  2018/11/28 13:18:46  rvv
*** empty log message ***

Revision 1.26  2018/11/24 19:11:26  rvv
*** empty log message ***

Revision 1.25  2018/11/03 18:45:31  rvv
*** empty log message ***

Revision 1.24  2018/09/26 15:53:28  rvv
*** empty log message ***

Revision 1.23  2018/09/01 16:53:24  rvv
*** empty log message ***

Revision 1.22  2018/08/25 17:11:27  rvv
*** empty log message ***

Revision 1.21  2018/08/18 12:40:14  rvv
php 5.6 & consolidatie

Revision 1.20  2018/08/01 17:56:09  rvv
*** empty log message ***

Revision 1.19  2018/07/29 10:55:26  rvv
*** empty log message ***

Revision 1.18  2017/09/09 18:01:12  rvv
*** empty log message ***

Revision 1.17  2017/09/06 16:31:45  rvv
*** empty log message ***

Revision 1.16  2017/04/12 15:38:14  rvv
*** empty log message ***

Revision 1.15  2017/03/29 15:57:04  rvv
*** empty log message ***

Revision 1.14  2017/03/23 11:44:51  rvv
*** empty log message ***

Revision 1.13  2017/03/22 16:53:22  rvv
*** empty log message ***

Revision 1.12  2017/03/15 16:36:10  rvv
*** empty log message ***

Revision 1.11  2017/02/25 18:02:28  rvv
*** empty log message ***

Revision 1.10  2017/02/08 16:23:32  rvv
*** empty log message ***

Revision 1.9  2017/01/18 17:02:28  rvv
*** empty log message ***

Revision 1.8  2016/12/18 13:19:51  rvv
*** empty log message ***

Revision 1.7  2016/11/19 19:03:08  rvv
*** empty log message ***

Revision 1.6  2016/11/03 06:30:46  rvv
*** empty log message ***

Revision 1.5  2016/11/02 16:34:11  rvv
*** empty log message ***

Revision 1.4  2016/10/02 12:38:58  rvv
*** empty log message ***

Revision 1.3  2016/09/25 18:53:45  rvv
*** empty log message ***

Revision 1.2  2016/09/18 08:49:02  rvv
*** empty log message ***

Revision 1.1  2016/09/11 08:30:02  rvv
*** empty log message ***

Revision 1.4  2016/05/29 13:26:30  rvv
*** empty log message ***

Revision 1.3  2016/05/21 19:00:02  rvv
*** empty log message ***

Revision 1.2  2016/05/15 17:15:00  rvv
*** empty log message ***

Revision 1.1  2016/05/08 19:24:24  rvv
*** empty log message ***

Revision 1.2  2015/12/16 17:06:48  rvv
*** empty log message ***

Revision 1.1  2015/09/05 16:48:04  rvv
*** empty log message ***



*/

include_once("rapportRekenClass.php");
include_once($__appvar["basedir"]."/html/rapport/rapportVertaal.php");
include_once("rapport/include/ATTberekening_L68.php");
include_once($__appvar["basedir"]."/html/rapport/rapportSDberekening.php");


class RapportHUIS_L68
{
	function RapportHUIS_L68($pdf, $portefeuille, $rapportageDatumVanaf, $rapportageDatum)
	{
		$this->pdf = &$pdf;

		$this->pdf->rapport_type = "HUIS";
		$this->pdf->rapport_datum = db2jul($rapportageDatum);
    $this->pdf->rapport_datumvanaf = db2jul($rapportageDatumVanaf);
		$this->pdf->rapport_titel = "Vermogensbalans";

		$this->portefeuille = $portefeuille;
		$this->rapportageDatumVanaf = $rapportageDatumVanaf;
		$this->rapportageDatum = $rapportageDatum;
		$this->att=new ATTberekening_L68($this);
		$this->checkImg=base64_decode('iVBORw0KGgoAAAANSUhEUgAAADAAAAAwCAMAAABg3Am1AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyZpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNi1jMDY3IDc5LjE1Nzc0NywgMjAxNS8wMy8zMC0yMzo0MDo0MiAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENDIDIwMTUgKFdpbmRvd3MpIiB4bXBNTTpJbnN0YW5jZUlEPSJ4bXAuaWlkOkY3QkI0NkM0OTU5MTExRThCNUQ4QTdFOTc2OENEOEExIiB4bXBNTTpEb2N1bWVudElEPSJ4bXAuZGlkOkY3QkI0NkM1OTU5MTExRThCNUQ4QTdFOTc2OENEOEExIj4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6RjdCQjQ2QzI5NTkxMTFFOEI1RDhBN0U5NzY4Q0Q4QTEiIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6RjdCQjQ2QzM5NTkxMTFFOEI1RDhBN0U5NzY4Q0Q4QTEiLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz5IXOqgAAADAFBMVEUzsRn29vZFtjIAjQIAmACl1aJ7e3tNiE3d3d35+fkAbSz+/v5Zd1lyy1MAbDP7+/tsxVwXkBfC4cHH1cc6sij09PS7u7sAlAANkgdZwEOd05Ty8vIUogqIiIgAcytKqUqEk4TV1dVhyTG65KZXtFZGd0aR2m08ripvum/w8PAAkQDg4OCoqKgwmzBra2suph9jxEvt8u1dv01zy1uo2ZlTp1OD02UkqhEqmirt7e1KvSRFuiEXdBd5tnkKnQSb3XoqrRQAcSQAeiNUwinr7OuKxYfm5uZeaF5SwSgfgB+9071CuSA6oTqN2GlkaWR6pHoxdjF60k7k5OSL1mxyz0dcxi1ZxSwAawUVbhWazJo+tx4Abxt80F06tRwAghVcxDeam5ojqRECfQICcwMEhgQEmgFMviXq7urT3dNtyVTa2tro6OjAwMCwy7AAghourxaWlpZkyzJUvTxpmmlpyUUMngbq6uri4uKA01wppRsAchVkyzSS2XKUzI1KujEkoxmU23Edpw54zlwAfRvN5crD6bANlwcAbQ1vzj+JoYkapQ0AeRMAhg7Ly8tgw0QAdAyJ1mYAigl4zWAXmg2Y3HYHnAMCihMGkhDx9PB+0GMAdhdrx1ZmxVIBkAoSgBLE6rHn6uaW3HPd4d0LkwrG7LSG1GwCZwJ2hXaV3HIAkwfv8+8AbzAPoAdowVppzThWwyoaog1IvCPI7LUAfAhAuB83tBtexy4IkQYKfgoAdycnqxIOjA78/PxmzDP9/f1QwCdHvCNYxCtPwCc/uB8XpAsorBMwsBcgqA8vsBcRoQhgyC8fqBAInAO4uLid2IUIlgOlxKX4+/genRc+gz7b7ts1rxpEszDj5uMFjwNkcGQxqyEPhg8NkAdopmgfoxPe8N53zlWqsqomiiYnqhxozDZHvCQ3sh1Ctynx+PHA5q+AxICXtpfg4+CGzXmNsI2ZrJnT49PT69Pa59bj7t4mqxVxm3Fjul+2u7YAgCEDihkhcCE/tDMeoA/p6elRvEH///9r2XbkAAABAHRSTlP///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////8AU/cHJQAABaxJREFUeNqUlnlUE1cUhzMEAQMhiEQKSNBIgmHYFCHEKIaAwRbBFAQiCkIWXEAW27iACwiCoIIWRHA5Wqt4MCBalJh0CIIsIlpb2lJEamMXba3dW2u1St+bCQHEpf39mfN99907mffekAb/Z0hjfvl03HSXhZ6eC12mL/kF0b1K+PJeQF+Zt1uvSa+bd9mAPGDuyvEvE351VW9dq9+wYdk2PXWVj4+DycYBud9E5EXCd9P7+vXh4Xv2+PtXLfOl+pQ4ODzd0j8g/+LY+OcK+QFnqGw8Ufq3l7deDIuLP0+6Y3fnlvyrNRzdWGGcujecnZPzbvmexY0twa3JhJAu0P7RPXDhpxDsWWGJ2oEN6PLwxZlXoluCm4FwiRCScosLpItEtNGCo9oH8PPK32xqymTiwlk+FC7jQluBtEiEjRRs1SWg/Dz2jqbnC13qCz+v0w0Lp3p62YAP/6YJCmNbapuslL/+F2dYcO1h54D6TTiPC8NDc7UCQWeXcr/UfCoyJNiqqezyeTmnCZ7ZGFO/oHU2nxC0M23zHbek1xZUyIoidQbBpT8cLLBjiDeMAARSh7buKI/H+60k/erfDZ+k0gkhv48atal8QyYe5pVouIBhhPa6FRyY/OauigrZFFSHCw+2+kdt2sTEc6UR8GACoiPIO8Fw9Nz9aQ3mqQgUPp6wFgiLoxthomNa6kFDgL8Ud15QN8OJjsdJrz38MGFOYgoUbPuo/lFRMS0xMC319QsAf5F/DvA2M+h0BM+0aGXNwzTZydU0INwr2+Z/Qh8cXA8THAz6BzxoKN3mFGLI90yuouZhXoMV6Ik06Oe9rerE8tbmBRc/u9Pa3Jps5GcgyHg8gN9nWiMUlrJu84AQ4OZbVZWcfPYfd88eJT95NnigOF+J4zQatp3ZflVhelgo3CnOEAFhQq/vO1R+WIHnG/ePudZe4vPDzg3xNAzTWR5htgfuA4JGs3OOMwqED0xW+ZaElS1ciaLr77u2xZ2Li4u/bFMJqgPa0vJIpkAJFjCt0GgOSMwsoNB70Hdt+nsTLdD1kaLPH3TGx5/vgDyN4LMFSriAfRoDCqthS26HDm4542eBQiHlaF17Rwfsh4ZZEnytEvCm9kKjEODtc+juGZdjKBopEj0JCRn3oyPsn+AzAQ8bsndnMBhZYlxw6Znpc1egmI8vsC6Ex3Giw3lhP+/D+oH4AhUqBqOUhQ89d+DpTLt0bu18QoC8oaERvLtQpWLEWmWsB8Ia+WMHO4GA27YZdsQzCLpRvD0Z8EGxNzzgH7fi5sandlptOzdpc8qToY7AAsdH8O55KhUlSxIBdgRpkOY3YGKXxAUROBpH0I3mySpriiqWZTYVvnyDa6T9j3O7uzs7uSRHHiHQdMez25VG3l1obU0Jqo5IFOEbiOP5ml13cXFubi43bhYQ4AhHsrXKwKsKnLcn7wI8pVDsBfcoEHQfSW993YYn6dtZuLA9m0uUx+vvVQFhaXWos2GLDlaay7dOxtOV1Pzh7/TKadlcgBt4MhnylFiW1yQnw6mBTbypbqslkjubueN0S6cRJ+pbUwoloRkoNnSQIT9IryuJBAZ2FU8mcJwn78J50JDZbmT4bOUtkl7fR0ShIGiiPDnPmuCtvK7xRhzGWORbUneFkcWfDcQTGBDHeQ+RbuRxT0P/lJHtjbARpwCcUlhtFeGB0kZfKDS0aI507+GaIXhvAnjdVICmLI2VhHoZ+eEriya6bS5rSEhLSEhLyxNqwOvPoAQBvLCaFWp2zXgBjbgUsZDdU8Sy0p15eUKhRqNhBIEAXBzqlbGbgz33nkbQSVPMJTJZaemBA1lZWYWx1RJWhJfzJBR50cWOOVmkJp60YokfSSSPxCyrGxFmiakoHXvJp4MOEU1NvZ3hbAbinOGRujrl2c+NsR8nGMIToRYgqIiHYP/ha+YV+VeAAQBsubMyVT2e/wAAAABJRU5ErkJggg==');
		$this->deleteImg=base64_decode('iVBORw0KGgoAAAANSUhEUgAAADAAAAAwCAMAAABg3Am1AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyZpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNi1jMDY3IDc5LjE1Nzc0NywgMjAxNS8wMy8zMC0yMzo0MDo0MiAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENDIDIwMTUgKFdpbmRvd3MpIiB4bXBNTTpJbnN0YW5jZUlEPSJ4bXAuaWlkOjA0RjcxMTA2OTU5MjExRThBRDAyRkYzMjNGRThERUQxIiB4bXBNTTpEb2N1bWVudElEPSJ4bXAuZGlkOjA0RjcxMTA3OTU5MjExRThBRDAyRkYzMjNGRThERUQxIj4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6MDRGNzExMDQ5NTkyMTFFOEFEMDJGRjMyM0ZFOERFRDEiIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6MDRGNzExMDU5NTkyMTFFOEFEMDJGRjMyM0ZFOERFRDEiLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz6gm9BkAAADAFBMVEX1+/3l6eqcnJy8EQDt7e3o2Nb/qYplX1+yMShoZmbnNhHd3d31fWXcxcL/fFL4TiTUHQDaIQK0DQCaJyb5//+td3P+4dr7LQCsVE2qqqrRvbz+jWv9a0B6enr4+Pjs8vTm5ub/ekz6w7PBFAD7+/vKu7ruHADjo5X/YTHzWTP/Xi7/gVn/gFLZ5Ob/aTn9MQD38e+NjY3/lHH/pIXacWH/NAH6+vr/ZjaoCAH/OAWXZ2SFhYXz8/P/bj7/kG3qKAD1LgD/nn3n6Ojk8vX/WSnWjoXuRib/uaT/ckb5MAD5Yz3yLACCW1vr7e3tKgDoxLrnl4f9flybDw/BNif/ViXj4+P/Th3U5Or19fXqjnb/UiHgIwD/PQrtMRD/imbNGgD/QA3oJgD/SRbg4OD/aTzr6+vkJQDrQCP4YTr/w6zbPijJycn/PgyXPTxua2va2trNzc3/NwT/d0nwKwD/RBL29vbjUz3i5OT96eXrdl//UR7iHwD/cUL/QhD59/b6VSj/dkb/mXj/hWDe1NLq5uX/SxjinJL4n4bFVUf/l3XYTz7/ooTw9ff6QRH3q5b/poj/Ogi2HRH0UCT/ooL/RxXzOQzkKQTh7vHb6e38o5LvRRzq0s///f3/XCv+WS//dUy3DwCVU1GMTk7/TRv/nXv9dEv/YC+xpaTphW32dE7p9vmjmZj7YTHvPxXIFQD6PQ763NfxUiz/d1FwZWX/VSTdNh6UQUH////w8PD+/v7+//+ysrL/YzPp6en+7+z+5uH/q4/R0ND4ckzZ3d/qLwX9/f3vVCv8///nMArvYUnyzMP91szm4uLzIwD5IAD5+/z5+fnFIxKzFgrQJxD4WCj4WS//n3/f7O/0iGbl9vr5KQDu+fzNHQmPgYHn7O3/VyjS4OTvLgjgh3uQgIDGFwD/zr7d3+Db29zbsqz4kX+ZLi335+L06uf37+vh5+n1YDj09/n3Qxfo6uv0TR//eEje5+nqKQf28fDY2dnnIwDwpJfd0M/wY0nguK//pIP///9xnuFlAAABAHRSTlP///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////8AU/cHJQAABdNJREFUeNq0lntcU2UYx5GDgM7mNiagwBHc1MFwU8bitrExUEcCisCsbbINxlQuOtgEubpRqKQIKqGCywmId1BDx1Uxo+ymZKVFBWUzky4WUYoW9J6zsyRD/fTp0++P89fv+z7P8z7P+77HZvRfyua/Ayfv52annjmTmu2/qaao+2nAhtuL3tj53EE6Hi9cPXUyMfXGxJ4nAeenfOWdGKYv9XNzKy6OpZ8u2HiJ6F8DPw7Yt3fl6jDWtXq9Xl8aSscLlUqlu3v/pQ8XdlLHBc79fqK0fNeuEdbWUDy3o8EQES/Oq0sSicuIWx7A4wDOi+7Yj4yw7MvddFwuIwAQ/c1iUUmltrCwKm4G5x+A86rX7Vks+67QoSxdAVeJABEIkKQtlEp5cbM5jwDnFgF/eRcLP+SFHxJylQwMqEOAiqAxhAXY9/YdxF8/5OXrPrk6C4sQn9frIkeATDbR9QI8Fth7wn6kvEuvw98hEhcSlQUMC9C7edarTCkC8M3TcNSHwIaVfixW1zWd15uEb3eHL4sBAA0ALrFkMnmDvi4okx0p2XEo5SEwxZu11d4+y8ur8davyTnhkzcqA2g+hszV5CvU7rfeLQEAUyVzVfdYgfO/6a9t7aJneeHzn8lJTuhbOofRQWtIn0omd3eb2vqlQWxmpEJy9ZinFbh9KiyMVVqQleVV7X89IUFwNLcpgKYtI5OprW2mRG0FAvAI5mB1EQasitXXlwsLhEKdu2R3jqcgeuaS5sqmIuA3td4sqQA1M/kaAsFxup0FOFlVWhoWyuAWcAt0ij8meHambHvnMvB3m0ymfQa5FKmZr5ERjF/jLMD9aj+/sCwGV8nlcqdmhws6o1tsts8nU7tbTSZ6khTNaIlGJoOC02AUyPUO9dPTAhiIlHMm9qXYtXB+sumhggCL69KlaEYIQDnQ3oIC2e5uocUNHR0BQIyy3KPRAHgefhZUcL5Zno4G4GtiZI3mDFsBCqQeTAwV+jTQOmhADe+/hAGgBKVWjgWIUZjNjQMLElDgzP5itwCDwQcVzWXKTBSgdpvoJVKpNYCK0tjoFJJsAYT0xIj+iIgIg8EQ3+uNRQDAYgNIyFIBj0ihmJ08cJaUlLHF4ub4+Ph+Q9nlZZ8OplgB001GOtsSgEegUCgDGPDyx/jYOrFYHC/qPYXrEwhSsBrArjrrCl0iQQU8BYUCQRlYSv7Vp/ElInB4NXOv54BGIwAH7gG7ZDKdKxb1goQUEgiCjLVY0ZsuHTydVFInipy7B5ecgALb1q/vQRtnMoUdB1ukkAGAFGU7iAI1BOX+yqSkEvbheQBI8OyM/mz79zZoI4Ba1xwfVqkgIwQFvoY1rihOJEzSaivZ3vNuISEGj27OE961gbEQa34ZVhGMRiPktBYbjdEbVe4iuTbz5z8uIsCEcDdxZiV9vg2WlC9PIgF+IykqBBu+0Ymfi/vT5ezDK+6twyX/+B5eFBTExoi2Nr/qfIkZCbDcwTreo+v9q3wqKirkX967982epQGV7EzQLa3vD+BEO9fvzJcQSCSSMbDWQw1bz3TNB01asOoLK1a8+EleBZuNdKu3yfe7Wb7HgZ9oRIDlDsjoYQC8UCYHLvmRI+JCJpPNZEZG8vmaV3QfxeRb/YFRlgDYvdS5hZjJZDKDkA/qBuMTwxseVkkkBMRPCjw7Ca3AClx5EKcCRkR8xI3YeQpgl8hI6PoDDrZjLzJQ9wxHCeoE0mhiUDuyPIWEyskh5O9X5ejo3dmOBNSKmHkKYFcBO7L9YH3gT+M8+j5wZrtCEhXvLzdRBsbB4j87xj/mBeKop+1oJKACBx5MM5hPoMDlUZNsL3DGe+Ng3CHXq5AZnPZGigWAgL3WwaN9HTz+K0rtUx8LdiSRIAgBjKTAQKcoBw9bdTT1se80LFBP/yL4QMaAk9NARm3UpLUh09UC+Il/ArDdxbR22wUhQAts29Nwdj1P/9eAWzoTknG45JzBFvh/+Tl5iv4UYABojC5AyFTaQwAAAABJRU5ErkJggg==');
		$this->pdf->underlinePercentage=0.8;
		$this->rapportFilter='';

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


	function getAttCategorieOmschrijving($categorie)
	{
		$db=new DB();
		$query="SELECT Omschrijving FROM AttributieCategorien WHERE AttributieCategorie='".mysql_real_escape_string($categorie)."'";
		$db->SQL($query);
		$omschrijving=$db->lookupRecord();
		return $omschrijving['Omschrijving'];
	}

	function getPlan($datum)
  {
    $doorkijkfondsen=array();
    if($this->pdf->lastPOST['doorkijk']==1)
    {
      $doorkijkfondsen = getFondsenVerdiept($this->portefeuille,$datum);
    }
    $portefeuilles=array();
    if(count($this->pdf->portefeuilles) > 1)
      $portefeuilles=$this->pdf->portefeuilles;
    else
      $portefeuilles[]=$this->portefeuille;

    $portefeuillesAandeel=array();
    if(count($portefeuilles)>1 || $this->pdf->lastPOST['doorkijk']==1)
    {
      $portefeuilleWaarde=array();
      $portefeuilleWaardeAbs=array();
      $totaleWaarde=0;
      $totaleWaardeAbs=0;
      
 
      foreach($portefeuilles as $portefeuille)
      {
        $fondsRegels = berekenPortefeuilleWaarde($portefeuille, $datum, (substr($datum, 5, 5) == '01-01')?true:false, $this->pdf->rapportageValuta, $datum);
        foreach ($fondsRegels as $regel)
        {
          if(isset($doorkijkfondsen[$regel['fonds']]))
          {
            $portefeuilleWaarde[$doorkijkfondsen[$regel['fonds']]]+=$regel['actuelePortefeuilleWaardeEuro'];
          }
          else
          {
            $portefeuilleWaarde[$portefeuille] += $regel['actuelePortefeuilleWaardeEuro'];
          }
          $totaleWaarde+= $regel['actuelePortefeuilleWaardeEuro'];
        
        }
        if(count($portefeuilleWaarde)==0)
          $portefeuilleWaarde[$portefeuille]=0;
      }
    
      foreach($portefeuilleWaarde as $portefeuille=>$waarde)
      {
        $portefeuilleWaardeAbs[$portefeuille] += abs($waarde);
        $totaleWaardeAbs += abs($waarde);
      }
    
      //   listarray($portefeuilleWaardeAbs);
      //  listarray($portefeuilleWaarde);
    
      foreach($portefeuilleWaarde as $portefeuille=>$waarde)
      {
        if($waarde==0 && $totaleWaarde==0)
          $portefeuillesAandeel[$portefeuille]=1;
        else
          $portefeuillesAandeel[$portefeuille]=($waarde)/$totaleWaarde;
      }
    }
    else
    {
      $portefeuillesAandeel[$this->portefeuille] = 1;
    }
  
    if(count($portefeuillesAandeel)==0)
      $portefeuillesAandeel[$this->portefeuille] = 1;



    $planTotalen=array();
    foreach($portefeuillesAandeel as $portefeuille=>$portefeuilleAandeel)
    {
      $plan=$this->getBeleggingsplan($portefeuille,$datum);
      foreach($plan as $categoriePlan=>$categorieAandeel)
      {
        $planTotalen[$categoriePlan]+=$portefeuilleAandeel*$categorieAandeel;
      }

      
    }
  
   return $planTotalen;

  }
  
  function getBeleggingsplan($portefeuille,$datum)
  {
    $DB=new DB();
    $query="SELECT Beleggingsplan.ProcentRisicoDragend/100 as ZAK,
Beleggingsplan.ProcentRisicoMijdend/100 as VAR,
(100-Beleggingsplan.ProcentRisicoDragend-Beleggingsplan.ProcentRisicoMijdend)/100 as Liquiditeiten
FROM
Beleggingsplan
WHERE  Beleggingsplan.Portefeuille='$portefeuille' AND (datum <= '".$datum."' OR datum='0000-00-00') ORDER by datum desc limit 1";
    $DB->SQL($query);
    $DB->Query();
    $data=$DB->nextRecord();
    return $data;
  }
  
  
  function writeRapport()
	{
		global $__appvar;
		$data=array();
		$query = "SELECT Clienten.Naam, Clienten.Naam1, Portefeuilles.Depotbank, Portefeuilles.AEXVergelijking, Portefeuilles.SpecifiekeIndex, Portefeuilles.Vermogensbeheerder, Portefeuilles.ClientVermogensbeheerder, Portefeuilles.Risicoklasse, Portefeuilles.Client FROM Portefeuilles, Clienten WHERE Portefeuille = '".$this->portefeuille."' AND Portefeuilles.Client = Clienten.Client ";
		$DB = new DB();
		$DB->SQL($query);
		$DB->Query();
		$portefeuilledata = $DB->nextRecord();
		$this->pdf->AddPage();
		$this->pdf->templateVars['HUISPaginas']=$this->pdf->page;
		$this->pdf->templateVarsOmschrijving['HUISPaginas']=$this->pdf->rapport_titel;
		$rapportageDatum = $this->rapportageDatum;
		$rapportageJaar=substr($rapportageDatum,0,4);
		$rapportageDag=substr($rapportageDatum,5,5);
		$jaarTerug=($rapportageJaar-1)."-".$rapportageDag;
		$rapportageDatumVanaf = $this->rapportageDatumVanaf;
	$portefeuille = $this->portefeuille;
	

		if ($this->pdf->lastPOST['doorkijk'] == 1)
		{
			//vulTijdelijkeTabel(bepaaldFondsWaardenVerdiept($this->pdf,$portefeuille, $this->rapportageDatum), $portefeuille, $this->rapportageDatum);
			$tmp = bepaalHuidfondsenVerdeling($portefeuille, $this->rapportageDatum, $this->rapportageDatumVanaf, $this->pdf->rapportageValuta);
			vulTijdelijkeTabel($tmp,  $portefeuille, $this->rapportageDatum);
		}

	$query = "SELECT SUM(actuelePortefeuilleWaardeEuro) AS totaal ".
					 "FROM TijdelijkeRapportage WHERE ".
					 " rapportageDatum ='".$rapportageDatum."' AND ".
					 " portefeuille = '".$portefeuille."' ".$this->rapportFilter
					 .$__appvar['TijdelijkeRapportageMaakUniek'];
	debugSpecial($query,__FILE__,__LINE__);
	$DB->SQL($query);
	$DB->Query();
	$totaalWaarde = $DB->nextRecord();
	$totaalWaarde = $totaalWaarde['totaal'];


	$query = "SELECT
			SUM(TijdelijkeRapportage.ActuelePortefeuilleWaardeEuro) AS WaardeEuro
			FROM
			TijdelijkeRapportage
			WHERE
			TijdelijkeRapportage.Portefeuille = '".$portefeuille."' AND
			TijdelijkeRapportage.rapportageDatum = '".$rapportageDatum."' AND
 			TijdelijkeRapportage.Type = 'rekening' ".$this->rapportFilter." ".
       $__appvar['TijdelijkeRapportageMaakUniek'];
	debugSpecial($query,__FILE__,__LINE__);
	$DB->SQL($query);
	$DB->Query();
	$totaalLiquiditeiten = $DB->nextRecord();
	$totaalLiquiditeiten = $totaalLiquiditeiten['WaardeEuro'];
    
    
    $query="SELECT waarde FROM KeuzePerVermogensbeheerder WHERE categorie='AttributieCategorien' AND vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."' ORDER BY Afdrukvolgorde";
    $DB->SQL($query);
    $DB->Query();
    $attCategorieen=array();
    while($vmdata=$DB->nextRecord())
    {
      $attCategorieen[$vmdata['waarde']]=$this->getAttCategorieOmschrijving($vmdata['waarde']);
      if($attCategorieen[$vmdata['waarde']]=='')
        $attCategorieen[$vmdata['waarde']]=$vmdata['waarde'];
    }
    
    foreach($attCategorieen as $categorie=>$omschrijving)
    {
      $data['beleggingscategorie']['data'][$categorie]['Omschrijving']=$omschrijving;
    }
    
$query="SELECT
TijdelijkeRapportage.attributiecategorie as hoofdcategorie,
TijdelijkeRapportage.attributiecategorieOmschrijving as hoofdcategorieOmschrijving,
if(TijdelijkeRapportage.attributiecategorie <> '',TijdelijkeRapportage.attributiecategorie,if(TijdelijkeRapportage.type='rekening','Liquiditeiten','geen')) as categorie,
sum(TijdelijkeRapportage.ActuelePortefeuilleWaardeEuro) AS WaardeEuro,
 if(TijdelijkeRapportage.attributiecategorie <> '',TijdelijkeRapportage.attributieCategorieOmschrijving,if(TijdelijkeRapportage.type='rekening','Liquiditeiten', 'Geen attributiecategorie')) as categorieOmschrijving
FROM TijdelijkeRapportage
	WHERE TijdelijkeRapportage.Portefeuille = '".$portefeuille."'
	AND TijdelijkeRapportage.rapportageDatum ='".$rapportageDatum."' ".$this->rapportFilter." "
	.$__appvar['TijdelijkeRapportageMaakUniek'].
	" GROUP BY categorie
	ORDER BY TijdelijkeRapportage.hoofdcategorieVolgorde,TijdelijkeRapportage.beleggingscategorieVolgorde, categorie";

	debugSpecial($query,__FILE__,__LINE__);
	$DB->SQL($query);
	$DB->Query();
	$totalen=array();
  $totalen['beleggingscategorie']=array();


	while($cat = $DB->nextRecord())
	{
	  //listarray($cat);
		//if($cat['categorie']=='LIQ')
		//	$cat['categorie']='Liquiditeiten';
	   $data['beleggingscategorie']['data'][$cat['categorie']]['waardeEur']+=$cat['WaardeEuro'];
	   $data['beleggingscategorie']['data'][$cat['categorie']]['Omschrijving']=$cat['categorieOmschrijving'];
	   $data['beleggingscategorie']['data'][$cat['categorie']]['percentage']+= $cat['WaardeEuro']/$totaalWaarde*100;

		 $totalen['beleggingscategorie']['waardeEur']+=$cat['WaardeEuro'];
		 $totalen['beleggingscategorie']['percentage']+=$cat['WaardeEuro']/$totaalWaarde*100;
	}
	//listarray($data);
	//exit;
    
    if ($this->pdf->lastPOST['doorkijk'] == 1)
      $hpiGebruik=true;
    else
      $hpiGebruik=false;
    
    $this->att->indexPerformance=true;
    $this->waarden['Periode']=$this->att->bereken($this->rapportageDatumVanaf,$this->rapportageDatum,'attributie',false);
    $this->att=new ATTberekening_L68($this);
    $this->waarden['PeriodeHcat']=$this->att->bereken($this->rapportageDatumVanaf,$this->rapportageDatum,'Hoofdcategorie',$hpiGebruik);
    
//listarray( $this->waarden['Periode']);
    $planMist=false;

      $plandata= $this->waarden['PeriodeHcat']['totaal']['perfWaarden'][$rapportageDatum]['planTotalen'];//$this->getPlan($rapportageDatum);
      $planSom=array_sum($plandata);
      if(round($planSom,2)<>1.00)
      {
         $planMist = true;
      }
      //listarray($plandata);echo "| $planSom |".round($planSom,2)."<>1.00 |<br>\n" ;exit;
      foreach($plandata as $categorie=>$percentage)
      {
        if($categorie=='ZAK')
          $data['beleggingscategorie']['data']['ZAKBEL']['strat'] = $percentage*100;
        if($categorie=='VAR')
        {
          if(isset($data['beleggingscategorie']['data']['REN-VARBEL']['waardeEur']))
            $data['beleggingscategorie']['data']['REN-VARBEL']['strat'] = $percentage * 100;
          else
            $data['beleggingscategorie']['data']['VARBEL']['strat'] = $percentage * 100;
        }
        if($categorie=='Liquiditeiten')
          $data['beleggingscategorie']['data']['LIQ']['strat'] = $percentage*100;
      }
      //listarray($categorie);
		


		$categorien=array();
    $categorieConversie=array('LIQ'=>'Liquiditeiten','REN-VARBEL'=>'VAR','VARBEZ'=>'VAR','ZAKBEL'=>'ZAK','ZAKBEZ'=>'ZAK','Liquiditeiten'=>'Liquiditeiten','VARBEL'=>'VAR');

		$query="SELECT waarde FROM KeuzePerVermogensbeheerder WHERE categorie='AttributieCategorien' AND vermogensbeheerder='".$this->pdf->portefeuilledata['Vermogensbeheerder']."' ORDER BY Afdrukvolgorde";
		$DB->SQL($query);
		$DB->Query();
		$attCategorieen=array();
		while($vmdata=$DB->nextRecord())
		{
      $attCategorieen[$vmdata['waarde']]=$this->getAttCategorieOmschrijving($vmdata['waarde']);
      if($attCategorieen[$vmdata['waarde']]=='')
        $attCategorieen[$vmdata['waarde']]=$vmdata['waarde'];
		}
		
		foreach($this->att->categorien as $categorie=>$omschrijving)
    {
        $attCategorieen[$categorie] = $omschrijving;
    }

    foreach($data['beleggingscategorie']['data'] as $categorie=>$categorieData)
    {
      if(isset($data['beleggingscategorie']['data'][$categorie]) && $data['beleggingscategorie']['data'][$categorie]['Omschrijving']=='')
        $data['beleggingscategorie']['data'][$categorie]['Omschrijving']= $attCategorieen[$categorie];
      
      if($data['rendement']['data'][$categorie]['omschrijving']=='' && $attCategorieen[$categorie]<>'')
        $data['rendement']['data'][$categorie]['omschrijving'] =  $attCategorieen[$categorie];
      $attCategorieen[$categorie]= $categorieData['Omschrijving'];
    }
   // listarray($data);exit;
//listarray($this->waarden['Periode']);
    //listarray($attCategorieen);
    //listarray($this->waarden['PeriodeHcat']);
    //listarray($categorieConversie);
		foreach($attCategorieen as $categorie=>$categorieOmschijving)
		{
			if($categorie <> 'totaal')
			{
		  	if($this->waarden['Periode'][$categorie]['procent'] <> 0 || $this->waarden['Periode'][$categorie]['beginwaarde'] <> 0 || $this->waarden['Periode'][$categorie]['eindwaarde'] <> 0)
				{
					$categorien[] = $categorie;
					
					if($categorieConversie[$categorie]=='Liquiditeiten')
					{
						$benchmarkTotaal = getFondsPerformance('Euribor 3M', $this->rapportageDatumVanaf, $this->rapportageDatum);
					}
					else
          {
            if(isset($this->waarden['PeriodeHcat'][$categorieConversie[$categorie]]['indexPerf']))
              $benchmarkTotaal= $this->waarden['PeriodeHcat'][$categorieConversie[$categorie]]['indexPerf'];
            else
              $benchmarkTotaal= $this->waarden['PeriodeHcat'][$categorie]['indexPerf'];
          }

					if($categorie==$categorieOmschijving)
					{
						$tmp=$this->getAttCategorieOmschrijving($categorie);
						if($tmp<>'')
							$categorieOmschijving=$tmp;
					}
          $categorieGebruik=$categorie;
//echo "$categorieGebruik $categorie <br>\n";
					$data['rendement']['data'][$categorieGebruik]=array('procent'=>$this->waarden['Periode'][$categorie]['procent'],
																											 'resultaat'=>$this->waarden['Periode'][$categorie]['resultaat'],
						                                            'gemWaarde'=>$this->waarden['Periode'][$categorie]['gemWaarde'],
						                                           'omschrijving'=>$categorieOmschijving,
						                                           'benchmarkProcent'=>$benchmarkTotaal);//getFondsPerformance($fonds['Fonds'],$this->rapportageDatumVanaf,$this->rapportageDatum));
				}
			}
		}

    $benchmarkTotaal=$this->waarden['PeriodeHcat']['totaal']['indexPerf'];
		$totalen['rendement']=array('procent'=>$this->waarden['Periode']['totaal']['procent'],
															  'resultaat'=>$this->waarden['Periode']['totaal']['resultaat'],
																'omschrijving'=>'Totaal',
																'benchmarkProcent'=>$benchmarkTotaal);//getSpecifikeIndexPerformance($this->portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum,'maanden',true));
//listarray($data['rendement']);exit;
		$query="desc CRM_naw";
		$DB->SQL($query);
		$DB->query();
		$fields=array();
		while($dbdata=$DB->nextRecord())
		{
			$fields[]=$dbdata['Field'];
		}
		$nodigeVelden=array('MinimaalRendement','MaximaleSD','MinimaleKasstroom');
		for($i=1;$i<11;$i++)
			$nodigeVelden[]='Aandachtspunt'.$i;

		$select="SELECT portefeuille";
		foreach($nodigeVelden as $veld)
		{
			if(in_array($veld,$fields))
				$select.=",$veld ";
		}
		$select.=" FROM CRM_naw WHERE portefeuille='".$portefeuille."'";
		$DB->SQL($select);
		$data['wensen']['data']=$DB->lookupRecord();
		$jaar=substr($this->rapportageDatum,0,4);

    $dagenInJaar=round((mktime(0,0,0,12,31,$jaar)-mktime(0,0,0,1,0,$jaar))/86400);

    if(substr($this->rapportageDatumVanaf,5,5)=='01-01')
      $extraDag=1;
    else
      $extraDag=0;
		$data['wensen']['data']['jaardeel']=(round((db2jul($this->rapportageDatum)-db2jul($this->rapportageDatumVanaf))/86400)+$extraDag)/$dagenInJaar;

	

		$stdev=new rapportSDberekening($portefeuille,$this->rapportageDatum);
    $stdev->filterJaarovergang=false;
		if(1)//||$this->pdf->lastPOST['doorkijk']==1
		{
      $this->att=new ATTberekening_L68($this);
      $this->att->perioden='weken';
      $query="SELECT date(startdatumMeerjarenrendement) as startdatum FROM Portefeuilles WHERE Portefeuille='".$this->portefeuille."'";
      $DB->SQL($query);
      $DB->Query();
      $startdatum=$DB->lookupRecord();
      if($startdatum['startdatum'] <> '0000-00-00')
        $startDatum=$startdatum['startdatum'];
      else
        $startDatum=substr($this->pdf->PortefeuilleStartdatum,0,10);
      $this->waarden['historieWeken'] = $this->att->bereken($startDatum, $this->rapportageDatum, 'Hoofdcategorie',$hpiGebruik);
			$reeks=array();
			$benchmark=array();
			//listarray($perioden);
			foreach($this->waarden['historieWeken']['totaal']['perfWaarden'] as $datum=>$perfData)
			{
				$reeks[$datum]=array('perf'=>$perfData['procent']*100,'datum'=>$datum);
				$benchmark[$datum]=array('perf'=>$perfData['indexPerf']*100,'datum'=>$datum);
			}
			//listarray($reeks);exit;
			$stdev->reeksen['totaal']=$reeks;
			$stdev->reeksen['benchmarkTot']=$benchmark;
		}
		$stdev->berekenWaarden(false);
		//listarray($stdev);
		$data['risico']['data']['portefeuille']=$stdev->riskAnalyze('totaal','benchmarkTot',false,true);
		if($data['risico']['data']['portefeuille']['valueAtRisk']<>0)
		  $data['risico']['data']['portefeuille']['valueAtRisk']=(100-$data['risico']['data']['portefeuille']['valueAtRisk'])/100*$totaalWaarde;
		else
      $data['risico']['data']['portefeuille']['valueAtRisk']=0;
		$data['risico']['data']['benchmark']=$stdev->riskAnalyze('benchmarkTot','totaal',false,true);
		if($data['risico']['data']['benchmark']['valueAtRisk']<>0)
		  $data['risico']['data']['benchmark']['valueAtRisk']=(100-$data['risico']['data']['benchmark']['valueAtRisk'])/100*$totaalWaarde;
		else
      $data['risico']['data']['benchmark']['valueAtRisk']=0;

		/*
De rente en het dividend (RENOB, DIV, DIVBE) van de afgelopen 12 maanden
De kosten (alle grootboekrekeningen waar Kosten = 1) van de afgelopen 12 maanden
De huuropbrengsten (HUUR) van de afgelopen 12 maanden
De rente op liquiditeiten (RENTE) van de afgelopen 12 maanden

Rente/dividen d bel.             40.000
Kosten beleggingen              -25.000
Saldo beleggingen                15.000

Huur                             35.000
Rente liq. en leningen           12.000
Totaal                           62.000
	*/

//	foreach($data['Kasstroom']['data'] as $categorie=>$waarden)

//listarray($this->pdf->portefeuilledata['SpecifiekeIndex']);
	//	listarray($tmp);
	//	listarray($this->waarden['Periode']);

		$select="SELECT
Rekeningen.Portefeuille,
Rekeningmutaties.Boekdatum,
(Rekeningmutaties.Debet*Rekeningmutaties.valutakoers) AS Debet,
(Rekeningmutaties.Credit*Rekeningmutaties.valutakoers) AS Credit";
		$whereBoekdatum="Rekeningmutaties.Boekdatum > '".$rapportageDatumVanaf."'";
		$where="Rekeningen.Portefeuille='".$portefeuille."' AND $whereBoekdatum AND Rekeningmutaties.Boekdatum<='".$rapportageDatum."'";
  //  $whereBoekdatumDoorkijk="Rekeningmutaties.Boekdatum > '".date('Y-m-d',db2jul($rapportageDatum)+86400)."'";
		$queries['renteendiv']="$select FROM Rekeningmutaties
INNER JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening
WHERE $where AND (Rekeningmutaties.Grootboekrekening IN ('RENOB','DIV','DIVBE','RENME','VKSTO') OR (Rekeningmutaties.Grootboekrekening IN ('RENTE') AND Rekeningen.Beleggingscategorie='REN-VARBEL') )";
		$queries['kosten']="$select FROM Rekeningmutaties
INNER JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening
INNER JOIN Grootboekrekeningen ON Rekeningmutaties.Grootboekrekening = Grootboekrekeningen.Grootboekrekening
WHERE Grootboekrekeningen.Kosten=1 AND $where AND Rekeningmutaties.Grootboekrekening NOT IN ('KNOG','KOST')";
		$queries['huur']="$select FROM Rekeningmutaties
INNER JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening
WHERE $where  AND Rekeningmutaties.Grootboekrekening IN ('HUUR')";
		$queries['rente']="$select FROM Rekeningmutaties
INNER JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening
WHERE $where AND Rekeningmutaties.Grootboekrekening IN ('RENTE') AND Rekeningen.Beleggingscategorie <> 'REN-VARBEL'";
		$queries['KNOG']="$select FROM Rekeningmutaties
INNER JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening
WHERE $where  AND Rekeningmutaties.Grootboekrekening IN ('KNOG')";
    $queries['KOST']="$select FROM Rekeningmutaties
INNER JOIN Rekeningen ON Rekeningmutaties.Rekening = Rekeningen.Rekening
WHERE $where  AND Rekeningmutaties.Grootboekrekening IN ('KOST')";
		foreach($queries as $type=>$query)
		{
			$DB->SQL($query);
			$DB->Query();
			while($kasstroom = $DB->nextRecord())
			  $data['Kasstroom']['data'][$type]+=($kasstroom['Credit']-$kasstroom['Debet']);
		}

		if($this->pdf->lastPOST['doorkijk']==1)
		{
			$doorkijk = getFondsenVerdiept($portefeuille, $rapportageDatum);
			foreach($doorkijk as $fonds=>$dPort)
			{
				foreach($queries as $type=>$query)
				{
				  $doorkijkQuery=str_replace(array("Rekeningen.Portefeuille='".$portefeuille."'"),array("Rekeningen.Portefeuille='".$dPort."'"),$query);
				  //echo "$doorkijkQuery <br>\n";
					$DB->SQL($doorkijkQuery);
					$DB->Query();
					while($kasstroom = $DB->nextRecord())
					{
						$aandeel = bepaalHuisfondsAandeel($fonds, $portefeuille, $kasstroom['Boekdatum']);
						//echo "$fonds $aandeel <br>\n";
						$data['Kasstroom']['data'][$type] += ($kasstroom['Credit'] - $kasstroom['Debet']) * $aandeel;
					}
				}
			}
		}

		#blokken tekenen.
		$blokken=array(array(10,35),array(160,35),
			array(85,90),
			array(10,145),array(160,145));
		foreach($blokken as $index=>$blok)
		{
			if($index==2)
			{
				$extraWidth=40;
				$extraXstart=$extraWidth*-.5;
			}
			else
			{
				$extraWidth=0;
				$extraXstart=0;
			}
			$this->pdf->RoundedRect($blok[0]+$extraXstart,$blok[1], 120+$extraWidth, 50, 8, '1111', 'DF',
															array('color' => array($this->pdf->rapport_kop_bgcolor['r'], $this->pdf->rapport_kop_bgcolor['g'], $this->pdf->rapport_kop_bgcolor['b']), 'width' => 1),
															$this->pdf->rapport_regelAchtergrond);
		}
		$this->pdf->setLineStyle(array('color' => array(0,0,0), 'width' => 0.1));
    
		#linksboven
		$this->pdf->setXY($this->pdf->marge,$blokken[0][1]+2);
		$this->pdf->SetWidths(array($blokken[0][0]+2,50,20,20,20));
	  $this->pdf->SetAligns(array('L', 'L','R','R','R'));
		$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize+4);
    $this->pdf->MultiCell(120,6,'Vermogen',0,"C");
		$this->pdf->SetFont($this->pdf->rapport_font,'i',$this->pdf->rapport_fontsize);
		if($planMist==true)
      $this->pdf->row(array('','','Bedrag','Perc.'));
		else
		  $this->pdf->row(array('','','Bedrag','Perc.','Strat.'));
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		$subtotalen=array();
  
  
  
    $categorieTonen=array();
    foreach($data['rendement']['data'] as $categorie=>$waarden)
      if($waarden['resultaat']<>0 || $waarden['procent']<>0 || $waarden['benchmarkProcent']<>0 || $waarden['benchmarkProcent']<>0)
        $categorieTonen[$categorie]=1;
    foreach($data['beleggingscategorie']['data'] as $categorie=>$waarden)
      if($waarden['waardeEur']<>0 || $waarden['percentage']<>0 || $waarden['strat']<>0 )
        $categorieTonen[$categorie]=1;
    $categorieGebruiken=array();
    foreach($attCategorieen as $categorie=>$omschrijving)
    {
      if(isset($categorieTonen[$categorie]))
      {
        $categorieGebruiken[]=$categorie;
      }
    }
    //listarray($categorieTonen);
    //listarray($attCategorieen);
    
    $eersteTotaal='';
    $tweedeTotaal='';
    $zakWaarde=false;
   // foreach($data['beleggingscategorie']['data'] as $categorie=>$waarden)
    foreach($categorieGebruiken as $categorie)
    {
      $waarden=$data['beleggingscategorie']['data'][$categorie];
      if(substr($categorie,0,3)=='ZAK')
      {
        $eersteTotaal = $categorie;
        if($waarden['waardeEur']<>0 || $waarden['percentage']<>0 || $waarden['strat']<>0)
        {
          $zakWaarde=true;
        }
      }
      else
      {
        if($zakWaarde==false)
        {
          if(substr($categorie,0,3)=='VAR'||substr($categorie,0,3)=='LIQ')
          {
            $eersteTotaal = $categorie;
          }
        }
        $tweedeTotaal = $categorie;
      }
    }


    
    $subTonen=array();
	//	foreach($data['beleggingscategorie']['data'] as $categorie=>$waarden)
		foreach($categorieGebruiken as $categorie)
		{
      $waarden=$data['beleggingscategorie']['data'][$categorie];
			$subtotalen['waardeEur']+=$waarden['waardeEur'];
			$subtotalen['percentage']+=$waarden['percentage'];
			$subtotalen['strat']+=$waarden['strat'];
      unset($this->pdf->CellBorders);
      if($waarden['waardeEur']<>0 || $waarden['percentage']<>0 || $waarden['strat']<>0 || $categorieTonen[$categorie]==1)
      {
        if($planMist==true)
          $this->pdf->row(array('', $waarden['Omschrijving'],
                          $this->formatGetal($waarden['waardeEur'], 0),
                          $this->formatGetal($waarden['percentage'], 1). '%'));
        else
          $this->pdf->row(array('', $waarden['Omschrijving'],
                          $this->formatGetal($waarden['waardeEur'], 0),
                          $this->formatGetal($waarden['percentage'], 1) . '%',
                          $this->formatGetal($waarden['strat'], 1) . '%'));
  
        $categorieTonen[$categorie]=1;
      }
      if($categorie==$eersteTotaal || $categorie==$tweedeTotaal)
			{
				$this->pdf->CellBorders=array('','','TS','TS','TS','TS');
        if($subtotalen['waardeEur']<>0 || $subtotalen['percentage']<>0 || $subtotalen['strat']<>0 || $categorieTonen[$categorie]==1)
        {
          $subTonen[$categorie]=1;
          if($planMist==true)
             $this->pdf->row(array('', 'Subtotaal',
                            $this->formatGetal($subtotalen['waardeEur'], 0),
                            $this->formatGetal($subtotalen['percentage'], 1) . '%'));
          else
            $this->pdf->row(array('', 'Subtotaal',
                              $this->formatGetal($subtotalen['waardeEur'], 0),
                              $this->formatGetal($subtotalen['percentage'], 1) . '%',
                              $this->formatGetal($subtotalen['strat'], 1) . '%'));
  
          $this->pdf->ln(2);
        }
				$subtotalen=array();
			}
		}

		$this->pdf->CellBorders=array('','','T','T','T','T');
    if($planMist==true)
	  	$this->pdf->row(array('','Totaal',$this->formatGetal($totalen['beleggingscategorie']['waardeEur'],0),
											                $this->formatGetal($totalen['beleggingscategorie']['percentage'],1).'%'));
    else
      $this->pdf->row(array('','Totaal',$this->formatGetal($totalen['beleggingscategorie']['waardeEur'],0),
                        $this->formatGetal($totalen['beleggingscategorie']['percentage'],1).'%',
                        $this->formatGetal(100,1).'%'));
    unset($this->pdf->CellBorders);

		#rechtsboven
		$this->pdf->setXY($blokken[1][0],$blokken[1][1]+2);
		$this->pdf->SetWidths(array($blokken[1][0]+2,50,20,20,20));
		$this->pdf->SetAligns(array('L', 'L','R','R','R'));
		$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize+4);
		$this->pdf->MultiCell(120,6,'Rendement',0,"C");
		$this->pdf->SetFont($this->pdf->rapport_font,'i',$this->pdf->rapport_fontsize);
    if($planMist==true)
		  $this->pdf->row(array('','','Bedrag','Perc.'));
    else
      $this->pdf->row(array('','','Bedrag','Perc.','Benchmark'));
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);


		foreach($data['rendement']['data'] as $categorie=>$waarden)
		{
	  	unset($this->pdf->CellBorders);
			$subtotalen['resultaat']+=$waarden['resultaat'];
			$subtotalen['gemWaarde']+=$waarden['gemWaarde'];
			$subtotalen['procentWaarde']+=$waarden['gemWaarde']*$waarden['procent'];
			$subtotalen['benchmarkWaarde']+=$waarden['gemWaarde']*$waarden['benchmarkProcent'];
      if($waarden['resultaat']<>0 || $waarden['procent']<>0 || $waarden['benchmarkProcent']<>0 || $waarden['benchmarkProcent']<>0 || isset($categorieTonen[$categorie]))
      {
        if($planMist==true)
          $this->pdf->row(array('', $waarden['omschrijving'],
                          $this->formatGetal($waarden['resultaat'], 0),
                          $this->formatGetal($waarden['procent'], 1) . '%'));
        else
          $this->pdf->row(array('', $waarden['omschrijving'],
                            $this->formatGetal($waarden['resultaat'], 0),
                            $this->formatGetal($waarden['procent'], 1) . '%',
                            $this->formatGetal($waarden['benchmarkProcent'], 1) . '%'));
      }
			if($categorie==$eersteTotaal||$categorie==$tweedeTotaal || count($data['rendement']['data'])==2)
			{
        $hcat=$categorieConversie[$categorie];
        if($hcat=='Liquiditeiten')
          $hcat='VAR';
        
        //$rendement=$subtotalen['procentWaarde']/$subtotalen['gemWaarde'];
        //$rendementBenchmark=$subtotalen['benchmarkWaarde']/$subtotalen['gemWaarde'];
        $rendement=$this->waarden['PeriodeHcat'][$hcat]['procent'];
        $rendementBenchmark=$this->waarden['PeriodeHcat'][$hcat]['indexPerf'];
        
          
				$this->pdf->CellBorders = array('', '', 'TS', 'TS', 'TS', 'TS');
        if($subtotalen['resultaat']<>0 || $subtotalen['procentWaarde']<>0 || $subtotalen['benchmarkWaarde']<>0 || isset($subTonen[$categorie]))
        {
          if($planMist==true)
           	$this->pdf->row(array('', 'Subtotaal',$this->formatGetal($subtotalen['resultaat'], 0),$this->formatGetal($rendement, 1).'%'));
			    else
            $this->pdf->row(array('', 'Subtotaal',$this->formatGetal($subtotalen['resultaat'], 0),$this->formatGetal($rendement, 1).'%',$this->formatGetal($rendementBenchmark, 1).'%'));
			  	$this->pdf->ln(2);
        }
				$subtotalen=array();
			}

		}
		$this->pdf->CellBorders=array('','','T','T','T');
    if($planMist==true)
		  $this->pdf->row(array('','Totaal',$this->formatGetal($totalen['rendement']['resultaat'],0),
		                                  $this->formatGetal($totalen['rendement']['procent'],1).'%'));
    else
      $this->pdf->row(array('','Totaal',$this->formatGetal($totalen['rendement']['resultaat'],0),
                        $this->formatGetal($totalen['rendement']['procent'],1).'%',
                        $this->formatGetal($totalen['rendement']['benchmarkProcent'],1).'%'));
		unset($this->pdf->CellBorders);

		#midden
		$extraWidth=40;
		$this->pdf->setXY($blokken[2][0]-20,$blokken[2][1]+2);
		$this->pdf->SetWidths(array($blokken[2][0]-$extraWidth/2+2,45,30,25,25));
		$this->pdf->SetAligns(array('L', 'L','R','R','R','R'));
		$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize+4);
		$this->pdf->MultiCell(120+$extraWidth,6,'Wensen en Doelstellingen',0,"C");
		if(count($data['wensen']['data']) > 1 || 1)
		{
			$this->pdf->SetFont($this->pdf->rapport_font, 'i', $this->pdf->rapport_fontsize);
			$this->pdf->row(array('', '', "Doelstelling\nJaarbasis","Doelstelling\nRapp.periode","Realisatie",''));
			$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
			$factor = $data['wensen']['data']['jaardeel'];

			$xVinkPlaatje=196+10;
			if($data['wensen']['data']['MinimaalRendement']<>-99)
			{
				if ($totalen['rendement']['procent'] >= $factor * $data['wensen']['data']['MinimaalRendement'])
					$this->pdf->MemImage($this->checkImg, $xVinkPlaatje, $this->pdf->getY(), 3.9, 3.9);
				else
					$this->pdf->MemImage($this->deleteImg, $xVinkPlaatje, $this->pdf->getY(), 3.9, 3.9);
				$this->pdf->row(array('', 'Minimaal rendement', $this->formatGetal($data['wensen']['data']['MinimaalRendement'], 1) . '%',
													$this->formatGetal($factor * $data['wensen']['data']['MinimaalRendement'], 1) . '%',$this->formatGetal($totalen['rendement']['procent'],1 ).'%'));
			}

			if($data['wensen']['data']['MaximaleSD'] <> -99)
			{
			  if (round($data['risico']['data']['portefeuille']['standaarddeviatie'],4) > round($data['wensen']['data']['MaximaleSD'],4))
				  $this->pdf->MemImage($this->deleteImg, $xVinkPlaatje, $this->pdf->getY(), 3.9, 3.9);
			  else
				  $this->pdf->MemImage($this->checkImg, $xVinkPlaatje, $this->pdf->getY(), 3.9, 3.9);
			  $this->pdf->row(array('', 'Maximale standaarddev.', $this->formatGetal($data['wensen']['data']['MaximaleSD'], 1) . '%',
													$this->formatGetal($data['wensen']['data']['MaximaleSD'], 1) . '%',$this->formatGetal($data['risico']['data']['portefeuille']['standaarddeviatie'], 1).'%'));
		  }

			if($data['wensen']['data']['MinimaleKasstroom'] <> -99)
			{
				if (array_sum($data['Kasstroom']['data']) > ($factor * $data['wensen']['data']['MinimaleKasstroom']))
					$this->pdf->MemImage($this->checkImg, $xVinkPlaatje, $this->pdf->getY(), 3.9, 3.9);
				else
					$this->pdf->MemImage($this->deleteImg, $xVinkPlaatje, $this->pdf->getY(), 3.9, 3.9);
				$this->pdf->row(array('', 'Minimale kasstroom', '€ ' . $this->formatGetal($data['wensen']['data']['MinimaleKasstroom'], 0) . '',
													'€ ' . $this->formatGetal($factor * $data['wensen']['data']['MinimaleKasstroom'], 0) . '', '€ ' . $this->formatGetal(array_sum($data['Kasstroom']['data']), 0)));
			}
			$this->pdf->SetWidths(array($blokken[2][0]-$extraWidth/2+2, 45 + 30 + 25 + 25));
			$this->pdf->SetAligns(array('L', 'L', 'L', 'L', 'R'));
			for ($i = 1; $i < 11; $i++)
			{
				$veld = 'Aandachtspunt' . $i;
				if (isset($data['wensen']['data'][$veld]) && $data['wensen']['data'][$veld] <> '' && $data['wensen']['data'][$veld] <> -99)
				{
					$this->pdf->row(array('', $data['wensen']['data'][$veld]));
				}
			}
		}
		else
		{
			$this->pdf->ln();
			$this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
			$this->pdf->setX($blokken[2][0]);
			$this->pdf->MultiCell(120,6,'Data niet beschikbaar',0,"C");
		}
		//  $this->pdf->row(array('','Kosten minimaliseren',$data['wensen']['data']['Aandachtspunt1']));
		//	$this->pdf->row(array('','Transparantie maximaliseren',$data['wensen']['data']['Aandachtspunt2']));
    //$this->pdf->CellBorders=array('','','T','T','T');
		//$this->pdf->row(array('','Totaal',$this->formatGetal($totalen['rendement']['waarde'],0),$this->formatGetal($totalen['rendement']['percentage'],1).'%'));
		//unset($this->pdf->CellBorders);
	//	listarray($data['wensen']['data']);exit;

    #linksonder
		$this->pdf->setXY($blokken[3][0],$blokken[3][1]+2);
		$this->pdf->SetWidths(array($blokken[3][0]+2,40,30,30));
		$this->pdf->SetAligns(array('L', 'L','R','R','R'));
		$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize+4);
		$this->pdf->MultiCell(120,6,'Risico Beleggingen',0,"C");
		$this->pdf->SetFont($this->pdf->rapport_font,'i',$this->pdf->rapport_fontsize);
    if($planMist==true)
    {
      $this->pdf->row(array('', '', 'Werkelijk'));
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      //$this->pdf->CellBorders=array('','R','R','R','R');
      $this->pdf->row(array('', 'Standaarddeviatie',
                        $this->formatGetal($data['risico']['data']['portefeuille']['standaarddeviatie'], 1) . '%'));
                        
      $this->pdf->row(array('', 'Sharpe ratio',
                        $this->formatGetal($data['risico']['data']['portefeuille']['sharpeRatio'], 1) . ''));
                        
      $this->pdf->row(array('', 'Value at Risk',
                        "€ " . $this->formatGetal($data['risico']['data']['portefeuille']['valueAtRisk'], 0) . ''));
                        
      $this->pdf->row(array('', 'Maximale terugval',
                        $this->formatGetal($data['risico']['data']['portefeuille']['maxDrawdown'], 1) . '%'));
                        
    }
    else
    {
      $this->pdf->row(array('', '', 'Werkelijk', 'Benchmark'));
      $this->pdf->SetFont($this->pdf->rapport_font, '', $this->pdf->rapport_fontsize);
      //$this->pdf->CellBorders=array('','R','R','R','R');
      $this->pdf->row(array('', 'Standaarddeviatie',
                        $this->formatGetal($data['risico']['data']['portefeuille']['standaarddeviatie'], 1) . '%',
                        $this->formatGetal($data['risico']['data']['benchmark']['standaarddeviatie'], 1) . '%'));
      $this->pdf->row(array('', 'Sharpe ratio',
                        $this->formatGetal($data['risico']['data']['portefeuille']['sharpeRatio'], 1) . '',
                        $this->formatGetal($data['risico']['data']['benchmark']['sharpeRatio'], 1) . ''));
      $this->pdf->row(array('', 'Value at Risk',
                        "€ " . $this->formatGetal($data['risico']['data']['portefeuille']['valueAtRisk'], 0) . '',
                        "€ " . $this->formatGetal($data['risico']['data']['benchmark']['valueAtRisk'], 0) . ''));
      $this->pdf->row(array('', 'Maximale terugval',
                        $this->formatGetal($data['risico']['data']['portefeuille']['maxDrawdown'], 1) . '%',
                        $this->formatGetal($data['risico']['data']['benchmark']['maxDrawdown'], 1) . '%'));
    }
		//$this->pdf->CellBorders=array('','','T','T','T');
		//$this->pdf->row(array('','Totaal',$this->formatGetal($totalen['rendement']['waarde'],0),$this->formatGetal($totalen['rendement']['percentage'],1).'%'));
		//unset($this->pdf->CellBorders);

		#rechtsonder
		$this->pdf->setXY($blokken[4][0],$blokken[4][1]+2);
		$this->pdf->SetWidths(array($blokken[4][0]+2,50,30));
		$this->pdf->SetAligns(array('L', 'L','R','R','R'));
		$this->pdf->SetFont($this->pdf->rapport_font,'b',$this->pdf->rapport_fontsize+4);
		$this->pdf->MultiCell(120,6,'Kasstroom',0,"C");
		//$this->pdf->SetFont($this->pdf->rapport_font,'i',$this->pdf->rapport_fontsize);
		//$this->pdf->row(array('','a','Werkelijk','Benchmark'));
		$this->pdf->SetFont($this->pdf->rapport_font,'',$this->pdf->rapport_fontsize);
		//$this->pdf->CellBorders=array('','','U');
		$this->pdf->row(array('','','Bedrag'));
		unset($this->pdf->CellBorders);
		if(round($data['Kasstroom']['data']['kosten'],0) <> 0)
		  $this->pdf->row(array('','Rente/dividend beleggingen',$this->formatGetal($data['Kasstroom']['data']['renteendiv'],0)));
		if(round($data['Kasstroom']['data']['kosten'],0) <> 0)
	  	$this->pdf->row(array('','Kosten beleggingen',$this->formatGetal($data['Kasstroom']['data']['kosten'],0)));
		$this->pdf->CellBorders=array('','','T');
		$this->pdf->row(array('','Saldo beleggingen',$this->formatGetal($data['Kasstroom']['data']['renteendiv']+$data['Kasstroom']['data']['kosten'],0)));
		unset($this->pdf->CellBorders);
		if(round($data['Kasstroom']['data']['huur'],0) <> 0)
		  $this->pdf->row(array('','Huuropbrengsten',$this->formatGetal($data['Kasstroom']['data']['huur'],0)));
		if(round($data['Kasstroom']['data']['rente'],0) <> 0)
		  $this->pdf->row(array('','Rente liquiditeiten en leningen',$this->formatGetal($data['Kasstroom']['data']['rente'],0)));
		if(round($data['Kasstroom']['data']['KNOG']+$data['Kasstroom']['data']['KOST'],0) <> 0)
			$this->pdf->row(array('','Kosten directe investeringen',$this->formatGetal($data['Kasstroom']['data']['KNOG']+$data['Kasstroom']['data']['KOST'],0)));



		$totaal=0;
		foreach($data['Kasstroom']['data'] as $type=>$waarden)
			$totaal+=$waarden;
		$this->pdf->CellBorders=array('','','T');
		$this->pdf->row(array('','Totaal',$this->formatGetal($totaal,0)));
		unset($this->pdf->CellBorders);
		$this->pdf->ln();
    
   // listarray($this->waarden['Periode']['totaal']);
    $totaal=$this->waarden['Periode']['totaal']['storting']-$this->waarden['Periode']['totaal']['onttrekking'];
	  //$totaal=(getStortingen($portefeuille, $this->rapportageDatumVanaf,$this->rapportageDatum)-getOnttrekkingen($portefeuille,$this->rapportageDatumVanaf,$this->rapportageDatum));
	  //if(round($totaal,0) <> 0)
	  $this->pdf->row(array('','Stortingen en onttrekkingen',$this->formatGetal($totaal,0)));

			//$this->pdf->row(array('',$type,$this->formatGetal($waarden,0)));

		//$this->pdf->CellBorders=array('','','T','T','T');
		//$this->pdf->row(array('','Totaal',$this->formatGetal($totalen['rendement']['waarde'],0),$this->formatGetal($totalen['rendement']['percentage'],1).'%'));
		//unset($this->pdf->CellBorders);

		if ($this->pdf->lastPOST['doorkijk'] == 1)
		{
			vulTijdelijkeTabel(berekenPortefeuilleWaarde($portefeuille, $this->rapportageDatum,(substr($this->rapportageDatum,5,5)=='01-01')?true:false,$this->pdf->rapportageValuta,$this->rapportageDatumVanaf), $portefeuille, $this->rapportageDatum);
		}

	}

}
?>