<?php

/*

CometChat
Copyright (c) 2016 Inscripts
License: https://www.cometchat.com/legal/license

*/


$sql_queries = array();

$sql_queries['setNames'] = "SET NAMES utf8";
$sql_queries['setCharacter'] = "SET CHARACTER SET utf8";
$sql_queries['setCollationConnection'] = "SET COLLATION_CONNECTION = 'utf8_general_ci'";
$sql_queries['getMaxID'] = "select max({field}) as id from {tablename}";
$sql_queries['cometchat_settings'] = "select * from cometchat_settings";
$sql_queries['admin_getAnnouncements'] = "select id,announcement,time,[to] from cometchat_announcements where [to] = 0 or [to] = '-1'  order by id desc";
$sql_queries['admin_deleteAnnouncements'] = "delete from cometchat_announcements where id = '{id}'";
$sql_queries['admin_insertAnnouncements'] = "insert into cometchat_announcements (announcement,time,[to]) values ('{announcement}', '{time}','{to}')";
$sql_queries['admin_getBotId'] = "select id from [cometchat_bots] where [apikey] = '{apikey}'";
$sql_queries['admin_addBot'] = "insert into [cometchat_bots] ([name], [description], [avatar], [apikey],[keywords]) values('{name}','{description}','{avatar}','{apikey}',' ')";
$sql_queries['admin_removeBot'] = "delete from [cometchat_bots] where [id] = '{id}'";
$sql_queries['admin_getBotData'] = "select * from [cometchat_bots] where [id] = '{id}'";
$sql_queries['admin_rebuildBot'] = "update [cometchat_bots] set [name]='{name}', [description]='{description}',[avatar]='{avatar}' where id='{id}'";
$sql_queries['admin_getGroups'] = "select * from cometchat_chatrooms order by name asc";
$sql_queries['admin_deleteGroup'] = "delete from cometchat_chatrooms where id = '{id}'";
$sql_queries['admin_deleteGroup_messages'] = "delete from cometchat_chatroommessages where chatroomid = '{id}'";
$sql_queries['admin_createGroup'] = "insert into cometchat_chatrooms (name,createdby,createdon,lastactivity,password,type) values ('{name}', '0','{lastactivity}','{createdon}','{password}','{type}')";
$sql_queries['admin_ccautocomplete'] = "select TOP 10 {usertable_userid} id, {usertable_username} username from {usertable} where {usertable_username} LIKE '%{username}%'";
$sql_queries['admin_searchgrouplogs'] = "select {usertable_userid} id, {usertable_username} username from {usertable} where {usertable_username} LIKE '%{username}%'";

/*START: Report queries*/
$sql_queries['checkMessage'] = "select TOP 1 id from cometchat";
$sql_queries['admin_getlast24hoursMessageCount'] = "select (select count(id) from cometchat where sent >= '{sent}') + (select count(id) from cometchat_chatroommessages where sent >= '{sent}') as totalmessages";

$sql_queries['admin_getlast30daysMessageCount'] = "select (((select count(id) from cometchat where sent >= '{sent}') + (select count(id) from cometchat_archive where sent >= '{sent}')) + ((select count(id) from cometchat_chatroommessages where sent >= '{sent}') + (select count(id) from cometchat_chatroommessages_archive where sent >= '{sent}'))) as totalmessages";

$sql_queries['admin_getAllMessageCount'] = "select (((select count(id) from cometchat) + (select count(id) from cometchat_archive)) + ((select count(id) from cometchat_chatroommessages) + (select count(id) from cometchat_chatroommessages_archive))) as totalmessages";

$sql_queries['admin_getlast24hoursActiveGuestsCount'] = "select (select count(userid) from cometchat_status where userid >{firstguestid} and lastactivity >= '{sent}') as activeguests";
$sql_queries['admin_getlast30daysActiveGuestsCount'] = "select (select count(userid) from cometchat_status where userid >{firstguestid} and lastactivity >= '{sent}') as activeguests";
$sql_queries['admin_getAllGuestsCount'] = "select (select count(id) from cometchat_guests) + (select count(id) from cometchat_guests_archive) as activeguests";

$sql_queries['admin_getActiveUsersCount'] = "select count(userid) as activeusers from cometchat_status where userid <{firstguestid} and lastactivity >= '{sent}'";
/*END: Report queries*/


$sql_queries['admin_getMessageCount'] = "select (select count(id) from cometchat where sent >= '{sent}') + (select count(id) from cometchat_chatroommessages where sent >= '{sent}') as totalmessages";
$sql_queries['admin_onlineusers'] = "select count(*) as users from (select DISTINCT cometchat.[from] userid from cometchat where cometchat.sent > {sent}-300 UNION SELECT DISTINCT cometchat_chatroommessages.userid userid FROM cometchat_chatroommessages WHERE cometchat_chatroommessages.sent > {sent}-300) as x";
$sql_queries['admin_getAllMessageCount'] = "select (select count(id) from cometchat) + (select count(id) from cometchat_chatroommessages) as totalmessages";
$sql_queries['admin_getLanguageCode'] = "select distinct [code] from [cometchat_languages]";
$sql_queries['admin_removeLanguage'] = "delete from [cometchat_languages] where [code] = '{code}'";
$sql_queries['admin_getLanguage'] = "select * from [cometchat_languages] where [code] = '{code}' order by [type] asc, [name] asc";
$sql_queries['admin_importLanguage'] = "IF ( EXISTS (SELECT * FROM [cometchat_languages] WHERE [lang_key] = '{lang_key}')) begin UPDATE [cometchat_languages] set [lang_text] = '{lang_text}',[code] = '{code}',[type] = '{type}',[name] = '{name}' WHERE [lang_key] = '{lang_key}' end else begin INSERT cometchat_languages ([lang_key], [lang_text], [code], [type], [name]) VALUES ('{lang_key}','{lang_text}','{code}', '{type}', '{name}') end; ";
$sql_queries['admin_addnewcolor'] = "insert into [cometchat_colors]([color_key],[color_value],[color]) values ('{color_key}','{color_value}','{color_key}')";
$sql_queries['admin_removecolor'] = "delete from [cometchat_colors] where [color] = '{color}'";
$sql_queries['admin_searchlogs'] = "(select {usertable_userid} id, {usertable_username} username from {usertable} where {usertable_username} LIKE '%{username}%' or {usertable_userid} = '{userid}') {guestpart} ";
$sql_queries['admin_searchlogs_guestpart'] = " union (select cometchat_guests.id, '{guestnamePrefix}' + cometchat_guests.name username from cometchat_guests where cometchat_guests.name LIKE '%{username}%' or cometchat_guests.id = '{userid}')";
$sql_queries['admin_viewusername'] = "select {usertable_username} username from {usertable} where {usertable_userid} = '{userid}'";
$sql_queries['admin_viewguestusername'] = "select '{guestnamePrefix}' + name username from cometchat_guests where cometchat_guests.id = '{userid}'";
$sql_queries['admin_viewuser_guestpart'] = " union (select distinct(f.id) id, '{guestnamePrefix}'+f.name username  from cometchat m1, cometchat_guests f where (f.id = m1.[from] and m1.[to] = '{userid}') or (f.id = m1.[to] and m1.[from] = '{userid}'))";

$sql_queries['admin_viewuser'] = "(select distinct(f.{usertable_userid}) id, f.{usertable_username} username from cometchat m1, {usertable} f where (f.{usertable_userid} = m1.[from] and m1.[to] = '{userid}') or (f.{usertable_userid} = m1.[to] and m1.[from] = '{userid}')) {guestpart} order by username asc";
$sql_queries['admin_viewuserconversation'] = "select {usertable_username} username from {usertable} where {usertable_userid} = '{userid}'";
$sql_queries['admin_viewguestconversation'] = "select '{guestnamePrefix}' + name username from cometchat_guests where cometchat_guests.id = '{userid}'";
$sql_queries['admin_viewconversation'] = "(select m.*  from cometchat m where  (m.[from] = '{userid}' and m.[to] = '{userid2}') or (m.[to] = '{userid}' and m.[from] = '{userid2}')) order by id desc";
$sql_queries['admin_groupLog'] = "select * from cometchat_chatrooms order by lastactivity desc";
$sql_queries['admin_groupName'] = "select name chatroomname from cometchat_chatrooms where id = '{id}'";
$sql_queries['admin_viewchatroomconversation_usertable'] = "(select {usertable_userid}, {usertable_username} from {usertable} union select id {usertable_userid}, '{guestnamePrefix}' + name {usertable_username} from cometchat_guests)";
$sql_queries['admin_viewuserchatroomconversation'] = "select TOP 200 cometchat_chatroommessages.*, f.{usertable_username} username  from cometchat_chatroommessages join {usertable} f on cometchat_chatroommessages.userid = f.{usertable_userid} where chatroomid = '{chatroomid}' order by id desc";

$sql_queries['admin_monitor_guestpart'] = "UNION (select cometchat.id id, cometchat.[from], cometchat.[to], cometchat.message, cometchat.sent, cometchat.[read],'{guestnamePrefix}'+f.name fromu, '{guestnamePrefix}'+t.name tou from cometchat, cometchat_guests f, cometchat_guests t where {criteria} f.id = cometchat.[from] and t.id = cometchat.[to]) UNION (select cometchat.id id, cometchat.[from], cometchat.[to], cometchat.message, cometchat.sent, cometchat.[read], f.{usertable_username} fromu, '{guestnamePrefix}'+t.name tou from cometchat, {usertable} f, cometchat_guests t where {criteria} f.{usertable_userid} = cometchat.[from] and t.id = cometchat.[to]) UNION (select cometchat.id id, cometchat.[from], cometchat.[to], cometchat.message, cometchat.sent, cometchat.[read], '{guestnamePrefix}'+f.name fromu, t.{usertable_username} tou from cometchat, cometchat_guests f, {usertable} t where {criteria} f.id = cometchat.[from] and t.{usertable_userid} = cometchat.[to]) ";

