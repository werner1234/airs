<?php

include_once("wwwvars.php");

define('ds', DIRECTORY_SEPARATOR);

if ( isset ($_GET['type']) ) {
  if ( $_GET['type'] === 'records' ) {
    $dir = $__appvar["recordsdir"];
  } elseif ( $_GET['type'] === 'widget' ) {
  $dir = $__appvar["basedir"] . '/html/widget/';
}
} else {
  echo 'Geen type opgegeven';
  exit();
}
$dir = $__appvar["basedir"] . '/html';

// debug($dir);
// debug($_POST);
class i18n
{
  private $scanDir = '';
  private $translations = array();


  private $fileSkipList = array(
    'reportBuilder.php',
    'queryWizard.php',
  );

  private $skip = array(
    '.',
    '..',
    '.gitignore',
    '.git',
    '.idea',
    'temp',
    'css',
    'style',


    'widget',
    'templates',
    'reconV3',
    'reconBewaarder',
    'recon',
    'rapport',
    'lookups',
    'javascript',
    'integrityCheck',
    'import',
    'images',
    'icon',
    'hmenu',
    'importdata',
    'font',
    'facmod',
    'custom',
    'apiExternal',
    'api',
    'ajax',
    'RTF_templates',
    'Mylo',
    'HTMLrapport',
    'CRM_include',
    'classTemplates'

  );

  private $functions = array(
    'vt',
    'vtb',
  );
  private $functionPatterns = array(
    'vt'  => '/vt',
    'vtb'  => '/vtb'
  );

  private $fileList = array();

  public function scanForFiles ($dir)
  {
    $this->scanDir = $dir;
    $this->fileList = $this->listFolderFiles($dir);
  }


  private function listFolderFiles($dir)
  {
    $fileInfo     = scandir($dir);
    $allFileLists = array();

    foreach ($fileInfo as $folder) {
      if ( ! in_array ($folder, $this->skip)) {
        if (is_dir($dir . DIRECTORY_SEPARATOR . $folder) === true) {
//          $allFileLists = array_merge( $this->listFolderFiles($dir . DIRECTORY_SEPARATOR . $folder), $allFileLists);
        } else {
          $allFileLists[$dir . DIRECTORY_SEPARATOR . $folder] = $folder;
        }
      }
    }
    return $allFileLists;
  }

  public function getFileList ()
  {
    return $this->fileList ? $this->fileList : array();
  }

  public function getTranslations ()
  {
    return $this->translations ? $this->translations : array();
  }


  public function extractTranslations ()
  {
    $counter = 0;
    $pattern = '/(' . implode('|', array_values($this->functions)) . ')\s*\(/';
//debug($pattern);
//      vt()
    $pattern2 = '#[' . implode('|', array_keys($this->functionPatterns)) . ']\s*\[#';
    //echo '<pre>';
    //print_r($pattern2);
    // echo '</pre>';

    foreach ( $this->getFileList () as $fileLocation => $fileName ) {

      if (
        ! is_dir ($fileLocation) &&
        ! in_array ($fileName, $this->fileSkipList)
        &&
        (strpos($fileLocation, '.php') !== false ) ) { // || strpos($fileLocation, '.html') !== false

        $code = file_get_contents($fileLocation);

        if (preg_match($pattern, $code) === 1 || preg_match($pattern2, $code) === 1) {
          $allTokens = token_get_all($code);

          $this->_tokens = array();
          foreach ($allTokens as $token) {
            if (!is_array($token) || ($token[0] !== T_WHITESPACE && $token[0] !== T_INLINE_HTML)) {
              $this->_tokens[] = $token;
            }
          }
          unset($allTokens);

          foreach ($this->functions as $functionKey => $functionName) {

            $count = 0;
            $tokenCount = count($this->_tokens);

            while ($tokenCount - $count > 1) {
              //Token uit array halen.
              $countToken = $this->_tokens[$count];

              // wanneer het geen array is deze overslaan
              if ( ! is_array($countToken)) {
                $count++;
                continue;
              }

              // Volgende token ophalen op te controleren of die een ( is
              $firstParenthesis = $this->_tokens[$count + 1];

              // Split de array naar type, functie en line nr
              list($type, $string, $line) = $countToken;
              if (($type === T_STRING) && ($string === $functionName) && ($firstParenthesis === '(')) {
                $position = $count;
                $target = 1;

                $strings = array();
                $count1 = count($strings);


                while ( $count1 < $target ) {
                  $count1 = count($strings);

                  if ($this->_tokens[$position][0] === T_CONSTANT_ENCAPSED_STRING && $this->_tokens[$position + 1] === '.') {
                    $string = '';
                    while ( $this->_tokens[$position][0] === T_CONSTANT_ENCAPSED_STRING ) {
                      if ($this->_tokens[$position][0] === T_CONSTANT_ENCAPSED_STRING) {
                        $string .= $this->_formatString($this->_tokens[$position][1]);
                      }
                      $position++;
                    }
                    $strings[] = $string;
                  } elseif ($this->_tokens[$position][0] === T_CONSTANT_ENCAPSED_STRING) {
                    $strings[] = $this->_formatString($this->_tokens[$position][1]);
                  } elseif ($this->_tokens[$position][0] === T_LNUMBER) {
                    $strings[] = $this->_tokens[$position][1];
                  }
                  $position++;
                }

                if ( ! empty ($strings)) {

                  if (  $strings[0]  === strip_tags($strings[0]) && strlen(str_replace(' ', '', $strings[0])) > 1) {//<option
                  $this->translations[$strings[0]] = array (
                    'origin'                 => $fileLocation,
                    'veld'                   => $strings[0],
                    'line'                   => $line,
                    'type'                   => $functionName,
                  );

                  }
                }
              }

              $count++;
            }
          }
        }
        $counter++;
      }
    }
  }


