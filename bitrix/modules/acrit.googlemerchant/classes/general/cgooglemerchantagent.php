<?php

class CGooglemerchantAgent{
    private static $moduleId = "acrit.googlemerchant";

    public static function StartExport( $profileId, $cronSetupId, $bTmpAgent = false ){
        $returnRow = ( $bTmpAgent ) ? "" : __CLASS__."::".__FUNCTION__."(".$profileId.",".$cronSetupId.");";

        if( file_exists( $_SERVER["DOCUMENT_ROOT"]."/bitrix/tools/".self::$moduleId."/export_{$profileId}_run.lock" ) ){
            return $returnRow;
        }

        AcritGooglemerchantSession::Init( 0 );
        AcritGooglemerchantSession::DeleteSession( $profileId );
        $arAgent = CAgent::GetList(
            array(),
            array(
                "NAME" => "CGooglemerchantAgent::StartExport(".$profileId.",".$cronSetupId.");"
            )
        )->Fetch();

        if( $arAgent ){
            $dbProfile = new CGooglemerchantProfileDB();
            $arProfile = $dbProfile->GetByID( $profileId );

            if( isset( $arProfile["SETUP"]["CRON"][$cronSetupId]["IS_PERIOD"] ) && ( $arProfile["SETUP"]["CRON"][$cronSetupId]["IS_PERIOD"] == "Y" ) ){
                register_shutdown_function( array( "CGooglemerchantAgent", "UpdateAgent" ), $profileId, $cronSetupId, $arAgent, $arProfile );
            }
        }

        $export = new CAcritGooglemerchantExport( intval( $profileId ) );
        $export->Export( "agent", 0, $cronSetupId );

        return $returnRow;
    }

    public static function AddAgent( $profileId, $cronSetupIdPreset = false ){
        if( $profileId > 0 ){
            $dbProfile = new CGooglemerchantProfileDB();
            $arProfile = $dbProfile->GetByID( $profileId );

            if( $cronSetupIdPreset ){
                $agentPeriod = 86400;
                $currentDateStamp = time() + 120;

                $runTime = date(
                    "d.m.Y H:i",
                    $currentDateStamp
                );

                $agentId = CAgent::AddAgent(
                    "CGooglemerchantAgent::StartExport(".$profileId.",".$cronSetupIdPreset.",true);",
                    self::$moduleId,
                    "N",
                    $agentPeriod,
                    "",
                    "Y",
                    $runTime
                );
            }
            else{
                if( is_array( $arProfile["SETUP"]["CRON"] ) && !empty( $arProfile["SETUP"]["CRON"] ) ){
                    foreach( $arProfile["SETUP"]["CRON"] as $cronSetupId => $arCronItem ){
                        $agentId = 0;
                        $agentPeriod = intval( $arCronItem["PERIOD"] ) * 60;

                        if( $agentPeriod <= 0 ){
                            $agentPeriod = 86400;
                        }

                        $setupDateStamp = MakeTimeStamp( $arCronItem["DAT_START"] );
                        $currentDateStamp = time();
                        $currentDateStampToStart = $currentDateStamp + 120;

                        $runTime = date(
                            "d.m.Y H:i",
                            $setupDateStamp
                        );

                        $arAgent = CAgent::GetList(
                            array(),
                            array(
                                "NAME" => "CGooglemerchantAgent::StartExport(".$profileId.",".$cronSetupId.");"
                            )
                        )->Fetch();

                        if( !$arAgent ){
                            if( !isset( $setupDateStamp ) ){
                                $runTime = date(
                                    "d.m.Y H:i",
                                    $currentDateStampToStart
                                );
                            }
                            elseif( $setupDateStamp < $currentDateStamp ){
                                while( $setupDateStamp < $currentDateStamp ){
                                    $setupDateStamp += $agentPeriod;
                                }

                                $runTime = date(
                                    "d.m.Y H:i",
                                    $setupDateStamp
                                );
                            }

                            $agentId = CAgent::AddAgent(
                                "CGooglemerchantAgent::StartExport(".$profileId.",".$cronSetupId.");",
                                self::$moduleId,
                                "N",
                                $agentPeriod,
                                "",
                                "Y",
                                $runTime
                            );
                        }
                        elseif( $arAgent ){
                            $agentId = $arAgent["ID"];
                            $agentNextStart = MakeTimeStamp( $arAgent["NEXT_EXEC"] );

                            if( ( $agentNextStart == $setupDateStamp ) && ( $agentNextStart > $currentDateStamp ) ){
                                $runTime = date(
                                    "d.m.Y H:i",
                                    $agentNextStart
                                );
                            }
                            elseif( $setupDateStamp < $currentDateStamp ){
                                while( $setupDateStamp < $currentDateStamp ){
                                    $setupDateStamp += $arAgent["AGENT_INTERVAL"];
                                }

                                $runTime = date(
                                    "d.m.Y H:i",
                                    $setupDateStamp
                                );
                            }

                            CAgent::Update(
                                $arAgent["ID"],
                                array(
                                    "AGENT_INTERVAL" => ( $agentPeriod != $arAgent["AGENT_INTERVAL"] ) ? $agentPeriod : $arAgent["AGENT_INTERVAL"],
                                    "NEXT_EXEC" => $runTime
                                )
                            );
                        }
                    }
                }
            }
        }

        if( file_exists( $_SERVER["DOCUMENT_ROOT"]."/bitrix/crontab/crontab.cfg" ) ){
            $arCronList = array();
            @exec( "crontab -l", $arCronList );

            $arUserCronList = array();
            $arProcessCronList = array();
            foreach( $arCronList as $cronItem ){
                if( stripos( $cronItem, $_SERVER["DOCUMENT_ROOT"] ) === false ){
                    $arUserCronList[] = $cronItem;
                }
                else{
                    if( stripos( $cronItem, "bitrix/modules/".self::$moduleId."/tools/cron_events.php" ) === false ){
                        $arUserCronList[] = $cronItem;
                        $arProcessCronList[] = $cronItem;
                    }
                }
            }

            if( $arProfile["SETUP"]["CRON_OPTIONS"]["MODE"] != "curl" ){
                $taskMode = trim( $arProfile["SETUP"]["CRON_OPTIONS"]["PHP"] )." -f";
            }
            else{
                $taskMode = "curl -s -o /dev/null";
            }

            $cronTask = "* * * * * {$taskMode} {$_SERVER["DOCUMENT_ROOT"]}/bitrix/modules/".self::$moduleId."/tools/cron_events.php";
            $arUserCronList[] = $cronTask;
            $arProcessCronList[] = $cronTask;

            file_put_contents( $_SERVER["DOCUMENT_ROOT"]."/bitrix/crontab/crontab.cfg", implode( PHP_EOL, $arUserCronList ).PHP_EOL );
            @exec( "crontab ".$_SERVER["DOCUMENT_ROOT"]."/bitrix/crontab/crontab.cfg" );

            file_put_contents( $_SERVER["DOCUMENT_ROOT"]."/bitrix/crontab/crontab.cfg", implode( PHP_EOL, $arProcessCronList ).PHP_EOL );
        }

        return $agentId;
    }