$sql_queries['admin_monitor_desc'] = " ";
$sql_queries['admin_monitor_criteria2'] = " TOP 20 ";
$sql_queries['admin_monitor'] = "(select {criteria2} cometchat.id id, cometchat.[from], cometchat.[to], cometchat.message, cometchat.sent, cometchat.[read], f.{usertable_username} fromu, t.{usertable_username} tou from cometchat, {usertable} f, {usertable} t where {criteria} f.{usertable_userid} = cometchat.[from] and t.{usertable_userid} = cometchat.[to] ) {guestpart} order by id desc";
$sql_queries['admin_searchlogs'] = "select {usertable_userid} id, {usertable_username} username from {usertable} where {usertable_username} LIKE '%{username}%'";
$sql_queries['admin_updateauthmode'] = "truncate table [cometchat];truncate table cometchat_block;truncate table cometchat_chatroommessages;truncate table cometchat_chatrooms;truncate table cometchat_chatrooms_users;truncate table cometchat_status;IF  NOT EXISTS (SELECT * FROM sys.objects WHERE object_id = OBJECT_ID(N'[dbo].[cometchat_users]') AND type in (N'U')) BEGIN CREATE TABLE [cometchat_users] ([userid] int NOT NULL IDENTITY(1,1) PRIMARY KEY,[username] varchar(100) NOT NULL UNIQUE,[displayname] varchar(100) NOT NULL,[password] varchar(100) NOT NULL,[avatar] varchar(200) NOT NULL,[link] varchar(200) NOT NULL,[grp] varchar(25) NOT NULL,[friends] varchar(25) NOT NULL,[uid] varchar(255) NOT NULL) END;";
$sql_queries['admin_configeditor'] = "IF (EXISTS (SELECT * FROM cometchat_settings WHERE setting_key = '{name}'))            begin UPDATE cometchat_settings SET value ='{value}' , key_type='{key_type}' WHERE setting_key ='{name}' end            else begin INSERT into cometchat_settings (setting_key,value, key_type)  values ('{name}','{value}','{key_type}') end";
$sql_queries['admin_languageeditor'] = "IF ( EXISTS (SELECT * FROM [cometchat_languages] WHERE [lang_key] = '{lang_key}')) begin UPDATE [cometchat_languages] set [lang_text] = '{lang_text}',[code] = '{code}',[type] = '{type}',[name] = '{name}' WHERE [lang_key] = '{lang_key}' end else begin INSERT cometchat_languages ([lang_key], [lang_text], [code], [type], [name]) VALUES ('{lang_key}','{lang_text}','{code}', '{type}', '{name}') end";
$sql_queries['admin_coloreditor'] = "IF (EXISTS (SELECT * FROM cometchat_colors WHERE color_key = '{name}')) begin UPDATE cometchat_colors SET color_value ='{value}' , color='{color_name}' WHERE setting_key ='{name}' end else begin INSERT into cometchat_colors (setting_key,value, key_type) values ('{name}','{value}','{color_name}') end";
$sql_queries['getDefaultColor'] = "select * from [cometchat_colors] where [color] = '{color}'";
$sql_queries['getParentColor'] = "select [color_value] from [cometchat_colors] where [color] = '{color}' and [color_key] = 'parentColor'";
$sql_queries['setNewColorValue'] = "select [color_key],[color_value] from [cometchat_colors] where [color] = '{color}'";
$sql_queries['getLanguageVar'] = "select [code],[type],[name],[lang_key],[lang_text] from [cometchat_languages] order by [type] asc, [name] asc";
$sql_queries['getColorVars'] = "select [color_key],[color_value],[color] from [cometchat_colors]";
$sql_queries['getBotList'] = "select * from [cometchat_bots]";
$sql_queries['getBlockedUserIDs_subquery'] = "select fromid as blockedid from cometchat_block where toid = '{userid}' UNION ";
$sql_queries['getBlockedUserIDs_send'] = "select (select CAST((blockedid) as varchar)+',' from ({querystring} SELECT toid blockedid FROM dbo.cometchat_block WHERE fromid = '{userid}') blockid for xml path('')) as blockedids";
$sql_queries['getBlockedUserIDs_receive'] = "select (SELECT CAST((toid) as varchar)+',' FROM cometchat_block WHERE fromid = '{userid}' for xml path('')) as blockedids";
$sql_queries['getPrevMessages_condition'] = " and (cometchat.id < '{id}') ";
$sql_queries['getPrevMessages'] = "select TOP {prelimit} * from cometchat where ((cometchat.[from] = '{from}' and cometchat.[to] = '{to}' and direction <>1) or ( cometchat.[from] = '{fromid}' and cometchat.[to] = '{toid}' and direction <>2 )) and cometchat.direction <> 3 {condition} order by cometchat.id desc;";
$sql_queries['getChatboxData'] = "select TOP {prelimit} * from cometchat where ((cometchat.[from] = '{from}' and cometchat.[to] = '{to}' and direction <>1) or ( cometchat.[from] = '{fromid}' and cometchat.[to] = '{toid}' and direction <>2 )) and cometchat.direction <> 3 order by cometchat.id desc;";
$sql_queries['getChatboxData_prependcondition'] = " and (cometchat.id < {id}) ";
$sql_queries['getChatboxData_prepend'] = "select TOP {prelimit} * from cometchat where ((cometchat.[from] = '{from}' and cometchat.[to] = '{to}' and direction <>1) or ( cometchat.[from] = '{fromid}' and cometchat.[to] = '{toid}' and direction <>2 )) {prepend} and cometchat.direction <> 3 order by cometchat.id desc;";
$sql_queries['getChatroomData_limitclause'] = " TOP {lastMessages} ";
$sql_queries['getChatroomData_prependcondition'] = " and (cometchat_chatroommessages.id < '{id}')";
$sql_queries['getChatroomData_guestpart'] = " UNION select {limitClause} cometchat_chatroommessages.id id, cometchat_chatroommessages.message, cometchat_chatroommessages.sent, '{guestnamePrefix}' + m.name [from],
cometchat_chatroommessages.userid fromid, m.id userid from cometchat_chatroommessages join cometchat_guests m on m.id = cometchat_chatroommessages.userid where cometchat_chatroommessages.chatroomid = '{chatroomid}' and cometchat_chatroommessages.message not like '%banned_%' and cometchat_chatroommessages.message not like '%kicked_%' and cometchat_chatroommessages.message not like '%deletemessage_%'";
$sql_queries['getChatroomData'] = "select TOP {limit} cometchat_chatroommessages.id id, cometchat_chatroommessages.message, cometchat_chatroommessages.sent, m.{usertable_username} [from],
cometchat_chatroommessages.userid fromid, m.{usertable_userid} userid from cometchat_chatroommessages join {usertable} m on m.{usertable_userid} = cometchat_chatroommessages.userid where cometchat_chatroommessages.chatroomid = '{chatroomid}' and cometchat_chatroommessages.message not like '%banned_%' and cometchat_chatroommessages.message not like '%kicked_%' and cometchat_chatroommessages.message not like '%deletemessage_%' {prependCondition} {guestpart} order by id desc";
$sql_queries['getChatroomDetails'] = "select * from cometchat_chatrooms where cometchat_chatrooms.id = '{id}'";

$sql_queries['getGuestID'] = "select id from cometchat_guests where id = '{id}'";
$sql_queries['getChatroomBannedGuests'] = "select cometchat_guests.id userid, '{guestnamePrefix}' + cometchat_guests.name username, '' link, '' avatar, cometchat_status.lastactivity lastactivity, cometchat_status.isdevice isdevice, cometchat_status.status, cometchat_status.message from cometchat_guests left join cometchat_status on cometchat_guests.id = cometchat_status.userid inner join cometchat_chatrooms_users on cometchat_guests.id =  cometchat_chatrooms_users.userid where chatroomid = '{chatroomid}' and cometchat_chatrooms_users.isbanned = 1 Union {originalsql}";
$sql_queries['getGuestDetails'] = "select cometchat_guests.id userid, '{guestnamePrefix}' + cometchat_guests.name username,  '' link,  '' avatar, cometchat_status.lastactivity lastactivity, cometchat_status.lastseen lastseen, cometchat_status.lastseensetting lastseensetting, cometchat_status.status, cometchat_status.message, cometchat_status.isdevice isdevice, cometchat_status.readreceiptsetting readreceiptsetting
from cometchat_guests left join cometchat_status on cometchat_guests.id = cometchat_status.userid where cometchat_guests.id = '{userid}'";
$sql_queries['receive_sqlpart1'] = " ([from] = {from} and [id] <= {id}) OR";
$sql_queries['getRecentGuestDetails'] = "select cometchat_guests.id userid, '{guestnamePrefix}' + cometchat_guests.name username, '' avatar from cometchat_guests where cometchat_guests.id in ({recentbuddyids}) UNION {sqlpart}";
$sql_queries['getRecentGroupMessages'] = "select * from cometchat_chatroommessages where id in (select max(id) from cometchat_chatroommessages {sqlpart} group by chatroomid)";
$sql_queries['getRecentGroupDetails'] = "select cometchat_chatrooms.id id, cometchat_chatrooms.name name from cometchat_chatrooms where cometchat_chatrooms.id in ({joinedrooms})";
$sql_queries['fetchMessages'] = "select cometchat.id, cometchat.[from], cometchat.[to], cometchat.message, cometchat.sent, cometchat.[read], cometchat.direction from cometchat where ((cometchat.[to] = '{userid}' and cometchat.direction <> 2) or (cometchat.[from] = '{userid}' and cometchat.direction <> 1)) and (cometchat.id > '{timestamp}') and cometchat.direction <> 3 order by cometchat.id";
$sql_queries['fetchunreadMessages'] = "select cometchat.id, cometchat.[from], cometchat.[to], cometchat.message, cometchat.sent, cometchat.[read], cometchat.direction from cometchat where cometchat.[to] = '{userid}' and cometchat.[read] <> 1 and cometchat.direction < 2 order by cometchat.id";
$sql_queries['typingTo'] = "select (select CAST((userid) as varchar)+',' from cometchat_status where typingto = '{userid}' and ('{timestamp}'-typingtime < 10))";
$sql_queries['getAnnouncementCount'] = "select count(id) as count from cometchat_announcements where [to] = '{userid}' and  [recd] = '0'";
$sql_queries['checkAnnoucements'] = "select TOP 1 id,announcement,time from cometchat_announcements where [to] = '{userid}' and  [recd] = '0' order by id desc";
$sql_queries['getAnnoucements'] = "select TOP 1 id,announcement an,time t from cometchat_announcements where [to] = '0' or [to] = '-1' order by id desc";
$sql_queries['cometchatSessionRead'] = "select session_data from cometchat_session where session_id = '{session_id}'";
$sql_queries['getFriends'] = "select friends from [cometchat_users] where uid = '{uid}'";
$sql_queries['api_getData'] = "select {fetchfield} from cometchat_users where {fieldname} = '{value}'";
$sql_queries['api_authenticateUser'] = "select userid from cometchat_users where username = '{username}' and password = '{password}'";
$sql_queries['announcement_datifyextra'] = "or [to] = '0' or [to] = '{userid}'";
$sql_queries['announcement_datify'] = "select TOP {limitClause} id,announcement,time,[to] from cometchat_announcements where [to] = '-1' {extra} order by id desc";
$sql_queries['getChatrooms'] = "select id,name,type from cometchat_chatrooms where name = '{name}'";
$sql_queries['getChatroomById'] = "select * from cometchat_chatrooms where id ='{id}'";
$sql_queries['getUserIdByChatroom'] = "select userid from cometchat_chatroommessages where id ='{id}'";
$sql_queries['getChatroom'] = "select TOP 1 id,name,type from cometchat_chatrooms where id = '{id}' and (type = '0' or type='3')";
$sql_queries['getJoinedGroups'] = "select distinct chatroomid as id from cometchat_chatrooms_users where userid = '{userid}' and isbanned <> 1";
$sql_queries['groups_sqlpart'] = "(select COUNT(cometchat_chatrooms_users.userid) members from cometchat_chatrooms_users left join cometchat_status on cometchat_chatrooms_users.userid = cometchat_status.userid where cometchat_chatrooms_users.chatroomid = cometchat_chatrooms.id and isbanned <> 1 {timestampCondition})";
$sql_queries['getGroupsData'] = "select DISTINCT cometchat_chatrooms.id, cometchat_chatrooms.name, cometchat_chatrooms.type, cometchat_chatrooms.password, cometchat_chatrooms.lastactivity, cometchat_chatrooms.invitedusers, cometchat_chatrooms.createdby, {sqlpart} members from cometchat_chatrooms order by name asc";
$sql_queries['getGroupMsgMaxIds'] = "select max(cometchat_chatroommessages.id) id, cometchat_chatroommessages.chatroomid from cometchat_chatroommessages where cometchat_chatroommessages.chatroomid IN ({implodedChatrooms}) group by cometchat_chatroommessages.chatroomid";
$sql_queries['getGroupPassword'] = "select password from cometchat_chatrooms where id = '{currentroom}'";
$sql_queries['group_timestampcondition1'] = " (cometchat_chatroommessages.chatroomid = '{chatroomid}' and cometchat_chatroommessages.id > '{id}') or";
$sql_queries['group_timestampcondition2'] = " (cometchat_chatroommessages.chatroomid = '{chatroomid}' and cometchat_chatroommessages.id > '{id}') or";
$sql_queries['group_timestampcondition3'] = " cometchat_chatroommessages.chatroomid in({joinedrooms}) and cometchat_chatroommessages.id > '{id}' and ";
$sql_queries['group_timestampcondition4'] = "cometchat_chatroommessages.chatroomid in({joinedrooms}) and ";
$sql_queries['groups_guestpart_limitClause'] =  " TOP {limit}";
$sql_queries['groups_guestpart'] = " UNION select {limitClause} cometchat_chatroommessages.id id, CAST(cometchat_chatroommessages.message as varchar(1024)) message, cometchat_chatroommessages.chatroomid, cometchat_chatroommessages.sent, '{guestnamePrefix}' + m.name [from], cometchat_chatroommessages.userid fromid, m.id userid from cometchat_chatroommessages join cometchat_guests m on cometchat_chatroommessages.userid = m.id where {timestampCondition} cometchat_chatroommessages.message not like 'banned_%' and cometchat_chatroommessages.message not like 'kicked_%' and cometchat_chatroommessages.message not like 'deletemessage_%' ";
$sql_queries['getGroupName'] = "select name from cometchat_chatrooms where name = '{name}'";
$sql_queries['checkchatroombanneduser'] = "select * from cometchat_chatrooms_users where userid ='{userid}' and chatroomid = '{chatroomid}' and isbanned = '1'";
$sql_queries['getchatroombannedusers'] = "select (SELECT CAST((cometchat_chatrooms_users.userid) as varchar)+',' FROM cometchat_chatrooms_users WHERE isbanned=1 and chatroomid='{chatroomid}')";
$sql_queries['getChatroomUserIDs'] = "select userid chatroomusers from cometchat_chatrooms_users where isbanned=0 and chatroomid='{chatroomid}'";
$sql_queries['getBlockedUsers_guestpart'] = " UNION (select distinct(m.id) [id], '{guestnamePrefix}' + m.name [name] , '' avatar from cometchat_block, cometchat_guests m where m.id = toid and fromid = '{fromid}')";
$sql_queries['getBlockedUsers'] = "(select distinct({usertable}.{usertable_userid}) [id], {usertable}.{usertable_username} [name], {avatarfield} avatar from cometchat_block, {usertable} {avatartable} where {usertable}.{usertable_userid} = toid and fromid = '{userid}') {guestpart}";

