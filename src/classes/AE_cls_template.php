<?php
/*
    AE-ICT source module
    Author  						: $Author: rm $
 		Laatste aanpassing	: $Date: 2020/06/17 14:13:40 $
 		File Versie					: $Revision: 1.11 $

 		$Log: AE_cls_template.php,v $
 		Revision 1.11  2020/06/17 14:13:40  rm
 		8662
 		
 		Revision 1.10  2017/08/31 09:40:32  rm
 		Html rapportage
 		
 		Revision 1.9  2017/02/28 12:34:06  rm
 		cache voor js
 		
 		Revision 1.8  2016/04/22 12:21:52  rm
 		no message
 		
 		Revision 1.7  2016/03/30 12:06:04  rvv
 		no message
 		
 		Revision 1.6  2015/07/01 14:52:22  rm
 		laden js en css path seperator gewijzigd ivm firefox
 		
 		Revision 1.5  2015/03/18 15:38:31  rm
 		bugfix voor includen icons
 		
 		Revision 1.4  2015/01/28 13:16:42  rm
 		Participanten
 		
 		Revision 1.3  2014/12/17 11:23:30  rm
 		php4 wijzigingen
 		
 		Revision 1.1  2014/12/10 16:00:29  rm
 		Template class
 		
 		Revision 1.1  2014/03/28 12:44:15  cvs
 		update 28-3-2014, versie 1.1
 		
 		Revision 1.1  2014/02/27 12:58:14  cvs
 		*** empty log message ***

 		Revision 1.1  2014/02/24 09:29:56  cvs
 		update tbv digifac


*/
/**
 * AE_template
 *
 * @package  AE_template class
 * @author Chris van Santen
 * @copyright 2014
 * @version 0.01
 * @access public
 */
class AE_template
{
  var $user;
  var $outputFile;
  var $templateBlock = array("default"=>"AE_template class<br/>FOUT: [timestamp] geen TEMPLATE gedefinieerd ");
  var $block         = "default";
  var $defaultData   = array();
  var $cleanupTags   = true;                         // verwijder niet gebruikte {} tags
  var $templatePath  = "classTemplates/";            // standaard template map
  var $htmlBorder    = false;                        // geef voor en na output een html comment regel voor afbakening

  /**
   * AE_template::AE_template()
   * constructor
   * @param $template string of file (file beginnen met file= dan bestandsnaam)
   * @param $defaultArray geeft array met defaultwaarden mee
   * @return
   */
  function AE_template($template="",$defaultArray=array())
  {
    global $USR;
	  $this->user = $USR;

    if (strtolower(substr($template,0,5)) == "file=") {
      $this->loadTemplateFromFile(substr($template,5));
    } elseif ($template <> "") {
      $this->loadTemplateFromString( $template);
    }
    if (count($defaultArray) > 0) {
      $this->setDefaultArray($defaultArray);
    }
    
    $this->outputFile = "ae_template_".date("YmdHis").".txt";
  }

  function template ($template, $objectData) {
    $template = $this->loadTemplateFromFile('./'.$template, 'template');
    return $this->parseBlock('template', $objectData);
  }

  /**
   * AE_template::loadTemplateFromFile()
   *
   * @param $file bestandnaam van in te lezen blok, begin bestandsnaam met ./ voor bestand uit huidige map
   * anders wordt het bestand uit de template map gelezen.
   * @param $block geeft bloknaam
   * @return
   */
  function loadTemplateFromFile($file,$block="default", $userTemplatePath = true)
  {
    if (substr($file,0,2) == "./" || $userTemplatePath === false) {
      $this->templateBlock[$block] = file_get_contents($file);
    } else {
      $this->templateBlock[$block] = file_get_contents($this->templatePath.$file);
    }
  }

  /**
   * AE_template::loadTemplateFromString()
   *
   * @param $txt string met inhoud template
   * @param $block bloknaam
   * @return
   */
  function loadTemplateFromString($txt,$block="default")
  {
    $this->templateBlock[$block] = $txt;
  }
  
  /**
   * AE_template::parseBlockFromFile()
   * 
   * Parse directly from file
   * 
   * @param type $file
   * @param type $data
   * @return type
   */
  function parseBlockFromFile($file, $data = array(), $userTemplatePath = true)
  {
    $this->loadTemplateFromFile($file, 'parseBlockFromFile', $userTemplatePath);
    return $this->parseBlock('parseBlockFromFile', $data);
  }

