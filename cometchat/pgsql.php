<?php

/*

CometChat
Copyright (c) 2016 Inscripts
License: https://www.cometchat.com/legal/license

*/


$sql_queries = array();

$sql_queries['setNames'] = "SET NAMES 'UTF8'";
$sql_queries['setCharacter'] = "SET CLIENT_ENCODING TO 'UTF8';";
$sql_queries['getMaxID'] = "select max({field}) from {tablename}";
$sql_queries['cometchat_settings'] = "select * from cometchat_settings";


$sql_queries['admin_getAnnouncements'] = "select id,announcement,time,'to' from cometchat_announcements where cometchat_announcements.to = 0 or cometchat_announcements.to = '-1'  order by id desc";


$sql_queries['admin_deleteAnnouncements'] = "delete from cometchat_announcements where id = '{id}'";


$sql_queries['admin_insertAnnouncements'] = "insert into cometchat_announcements (announcement,time,\"to\") values ('{announcement}', '{time}','{to}')";


$sql_queries['admin_getBotId'] = "select id from cometchat_bots where apikey = '{apikey}'";
$sql_queries['admin_addBot'] = "insert into cometchat_bots(name, description, avatar, apikey) values('{name}','{description}','{avatar}','{apikey}')";
$sql_queries['admin_removeBot'] = "delete from cometchat_bots where id = '{id}'";
$sql_queries['admin_getBotData'] = "select * from cometchat_bots where id = '{id}'";
$sql_queries['admin_rebuildBot'] = "update cometchat_bots set name='{name}', description='{description}',avatar='{avatar}' where id='{id}'";
$sql_queries['admin_getGroups'] = "select * from cometchat_chatrooms order by name asc";
$sql_queries['admin_deleteGroup'] = "delete from cometchat_chatrooms where id = '{id}'";
$sql_queries['admin_createGroup'] = "insert into cometchat_chatrooms (name,createdby,createdon,lastactivity,password,type) values ('{name}', '0','{lastactivity}','{createdon}','{password}','{type}')";
$sql_queries['admin_ccautocomplete'] = "select {usertable_userid} id, {usertable_username} username from {usertable} where {usertable_username} LIKE '%{username}%' limit 10";
$sql_queries['admin_searchgrouplogs'] = "select {usertable_userid} id, {usertable_username} username from {usertable} where {usertable_username} LIKE '%{username}%'";

/*START: Report queries*/
$sql_queries['checkMessage'] = "select id from cometchat LIMIT 1";
$sql_queries['admin_getlast24hoursMessageCount'] = "select (select count(id) from cometchat where sent >= '{sent}') + (select count(id) from cometchat_chatroommessages where sent >= '{sent}') as totalmessages";

$sql_queries['admin_getlast30daysMessageCount'] = "select (((select count(id) from cometchat where sent >= '{sent}') + (select count(id) from cometchat_archive where sent >= '{sent}')) + ((select count(id) from cometchat_chatroommessages where sent >= '{sent}') + (select count(id) from cometchat_chatroommessages_archive where sent >= '{sent}'))) as totalmessages";

$sql_queries['admin_getAllMessageCount'] = "select (((select count(id) from cometchat) + (select count(id) from cometchat_archive)) + ((select count(id) from cometchat_chatroommessages) + (select count(id) from cometchat_chatroommessages_archive))) as totalmessages";

$sql_queries['admin_getlast24hoursActiveGuestsCount'] = "select (select count(userid) from cometchat_status where userid >{firstguestid} and lastactivity >= '{sent}') as activeguests";
$sql_queries['admin_getlast30daysActiveGuestsCount'] = "select (select count(userid) from cometchat_status where userid >{firstguestid} and lastactivity >= '{sent}') as activeguests";
$sql_queries['admin_getAllGuestsCount'] = "select (select count(id) from cometchat_guests) + (select count(id) from cometchat_guests_archive) as activeguests";

$sql_queries['admin_getActiveUsersCount'] = "select count(userid) as activeusers from cometchat_status where userid <{firstguestid} and lastactivity >= '{sent}'";
/*END: Report queries*/


$sql_queries['admin_getMessageCount'] = "select (select count(id) from cometchat where sent >= '{sent}') + (select count(id) from cometchat_chatroommessages where sent >= '{sent}') as totalmessages";
$sql_queries['admin_onlineusers'] = "select count(*) as users from (select DISTINCT cometchat.from userid from cometchat where ('{sent}'-cometchat.sent)<300 UNION SELECT DISTINCT cometchat_chatroommessages.userid userid FROM cometchat_chatroommessages WHERE ('{sent}'-cometchat_chatroommessages.sent)<300) x";
$sql_queries['admin_getAllMessageCount'] = "select (select count(id) from cometchat) + (select count(id) from cometchat_chatroommessages) as totalmessages";
$sql_queries['admin_getLanguageCode'] = "select distinct code from cometchat_languages";
$sql_queries['admin_removeLanguage'] = "delete from cometchat_languages where code = '{code}'";
$sql_queries['admin_getLanguage'] = "select * from cometchat_languages where code = '{code}' order by type asc, name asc";


$sql_queries['admin_importLanguage'] = "WITH upsert AS (UPDATE cometchat_languages SET lang_key = '{lang_key}', lang_text = '{lang_text}', type = '{type}', name = '{name}' WHERE code = '{code}' RETURNING *) INSERT INTO cometchat_languages (lang_key, lang_text, code, type, name) SELECT '{lang_key}','{lang_text}','{code}','{type}','{name}' WHERE NOT EXISTS (SELECT * FROM upsert);";


$sql_queries['admin_addnewcolor'] = "insert into cometchat_colors(color_key,color_value,color) values ('{color_key}','{color_value}','{color_key}')";
$sql_queries['admin_removecolor'] = "delete from cometchat_colors where color = '{color}'";
$sql_queries['admin_searchlogs'] = "(select {usertable_userid} id, {usertable_username} username from {usertable} where {usertable_username} LIKE '%{username}%' or {usertable_userid} = '{userid}') {guestpart} ";
$sql_queries['admin_searchlogs_guestpart'] = " union (select cometchat_guests.id, concat('{guestnamePrefix}',cometchat_guests.name) username from cometchat_guests where cometchat_guests.name LIKE '%{username}%' or cometchat_guests.id = '{userid}')";
$sql_queries['admin_viewusername'] = "select {usertable_username} username from {usertable} where {usertable_userid} = '{userid}'";
$sql_queries['admin_viewguestusername'] = "select concat('{guestnamePrefix}',name) username from cometchat_guests where cometchat_guests.id = '{userid}'";
$sql_queries['admin_viewuser_guestpart'] = " union (select distinct(f.id) id, concat('{guestnamePrefix}',f.name) username  from cometchat m1, cometchat_guests f where (f.id = m1.from and m1.to = '{userid}') or (f.id = m1.to and m1.from = '{userid}'))";
$sql_queries['admin_viewuser'] = "(select distinct(f.{usertable_userid}) id, f.{usertable_username} username  from cometchat m1, {usertable} f where (f.{usertable_userid} = m1.from and m1.to = '{userid}') or (f.{usertable_userid} = m1.to and m1.from = '{userid}')) {guestpart} order by username asc";
$sql_queries['admin_viewuserconversation'] = "select {usertable_username} username from {usertable} where {usertable_userid} = '{userid}'";
$sql_queries['admin_viewguestconversation'] = "select concat('{guestnamePrefix}',name) username from cometchat_guests where cometchat_guests.id = '{userid}'";
$sql_queries['admin_viewconversation'] = "(select m.*  from cometchat m where  (m.from = '{userid}' and m.to = '{userid2}') or (m.to = '{userid}' and m.from = '{userid2}')) order by id desc";
$sql_queries['admin_groupLog'] = "select * from cometchat_chatrooms order by lastactivity desc";
$sql_queries['admin_groupName'] = "select name chatroomname from cometchat_chatrooms where id = '{id}'";
$sql_queries['admin_viewchatroomconversation_usertable'] = "(select {usertable_userid}, {usertable_username}  from {usertable} union select id {usertable_userid},concat('{guestnamePrefix}',name) \"{usertable_username}\" from cometchat_guests)";
$sql_queries['admin_viewuserchatroomconversation'] = "select cometchat_chatroommessages.*, f.{usertable_username} username  from cometchat_chatroommessages join {usertable} f on cometchat_chatroommessages.userid = f.{usertable_userid} where chatroomid = '{chatroomid}' order by id desc LIMIT 200";
$sql_queries['admin_monitor_guestpart'] = "UNION (select cometchat.id id, cometchat.from, cometchat.to, cometchat.message, cometchat.sent, cometchat.read,CONCAT('{guestnamePrefix}',f.name) fromu, CONCAT('{guestnamePrefix}',t.name) tou from cometchat, cometchat_guests f, cometchat_guests t where {criteria} f.id = cometchat.from and t.id = cometchat.to) UNION (select cometchat.id id, cometchat.from, cometchat.to, cometchat.message, cometchat.sent, cometchat.read, f.{usertable_username} fromu, CONCAT('{guestnamePrefix}',t.name) tou from cometchat, {usertable} f, cometchat_guests t where {criteria} f.{usertable_userid} = cometchat.from and t.id = cometchat.to) UNION (select cometchat.id id, cometchat.from, cometchat.to, cometchat.message, cometchat.sent, cometchat.read, CONCAT('{guestnamePrefix}',f.name) fromu, t.{usertable_username} tou from cometchat, cometchat_guests f, {usertable} t where {criteria} f.id = cometchat.from and t.{usertable_userid} = cometchat.to) ";
$sql_queries['admin_monitor'] = "(select cometchat.id id, cometchat.from, cometchat.to, cometchat.message, cometchat.sent, cometchat.read, f.{usertable_username} fromu, t.{usertable_username} tou from cometchat, {usertable} f, {usertable} t where {criteria} f.{usertable_userid} = cometchat.from and t.{usertable_userid} = cometchat.to ) {guestpart} order by id {criteria2}";
$sql_queries['admin_searchlogs'] = "select {usertable_userid} id, {usertable_username} username from {usertable} where {usertable_username} LIKE '%{username}%'";


