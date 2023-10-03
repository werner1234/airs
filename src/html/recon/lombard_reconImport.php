<?php
/*
    AE-ICT source module
    Author  						: $Author: cvs $
 		Laatste aanpassing	: $Date: 2015/12/01 09:03:06 $
 		File Versie					: $Revision: 1.1 $

 		$Log: lombard_reconImport.php,v $
 		Revision 1.1  2015/12/01 09:03:06  cvs
 		update 2540, call 4352
 		
 		Revision 1.2  2015/05/08 12:10:28  cvs
 		*** empty log message ***
 		
 		Revision 1.1  2015/04/21 13:32:04  cvs
 		*** empty log message ***
 		

*/

/*
 * dataindex
 * 
   0 = FCDateF 
  1 = CAmountOfTheNextCouponME 
  2 = CAvecPrixClient 
  3 = CAvecTauxAvanceSpec 
  4 = CBLInstrVirtuel 
  5 = CBPDevTotalMEPO 
  6 = CBPMarTotalMEPO 
  7 = CBPNRDevMEPO 
  8 = CBPNRMarMEPO 
  9 = CBPNRMB 
  10 = CBPNRMC 
  11 = CBPNRMonChifPO 
  12 = CBPNRMonCotPO 
  13 = CBPRDevMEPO 
  14 = CBPResMB 
  15 = CBPResMC 
  16 = CBPRMarMEPO 
  17 = CBPRMB 
  18 = CBPRMC 
  19 = CBPRMonChifPO 
  20 = CBPRMonCotPO 
  21 = CBPTotMCPO 
  22 = CBPTotMEPO 
  23 = CBrutIRR 
  24 = CCamb 
  25 = CCamc 
  26 = CCAMonChifPO 
  27 = CCAMonCotPO 
  28 = CCdDepot 
  29 = CDollarDuration 
  30 = CDollarDurationContrib 
  31 = CCdRubValAvance 
  32 = CDescReductionNoval 
  33 = CDescReductionPays 
  34 = CDescReductionUltimate 
  35 = CDTS 
  36 = CDTSContribution 
  37 = CCodeIsin 
  38 = CCodeRubrique 
  39 = CCodeUsance 
  40 = CContrEcheancier 
  41 = CContribDuration 
  42 = CContribDurationMod 
  43 = CContributionPerf 
  44 = CContributionPerfMWPO 
  45 = CContributionPerfWeight 
  46 = CConvexite 
  47 = CConvexityComp 
  48 = CConvexityContribution 
  49 = CConvexiteRbtAnt 
  50 = FCCours 
  51 = CCrmb 
  52 = CCrmc 
  53 = CCRMonChifPO 
  54 = CCRMonCotPO 
  55 = CDateApportEcheancier 
  56 = CDateCours 
  57 = CDateEcheanceContrat 
  58 = CDateOuvertureContrat 
  59 = CDaysAccrued 
  60 = CDEChangeRegl 
  61 = CDeriveDelta 
  62 = CDevise 
  63 = CDeviseContrepartie 
  64 = CDiffEstMB 
  65 = CDiffPoids 
  66 = CDiffPoidsPosCli 
  67 = CDirtyPrice 
  68 = CDurAnticipe 
  69 = CDuration 
  70 = CDurationComp 
  71 = CDurationModComp 
  72 = CDurationModifiee 
  73 = CDurationModRbtAnt 
  74 = CDurationRbtAnt 
  75 = CDureeVieMoyenne 
  76 = CDurFinale 
  77 = CEffetChangeMCR 
  78 = CEffetChangeMCRPO 
  79 = CEffetChgMW 
  80 = CEmetteur 
  81 = CEntJuClient 
  82 = CEstDocumente 
  83 = FCEstimationTotale 
  84 = CEstPosDebMB 
  85 = CExpoActuelleMarche 
  86 = CExpoContractuelle 
  87 = CExpoContractuelleMarche 
  88 = CExpoCredit 
  89 = CExpoDeltaMarche 
  90 = CExpoDerive 
  91 = CExpoDeriveDelta 
  92 = CExpoMonetaire 
  93 = CFirstNoticeDate 
  94 = CFiscalEfficiency 
  95 = CFiscalNonEfficiencyReason 
  96 = CFiscalnonEfficiencyReasonTxt 
  97 = CFrais 
  98 = CGainLossTaxable 
  99 = CHandleActif 
  100 = CHandleInstr 
  101 = CHstPerfMBPO 
  102 = CHstPerfMCPO 
  103 = CIDRating 
  104 = CIndInvestissement 
  105 = CInfluencePerf 
  106 = CInfluenceRisque 
  107 = CInstrCatProdASPS 
  108 = CInstrCatProdASPSLib 
  109 = CInstrCleanPrice 
  110 = CInstrCleanRatio 
  111 = CInstrCodeProdASPS 
  112 = CInstrCodeProdASPSLib 
  113 = CInstrCodeProdLODH 
  114 = CInstrCodeProdLODHLib 
  115 = CInstrConditionnel 
  116 = CInstrInTheMoney 
  117 = CInstrNature 
  118 = CInstrTransparent 
  119 = FCIntCourus 
  120 = CIntCourusDeb 
  121 = CIntCourusME 
  122 = CIntituleValAvance 
  123 = CIntPayeRecu 
  124 = CInvestGrade 
  125 = CLastTradeDate 
  126 = FCLibelle 
  127 = CLibelleEstimation 
  128 = CLogicalKeyPos 
  129 = CMasterListLevel 
  130 = CMCRMB 
  131 = CmcrMBMC 
  132 = CmcrMBMCZero 
  133 = CMCRMBZero 
  134 = CMCRMC 
  135 = CMCRMCZero 
  136 = CMCRMonChifPO 
  137 = CMCRMonChifPOZero 
  138 = CMCRMonCotPO 
  139 = CMCRMonCotPOZero 
  140 = CMntRembFinal 
  141 = CModeExprPositionsLOC 
  142 = CNbContrats 
  143 = CNetIRR 
  144 = CNmDepot 
  145 = CNoCliDosCorresp 
  146 = CNoCliDosFCP 
  147 = CNocliNodos 
  148 = CNoContrat 
  149 = CNomEcheancier 
  150 = CNoObj 
  151 = FCNoVal 
  152 = CNovalInstrLie 
  153 = CNoValInt 
  154 = CNoValSousJacent 
  155 = CPAMC 
  156 = CPerfInstr 
  157 = CPerfMonChif 
  158 = CPerfMWMCNonPO 
  159 = CPerfMWMCPO 
  160 = CPerfMWNonPO 
  161 = CPerMonCot 
  162 = CPlusValue 
  163 = CPoidsDateDeb 
  164 = CPoidsPosCliDateDeb 
  165 = CPoidsPosCliDateFin 
  166 = CPoidsValeurBrute 
  167 = CPosBloquee 
  168 = CPositionState 
  169 = CPosWithEurTax 
  170 = FCPourcentPPoste 
  171 = CPRMBMC 
  172 = CPRMC 
  173 = CRating 
  174 = CRatingCreditDelta 
  175 = CRatingFITCH 
  176 = CRatingLODH 
  177 = CRatingLODH_SBI 
  178 = CRatingSandP 
  179 = CRatingSBI 
  180 = CRdtAnticipe 
  181 = CRdtFinal 
  182 = CRdtImm 
  183 = CRdtMoyen 
  184 = CRdtPrixAchat 
  185 = CRecommendation 
  186 = CRepGen 
  187 = CRepGenKey 
  188 = CRepLPPKey 
  189 = CRepLPPLb 
  190 = CRepMon 
  191 = CRepMonKey 
  192 = CRepMonLibelle 
  193 = CRepMSCIKey 
  194 = CRepMSCILb 
  195 = CRepNativeCountry 
  196 = CRepOblKey 
  197 = FCBarraRepPays 
  198 = CRepPaysKey 
  199 = CRepPaysLibelle 
  200 = CRepPaysUltimateKey 
  201 = CRepPaysUltimateLbCourt 
  202 = CRepPaysUltimateLibelle 
  203 = CRepPocheDim1 
  204 = CRepPocheDim2 
  205 = CRepPocheDim3 
  206 = CRepPocheDim4 
  207 = CRepRubrique 
  208 = CRepSchemaActifRealloc 
  209 = CRepSchemaASPS 
  210 = CRepSchemaGen 
  211 = CRepSchemaGenLb 
  212 = CRepSchemaGeo 
  213 = CRepSchemaLPP 
  214 = CRepSchemaLPPLb 
  215 = CRepSchemaMon 
  216 = CRepSchemaMSCI 
  217 = CRepSchemaMSCILibelle 
  218 = FCBarraRepSchemaPays 
  219 = CRepSchemaPocket 
  220 = CRepSchemaRating 
  221 = CRepSchemaSecObl 
  222 = CRepSchemaTypeVehicule 
  223 = CRepSec 
  224 = CRepSecFT 
  225 = CRepSecLibelle 
  226 = CRepSecMSCI 
  227 = CRepSecObl 
  228 = CRepSecOblKey 
  229 = CRepUltimateParentLb 
  230 = CRevEcheancier 
  231 = CRevenus 
  232 = CRisqueCredit 
  233 = CSectLBLevel1 
  234 = CSectLBLevel2 
  235 = CSectLBLevel3 
  236 = CSeuilDiversification 
  237 = CSimulationScenarioType 
  238 = FCSolde 
  239 = CSoldeActifVirtuel 
  240 = CSpreadToGouv 
  241 = CSpreadToLibor 
  242 = CSrcPrixReconnue 
  243 = CSymbTelekurs 
  244 = CTauxChangeMoyenAchat 
  245 = CTauxChangeMoyenAchatPO 
  246 = CTauxReductionMonetaire 
  247 = CTauxReductionNoval 
  248 = CTauxReductionPays 
  249 = CTauxReductionUltimate 
  250 = CTauxValAvance 
  251 = CTimeToNextCoupon 
  252 = FCTotalPos 
  253 = FCTotalPosCHF 
  254 = FCTotalPosMC 
  255 = CTpInstrument 
  256 = CTpOpeCtrDevise 
  257 = CTypeActif 
  258 = CTypeBlocage 
  259 = CTypeCourtTerme 
  260 = CTypePosition 
  261 = CValeurAvance 
  262 = CValeurBrute 
  263 = CYieldComp 
  264 = CYieldContribution 
  265 = VAccruedInterestCH 
  266 = VAccruedInterestEU 
  267 = VActInterestRate 
  268 = VCallDate 
  269 = VCallPrice 
  270 = VCours 
  271 = VCoursMBPort 
  272 = VCoursMonPos 
  273 = VCurrExchNumberMBPort 
  274 = VCurrExchNumberMonPos 
  275 = VExercicePrice 
  276 = VExpirationDate 
  277 = VFirstPayDate 
  278 = VFrequence 
  279 = VGeoUnitISO 
  280 = VIndustrySymbol 
  281 = VInstiID 
  282 = VInstiIDSchemeID 
  283 = VInstiLOName 
  284 = VInstiShName 
  285 = VInstiStatus 
  286 = VInstiSymbol 
  287 = VInstiSymbolBCN 
  288 = VInstiSymbolMIC 
  289 = VInstiType 
  290 = VInstrLoNameDE 
  291 = VInstrLoNameEN 
  292 = VInstrLoNameFR 
  293 = VInstrLoNameIT 
  294 = VInstrLoNameNL 
  295 = VInstrSubclass 
  296 = VInstrumentFactor 
  297 = VInterestCalcul 
  298 = FCVIsin 
  299 = ISPerformanceTWNPondere 
  300 = FCBarraMon 
  301 = VIssuerShName 
  302 = VListingTypeID 
  303 = VLocationID 
  304 = VLOCInstrNature 
  305 = VMainTrdPlaceID 
  306 = VMainTrdPlaceMIC 
  307 = VNumberOfShares 
  308 = VOptionStyleType 
  309 = VPaymentDate 
  310 = VRatingSchemeID 
  311 = VRatingSymbol 
  312 = VRedeemable 
  313 = VRic 
  314 = VUnderInstrNo 
  315 = FCPortefeuilleF 
  316 = FCIntitulePortefeuille 
  317 = FCMonBasePortefeuille 
  318 = FCEstimationPortefeuille 
  319 = FCBarraType 
  320 = FCPayableCurrency 
  321 = FCReceivableCurrency 
  322 = FCAmountToPayMB 
  323 = FCAmountToReceiveMB 
  324 = FCAmountToPayCHF 
  325 = FCAmountToReceiveCHF 
  326 = FCAmountToPayMC 
  327 = FCAmountToReceiveMC 
  328 = FCDateExpiration 
  329 = FCDateOuverture 
  330 = FCCDEChangeRegl 
  331 = FCCodeCompte 
  332 = FCUG 
  333 = FCCodeGerant1 
  334 = FCGerant1 
  335 = FCGerant2 
  336 = FCMandat 
  337 = FCProfil 
  338 = FCProfilRisque 
  339 = FCLibelleGerant 
  340 = FCDescription 
  341 = FCTrackingError 
  342 = FCBenchmarkId 
  343 = FCBenchmarkLibelle 
  344 = FCInfoRatio 
  345 = FCPerfRelative 
  346 = FCBeta 
  347 = FCOpenDateAccount 
  348 = FCCloseDateAccount 
  349 = FCLangueClient 
  350 = FCValeurAvance 
  351 = FCHstMonBase 
  352 = FCNatureDossier 
  353 = FCEntiteJuridique 
  354 = FCCdUniteClient 
  355 = FCCdProfilUC 
  356 = FCCdProfilUG 
  357 = FCCdAssocieRel 
  358 = FCBalancement 
  359 = FCCodeRelation2 
  360 = FCDateDebutCalcPerf 
  361 = FCLibDossier 
  362 = FCLibelleEServices 
  363 = FCDomicile 
  364 = FCNationalite 
  365 = FCCDDocumentUSSigne 
  366 = FCTypePortf 
  367 = FCInstrReference 
  368 = FCCouponBrut 
  369 = FCHasConstraint 
  370 = FCHasESDocSign 
  371 = FCCdAssocieLevel 
  372 = FCMBHomogene 
  373 = FCEntiteDenormalisee 
  374 = FCIsPEA 
  375 = FCIntituleExterne 
  376 = ISPerformanceMW 
  377 = ISRisqueD 
  378 = VInstrIdentSymbol 
  379 = VInstrIdSchemeID 
  380 = VInstrSymbol 
  381 =  
*/
include_once("wwwvars.php");
include_once("lombard_reconFuncties.php");


