<?php
/*
    AE-ICT CODEX source module versie 1.6, 31 oktober 2016
    Author              : $Author: cvs $
    Laatste aanpassing  : $Date: 2017/09/20 06:26:57 $
    File Versie         : $Revision: 1.4 $

    $Log: rekeningmutatiesafvinkList.php,v $
    Revision 1.4  2017/09/20 06:26:57  cvs
    no message

    Revision 1.3  2017/07/22 18:20:50  rvv
    *** empty log message ***

    Revision 1.2  2016/12/05 09:57:01  rm
    5086 Opmaak

    Revision 1.1  2016/12/02 14:05:30  cvs
    call 5086


*/
include_once("wwwvars.php");
include_once("../classes/mysqlList.php");
include_once("../classes/AIRS_rekeningAfvinkHelper.php");
session_start();


$_SESSION["afvinkVB"] = "VEC";

$afh = new AIRS_rekeningAfvinkHelper($_SESSION["afvinkVB"]);

$_SESSION["backlink"] = $_SERVER["REQUEST_URI"];


$editScript = "rekeningmutatiesafvinkEdit.php";
$allow_add  = false;


$dbg = new DB();
$query = "SELECT * FROM grootboeknummers  ORDER BY grootboekrekening ";
$dbg->executeQuery($query);
while ($gbRec = $dbg->nextRecord())
{
	$gbOptions .= '<option value="'.$gbRec["rekeningnummer"].'" >'.$gbRec["rekeningnummer"]." - ".$gbRec["grootboekrekening"]."</option>";
}



if ( ! isset ($_GET["blockFilterAction"]) || $_GET["blockFilterAction"] == "nomatch")
{
	$extraWhere .= " AND (RekeningmutatiesAfvink.status = 0) ";
	$subHeader  .= " ( niet verwerkt ) ";
	$_GET["blockFilterAction"] = 'nomatch';
} elseif (  isset ($_GET["blockFilterAction"]) && $_GET["blockFilterAction"] == "matched" ) {
	$extraWhere .= " AND (RekeningmutatiesAfvink.status <> 0 ) ";
	$subHeader  .= " ( verwerkt ) ";
}
else
{
	$subHeader .= "( allemaal )";
}

$table = "RekeningmutatiesAfvink";
$list = new MysqlList2();
$list->idField = "id";
$list->idTable =$table;
$list->editScript = $editScript;
$list->perPage = 1500;

$list->setFullEditScript();

// get afschriftgegevens.
$list->queryWhere = '
  Rekeningmutaties.Boekdatum > "2010-01-01"
    AND
  Portefeuilles.Vermogensbeheerder = "VEC"
  '.$extraWhere;
$list->addColumn("","icon",array("list_width"=>"30","description"=>" ",'list_nobreak'=>true,'list_order'=>false));
$list->addColumn("RekeningmutatiesAfvink","status",array("list_invisible"=>true));
$list->addFixedField("RekeningmutatiesAfvink","grootboek",array("list_width" => "100"));
$list->addFixedField("RekeningmutatiesAfvink","grootboekOrg",array("list_width" => "100"));
$list->addColumn("RekeningmutatiesAfvink","rekmut_id",array("list_invisible"=>true));
$list->addFixedField($table,"bedragOrg");
$list->addFixedField($table,"rekening",array("list_align"=>"left","search"=>true, "list_width" => "120"));
$list->addFixedField("Rekeningmutaties","Aantal",array("list_align"=>"right","search"=>false, "list_width" => "120"));
$list->addFixedField("Portefeuilles","Client",array("list_align"=>"left","search"=>true));
$list->addFixedField("Portefeuilles","Portefeuille",array("list_align"=>"left","search"=>true));
$list->addFixedField($table,"Boekdatum",array("list_align"=>"right","search"=>false,"list_width" => "", "description"=>"B. datum"));
$list->addFixedField($table,"Grootboekrekening",array("list_align"=>"center","search"=>true,"description" => "GB"));
$list->addFixedField("Rekeningmutaties","Fonds",array("list_width"=>"200","list_align"=>"left","search"=>true,'list_nobreak'=>true));
$list->addFixedField($table,"omschrijving",array("list_align"=>"left","search"=>true,"td_style" => "nowrap",'list_nobreak'=>true, "list_width"=>"30%"));