  public function processNewTranslations ()
  {
    global $USR;

    // voorkom dubbele vertalingen
    $db = new DB();
    $query = "SELECT * FROM `appVertaling`;";

    $currentTranslations = null;
    $db->executeQuery($query);
    while ($currentTranslation = $db->nextRecord()) {
      $currentvtbTranslations[$currentTranslation['veld']] = $currentTranslation['veld'];

      $vtveld = trim($currentTranslation['veld']);
      $vtveld = strtolower(trim($vtveld));
      $currentvtTranslations[$vtveld] = $vtveld;
    }

    $newTranslations = array();
    foreach ( $this->translations as $key => $value ) {
      $veldValue = $value['veld'];

      $add = false;

      if ( $value['type'] === 'vt' ) {

        $vtveld = trim($veldValue);
        $vtveld = strtolower(trim($vtveld));

        if ( ! isset($currentvtTranslations[$vtveld]) ) {
          $add = true;
        }

      } else {
        if ( ! isset($currentvtbTranslations[$veldValue]) ) {
          $add = true;
        }
      }

      if ( $veldValue[0] === '$' || $veldValue === '' || strpos($veldValue, '<br>') !== false || strpos(htmlentities($veldValue), '&quot;&gt;') !== false) {
        $add = false;
      }

      if ( $add === true ) {
        $this->newTranslations[] = array(
          'add_user'                => '',
          'change_user'             => '',
          'veld'                    => $veldValue,
          'nl'                      => ($veldValue),
          'en'                      => '(en)' . $veldValue,
          'fr'                      => '',
          'du'                      => '',
          'orgin'                  => $value['origin'] . ":" . $value['line'],
        );
      }
    }
  }

  private function _formatString($string)
  {
    $quote = substr($string, 0, 1);
    $string = substr($string, 1, -1);

    // Bij " quote weghalen anders escapen
    if ($quote === '"') {
      $string = stripcslashes($string);
    } else {
      $string = strtr($string, array("\\'" => "'", '\\\\' => '\\'));
    }
    $string = str_replace("\r\n", "\n", $string);

    return $string;
  }


}

$i18n = new i18n();
$i18n->scanForFiles($dir);
$i18n->extractTranslations();
$i18n->processNewTranslations();


$_SESSION['NAV'] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));