session_start();
$_SESSION[NAV] = ""; 		
//listarray($__appvar);
$error    = array();
$content  = array("title"=>$PHP_SELF);

$filetype = "";



$prb = new ProgressBar();	// create new ProgressBar
//$prb->pedding = 2;	// Bar Pedding
//$prb->brd_color = "#404040 #dfdfdf #dfdfdf #404040";	// Bar Border Color
//$prb->setFrame();          	                // set ProgressBar Frame
//$prb->bgr_color = "#F8F5AD";
//$prb->color = "#808080";
//$prb->frame['width'] = 	400;	                  // Frame position from top
//$prb->frame['heigth'] = 	80;	                  // Frame position from top
//$prb->frame['left'] = 50;	                  // Frame position from left
//$prb->frame['top'] = 	80;	                  // Frame position from top
//$prb->frame['color'] = 	"beige";	                  // Frame position from top
//$prb->addLabel('text','txt1','Inlezen bankbestand ...');	// add Text as Label 'txt1' and value 'Please wait'
//$prb->addLabel('procent','pct1');	          // add Percent as Label 'pct1'


$content['jsincludes'] .= '<link rel="stylesheet" href="../style/smoothness/jquery-ui-1.11.1.custom.css">';
$content['jsincludes'] .= "<script type=\"text/javascript\" src=\"../javascript/jquery-min.js\"></script>";
$content['jsincludes'] .= "<script type=\"text/javascript\" src=\"../javascript/jquery-ui-min.js\"></script>";
echo template("../".$__appvar["templateContentHeader"],$content);
?>
<div id="running">
  <h2> Reconciliatie, moment a.u.b. </h2>

  <img src="../images/loading.gif" alt=""/>