$list->categorieVolgorde[$table]=array('Algemeen');
$list->categorieVolgorde['Portefeuilles']=array('Gegevens','Beheerfee','Staffels');
$list->categorieVolgorde['Rekeningmutaties']=array('Algemeen');
//$list->categorieVolgorde['RekeningmutatiesAfvink']=array('Algemeen');
$html = $list->getCustomFields(array($table,'Portefeuilles',"Rekeningmutaties"),'rekMutAfvink');
$list->ownTables=array($table);
$list->setJoin("
INNER JOIN Rekeningmutaties ON 
	RekeningmutatiesAfvink.rekmut_id = Rekeningmutaties.id
INNER JOIN Rekeningen ON 
	Rekeningmutaties.Rekening = Rekeningen.Rekening AND Rekeningen.consolidatie=0
INNER JOIN Portefeuilles ON 
	Rekeningen.Portefeuille = Portefeuilles.Portefeuille AND Portefeuilles.consolidatie=0
AND 
	Portefeuilles.interndepot = 1
");
// set default sort

//if(trim($_POST['sort_0_veldnaam']) == "")

//$list->addSort("RekeningmutatiesAfvink.BoekdatumOrg", "DESC");
//$list->addSort("RekeningmutatiesAfvink.id", "ASC");

// set sort
$list->setOrder($_GET['sort'],$_GET['direction']);
// set searchstring
$list->setSearch($_GET['selectie']);
// select page
$list->selectPage($_GET['page']);

$_SESSION['submenu'] = New Submenu();
$_SESSION['submenu']->addItem("Genereer records","rekeningmutatiesAfvinkGenereer.php");
$_SESSION['submenu']->addItem($html,"");

$content['style'] = $editcontent['style'];
$content['jsincludes'] = '
	<link href="style/smoothness/jquery-ui-1.11.1.custom.css" rel="stylesheet" type="text/css" media="screen">
	<script src="javascript/jquery-1.11.1.min.js"></script>
	<script src="javascript/jquery-ui-1.11.0-min.js"></script>
	<script src="javascript/algemeen.js"> </script>	';

$content['javascript'] .= "
function addRecord() 
{
	parent.frames['content'].location = '".$editScript."?action=new';
}
";
echo template($__appvar["templateContentHeader"],$content);

session_start();
$_SESSION['NAV'] = new NavBar($PHP_SELF, getenv("QUERY_STRING"));
$_SESSION['NAV']->addItem(new NavList($_GET['page'], $list->records(), $__appvar['rowsPerPage'],$allow_add));
$_SESSION['NAV']->addItem(new NavSearch($_GET['selectie']));
session_write_close();
?>



	<style>
		.ar{
			text-align: right;
		}
		#totSelectie, #totSelectieAantal{
			font-size: 1.2em;
		}
		.rowRed
		{
			background: #FCC;
		}
		.rowGreen{
			background: #CFC;
		}
		.rowBlue{
			background: #CCF;
		}
		.rowRose{
			background: #FFD;
		}
		.selblok
		{
			width: 400px;
			padding-bottom: 10px;
			border: 1px #000000 solid;
			border-radius: 5px;
			background: white;
			text-align: center;
			vertical-align: middle;
			height: 61px;
			line-height: 30px;
			font-size: 20px;
		}

		#dialog-form{
			height: 250px;
			width: 400px;
			z-index: 950;
		}

		#dialogTable{
			width: 95%;
		}
		.ui-dialog .ui-dialog-titlebar-close span {
			display: block;
			margin: -8!important;
		}

		#mutationTable #test {
			/*flex: 0 0 auto;*/
			/*width: calc(100% - 0.9em);*/
		}
		#mutationTable tbody:not(:first-child) {
			/*flex: 1 1 auto;*/
			display: block;
			overflow-y: scroll;
		}
		#mutationTable :not(:first-child) tr {
			/*width: 100%;*/
		}
		#mutationTable thead,
		#mutationTable tbody:first-child tr {
			display: table;
			table-layout: fixed;
		}
	</style>



