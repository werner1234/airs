<?php
/*
    AE-ICT source module
    Author  						: $Author: rvv $
 		Laatste aanpassing	: $Date: 2020/04/26 13:31:52 $
 		File Versie					: $Revision: 1.15 $
*/

class Menu {

  var $menuItems        = array();
  var $tree             = array();
  var $edit             = false;
  var $defaultTarget    = "content";
  var $style            = "";
  var $menuId           = "menu";


  function Menu()
  {
    global $USR;

    //$isae3402Img='iVBORw0KGgoAAAANSUhEUgAAAEYAAAAQCAMAAACcJ/lcAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAA3RpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNi1jMDY3IDc5LjE1Nzc0NywgMjAxNS8wMy8zMC0yMzo0MDo0MiAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wUmlnaHRzPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvcmlnaHRzLyIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcFJpZ2h0czpNYXJrZWQ9IlRydWUiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENDIDIwMTUgKFdpbmRvd3MpIiB4bXBNTTpJbnN0YW5jZUlEPSJ4bXAuaWlkOjJBQzUxQjIyODZFMzExRUE4MDQ0RDIxNzQ2QTU3RTA3IiB4bXBNTTpEb2N1bWVudElEPSJ4bXAuZGlkOjJBQzUxQjIzODZFMzExRUE4MDQ0RDIxNzQ2QTU3RTA3Ij4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6MkFDNTFCMjA4NkUzMTFFQTgwNDREMjE3NDZBNTdFMDciIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6MkFDNTFCMjE4NkUzMTFFQTgwNDREMjE3NDZBNTdFMDciLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz7wW5+HAAAAYFBMVEX8/PvNn1QuODOnqbLo6Ojpx37y5crT09KPjo/Ly8rRspD59eo9czy2gTHm2MeIpISzs7bhy6ucnJ9eW13c3d3479fGydTv7u59fH6fuJ2+vbvk8OL39/f5+fn4+Pj///8FNpVqAAACSUlEQVR42pRT2bLbIAyVjcLmxLExNoQr0P//ZQVp79LpSzUjmcUcDjoS8L8NqIlRq+2nJTbPCn8ttgp/zi1V/Fz4PGWJgKkRjN0qExl3aABiznncUmXKw/uVnzDrfgrEAqde6rrLycYOU9m2xiUwx62UGOWq1/wRbXbMduNwXOIK4AvG7/ocMOepd4/Chq3xmTfk7YgClQw/nx+v+ck255nLbLfDH/nAOfA3GM+LFhTQmvVjHTCuWlvYWaFTclDwMd/v9yjU5rxla4/Ah2U1ly8YqD3Wfdfv6YApJqaMwZqMTlEkft2fLybrtlloiPMc8lG4fs/NfsJtekxa3rcOGBVdMCWWxKqgchcyvAL0JFsLbC3mQ4VZWfftUaufbsP0bbKeh1LAGLteVaRLiCRxIcFHfJ8hAQZKNNhg4hKvi/xxG0jTY6cuIkd3GWMu5+Tr5IstYsREsihrTtZMjw4HG5U5m6yO2ezTJEjaT9TZVEKhkHpM3ZCq8CGxvpzS2yVK/XWY2WwgqcoP3qeHB+1FtcEmRuBkEkuQ/DYndYMIbAxxxcqt79cYB5tLBauSnS2LgKcGuLGeOhsuRf43oSsciqTD9JFh3jZicsRJ9VDKSLHCFEJUPquiFykdqZs1r72KY5FzWORK4ZUqYHcEMKn2BwO5SwS4rnduuL+eyu41rhGNKO9F49aVkqdJHxLV3o0SW09Pg96vVXoXRoDfPVWpAuF6AuqF9Lq0VYTsqRSA0edj9A591n7sjfBZN32wLCDt+Z60+h8GvwQYAIWiWZlUK6MwAAAAAElFTkSuQmCC';
    $isae3402Img='iVBORw0KGgoAAAANSUhEUgAAAFcAAAAUCAMAAADP2lGcAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAA3RpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuNi1jMDY3IDc5LjE1Nzc0NywgMjAxNS8wMy8zMC0yMzo0MDo0MiAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wUmlnaHRzPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvcmlnaHRzLyIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcFJpZ2h0czpNYXJrZWQ9IlRydWUiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENDIDIwMTUgKFdpbmRvd3MpIiB4bXBNTTpJbnN0YW5jZUlEPSJ4bXAuaWlkOjQ5OEQ3N0Y2ODdDMTExRUFBMzNCOTc2OEE5RTA2NzZGIiB4bXBNTTpEb2N1bWVudElEPSJ4bXAuZGlkOjQ5OEQ3N0Y3ODdDMTExRUFBMzNCOTc2OEE5RTA2NzZGIj4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6NDk4RDc3RjQ4N0MxMTFFQUEzM0I5NzY4QTlFMDY3NkYiIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6NDk4RDc3RjU4N0MxMTFFQUEzM0I5NzY4QTlFMDY3NkYiLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz4+qeI5AAAAYFBMVEXMyskfKRzcsFTv1Jrg4OD26cyqqqv8/Px9fIDWuZHy8vH7897m1sE/czy3hDr9+e2Rk5WwxKy/vr5nj2fW1tby6uHFn3Ht7ez09PVVVFvn5+j5+fn6+vr39/f4+Pj////TQzneAAADWUlEQVR42pxV2ZbjKgyEGBBgJyaEmN36/78cQdK3uzP3aXRO2FMUWsoM/zb4b9T6Oay/ug/rCYVL8H9bZ2UfmLvcEaR8QwMBVqx9XEFAiH00ZCecNVV86Dmbq73hV0PA37ggt5Vg1xXsKiWi3J6Tb08Ji2c0hp5qbzEwJipCanjnd0QfEE6MtBQGWPAT+Afutpm1revgu680Y8/BF6NrQTvtesXsEMBppUMhQIK9NpFz1h0F1+gyf6DmORf4iftUx7o8rSRQKZ+b3A4zcYtGnYmWQK+1wKQjHU7Xx93x6wNVBsVTylwHHhLXnCHP+BO3Hwc9XnaQKzzJJdtte/HVyRJdgaiDcti0Vi4g49fr9XGeiIrrlL3XiuMkoLn/hQu7HXGDXW77PuZ2H/7F4mJA67OwWenrSeg9phMf18c1YFWMfJC543lgchdyTtjr+Ssf4gjX5XaYFdflnQ/EF90jBF2c6lU71Ir5QNfdH3ds6LjSWTGVechaZ8Zp7n/5F81hbvtymSaXmzlmPkBkCZTTAT29R3gaO+ehY7hDOxs5WL/TQGRdrHNa//YDPr25LHJZjFmkuRzG7IMvMX4lc6UcbrMbcUsxVRtTmnvte+OdzxP3xMZEYQxkPhZz2y7LstyOUme9QQ+efZgPLRYhRInw9yZjAV7+1QqzCno4yi+X5UK0F3kzbfq3UZTKsGRHa22ktlNZnOf4WTuWx1qJaXYp2jr52kwZ6qsGzvO6mxvZgpdLlpNvh8QiAHQxHyDECQghlNZq7LUmIcgVnfwBSRQatiDgVcfKZXS+unyTOfdujmPB5UIXvPhaeinVbFCjFUpR2Tam6CrLLC07Vtuo4IpeBaATnoUZt+BKptosWfETs0FcV9IGwOcrH0gbUoiAzImJ7inaKBgpjFWE61WhM8xPXFGHSAg1cekgc44VZ27OsxWo3nCohHQE3s9KoQmtElQgGHKwgAqFKDUrUgVRsHcoAidipS3mv/QBUrAliMP44xmYEcEwZrwxYepkxzrSrUGiMLVaG4naOVV37LZGxUVtqglaGuFoAF96VkSkUHpvotgoe4p4hsiMiS+dpD+O7kvNSeTH0qfIf0v6bH/WBb2bnEAivJIrcJ36+6/2+b2AFWbk3rPa/tH+CDAApiKP//t+QGoAAAAASUVORK5CYII=';

    $this->menuEnd = '<li><a href="#"><span id="sessionstatus">'.$USR.'</span> &nbsp;&nbsp;<span id="loadTime"></span> &nbsp;&nbsp;<span id="statusLights"></span> </a> </li>
		<li><a href="javascript:void(0);" onclick="javascript:checkChange(\'login.php?logout=true\',\'_top\')">'.vt("Uitloggen").'</a></li>
		<li ><img style="  " src="data:image/png;base64,'.$isae3402Img.'"></li>';

  }