$sql_queries['groupchathistory_guestpart'] = " UNION select id as userid, '{guestnamePrefix}'+name as username from cometchat_guests";

$sql_queries['groupchathistory'] = "select cometchat_chatroommessages.id, cometchat_chatroommessages.message, cometchat_chatroommessages.sent, usertable.username as fromu from (
		select {usertable_userid} as userid, {usertable_username} as username from {usertable}
		{guestpart}
	) as usertable left join cometchat_chatroommessages on usertable.userid = cometchat_chatroommessages.userid
	where
	cometchat_chatroommessages.id in
	(
	select min(id) from cometchat_chatroommessages where cometchat_chatroommessages.message NOT LIKE '%banned%'
	    AND     cometchat_chatroommessages.message NOT LIKE '%kicked%'
	    AND     cometchat_chatroommessages.message NOT LIKE '%deletemessage%'
	    AND 	cometchat_chatroommessages.chatroomid = '{chatroomid}'
	group by Floor(cometchat_chatroommessages.sent/86400)
	)";

$sql_queries['chathistory_guestpart'] = "UNION select id as userid, '{guestnamePrefix}'+name as username from cometchat_guests";

$sql_queries['chathistory'] = "	select cometchat.[id], cometchat.message, cometchat.[sent], cometchat.[read], cometchat.[sent], fromusertable.[username] as fromu ,tousertable.[username] as tou from cometchat
	left join
	(	select {usertable_userid} as userid, {usertable_username} as username from {usertable} {guestpart}
	) as fromusertable on fromusertable.userid = cometchat.[from]
	left join (
		select {usertable_userid} as userid, {usertable_username} as username from {usertable} {guestpart}
	) as tousertable on tousertable.userid = cometchat.[to]
	where
	cometchat.id in
	(	select min(id) from cometchat where ([from] = '{userid}' AND [to] = '{chatroomid}' AND direction <> 1 ) OR ([to] = '{userid}' AND [from] = '{chatroomid}' AND direction <> 2 ) AND direction <> 3 group by Floor(cometchat.sent/86400)
	)";

$sql_queries['chatroomviewlog_guestpart'] = "union (select m1.id, m1.userid, m1.chatroomid, m1.sent, CONVERT(VARCHAR(MAX), m1.message) message, m2.name chatroom, '{guestnamePrefix}' + f.name fromu from cometchat_chatroommessages m1, cometchat_chatrooms m2, cometchat_guests f where  f.id = m1.userid and m1.chatroomid=m2.id and m1.chatroomid={chatroomid} and m1.id >= {id} and m1.message not like 'CC^CONTROL_deletemessage_%')";
$sql_queries['chatroomviewlog'] = "(select TOP {limit} m1.id, m1.userid, m1.chatroomid, m1.sent, CONVERT(VARCHAR(MAX), m1.message) message, m2.name chatroom, f.{usertable_username} fromu from cometchat_chatroommessages m1, cometchat_chatrooms m2, {usertable} f where  f.{usertable_userid} = m1.userid and m1.chatroomid=m2.id and m1.chatroomid='{chatroomid}' and m1.id >= {id} and m1.message not like '%banned%' and m1.message not like '%kicked%' and m1.message not like '%deletemessage%') {guestpart} order by id";