  function parseBlockFromFileWithForm($file, $data = array(), $object)
  {
    $form = new Form($object);
    $form->skipStripAll = true;

    $this->block = 'parseBlockFromFile';
    $this->templateBlock[$this->block] = $form->template($file);

    return $this->parseBlock('parseBlockFromFile', $data);
  }
  /**
   * Static function to get parsed file whitin the html directory
   * use as AE_template::parseFile(the file, the data array)
   * 
   * Start with a / to define a path from /html/
   * 
   * @param string $file
   * @param array $data
   * @return parsed file
   */
  function parseFile ($file, $data = array(), $userTemplatePath = true)
  {
    $userTemplatePath = false;
    if (substr($file,0,1) != "/" ) {
      $file = realpath(dirname(__FILE__)."/../html/") . DIRECTORY_SEPARATOR . $this->templatePath . $file;
    } else {
      $file = realpath(dirname(__FILE__)."/../html/") . DIRECTORY_SEPARATOR . $file;
    }
    return $this->parseBlockFromFile($file, $data, $userTemplatePath );
  }
  
  /**
   * Load js file as language javascript
   * 
   * @author RM
   * @since 24-9-2014
   * 
   * @param type $file file name
   * @param type $location file location default javascript /
   * @param string $ext file extension default .js
   * @return string <script language=javascript
   */
  function loadJs ($file, $location = 'javascript', $ext = '.js')
  {
    if ( ! $_SESSION['usersession']['cacheKey'] )
    {
      $_SESSION['usersession']['cacheKey'] = mt_rand(100, 10000);
    }
    $random = $_SESSION['usersession']['cacheKey'];

    if (strpos ($file,$ext) !== false) {
      $ext = '';
    }
    if (strpos ($file,$location) !== false) {
      $location = '';
    } else {
      $location = $location . '/';
    }
    return '<script language=JavaScript src="'. $location . $file . $ext . '?cache=' . $random . '" type=text/javascript></script>';
  }
  
  function loadCss ($file, $location = 'style', $ext = '.css')
  {
    if ( ! $_SESSION['usersession']['cacheKey'] )
    {
      $_SESSION['usersession']['cacheKey'] = mt_rand(100, 10000);
    }
    $random = $_SESSION['usersession']['cacheKey'];
    
    if (strpos ($file,$ext) !== false) {
      $ext = '';
    }
    if (strpos ($file,$location) !== false) {
      $location = '';
    } else {
      $location = $location . '/';
    }
    return '<link rel="stylesheet" type="text/css" href="'. $location . $file . $ext . '?cache=' . $random . '">';
  }

  /**
   * AE_template::setTemplatePath()
   *
   * @param $path wijzig templatemap
   * defaultmap is classTemplate/
   * @return
   */
  function setTemplatePath($path)       { $this->templatePath = $path;       }

  /**
   * AE_template::appendSubdirToTemplatePath()
   *
   * @param $path selecteer submap in template dir
   * nw path wordt classTemplate/$subDir
   * @return
   */
  function appendSubdirToTemplatePath($subDir)
  {
    if (substr($subDir,-1) <>"/") $subDir .= "/";  // trailing slash
    $this->templatePath .= $subDir;
  }



  /**
   * AE_template::clearDefaultData() maak de defaultArray leeg
   *
   * @return
   */
  function clearDefaultData()           { $this->defaultData = array();      }

  /**
   * AE_template::setDefaultData() maak/wijzig key=>value pair in defaultArray
   *
   * @param mixed $key
   * @param mixed $value
   * @return
   */
  function setDefaultData($key,$value)  { $this->defaultData[$key] = $value; }

  /**
   * AE_template::setDefaultArray()  vervang de complete defaultArray
   *
   * @param mixed $array
   * @return
   */
  function setDefaultArray($array)      { $this->defaultData = $array;       }


  /**
   * AE_template::defaultBlock() reset de defaultBlock waarde
   *
   * @return
   */
  function defaultBlock()               { $this->block = "default";          }

  /**
   * AE_template::showObject() geef een listarray van het object voor debugging
   *
   * @return
   */
  function showObject()                 { listarray($this); }