if ( isset ($_POST['vertaal']) ) {
  $db = new DB();
  $query = '';
  $queryKeys = '';

  foreach ( $_POST['vertaal'] as $hash => $translateData ) {
    $translateData['add_date'] = date('Y-m-d H:i:s');
    $translateData['change_date'] = date('Y-m-d H:i:s');

    $translateData = array_map('mysql_escape_string', $translateData);
    if ( ! $queryKeys ) {

      $queryKeys = implode(", ",array_keys($translateData));
    }

    $query  .= (! empty($query) ? ',':'' ) . '("'.implode('", "', $translateData) . '")';

  }

  $queryHead = "
    INSERT INTO
      `appVertaling`
      (" . $queryKeys . ") VALUES 
  ";
//debug($queryHead);
//  $db->executeQuery($queryHead . $query);

//  header("Location: portaalvertalingList.php");
// debug($db);
}elseif ( ! isset ($_GET['step']) ) {

  if ( empty ($i18n->newTranslations) ) {
    $_SESSION['translateMessage'] = 'Geen nieuwe vertalingen gevonden!';
//    header("Location: portaalvertalingList.php");
  }

  echo template($__appvar["templateContentHeader"],$content);
  echo '<h2>'.vt('Nieuwe vertalingen').'</h2>';




  $query = '';
  $queryKeys = '';

  foreach ( $i18n->newTranslations as $hash => $translateData ) {
    $translateData['add_date'] = date('Y-m-d H:i:s');
    $translateData['change_date'] = date('Y-m-d H:i:s');

    $translateData = array_map('mysql_escape_string', $translateData);
    if ( ! $queryKeys ) {

      $queryKeys = implode(", ",array_keys($translateData));
    }

    $query  .= (! empty($query) ? ',':'' ) . '("'.implode('", "', $translateData) . '")' . "\n";

  }

  $queryHead = "
    INSERT INTO
      `appVertaling`
      (" . $queryKeys . ") VALUES 
  ";
//  debug($queryHead . $query);









  $htmlData = '';


  $_SESSION['NAV']->addItem(new NavHeader('<a href="#" onclick="parent.content.submitForm()">
        
  <img src="images//16/save.gif" width="16" height="16" border="0" alt="(en)sla de wijzigingen op" 
  align="absmiddle"> '.vt('Opslaan').'</a>'));


  $htmlData .= '
    
    <form name="editForm" method="post">
    
  ';


  $htmlData .= '
  <table style="width:98%" class="list_tabel" id="transTable" cellspacing="0">
  <thead>
    <tr class="list_kopregel">
      <td class="list_kopregel_data">Type</td>
      <td class="list_kopregel_data">Veld</td>
      <td class="list_kopregel_data">NL</td>
      <td class="list_kopregel_data">EN</td>
      <td class="list_kopregel_data">FR</td>
      <td class="list_kopregel_data">DU</td>
      <td class="list_kopregel_data">hash</td>
      <td class="list_kopregel_data">origin</td>
    </tr>
  </thead>
  <tbody id="fbody">
  
  
  
  ';

  if ( $i18n->newTranslations ) {
    foreach ( $i18n->newTranslations as $key => $vtdata ) {

      $nlValue = ( isset ($_POST['vertaal'][$vtdata['hash']]['nl']) ? $_POST['vertaal'][$vtdata['hash']]['nl'] : $vtdata['nl'] );
      $enValue = ( isset ($_POST['vertaal'][$vtdata['hash']]['en']) ? $_POST['vertaal'][$vtdata['hash']]['en'] : $vtdata['en'] );
      $frValue = ( isset ($_POST['vertaal'][$vtdata['hash']]['fr']) ? $_POST['vertaal'][$vtdata['hash']]['fr'] : $vtdata['fr'] );
      $duValue = ( isset ($_POST['vertaal'][$vtdata['hash']]['du']) ? $_POST['vertaal'][$vtdata['hash']]['du'] : $vtdata['du'] );
      // $_POST['vertaal'];




      // debug($vtdata);
      $htmlData .= '
        <tr>
          <td class="listTableData">
            <input name="vertaal['.$vtdata['hash'].'][type]" type="hidden" value="'.$vtdata['type'].'"/>
            <input name="vertaal['.$vtdata['hash'].'][origin]" type="hidden" value="'.$vtdata['origin'].'"/>

            <input name="vertaal['.$vtdata['hash'].'][add_user]" type="hidden" value="'.$vtdata['add_user'].'"/>
            <input name="vertaal['.$vtdata['hash'].'][change_user]" type="hidden" value="'.$vtdata['change_user'].'"/>
            <input name="vertaal['.$vtdata['hash'].'][hash]" type="hidden" value="'.$vtdata['hash'].'"/>
          
          '.$vtdata['type'].'
          
          </td>
          
          <td class="listTableData"><input name="vertaal['.$vtdata['hash'].'][veld]" type="hidden" value="' . htmlentities($vtdata['veld']) . '"/>'.htmlentities($vtdata['veld']).'</td>
          <td class="listTableData"><input name="vertaal['.$vtdata['hash'].'][nl]" type="text" value="' . htmlentities($nlValue) . '"></td>
          <td class="listTableData"><input name="vertaal['.$vtdata['hash'].'][en]" type="text" value="' . htmlentities($enValue) . '"></td>
          <td class="listTableData"><input name="vertaal['.$vtdata['hash'].'][fr]" type="text" value="' . htmlentities($frValue) . '"></td>
          <td class="listTableData"><input name="vertaal['.$vtdata['hash'].'][du]" type="text" value="' . htmlentities($duValue) . '"></td>
          <td class="listTableData"><input name="vertaal['.$vtdata['hash'].'][hash]" type="text" value="' . $vtdata['hash'] . '"></td>
          <td class="listTableData"><input name="vertaal['.$vtdata['hash'].'][orgin]" type="text" value="' . $vtdata['orgin'] . '"></td>
        </tr>
      ';

    }
  }



  $htmlData .= '
  </tbody>
  </table>
  </form>
  
  ';



  echo '
  <table style="width:50%" class="list_tabel" id="" cellspacing="0">
  <thead>
    <tr class="list_kopregel">
      <td class="list_kopregel_data">Nieuwe vertalingen: ' . count($i18n->newTranslations) . '</td>
    </tr>
  </thead>
  <tbody id="">
  
  ';


  echo  '
  </tbody>
  </table>
<br />
<br />
  ';


  echo $htmlData;

  echo '
  <script>

  function submitForm()
  {
      //preSubmit//
      document.editForm.submit();
      //postSubmit//
  }

  </script>
  
  
  
  ';

}





echo template($__appvar["templateRefreshFooter"],$content);