$sql_queries['viewlog_guestpart'] = "union (select m1.id, m1.[from], m1.[to], m1.sent, CONVERT(VARCHAR(MAX), m1.message),m1.[read],m1.direction, '{guestnamePrefix}' + f.name fromu, '{guestnamePrefix}' + t.name tou from cometchat m1, cometchat_guests f, cometchat_guests t where f.id = m1.[from] and t.id = m1.[to] and ((m1.[from] = '{userid}' and m1.[to] = '{chatroomid}' and m1.direction <> 1) or (m1.[to] = '{userid}' and m1.[from] = '{chatroomid}' and m1.direction <> 2)) and m1.id >= {id} and m1.direction <> 3) union (select select m1.id, m1.[from], m1.[to], m1.sent, CONVERT(VARCHAR(MAX), m1.message),m1.[read],m1.direction, '{guestnamePrefix}' + f.name fromu, t.{usertable_username} tou from cometchat m1, cometchat_guests f, {usertable} t where f.id = m1.[from] and t.{usertable_userid} = m1.[to] and ((m1.[from] = '{userid}' and m1.[to] = '{chatroomid}' and m1.direction <> 1) or (m1.[to] = '{userid}' and m1.[from] = '{chatroomid}' and m1.direction <> 2)) and m1.id >= {id} and m1.direction <> 3) union (select m1.id, m1.[from], m1.[to], m1.sent, CONVERT(VARCHAR(MAX), m1.message),m1.[read],m1.direction, f.{usertable_username} fromu, '{guestnamePrefix}' + t.name tou from cometchat m1, {usertable} f, cometchat_guests t where f.{usertable_userid} = m1.[from] and t.id = m1.[to] and ((m1.[from] = '{userid}' and m1.[to] = '{chatroomid}'and m1.direction <> 1) or (m1.[to] = '{userid}' and m1.[from] = '{chatroomid}' and m1.direction <> 2)) and m1.id >= {id} and m1.direction <> 3)";
$sql_queries['viewlog'] = "(select TOP {limit} select m1.id, m1.[from], m1.[to], m1.sent, CONVERT(VARCHAR(MAX), m1.message),m1.[read],m1.direction, f.{usertable_username} fromu, t.{usertable_username} tou from cometchat m1, {usertable} f, {usertable} t  where  f.{usertable_userid} = m1.[from] and t.{usertable_userid} = m1.[to] and ((m1.[from] = '{userid}' and m1.[to] = '{chatroomid}' and m1.direction <> 1) or (m1.[to] = '{userid}' and m1.[from] = '{chatroomid}' and m1.direction <> 2)) and m1.id >= {id} and m1.direction <> 3) {guestpart} order by id";
$sql_queries['getGroup'] = "select guid from cometchat_chatrooms where guid = '{guid}'";
$sql_queries['getGroupId'] = "select id from cometchat_chatrooms where guid = '{guid}' or id = '{guid}'";
$sql_queries['checkUserExists'] = "select userid as count from cometchat_users where {field} = '{value}' ";
$sql_queries['checkGroupUserExists'] = "select userid as userid from cometchat_chatrooms_users where chatroomid = '{chatroomid}' and userid = '{userid}' ";
$sql_queries['addGroupUser'] = "insert into cometchat_chatrooms_users (chatroomid,userid) values ('{chatroomid}', '{userid}') ";
$sql_queries['deleteGroupUser'] = "delete from cometchat_chatrooms_users where  chatroomid = '{chatroomid}' and userid = '{userid}' ";
$sql_queries['deleteGroupApi'] = "delete from cometchat_chatrooms where guid = '{guid}' ";
$sql_queries['deleteGroupUserApi'] = "delete from cometchat_chatrooms_users where chatroomid = '{chatroomid}' ";
$sql_queries['setBaseUrl'] = "IF ( EXISTS (SELECT * FROM [cometchat_settings] WHERE setting_key = 'BASE_URL')) begin UPDATE [cometchat_settings] set value = '{baseurl}' , key_type = '0' WHERE setting_key = 'BASE_URL' end else begin INSERT cometchat_settings ([setting_key], [value], [key_type]) VALUES ('BASE_URL','{baseurl}','0') end";
$sql_queries['insertMessage'] = "insert into cometchat (cometchat.[from],cometchat.[to],cometchat.message,cometchat.sent,cometchat.[read], cometchat.direction) values ('{userid}', '{to}',N'{message}','{timestamp}','{old}','{dir}')";
$sql_queries['insertRecentConversation'] = "IF ( EXISTS (SELECT * FROM [cometchat_recentconversation] WHERE cometchat_recentconversation.[convo_id] = '{convo_hash}')) begin UPDATE [cometchat_recentconversation] set cometchat_recentconversation.[from] = '{userid}',cometchat_recentconversation.[to] = '{to}',cometchat_recentconversation.[message] = '{message}',cometchat_recentconversation.[id] = '{insertedid}', cometchat_recentconversation.[sent] = '{timestamp}'end else begin INSERT cometchat_recentconversation (cometchat_recentconversation.[id], cometchat_recentconversation.[from], cometchat_recentconversation.[to], cometchat_recentconversation.[message], cometchat_recentconversation.[sent], cometchat_recentconversation.[convo_id]) VALUES ('{insertedid}', '{userid}', '{to}','{message}','{timestamp}','{convo_hash}') end";
$sql_queries['insertBroadcastMessages'] = "insert into cometchat (cometchat.[from],cometchat.[to],cometchat.message,cometchat.sent,cometchat.[read], cometchat.direction) values {sqlpart}";
$sql_queries['insertGroupMessage'] = "insert into cometchat_chatroommessages (userid,chatroomid,message,sent) values ('{userid}', '{to}','{styleStart}{message}{styleEnd}','{timestamp}')";
$sql_queries['insertAnnouncement'] = "insert into cometchat_announcements (announcement,time,[to]) values ('{announcement}', '{time}','{to}')";
$sql_queries['updateLastActivity'] = "IF ( EXISTS (SELECT * FROM [cometchat_status] WHERE cometchat_status.[userid] = '{userid}')) begin UPDATE [cometchat_status] set lastactivity = '{timestamp}',lastseen = '{timestamp}' WHERE cometchat_status.[userid] = '{userid}' end else begin INSERT cometchat_status (userid,lastactivity,lastseen) values ('{userid}','{timestamp}','{timestamp}') end";
$sql_queries['setLastseensettings'] = "IF ( EXISTS (SELECT * FROM [cometchat_status] WHERE cometchat_status.[userid] = '{userid}')) begin UPDATE [cometchat_status] set lastseensetting = '{message}' end else begin INSERT cometchat_status (userid,lastseensetting) values ('{userid}','{message}') end";
$sql_queries['setReadReceiptsettings'] = "IF ( EXISTS (SELECT * FROM [cometchat_status] WHERE cometchat_status.[userid] = '{userid}')) begin UPDATE [cometchat_status] set readreceiptsetting = '{message}' end else begin INSERT cometchat_status (userid,readreceiptsetting) values ('{userid}','{message}') end";
$sql_queries['setStatus'] = "IF ( EXISTS (SELECT * FROM [cometchat_status] WHERE cometchat_status.[userid] = '{userid}')) begin UPDATE [cometchat_status] set status = '{message}' WHERE cometchat_status.[userid] = '{userid}' end else begin INSERT cometchat_status (userid,status) values ('{userid}','{message}') end";
$sql_queries['insertGuest'] = "insert into cometchat_guests (name) values ('{name}')";
$sql_queries['insertStatus'] = "IF ( EXISTS (SELECT * FROM [cometchat_status] WHERE cometchat_status.[userid] = '{userid}')) begin UPDATE [cometchat_status] set isdevice = '1' end else begin INSERT cometchat_status (userid,isdevice) values ('{userid}','1') end";
$sql_queries['insertIsTyping'] = "IF ( EXISTS (SELECT * FROM [cometchat_status] WHERE cometchat_status.[userid] = '{userid}')) begin UPDATE [cometchat_status] set typingto = '{typingto}', typingtime = '{typingtime}' end else begin INSERT cometchat_status (userid,typingto,typingtime) values ('{userid}','{typingto}','{typingtime}') end";
$sql_queries['insertCometStatus'] = "IF ( EXISTS (SELECT * FROM [cometchat_status] WHERE cometchat_status.[userid] = {userid})) begin UPDATE [cometchat_status] set status = '{message}' WHERE cometchat_status.[userid] = {userid} end else begin INSERT cometchat_status (userid,status) values ('{userid}','{message}') end";
$sql_queries['insertStatusMessage'] = "IF ( EXISTS (SELECT * FROM [cometchat_status] WHERE cometchat_status.[userid] = {userid})) begin UPDATE [cometchat_status] set message = '{message}' WHERE cometchat_status.[userid] = {userid} end else begin INSERT cometchat_status (userid,message) values ('{userid}','{message}') end";
$sql_queries['cometchatSessionOpen'] = "IF ( EXISTS (SELECT * FROM [cometchat_session] WHERE cometchat_session.[session_id] = '{session_id}')) begin UPDATE [cometchat_session] set session_lastaccesstime = GETDATE() end else begin INSERT cometchat_session (session_id,session_lastaccesstime) values ('{session_id}',GETDATE()) end";
$sql_queries['cometchatSessionWrite'] = "IF ( EXISTS (SELECT * FROM [cometchat_session] WHERE cometchat_session.[session_id] = '{session_id}')) begin UPDATE [cometchat_session] set session_data = '{session_data}' end else begin INSERT cometchat_session (session_id,session_data) values ('{session_id}','{session_data}') end";
$sql_queries['insertFriends'] = "insert into [cometchat_users] (friends) values('{friends}')";
$sql_queries['api_createuser'] = "insert into cometchat_users (username,password,displayname,link,grp) values ('{username}','{password}','{displayname}','{link}','{grp}')";
$sql_queries['api_creategroup'] = "insert into cometchat_chatrooms (name,createdby,lastactivity,password,type,guid) values ('{name}', '{createdby}','{lastactivity}','{password}','{type}','{guid}')";
$sql_queries['api_creategroupuser'] = "IF ( EXISTS (SELECT * FROM [cometchat_chatrooms_users] WHERE cometchat_chatrooms_users.[userid] = {userid} AND cometchat_chatrooms_users.[chatroomid] = {chatroomid}))
begin UPDATE [cometchat_chatrooms_users] set chatroomid = '{chatroomid}' end else begin INSERT cometchat_chatrooms_users (userid,chatroomid) values ('{userid}','{chatroomid}') end";
$sql_queries['insertBot'] = "insert into [cometchat_bots]([name], [description], [avatar], [apikey],[keywords]) values('{name}','{description}','{avatar}','{apikey}','{keywords}')";
$sql_queries['insertChatroom'] = "insert into cometchat_chatrooms (name,createdby,lastactivity,createdon,password,type) values ('{name}','{createdby}','{lastactivity}','{createdon}','{password}','{type}')";
$sql_queries['insertChatroomUser'] = "IF ( EXISTS (SELECT * FROM [cometchat_chatrooms_users] WHERE cometchat_chatrooms_users.[userid] = {userid} AND cometchat_chatrooms_users.[chatroomid] = {chatroomid} )) begin UPDATE [cometchat_chatrooms_users] set chatroomid = '{chatroomid}' end else begin INSERT [cometchat_chatrooms_users](userid,chatroomid) values('{userid}','{chatroomid}') end";
$sql_queries['unbanChatroomUser'] = "IF ( NOT EXISTS (SELECT * FROM [cometchat_chatrooms_users] WHERE cometchat_chatrooms_users.[userid] = {userid} AND cometchat_chatrooms_users.[chatroomid] = {chatroomid} )) begin INSERT [cometchat_chatrooms_users](userid,chatroomid,isbanned) values('{userid}','{chatroomid}','0') end";
$sql_queries['blockUser'] = "insert into cometchat_block (fromid, toid) values ('{fromid}','{toid}')";
$sql_queries['insertFirstGuestID'] = "insert into [cometchat_guests] ([id], [name]) VALUES ('{id}', 'guest-{id}');";
$sql_queries['getTblDetails'] = "SELECT * from {table} where {key}={value};";
$sql_queries['updateGroupActivity'] = "update cometchat_chatrooms set lastactivity = '{lastactivity}' where id = '{id}'";
$sql_queries['cometchatdelete_sql1'] = "update cometchat set cometchat.direction = 1 where cometchat.[from] = {from} and cometchat.direction = 0 and cometchat.[to] = {to}";
$sql_queries['cometchatdelete_sql2'] = "update cometchat set cometchat.direction = 2 where cometchat.[from] = {from} and cometchat.direction = 0 and cometchat.[to] = {to}";
$sql_queries['cometchatdelete_sql3'] = "update cometchat set cometchat.direction = 3  where cometchat.direction = 1 and cometchat.[from]={from} and cometchat.[to] = {to}";
$sql_queries['cometchatdelete_sql4'] = "update cometchat set cometchat.direction = 3 where cometchat.direction = 2 and cometchat.[from]={from} and cometchat.[to] = {to}";
$sql_queries['mobileapp_logout'] = "update [cometchat_status] set isdevice = '0' where userid = {userid}";
$sql_queries['updateReadMessages'] = "update cometchat set [read] = '1' where [to]= '{to}' and ({sqlpart}) and [read] = '0'";
$sql_queries['updateFetchMessages'] = "update cometchat set cometchat.[read] = '1' where cometchat.[to] = '{to}' and cometchat.id <= '{id}'";
$sql_queries['updateAnnoucements'] = "update cometchat_announcements set [recd] = '1' where [id] <= '{id}' and [to]  = '{userid}'";
$sql_queries['checkGuestName'] = "select name from cometchat_guests where name = '{name}'";
$sql_queries['updateGuestName'] = "update [cometchat_guests] set name='{name}' where id='{id}'";
$sql_queries['updateFriends'] = "update [cometchat_users] set friends = '{friends}' where uid = '{uid}'";
$sql_queries['cloudapi_updatestatus'] = "update [cometchat_status] set {set} where [userid] = '{userid}'";
$sql_queries['api_updateuser'] = "update cometchat_users set {fieldname} = '{value}' where userid = '{userid}'";
$sql_queries['renameGroup'] = "update cometchat_chatrooms set name = '{name}' where id = '{id}'";
$sql_queries['banUser'] = "update cometchat_chatrooms_users set isbanned = '1' where userid = '{userid}' and chatroomid = '{chatroomid}'";
$sql_queries['addUsersToChatroom'] = "update cometchat_chatrooms set invitedusers = '{invitedusers}' where id='{id}'";
$sql_queries['cometchatSessionDestroy'] = "delete from cometchat_session where session_id = '{session_id}'";