$sql_queries['admin_updateauthmode'] = "
truncate table cometchat;
truncate table cometchat_block;
truncate table cometchat_chatroommessages;
truncate table cometchat_chatrooms;
truncate table cometchat_chatrooms_users;
truncate table cometchat_status;

CREATE TABLE IF NOT EXISTS cometchat_users (
  userid serial  NOT NULL ,
  username varchar(100) UNIQUE NOT NULL,
  displayname varchar(100)  NOT NULL,
  password varchar(100)  NOT NULL,
  avatar varchar(200) NOT NULL,
  link varchar(200) NOT NULL,
  grp varchar(25) NOT NULL,
  friends text NOT NULL,
  PRIMARY KEY (userid)
);";


$sql_queries['admin_configeditor_insertvalues'] = "('{name}', '{value}', {key_type}),";
$sql_queries['admin_configeditor'] = "insert into cometchat_settings (setting_key,value, key_type) values ('{name}','{value}','{key_type}') ON CONFLICT (setting_key) DO UPDATE set value=excluded.value, key_type = excluded.key_type";

$sql_queries['admin_languageeditor'] = "WITH upsert AS (UPDATE cometchat_languages SET lang_key = '{lang_key}', lang_text = '{lang_text}', code = '{code}', type = '{type}', name = '{name}' WHERE code = '{code}' RETURNING *) INSERT INTO cometchat_languages (lang_key , lang_text, code, type, name) SELECT '{lang_key}', '{lang_text}', '{code}', '{type}', '{name}' WHERE NOT EXISTS (SELECT * FROM upsert)";



$sql_queries['admin_coloreditor_insertvalues'] = "('{name}', '{value}', '{color_name}'),";
$sql_queries['admin_coloreditor'] = "replace into cometchat_colors (color_key,color_value, color) values {insertvalues}"; // no changes ***
$sql_queries['getDefaultColor'] = "select * from cometchat_colors where color = '{color}'";
$sql_queries['getParentColor'] = "select color_value from cometchat_colors where color = '{color}' and color_key = 'parentColor'";
$sql_queries['setNewColorValue'] = "select color_key,color_value from cometchat_colors where color = '{color}'";
$sql_queries['getLanguageVar'] = "select code,type,name,lang_key,lang_text from cometchat_languages order by type asc, name asc";
$sql_queries['getColorVars'] = "select color_key,color_value,color from cometchat_colors";
$sql_queries['getBotList'] = "select * from cometchat_bots";
$sql_queries['getBlockedUserIDs_subquery'] = "select fromid as blockedid from cometchat_block where toid = '{userid}' UNION ";


$sql_queries['getBlockedUserIDs_send'] = "select array_to_string(array_agg(blockedid),',') blockedids from ({querystring} select toid as blockedid from cometchat_block where fromid = '{userid}') as blocked";


$sql_queries['getBlockedUserIDs_receive'] = "select array_to_string(array_agg(toid),',') blockedids from cometchat_block where fromid = '{userid}'";


$sql_queries['getPrevMessages_condition'] = " and (cometchat.id < '{id}') ";
$sql_queries['getPrevMessages'] = "select * from cometchat where ((cometchat.from = '{from}' and cometchat.to = '{to}' and direction <>1) or ( cometchat.from = '{fromid}' and cometchat.to = '{toid}' and direction <>2 )) and cometchat.direction <> 3 {condition} order by cometchat.id desc limit {prelimit};";
$sql_queries['getChatboxData'] = "select * from cometchat where ((cometchat.from = {from} and cometchat.to = {to} and direction <>1) or ( cometchat.from = {fromid} and cometchat.to = {toid} and direction <>2 )) and cometchat.direction <> 3 order by cometchat.id desc {prelimit};";
$sql_queries['getChatboxData_prependcondition'] = " and (cometchat.id < {id}) ";
$sql_queries['getChatboxData_prepend'] = "select * from cometchat where ((cometchat.from = {from} and cometchat.to = {to} and direction <>1) or ( cometchat.from = {fromid} and cometchat.to = {toid} and direction <> 2)) {prepend} and cometchat.direction <> 3 order by cometchat.id desc limit {prelimit};";
$sql_queries['getChatroomData_limitclause'] = " limit {lastMessages} ";
$sql_queries['getChatroomData_prependcondition'] = " and (cometchat_chatroommessages.id < '{id}')";


$sql_queries['getChatroomData_guestpart'] = " UNION select DISTINCT cometchat_chatroommessages.id id, cometchat_chatroommessages.message, cometchat_chatroommessages.sent, CONCAT('{guestnamePrefix}',m.name) \"from\", cometchat_chatroommessages.userid fromid, m.id userid from cometchat_chatroommessages join cometchat_guests m on m.id = cometchat_chatroommessages.userid where cometchat_chatroommessages.chatroomid = '{chatroomid}' and cometchat_chatroommessages.message not like '%banned_%' and cometchat_chatroommessages.message not like '%kicked_%' and cometchat_chatroommessages.message not like '%deletemessage_%' {prependCondition}";


$sql_queries['getChatroomData'] = "select DISTINCT cometchat_chatroommessages.id id, cometchat_chatroommessages.message, cometchat_chatroommessages.sent, m.{usertable_username} \"from\", cometchat_chatroommessages.userid fromid, m.{usertable_userid} userid from cometchat_chatroommessages join {usertable} m on m.{usertable_userid} = cometchat_chatroommessages.userid  where cometchat_chatroommessages.chatroomid = '{chatroomid}' and cometchat_chatroommessages.message not like '%banned_%' and cometchat_chatroommessages.message not like '%kicked_%' and cometchat_chatroommessages.message not like '%deletemessage_%' {prependCondition} {guestpart} order by id desc {limitClause}";


$sql_queries['getChatroomDetails'] = "select * from cometchat_chatrooms where cometchat_chatrooms.id = '{id}'";
$sql_queries['getGuestID'] = "select id from cometchat_guests where id = '{id}'";
$sql_queries['getChatroomBannedGuests'] = "select DISTINCT cometchat_guests.id userid, concat('{guestnamePrefix}',cometchat_guests.name) username, '' link, '' avatar, cometchat_status.lastactivity lastactivity, cometchat_status.isdevice isdevice, cometchat_status.status, cometchat_status.message from cometchat_guests left join cometchat_status on cometchat_guests.id = cometchat_status.userid inner join cometchat_chatrooms_users on  cometchat_guests.id =  cometchat_chatrooms_users.userid where chatroomid = '{chatroomid}' and cometchat_chatrooms_users.isbanned = 1 Union {originalsql}";
$sql_queries['getGuestDetails'] = "select cometchat_guests.id userid, concat('{guestnamePrefix}',cometchat_guests.name) username,  '' link,  '' avatar, cometchat_status.lastactivity lastactivity, cometchat_status.lastseen lastseen, cometchat_status.lastseensetting lastseensetting, cometchat_status.status, cometchat_status.message, cometchat_status.isdevice isdevice, cometchat_status.readreceiptsetting readreceiptsetting from cometchat_guests left join cometchat_status on cometchat_guests.id = cometchat_status.userid where cometchat_guests.id = '{userid}'";
$sql_queries['receive_sqlpart1'] = " (cometchat.from = {from} and cometchat.id <= {id}) OR";
$sql_queries['getRecentGuestDetails'] = "select DISTINCT cometchat_guests.id userid, concat('{guestnamePrefix}',cometchat_guests.name) username, '' avatar from cometchat_guests where cometchat_guests.id in ({recentbuddyids}) UNION {sqlpart}";
$sql_queries['getRecentGroupMessages'] = "select * from cometchat_chatroommessages where id in (select max(id) from cometchat_chatroommessages {sqlpart} group by chatroomid)";


$sql_queries['getRecentGroupDetails'] = "select cometchat_chatrooms.id as id, cometchat_chatrooms.name as name from cometchat_chatrooms where cometchat_chatrooms.id in ({joinedrooms})";


