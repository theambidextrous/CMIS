<?php

/*

CometChat
Copyright (c) 2016 Inscripts
License: https://www.cometchat.com/legal/license

*/
class CometChatSessionHandler{
    function cometchatSessionOpen($path, $name) {
        global $dbh;
        cometchatDBConnect();
        $query = sql_query('cometchatSessionOpen',array('session_id'=>session_id()));
        return true;
    }

    function cometchatSessionClose() {
        $sessionId = session_id();
        //perform some action here
        return true;
    }

    function cometchatSessionRead($sessionId) {
        global $dbh;
        cometchatDBConnect();
        $data = "";
        $query = sql_query('cometchatSessionRead',array('session_id'=>session_id()));
        if($session = sql_fetch_assoc($query)){
            $data = $session['session_data'];
        }
        return $data;
    }

    function cometchatSessionWrite($sessionId, $data) {
        global $dbh;
        cometchatDBConnect();
        $query = sql_query('cometchatSessionWrite',array('session_id'=>session_id(), 'session_data'=>$data));
        return true;
    }

    function cometchatSessionDestroy($sessionId) {
        global $dbh;
        cometchatDBConnect();
        $query = sql_query('cometchatSessionDestroy',array('session_id'=>session_id()));
        setcookie(session_name(), "", time() - 3600);
        return true;
    }

    function cometchatSessionGarbageCollector($lifetime) {
        global $dbh;
        cometchatDBConnect();
        $query = sql_query('cometchatSessionGarbageCollector',array('lifetime'=>$lifetime));
        return true;
    }
}
$handler = new CometChatSessionHandler();
if((defined('USE_COMETCHAT_SESSION') && USE_COMETCHAT_SESSION == 1) || (defined('CCADMIN') &&  CCADMIN ==1 )){
    @session_set_save_handler(
        array($handler,"cometchatSessionOpen"),
        array($handler,"cometchatSessionClose"),
        array($handler,"cometchatSessionRead"),
        array($handler,"cometchatSessionWrite"),
        array($handler,"cometchatSessionDestroy"),
        array($handler,"cometchatSessionGarbageCollector")
    );
    register_shutdown_function('session_write_close');
}