$sql_queries['cometchatSessionGarbageCollector'] = "delete from cometchat_session where session_lastaccesstime < DATE_SUB(NOW(), INTERVAL {lifetime} SECOND)";

$sql_queries['api_removeuser'] = "delete from [cometchat_users] where [userid] = '{userid}'";
$sql_queries['check_group'] = "select name, guid from cometchat_chatrooms where name = '{groupname}'";
$sql_queries['cron_groups'] = "delete from cometchat_chatrooms where createdby <> 0 and lastactivity < ({lastactivity}- {timeout})";
$sql_queries['cron_groupmessages'] = "delete from cometchat_chatroommessages where sent < ({sent}-10800)";
$sql_queries['cron_groupusers'] = "delete from cometchat_chatrooms_users where lastactivity < ({lastactivity}-3600)";
$sql_queries['deleteGroup'] = "delete from cometchat_chatrooms where id = '{id}' and createdby != 0 ";
$sql_queries['deleteGroup_messages'] = "delete from cometchat_chatroommessages where chatroomid = '{id}'";
$sql_queries['deleteKickedMessage'] = "delete from cometchat_chatroommessages where chatroomid = '{chatroomid}' and (message like '%kicked_{userid}')";
$sql_queries['leavechatroom'] = "delete from cometchat_chatrooms_users where userid = '{userid}' and chatroomid = '{chatroomid}' and isbanned != 1";
$sql_queries['kickUser'] = "delete from cometchat_chatrooms_users where userid = '{userid}' and chatroomid = '{chatroomid}'";
$sql_queries['deleteBanUserMessage'] = "delete from cometchat_chatroommessages where chatroomid = '{chatroomid}' and (message like '%banned_{userid}')";
$sql_queries['unbanusers'] = "delete from cometchat_chatrooms_users where userid = '{userid}' and chatroomid = '{chatroomid}'";
$sql_queries['deleteGroupMessage'] = "delete from cometchat_chatroommessages where id='{id}'";
$sql_queries['unblockUser'] = "delete from cometchat_block where toid = '{toid}' and fromid = '{fromid}'";


/*START: Graph Queries*/
$sql_queries['today_messages'] = "";

$sql_queries['weekly_privateMessages'] = "";

$sql_queries['weekly_groupMessages'] = "";

$sql_queries['monthly_Messages'] = "";

$sql_queries['all_Messages'] = "";

$sql_queries['weekly_active_users'] = "";

$sql_queries['monthly_active_users'] = "";

$sql_queries['all_active_users'] = "";

$sql_queries['all_groups'] = "";

/*END: Graph Queries*/


/*Install Query*/
$sql_queries['install_cometchatsettings'] = "select [value] from [cometchat_settings] where [setting_key] like '{setting_key}'";
$sql_queries['install_updateextensions'] = "update [cometchat_settings] set [value]='{value}' WHERE [setting_key] like '{setting_key}';";
$sql_queries['install_showtablestatus'] = "exec sp_help '{table_prefix}{db_usertable}'";
$sql_queries['install_showfullcolumns'] = "show FULL columns from {table_prefix}{db_usertable} where field = '{db_usertable_name}'";
$sql_queries['install_collateguests'] = "alter table cometchat_guests default collate {table_co}";
$sql_queries['install_charsetguests'] = "alter table cometchat_guests convert to character set {field_cs} collate {field_co}";
$sql_queries['install_statusfullcolumns'] = "SHOW FULL COLUMNS FROM `cometchat_status` WHERE field = 'isdevice' or field = 'lastactivity'";
$sql_queries['install_renamestatus'] = "RENAME TABLE `cometchat_status` to `cometchat_status_old`";
$sql_queries['install_insertstatus'] = "INSERT INTO `cometchat_status` (`userid`, `message`, `status`, `typingto`, `typingtime`, `isdevice`, `lastactivity`, `lastseen`, `lastseensetting`) SELECT *, NULL, NULL from `cometchat_status_old`";
$sql_queries['install_insertbaseurl'] = "insert into cometchat_settings(`setting_key`,`value`,`key_type`) values('BASE_URL','{baseurl}','0') on duplicate key update `value` = '{baseurl}'";
$sql_queries['install_insertapikey'] = "insert into cometchat_settings(`setting_key`,`value`,`key_type`) values('apikey','{apikey}','1') on duplicate key update `value` = '{apikey}'";
$sql_queries['limitclause'] = "TOP {limit}";

$sql_queries['update_clearconversation'] = "UPDATE
        cometchat
    SET
        cometchat.direction =
        (
            CASE
                WHEN
                    (cometchat.[from] ={userid} AND cometchat.direction = 0 AND cometchat.[to] = {to})
                THEN
                    1
                WHEN
                    (cometchat.[from] = {to} AND cometchat.direction = 0 AND cometchat.[to] = {userid})
                THEN
                    2
                WHEN
                    (cometchat.direction = 1 AND cometchat.[from]={to} AND cometchat.[to] = {userid})
                THEN
                    3
                WHEN
                    (cometchat.direction = 2 AND cometchat.[from]={userid} AND cometchat.[to] = {to})
                THEN
                    3
                ELSE
                	cometchat.direction
            END
        )";

$sql_queries['alter_cometchat_chatrooms_users'] = "ALTER TABLE cometchat_chatrooms_users ADD COLUMN isbanned int(1) default 0;";

$sql_queries['cometchat_chatrooms_users'] = "CREATE TABLE IF NOT EXISTS `cometchat_chatrooms_users` (`userid` int(10) unsigned NOT NULL,`chatroomid` int(10) unsigned NOT NULL, PRIMARY KEY  USING BTREE (`userid`,`chatroomid`), `isbanned` int(1) default 0, KEY `chatroomid` (`chatroomid`), KEY `userid` (`userid`), KEY `userid_chatroomid` (`chatroomid`,`userid`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

$sql_queries['install_content'] = <<<EOD
RENAME TABLE `cometchat` to `{cometchat_messages_old}`;

