<?php
/*
    AE-ICT CODEX source module versie 1.5 (simbis), 30 mei 2012
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2018/02/15 07:08:19 $
    File Versie         : $Revision: 1.2 $


*/


$__appvar["module"] = "";
include_once("wwwvars.php");


$tabblad = ($_GET["tab"] != "")?$_GET["tab"]:"";

$_SESSION["widgetReturnUrl"] = $_SERVER["REQUEST_URI"];
//debug($_SERVER);
$db = new DB();
include_once ("../classes/AE_cls_Widgets.php");
$wdg = new AE_cls_Widgets($tabblad);
//debug($wdg);

$widgetReload = false;
$cfg = new AE_config();
$layName = $wdg->layName;

$layout = $wdg->getLayoutSettings();

if ($widgetReload)
{
  header("location: ".$PHP_SELF);
}

$optionStr = "";
for ($x=0; $x <= $wdg->maxWidgetsAllowed; $x++)
{
  $selected = ($x == $wdg->widgets)?"SELECTED":"";
  $optionStr .= "<option value='$x' $selected>" . vtb('%s widgets', array($x)) . "</option>";
}

//$wdg->showSettings();

session_start();

$content = array(
  "jsincludes" => '
   
    <link rel="stylesheet" href="widget/css/gridstack.css">
    <link rel="stylesheet" href="widget/css/font-awesome.min.css">
    <link rel="stylesheet" href="widget/css/Bootstrap_3_2_0.css"> 
    <link rel="stylesheet" href="widget/css/jquery-ui-1.11.1.custom.css"  type="text/css" media="screen">

    <script src="widget/js/jquery.min.js"></script>
    <script src="widget/js/bootstrap.min.js"></script>
    
    <script src="widget/js/jquery-ui.js"></script>
    
    <script src="widget/js/lodash.min.js"></script>
    <script src="widget/js/knockout-min.js"></script>
    <script src="widget/js/gridstack.js"></script>
    <script src="widget/js/gridstack.jQueryUI.js"></script>
    <script type="text/javascript" src="javascript/algemeen.js"></script>
    <script src="widget/js/bootstrapTooltip.js"></script>
   '
  );


$template_content["style"] .= "\n    <link href='style/welcome.css' rel='stylesheet' type='text/css' media='screen'>";
echo template($__appvar["templateContentHeader"],$content);


if ($__develop)
{
//  debug($_SESSION["usersession"]["gebruiker"]);
}

