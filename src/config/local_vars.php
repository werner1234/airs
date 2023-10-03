<?php
        // localDatabase
        $_DB_resources[1]['server'] = !empty(getenv("AIRS_CONFIG__DB_SERVER")) ? getenv("AIRS_CONFIG__DB_SERVER") : "mysql";
        $_DB_resources[1]['user']   = !empty(getenv("AIRS_CONFIG__DB_USER")) ? getenv("AIRS_CONFIG__DB_USER") : "airs-dev";
        $_DB_resources[1]['passwd'] = !empty(getenv("AIRS_CONFIG__DB_PASSWORD")) ? getenv("AIRS_CONFIG__DB_PASSWORD") : "airs-dev-php-pwd";
        $_DB_resources[1]['db']     = !empty(getenv("AIRS_CONFIG__DB_NAME")) ? getenv("AIRS_CONFIG__DB_NAME") : "airs_dev_db";

        // $_DB_resources[1]['server'] = "blanco-airs-poc-eu-centr-databaseclusterprovision-duj557o9szdc.cluster-cpuq1igb3uur.eu-central-1.rds.amazonaws.com";
        // $_DB_resources[1]['user']   = "customer01_user";
        // $_DB_resources[1]['passwd'] = "UZO4=>o3VDr.sRUp2&yC4hgpN{sAK#u%";
        // $_DB_resources[1]['db']     = "customer01";

        // // queueDatabase
        $_DB_resources[2]['server'] = !empty(getenv("DB_UPDATE_SERVER")) ? getenv("DB_UPDATE_SERVER") : "";
        $_DB_resources[2]['user']   = !empty(getenv("DB_UPDATE_USER")) ? getenv("DB_UPDATE_USER") : "";
        $_DB_resources[2]['passwd'] = !empty(getenv("DB_UPDATE_PASSWORD")) ? getenv("DB_UPDATE_PASSWORD") : "";
        $_DB_resources[2]['db']     = !empty(getenv("DB_UPDATE_NAME")) ? getenv("DB_UPDATE_NAME") : "";

        $__appvar['basedir'] =  realpath(dirname(__FILE__)."/..");
        $__appvar['bedrijf'] = "BTR";
        $__appvar['logAccess'] = true;
        $__appvar["ftpPasv"] = true;
        $__appvar["tgc"]       = "enabled";
        $__appvar["SMSapiKey"] = "0987209";

        putenv("AIRS_CONFIG__API_URL=blah");
        putenv("AIRS_CONFIG__FRONTEND_URL=blah");

        $__debug = true;

// function vt($in)
// {
//         return($in);
// }