  function addItem($node = "", $name, $action, $separator = 0, $target = "",$help=array())
  {
    $target = ( empty ($target) ? $this->defaultTarget : $target );
    $node = ( empty ($node) ? '__main__' : $node );
    if( ! is_array($this->menuItems[$node]) ) {
      $this->menuItems[$node] = array();
    }

    $this->menuItems[$node][] = array(
      "node"          => $node,
      "name"          => vt($name),
      "originalName"  => $name,
      "action"        => $action,
      "seperator"     => $separator,
      "target"        => $target,
      "help"          => $help
    );
  }

  function createMenu($type = "list")
  {
    switch($type)
    {
      case "list" :
        return $this->buildList();
        break;
    }
  }

  function buildList($submenu = "__main__")
  {
    $main = $this->menuItems[$submenu];

    if($submenu == "__main__") {
      $result = "<ul id=\"".$this->menuId."\" >\n"; //style=\"display: none;\"
    } else {
      $result = "<ul><div>\n";
    }

//    debug($main);
    foreach ( $main as $itemID => $menuItem )
//    for($a = 0; $a < count($main); $a++)
    {

      if (stristr($menuItem['action'],"submenu="))
      {
        // print menu
        if(count($this->menuItems[substr($menuItem['action'],8)]) > 0)
        {
          $result .= "<li>\n";
          $result .= '<a data-field="'.$menuItem['originalName'].'" href="#">' . $menuItem['name'] . '</a>';
          $result .= $this->buildList(substr($menuItem['action'],8));
          $result .= "</li>\n";
        }
      }
      else
      {
        if ( isset ($menuItem['name']) && ! empty ($menuItem['name']) ) {
          $result .= "<li>";
          $result .= '<a class="mainMenuLinkItem" data-field="'.$menuItem['originalName'].'" href="#" onclick="javascript:checkChange(\''. substr($menuItem['action'],4) .'\', \''.$menuItem['target'].'\')">'.$menuItem['name'].'</a>';
          $result .= "</li>\n";
        } else {
          $result .= "<li><hr></li>\n";
        }
      }
    }

    if($submenu == "__main__")
      $result .=$this->menuEnd;
    $result .= "</ul>\n";



    return $result;
  }