    public static function DelAgent( $profileId, $cronSetupId = false ){
        if( $profileId > 0 ){
            if( $cronSetupId ){
                CAgent::RemoveAgent(
                    "CGooglemerchantAgent::StartExport(".$profileId.",".$cronSetupId.");",
                    self::$moduleId
                );
            }
            else{
                $dbProfile = new CGooglemerchantProfileDB();
                $arProfile = $dbProfile->GetByID( $profileId );

                if( is_array( $arProfile["SETUP"]["CRON"] ) && !empty( $arProfile["SETUP"]["CRON"] ) ){
                    foreach( $arProfile["SETUP"]["CRON"] as $cronSetupId => $arCronItem ){
                        CAgent::RemoveAgent(
                            "CGooglemerchantAgent::StartExport(".$profileId.",".$cronSetupId.");",
                            self::$moduleId
                        );
                    }
                }
            }
        }
    }

    public static function UpdateAgent( $profileId, $cronSetupId, $arAgent, $arProfile ){
        $arAgentAfter = CAgent::GetList(
            array(),
            array(
                "NAME" => "CGooglemerchantAgent::StartExport(".$profileId.",".$cronSetupId.");"
            )
        )->Fetch();

        if( !$arAgentAfter || ( $arAgentAfter["ID"] !== $arAgent["ID"] ) ) return false;

        $currentDateStamp = time() + 120;
        $agentPeriod = intval( $arAgent["AGENT_INTERVAL"] );

        if( $agentPeriod <= 0 ){
            $agentPeriod = 86400;
        }

        $format = CSite::GetDateFormat();
        $agentNextExec = MakeTimeStamp( $arAgent["NEXT_EXEC"], $format );
        $nextExec = $agentNextExec + $agentPeriod;

        $runTime = date(
            "d.m.Y H:i",
            $nextExec
        );

        if( isset( $arAgentAfter["LAST_EXEC"] ) && !is_null( $arAgentAfter["LAST_EXEC"] ) ){
            $agentLastExec = MakeTimeStamp( $arAgentAfter["LAST_EXEC"], $format );
            $agentLastExec = $agentLastExec + 120;

            if( $nextExec < $agentLastExec ){
                while( $nextExec < $agentLastExec ){
                    $nextExec = $nextExec + $agentPeriod;
                }
            }

            $runTime = date(
                "d.m.Y H:i",
                $nextExec
            );
        }
        elseif( $nextExec < $currentDateStamp ){
            while( $nextExec < $currentDateStamp ){
                $nextExec = $nextExec + $agentPeriod;
            }

            $runTime = date(
                "d.m.Y H:i",
                $nextExec
            );
        }

        CAgent::Update(
            $arAgent["ID"],
            array(
                "NEXT_EXEC" => $runTime
            )
        );
    }