<div class="main_content">

	<div class="formHolder box box12 {fieldsetClass}">
		<div class="formTitle textB">Filter</div>
		<div class="formContent">
			<div class="padded-10">
			<?=$list->filterHeader();?>
			<div class="btn-group">
				<span style="position: relative;float: left;display: inline-block; padding: 4px 10px 4px;  font-size: 14px!important; line-height: 18px;" class="">Selectie </span>
				<button <?=($_GET["blockFilterAction"] == "filterAll" ? 'disabled=disabled':'' );?> id="filterAll" class="btn-new btn-default"> Alles </button>
				<button <?=($_GET["blockFilterAction"] === "matched" ? 'disabled=disabled':'' );?> id="filterProcessed" class="btn-new btn-default"> Verwerkt </button>
				<button <?=($_GET["blockFilterAction"] == "nomatch" ? 'disabled=disabled':'' );?> id="filterNomatch" class="btn-new btn-default"> Niet verwerkt </button>
			</div>
			</div>
		</div>
	</div>


	<form id="blockFilterForm">
		<input type="hidden" name="page" value="<?=$_GET["page"]?>" />
		<input type="hidden" name="selectie" value="<?=$_GET["selectie"]?>" />
		<input type="hidden" name="einddatum" id="einddatum" value="<?=$_GET["einddatum"]?>" />
		<input type="hidden" id="blockFilterAction" name="blockFilterAction" value="" />
	</form>

	<div class="formHolder box box12 {fieldsetClass}">
	<div class="formTitle textB">
		<div style="height:75px">

			<div class="floatL inline" style="">
				<div class="selblok" id="selblok">

					bedrag selectie: <span id="totSelectie">0.00</span> <br/>
					aantallen selectie: <span id="totSelectieAantal">0.00</span> (<span id="cntSelectie">0</span>)

				</div>
			</div>

			<div class="floatR inline" style="padding-right: 15px;">
				<div>
					<div class="btn-group" style="    ">
						<button id="btnAll" class="btnAction btn-new btn-default">alles selecteren</button>
						<button id="btnNone" class="btnAction btn-new btn-default">niets selecteren</button>
					</div>
					<button id="btnMatch" class="btnAction btn-new btn-default">match selectie</button>
					<button id="btnFonds" class="btnAction btn-new btn-default">matchen fondsmutaties</button>
				</div>

				<div style="padding-top: 10px;">
					<button id="btnGoedkeur" class="btnAction  btn-new btn-default">goedkeuren voorstel</button>
					<select name="nwGrootboek" id="nwGrootboek">
						<?=$gbOptions?>
					</select>
					<button id="btnGrootboek" class="btnAction  btn-new btn-default">&nbsp;&nbsp;&nbsp;grootboek wijzigen*</button>
					<button id="btnReload" class="btnAction  btn-new btn-default">herlaad</button>
				</div>
			</div>
		</div>
	</div>
	<div class="formContent" style="height: 350px;" >
	<div class="" >


	<table id="mutationTable" class="table table-sm" cellspacing="0" style="display: flex;flex-flow: column;height: 100%;width: 100%;">
<?
echo $list->printHeader(true);//true