</div>
  <br />

<ul>
<?
$starttijd = mktime();
echo " <li> gestart om ".date("H:i:s")."</li>";
ob_flush();flush();

if (!validateFile($_GET["file"]))
{
  listError($error);
  exit;
}

$recon = new reconcilatieClass("LOM",$_GET["manualBoekdatum"]);
$batch = "LOM_".date("ymd_His");
$recon->batch = $batch;


?>
<br />
<ul>

<?
$starttijd = mktime();
echo " <li> gestart om ".date("H:i:s")."</li>";
echo " <li> depotbank ".$recon->depotbank."</li>";
ob_flush();flush(); 
$bankRecords = recon_readBank($_GET["file"]);

echo " <li> bankbestand bevatte ".$bankRecords." dataregels</li>";
echo " <li> ontbrekende rekeningen erbij zoeken</li>";

echo " <li> ".$airsOnly." AIRS rekeningen zonder bankposities</li>";
echo " <li> afgerond om ".date("H:i:s")." (= ".round(mktime()-$starttijd,0)." sec.) </li>";
ob_flush();flush(); 
?>
</ul>
<?
?>
<p>Ga naar het <a href="../tijdelijkereconList.php">overzicht</a></p>

<script>
  $("#running").hide(600);
</script>

<?
echo template("../".$__appvar["templateRefreshFooter"],$content);
?>