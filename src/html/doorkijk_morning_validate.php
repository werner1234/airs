<?php
/**
 * Created by PhpStorm.
 * User: bdl
 * Date: 16-10-2017
 * Time: 10:44
 *
 */

include_once("wwwvars.php");

define("EPSILON", 0.001 );  // --- max foutmarge tov 100% ---

function validateCvsFile($filename, $categorieSoort)
{

    global $error, $csvRegels, $_POST;
    $error = array();
    $db = new DB();
    $parts = explode("-",$_POST['datumVanaf']);
    $dbdatum = $parts[2]."-".$parts[1]."-".$parts[0];
    if (!$handle = @fopen($filename, "r"))
    {
        $error[] = vt("FOUT").": ".vt("bestand")." $filename ".vt("is niet leesbaar");
        return false;
    }

    $row = 0;
    $skipHeader = TRUE;
    $db_check = new DB();
    while ($data = fgetcsv($handle, 1000, DELIM))
    {
        $row++;
        if ($skipHeader)
        {
            $skipHeader = FALSE;
            $velden = $data;
            $aantal_velden = count($velden) - 2; // 1 minder tgv tellen vanaf 0, 1 minder omdat laatste kol is totaal
            for ($idx = BEGIN_VELDEN; $idx <= $aantal_velden; $idx++)
            {
                // --- komt deze key voor in doorkijk_msCategorieSoort
                $sql_check = "SELECT id 
                                   FROM doorkijk_msCategoriesoort 
                                   WHERE msCategoriesoort = '" . $categorieSoort . "' AND
                                         msCategorie      = '" . $velden[$idx] . "  '";
                $db_check->SQL($sql_check);
                $id = $db_check->lookupRecord();
                if (!$id)
                {
                    $_code = $categorieSoort . " en " . $velden[$idx];
                    $error[] = "$row : ".vt("Weging komt niet voor in doorkijk_msCategoriesoort")." ($_code)";
                }
            }
        }
        else
        {
            //--- is dit fonds bekend in Fondsen
            $sql_fonds = "SELECT fonds FROM Fondsen 
                                           WHERE fonds = '" . $data[0] . "'";
            $db_check->SQL($sql_fonds);
            $id = $db_check->lookupRecord();
            if (!$id)
            {
                $_code = $data[0];
                $error[] = "$row : ".vt("Fonds komt niet voor in Fondsen").". ($_code)";
            }

            // --- validatie op bestaan van fonds op inleesdatum ---
            $query = "
            SELECT 
                id
            FROM 
                doorkijk_categorieWegingenPerFonds
            WHERE 
                Fonds = '" . $data[0] . "' AND 
                msCategoriesoort = '".$_POST["categorieSoort"]."' AND 
                datumVanaf = '" . $dbdatum . "'";

            if ($rec = $db->lookupRecordByQuery($query))
            {
                $error[] = "$row : ".vt("Fonds")." ".$data[0]." ".vt("bestaat al op datum")." ".$_POST['datumVanaf'];
            }

            // --- controle op correctheid wegingskolommen ---
            $row_total = 0;
            for ($idx = BEGIN_VELDEN; $idx <= $aantal_velden; $idx++)
            {
                // --- decimale kommas door punten vervangen ---
                $data[$idx] = 1 * (str_replace(",", ".", $data[$idx]));
                // lege waarden niet verwerken
                if ($data[$idx] != 0)
                {
                    // --- T.b.v. controle op 100% ---
                    $row_total = $row_total + $data[$idx];
                }
            } // --- end of wegingsvelden ---
            // --- validate op 100% ---
            $rowTotalCompair = $row_total - 100;
            $absRowTotal = abs($rowTotalCompair);

            if ($absRowTotal > EPSILON)
            {
                $error[] = "$row : ".vt("Som der wegingen")." $row_total% ".vt("is <> 100% in rij")." ($row)";
            }
        } // end not header record
    } //--- end of while ---

    fclose($handle);
    if (Count($error) == 0)
        return true;
    else
    {
        return false;
    }
} // --- end of function ---


