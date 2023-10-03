<?php

class AE_cls_mysqlListConfig
{
  private $usersWithSelection;
  private $usersProfileNames = array();
  private $usersProfiles = array();
  private $user = '';

  public function __construct ()
  {
    global $__appvar;

    $this->db = new DB();
    $this->form = new Form();
    $this->getForTable();
  }


  public function getConfigHtml ()
  {
    global $USR;
    $AETemplate = new AE_template();

    return $AETemplate->parseFile('jqueryDialog/mysqlListConfigDialog.html', array(
      'users'         => $this->form->htmlList('listConfigUsers', $this->usersWithSelection),
      'table'         => $_SESSION['tableSettings']['table']
    ));
  }



  private function getForTable()
  {
    global $USR;
    $usersWithSelection = array();
    $query = 'SELECT * FROM ae_config WHERE `field` LIKE "%' . mysql_real_escape_string($_SESSION['tableSettings']['table']) . '\_n\_%" GROUP BY `add_user`';

    $this->db->executeQuery($query);
    while ($selection = $this->db->nextRecord()) {
      if ( $USR !== $selection['add_user'] ) {
        $this->usersWithSelection[$selection['add_user']] = $selection['add_user'];
      }
    }

  }

  public function getHtmlForUserInTable ($user = '', $table = '')
  {
    global $USR;
    if ( requestType('ajax') ) {
      $requestData = array_merge($_GET,$_POST);
      $user = ( isset ($requestData['user']) ? $requestData['user'] : null);
      $table = ( isset ($requestData['table']) ? $requestData['table'] : null);
    }

    if ( empty ($user) || empty ($table) ) {
      return null;
    }

    $user = mysql_real_escape_string($user);
    $table = mysql_real_escape_string($table);

    $this->getUserProfileNameListForTable($user, $table);
    $this->getUserProfileNameListForTable($USR, $table);
    $this->getUserProfilesForTable($user, $table);

    $returnHtml = '<table class="table table-striped" style="width: 100%">';

    $rowCounter = 1;
    foreach ( $this->usersProfiles as $profileName => $profileData ) {
      $volgorde = '';
      if ( isset ($profileData['veldVolgorde']) ) {
        $volgorde .= '<li>' . implode('</li><li>', $profileData['veldVolgorde']) . '</li>';
      }

      $fieldList = array();
      if ( isset ($profileData['fields']) && ! empty ($profileData['fields']) ) {
        foreach ( $profileData['fields'] as $fullFieldName => $active ) {
          if ( $active === 1 ) {
            list ($tableName, $field) = explode('.', $fullFieldName);
            $fieldList[$tableName][] = $field;
          }
        }
      }

      $fieldListHtml = '';
      if ( ! empty ($fieldList) ) {
        foreach ( $fieldList as $tableName => $fieldNames ) {
          $fieldListHtml .= '<strong>'.$tableName.'</strong><ul><li>' . implode('</li><li>', $fieldNames) . '</li></ul>';
        }
      }

      $returnHtml .= '
        <tr data-table="'.$table.'" data-fieldname="' . $profileData['field'] . '"  data-profileName="' . $profileName . '" data-row="profileRow_' . $rowCounter . '" class="table-dark">
          <td style="width: 25px"><button class="btn-xs  btn btn-default openMysqlListprofileInfo"><i class="fa fa-info" aria-hidden="true"></i></button>&nbsp;</td>
          <td style="width: 200px">' . $profileName . '</td>
          <td>
            ' . vt('Kopieer naar profiel') . ': ' . $this->getHtmlProfileSelect ($USR) . '
            <input style="width: 150px" type="text" class="newProfileName" />
            <button data-row="profileRow_'.$rowCounter.'" class="btn-xs btn btn-default mysqlCopyProfile" id=""><i class="fa fa-floppy-o" aria-hidden="true"></i> ' . vt('Toevoegen') . '</button>&nbsp;
          </td>
        </tr>
        <tr class="mysqlListprofileInfo" style="display: none;" >
          <td colspan="3" >
            <table>
              <tr>
                <td style="border:none;"><strong>'.vt('Volgorde').'</strong><ol>'.$volgorde.'</ol></td>
                <td style="border:none;"><strong>'.vt('Velden').'</strong><ul>'.$fieldListHtml.'</ul></td>
              </tr>
            </table>
          </td>
        </tr>
      ';
      $rowCounter++;
    }
    $returnHtml .= '</table>';

    echo $returnHtml;



  }

