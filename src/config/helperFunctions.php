<?
/* 	
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2007/09/11 13:41:57 $
 		File Versie					: $Revision: 1.1 $
 		
 		$Log: helperFunctions.php,v $
 		Revision 1.1  2007/09/11 13:41:57  cvs
 		*** empty log message ***
 		
 	
*/

function searchdir ( $path , $maxdepth = -1 , $mode = "FULL" , $d = 0 )
{
  if ( substr ( $path , strlen ( $path ) - 1 ) != '/' )
  {
    $path .= '/' ;
  }
  $dirlist = array () ;
  if ( $mode != "FILES" )
  {
    $dirlist[] = $path ;
  }
  if ( $handle = opendir ( $path ) )
  {
    while ( false !== ( $file = readdir ( $handle ) ) )
    {
      if ( $file != '.' && $file != '..' )
      {
        $file = $path . $file ;
        if ( ! is_dir ( $file ) )
        {
          if ( $mode != "DIRS" )
          {
            $dirlist[] = $file ;
          }
        }
        elseif ( $d >=0 && ($d < $maxdepth || $maxdepth < 0) )
        {
          $result = searchdir ( $file . '/' , $maxdepth , $mode , $d + 1 ) ;
          $dirlist = array_merge ( $dirlist , $result ) ;
        }
      }
    }
    closedir ( $handle ) ;
  }
  if ( $d == 0 ) { natcasesort ( $dirlist ) ; }
  return ( $dirlist ) ;
}

function validExt($file)
{
  global $extArray;
  if (!is_file($file))   return false;

  $fileParts = explode(".",$file);
  $ext = strtolower($fileParts[count($fileParts)-1]);
  return in_array($ext,$extArray);
}

function getVersie($file)
{
  global $extArray;
  if (!is_file($file))   return false;
  
  $handle = fopen ("$file", "r");
  for ($x = 0; $x < 8; $x++)
  {
    $r[$x] = fgets($handle, 4096);
  }
  fclose ($handle);

  $crypted = (strstr($r[0],"@Zend;"))?"C/":"/O";
  if ($crypted == "C/")
  {
    if (substr($r[4],0,2) == "//")
    {
      $r[4] = str_replace("\n","",$r[4]);
      $out = "C/".substr($r[4],10,10).":".substr($r[5],3,10);
    } 
    else   
    $out = "C/ no version info";
  }
  else 
  {
    $out = "no info";
  }
  return $out;
}
?>