    public static function GetIntersectAgents( $arProfileAgents ){
        $arAgents = array();

        if( is_array( $arProfileAgents ) && !empty( $arProfileAgents ) ){
            foreach( $arProfileAgents as $cronSetupId => $arCronItem ){
                $setupDateStamp = MakeTimeStamp( $arCronItem["DAT_START"] );
                $currentDateStampToStart = $setupDateStamp - 1200;

                $runTime = date(
                    "d.m.Y H:i",
                    $currentDateStampToStart
                );

                $dbAgent = CAgent::GetList(
                    array(),
                    array(
                        "MODULE_ID" => self::$moduleId,
                        "NEXT_EXEC" => $runTime,
                    )
                );

                while( $arAgent = $dbAgent->Fetch() ){
                    if(
                        ( ( MakeTimeStamp( $arAgent["NEXT_EXEC"] ) - $setupDateStamp ) > 0 )
                        && ( ( MakeTimeStamp( $arAgent["NEXT_EXEC"] ) - $setupDateStamp ) < 1200 )
                    ){
                        $arAgents[] = $arAgent;
                    }
                }
            }
        }

        return $arAgents;
    }

    public static function GetNextAgentTime( $profileId ){
        if( $profileId > 0 ){
            $dbProfile = new CGooglemerchantProfileDB();
            $arProfile = $dbProfile->GetByID( $profileId );

            $runTime = false;

            $arRunTimes = array();
            if( is_array( $arProfile["SETUP"]["CRON"] ) && !empty( $arProfile["SETUP"]["CRON"] ) ){
                foreach( $arProfile["SETUP"]["CRON"] as $cronSetupId => $arCronItem ){
                    $agentId = 0;
                    $agentPeriod = intval( $arCronItem["PERIOD"] ) * 60;

                    if( $agentPeriod <= 0 ){
                        $agentPeriod = 86400;
                    }

                    $setupDateStamp = MakeTimeStamp( $arCronItem["DAT_START"] );
                    $currentDateStamp = time();
                    $currentDateStampToStart = $currentDateStamp + 120;

                    $runTime = date(
                        "d.m.Y H:i:s",
                        $setupDateStamp
                    );

                    $arAgent = CAgent::GetList(
                        array(),
                        array(
                            "NAME" => "CGooglemerchantAgent::StartExport(".$profileId.",".$cronSetupId.");"
                        )
                    )->Fetch();

                    if( !$arAgent ){
                        if( !isset( $setupDateStamp ) ){
                            $runTime = date(
                                "d.m.Y H:i:s",
                                $currentDateStampToStart
                            );
                        }
                        elseif( $setupDateStamp < $currentDateStamp ){
                            while( $setupDateStamp < $currentDateStamp ){
                                $setupDateStamp += $agentPeriod;
                            }

                            $runTime = date(
                                "d.m.Y H:i:s",
                                $setupDateStamp
                            );
                        }
                    }
                    elseif( $arAgent ){
                        $agentId = $arAgent["ID"];
                        $agentNextStart = MakeTimeStamp( $arAgent["NEXT_EXEC"] );

                        if( ( $agentNextStart == $setupDateStamp ) && ( $agentNextStart > $currentDateStamp ) ){
                            $runTime = date(
                                "d.m.Y H:i:s",
                                $agentNextStart
                            );
                        }
                        elseif( $setupDateStamp < $currentDateStamp ){
                            while( $setupDateStamp < $currentDateStamp ){
                                $setupDateStamp += $arAgent["AGENT_INTERVAL"];
                            }

                            $runTime = date(
                                "d.m.Y H:i:s",
                                $setupDateStamp
                            );
                        }
                    }
                    $arRunTimes[] = $runTime;
                }

                if( is_array( $arRunTimes ) && !empty( $arRunTimes ) ){
                    $arRunTimesStamps = array();
                    foreach( $arRunTimes as $runTimesItem ){
                        $arRunTimesStamps[] = MakeTimeStamp( $runTimesItem );
                    }

                    $runTime = date(
                        "d.m.Y H:i:s",
                        min( $arRunTimesStamps )
                    );
                }
            }
        }

        return $runTime;
    }

