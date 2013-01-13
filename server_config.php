<?php

        //This file contains code that defines constants that help with including files.

        $os = PHP_OS;
        switch($os)
        {
            case "Linux": define("DS", "/"); break;
            case "Windows": define("DS", "\\"); break;
            default: define("DS", "/"); break;
        }
        define("APP_ROOT", $_SERVER['DOCUMENT_ROOT'] . DS . 's2h' . DS);
        define("BASE_URL", $_SERVER['SERVER_NAME'] . DS . 's2h' . DS);
        define("BASE_DIR", 's2h' . DS);
?>