$sql_queries['fetchMessages'] = "select cometchat.id, cometchat.from, cometchat.to, cometchat.message, cometchat.sent, cometchat.read, cometchat.direction from cometchat where ((cometchat.to = '{userid}' and cometchat.direction <> 2) or (cometchat.from = '{userid}' and cometchat.direction <> 1)) and (cometchat.id > '{timestamp}') and cometchat.direction <> 3 order by cometchat.id";
$sql_queries['fetchunreadMessages'] = "select cometchat.id, cometchat.from, cometchat.to, cometchat.message, cometchat.sent, cometchat.read, cometchat.direction from cometchat where cometchat.to = '{userid}' and cometchat.read <> 1 and cometchat.direction < 2 order by cometchat.id";


$sql_queries['typingTo'] = "select array_to_string(array_agg(userid),',') as tt from cometchat_status where typingto = '{userid}' and ('{timestamp}'-typingtime < 10)";


$sql_queries['getAnnouncementCount'] = "select count(id) as count from cometchat_announcements where \"to\" = '{userid}' and  recd = '0'";


$sql_queries['checkAnnoucements'] = "select id,announcement,time from cometchat_announcements where \"to\" = '{userid}' and  recd = '0' order by id desc limit 1";


$sql_queries['getAnnoucements'] = "select id,announcement an,time t from cometchat_announcements where \"to\" = '0' or \"to\" = '-1' order by id desc limit 1";


$sql_queries['cometchatSessionRead'] = "select session_data from cometchat_session where session_id = '{session_id}'";
$sql_queries['getFriends'] = "select friends from cometchat_users where uid = '{uid}'";
$sql_queries['api_getData'] = "select {fetchfield} from cometchat_users where {fieldname} = '{value}'";
$sql_queries['api_authenticateUser'] = "select userid from cometchat_users where username = '{username}' and password = '{password}'";
$sql_queries['announcement_datifyextra'] = "or \"to\" = '0' or \"to\" = '{userid}'";
$sql_queries['announcement_datify'] = "select id,announcement,time,\"to\" from cometchat_announcements where \"to\" = '-1' {extra} order by id desc limit {limitClause}";
$sql_queries['getChatrooms'] = "select id,name,type from cometchat_chatrooms where name = '{name}'";
$sql_queries['getChatroomById'] = "select * from cometchat_chatrooms where id ='{id}'";
$sql_queries['getUserIdByChatroom'] = "select userid from cometchat_chatroommessages where id ='{id}'";
$sql_queries['getChatroom'] = "select id,name,type from cometchat_chatrooms where id = '{id}' and (type = '0' or type='3') limit 1";
$sql_queries['getJoinedGroups'] = "select distinct chatroomid as id from cometchat_chatrooms_users where userid = '{userid}' and isbanned <> 1";
$sql_queries['groups_sqlpart'] = "(select COUNT(cometchat_chatrooms_users.userid) members from cometchat_chatrooms_users where cometchat_chatrooms_users.chatroomid = cometchat_chatrooms.id and isbanned <> 1)";
$sql_queries['getGroupsData'] = "select DISTINCT cometchat_chatrooms.id, cometchat_chatrooms.name, cometchat_chatrooms.type, cometchat_chatrooms.password, cometchat_chatrooms.lastactivity, cometchat_chatrooms.invitedusers, cometchat_chatrooms.createdby, {sqlpart} members from cometchat_chatrooms order by name asc";
$sql_queries['getGroupMsgMaxIds'] = "select max(cometchat_chatroommessages.id) id, cometchat_chatroommessages.chatroomid from cometchat_chatroommessages where cometchat_chatroommessages.chatroomid IN ({implodedChatrooms}) group by cometchat_chatroommessages.chatroomid";
$sql_queries['getGroupPassword'] = "select password from cometchat_chatrooms where id = '{currentroom}'";
$sql_queries['group_timestampcondition1'] = " (cometchat_chatroommessages.chatroomid = '{chatroomid}' and cometchat_chatroommessages.id > '{id}') or";
$sql_queries['group_timestampcondition2'] = " (cometchat_chatroommessages.chatroomid = '{chatroomid}' and cometchat_chatroommessages.id > '{id}') or";
$sql_queries['group_timestampcondition3'] = " cometchat_chatroommessages.chatroomid in('{joinedrooms}') and cometchat_chatroommessages.id > '{id}' and ";
$sql_queries['group_timestampcondition4'] = "cometchat_chatroommessages.chatroomid in('{joinedrooms}') and ";
$sql_queries['groups_guestpart_limitClause'] =  " LIMIT {limit}";

$sql_queries['groups_guestpart'] = " UNION select DISTINCT cometchat_chatroommessages.id id, cometchat_chatroommessages.message, cometchat_chatroommessages.chatroomid, cometchat_chatroommessages.sent, CONCAT('{guestnamePrefix}',m.name) \"from\", cometchat_chatroommessages.userid fromid, m.id userid from cometchat_chatroommessages join cometchat_guests m on cometchat_chatroommessages.userid = m.id where {timestampCondition} cometchat_chatroommessages.message not like 'banned_%' and cometchat_chatroommessages.message not like 'kicked_%' and cometchat_chatroommessages.message not like 'deletemessage_%' ";


$sql_queries['getGroupName'] = "select name from cometchat_chatrooms where name = '{name}'";
$sql_queries['checkchatroombanneduser'] = "select * from cometchat_chatrooms_users where userid ='{userid}' and chatroomid = '{chatroomid}' and isbanned = '1'";


$sql_queries['getchatroombannedusers'] = "select array_to_string(array_agg(cometchat_chatrooms_users.userid),',') bannedusers from cometchat_chatrooms_users where isbanned=1 and chatroomid='{chatroomid}'";


$sql_queries['getChatroomUserIDs'] = "select userid chatroomusers from cometchat_chatrooms_users where isbanned=0 and chatroomid='{chatroomid}'";
$sql_queries['getBlockedUsers_guestpart'] = " UNION (select distinct(m.id) AS id, concat('{guestnamePrefix}',m.name) AS name, '' AS avatar from cometchat_block, cometchat_guests m where m.id = toid and fromid = '{fromid}')";

$sql_queries['getBlockedUsers'] = "(select distinct({usertable}.{usertable_userid}) as id, {usertable}.{usertable_username} as name, {avatarfield} as avatar from cometchat_block, {usertable} {avatartable} where {usertable}.{usertable_userid} = toid and fromid = '{userid}') {guestpart}";

$sql_queries['groupchathistory_guestpart'] = " UNION select id as userid, concat('{guestnamePrefix}',name) as username from cometchat_guests";

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

$sql_queries['chathistory_guestpart'] = "UNION select id as userid, concat('{guestnamePrefix}',name) as username from cometchat_guests";