  /**
   * AE_template::parseBlock() parse een specifiek blok
   *
   * @param mixed $block  geef de bloknaam van de te parsen template
   * @param mixed $data   geef de Array met data in key=>value pairs
   * @param string $output  geeft soort output
   * @param bool $mergeDefaults moet de defaultArray gemerged worden met de dataArray
   * @return
   */
  function parseBlock($block, $data=array(), $output="var", $mergeDefaults=true)
  {

    $this->block = $block;
    return $this->parse($data, $output, $mergeDefaults);

  }
  /**
   * AE_template::parse() parse met de default template
   *
   * @param $data array() geef de Array met data in key=>value pairs
   * @param $output soort output 'file', 'var', 'echo'
   * @param $mergeDefaults moet de defaultArray gemerged worden met de dataArray
   * @return
   */
  function parse($data=array(),$output="var", $mergeDefaults=true)
  {
    global $template_content;
    global $AE_debug;

    if ($AE_debug)
    {
      echo "<BR>DEBUGMODE<HR>";
      while ( list( $key, $val ) = each( $content ) )
      {
        echo "<br>$key -- $val";
      }
      echo "<HR>";
      while ( list( $key, $val ) = each( $template_content ) )
      {
        echo "<br>$key -- $val";
      }
      echo "<HR>";
    }


    $parseStr = "";
    if ($this->htmlBorder) $parseStr .= "\n<!-- AE template start block:".$this->block." -->\n\n";

    $parseStr .= $this->templateBlock[$this->block];

    if ($mergeDefaults === true)
    {
      $data = array_merge($this->defaultData,$data);
    }

    if ( isset ($template_content) )
    {
      $data = array_merge ($template_content,$data);
    }

    $parseStr = $this->parseSystem($parseStr);

    $parseStr = $this->vtTags($parseStr);

    foreach ( $data as $key => $val )
    {
      $parseStr = str_replace( "{".$key."}", $val, $parseStr);
      if (is_numeric($val) )
      {
        $parseStr = str_replace( "{".$key."|b}", number_format($val,2), $parseStr);
      }
    }

    foreach ( $data as $name => $field )
    {
      if ( is_array($field) ) {
        while ( list( $key, $val ) = each( $field ) )
        {
          $parseStr = str_replace( "{".$name."_".$key."}", $val, $parseStr);
        }
      }
    }



    /** parse icons **/
    if ( ! isset ($ICONS16) ) { include realpath(dirname(__FILE__)."/..") . '/config/icons.php';}
    if ( isset ($ICONS16) ) {
      foreach ( $ICONS16 as $iconKey => $icon ) {
        $parseStr = str_replace( "{icon=".$iconKey."}", drawButton($iconKey), $parseStr);
      }
    }
    
    if ($this->cleanupTags)
    {
      //eregi_replace depreciated in 2.3
      $parseStr = preg_replace( "/\{[a-zA-Z0-9_-|]+\}/i", "", $parseStr);
    }
    if ($this->htmlBorder) $parseStr .= "\n\n<!-- AE template stop block:".$this->block." -->\n\n";

    $this->defaultBlock();

    switch ( strtolower($output) )
    {
      case "file":
        file_put_contents($this->outputFile,$parseStr);
        break;
      case "echo":
        echo $parseStr;
        break;
      default:
        return $parseStr;
    }
  }

  /**
   * AE_template::parseSystem() parse een string met de systeem tags
   * (normaal een onderdeel van parse, is echter ook afzonderlijk aan te roepen)
   *
   * @param mixed $output
   * @return
   */
  function parseSystem($output)
  {
    $dagnaam    = array("zondag","maandag","dinsdag","woensdag","donderdag","vrijdag","zaterdag");
    $dagKort    = array("zo","ma","di","wo","do","vr","za");

    $maandnaam  = array("","januari","februari","maart","april","mei","juni",
                           "juli","augustus","september","oktober","november","december");
    $maandKort  = array("","jan","feb","mrt","apr","mei","jun","jul","aug","sep","okt","nov","dec");

    $systemData = array(
    "timestamp" => date("d-m-Y H:i:s"),
    "dag"       => date("j"),
    "maand"     => date("n"),
    "jaar"      => date("Y"),
    "uur"       => date("G"),
    "min"       => date("i"),
    "sec"       => date("s"),
    "vandaag"   => date("j-n-Y"),
    "tijd"      => date("H:i"),
    "dagnaam"   => $dagnaam[date("w")],
    "dagkort"   => $dagKort[date("w")],
    "maandnaam" => $maandnaam[date("n")],
    "maandkort" => $maandKort[date("n")],
    "week"      => (int)date("W"),
    "dag/jaar"  => date("z"),
    "ip"        => $_SERVER["REMOTE_ADDR"],
    "referer"   => isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : '',
    "uri"       => $_SERVER["REQUEST_URI"],
    "zelf"      => $_SERVER["PHP_SELF"],
    "agent"     => $_SERVER["HTTP_USER_AGENT"],
    "user"      => $this->user,

    );

    foreach ( $systemData as $key => $val)
//    while ( list( $key, $val ) = each( $systemData ) )
    {
      $output = str_replace( "[".$key."]", $val, $output);
    }

    return $output;
  }

  function vtTags($data)
  {
    preg_match_all('/\[vt\](.*?)\[\/vt\]/s', $data, $matches);
    if (is_array($matches))
    {

      foreach ($matches[1] as $item)
      {
        $vtText = vt($item);
        $data = str_replace("[vt]{$item}[/vt]", $vtText, $data);
      }
    }
    return $data;
  }

}

