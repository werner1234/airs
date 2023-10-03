<?php

//include("wwwvars.php");
include_once("../classes/AE_cls_SQLman.php");

$tst = new SQLman();

$tst->changeIndex('VermogensbeheerdersPerBedrijf','Bedrijf',array('columns'=>array('Bedrijf')));
$tst->changeIndex('Accountmanagers','Accountmanager',array('columns'=>array('Accountmanager')));
$tst->changeIndex('Portefeuilles','Client',array('columns'=>array('Client')));
$tst->changeIndex('Portefeuilles','Accountmanager',array('columns'=>array('Accountmanager')));
$tst->changeIndex('Portefeuilles','tweedeAanspreekpunt',array('columns'=>array('tweedeAanspreekpunt')));
$tst->changeIndex('Vermogensbeheerders','Vermogensbeheerder',array('columns'=>array('Vermogensbeheerder')));
$tst->changeIndex('ZorgplichtPerPortefeuille','Portefeuille',array('columns'=>array('Portefeuille')));
$tst->changeIndex('uitsluitingenModelcontrole','portefeuille',array('columns'=>array('portefeuille')));
$tst->changeIndex('Risicoklassen','Vermogensbeheerder',array('columns'=>array('Vermogensbeheerder')));
$tst->changeIndex('Risicoklassen','Risicoklasse',array('columns'=>array('Risicoklasse')));
$tst->changeIndex('Regios','Regio',array('columns'=>array('Regio')));
$tst->changeIndex('KeuzePerVermogensbeheerder','vermogensbeheerder',array('columns'=>array('vermogensbeheerder')));
$tst->changeIndex('Indices','Vermogensbeheerder',array('columns'=>array('Vermogensbeheerder')));
$tst->changeIndex('IndexPerBeleggingscategorie','Vermogensbeheerder',array('columns'=>array('Vermogensbeheerder')));
$tst->changeIndex('GeconsolideerdePortefeuilles','VirtuelePortefeuille',array('columns'=>array('VirtuelePortefeuille')));
$tst->changeIndex('Gebruikers','Gebruiker',array('columns'=>array('Gebruiker')));
$tst->changeIndex('emittenten','emittent',array('columns'=>array('emittent')));
$tst->changeIndex('Depotbanken','Depotbank',array('columns'=>array('Depotbank')));
$tst->changeIndex('CategorienPerVermogensbeheerder','Vermogensbeheerder',array('columns'=>array('Vermogensbeheerder')));
$tst->changeIndex('CategorienPerHoofdcategorie','Vermogensbeheerder',array('columns'=>array('Vermogensbeheerder')));
$tst->changeIndex('benchmarkverdelingVanaf','benchmark',array('columns'=>array('benchmark')));
$tst->changeIndex('benchmarkverdelingVanaf','fonds',array('columns'=>array('fonds')));
$tst->changeIndex('benchmarkverdeling','benchmark',array('columns'=>array('benchmark')));
$tst->changeIndex('benchmarkverdeling','fonds',array('columns'=>array('fonds')));
$tst->changeIndex('Beleggingssectoren','Beleggingssector',array('columns'=>array('Beleggingssector')));
$tst->changeIndex('AttributieCategorien','AttributieCategorie',array('columns'=>array('AttributieCategorie')));

