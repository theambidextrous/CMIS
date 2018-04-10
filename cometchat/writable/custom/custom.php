<?php

/*
CometChat
Copyright (c) 2016 Inscripts
License: https://www.cometchat.com/legal/license
*/

/* HOOKS */

/*
* Hook: hooks_processUserID
* Description: action performed on the basis of userid and usertype.
* Params: 1. userid
* Returns: processed userid
*/
/*
function hooks_processUserID($params){
	return $params['userid'];
}
*/

/*
* Hook: hooks_processMessageBefore
* Description: process message or perform action before sending message
* Params:
*		1. to
* 		2. message
*		3. dir
* Returns: processed message
*/
/*
function hooks_processMessageBefore($params){
	return $params['message'];
}
*/

/*
* Hook: hooks_processMessageAfter
* Description: action performed after sending the message
* Params:
*		1. insertedid
*		2. to
* 		3. message
*		4. dir
* Returns: processed message
*/
/*
function hooks_processMessageAfter($params){
	return $params['message'];
}
*/

/*
* Hook: hooks_processGroupMessageBefore
* Description: process message  or perform action before sending group message
* Params:
*		1. to
* 		2. message
* Returns: processed message
*/
/*
function hooks_processGroupMessageBefore($params){
	return $params['message'];
}
*/

/*
* Hook: hooks_processGroupMessageAfter
* Description: action performed after sending the group message
* Params:
*		1. to
* 		2. message
*		3. styleStart
*		4. styleEnd
*		5. comet
*		6. channel
*		7. timestamp
* Returns: processed message
*/
/*
function hooks_processGroupMessageAfter($params){
	return $params['message'];
}
*/

/*
* Hook: hooks_guestLogin
* Description: customize guest login.
* Params:
*		1. guestname
* Returns: userid of guest
*/
/*
function hooks_guestLogin($params){
	return 0;
}
*/

/*
* Hook: hooks_forceFriendsAfter
* Description: forcefully add friends after buddylist is sorted
* Params: 1. buddyList
* Returns: customised buddylist
*/
/*
function hooks_forceFriendsAfter($params){
	$buddyList = array();
	return $buddyList;
}
*/

/*
* Hook: hooks_getBotIDs
* Description: gets custom bots
* Params: None
* Returns: ids of custom bots
*/
/*
function hooks_getBotIDs($params){
	$botids = array();
	return $botids;
}
*/

/*
* Hook: hooks_sendOptionalMessage
* Description: send optional messages instead of the default one in case of plugins.
* Params:
*		1. to
* 		2. plugin
* Returns: return 0 to also send the default message or return 1.
*/
/*
function hooks_sendOptionalMessage($params){
	return 0;
}
*/

/*
* Hook: hooks_groupHeartbeat
* Description: customises response of group heartbeat
* Params: None
* Returns: None
*/
/*
function hooks_groupHeartbeat($params){
}
*/

/*
* Hook: hooks_createGroup
* Description: action to be performed once group is created.
* Params: 1. chatroomid
* Returns: None
*/
/*
function hooks_createGroup($params){
}
*/

/*
* Hook: hooks_getGroupList
* Description: to fetch list of groups.
* Params: 1. sqlPart
* Returns: customised query for fetching groups.
*/
/*
function hooks_getGroupList($params){
	return '';
}
*/
/*
* Hook: hooks_getJoinedGroupList
* Description: to fetch list of joind groups.
* Params: 1. userid
* Returns: customised query for fetching joined groups.
*/
/*
function hooks_getJoinedGroupList($params){
	return '';
}
*/
/*
* Hook: hooks_getGroupByID
* Description: to fetch list of joind groups.
* Params: 1. group Id
* Returns: customised query for fetching groups details.
*/
/*
function hooks_getGroupByID($params){
	return '';
}
*/

/*
* Hook: hooks_getGroupUserIds
* Description: to fetch userids joined mebmers.
* Params: 1. group Id
* Returns: customised query for groups users.
*/
/*
function hooks_getGroupUserIds($params){
	return '';
}
*/