  function buildTree()
  {
    $nodeVertaling=array('__main__'=>'__main__');
    foreach ($this->menuItems as $key=>$value)
    {
      foreach ($value as $index=>$sleutelData)
      {
        if (stristr($sleutelData['action'],"submenu="))
          $nodeVertaling[substr($sleutelData['action'],8)]=$sleutelData['name'];
      }

    }

    foreach ($this->menuItems as $key=>$value)
    {
      foreach ($value as $index=>$sleutelData)
      {
        $nodeName=$nodeVertaling[$sleutelData['node']];
        if (stristr($sleutelData['action'],"submenu="))
        {
          $newNodeName=substr($sleutelData['action'],8);
          // if($key=='__main__')
          $newNodeName=$nodeVertaling[$newNodeName];

          $this->tree[$nodeName][$newNodeName]=array();
        }
        else
        {
          // listarray($sleutelData);
          if($sleutelData['name'] !='' && $sleutelData['node'] <> 'htekst')
            $this->tree[$nodeName][$sleutelData['name']]=array_merge($sleutelData['help'],array('url'=>basename(substr($sleutelData['action'],4),'.php')));
        }
      }

      foreach ($this->tree as $key=>$data)
      {
        if(is_array($data))
        {
          foreach ($data as $menu=>$items)
          {
            if(isset($this->tree[$menu]))
              $this->tree[$key][$menu]=&$this->tree[$menu];
          }
        }
      }


    }
  }

