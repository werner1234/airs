<?php
/*
    AE-ICT
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2017/09/06 08:17:31 $
    File Versie         : $Revision: 1.4 $

    $Log: AE_cls_ajaxLookup.php,v $
    Revision 1.4  2017/09/06 08:17:31  cvs
    megaupdate 201709

    Revision 1.3  2016/10/19 07:15:28  cvs
    call 3856

    Revision 1.2  2016/09/23 12:43:35  cvs
    call 3856

    Revision 1.1  2016/09/23 12:16:13  cvs
    call 3856



*/

class AE_cls_ajaxLookup
{
  var $db;
  var $modules;
  var $triggers;
  var $vbSelectReload = false;
  var $changeRoot;
  var $volg = 0;
  var $extraParameters = "";
  var $template = '
      $("{trigger}").autocomplete(
      {
        source: "{changeRoot}lookups/{lookupfile}",           // link naar lookup script
        change: function(e, ui) 
        {
          if (!ui.item) 
          {
            $( "#popup" ).dialog("open");
            $(this).val("");                                  // reset waarde als niet uit de lookup
          }
        },
        select: function(event, ui)                           // bij selectie clientside vars updaten
        {
          $(this).val(ui.item.{lookupField});
          {vbSelectReload}
        },
        open: function()
        {
          $(".ui-autocomplete").css("width", "500px");
        },
        minLength: 2,                                         // pas na de tweede letter starten met zoeken
        delay: 0,
        autoFocus: true
      });
            
      
  ';

  function AE_cls_ajaxLookup($modules)
  {
    $this->db = new DB();
    if (is_array($modules))
    {
      $this->modules =$modules;
    }
    else
    {
      $this->modules[] = $modules;
    }
  }

  function changeModuleTriggerClass($module,$trigger)
  {
    $this->triggers[$module] = ".".$trigger;
  }

  function changeModuleTriggerID($module,$id)
  {
    $this->triggers[$module] = "#".$id;
  }

  function getJsInTags()
  {

     $out .= "\n<script>
     ".$this->getJS()."\n</script>
     ";
    return $out;
  }

  function getJS()
  {
    $tmpl = new AE_template();
    $tmpl->loadTemplateFromString($this->template, "template");

    $out = '
    $(document).ready(function()
    { 
      $("body").after("<div id=\"popup\">De opgegeven waarde is ongeldig en wordt verwijderd</div>");
      
      $( "#popup" ).dialog({
        modal: true,
        autoOpen: false,
        buttons: [
        {
          text: "Sluit",
          click: function() {  $( this ).dialog( "close" ); $( "{trigger}" ).select();}
        }]
      });
      $(".ui-autocomplete-input").css("min-width","600px");
      
    ';
    foreach ($this->modules as $module)
    {
      $this->volg++;
      $trigger = $this->triggers[$module];
      $data = array(
        "changeRoot"  => $this->changeRoot,
        "trigger"     => $trigger <> ""?$trigger:"#".$module,

      );

      $skipParse = false;
      switch($module)
      {
        case "portefeuille":
          if ($this->extraParameters != "")
          {
            $data["lookupfile"]  = "getPortefeuille.php?".$this->extraParameters;
          }
          else
          {
            $data["lookupfile"]  = "getPortefeuille.php";
          }

          $data["field"]       = "portefeuille";
          $data["lookupField"] = "portefeuille";
          if ($this->vbSelectReload)
          {
            $data["vbSelectReload"] = "updateSelectionPerVb(ui.item.vb, $(this).data('idx'),ui.item.portefeuille)";
          }
          break;
        case "rekening":
          $data["lookupfile"]  = "getRekening.php";
          $data["field"]       = "rekening";
          $data["lookupField"] = "Rekening";
          break;
        case "fonds":
          $data["lookupfile"]  = "getFonds.php";
          $data["field"]       = "fonds";
          $data["lookupField"] = "Fonds";
          break;
        case "client":
          $data["lookupfile"]  = "getClient.php";
          $data["field"]       = "Client";
          $data["lookupField"] = "Client";
          break;
        default:
          $skipParse = false;
      }
      if (!$skipParse)
      {
        $out .= "\n".$tmpl->parseBlock("template", $data);
      }

    }

    $out .= '
    });
  ';
    return $out;
  }
}