CREATE TABLE  `cometchat` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `from` int(10) unsigned NOT NULL,
  `to` int(10) unsigned NOT NULL,
  `message` text NOT NULL,
  `sent` int(10) unsigned NOT NULL default '0',
  `read` tinyint(1) unsigned NOT NULL default '0',
  `direction` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `to` (`to`),
  KEY `from` (`from`),
  KEY `direction` (`direction`),
  KEY `read` (`read`),
  KEY `sent` (`sent`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `cometchat_announcements` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `announcement` text NOT NULL,
  `time` int(10) unsigned NOT NULL,
  `to` int(10) NOT NULL,
  `recd` int(1) NOT NULL DEFAULT 0,

  PRIMARY KEY  (`id`),
  KEY `to` (`to`),
  KEY `time` (`time`),
  KEY `to_id` (`to`,`id`)
) ENGINE=InnoDB AUTO_INCREMENT = 5000 DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `cometchat_chatroommessages` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `userid` int(10) unsigned NOT NULL,
  `chatroomid` int(10) unsigned NOT NULL,
  `message` text NOT NULL,
  `sent` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `userid` (`userid`),
  KEY `chatroomid` (`chatroomid`),
  KEY `sent` (`sent`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `cometchat_chatrooms` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `lastactivity` int(10) unsigned NOT NULL,
  `createdby` int(10) unsigned NOT NULL,
  `password` varchar(255) NOT NULL,
  `type` tinyint(1) unsigned NOT NULL,
  `vidsession` varchar(512) default NULL,
  `invitedusers` varchar(512) default NULL,
  PRIMARY KEY  (`id`),
  KEY `lastactivity` (`lastactivity`),
  KEY `createdby` (`createdby`),
  KEY `type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `cometchat_chatrooms`
add column(
`invitedusers` varchar(512) default NULL
);

CREATE TABLE IF NOT EXISTS `cometchat_status` (
  `userid` int(10) unsigned NOT NULL,
  `message` text,
  `status` enum('available','away','busy','invisible','offline') default NULL,
  `typingto` int(10) unsigned default NULL,
  `typingtime` int(10) unsigned default NULL,
  `isdevice` int(1) unsigned NOT NULL default '0',
  `lastactivity` int(10) unsigned NOT NULL default '0',
  `lastseen` int(10) unsigned NOT NULL default '0',
  `lastseensetting` int(1) unsigned NOT NULL default '0',
  `readreceiptsetting` int(1) unsigned NOT NULL default '1',
  PRIMARY KEY  (`userid`),
  KEY `typingto` (`typingto`),
  KEY `typingtime` (`typingtime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `cometchat_status`
add column(
`lastseen` int(10) unsigned NOT NULL default '0',
`lastseensetting` int(1) unsigned NOT NULL default '0'
);

ALTER TABLE `cometchat_status`
add column(
`readreceiptsetting` int(1) unsigned NOT NULL default '1'
);

CREATE TABLE IF NOT EXISTS `cometchat_videochatsessions` (
  `username` varchar(255) NOT NULL,
  `identity` varchar(255) NOT NULL,
  `timestamp` int(10) unsigned default 0,
  PRIMARY KEY  (`username`),
  KEY `username` (`username`),
  KEY `identity` (`identity`),
  KEY `timestamp` (`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `cometchat_block` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `fromid` int(10) unsigned NOT NULL,
  `toid` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `fromid` (`fromid`),
  KEY `toid` (`toid`),
  KEY `fromid_toid` (`fromid`,`toid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `cometchat_guests` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10000001 DEFAULT CHARSET=utf8;

INSERT INTO `cometchat_guests` (`id`, `name`) VALUES ('10000000', 'guest-10000000');

CREATE TABLE IF NOT EXISTS `cometchat_session` (
  `session_id` char(32) NOT NULL,
  `session_data` text NOT NULL,
  `session_lastaccesstime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `cometchat_settings` (
  `setting_key` varchar(50) COLLATE utf8_unicode_ci NOT NULL COMMENT 'Configuration setting name. It can be PHP constant, variable or array',
  `value` text COLLATE utf8_unicode_ci NOT NULL COMMENT 'Value of the key.',
  `key_type` tinyint(4) NOT NULL DEFAULT '1' COMMENT 'States whether the key is: 0 = PHP constant, 1 = atomic variable or 2 = serialized associative array.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Stores all the configuration settings for CometChat';

ALTER TABLE `cometchat_settings`
  ADD PRIMARY KEY (`setting_key`);

INSERT INTO `cometchat_settings` set `setting_key` = 'extensions_core', `value` = 'a:4:{s:3:"ads";s:14:"Advertisements";s:9:"mobileapp";s:9:"Mobileapp";s:7:"desktop";s:7:"Desktop";s:4:"bots";s:4:"Bots";}', `key_type` = 2 on duplicate key update `value` = 'a:5:{s:3:"ads";s:14:"Advertisements";s:6:"jabber";s:10:"Gtalk Chat";s:9:"mobileapp";s:9:"Mobileapp";s:7:"desktop";s:7:"Desktop";s:4:"bots";s:4:"Bots";}';

INSERT INTO `cometchat_settings` set `setting_key` = 'plugins_core', `value` = 'a:17:{s:9:"audiochat";a:2:{i:0;s:10:"Audio Chat";i:1;i:0;}s:6:"avchat";a:2:{i:0;s:16:"Audio/Video Chat";i:1;i:0;}s:5:"block";a:2:{i:0;s:10:"Block User";i:1;i:1;}s:9:"broadcast";a:2:{i:0;s:21:"Audio/Video Broadcast";i:1;i:0;}s:11:"chathistory";a:2:{i:0;s:12:"Chat History";i:1;i:0;}s:17:"clearconversation";a:2:{i:0;s:18:"Clear Conversation";i:1;i:0;}s:12:"filetransfer";a:2:{i:0;s:11:"Send a file";i:1;i:0;}s:9:"handwrite";a:2:{i:0;s:19:"Handwrite a message";i:1;i:0;}s:6:"report";a:2:{i:0;s:19:"Report Conversation";i:1;i:1;}s:4:"save";a:2:{i:0;s:17:"Save Conversation";i:1;i:0;}s:11:"screenshare";a:2:{i:0;s:13:"Screensharing";i:1;i:0;}s:7:"smilies";a:2:{i:0;s:7:"Smilies";i:1;i:0;}s:8:"stickers";a:2:{i:0;s:8:"Stickers";i:1;i:0;}s:5:"style";a:2:{i:0;s:15:"Color your text";i:1;i:2;}s:13:"transliterate";a:2:{i:0;s:13:"Transliterate";i:1;i:0;}s:10:"whiteboard";a:2:{i:0;s:10:"Whiteboard";i:1;i:0;}s:10:"writeboard";a:2:{i:0;s:10:"Writeboard";i:1;i:0;}}', `key_type` = 2 on duplicate key update `value` = 'a:17:{s:9:"audiochat";a:2:{i:0;s:10:"Audio Chat";i:1;i:0;}s:6:"avchat";a:2:{i:0;s:16:"Audio/Video Chat";i:1;i:0;}s:5:"block";a:2:{i:0;s:10:"Block User";i:1;i:1;}s:9:"broadcast";a:2:{i:0;s:21:"Audio/Video Broadcast";i:1;i:0;}s:11:"chathistory";a:2:{i:0;s:12:"Chat History";i:1;i:0;}s:17:"clearconversation";a:2:{i:0;s:18:"Clear Conversation";i:1;i:0;}s:12:"filetransfer";a:2:{i:0;s:11:"Send a file";i:1;i:0;}s:9:"handwrite";a:2:{i:0;s:19:"Handwrite a message";i:1;i:0;}s:6:"report";a:2:{i:0;s:19:"Report Conversation";i:1;i:1;}s:4:"save";a:2:{i:0;s:17:"Save Conversation";i:1;i:0;}s:11:"screenshare";a:2:{i:0;s:13:"Screensharing";i:1;i:0;}s:7:"smilies";a:2:{i:0;s:7:"Smilies";i:1;i:0;}s:8:"stickers";a:2:{i:0;s:8:"Stickers";i:1;i:0;}s:5:"style";a:2:{i:0;s:15:"Color your text";i:1;i:2;}s:13:"transliterate";a:2:{i:0;s:13:"Transliterate";i:1;i:0;}s:10:"whiteboard";a:2:{i:0;s:10:"Whiteboard";i:1;i:0;}s:10:"writeboard";a:2:{i:0;s:10:"Writeboard";i:1;i:0;}}';

INSERT INTO `cometchat_settings` (`setting_key`, `value`, `key_type`) VALUES ('modules_core', 'a:11:{s:13:"announcements";a:9:{i:0;s:13:"announcements";i:1;s:13:"Announcements";i:2;s:31:"modules/announcements/index.php";i:3;s:6:"_popup";i:4;s:3:"280";i:5;s:3:"310";i:6;s:0:"";i:7;s:1:"1";i:8;s:0:"";}s:16:"broadcastmessage";a:9:{i:0;s:16:"broadcastmessage";i:1;s:17:"Broadcast Message";i:2;s:34:"modules/broadcastmessage/index.php";i:3;s:6:"_popup";i:4;s:3:"385";i:5;s:3:"300";i:6;s:0:"";i:7;s:1:"1";i:8;s:0:"";}s:9:"chatrooms";a:9:{i:0;s:9:"chatrooms";i:1;s:9:"Chatrooms";i:2;s:27:"modules/chatrooms/index.php";i:3;s:6:"_popup";i:4;s:3:"600";i:5;s:3:"300";i:6;s:0:"";i:7;s:1:"1";i:8;s:1:"1";}s:8:"facebook";a:9:{i:0;s:8:"facebook";i:1;s:17:"Facebook Fan Page";i:2;s:26:"modules/facebook/index.php";i:3;s:6:"_popup";i:4;s:3:"500";i:5;s:3:"460";i:6;s:0:"";i:7;s:1:"1";i:8;s:0:"";}s:5:"games";a:9:{i:0;s:5:"games";i:1;s:19:"Single Player Games";i:2;s:23:"modules/games/index.php";i:3;s:6:"_popup";i:4;s:3:"465";i:5;s:3:"300";i:6;s:0:"";i:7;s:1:"1";i:8;s:0:"";}s:4:"home";a:8:{i:0;s:4:"home";i:1;s:4:"Home";i:2;s:1:"/";i:3;s:0:"";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";i:7;s:0:"";}s:17:"realtimetranslate";a:9:{i:0;s:17:"realtimetranslate";i:1;s:23:"Translate Conversations";i:2;s:35:"modules/realtimetranslate/index.php";i:3;s:6:"_popup";i:4;s:3:"280";i:5;s:3:"310";i:6;s:0:"";i:7;s:1:"1";i:8;s:0:"";}s:11:"scrolltotop";a:8:{i:0;s:11:"scrolltotop";i:1;s:13:"Scroll To Top";i:2;s:40:"javascript:jqcc.cometchat.scrollToTop();";i:3;s:0:"";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";i:7;s:0:"";}s:5:"share";a:8:{i:0;s:5:"share";i:1;s:15:"Share This Page";i:2;s:23:"modules/share/index.php";i:3;s:6:"_popup";i:4;s:3:"350";i:5;s:2:"50";i:6;s:0:"";i:7;s:1:"1";}s:9:"translate";a:9:{i:0;s:9:"translate";i:1;s:19:"Translate This Page";i:2;s:27:"modules/translate/index.php";i:3;s:6:"_popup";i:4;s:3:"280";i:5;s:3:"310";i:6;s:0:"";i:7;s:1:"1";i:8;s:0:"";}s:7:"twitter";a:8:{i:0;s:7:"twitter";i:1;s:7:"Twitter";i:2;s:25:"modules/twitter/index.php";i:3;s:6:"_popup";i:4;s:3:"500";i:5;s:3:"300";i:6;s:0:"";i:7;s:1:"1";}}', 2);

CREATE TABLE IF NOT EXISTS `cometchat_languages` (
  `lang_key` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT 'Key of a language variable',
  `lang_text` text CHARACTER SET utf8 NOT NULL COMMENT 'Text/value of a language variable',
  `code` varchar(20) CHARACTER SET utf8 NOT NULL COMMENT 'Language code for e.g. en for English',
  `type` varchar(20) CHARACTER SET utf8 NOT NULL COMMENT 'Type of CometChat add on for e.g. module/plugin/extension/function',
  `name` varchar(50) CHARACTER SET utf8 NOT NULL COMMENT 'Name of add on for e.g. announcement,smilies, etc.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci COMMENT='Stores all CometChat languages';

ALTER TABLE `cometchat_languages`
  ADD UNIQUE KEY `lang_index` (`lang_key`,`code`,`type`,`name`) USING BTREE;

INSERT INTO `cometchat_languages` (`lang_key`, `lang_text`, `code`, `type`, `name`) VALUES ('rtl', '0', 'en', 'core', 'default');

CREATE TABLE IF NOT EXISTS `cometchat_colors` (
  `color_key` varchar(100) NOT NULL,
  `color_value` text NOT NULL,
  `color` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `cometchat_colors`
  ADD UNIQUE KEY `color_index` (`color_key`,`color`);

INSERT INTO `cometchat_colors` (`color_key`, `color_value`, `color`) VALUES
('color1', 'a:3:{s:7:"primary";s:6:"56a8e3";s:9:"secondary";s:6:"3777A7";s:5:"hover";s:6:"ECF5FB";}', 'color1'),
('color2', 'a:3:{s:7:"primary";s:6:"4DC5CE";s:9:"secondary";s:6:"068690";s:5:"hover";s:6:"D3EDEF";}', 'color2'),
('color3', 'a:3:{s:7:"primary";s:6:"FFC107";s:9:"secondary";s:6:"FFA000";s:5:"hover";s:6:"FFF8E2";}', 'color3'),
('color4', 'a:3:{s:7:"primary";s:6:"FB4556";s:9:"secondary";s:6:"BB091A";s:5:"hover";s:6:"F5C3C8";}', 'color4'),
('color5', 'a:3:{s:7:"primary";s:6:"DBA0C3";s:9:"secondary";s:6:"D87CB3";s:5:"hover";s:6:"ECD9E5";}', 'color5'),
('color6', 'a:3:{s:7:"primary";s:6:"3B5998";s:9:"secondary";s:6:"213A6D";s:5:"hover";s:6:"DFEAFF";}', 'color6'),
('color7', 'a:3:{s:7:"primary";s:6:"065E52";s:9:"secondary";s:6:"244C4E";s:5:"hover";s:6:"AFCCAF";}', 'color7'),
('color8', 'a:3:{s:7:"primary";s:6:"FF8A2E";s:9:"secondary";s:6:"CE610C";s:5:"hover";s:6:"FDD9BD";}', 'color8'),
('color9', 'a:3:{s:7:"primary";s:6:"E99090";s:9:"secondary";s:6:"B55353";s:5:"hover";s:6:"FDE8E8";}', 'color9'),
('color10', 'a:3:{s:7:"primary";s:6:"23025E";s:9:"secondary";s:6:"3D1F84";s:5:"hover";s:6:"E5D7FF";}', 'color10'),
('color11', 'a:3:{s:7:"primary";s:6:"24D4F6";s:9:"secondary";s:6:"059EBB";s:5:"hover";s:6:"DBF9FF";}', 'color11'),
('color12', 'a:3:{s:7:"primary";s:6:"289D57";s:9:"secondary";s:6:"09632D";s:5:"hover";s:6:"DDF9E8";}', 'color12'),
('color13', 'a:3:{s:7:"primary";s:6:"D9B197";s:9:"secondary";s:6:"C38B66";s:5:"hover";s:6:"FFF1E8";}', 'color13'),
('color14', 'a:3:{s:7:"primary";s:6:"FF67AB";s:9:"secondary";s:6:"D6387E";s:5:"hover";s:6:"F3DDE7";}', 'color14'),
('color15', 'a:3:{s:7:"primary";s:6:"8E24AA";s:9:"secondary";s:6:"7B1FA2";s:5:"hover";s:6:"EFE8FD";}', 'color15');

DELETE FROM `cometchat_colors` WHERE `color_key` NOT LIKE 'color%';

INSERT INTO `cometchat_settings` set `setting_key` = 'theme', `value` = 'docked', `key_type` = 1 on duplicate key update `value` = 'docked';

INSERT INTO `cometchat_settings` set `setting_key` = 'color', `value` = 'color1', `key_type` = 1 on duplicate key update `value` = 'color1';

CREATE TABLE IF NOT EXISTS `cometchat_users` (
  `userid` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `displayname` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `password` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `avatar` varchar(200) NOT NULL,
  `link` varchar(200) NOT NULL,
  `grp` varchar(25) NOT NULL,
  `friends` text NOT NULL,
  PRIMARY KEY (`userid`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `cometchat_bots` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `description` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `keywords` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  `avatar` varchar(200) NOT NULL,
  `apikey` varchar(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `cometchat_recentconversation` (
  `convo_id` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `id` int(10) unsigned NOT NULL,
  `from` int(10) unsigned NOT NULL,
  `to` int(10) unsigned NOT NULL,
  `message` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  `sent` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
  UNIQUE KEY `convo_id` (`convo_id`),
  KEY `fromid` (`from`),
  KEY `toid` (`to`),
  KEY `fromid_toid` (`from`,`to`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

{cometchat_chatrooms_users}

EOD;

$sql_queries['update_content_630'] = <<<EOD
			INSERT INTO [cometchat_colors] ([color_key], [color_value], [color]) VALUES
			('color1', 'a:3:{s:7:"primary";s:6:"56a8e3";s:9:"secondary";s:6:"3777A7";s:5:"hover";s:6:"ECF5FB";}', 'color1'),
			('color2', 'a:3:{s:7:"primary";s:6:"4DC5CE";s:9:"secondary";s:6:"068690";s:5:"hover";s:6:"D3EDEF";}', 'color2'),
			('color3', 'a:3:{s:7:"primary";s:6:"FFC107";s:9:"secondary";s:6:"FFA000";s:5:"hover";s:6:"FFF8E2";}', 'color3'),
			('color4', 'a:3:{s:7:"primary";s:6:"FB4556";s:9:"secondary";s:6:"BB091A";s:5:"hover";s:6:"F5C3C8";}', 'color4'),
			('color5', 'a:3:{s:7:"primary";s:6:"DBA0C3";s:9:"secondary";s:6:"D87CB3";s:5:"hover";s:6:"ECD9E5";}', 'color5'),
			('color6', 'a:3:{s:7:"primary";s:6:"3B5998";s:9:"secondary";s:6:"213A6D";s:5:"hover";s:6:"DFEAFF";}', 'color6'),
			('color7', 'a:3:{s:7:"primary";s:6:"065E52";s:9:"secondary";s:6:"244C4E";s:5:"hover";s:6:"AFCCAF";}', 'color7'),
			('color8', 'a:3:{s:7:"primary";s:6:"FF8A2E";s:9:"secondary";s:6:"CE610C";s:5:"hover";s:6:"FDD9BD";}', 'color8'),
			('color9', 'a:3:{s:7:"primary";s:6:"E99090";s:9:"secondary";s:6:"B55353";s:5:"hover";s:6:"FDE8E8";}', 'color9'),
			('color10', 'a:3:{s:7:"primary";s:6:"23025E";s:9:"secondary";s:6:"3D1F84";s:5:"hover";s:6:"E5D7FF";}', 'color10'),
			('color11', 'a:3:{s:7:"primary";s:6:"24D4F6";s:9:"secondary";s:6:"059EBB";s:5:"hover";s:6:"DBF9FF";}', 'color11'),
			('color12', 'a:3:{s:7:"primary";s:6:"289D57";s:9:"secondary";s:6:"09632D";s:5:"hover";s:6:"DDF9E8";}', 'color12'),
			('color13', 'a:3:{s:7:"primary";s:6:"D9B197";s:9:"secondary";s:6:"C38B66";s:5:"hover";s:6:"FFF1E8";}', 'color13'),
			('color14', 'a:3:{s:7:"primary";s:6:"FF67AB";s:9:"secondary";s:6:"D6387E";s:5:"hover";s:6:"F3DDE7";}', 'color14'),
			('color15', 'a:3:{s:7:"primary";s:6:"8E24AA";s:9:"secondary";s:6:"7B1FA2";s:5:"hover";s:6:"EFE8FD";}', 'color15');

			DELETE FROM [cometchat_colors] WHERE [color_key] NOT LIKE 'color%';
EOD;

$sql_queries['update_content_640'] = <<<EOD
			ALTER TABLE cometchat_status
			ADD readreceiptsetting int DEFAULT '1' NOT NULL;

			IF  NOT EXISTS (SELECT * FROM sys.objects
			  WHERE object_id = OBJECT_ID(N'[dbo].[cometchat_recentconversation]') AND type in (N'U'))
			BEGIN
			CREATE TABLE [cometchat_recentconversation] (
			  [convo_id] varchar(100) NOT NULL UNIQUE,
			  [id] int NOT NULL,
			  [from] int NOT NULL,
			  [to] int NOT NULL,
			  [message] text NOT NULL,
			  [sent] varchar(100) NOT NULL
			)
			END;

EOD;

$sql_queries['install_createstatus'] = "CREATE TABLE  IF NOT EXISTS `cometchat_status` (
                `userid` int(10) unsigned NOT NULL,
                `message` text,
                `status` enum('available','away','busy','invisible','offline') default NULL,
                `typingto` int(10) unsigned default NULL,
                `typingtime` int(10) unsigned default NULL,
                `isdevice` int(1) unsigned NOT NULL default '0',
                `lastactivity` int(10) unsigned NOT NULL default '0',
                `lastseen` int(10) unsigned NOT NULL default '0',
                `lastseensetting` int(1) unsigned NOT NULL default '0',
                PRIMARY KEY  (`userid`),
                KEY `typingto` (`typingto`),
                KEY `typingtime` (`typingtime`)
              ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";


class SqlQueries{
	function getQueries(){
		$sqlqueries = array();

	$sqlqueries['getRecentMessages'] = "select cometchat_recentconversation.* from cometchat_recentconversation join  " . TABLE_PREFIX . DB_USERTABLE . " on  " . TABLE_PREFIX . DB_USERTABLE . "." . DB_USERTABLE_USERID . " = cometchat_recentconversation.from join  " . TABLE_PREFIX . DB_USERTABLE . " a on  a." . DB_USERTABLE_USERID . " = cometchat_recentconversation.to where cometchat_recentconversation.to = '{userid}' or cometchat_recentconversation.from = '{userid}'";

		$sqlqueries['checkUserExists'] = "select ".DB_USERTABLE.".".DB_USERTABLE_USERID." as userid from ".TABLE_PREFIX.DB_USERTABLE." where {field} = '{value}'";
		$sql_queries['selectUser'] = "select userid, username, link, avatar, uid, friends, grp, displayname from ".TABLE_PREFIX.DB_USERTABLE." ";
		$sqlqueries['checkSocialLogin'] = "select ".DB_USERTABLE.".".DB_USERTABLE_USERID." from ".DB_USERTABLE." where ".DB_USERTABLE.".{db_usertable_username} = '{network_name}_{identifier}'";
		$sqlqueries['auth_getFriendsList'] = "select DISTINCT ".DB_USERTABLE.".".DB_USERTABLE_USERID." userid, ".DB_USERTABLE.".".DB_USERTABLE_NAME." username, ".DB_USERTABLE.".".DB_LINKFIELD." link, ".DB_AVATARFIELD." avatar, ".DB_USERTABLE.".{db_groupfield} grp, cometchat_status.lastactivity lastactivity, cometchat_status.lastseen lastseen, cometchat_status.lastseensetting lastseensetting, cometchat_status.status, cometchat_status.message, cometchat_status.isdevice from ".DB_USERTABLE." left join cometchat_status on ".DB_USERTABLE.".".DB_USERTABLE_USERID." = cometchat_status.userid ".DB_AVATARTABLE." where {timestampCondition} (cometchat_status.status IS NULL OR cometchat_status.status <> 'invisible' OR cometchat_status.status <> 'offline') order by username asc";
		$sqlqueries['auth_getUserDetails'] = "select ".DB_USERTABLE.".".DB_USERTABLE_USERID." userid, ".DB_USERTABLE.".".DB_USERTABLE_NAME." username,  ".DB_USERTABLE.".".DB_LINKFIELD." link, ".DB_AVATARFIELD." avatar, cometchat_status.lastactivity lastactivity, cometchat_status.lastseen lastseen, cometchat_status.lastseensetting lastseensetting, cometchat_status.status, cometchat_status.message, cometchat_status.isdevice from ".DB_USERTABLE." left join cometchat_status on ".DB_USERTABLE.".".DB_USERTABLE_USERID." = cometchat_status.userid ".DB_AVATARTABLE." where ".DB_USERTABLE.".".DB_USERTABLE_USERID." = '{userid}'";
		$sqlqueries['auth_getActivechatboxdetails'] = "select DISTINCT ".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_USERID." userid, ".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_NAME." username,  ".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_USERID." link, ".DB_AVATARFIELD." avatar,".DB_USERTABLE.".{db_groupfield} grp, cometchat_status.lastactivity lastactivity, cometchat_status.lastseen lastseen, cometchat_status.lastseensetting lastseensetting, cometchat_status.status, cometchat_status.message, cometchat_status.isdevice from ".TABLE_PREFIX.DB_USERTABLE." left join cometchat_status on ".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_USERID." = cometchat_status.userid ".DB_AVATARTABLE." where ".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_USERID." IN ({userids})";
		$sqlqueries['getGuestsList'] = "select DISTINCT cometchat_guests.id userid, '{guestnamePrefix}' + cometchat_guests.name username, '' link, '' avatar, cometchat_status.lastactivity lastactivity, cometchat_status.lastseen lastseen, cometchat_status.lastseensetting lastseensetting, cometchat_status.status, cometchat_status.message, cometchat_status.isdevice, cometchat_status.readreceiptsetting readreceiptsetting from cometchat_guests left join cometchat_status on cometchat_guests.id = cometchat_status.userid where (cometchat_status.lastactivity > {time}-".ONLINE_TIMEOUT."*2 OR cometchat_status.isdevice = 1) and (cometchat_status.status IS NULL OR cometchat_status.status <> 'invisible' OR cometchat_status.status <> 'offline')";
		$sqlqueries['getChatroomGuests'] = "select DISTINCT cometchat_guests.id userid, '{guestnamePrefix}' + cometchat_guests.name username, '' avatar, cometchat_status.lastactivity lastactivity, cometchat_status.isdevice isdevice, cometchat_chatrooms_users.isbanned, cometchat_status.status from cometchat_guests left join cometchat_status on cometchat_guests.id = cometchat_status.userid inner join cometchat_chatrooms_users on cometchat_guests.id = cometchat_chatrooms_users.userid where chatroomid = '{chatroomid}' and cometchat_status.lastactivity > {time}- ".ONLINE_TIMEOUT."*2 Union {originalsql}";
		$sqlqueries['getRecentUserDetails'] = "select ".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_USERID." userid, ".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_NAME." username, ".DB_AVATARFIELD." avatar from ".TABLE_PREFIX.DB_USERTABLE.DB_AVATARTABLE." where ".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_USERID." in ({recentbuddyids})";
		$sqlqueries['getUserData'] = "select ".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_USERID." userid,".TABLE_PREFIX.DB_USERTABLE.".uid uid, ".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_NAME." username, ".TABLE_PREFIX.DB_USERTABLE.".link link, ".DB_AVATARFIELD." avatar, cometchat_status.lastactivity lastactivity, cometchat_status.status, cometchat_status.message, cometchat_status.isdevice from ".TABLE_PREFIX.DB_USERTABLE." left join cometchat_status on ".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_USERID." = cometchat_status.userid ".DB_AVATARTABLE." where uid = '{uid}'";
		$sqlqueries['cloudapi_getData'] = "select {fetchfield} from ".TABLE_PREFIX.DB_USERTABLE." where {fieldname} = '{value}'";
		$sqlqueries['cloudapi_getUserDetails'] = "select [userid],[username],[link],[avatar],[displayname] from [".TABLE_PREFIX.DB_USERTABLE."] {where}";
		$sql_queries['loginuser'] = "select userid, username, link, avatar, uid, friends, grp, displayname from ".TABLE_PREFIX.DB_USERTABLE." where [username] = '{username}' and [password] = '{password}'";
		$sqlqueries['api_checkpassword'] = "select id, password from cometchat_users where id = {id}";
		$sqlqueries['groupMessages'] = "select cometchat_chatroommessages.id id, CAST(cometchat_chatroommessages.message as varchar(1024)) message, cometchat_chatroommessages.chatroomid, cometchat_chatroommessages.sent, m.".DB_USERTABLE_NAME." [from], cometchat_chatroommessages.userid fromid, m.".DB_USERTABLE_USERID." userid from cometchat_chatroommessages join ".TABLE_PREFIX.DB_USERTABLE." m on cometchat_chatroommessages.userid = m.".DB_USERTABLE_USERID." where {timestampCondition} cometchat_chatroommessages.message not like 'banned_%' and cometchat_chatroommessages.message not like 'kicked_%' and cometchat_chatroommessages.message not like 'deletemessage_%' {guestpart} order by id desc";
		$sqlqueries['getchatroomusers'] = "select DISTINCT ".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_USERID." userid, ".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_NAME." username, ".DB_AVATARFIELD." avatar, cometchat_status.lastactivity lastactivity, cometchat_status.isdevice isdevice, cometchat_chatrooms_users.isbanned, cometchat_status.status from ".TABLE_PREFIX.DB_USERTABLE." left join cometchat_status on ".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_USERID." = cometchat_status.userid inner join cometchat_chatrooms_users on  ".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_USERID." =  cometchat_chatrooms_users.userid ". DB_AVATARTABLE ." where chatroomid = '{chatroomid}' {timestampCondition} order by username asc";
		$sqlqueries['unban'] = "select DISTINCT ".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_USERID." userid, ".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_NAME." username, ".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_NAME." link, ".DB_AVATARFIELD." avatar, cometchat_status.lastactivity lastactivity, cometchat_status.isdevice isdevice, cometchat_status.status, cometchat_status.message from ".TABLE_PREFIX.DB_USERTABLE." left join cometchat_status on ".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_USERID." = cometchat_status.userid right join cometchat_chatrooms_users on ".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_USERID." =cometchat_chatrooms_users.userid ".DB_AVATARTABLE." where ".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_USERID." <> '{userid}' and cometchat_chatrooms_users.chatroomid = '{chatroomid}' and cometchat_chatrooms_users.isbanned ='1' order by username asc";
		$sqlqueries['insertSocialLogin'] = "insert into ".DB_USERTABLE." (".DB_USERTABLE.".{db_usertable_username},".DB_USERTABLE.".".DB_USERTABLE_NAME.",".DB_AVATARFIELD.",".DB_USERTABLE.".".DB_LINKFIELD.",".DB_USERTABLE.".{db_groupfield}) values ( '{network_name}_{identifier}','{firstName}','{photoURL}','{profileURL}','{groupfield}')";
		$sqlqueries['cloudapi_createuser'] = "insert into ".TABLE_PREFIX.DB_USERTABLE." set [username] = '{username}',[password] = '{password}', [link] = '{link}', [avatar] = '{avatar}', [displayname] = '{displayname}', [uid] = '{uid}'";
		$sqlqueries['loginWithUserDetails'] = "insert into ".TABLE_PREFIX.DB_USERTABLE." set [username] = '{username}', [link] = '{link}', [avatar] = '{avatar}', [displayname] = '{displayname}', [uid] = '{uid}'";
		$sqlqueries['updateSocialLogin'] = "update ".DB_USERTABLE." set ".DB_USERTABLE.".".DB_USERTABLE_NAME."='{firstName}',".DB_AVATARFIELD."='{photoURL}',".DB_USERTABLE.".".DB_LINKFIELD."='{profileURL}' where ".DB_USERTABLE.".{db_usertable_username}='{network_name}_{identifier}'";
		$sqlqueries['cloudapi_updateuser'] = "update ".TABLE_PREFIX.DB_USERTABLE." set {set} where [userid] = '{userid}'";
		$sqlqueries['cloudapi_updatefriends'] = "update ".TABLE_PREFIX.DB_USERTABLE." set [friends] = '{friends}' where [userid] = '{userid}'";
		$sqlqueries['update_loggedinuser'] = "update ".TABLE_PREFIX.DB_USERTABLE." set [uid] = '{uid}'  where [userid] = '{userid}'";
		$sqlqueries['cloudapi_removeuser'] = "delete from ".TABLE_PREFIX.DB_USERTABLE." where userid = '{userid}'";
		$sqlqueries['export_cometchat_messages'] = "select FROM_UNIXTIME(m.sent) as Time, fromuser.username as FromName, touser.username as ToName , m.message as Message from cometchat as m left join ( select ".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_USERID." as userid, ".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_NAME." as username from ".TABLE_PREFIX.DB_USERTABLE." union select id as userid, name as username from cometchat_guests ) as fromuser on fromuser.userid=m.from left join (
			select ".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_USERID." as userid, ".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_NAME." as username from ".TABLE_PREFIX.DB_USERTABLE." union select id as userid, name as username from cometchat_guests ) as touser on touser.userid=m.to where DATE(FROM_UNIXTIME(m.sent)) > (NOW() - INTERVAL 7 DAY)";
		$sqlqueries['export_cometchat_group_messages'] = "select FROM_UNIXTIME(m.sent) as Time, Grouptbl.name as GroupName, fromuser.username as FromName, m.message as Message from cometchat_chatroommessages as m left join ( select ".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_USERID." as userid, ".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_NAME." as username from ".TABLE_PREFIX.DB_USERTABLE." union select id as userid, name as username from cometchat_guests ) as fromuser on fromuser.userid=m.userid left join cometchat_chatrooms AS Grouptbl  ON m.chatroomid = Grouptbl.id where DATE(FROM_UNIXTIME(m.sent)) > (NOW() - INTERVAL 7 DAY)";

			/**
		START: Report New Optimize Queries
	*/
		$sqlqueries['get_users'] = "select count(".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_USERID.") as totalusers from ".TABLE_PREFIX.DB_USERTABLE;

		$sqlqueries['get_guest_users'] = "select count(cometchat_guests.id) as totalusers from cometchat_guests";

		$sqlqueries['admin_getActiveUsersCount'] = "select count(userid) as activeusers from cometchat_status where userid <{firstguestid} and lastactivity >= '{sent}'";

		$sqlqueries['admin_getlast24hoursActiveGuestsCount'] = "select (select count(userid) from cometchat_status where userid >{firstguestid} and lastactivity >= '{sent}') as activeguests";

		$sqlqueries['admin_getlast24hrsPrivateMessageCount'] = "select count(id) as messagecount from cometchat where sent >= '{sent}'";

		$sqlqueries['admin_getlast24hoursGroupMessageCount'] = "select count(id) as messagecount from cometchat_chatroommessages where sent >= '{sent}'";
		$sqlqueries['groupCreatedin24hrs'] = "select count(id) as groupCount from cometchat_chatrooms where createdon >= '{createdon}'";
		$sqlqueries['insert_report'] = "insert into [cometchat_report] ([timestamp_start], [total_no_of_users], [total_no_of_guest], [no_of_active_users_last_24_hrs], [no_of_active_guest_last_24_hrs], [no_of_messages_exchange_one_on_one_last_24_hrs], [no_of_messages_exchange_groupchat_last_24_hrs], [no_of_group_created_last_24_hrs]) VALUES ('{timestamp_start}', '{total_no_of_users}', '{total_no_of_guest}', '{no_of_active_users_last_24_hrs}', '{no_of_active_guest_last_24_hrs}', '{no_of_messages_exchange_one_on_one_last_24_hrs}', '{no_of_messages_exchange_groupchat_last_24_hrs}', '{no_of_group_created_last_24_hrs}')";

	/**
		END: Report New Optimize Queries
	*/


		return $sqlqueries;
	}
}