  function getText($key,$objects)
  {
    $db=new DB();
    $query="SELECT txt FROM help_tekst where titel='".mysql_escape_string($key)."'";
    $db->SQL($query);
    $data=$db->lookupRecord();
    $txt=$data['txt'];

    if(is_array($objects))
    {
      foreach ($objects as $objectNaam)
      {
        $object=new $objectNaam();
        $table=$object->data['table'];

        $txt.="<br><br>Veld informatie bij $key:<br>";
        foreach ($object->data['fields'] as $veld=>$opties)
        {
          if($opties['form_visible']==true)
          {
            $query="SELECT txt FROM help_velden WHERE veld='".strtolower($table.".".$veld)."'";
            $db->SQL($query);
            $data=$db->lookupRecord();

            $txt.="<b>".$opties['description']."</b><br>";
            $txt.="".$data['txt']."<br>";
          }
        }
      }
    }
    return $txt;
  }

  function checkVulling($key,$url)
  {
    global $db;
    if(!is_object($db))
      $db=new DB();
    $query="SELECT id FROM help_tekst WHERE titel='".mysql_real_escape_string($key)."' AND url='".mysql_real_escape_string($url)."'";
    //echo $query."<br>\n";
    if($db->QRecords($query)>0)
      $vulling="*  ";
    else
      $vulling="";
    //echo $query."<br>\n";
    return $vulling;
  }


  function showTree($item='__main__')
  {
    $db=new DB();
    $base=&$this->tree[$item];
    $helpUrl="help_tekstEdit.php";
    if(is_array($base))
    {
      foreach ($base as $key=>$value)
      {
        if(is_array($value))
        {
          $vulling=$this->checkVulling($key,$value['url']);
          if($this->edit==true)
            $result.= "<ul><li><a href=\"$helpUrl?key=".urlencode($key)."&url=".$value['url']."\">$vulling<b>$key</b> </a>";
          else
            $result.= "<ul><li ><a href=\"javascript:SwitchMenu('$key')\">$vulling<b>$key</b> </a>";

          $result.=$this->showTree($key);
          if($this->edit==false)
            $result.= "<div id=\"$key\" > ".$this->getText($key,$value['objects'])."</div>";


          //if($value['url'] <> '')
          //  $result.=" <SMALL>url(".$value['url'].")</SMALL> ";

          //if(is_array($value['objects']))
          //  $result.=" <SMALL> objects(".implode(',',$value['objects']).") </SMALL>";

          if(is_array($value['pages']))
          {
            $result.= "<ul>";
            foreach ($value['pages'] as $page)
            {
              if($this->edit==true)
              {
                $vulling=$this->checkVulling($page);
                $result.= "<li><a href=\"$helpUrl?key=$page\">$vulling $page</a></li>";
              }
              else
              {
                $result.= "<li><a href=\"javascript:SwitchMenu('$page')\">$page</a>";
                $result.= "<div id=\"$page\"> ".$this->getText($page)."</div>";
              }

            }
            $result.= "</ul>\n";
          }
          $result.= "</li></ul>\n";
        }
      }
    }


    return $result;
  }

}
?>