?>
  <style type="text/css">
    .ui-dialog .ui-dialog-titlebar-close span {
      display: block;
      margin: -8px!important;
    }

    .grid-stack {
      background: white;
    }

    .grid-stack-item-content {
      color: #2c3e50;
      text-align: center;
      background-color: #999;
      border-radius: 6px;

      /* Permalink - use to edit and share this gradient: http://colorzilla.com/gradient-editor/#e5e5e5+0,ffffff+100 */
      background: rgb(229,229,229); /* Old browsers */
      background: -moz-linear-gradient(top,  rgba(229,229,229,1) 0%, rgba(255,255,255,1) 100%); /* FF3.6-15 */
      background: -webkit-linear-gradient(top,  rgba(229,229,229,1) 0%,rgba(255,255,255,1) 100%); /* Chrome10-25,Safari5.1-6 */
      background: linear-gradient(to bottom,  rgba(229,229,229,1) 0%,rgba(255,255,255,1) 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ */
      filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#e5e5e5', endColorstr='#ffffff',GradientType=0 ); /* IE6-9 */
      border: 2px solid #AAA;

    }
  </style>
  <div id="setupDialog" title="<?= vt('Instellen persoonlijke voorkeuren'); ?>">
    <div id="tabs">
      <ul>
        <li><a href="#tabs-1"><?= vt('Widget selectie'); ?></a></li>
<!--        <li><a href="#tabs-2">Widget configuratie</a></li>-->
      </ul>
      <!-- tab1 -->
      <div id="tabs-1" style="padding:0px;">
        <br/>
        &nbsp;&nbsp;&nbsp;<?= vt('Hoeveel widgets wilt u op uw beginscherm?'); ?> <select name="aantalWidgets" id="aantalWidgets" data-prev="<?=$wdg->widgets?>"><?=$optionStr?></select>
        <div>
           <ol>
<?
            for ($x=1; $x <= $wdg->widgets; $x++)
            {

              $out .= '<li class="selectBox"><select id="'.$wdg->widgetId($x).'">'.$wdg->getOptions($wdg->layout[$wdg->widgetId($x)]).'</select></li>';
            }
            echo $out;
?>
             </ol>
          &nbsp;&nbsp;&nbsp;<input type="checkbox" name="resetLayout" id="resetLayout" /> <?= vt('reset mijn layout'); ?>
        </div>
      </div>
      <!-- tab2 -->
      <div id="tabs-2" style="padding:0px;">
        <form>
          <div class="padded-10" style="display: inline-block;">

          </div>
        </form>

      </div>
    </div>

  </div>

  <section>
    <div class="container">
      <div class="row">
        <div class="col-lg-12 text-center">
          <button class="btn-new btn-default pull-right fa fa-wrench mainSetup" id="btnSetup"  aria-hidden="true" title="Indelen widgets">&nbsp;</button>
          <br/>
          <br/>
        </div>
      </div>
      <?  if ($__BTR_CONFIG["CUSTOM_HTML_ELEMENTS"]) { ?>
        <div id="widget-position-config">
          <? { echo $layout; }?>
        </div>
      <? } ?>
      <div class="grid-stack" data-gs-width="12">

      </div>
    </div>
  </section>


  <script type="text/javascript">


    function reloadPage()
    {
      setTimeout(function(){
        location.reload(true);
        const d = new Date();
        console.log(d.getTime());
      },200);
    }

    function cl(val, tit)
    {
      if (tit == undefined)
      {
        tit = " -- ";
      }

      console.log(tit + " :: " + val);
    }
    var layoutName = '<?=$wdg->user?>_widgetFrontLayout';
    var wdgValues = [];
    <?=$wdg->getJS()?>
    function saveLayout()
    {
      console.log("saveLayout");
      var res = _.map($('.grid-stack .grid-stack-item:visible'), function (el)
      {
        el = $(el);
        //console.table(el);
        var node = el.data('_gridstack_node');

        return {
          id: node.id,
          x: node.x,
          y: node.y,
          width: node.width,
          height: node.height
        };
      });

      // BTR: early return when empty to fix value reset issue
      if (!res.length) return;

      var val = JSON.stringify(res);
//      console.log(val);
      $.ajax(
      {
        url:'ajax/updateAEconfig.php',
        data:{
          field: layoutName,
          value: val
        },
        dataType:'json',
        success:function(data)
        {
          populateWidgets();
        }
      });
//      location.reload(true);
    }

    function populateWidgets()
    {
      console.log("populateWidgets");
      console.log(wdgValues);
      for (i=1; i < wdgValues.length; i++)
      {
        fld = "#"+wdgValues[i].field;
        file = "widget/"+wdgValues[i].value;
        if (wdgValues[i].value.trim() != "")
        {
          $(fld).load(file);
        }

      }
    }
    
    function resetLayout()
    {
      console.log("resetLayout");
      $.ajax(
        {
          url:'ajax/updateAEconfig.php',
          data:
          {
            field: "layoutReset",
            value: "<?=$layName?>",
          },
          dataType:'json',
          success:function(data)
          {
            //console.log("Layout gereset" );
            populateWidgets();
          }
        });
    }


    function updateCFG(field, value)
    {
      console.log("in updateCFG met " + field + " v= " + value);
      $.ajax(
      {
        url:'ajax/updateAEconfig.php',
        data:{
          field: field,
          value: value
        },
        dataType:'json',
        success:function(data)
        {
          console.log("CFG: " + field + " bijgewerkt naar " + data.value);
          populateWidgets();
        }
      });
    }


    $(document).ready(function ()
    {
      $('body').tooltip(
      {
        selector: '[data-toggle="tooltip"]',
        placement: 'bottom',
      });

      var setupDialog = $('#setupDialog').dialog(
      {
        autoOpen: false,
        height: 500,
        width: '40%',
        modal: true,
        buttons:
        {
          "<?=vt('Sluiten');?>": function()
          {
            $( this ).dialog( "close" );
          },
          "<?= vt('Opslaan'); ?>": function()
          {
            if ($("#resetLayout").is(":checked"))
            {
              resetLayout();
            }
            else
            {
              for (x = 1; x <= <?=$wdg->widgets?>; x++)
              {
                var fldName = "<?=$wdg->user?>_widgetLayout_" + x;
                updateCFG(fldName, $("#"+fldName).val());
                cl(fldName + " = " + $("#"+fldName).val());
              }

              updateCFG("<?=$wdg->user?>_widgetAantal", $("#aantalWidgets").val());
            }

            $( this ).dialog( "close" );
            reloadPage();
          }
        },
        close: function ()
        {
        }
      });


      $('#tabs').tabs();


      $("#btnSetup").on("click", function()
      {
        setupDialog.dialog('open');
      });

      $('.grid-stack').gridstack(
      {
        width: 12
      });


<?

  if ($layout <> "")
  {
?>

    var serialization = <?=$layout?>;
    var grid = $('.grid-stack').data('gridstack');

    grid.removeAll();

    var tel = 0;
    var reloadNeeded = 0;
    var max = eval(<?=$wdg->widgets?>);
    var pIdx = 0;
    console.log(serialization, "serialization");
    _.each(serialization, function (node)
    {

      var nodeId = "widget-" + eval(tel+1);
      var idx = tel;

      if (tel < max)
      {

        grid.addWidget($('<div><div class="grid-stack-item-content" id="'+nodeId+'"/></div>'),
          node.x, node.y, node.width, node.height, null,null,null,null,null,nodeId);
      }
      else
      {
        reloadNeeded = 1;
      }

      pIdx = idx;
      tel++;

    });

    while (tel < max)
    {
      console.log(`max: ${max} tel: ${tel}`);
      reloadNeeded = 1;
      id = "widget-"+eval(tel+1);
      grid.addWidget($('<div><div class="grid-stack-item-content" id="'+id+'"/></div>'), 0, 0, 3, 2, true,null,null,null,null,id);
      tel++;
    }
    saveLayout();

    if (reloadNeeded)
    {
      reloadPage();
    }
    else
    {
      populateWidgets();
    }

<?
  }

?>
    $('.grid-stack').change(function()
    {
      saveLayout();
    });


  });

  </script>
<?

//clear navigatie
$_SESSION['NAV'] = "";
session_write_close();

echo template($__appvar["templateRefreshFooter"],$content);