    public static function CheckAgents(){
        global $CACHE_MANAGER;

        //For a while agents will execute only on primary cluster group
        if( ( defined( "NO_AGENT_CHECK" ) && NO_AGENT_CHECK === true )
            || ( defined( "BX_CLUSTER_GROUP" ) && ( BX_CLUSTER_GROUP !== 1 ) )
        ){
            return null;
        }

        $bAgentsUseCrontab = COption::GetOptionString( "main", "agents_use_crontab", "N" );
        $sCrontab = "";
        if( ( $bAgentsUseCrontab == "Y" )
            || ( defined( "BX_CRONTAB_SUPPORT" ) && ( BX_CRONTAB_SUPPORT === true ) )
        ){
            if( defined( "BX_CRONTAB" ) && ( BX_CRONTAB === true ) ){
                $sCrontab = " AND IS_PERIOD='N' ";
            }
            else{
                $sCrontab = " AND IS_PERIOD='Y' ";
            }
        }

        if( ( CACHED_b_agent !== false )
            && $CACHE_MANAGER->Read( CACHED_b_agent, ( $cacheId = "agents".$sCrontab ), "agents" )
        ){
            $savedTime = $CACHE_MANAGER->Get( $cacheId );
            if( time() < $savedTime ){
                return "";
            }
        }

        return CAgent::ExecuteAgents( $sCrontab );
    }