$list->noClick = true;
while($data = $list->getRow())
{
//	$data["noClick"] = true;
	switch ($data["RekeningmutatiesAfvink.status"]["value"] )
	{
		case 1: $data["tr_class"] = "rowRose";   	break;
		case 2:	$data["tr_class"] = "rowRed"; 		break;
		case 3: $data["tr_class"] = "rowBlue"; 		break;
		case 4:	$data["tr_class"] = "rowGreen"; 	break;
	}


	$data[".icon"]["value"] = "
    	<input type='checkbox' class='afvink'  name='id_".$data["id"]["value"]."' id='id_".$data["id"]["value"]."' 
           value='1' 
           data-rekmut_id='".$data["RekeningmutatiesAfvink.rekmut_id"]["value"]."'
           data-bedrag='".$data["RekeningmutatiesAfvink.bedragOrg"]["value"]."'
           data-oms='".$data["RekeningmutatiesAfvink.omschrijving"]["value"]."'
           data-grootboek='".$data["RekeningmutatiesAfvink.grootboek"]["value"]."'
           data-gbOrg='".$data["RekeningmutatiesAfvink.grootboekOrg"]["value"]."'
           data-aantal='".$data["Rekeningmutaties.Aantal"]["value"]."'
           data-fonds='".$data["Rekeningmutaties.Fonds"]["value"]."'/>
    ";
	$data['disableEdit']=true;
	$data['RekeningmutatiesAfvink.bedragOrg']['td_style'] = " id='totaal_".$data["id"]["value"]."' data-bedrag='".$data["RekeningmutatiesAfvink.bedragOrg"]["value"]."' ";

	echo $list->buildRow($data);
}
?>
</table>

	</div>
	</div>
	</div>



	<div id="dialog-form" title="Matchen Fonds">
		<p class="melding"></p>
		<p id="formulier">
			<fieldset>
				<div id="rowsTable">
				</div>
				<table id="dialogTable">
					<tbody id="first">
					</tbody>
					<tfoot>
					<tr>
						<td colspan='10'><hr/></td>
					</tr>
					<tr>
						<td class='editBlok' id="hb_GB">&nbsp;</td>
						<td class='editBlok' id="hb_OMS">&nbsp;</td>
						<td class='editBlok ar'><input type='text' class='ar' data-rekmut="0" value='0' READONLY id='hb_BEDRAG' /></td>
						<td class='editBlok'>&nbsp;</td>
					</tr>
					<tr>
						<td class="editBlok"><select id="gb1"><option value="">---</option> <?=$gbOptions?></select></td>
						<td class="editBlok">extra mutatie 1</td>
						<td class="editBlok ar"><input type="text" name="bedrag1" id="bedrag1" value="0" class="ar diaInput" />
						</td>
					</tr>
					<tr>
						<td class="editBlok"><select id="gb2"><option value="">---</option><?=$gbOptions?></select>
						</td class="editBlok"><td>extra mutatie 2</td>
						<td class="editBlok ar"><input type="text" name="bedrag2" id="bedrag2" value="0" class="ar diaInput" />
						</td>
					</tr>

					</tfoot>
				</table>
			</fieldset>
		</p>
	</div>

	<script>
		var totaal;
		var matchOk = false;
		var count;
		var dataRows = [];
		var hoogsteBedrag = 0;
		var fondsmatchOk = true;
		var scrollId = "";
		var calcHb = 0;
		var calcDif = 0;
		function berekenDialog()
		{
			var b1 = parseFloat($("#bedrag1").val());
			var b2 = parseFloat($("#bedrag2").val());
			var diff = calcHb - (b1 + b2);
			$("#hb_BEDRAG").val(diff.toFixed(2));
			$("#bedrag1").val(b1.toFixed(2));
			$("#bedrag2").val(b2.toFixed(2));
		}

		function berekenSelectie()
		{
			var a;
			var b;
			aantal = 0;
			totaal = 0;
			count = 0;
			dataRows = [];

			$(".afvink:checked").each(function(index, elem)
			{
				var mId = $(elem).attr("id").substring(3);
				if (scrollId == "")
				{
					scrollId = $(elem).attr("id");
				}

				b = parseFloat($(elem).data("bedrag"));
				a = parseFloat($(elem).data("aantal"));

        aantal = aantal + a;
				totaal = totaal + b;
				var curRow = {
					'id': mId,
					'bedrag': $(elem).data("bedrag"),
					'bedragABS': Math.abs($(elem).data("bedrag")),
					'rekmut_id': $(elem).data("rekmut_id"),
					'oms': $(elem).data("oms"),
					'grootboekOrg': $(elem).data("gborg"),
					'grootboek': $(elem).data("grootboek"),
					'fonds': $(elem).data("fonds"),
					'Aantal': $(elem).data("aantal"),

				};

				dataRows.push(curRow);
				if (Math.abs($(elem).data("bedrag")) > hoogsteBedrag)
				{
					hoogsteBedrag = Math.abs($(elem).data("bedrag"));
				}
				count++;

			});

			matchOk = false;
			aantalOk = false;

			if (!isNaN(totaal))
			{
				var tot = totaal.toFixed(2) * -1;
				console.log("match totaal = "+tot+ " / "+Math.abs(tot));
				if (Math.abs(tot) == "0.00")
				{
					matchOk = true;
				}

				$("#totSelectie").text(tot);
			}
			else
			{
				$("#totSelectie").text("--");
			}
			var cnt = count.toFixed(0);
			$("#cntSelectie").text(cnt);

			if (!isNaN(aantal))
			{
				var aant = aantal.toFixed(2);
				if (aant == "0.00")
				{
					aantalOk = true;
				}
				$("#totSelectieAantal").text(aant);
			}

			if (matchOk && aantalOk)
			{
				$("#selblok").css({
					'background': 'green',
					'color': 'white'
				});
			}
			else
			{
				$("#selblok").css({
					'background': 'red',
					'color': 'white'
				});
			}

			if (cnt == "0")
			{
				$("#selblok").css({
					'background': 'white',
					'color': '#333'
				});
			}


		}

		$(document).ready(function()
		{
//      $('#mutationTable colgroup').prependTo($('#mutationTable'));
//
//			$('<thead></thead>').prependTo('#mutationTable').append($('#mutationTable tr:first'));

      $('#mutationTable tr:first').attr('id', 'test');



 			$(".diaInput").change(function(){
 				berekenDialog();
 			});

			dialog = $( "#dialog-form" ).dialog({
				autoOpen: false,
				height: 400,
				width: 700,
				modal: true,
				position: { my: 'center', at: 'center', of: $(this) },

				buttons: {
					"opslaan": updateFondsMatch,
					"sluit": function() {
						dialog.dialog( "close" );
					}
				},
				close: function() {

				}
			});

			function updateFondsMatch()
			{
				var item_id ="";
				$(".afvink:checked").each(function(index, elem)
				{
					item_id += ($(elem).attr("name").substring(3)+";");
				});

				$(".afvink").prop('checked',false);
				berekenSelectie();
				var b1 = parseFloat($("#bedrag1").val());
				var b2 = parseFloat($("#bedrag2").val());
				var diff = calcHb - (b1 + b2);
//				alert("grootboek1: " + $("#gb1").val() + " bedrag: " + b1 + "\n" + "grootboek2: " + $("#gb2").val() + " bedrag: " + b2 + "\n" + "mutatie: " + $("#hb_BEDRAG").val() + "rekmut:" + $("#hb_BEDRAG").data("rekmut") );
				$.ajax({
					url: 'ajax/updateRekmutAfvink.php',
					type: "POST",
					data: {
						action: "fondsMatch",
						gb1: $("#gb1").val(),
						b1: b1,
						gb2: $("#gb2").val(),
						b2: b2,
						rekmutBedrag: $("#hb_BEDRAG").val(),
						rekmutId: $("#hb_BEDRAG").data("rekmut"),
						itemId: $("#hb_BEDRAG").data("item"),
						items: item_id,
					},
					dataType: 'text',
					success: function (data)
					{
						console.log("reloading...");
						location.reload(true);
					}
				});
				dialog.dialog( "close" );
			}

			function checkGoedkeurRules()
			{
				var gbmatch = true;
				var bedragmatch = true;
				var arrayLength = dataRows.length;
				for (var i = 0; i < arrayLength; i++)
				{
					if (dataRows[i].grootboek == "")
					{
						gbmatch = false;
					}
					if (parseFloat(dataRows[i].bedrag) == 0.00)
					{
						bedragmatch = false;
					}
				}
				if (!gbmatch || !bedragmatch)
				{
					alert("Grootboek niet bij ieder item gevuld of\nBedrag niet bij ieder item gevuld");
					return false;
				}
				return true;
			}

			function checkFondsRules()
			{
				var fondsmatch = true;
				var grootboekmatch = true;
				var prevFonds = dataRows[0].fonds;
				var arrayLength = dataRows.length;
				for (var i = 0; i < arrayLength; i++)
				{
					if (prevFonds != dataRows[i].fonds)
					{
						fondsmatch = false
					}
					prevFonds = dataRows[i].fonds;
					if (dataRows[i].grootboekOrg != "FONDS"  &&
							dataRows[i].grootboekOrg != "RENOB"  &&
							dataRows[i].grootboekOrg != "RENME"  )
					{
						grootboekmatch = false;
					}

				}
				if (!grootboekmatch || !fondsmatch)
				{
					alert("selectie voldoet niet \n * fondsen niet gelijk \n* grootboek niet in FONDS, RENOB, RENME");
					return false;
				}
				return true;

			}

			var floaterPos = $("#floater").position();

			$(window).scroll(function()
			{
				var scrollBar = $(window).scrollTop();
				if (scrollBar >= floaterPos.top)
				{
					$("#floater").css({ 'top': 5   });
				}
				else
				{
					$("#floater").css({ 'top': 5    });
				}

			});

			$("#filterAll").click(function()
			{
				$("#blockFilterForm").submit();
			});

			$("#filterNomatch").click(function()
			{
				$("#blockFilterAction").val("nomatch");
				$("#blockFilterForm").submit();
			});
			$("#filterProcessed").click(function()
			{
				$("#blockFilterAction").val("matched");
				$("#blockFilterForm").submit();
			});

			$("#filterAll").click(function()
			{
				$("#blockFilterAction").val("filterAll");
				$("#blockFilterForm").submit();
			});

			$(".afvink").change(function(){
				berekenSelectie();
			});

			$(".btnAction").click(function()
			{
				var mId = $(this).attr("id");
				var item_id = "";
				var action = mId;
				var grootboek = $("#nwGrootboek").val();

				$(".afvink:checked").each(function(index, elem)
				{
					item_id += ($(elem).attr("name").substring(3)+";");
				});

				switch(mId)
				{
					case "btnReload":
						location.reload(true);
						break;
					case "btnAll":
						$(".afvink").prop('checked',true);
						berekenSelectie();
						break;
					case "btnNone":
						$(".afvink").prop('checked',false);
						berekenSelectie();
						break;
					case "btnGoedkeur":
						if (checkGoedkeurRules())
						{
							$.ajax({
								url: 'ajax/updateRekmutAfvink.php',
								type: "POST",
								data: {
									action: action,
									items: item_id,
									grootboek: grootboek,
								},
								dataType: 'text',
								success: function (data)
								{
									console.log("succes");
									location.reload(true);
								}
							});

						}
						break;
					case "btnFonds":
						//if (checkFondsRules())
						if(1 == 1)
						{
							var plusBedrag = 0;
							var minBedrag = 0;
							var hb = "";
							var txt = "";
							var arrayLength = dataRows.length;
							calcDif = 0;
							calcHb  = 0;
							for (var i = 0; i < arrayLength; i++)
							{

								if (hoogsteBedrag == dataRows[i].bedragABS)
								{
									hb = "*";
									calcHb = dataRows[i].bedrag;
									$("#hb_GB").text(dataRows[i].grootboek);
									$("#hb_OMS").text(dataRows[i].oms);
									$("#hb_BEDRAG").val(dataRows[i].bedrag.toFixed(2));
									$("#hb_BEDRAG").data("rekmut", dataRows[i].rekmut_id);
									$("#hb_BEDRAG").data("item", dataRows[i].id);
								}
								else
								{
									hb = "";
								}
								if (dataRows[i].bedrag < 0)
								{
									minBedrag += dataRows[i].bedragABS;
								}
								else
								{
									plusBedrag += dataRows[i].bedragABS;
								}

								txt += "<tr><td>" + dataRows[i].grootboek +
									"</td><td>" + dataRows[i].oms +
									"</td><td class='ar'>" + dataRows[i].bedrag.toFixed(2) +
									"</td><td>" + hb +
									"</td></tr>";
							}

							$('#dialogTable > tbody').html(txt);

							var mutBedrag = plusBedrag - minBedrag;
							$("#bedrag1").val(mutBedrag.toFixed(2));
							berekenDialog();
							dialog.dialog("open");
						}
						break;
					case "btnGrootboek":
						$.ajax({
							url: 'ajax/updateRekmutAfvink.php',
							type: "POST",
							data: {
								action: action,
								items: item_id,
								grootboek: grootboek,
							},
							dataType: 'text',
							success: function (data)
							{
								location.reload(true);
							}
						});
						break;
					case "btnMatch":
						if (!matchOk || !aantalOk)
						{
							alert("FOUT saldo en/of aantal niet 0.00");
						}
						else
						{
							$.ajax({
								url: 'ajax/updateRekmutAfvink.php',
								type: "POST",
								data: {
									action: action,
									items: item_id,
									grootboek: grootboek,
								},
								dataType: 'text',
								success: function (data)
								{
									location.reload(true);
								}
							});
						}
						break;
				}

			});
		});

	</script>

<?
logAccess();
if($__debug)
{
	echo getdebuginfo();
}
echo template($__appvar["templateRefreshFooter"],$content);
?>