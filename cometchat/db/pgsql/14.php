<?php

$content = <<<EOD

CREATE TABLE IF NOT EXISTS cometchat_settings (
  setting_key varchar(50) NOT NULL PRIMARY KEY ,
  value text NOT NULL ,
  key_type int NOT NULL DEFAULT '0'
);
COMMENT ON COLUMN cometchat_settings.setting_key IS 'Configuration setting name. It can be PHP constant, variable or array';
COMMENT ON COLUMN cometchat_settings.value IS 'Value of the key.';
COMMENT ON COLUMN cometchat_settings.key_type IS 'States whether the key is: 0 = PHP constant, 1 = atomic variable or 2 = serialized associative array.';
COMMENT ON TABLE cometchat_settings IS 'Stores all the configuration settings for CometChat';

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

CREATE TABLE IF NOT EXISTS cometchat_chatrooms_users (
  userid integer NOT NULL,
  chatroomid integer NOT NULL,
  PRIMARY KEY  (userid,chatroomid),
  isbanned integer default 0
);

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

CREATE TABLE IF NOT EXISTS `cometchat_videochatsessions` (
  `username` varchar(255) NOT NULL,
  `identity` varchar(255) NOT NULL,
  `timestamp` int(10) unsigned default 0,
  PRIMARY KEY  (`username`),
  KEY `username` (`username`),
  KEY `identity` (`identity`),
  KEY `timestamp` (`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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

WITH upsert AS(update cometchat_guests
SET
name = 'guest-10000001'
WHERE id = '10000000' returning *)
INSERT INTO cometchat_guests (id, name)
SELECT '10000000', 'guest-10000000' WHERE NOT EXISTS(SELECT * FROM upsert);

ALTER SEQUENCE cometchat_guests_id_seq RESTART WITH 10000001;

CREATE TABLE IF NOT EXISTS cometchat_session (
  session_id char(32) NOT NULL,
  session_data text NOT NULL,
  session_lastaccesstime timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (session_id)
);

WITH upsert AS(update cometchat_settings SET value = 'a:5:{s:3:"ads";s:14:"Advertisements";s:6:"jabber";s:10:"Gtalk Chat";s:9:"mobileapp";s:9:"Mobileapp";s:7:"desktop";s:7:"Desktop";s:4:"bots";s:4:"Bots";}' WHERE  setting_key = 'extensions_core' returning *)
INSERT INTO cometchat_settings (setting_key,value,key_type)
SELECT 'extensions_core','a:4:{s:3:"ads";s:14:"Advertisements";s:9:"mobileapp";s:9:"Mobileapp";s:7:"desktop";s:7:"Desktop";s:4:"bots";s:4:"Bots";}','2' WHERE NOT EXISTS(SELECT * FROM upsert);

WITH upsert AS(update cometchat_settings SET value = 'a:18:{s:9:"audiochat";a:2:{i:0;s:10:"Audio Chat";i:1;i:0;}s:6:"avchat";a:2:{i:0;s:16:"Audio/Video Chat";i:1;i:0;}s:5:"block";a:2:{i:0;s:10:"Block User";i:1;i:1;}s:9:"broadcast";a:2:{i:0;s:21:"Audio/Video Broadcast";i:1;i:0;}s:11:"chathistory";a:2:{i:0;s:12:"Chat History";i:1;i:0;}s:17:"clearconversation";a:2:{i:0;s:18:"Clear Conversation";i:1;i:0;}s:12:"filetransfer";a:2:{i:0;s:11:"Send a file";i:1;i:0;}s:9:"handwrite";a:2:{i:0;s:19:"Handwrite a message";i:1;i:0;}s:6:"report";a:2:{i:0;s:19:"Report Conversation";i:1;i:1;}s:4:"save";a:2:{i:0;s:17:"Save Conversation";i:1;i:0;}s:11:"screenshare";a:2:{i:0;s:17:"Share Your Screen";i:1;i:0;}s:7:"smilies";a:2:{i:0;s:5:"Emoji";i:1;i:0;}s:8:"stickers";a:2:{i:0;s:8:"Stickers";i:1;i:0;}s:5:"style";a:2:{i:0;s:15:"Color your text";i:1;i:2;}s:13:"transliterate";a:2:{i:0;s:22:"Write in your language";i:1;i:0;}s:10:"whiteboard";a:2:{i:0;s:25:"Share Whiteboard Document";i:1;i:0;}s:10:"writeboard";a:2:{i:0;s:28:"Share Collaborative Document";i:1;i:0;}s:9:"voicenote";a:2:{i:0;s:16:"Share Voice Note";i:1;i:0;}}' WHERE  setting_key = 'plugins_core' returning *)
INSERT INTO cometchat_settings (setting_key,value,key_type)
SELECT 'plugins_core','a:18:{s:9:"audiochat";a:2:{i:0;s:10:"Audio Chat";i:1;i:0;}s:6:"avchat";a:2:{i:0;s:16:"Audio/Video Chat";i:1;i:0;}s:5:"block";a:2:{i:0;s:10:"Block User";i:1;i:1;}s:9:"broadcast";a:2:{i:0;s:21:"Audio/Video Broadcast";i:1;i:0;}s:11:"chathistory";a:2:{i:0;s:12:"Chat History";i:1;i:0;}s:17:"clearconversation";a:2:{i:0;s:18:"Clear Conversation";i:1;i:0;}s:12:"filetransfer";a:2:{i:0;s:11:"Send a file";i:1;i:0;}s:9:"handwrite";a:2:{i:0;s:19:"Handwrite a message";i:1;i:0;}s:6:"report";a:2:{i:0;s:19:"Report Conversation";i:1;i:1;}s:4:"save";a:2:{i:0;s:17:"Save Conversation";i:1;i:0;}s:11:"screenshare";a:2:{i:0;s:17:"Share Your Screen";i:1;i:0;}s:7:"smilies";a:2:{i:0;s:5:"Emoji";i:1;i:0;}s:8:"stickers";a:2:{i:0;s:8:"Stickers";i:1;i:0;}s:5:"style";a:2:{i:0;s:15:"Color your text";i:1;i:2;}s:13:"transliterate";a:2:{i:0;s:22:"Write in your language";i:1;i:0;}s:10:"whiteboard";a:2:{i:0;s:25:"Share Whiteboard Document";i:1;i:0;}s:10:"writeboard";a:2:{i:0;s:28:"Share Collaborative Document";i:1;i:0;}s:9:"voicenote";a:2:{i:0;s:16:"Share Voice Note";i:1;i:0;}}','2' WHERE NOT EXISTS(SELECT * FROM upsert);

INSERT INTO cometchat_settings (setting_key, value, key_type) VALUES ('modules_core', 'a:11:{s:13:"announcements";a:9:{i:0;s:13:"announcements";i:1;s:13:"Announcements";i:2;s:31:"modules/announcements/index.php";i:3;s:6:"_popup";i:4;s:3:"280";i:5;s:3:"310";i:6;s:0:"";i:7;s:1:"1";i:8;s:0:"";}s:16:"broadcastmessage";a:9:{i:0;s:16:"broadcastmessage";i:1;s:17:"Broadcast Message";i:2;s:34:"modules/broadcastmessage/index.php";i:3;s:6:"_popup";i:4;s:3:"385";i:5;s:3:"300";i:6;s:0:"";i:7;s:1:"1";i:8;s:0:"";}s:9:"chatrooms";a:9:{i:0;s:9:"chatrooms";i:1;s:9:"Chatrooms";i:2;s:27:"modules/chatrooms/index.php";i:3;s:6:"_popup";i:4;s:3:"600";i:5;s:3:"300";i:6;s:0:"";i:7;s:1:"1";i:8;s:1:"1";}s:8:"facebook";a:9:{i:0;s:8:"facebook";i:1;s:17:"Facebook Fan Page";i:2;s:26:"modules/facebook/index.php";i:3;s:6:"_popup";i:4;s:3:"500";i:5;s:3:"460";i:6;s:0:"";i:7;s:1:"1";i:8;s:0:"";}s:5:"games";a:9:{i:0;s:5:"games";i:1;s:19:"Single Player Games";i:2;s:23:"modules/games/index.php";i:3;s:6:"_popup";i:4;s:3:"465";i:5;s:3:"300";i:6;s:0:"";i:7;s:1:"1";i:8;s:0:"";}s:4:"home";a:8:{i:0;s:4:"home";i:1;s:4:"Home";i:2;s:1:"/";i:3;s:0:"";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";i:7;s:0:"";}s:17:"realtimetranslate";a:9:{i:0;s:17:"realtimetranslate";i:1;s:23:"Translate Conversations";i:2;s:35:"modules/realtimetranslate/index.php";i:3;s:6:"_popup";i:4;s:3:"280";i:5;s:3:"310";i:6;s:0:"";i:7;s:1:"1";i:8;s:0:"";}s:11:"scrolltotop";a:8:{i:0;s:11:"scrolltotop";i:1;s:13:"Scroll To Top";i:2;s:40:"javascript:jqcc.cometchat.scrollToTop();";i:3;s:0:"";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";i:7;s:0:"";}s:5:"share";a:8:{i:0;s:5:"share";i:1;s:15:"Share This Page";i:2;s:23:"modules/share/index.php";i:3;s:6:"_popup";i:4;s:3:"350";i:5;s:2:"50";i:6;s:0:"";i:7;s:1:"1";}s:9:"translate";a:9:{i:0;s:9:"translate";i:1;s:19:"Translate This Page";i:2;s:27:"modules/translate/index.php";i:3;s:6:"_popup";i:4;s:3:"280";i:5;s:3:"310";i:6;s:0:"";i:7;s:1:"1";i:8;s:0:"";}s:7:"twitter";a:8:{i:0;s:7:"twitter";i:1;s:7:"Twitter";i:2;s:25:"modules/twitter/index.php";i:3;s:6:"_popup";i:4;s:3:"500";i:5;s:3:"300";i:6;s:0:"";i:7;s:1:"1";}}', 2);

CREATE TABLE IF NOT EXISTS cometchat_languages (
  lang_key varchar(255),
  lang_text text ,
  code varchar(20),
  type varchar(20),
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
  avatar varchar(200) NOT NULL,
  link varchar(200) NOT NULL,
  grp varchar(25) NOT NULL,
  friends text NOT NULL,
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

DROP TABLE cometchat_settings_old;

WITH upsert AS (UPDATE cometchat_settings SET value = '14', key_type = '1' WHERE setting_key = 'dbversion' RETURNING *) INSERT INTO cometchat_settings (setting_key , value, key_type) SELECT 'dbversion', '14', '1' WHERE NOT EXISTS (SELECT * FROM upsert);



EOD;
$q = preg_split('/;[\r\n]+/',$content);
if(!isset($errors)){
  $errors='';
}
foreach ($q as $query) {
  if (strlen($query) > 4) {
    if (!sql_query($query, array(), 1)) {
      $errors .= sql_error($dbh)."<br/>\n";
    }
  }
}

?>