  public function getForUserInTable ($user = '', $table = '')
  {
    if ( requestType('ajax') ) {
      $requestData = array_merge($_GET,$_POST);
      $user = ( isset ($requestData['user']) ? $requestData['user'] : null);
      $table = ( isset ($requestData['table']) ? $requestData['table'] : null);
    }

    if ( empty ($user) || empty ($table) ) {
      return null;
    }

    $user = mysql_real_escape_string($user);
    $table = mysql_real_escape_string($table);

    $this->getUserProfileNameListForTable($user, $table);
    $this->getUserProfilesForTable($user, $table);

  }

  public function getUserProfilesForTable ($user, $table)
  {
    if ( empty ($this->usersProfileNames[$user]) ) {
      $this->getUserProfileNameListForTable($user, $table);
    }
    // Get profile for user
    $query = 'SELECT * FROM ae_config WHERE `add_user` = "' . $user . '" AND `field` LIKE "%' . $user . '_' . $table . '%"';

    $this->db->executeQuery($query);
    while ($profiles = $this->db->nextRecord()) {

      $profileName = str_replace($user . '_' . $table,'',$profiles['field']);
      if ( isset ($this->usersProfileNames[$user][$profileName]) ) {
        $profileName = $this->usersProfileNames[$user][$profileName];
      } elseif ( is_numeric($profileName) ) {
        $profileName = 'profiel ' . $profileName;
      } else {
        $profileName = 'standaard';
      }

      $this->usersProfiles[$profileName] = unserialize($profiles['value']);
      $this->usersProfiles[$profileName]['field'] = $profiles['field'];
    }
  }

  public function getUserProfileNameListForTable ($user, $table)
  {
    $query = 'SELECT * FROM ae_config WHERE `add_user` = "' . $user . '" AND `field` = "' . $table . '_n_' . $user . '"';
    if ( $profileList = $this->db->lookupRecordByQuery($query) ) {
      $this->usersProfileNames[$user] = unserialize($profileList['value']);
    }

    return $this->usersProfileNames;
  }

  public function getProfileNameListForTable ($user)
  {
    $profielSelect = array();
    foreach(range(1,20) as $index) {
      $profileName = 'profiel ' . $index;
      if ( isset ($this->usersProfileNames[$user][$index]) && ! empty ($this->usersProfileNames[$user][$index]) ) {
        $profileName = $this->usersProfileNames[$user][$index];
      }
      $profielSelect[$index] = $profileName;
    }
    return $profielSelect;
  }

  public function getHtmlProfileSelect ($user)
  {
    $profileList = $this->getProfileNameListForTable($user);
    $profielSelect = '';

    foreach ( $profileList as $index => $profileName ) {
      $profielSelect .= '<option value="' . $index . '" ' . ($_SESSION['tableSettings']['profiel'] === $index ? 'selected':'') . '>' . $profileName . '</option>';
    }

    $html = '
      <select class="profiel" type="select"  name="profiel"  id="" >
        <option value="">standaard</option>
        ' . $profielSelect . '
      </select>
    ';

    return $html;
  }

  public function copyFromProfileToProfile ()
  {
    global $USR;
    $cfg = new AE_config();
    if ( requestType('ajax') ) {
      $requestData = array_merge($_GET,$_POST);
    }

    $profileList = $cfg->getData($requestData['from']);

    if ( ! empty ($profileList) ) {
      $toName = (! empty ($requestData['toName']) ? $requestData['toName'] : $requestData['profileName']);
      $cfg->addItem($USR . '_' . $requestData['table'] . '' . (int) $requestData['to'],$profileList);

      $data=unserialize($cfg->getData($requestData['table'] . '_n_' . $USR));
      $data[(int) $requestData['to']] = $toName;
      $cfg->addItem($requestData['table'] . '_n_' . $USR,serialize($data));
    }



  }
}