$sql_queries['chathistory'] = "	select cometchat.id, cometchat.message, cometchat.sent, cometchat.read, cometchat.sent, fromusertable.username as fromu ,tousertable.username as tou from cometchat
	left join
	(	select {usertable_userid} as userid, {usertable_username} as username from {usertable} {guestpart}
	) as fromusertable on fromusertable.userid = cometchat.\"from\"
	left join (
		select {usertable_userid} as userid, {usertable_username} as username from {usertable} {guestpart}
	) as tousertable on tousertable.userid = cometchat.\"to\"
	where
	cometchat.id in
	(	select min(id) from cometchat where (\"from\" = '{userid}' AND \"to\" = '{chatroomid}' AND direction <> 1 ) OR (\"to\" = '{userid}' AND \"from\" = '{chatroomid}' AND direction <> 2 ) AND direction <> 3 group by Floor(cometchat.sent/86400)
	)";

$sql_queries['chatroomviewlog_guestpart'] = "union (select m1.*, m2.name chatroom, concat('{guestnamePrefix}',f.name) fromu from cometchat_chatroommessages m1, cometchat_chatrooms m2, cometchat_guests f where  f.id = m1.userid and m1.chatroomid=m2.id and m1.chatroomid={chatroomid} and m1.id >= {id} and m1.message not like 'CC^CONTROL_deletemessage_%')";
$sql_queries['chatroomviewlog'] = "(select m1.*, m2.name chatroom, f.{usertable_username} fromu from cometchat_chatroommessages m1, cometchat_chatrooms m2, {usertable} f where  f.{usertable_userid} = m1.userid and m1.chatroomid=m2.id and m1.chatroomid='{chatroomid}' and m1.id >= {id} and m1.message not like '%banned%' and m1.message not like '%kicked%' and m1.message not like '%deletemessage%') {guestpart} order by id limit {limit}";
$sql_queries['viewlog_guestpart'] = "union (select m1.*, concat('{guestnamePrefix}',f.name) fromu, concat('{guestnamePrefix}',t.name) tou from cometchat m1, cometchat_guests f, cometchat_guests t where f.id = m1.from and t.id = m1.to and ((m1.from = '{userid}' and m1.to = '{chatroomid}' and m1.direction <> 1) or (m1.to = '{userid}' and m1.from = '{chatroomid}' and m1.direction <> 2)) and m1.id >= {id} and m1.direction <> 3) union (select m1.*, concat('{guestnamePrefix}',f.name) fromu, t.{usertable_username} tou from cometchat m1, cometchat_guests f, {usertable} t where f.id = m1.from and t.{usertable_userid} = m1.to and ((m1.from = '{userid}' and m1.to = '{chatroomid}' and m1.direction <> 1) or (m1.to = '{userid}' and m1.from = '{chatroomid}' and m1.direction <> 2)) and m1.id >= {id} and m1.direction <> 3) union (select m1.*, f.{usertable_username} fromu, concat('{guestnamePrefix}',t.name) tou from cometchat m1, {usertable} f, cometchat_guests t where f.{usertable_userid} = m1.from and t.id = m1.to and ((m1.from = '{userid}' and m1.to = '{chatroomid}'and m1.direction <> 1) or (m1.to = '{userid}' and m1.from = '{chatroomid}' and m1.direction <> 2)) and m1.id >= {id} and m1.direction <> 3)";
$sql_queries['viewlog'] = "(select m1.*, f.{usertable_username} fromu, t.{usertable_username} tou from cometchat m1, {usertable} f, {usertable} t  where  f.{usertable_userid} = m1.from and t.{usertable_userid} = m1.to and ((m1.from = '{userid}' and m1.to = '{chatroomid}' and m1.direction <> 1) or (m1.to = '{userid}' and m1.from = '{chatroomid}' and m1.direction <> 2)) and m1.id >= {id} and m1.direction <> 3) {guestpart} order by id limit {limit}";

$sql_queries['getGroup'] = "select guid from cometchat_chatrooms where guid = '{guid}'";
$sql_queries['getGroupId'] = "select id from cometchat_chatrooms where guid = '{guid}' or id = '{guid}'";
//$sql_queries['checkUserExists'] = "select userid as count from cometchat_users where {field} = '{value}'";
$sql_queries['checkGroupUserExists'] = "select userid as userid from cometchat_chatrooms_users where chatroomid = '{chatroomid}' and userid = '{userid}' ";
$sql_queries['addGroupUser'] = "insert into cometchat_chatrooms_users (chatroomid,userid) values ('{chatroomid}', '{userid}') ";
$sql_queries['deleteGroupUser'] = "delete from cometchat_chatrooms_users where  chatroomid = '{chatroomid}' and userid = '{userid}' ";
$sql_queries['deleteGroupApi'] = "delete from cometchat_chatrooms where guid = '{guid}' ";


$sql_queries['deleteGroupUserApi'] = "delete from cometchat_chatrooms_users where chatroomid = '{chatroomid}'";


$sql_queries['setBaseUrl'] = "WITH upsert AS (UPDATE cometchat_settings SET value = '{baseurl}', key_type = 0 WHERE setting_key = 'BASE_URL' RETURNING *) INSERT INTO cometchat_settings (setting_key,value,key_type) SELECT 'BASE_URL', '{baseurl}', 0 WHERE NOT EXISTS (SELECT * FROM upsert);";


$sql_queries['insertMessage'] = "insert into cometchat (\"from\",\"to\",message,sent,read, direction) values ('{userid}', '{to}','{message}','{timestamp}','{old}','{dir}')";


$sql_queries['insertRecentConversation'] = "WITH upsert AS (UPDATE cometchat_recentconversation SET \"from\" = '{userid}', \"to\" = '{to}', message = '{message}', id = '{insertedid}', sent = '{timestamp}' WHERE cometchat_recentconversation.convo_id = '{convo_hash}' RETURNING *) INSERT INTO cometchat_recentconversation (id,\"from\",\"to\",message,sent,convo_id) SELECT '{insertedid}', '{userid}', '{to}', '{message}', '{timestamp}', '{convo_hash}' WHERE NOT EXISTS (SELECT * FROM upsert)";


$sql_queries['insertBroadcastMessages'] = "insert into cometchat (\"from\",\"to\",message,sent,read, direction) values {sqlpart}";
$sql_queries['insertGroupMessage'] = "insert into cometchat_chatroommessages (userid,chatroomid,message,sent) values ('{userid}', '{to}','{styleStart}{message}{styleEnd}','{timestamp}')";


$sql_queries['insertAnnouncement'] = "insert into cometchat_announcements (announcement,time,to) values ('{announcement}', '{time}','{to}')";
$sql_queries['updateLastActivity'] = "insert into cometchat_status (userid,lastactivity,lastseen) values ('{userid}','{timestamp}','{timestamp}') ON CONFLICT (userid) DO UPDATE SET lastactivity = excluded.lastactivity, lastseen = excluded.lastseen;";

$sql_queries['setLastseensettings'] = "WITH upsert AS(UPDATE cometchat_status SET lastseensetting = '{message}' where userid = '{userid}' RETURNING *) INSERT INTO cometchat_status (userid,lastseensetting) SELECT '{userid}','{message}' WHERE NOT EXISTS (SELECT * FROM upsert)";

$sql_queries['setReadReceiptsettings'] = "WITH upsert AS(UPDATE cometchat_status SET readreceiptsetting = '{message}' where userid = '{userid}' RETURNING *) INSERT INTO cometchat_status (userid,readreceiptsetting) SELECT '{userid}','{message}' WHERE NOT EXISTS (SELECT * FROM upsert)";

$sql_queries['setStatus'] = "WITH upsert AS ( update cometchat_status SET status = '{message}' where userid = '{userid}' returning * ) INSERT INTO cometchat_status (userid, status) SELECT '{userid}','{message}' WHERE  NOT EXISTS ( SELECT * FROM   upsert)";

$sql_queries['insertGuest'] = "insert into cometchat_guests (name) values ('{name}')";

$sql_queries['insertStatus'] = "WITH upsert AS(UPDATE cometchat_status SET isdevice = '1' where userid = '{userid}' RETURNING *) INSERT INTO cometchat_status (userid,isdevice) SELECT '{userid}','1' WHERE NOT EXISTS (SELECT * FROM upsert)";

$sql_queries['insertIsTyping'] = "WITH upsert AS(UPDATE cometchat_status SET typingto = '{typingto}', typingtime = '{typingtime}' where userid = '{userid}' RETURNING *) INSERT INTO cometchat_status (userid,typingto,typingtime) SELECT '{userid}','{typingto}','{typingtime}' WHERE NOT EXISTS (SELECT * FROM upsert)";

$sql_queries['insertCometStatus'] = "WITH upsert AS ( update cometchat_status SET status = '{message}' where userid = '{userid}' returning * ) INSERT INTO cometchat_status (userid, status) SELECT '{userid}','{message}' WHERE  NOT EXISTS ( SELECT * FROM   upsert)";

$sql_queries['insertStatusMessage'] = "WITH upsert AS ( update cometchat_status SET message = '{message}' where userid = '{userid}' returning * ) INSERT INTO cometchat_status (userid, message) SELECT '{userid}','{message}' WHERE  NOT EXISTS ( SELECT * FROM   upsert)";

$sql_queries['cometchatSessionOpen'] = "WITH upsert AS ( update cometchat_session SET session_lastaccesstime = NOW() where session_id = '{session_id}' returning * ) INSERT INTO cometchat_session (session_id,session_lastaccesstime) SELECT '{session_id}',NOW() WHERE  NOT EXISTS ( SELECT * FROM   upsert)";

$sql_queries['cometchatSessionWrite'] = "WITH upsert AS ( update cometchat_session SET session_data = '{session_data}' where session_id = '{session_id}' returning * ) INSERT INTO cometchat_session (session_id,session_data) SELECT '{session_id}','{session_data}' WHERE  NOT EXISTS ( SELECT * FROM   upsert)";

$sql_queries['insertFriends'] = "insert into cometchat_users (friends) values('{friends}')"; //issue
$sql_queries['api_createuser'] = "insert into cometchat_users (username,password,displayname,link,grp) values ('{username}','{password}','{displayname}','{link}','{grp}')";
$sql_queries['api_creategroup'] = "insert into cometchat_chatrooms (name,createdby,lastactivity,password,type,guid) values ('{name}', '{createdby}','{lastactivity}','{password}','{type}','{guid}')";
$sql_queries['api_creategroupuser'] = "insert into cometchat_chatrooms_users (userid,chatroomid) values ('{userid}','{chatroomid}')";

$sql_queries['insertBot'] = "insert into cometchat_bots(name, description, avatar, apikey,keywords) values('{name}','{description}','{avatar}','{apikey}','{keywords}')";
$sql_queries['insertChatroom'] = "insert into cometchat_chatrooms (name,createdby,lastactivity,createdon,password,type) values ('{name}','{createdby}','{lastactivity}','{createdon}','{password}','{type}')";

$sql_queries['insertChatroomUser'] = "WITH upsert AS ( update cometchat_chatrooms_users SET isbanned = '0' where userid = '{userid}' AND  chatroomid = '{chatroomid}' returning * ) INSERT INTO cometchat_chatrooms_users (userid,chatroomid,isbanned) SELECT '{userid}','{chatroomid}','0' WHERE  NOT EXISTS ( SELECT * FROM   upsert)";

$sql_queries['unbanChatroomUser'] = "WITH upsert AS ( update cometchat_chatrooms_users SET isbanned = '0' where userid = '{userid}' AND  chatroomid = '{chatroomid}' returning * ) INSERT INTO cometchat_chatrooms_users (userid,chatroomid,isbanned) SELECT '{userid}','{chatroomid}','0' WHERE  NOT EXISTS ( SELECT * FROM   upsert)";
$sql_queries['blockUser'] = "insert into cometchat_block (fromid, toid) values ('{fromid}','{toid}')";
$sql_queries['insertFirstGuestID'] = "insert into cometchat_guests (id, name) VALUES ('{id}', 'guest-{id}');";
$sql_queries['getTblDetails'] = "SELECT * from {table} where {key}={value};";
$sql_queries['updateGroupActivity'] = "update cometchat_chatrooms set lastactivity = '{lastactivity}' where id = '{id}'";
$sql_queries['cometchatdelete_sql1'] = "update cometchat set cometchat.direction = 1 where cometchat.from = {from} and cometchat.direction = 0 and cometchat.to = {to}";
$sql_queries['cometchatdelete_sql2'] = "update cometchat set cometchat.direction = 2 where cometchat.from = {from} and cometchat.direction = 0 and cometchat.to = {to}";
$sql_queries['cometchatdelete_sql3'] = "update cometchat set cometchat.direction = 3  where cometchat.direction = 1 and cometchat.from={from} and cometchat.to = {to}";
$sql_queries['cometchatdelete_sql4'] = "update cometchat set cometchat.direction = 3 where cometchat.direction = 2 and cometchat.from={from} and cometchat.to = {to}";
$sql_queries['mobileapp_logout'] = "update cometchat_status set isdevice = '0' where userid = {userid}";
$sql_queries['updateReadMessages'] = "update cometchat set read = '1' where cometchat.to= '{to}' and ({sqlpart}) and read = '0'";
$sql_queries['updateFetchMessages'] = "update cometchat set read = '1' where cometchat.to = '{to}' and cometchat.id <= '{id}'";
$sql_queries['updateAnnoucements'] = "update cometchat_announcements set recd = '1' where id <= '{id}' and to  = '{userid}'";
$sql_queries['checkGuestName'] = "select name from cometchat_guests where name='{name}'";
$sql_queries['updateGuestName'] = "update cometchat_guests set name='{name}' where id='{id}'";
$sql_queries['updateFriends'] = "update cometchat_users set friends = '{friends}' where uid = '{uid}'";
$sql_queries['cloudapi_updatestatus'] = "update cometchat_status set {set} where userid = '{userid}'";
$sql_queries['api_updateuser'] = "update cometchat_users set {fieldname} = '{value}' where userid = '{userid}'";
$sql_queries['renameGroup'] = "update cometchat_chatrooms set name = '{name}' where id = '{id}'";
$sql_queries['banUser'] = "update cometchat_chatrooms_users set isbanned = '1' where userid = '{userid}' and chatroomid = '{chatroomid}'";
$sql_queries['addUsersToChatroom'] = "update cometchat_chatrooms set invitedusers = '{invitedusers}' where id='{id}'";
$sql_queries['cometchatSessionDestroy'] = "delete from cometchat_session where session_id = '{session_id}'";
$sql_queries['cometchatSessionGarbageCollector'] = "DELETE FROM cometchat_session WHERE session_lastaccesstime > CURRENT_TIMESTAMP";
$sql_queries['api_removeuser'] = "delete from cometchat_users where userid = '{userid}'";
$sql_queries['check_group'] = "select name, guid from cometchat_chatrooms where name = '{groupname}'";
$sql_queries['cron_groups'] = "delete from cometchat_chatrooms where createdby <> 0 and lastactivity < ({lastactivity}- {timeout})";
$sql_queries['cron_groupmessages'] = "delete from cometchat_chatroommessages where sent < ({sent}-10800)";
$sql_queries['cron_groupusers'] = "delete from cometchat_chatrooms_users where lastactivity < ({lastactivity}-3600)";
$sql_queries['deleteGroup'] = "delete from cometchat_chatrooms where id = '{id}' and createdby != 0 ";
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


$sql_queries['install_cometchatsettings'] = "select value from cometchat_settings where setting_key like '{setting_key}'";
$sql_queries['install_updateextensions'] = "update cometchat_settings set value='{value}' WHERE setting_key like '{setting_key}';";
$sql_queries['install_showtablestatus'] = "show table status where name = '{table_prefix}{db_usertable}'";
$sql_queries['install_showfullcolumns'] = "show FULL columns from {table_prefix}{db_usertable} where field = '{db_usertable_name}'";
$sql_queries['install_collateguests'] = "alter table cometchat_guests default collate {table_co}";
$sql_queries['install_charsetguests'] = "alter table cometchat_guests convert to character set {field_cs} collate {field_co}";
$sql_queries['install_statusfullcolumns'] = "SHOW FULL COLUMNS FROM cometchat_status WHERE field = 'isdevice' or field = 'lastactivity'";
$sql_queries['install_renamestatus'] = "RENAME TABLE cometchat_status to cometchat_status_old";
$sql_queries['install_insertstatus'] = "INSERT INTO cometchat_status (userid, message, status, typingto, typingtime, isdevice, lastactivity, lastseen, lastseensetting) SELECT *, NULL, NULL from cometchat_status_old";
$sql_queries['install_insertbaseurl'] = "WITH upsert AS ( update cometchat_settings SET value = '{baseurl}' , key_type = '0' where setting_key = 'BASE_URL' returning * ) INSERT INTO cometchat_settings (setting_key,value,key_type) SELECT 'BASE_URL','{baseurl}','0' WHERE  NOT EXISTS ( SELECT * FROM   upsert)";
$sql_queries['install_insertapikey'] = "WITH upsert AS ( update cometchat_settings SET value = '{apikey}' , key_type = '1' where setting_key = 'apikey' returning * ) INSERT INTO cometchat_settings (setting_key,value,key_type) SELECT 'apikey','{apikey}','1' WHERE  NOT EXISTS ( SELECT * FROM   upsert)";

$sql_queries['update_clearconversation'] = "UPDATE
        cometchat
    SET
        cometchat.direction =
        (
            CASE
                WHEN
                    (cometchat.from ={userid} AND cometchat.direction = 0 AND cometchat.to = {to})
                THEN
                    1
                WHEN
                    (cometchat.from = {to} AND cometchat.direction = 0 AND cometchat.to = {userid})
                THEN
                    2
                WHEN
                    (cometchat.direction = 1 AND cometchat.from={to} AND cometchat.to = {userid})
                THEN
                    3
                WHEN
                    (cometchat.direction = 2 AND cometchat.from={userid} AND cometchat.to = {to})
                THEN
                    3
                ELSE
                	cometchat.direction
            END
        )";

$sql_queries['alter_cometchat_chatrooms_users'] = <<<EOD

ALTER TABLE cometchat_chatrooms_users
ADD COLUMN isbanned integer NOT NULL default '0';

EOD;

$sql_queries['cometchat_chatrooms_users'] = <<<EOD

	CREATE TABLE IF NOT EXISTS cometchat_chatrooms_users (
	  userid integer NOT NULL,
	  chatroomid integer NOT NULL,
	  PRIMARY KEY  (userid,chatroomid),
	  isbanned integer default 0
	);

EOD;

$sql_queries['install_content'] = <<<EOD

ALTER TABLE cometchat RENAME TO {cometchat_messages_old};

CREATE TABLE IF NOT EXISTS cometchat (
  id serial unique  NOT NULL,
  "from" integer  NOT NULL,
  "to" integer  NOT NULL,
  message text NOT NULL,
  sent integer  NOT NULL default '0',
  read integer  NOT NULL default '0',
  direction integer  NOT NULL default '0',
  PRIMARY KEY  (id)
);

CREATE TABLE IF NOT EXISTS cometchat_announcements (
  id serial unique NOT NULL,
  announcement text NOT NULL,
  time integer NOT NULL,
  "to" integer NOT NULL,
  recd integer NOT NULL DEFAULT 0,

  PRIMARY KEY  (id)
);

CREATE TABLE IF NOT EXISTS cometchat_chatroommessages (
  id serial unique NOT NULL,
  userid integer  NOT NULL,
  chatroomid integer  NOT NULL,
  message text NOT NULL,
  sent integer NOT NULL,
  PRIMARY KEY  (id)
);

CREATE TABLE IF NOT EXISTS cometchat_chatrooms (
  id serial unique NOT NULL,
  name varchar(255) NOT NULL,
  lastactivity integer NOT NULL,
  createdby integer NOT NULL,
  password varchar(255) NOT NULL,
  type integer NOT NULL,
  vidsession varchar(512) default NULL,
  invitedusers varchar(512) default NULL,
  guid integer default NULL,
  PRIMARY KEY  (id)
);

DO
$$
BEGIN
IF not EXISTS (SELECT column_name
               FROM information_schema.columns
               WHERE table_schema='public' and table_name='cometchat_chatrooms' and column_name='invitedusers') THEN
alter table cometchat_chatrooms add column invitedusers varchar(512) default NULL ;
else
raise NOTICE 'Already exists';
END IF;
END
$$;

CREATE TABLE IF NOT EXISTS cometchat_status (
  userid integer NOT NULL,
  message text,
  status varchar check (status  in ('available','away','busy','invisible','offline',''))default NULL,
  typingto integer default NULL,
  typingtime integer default NULL,
  isdevice integer NOT NULL default '0',
  lastactivity integer NOT NULL default '0',
  lastseen integer NOT NULL default '0',
  lastseensetting integer NOT NULL default '0',
  PRIMARY KEY  (userid)
);

DO
$$
BEGIN
IF not EXISTS (SELECT column_name
               FROM information_schema.columns
               WHERE table_schema='public' and table_name='cometchat_status' and column_name='readreceiptsetting') THEN
alter table cometchat_status add column readreceiptsetting integer NOT NULL default '1' ;
else
raise NOTICE 'Already exists';
END IF;
END
$$;

CREATE TABLE IF NOT EXISTS cometchat_videochatsessions (
  username varchar(255) NOT NULL,
  identity varchar(255) NOT NULL,
  timestamp integer default 0,
  PRIMARY KEY  (username)
);

CREATE TABLE IF NOT EXISTS cometchat_block (
  id serial unique NOT NULL,
  fromid int NOT NULL,
  toid int NOT NULL,
  PRIMARY KEY  (id)
);

CREATE TABLE IF NOT EXISTS cometchat_guests (
  id serial unique NOT NULL,
  name varchar(255) NOT NULL,
  lastactivity integer,
  PRIMARY KEY  (id)
);

ALTER SEQUENCE cometchat_guests_id_seq RESTART WITH 10000001;

WITH upsert AS(update cometchat_guests
SET
name = 'guest-10000001'
WHERE id = '10000000' returning *)
INSERT INTO cometchat_guests (id, name)
SELECT '10000000', 'guest-10000000' WHERE NOT EXISTS(SELECT * FROM upsert);

CREATE TABLE IF NOT EXISTS cometchat_session (
  session_id char(32) NOT NULL,
  session_data text NOT NULL,
  session_lastaccesstime timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (session_id)
);

CREATE TABLE IF NOT EXISTS cometchat_settings (
  setting_key varchar(50)  NOT NULL PRIMARY KEY,
  value text  NOT NULL ,
  key_type int NOT NULL DEFAULT '1'
);

WITH upsert AS(update cometchat_settings SET value = 'a:5:{s:3:"ads";s:14:"Advertisements";s:6:"jabber";s:10:"Gtalk Chat";s:9:"mobileapp";s:9:"Mobileapp";s:7:"desktop";s:7:"Desktop";s:4:"bots";s:4:"Bots";}' WHERE  setting_key = 'extensions_core' returning *)
INSERT INTO cometchat_settings (setting_key,value,key_type)
SELECT 'extensions_core','a:4:{s:3:"ads";s:14:"Advertisements";s:9:"mobileapp";s:9:"Mobileapp";s:7:"desktop";s:7:"Desktop";s:4:"bots";s:4:"Bots";}','2' WHERE NOT EXISTS(SELECT * FROM upsert);

WITH upsert AS(update cometchat_settings SET value = 'a:17:{s:9:"audiochat";a:2:{i:0;s:10:"Audio Chat";i:1;i:0;}s:6:"avchat";a:2:{i:0;s:16:"Audio/Video Chat";i:1;i:0;}s:5:"block";a:2:{i:0;s:10:"Block User";i:1;i:1;}s:9:"broadcast";a:2:{i:0;s:21:"Audio/Video Broadcast";i:1;i:0;}s:11:"chathistory";a:2:{i:0;s:12:"Chat History";i:1;i:0;}s:17:"clearconversation";a:2:{i:0;s:18:"Clear Conversation";i:1;i:0;}s:12:"filetransfer";a:2:{i:0;s:11:"Send a file";i:1;i:0;}s:9:"handwrite";a:2:{i:0;s:19:"Handwrite a message";i:1;i:0;}s:6:"report";a:2:{i:0;s:19:"Report Conversation";i:1;i:1;}s:4:"save";a:2:{i:0;s:17:"Save Conversation";i:1;i:0;}s:11:"screenshare";a:2:{i:0;s:13:"Screensharing";i:1;i:0;}s:7:"smilies";a:2:{i:0;s:7:"Smilies";i:1;i:0;}s:8:"stickers";a:2:{i:0;s:8:"Stickers";i:1;i:0;}s:5:"style";a:2:{i:0;s:15:"Color your text";i:1;i:2;}s:13:"transliterate";a:2:{i:0;s:13:"Transliterate";i:1;i:0;}s:10:"whiteboard";a:2:{i:0;s:10:"Whiteboard";i:1;i:0;}s:10:"writeboard";a:2:{i:0;s:10:"Writeboard";i:1;i:0;}}' WHERE  setting_key = 'plugins_core' returning *)
INSERT INTO cometchat_settings (setting_key,value,key_type)
SELECT 'plugins_core','a:17:{s:9:"audiochat";a:2:{i:0;s:10:"Audio Chat";i:1;i:0;}s:6:"avchat";a:2:{i:0;s:16:"Audio/Video Chat";i:1;i:0;}s:5:"block";a:2:{i:0;s:10:"Block User";i:1;i:1;}s:9:"broadcast";a:2:{i:0;s:21:"Audio/Video Broadcast";i:1;i:0;}s:11:"chathistory";a:2:{i:0;s:12:"Chat History";i:1;i:0;}s:17:"clearconversation";a:2:{i:0;s:18:"Clear Conversation";i:1;i:0;}s:12:"filetransfer";a:2:{i:0;s:11:"Send a file";i:1;i:0;}s:9:"handwrite";a:2:{i:0;s:19:"Handwrite a message";i:1;i:0;}s:6:"report";a:2:{i:0;s:19:"Report Conversation";i:1;i:1;}s:4:"save";a:2:{i:0;s:17:"Save Conversation";i:1;i:0;}s:11:"screenshare";a:2:{i:0;s:13:"Screensharing";i:1;i:0;}s:7:"smilies";a:2:{i:0;s:7:"Smilies";i:1;i:0;}s:8:"stickers";a:2:{i:0;s:8:"Stickers";i:1;i:0;}s:5:"style";a:2:{i:0;s:15:"Color your text";i:1;i:2;}s:13:"transliterate";a:2:{i:0;s:13:"Transliterate";i:1;i:0;}s:10:"whiteboard";a:2:{i:0;s:10:"Whiteboard";i:1;i:0;}s:10:"writeboard";a:2:{i:0;s:10:"Writeboard";i:1;i:0;}}','2' WHERE NOT EXISTS(SELECT * FROM upsert);

INSERT INTO cometchat_settings (setting_key, value, key_type) VALUES ('modules_core', 'a:11:{s:13:"announcements";a:9:{i:0;s:13:"announcements";i:1;s:13:"Announcements";i:2;s:31:"modules/announcements/index.php";i:3;s:6:"_popup";i:4;s:3:"280";i:5;s:3:"310";i:6;s:0:"";i:7;s:1:"1";i:8;s:0:"";}s:16:"broadcastmessage";a:9:{i:0;s:16:"broadcastmessage";i:1;s:17:"Broadcast Message";i:2;s:34:"modules/broadcastmessage/index.php";i:3;s:6:"_popup";i:4;s:3:"385";i:5;s:3:"300";i:6;s:0:"";i:7;s:1:"1";i:8;s:0:"";}s:9:"chatrooms";a:9:{i:0;s:9:"chatrooms";i:1;s:9:"Chatrooms";i:2;s:27:"modules/chatrooms/index.php";i:3;s:6:"_popup";i:4;s:3:"600";i:5;s:3:"300";i:6;s:0:"";i:7;s:1:"1";i:8;s:1:"1";}s:8:"facebook";a:9:{i:0;s:8:"facebook";i:1;s:17:"Facebook Fan Page";i:2;s:26:"modules/facebook/index.php";i:3;s:6:"_popup";i:4;s:3:"500";i:5;s:3:"460";i:6;s:0:"";i:7;s:1:"1";i:8;s:0:"";}s:5:"games";a:9:{i:0;s:5:"games";i:1;s:19:"Single Player Games";i:2;s:23:"modules/games/index.php";i:3;s:6:"_popup";i:4;s:3:"465";i:5;s:3:"300";i:6;s:0:"";i:7;s:1:"1";i:8;s:0:"";}s:4:"home";a:8:{i:0;s:4:"home";i:1;s:4:"Home";i:2;s:1:"/";i:3;s:0:"";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";i:7;s:0:"";}s:17:"realtimetranslate";a:9:{i:0;s:17:"realtimetranslate";i:1;s:23:"Translate Conversations";i:2;s:35:"modules/realtimetranslate/index.php";i:3;s:6:"_popup";i:4;s:3:"280";i:5;s:3:"310";i:6;s:0:"";i:7;s:1:"1";i:8;s:0:"";}s:11:"scrolltotop";a:8:{i:0;s:11:"scrolltotop";i:1;s:13:"Scroll To Top";i:2;s:40:"javascript:jqcc.cometchat.scrollToTop();";i:3;s:0:"";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";i:7;s:0:"";}s:5:"share";a:8:{i:0;s:5:"share";i:1;s:15:"Share This Page";i:2;s:23:"modules/share/index.php";i:3;s:6:"_popup";i:4;s:3:"350";i:5;s:2:"50";i:6;s:0:"";i:7;s:1:"1";}s:9:"translate";a:9:{i:0;s:9:"translate";i:1;s:19:"Translate This Page";i:2;s:27:"modules/translate/index.php";i:3;s:6:"_popup";i:4;s:3:"280";i:5;s:3:"310";i:6;s:0:"";i:7;s:1:"1";i:8;s:0:"";}s:7:"twitter";a:8:{i:0;s:7:"twitter";i:1;s:7:"Twitter";i:2;s:25:"modules/twitter/index.php";i:3;s:6:"_popup";i:4;s:3:"500";i:5;s:3:"300";i:6;s:0:"";i:7;s:1:"1";}}', 2);

CREATE TABLE IF NOT EXISTS cometchat_languages (
  lang_key varchar(255) ,
  lang_text text ,
  code varchar(20) ,
  type varchar(20) ,
  name varchar(50)
);

ALTER TABLE cometchat_languages
ADD CONSTRAINT  lang_index UNIQUE (lang_key,code,type,name);

WITH upsert AS(update cometchat_languages
SET
lang_text = '0',  lang_key = 'rtl', type = 'core', name = 'default'
WHERE code = 'en' returning *)
INSERT INTO cometchat_languages (lang_key, lang_text, code, type, name)
SELECT 'rtl', '0', 'en', 'core', 'default' WHERE NOT EXISTS(SELECT * FROM upsert);

CREATE TABLE IF NOT EXISTS cometchat_colors (
  color_key varchar(100) NOT NULL,
  color_value text NOT NULL,
  color varchar(50) NOT NULL
);

ALTER TABLE cometchat_colors
ADD CONSTRAINT  color_index UNIQUE (color_key,color_value);

INSERT INTO cometchat_colors (color_key, color_value, color) VALUES
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

DELETE FROM cometchat_colors WHERE color_key NOT LIKE 'color%';

WITH upsert AS (UPDATE cometchat_settings SET value = 'docked' WHERE setting_key = 'theme' RETURNING *) INSERT INTO cometchat_settings (setting_key , value, key_type) SELECT 'theme', 'docked', 1 WHERE NOT EXISTS (SELECT * FROM upsert);

WITH upsert AS (UPDATE cometchat_settings SET value = 'color1' WHERE setting_key = 'color' RETURNING *) INSERT INTO cometchat_settings (setting_key , value, key_type) SELECT 'color', 'color1', 1 WHERE NOT EXISTS (SELECT * FROM upsert);

CREATE TABLE IF NOT EXISTS cometchat_users (
  userid serial  NOT NULL ,
  username varchar(100) UNIQUE NOT NULL,
  displayname varchar(100)  NOT NULL,
  password varchar(100)  NOT NULL,
  avatar varchar(200) NULL,
  link varchar(200) NULL,
  grp varchar(25) NULL,
  friends text NULL,
  PRIMARY KEY (userid)
);

CREATE TABLE IF NOT EXISTS cometchat_bots (
  id serial NOT NULL,
  name varchar(100) UNIQUE NOT NULL,
  description text NOT NULL,
  keywords text NOT NULL,
  avatar varchar(200) NOT NULL,
  apikey varchar(200) NOT NULL,
  PRIMARY KEY (id)
);

CREATE TABLE IF NOT EXISTS cometchat_recentconversation (
	convo_id varchar(100) NOT NULL,
	id integer NOT NULL,
	"from" integer NOT NULL,
	"to" integer NOT NULL,
	message text NOT NULL,
	sent varchar(100) NOT NULL
);

ALTER TABLE cometchat_recentconversation
ADD CONSTRAINT convo_id UNIQUE (id,"from","to",message,sent);


{cometchat_chatrooms_users}

EOD;

$sql_queries['update_content_630'] = <<<EOD
			INSERT INTO cometchat_colors (color_key, color_value, color) VALUES
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

			DELETE FROM cometchat_colors WHERE color_key NOT LIKE 'color%';
EOD;

$sql_queries['update_content_640'] = <<<EOD
			ALTER TABLE cometchat_status
			add column(
				readreceiptsetting int(1) unsigned NOT NULL default '1'
			);


			CREATE TABLE IF NOT EXISTS cometchat_recentconversation (
				convo_id varchar(100) NOT NULL,
				id integer NOT NULL,
				"from" integer NOT NULL,
				"to" integer NOT NULL,
				message text NOT NULL,
				sent varchar(100) NOT NULL
			);

			ALTER TABLE cometchat_recentconversation
			ADD CONSTRAINT convo_id UNIQUE (id,"from","to",message,sent);

EOD;

$sql_queries['install_createstatus'] = <<<EOD

	CREATE TABLE IF NOT EXISTS cometchat_status (
	  userid integer NOT NULL,
	  message text,
	  status varchar check (status  in ('available','away','busy','invisible','offline'))default NULL,
	  typingto integer default NULL,
	  typingtime integer default NULL,
	  isdevice integer NOT NULL default '0',
	  lastactivity integer NOT NULL default '0',
	  lastseen integer NOT NULL default '0',
	  lastseensetting integer NOT NULL default '0',
	  PRIMARY KEY  (userid)
	);

EOD;


class SqlQueries{
	function getQueries(){
		$sqlqueries = array();

		$sqlqueries['getRecentMessages'] = "select cometchat_recentconversation.* from cometchat_recentconversation join  " . TABLE_PREFIX . DB_USERTABLE . " on  " . TABLE_PREFIX . DB_USERTABLE . "." . DB_USERTABLE_USERID . " = cometchat_recentconversation.from join  " . TABLE_PREFIX . DB_USERTABLE . " a on  a." . DB_USERTABLE_USERID . " = cometchat_recentconversation.to where cometchat_recentconversation.to = '{userid}' or cometchat_recentconversation.from = '{userid}'";

		$sql_queries['selectUser'] = "select userid, username, link, avatar, uid, friends, grp, displayname from ".TABLE_PREFIX.DB_USERTABLE." ";
		$sqlqueries['checkUserExists'] = "select ".DB_USERTABLE.".".DB_USERTABLE_USERID." as userid from ".TABLE_PREFIX.DB_USERTABLE." where {field} = '{value}'";

		$sqlqueries['checkSocialLogin'] = "select ".DB_USERTABLE.".".DB_USERTABLE_USERID." from ".DB_USERTABLE." where ".DB_USERTABLE.".{db_usertable_username} = '{network_name}_{identifier}'";
		$sqlqueries['auth_getFriendsList'] = "select DISTINCT ".DB_USERTABLE.".".DB_USERTABLE_USERID." userid, ".DB_USERTABLE.".".DB_USERTABLE_NAME." username, ".DB_USERTABLE.".".DB_LINKFIELD." link, ".DB_AVATARFIELD." avatar, ".DB_USERTABLE.".{db_groupfield} grp, cometchat_status.lastactivity lastactivity, cometchat_status.lastseen lastseen, cometchat_status.lastseensetting lastseensetting, cometchat_status.status, cometchat_status.message, cometchat_status.isdevice from ".DB_USERTABLE." left join cometchat_status on ".DB_USERTABLE.".".DB_USERTABLE_USERID." = cometchat_status.userid ".DB_AVATARTABLE." where (cometchat_status.status IS NULL OR cometchat_status.status <> 'invisible' OR cometchat_status.status <> 'offline') order by username asc";
		$sqlqueries['auth_getUserDetails'] = "select ".DB_USERTABLE.".".DB_USERTABLE_USERID." userid, ".DB_USERTABLE.".".DB_USERTABLE_NAME." username,  ".DB_USERTABLE.".".DB_LINKFIELD." link, ".DB_AVATARFIELD." avatar, cometchat_status.lastactivity lastactivity, cometchat_status.lastseen lastseen, cometchat_status.lastseensetting lastseensetting, cometchat_status.status, cometchat_status.message, cometchat_status.isdevice from ".DB_USERTABLE." left join cometchat_status on ".DB_USERTABLE.".".DB_USERTABLE_USERID." = cometchat_status.userid ".DB_AVATARTABLE." where ".DB_USERTABLE.".".DB_USERTABLE_USERID." = '{userid}'";
		$sqlqueries['auth_getActivechatboxdetails'] = "select DISTINCT ".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_USERID." userid, ".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_NAME." username,  ".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_USERID." link, ".DB_AVATARFIELD." avatar,".DB_USERTABLE.".{db_groupfield} grp, cometchat_status.lastactivity lastactivity, cometchat_status.lastseen lastseen, cometchat_status.lastseensetting lastseensetting, cometchat_status.status, cometchat_status.message, cometchat_status.isdevice from ".TABLE_PREFIX.DB_USERTABLE." left join cometchat_status on ".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_USERID." = cometchat_status.userid ".DB_AVATARTABLE." where ".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_USERID." IN ({userids})";
		$sqlqueries['getGuestsList'] = "select DISTINCT cometchat_guests.id userid, concat('{guestnamePrefix}',cometchat_guests.name) username, '' link, '' avatar, cometchat_status.lastactivity lastactivity, cometchat_status.lastseen lastseen, cometchat_status.lastseensetting lastseensetting, cometchat_status.status, cometchat_status.message, cometchat_status.isdevice, cometchat_status.readreceiptsetting readreceiptsetting from cometchat_guests left join cometchat_status on cometchat_guests.id = cometchat_status.userid where ('{time}'- cometchat_status.lastactivity < '".((ONLINE_TIMEOUT)*2)."') and (cometchat_status.status IS NULL OR cometchat_status.status <> 'invisible' OR cometchat_status.status <> 'offline')";
		$sqlqueries['getChatroomGuests'] = "select DISTINCT cometchat_guests.id userid, concat('{guestnamePrefix}',cometchat_guests.name) username, '' avatar, cometchat_status.lastactivity lastactivity, cometchat_status.isdevice isdevice, cometchat_chatrooms_users.isbanned , cometchat_status.status from cometchat_guests left join cometchat_status on cometchat_guests.id = cometchat_status.userid inner join cometchat_chatrooms_users on cometchat_guests.id = cometchat_chatrooms_users.userid where chatroomid = '{chatroomid}' and (cometchat_status.lastactivity > '{time}' -".ONLINE_TIMEOUT.") Union {originalsql}";
		$sqlqueries['getRecentUserDetails'] = "select ".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_USERID." userid, ".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_NAME." username, ".DB_AVATARFIELD." avatar from ".TABLE_PREFIX.DB_USERTABLE.DB_AVATARTABLE." where ".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_USERID." in ({recentbuddyids})";
		$sqlqueries['getUserData'] = "select ".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_USERID." userid,".TABLE_PREFIX.DB_USERTABLE.".uid uid, ".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_NAME." username, ".TABLE_PREFIX.DB_USERTABLE.".link link, ".DB_AVATARFIELD." avatar, cometchat_status.lastactivity lastactivity, cometchat_status.status, cometchat_status.message, cometchat_status.isdevice from ".TABLE_PREFIX.DB_USERTABLE." left join cometchat_status on ".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_USERID." = cometchat_status.userid ".DB_AVATARTABLE." where uid = '{uid}'";
		$sqlqueries['cloudapi_getData'] = "select {fetchfield} from ".TABLE_PREFIX.DB_USERTABLE." where {fieldname} = '{value}'";
		$sqlqueries['cloudapi_getUserDetails'] = "select userid,username,link,avatar,displayname from ".TABLE_PREFIX.DB_USERTABLE." {where}";
		$sql_queries['loginuser'] = "select userid, username, link, avatar, uid, friends, grp, displayname from ".TABLE_PREFIX.DB_USERTABLE." where username = '{username}' and password = '{password}'";
		$sqlqueries['api_checkpassword'] = "select id, password from cometchat_users where id = {id}";
		$sqlqueries['groupMessages'] = "select DISTINCT cometchat_chatroommessages.id id, cometchat_chatroommessages.message, cometchat_chatroommessages.chatroomid, cometchat_chatroommessages.sent, m.".DB_USERTABLE_NAME." \"from\", cometchat_chatroommessages.userid fromid, m.".DB_USERTABLE_USERID." userid from cometchat_chatroommessages join ".TABLE_PREFIX.DB_USERTABLE." m on cometchat_chatroommessages.userid = m.".DB_USERTABLE_USERID." where {timestampCondition} cometchat_chatroommessages.message not like 'banned_%' and cometchat_chatroommessages.message not like 'kicked_%' and cometchat_chatroommessages.message not like 'deletemessage_%' {guestpart} order by id desc {limitClause}";
		$sqlqueries['getchatroomusers'] = "select DISTINCT ".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_USERID." userid, ".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_NAME." username, ".DB_AVATARFIELD." avatar, cometchat_status.lastactivity lastactivity, cometchat_status.isdevice isdevice, cometchat_chatrooms_users.isbanned , cometchat_status.status from ".TABLE_PREFIX.DB_USERTABLE." left join cometchat_status on ".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_USERID." = cometchat_status.userid inner join cometchat_chatrooms_users on  ".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_USERID." =  cometchat_chatrooms_users.userid ". DB_AVATARTABLE ." where chatroomid = '{chatroomid}' {timestampCondition} order by username asc";
		$sqlqueries['unban'] = "select DISTINCT ".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_USERID." userid, ".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_NAME." username, ".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_NAME." link, ".DB_AVATARFIELD." avatar, cometchat_status.lastactivity lastactivity, cometchat_status.isdevice isdevice, cometchat_status.status, cometchat_status.message from ".TABLE_PREFIX.DB_USERTABLE." left join cometchat_status on ".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_USERID." = cometchat_status.userid right join cometchat_chatrooms_users on ".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_USERID." =cometchat_chatrooms_users.userid ".DB_AVATARTABLE." where ".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_USERID." <> '{userid}' and cometchat_chatrooms_users.chatroomid = '{chatroomid}' and cometchat_chatrooms_users.isbanned ='1' order by username asc";
		$sqlqueries['insertSocialLogin'] = "insert into ".DB_USERTABLE." (".DB_USERTABLE.".{db_usertable_username},".DB_USERTABLE.".".DB_USERTABLE_NAME.",".DB_AVATARFIELD.",".DB_USERTABLE.".".DB_LINKFIELD.",".DB_USERTABLE.".{db_groupfield}) values ( '{network_name}_{identifier}','{firstName}','{photoURL}','{profileURL}','{groupfield}')";
		$sqlqueries['cloudapi_createuser'] = "insert into ".TABLE_PREFIX.DB_USERTABLE." set username = '{username}',password = '{password}', link = '{link}', avatar = '{avatar}', displayname = '{displayname}', uid = '{uid}'";
		$sqlqueries['loginWithUserDetails'] = "insert into ".TABLE_PREFIX.DB_USERTABLE." set username = '{username}', link = '{link}', avatar = '{avatar}', displayname = '{displayname}', uid = '{uid}'";
		$sqlqueries['updateSocialLogin'] = "update ".DB_USERTABLE." set ".DB_USERTABLE.".".DB_USERTABLE_NAME."='{firstName}',".DB_AVATARFIELD."='{photoURL}',".DB_USERTABLE.".".DB_LINKFIELD."='{profileURL}' where ".DB_USERTABLE.".{db_usertable_username}='{network_name}_{identifier}'";
		$sqlqueries['cloudapi_updateuser'] = "update ".TABLE_PREFIX.DB_USERTABLE." set {set} where userid = '{userid}'";
		$sqlqueries['cloudapi_updatefriends'] = "update ".TABLE_PREFIX.DB_USERTABLE." set friends = '{friends}' where userid = '{userid}'";
		$sqlqueries['update_loggedinuser'] = "update ".TABLE_PREFIX.DB_USERTABLE." set uid = '{uid}'  where userid = '{userid}'";
		$sqlqueries['cloudapi_removeuser'] = "delete from ".TABLE_PREFIX.DB_USERTABLE." where userid = '{userid}'";
		$sqlqueries['export_cometchat_messages'] = "select to_timestamp(m.sent) as Time, fromuser.username as FromName, touser.username as ToName , m.message as Message from cometchat as m left join ( select ".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_USERID." as userid, ".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_NAME." as username from ".TABLE_PREFIX.DB_USERTABLE." union select id as userid, name as username from cometchat_guests ) as fromuser on fromuser.userid=m.from left join (
			select ".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_USERID." as userid, ".TABLE_PREFIX.DB_USERTABLE.".".DB_USERTABLE_NAME." as username from ".TABLE_PREFIX.DB_USERTABLE." union select id as userid, name as username from cometchat_guests ) as touser on touser.userid=m.to where DATE(to_timestamp(m.sent)) > (NOW() - INTERVAL 7 DAY)";
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
		$sqlqueries['insert_report'] = "insert into cometchat_report (timestamp_start, total_no_of_users, total_no_of_guest, no_of_active_users_last_24_hrs, no_of_active_guest_last_24_hrs, no_of_messages_exchange_one_on_one_last_24_hrs, no_of_messages_exchange_groupchat_last_24_hrs, no_of_group_created_last_24_hrs) VALUES ('{timestamp_start}', '{total_no_of_users}', '{total_no_of_guest}', '{no_of_active_users_last_24_hrs}', '{no_of_active_guest_last_24_hrs}', '{no_of_messages_exchange_one_on_one_last_24_hrs}', '{no_of_messages_exchange_groupchat_last_24_hrs}', '{no_of_group_created_last_24_hrs}')";

	/**
		END: Report New Optimize Queries
	*/

		return $sqlqueries;
	}
}
