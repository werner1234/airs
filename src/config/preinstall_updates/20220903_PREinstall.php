<?php

include_once("../classes/AE_cls_SQLman.php");

$tables=array();
$tables['afmCategorien']='afmCategorien_202209';
$db=new DB();
$txt="INSERT INTO `afmCategorien` VALUES ('1', '01liquiditeiten', 'Liquiditeiten', '0.5', 'a:17:{i:1;s:1:\"1\";i:2;s:1:\"0\";i:3;s:1:\"0\";i:17;s:1:\"0\";i:14;s:3:\"0.2\";i:6;s:1:\"0\";i:5;s:1:\"0\";i:7;s:1:\"0\";i:9;s:1:\"0\";i:8;s:4:\"-0.2\";i:11;s:4:\"-0.2\";i:12;s:1:\"0\";i:10;s:4:\"-0.2\";i:13;s:4:\"-0.2\";i:15;s:1:\"0\";i:16;s:1:\"0\";i:4;s:1:\"0\";}', '2011-12-26 21:06:51', 'TNT', '2022-07-11 08:40:30', 'FEGT', '0', '0', '1');
INSERT INTO `afmCategorien` VALUES ('2', '02StLenEURAAA', 'Staatsleningen Euro AAA-AA', '4', 'a:17:{i:1;s:0:\"\";i:2;s:1:\"1\";i:3;s:1:\"1\";i:17;s:3:\"0.6\";i:14;s:3:\"0.8\";i:6;s:3:\"0.2\";i:5;s:3:\"0.2\";i:7;s:3:\"0.6\";i:9;s:3:\"0.4\";i:8;s:1:\"0\";i:11;s:4:\"-0.2\";i:12;s:4:\"-0.2\";i:10;s:4:\"-0.2\";i:13;s:3:\"0.2\";i:15;s:1:\"0\";i:16;s:4:\"-0.2\";i:4;s:3:\"0.2\";}', '2011-12-26 21:06:51', 'TNT', '2022-07-11 09:10:34', 'FEGT', '0', '3', '5');
INSERT INTO `afmCategorien` VALUES ('3', '03StLenEMU', 'Staatsleningen EMU', '4', 'a:17:{i:1;s:0:\"\";i:2;s:0:\"\";i:3;s:1:\"1\";i:17;s:3:\"0.8\";i:14;s:3:\"0.8\";i:6;s:3:\"0.2\";i:5;s:3:\"0.2\";i:7;s:3:\"0.6\";i:9;s:3:\"0.4\";i:8;s:1:\"0\";i:11;s:4:\"-0.2\";i:12;s:4:\"-0.2\";i:10;s:4:\"-0.2\";i:13;s:3:\"0.2\";i:15;s:1:\"0\";i:16;s:4:\"-0.2\";i:4;s:3:\"0.2\";}', '2011-12-26 21:06:51', 'TNT', '2022-07-11 09:10:59', 'FEGT', '0', '3', '5');
INSERT INTO `afmCategorien` VALUES ('4', '14Goud', 'Goud', '17.5', 'a:17:{i:1;s:0:\"\";i:2;s:0:\"\";i:3;s:0:\"\";i:17;s:0:\"\";i:14;s:0:\"\";i:6;s:0:\"\";i:5;s:0:\"\";i:7;s:0:\"\";i:9;s:0:\"\";i:8;s:0:\"\";i:11;s:0:\"\";i:12;s:0:\"\";i:10;s:0:\"\";i:13;s:0:\"\";i:15;s:0:\"\";i:16;s:0:\"\";i:4;s:1:\"1\";}', '2011-12-26 21:06:51', 'TNT', '2022-07-11 09:15:16', 'FEGT', '0', '15', '20');
INSERT INTO `afmCategorien` VALUES ('5', '05StLenOpkLV', 'Staatslen. Opk. Mrkt (loc. valuta)', '10', 'a:17:{i:1;s:0:\"\";i:2;s:0:\"\";i:3;s:0:\"\";i:17;s:0:\"\";i:14;s:0:\"\";i:6;s:0:\"\";i:5;s:1:\"1\";i:7;s:3:\"0.4\";i:9;s:3:\"0.6\";i:8;s:3:\"0.6\";i:11;s:3:\"0.6\";i:12;s:3:\"0.6\";i:10;s:3:\"0.6\";i:13;s:3:\"0.6\";i:15;s:3:\"0.4\";i:16;s:3:\"0.2\";i:4;s:3:\"0.2\";}', '2011-12-26 21:06:51', 'TNT', '2022-07-20 07:41:01', 'FEGT', '0', '8', '12');
INSERT INTO `afmCategorien` VALUES ('6', '04StLenOpkHV', 'Staatslen. Opk. Mrkt (harde valuta)', '12', 'a:17:{i:1;s:0:\"\";i:2;s:0:\"\";i:3;s:0:\"\";i:17;s:0:\"\";i:14;s:0:\"\";i:6;s:1:\"1\";i:5;s:3:\"0.8\";i:7;s:3:\"0.4\";i:9;s:3:\"0.8\";i:8;s:3:\"0.8\";i:11;s:3:\"0.6\";i:12;s:3:\"0.4\";i:10;s:3:\"0.6\";i:13;s:3:\"0.6\";i:15;s:3:\"0.8\";i:16;s:3:\"0.2\";i:4;s:3:\"0.4\";}', '2011-12-26 21:06:51', 'TNT', '2022-07-11 09:12:22', 'FEGT', '0', '10', '14');
INSERT INTO `afmCategorien` VALUES ('7', '06InvGBedrEUR', 'Inv. Grade Bedrijfsobligaties EUR', '4', 'a:17:{i:1;s:0:\"\";i:2;s:0:\"\";i:3;s:0:\"\";i:17;s:0:\"\";i:14;s:0:\"\";i:6;s:0:\"\";i:5;s:0:\"\";i:7;s:1:\"1\";i:9;s:3:\"0.4\";i:8;s:3:\"0.4\";i:11;s:3:\"0.2\";i:12;s:3:\"0.4\";i:10;s:3:\"0.2\";i:13;s:3:\"0.6\";i:15;s:3:\"0.2\";i:16;s:3:\"0.2\";i:4;s:1:\"0\";}', '2011-12-26 21:06:51', 'TNT', '2022-07-20 07:41:43', 'FEGT', '0', '3', '5');
INSERT INTO `afmCategorien` VALUES ('8', '08BedObHighY', 'High Yield Bedrijfsobligaties ', '11', 'a:17:{i:1;s:0:\"\";i:2;s:0:\"\";i:3;s:0:\"\";i:17;s:0:\"\";i:14;s:0:\"\";i:6;s:0:\"\";i:5;s:0:\"\";i:7;s:0:\"\";i:9;s:0:\"\";i:8;s:1:\"1\";i:11;s:3:\"0.6\";i:12;s:3:\"0.6\";i:10;s:3:\"0.6\";i:13;s:3:\"0.6\";i:15;s:3:\"0.8\";i:16;s:3:\"0.4\";i:4;s:3:\"0.2\";}', '2011-12-26 21:06:51', 'TNT', '2022-07-11 09:13:48', 'FEGT', '0', '9', '13');
INSERT INTO `afmCategorien` VALUES ('9', '07IGBedrNietEUR', 'Inv. Grade Bedrijfsobligaties niet-EUR', '7', 'a:17:{i:1;s:0:\"\";i:2;s:0:\"\";i:3;s:0:\"\";i:17;s:0:\"\";i:14;s:0:\"\";i:6;s:0:\"\";i:5;s:0:\"\";i:7;s:0:\"\";i:9;s:1:\"1\";i:8;s:3:\"0.8\";i:11;s:3:\"0.4\";i:12;s:3:\"0.2\";i:10;s:3:\"0.4\";i:13;s:3:\"0.4\";i:15;s:3:\"0.8\";i:16;s:3:\"0.2\";i:4;s:3:\"0.4\";}', '2011-12-26 21:06:51', 'TNT', '2022-07-20 07:42:40', 'FEGT', '0', '6', '8');
INSERT INTO `afmCategorien` VALUES ('10', '11aAandOntwSmlC', 'Aandelen small caps ontwikkelde markten', '17.5', 'a:17:{i:1;s:0:\"\";i:2;s:0:\"\";i:3;s:0:\"\";i:17;s:0:\"\";i:14;s:0:\"\";i:6;s:0:\"\";i:5;s:0:\"\";i:7;s:0:\"\";i:9;s:0:\"\";i:8;s:0:\"\";i:11;s:0:\"\";i:12;s:0:\"\";i:10;s:1:\"1\";i:13;s:3:\"0.8\";i:15;s:3:\"0.6\";i:16;s:3:\"0.4\";i:4;s:1:\"0\";}', '2011-12-26 21:06:51', 'TNT', '2022-07-20 10:15:54', 'FEGT', '0', '15', '20');
INSERT INTO `afmCategorien` VALUES ('11', '09AandOntw', 'Aandelen Ontwikkelde Markten', '14.5', 'a:17:{i:1;s:0:\"\";i:2;s:0:\"\";i:3;s:0:\"\";i:17;s:0:\"\";i:14;s:0:\"\";i:6;s:0:\"\";i:5;s:0:\"\";i:7;s:0:\"\";i:9;s:0:\"\";i:8;s:0:\"\";i:11;s:1:\"1\";i:12;s:3:\"0.8\";i:10;s:3:\"0.8\";i:13;s:3:\"0.8\";i:15;s:3:\"0.6\";i:16;s:3:\"0.4\";i:4;s:1:\"0\";}', '2011-12-26 21:06:51', 'TNT', '2022-07-11 09:14:02', 'FEGT', '0', '12', '17');
INSERT INTO `afmCategorien` VALUES ('12', '10AandOpk', 'Aandelen Opkomende Markten', '20.5', 'a:17:{i:1;s:0:\"\";i:2;s:0:\"\";i:3;s:0:\"\";i:17;s:0:\"\";i:14;s:0:\"\";i:6;s:0:\"\";i:5;s:0:\"\";i:7;s:0:\"\";i:9;s:0:\"\";i:8;s:0:\"\";i:11;s:0:\"\";i:12;s:1:\"1\";i:10;s:3:\"0.8\";i:13;s:3:\"0.6\";i:15;s:3:\"0.4\";i:16;s:3:\"0.4\";i:4;s:3:\"0.2\";}', '2011-12-26 21:06:51', 'TNT', '2022-07-11 09:14:20', 'FEGT', '0', '18', '23');
INSERT INTO `afmCategorien` VALUES ('13', '11BeursgVastg', 'Beursgenoteerd Vastgoed', '18.5', 'a:17:{i:1;s:0:\"\";i:2;s:0:\"\";i:3;s:0:\"\";i:17;s:0:\"\";i:14;s:0:\"\";i:6;s:0:\"\";i:5;s:0:\"\";i:7;s:0:\"\";i:9;s:0:\"\";i:8;s:0:\"\";i:11;s:0:\"\";i:12;s:0:\"\";i:10;s:0:\"\";i:13;s:1:\"1\";i:15;s:3:\"0.4\";i:16;s:3:\"0.4\";i:4;s:1:\"0\";}', '2011-12-26 21:06:51', 'TNT', '2022-07-11 09:14:45', 'FEGT', '0', '16', '21');
INSERT INTO `afmCategorien` VALUES ('14', '04bStLenWrldEhg', 'Staatsleningen wereldwijd (eurohedged)', '5', 'a:17:{i:1;s:0:\"\";i:2;s:0:\"\";i:3;s:0:\"\";i:17;s:0:\"\";i:14;s:1:\"1\";i:6;s:3:\"0.2\";i:5;s:3:\"0.2\";i:7;s:3:\"0.6\";i:9;s:3:\"0.4\";i:8;s:4:\"-0.2\";i:11;s:4:\"-0.2\";i:12;s:4:\"-0.2\";i:10;s:4:\"-0.4\";i:13;s:1:\"0\";i:15;s:4:\"-0.2\";i:16;s:4:\"-0.2\";i:4;s:3:\"0.2\";}', '2011-12-26 21:06:51', 'TNT', '2022-07-20 10:16:09', 'FEGT', '0', '3', '7');
INSERT INTO `afmCategorien` VALUES ('15', '12Hedgefunds', 'Hedge Funds', '9.5', 'a:17:{i:1;s:0:\"\";i:2;s:0:\"\";i:3;s:0:\"\";i:17;s:0:\"\";i:14;s:0:\"\";i:6;s:0:\"\";i:5;s:0:\"\";i:7;s:0:\"\";i:9;s:0:\"\";i:8;s:0:\"\";i:11;s:0:\"\";i:12;s:0:\"\";i:10;s:0:\"\";i:13;s:0:\"\";i:15;s:1:\"1\";i:16;s:3:\"0.4\";i:4;s:3:\"0.2\";}', '2011-12-26 21:06:51', 'TNT', '2022-07-11 09:14:55', 'FEGT', '0', '7', '12');
INSERT INTO `afmCategorien` VALUES ('16', '13Grondstoffen', 'Grondstoffen', '22.5', 'a:17:{i:1;s:0:\"\";i:2;s:0:\"\";i:3;s:0:\"\";i:17;s:0:\"\";i:14;s:0:\"\";i:6;s:0:\"\";i:5;s:0:\"\";i:7;s:0:\"\";i:9;s:0:\"\";i:8;s:0:\"\";i:11;s:0:\"\";i:12;s:0:\"\";i:10;s:0:\"\";i:13;s:0:\"\";i:15;s:0:\"\";i:16;s:1:\"1\";i:4;s:3:\"0.2\";}', '2011-12-26 21:06:51', 'TNT', '2022-07-11 09:15:05', 'FEGT', '0', '20', '25');
INSERT INTO `afmCategorien` VALUES ('17', '04aStLenInflEUR', 'Staatsleningen infl gerelateerd EUR', '6.5', 'a:17:{i:1;s:0:\"\";i:2;s:0:\"\";i:3;s:0:\"\";i:17;s:1:\"1\";i:14;s:3:\"0.6\";i:6;s:3:\"0.2\";i:5;s:3:\"0.4\";i:7;s:3:\"0.8\";i:9;s:3:\"0.2\";i:8;s:3:\"0.2\";i:11;s:3:\"0.2\";i:12;s:3:\"0.2\";i:10;s:3:\"0.2\";i:13;s:3:\"0.4\";i:15;s:1:\"0\";i:16;s:3:\"0.2\";i:4;s:1:\"0\";}', '2011-12-26 21:06:51', 'TNT', '2022-07-11 09:11:30', 'FEGT', '0', '5', '8');";
$insertQueries=explode(";\n",$txt);

foreach ($tables as $table => $newTable)
{
 if ($db->QRecords("SHOW TABLE STATUS LIKE '$newTable'") < 1)
 {
   $query="CREATE TABLE $newTable LIKE $table;";
   $db->SQL($query);
   $db->Query();
   $query="INSERT INTO $newTable (SELECT * FROM $table)";
   $db->SQL($query);
   if($db->Query())
   {
     $query="TRUNCATE afmCategorien";
     $db->SQL($query);
     $db->Query();
     foreach($insertQueries as $query)
     {
       $db->SQL($query);
       $db->Query();
     }
   }
 }
}