    public static function ExecuteAgents( $sCrontab ){
        global $DB, $CACHE_MANAGER, $pPERIOD;

        if( defined( "BX_FORK_AGENTS_AND_EVENTS_FUNCTION" ) ){
            if( CMain::ForkActions( array( "CAgent", "ExecuteAgents" ), array( $sCrontab ) ) ){
                return "";
            }
        }

        $savedTime = 0;
        $cacheId = "agents".$sCrontab;
        if( ( CACHED_b_agent !== false ) && $CACHE_MANAGER->Read( CACHED_b_agent, $cacheId, "agents" ) ){
            $savedTime = $CACHE_MANAGER->Get( $cacheId );
            if( time() < $savedTime ){
                return "";
            }
        }

        $uniq = CMain::GetServerUniqID();

        $strSql = "
            SELECT 'x'
            FROM b_agent
            WHERE
                ACTIVE = 'Y'
                AND NEXT_EXEC <= now()
                AND (DATE_CHECK IS NULL OR DATE_CHECK <= now())
                AND (MODULE_ID = '".self::$moduleId."')
                ".$sCrontab."
            LIMIT 1
        ";

        $dbAgentsResult = $DB->Query( $strSql );
        if( $dbAgentsResult->Fetch() ){
            $dbLock = $DB->Query( "SELECT GET_LOCK('".$uniq."_agent', 0) as L" );
            $arLock = $dbLock->Fetch();
            if( $arLock["L"] == "0" ){
                return "";
            }
        }
        else{
            if( CACHED_b_agent !== false ){
                $rs = $DB->Query( "SELECT UNIX_TIMESTAMP(MIN(NEXT_EXEC))-UNIX_TIMESTAMP(NOW()) DATE_DIFF FROM b_agent WHERE ACTIVE='Y' ".$sCrontab."" );
                $ar = $rs->Fetch();
                if( !$ar || ( $ar["DATE_DIFF"] < 0 ) ){
                    $dateDiff = 0;
                }
                elseif( $ar["DATE_DIFF"] > CACHED_b_agent ){
                    $dateDiff = CACHED_b_agent;
                }
                else{
                    $dateDiff = $ar["DATE_DIFF"];
                }

                if( $savedTime > 0 ){
                    $CACHE_MANAGER->Clean( $cacheId, "agents" );
                    $CACHE_MANAGER->Read( CACHED_b_agent, $cacheId, "agents" );
                }

                $CACHE_MANAGER->Set( $cacheId, intval( time() + $dateDiff ) );
            }

            return "";
        }

        $strSql =
            "SELECT ID, NAME, AGENT_INTERVAL, IS_PERIOD, MODULE_ID ".
            "FROM b_agent ".
            "WHERE ACTIVE='Y' ".
            "    AND NEXT_EXEC<=now() ".
            "    AND (DATE_CHECK IS NULL OR DATE_CHECK<=now()) ".
            "    AND (MODULE_ID = '".self::$moduleId."') ".
            $sCrontab.
            " ORDER BY RUNNING ASC, SORT desc";

        $dbAgentsResult = $DB->Query( $strSql );

        $ids = "";
        $arAgents = array();
        while( $arAgentsResult = $dbAgentsResult->Fetch() ){
            $arAgents[] = $arAgentsResult;
            $ids .= ( ( $ids != "" ) ? ", " : "" ).$arAgentsResult["ID"];
        }

        if( $ids != "" ){
            $strSql = "UPDATE b_agent SET DATE_CHECK=DATE_ADD(IF(DATE_CHECK IS NULL, now(), DATE_CHECK), INTERVAL 600 SECOND) WHERE ID IN (".$ids.")";
            $DB->Query( $strSql );
        }

        $DB->Query( "SELECT RELEASE_LOCK('".$uniq."_agent')" );

        $logFunction = ( defined( "BX_AGENTS_LOG_FUNCTION" ) && function_exists( BX_AGENTS_LOG_FUNCTION ) ) ? BX_AGENTS_LOG_FUNCTION : false;

        for( $i = 0, $n = count( $arAgents ); $i < $n; $i++ ){
            $arAgent = $arAgents[$i];
            if( $arAgent["MODULE_ID"] != self::$moduleId ){
                continue;
            }

            if( $logFunction ){
                $logFunction( $arAgent, "start" );
            }

            @set_time_limit( 0 );
            ignore_user_abort( true );

            if( ( strlen( $arAgent["MODULE_ID"] ) > 0 ) && ( $arAgent["MODULE_ID"] != "main" ) ){
                if( !CModule::IncludeModule( $arAgent["MODULE_ID"] ) ){
                    continue;
                }
            }

            //update the agent to the running state - if it fails it'll go to the end of the list on the next try
            $DB->Query( "UPDATE b_agent SET RUNNING='Y' WHERE ID=".$arAgent["ID"] );

            //these vars can be assigned within agent code
            $pPERIOD = $arAgent["AGENT_INTERVAL"];

            CTimeZone::Disable();

            global $USER;
            unset( $USER );
            try{
                $evalResult = "";
                $e = eval( "\$evalResult=".$arAgent["NAME"] );
            }
            catch( Exception $e ){
                CTimeZone::Enable();

                $application = \Bitrix\Main\Application::getInstance();
                $exceptionHandler = $application->getExceptionHandler();
                $exceptionHandler->writeToLog( $e );

                continue;
            }
            unset( $USER );

            CTimeZone::Enable();

            if( $logFunction ){
                $logFunction( $arAgent, "finish", $evalResult, $e );
            }

            if( $e === false ){
                continue;
            }
            elseif( strlen( $evalResult ) <= 0 ){
                $strSql = "DELETE FROM b_agent WHERE ID=".$arAgent["ID"];
            }
            else{
                $strSql = "
                    UPDATE b_agent SET
                        NAME='".$DB->ForSQL( $evalResult, 2000 )."',
                        LAST_EXEC=now(),
                        NEXT_EXEC=DATE_ADD(".( ( $arAgent["IS_PERIOD"] == "Y" ) ? "NEXT_EXEC" : "now()" ).", INTERVAL ".$pPERIOD." SECOND),
                        DATE_CHECK=NULL,
                        RUNNING='N'
                    WHERE ID=".$arAgent["ID"];
            }

            $DB->Query( $strSql );
        }
        return null;
    }

    public static function GetCronTasks(){
        $sTasks = false;

        if( file_exists( $_SERVER["DOCUMENT_ROOT"]."/bitrix/crontab/crontab.cfg" ) ){
            $cfgFileSize = filesize( $_SERVER["DOCUMENT_ROOT"]."/bitrix/crontab/crontab.cfg" );
            $fp = fopen( $_SERVER["DOCUMENT_ROOT"]."/bitrix/crontab/crontab.cfg", "rb" );
            $sTasks = fread( $fp, $cfgFileSize );
            fclose( $fp );
        }

        return $sTasks;
    }
}