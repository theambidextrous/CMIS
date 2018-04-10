<?php

$content = <<<EOD

IF  NOT EXISTS (SELECT * FROM sys.objects
    WHERE object_id = OBJECT_ID(N'[dbo].[cometchat_settings]') AND type in (N'U'))
  BEGIN
  CREATE TABLE [cometchat_settings] (
    [setting_key] varchar(50) NOT NULL PRIMARY KEY,
    [value] text NOT NULL,
    [key_type] tinyint NOT NULL DEFAULT 1
  )
  END;


CREATE TABLE  [cometchat] (
  [id] int IDENTITY(1,1) PRIMARY KEY,
  [from] int NOT NULL,
  [to] int NOT NULL,
  [message] text NOT NULL,
  [sent] int NOT NULL default '0',
  [read] tinyint  NOT NULL default '0',
  [direction] tinyint NOT NULL default '0',
) ;

IF  NOT EXISTS (SELECT * FROM sys.objects
  WHERE object_id = OBJECT_ID(N'[dbo].[cometchat_announcements]') AND type in (N'U'))
BEGIN
CREATE TABLE [cometchat_announcements] (
  [id] int NOT NULL IDENTITY(1,1) PRIMARY KEY,
  [announcement] text NOT NULL,
  [time] int NOT NULL,
  [to] int NOT NULL,
  [recd] int NOT NULL DEFAULT 0
)
END;

IF  NOT EXISTS (SELECT * FROM sys.objects
  WHERE object_id = OBJECT_ID(N'[dbo].[cometchat_chatroommessages]') AND type in (N'U'))
BEGIN
CREATE TABLE [cometchat_chatroommessages] (
  [id] int NOT NULL IDENTITY(1,1) PRIMARY KEY,
  [userid] int NOT NULL,
  [chatroomid] int NOT NULL,
  [message] text NOT NULL,
  [sent] int NOT NULL
)
END;

IF  NOT EXISTS (SELECT * FROM sys.objects
  WHERE object_id = OBJECT_ID(N'[dbo].[cometchat_chatrooms]') AND type in (N'U'))
BEGIN
CREATE TABLE [cometchat_chatrooms] (
  [id] int NOT NULL IDENTITY(1,1) PRIMARY KEY,
  [name] varchar(255) NOT NULL,
  [lastactivity] int NOT NULL,
  [createdby] int NOT NULL,
  [password] varchar(255) NOT NULL,
  [type] tinyint NOT NULL,
  [vidsession] varchar(512) default NULL,
  [invitedusers] varchar(512) default NULL
)
END;

IF  NOT EXISTS (SELECT * FROM sys.objects
    WHERE object_id = OBJECT_ID(N'[dbo].[cometchat_chatrooms_users]') AND type in (N'U'))
BEGIN
  CREATE TABLE cometchat_chatrooms_users (
  [userid] int,
  [chatroomid] int NOT NULL,
  [lastactivity] int default 0,
  [isbanned] int default 0,
  primary key (userid, chatroomid)
)
END;

IF  NOT EXISTS (SELECT * FROM sys.objects
  WHERE object_id = OBJECT_ID(N'[dbo].[cometchat_status]') AND type in (N'U'))
BEGIN
CREATE TABLE [cometchat_status] (
  [userid] int NOT NULL PRIMARY KEY,
  [message] nvarchar(255),
  [status] nvarchar(50) check (status  in ('available','away','busy','invisible','offline')) default NULL ,
  [typingto] int default NULL,
  [typingtime] int default NULL,
  [isdevice] int NOT NULL default '0',
  [lastactivity] int NOT NULL default '0',
  [lastseen] int NOT NULL default '0',
  [lastseensetting] int NOT NULL default '0'
)
END;

IF  NOT EXISTS (SELECT * FROM sys.objects
  WHERE object_id = OBJECT_ID(N'[dbo].[cometchat_videochatsessions]') AND type in (N'U'))
BEGIN
CREATE TABLE [cometchat_videochatsessions] (
[username] varchar(255) NOT NULL PRIMARY KEY,
[identity] varchar(255) NOT NULL,
[timestamp] int default NULL
)
END;

IF  NOT EXISTS (SELECT * FROM sys.objects
  WHERE object_id = OBJECT_ID(N'[dbo].[cometchat_block]') AND type in (N'U'))
BEGIN
CREATE TABLE [cometchat_block] (
  [id] int NOT NULL IDENTITY(1,1) PRIMARY KEY,
  [fromid] int NOT NULL,
  [toid] int NOT NULL
)
END;

IF  NOT EXISTS (SELECT * FROM sys.objects
  WHERE object_id = OBJECT_ID(N'[dbo].[cometchat_guests]') AND type in (N'U'))
BEGIN
CREATE TABLE [cometchat_guests] (
  [id] int NOT NULL IDENTITY(10000001,1) PRIMARY KEY,
  [name] varchar(255) NOT NULL
)
END;



INSERT INTO cometchat_guests (name) VALUES ('guest-10000000');


IF  NOT EXISTS (SELECT * FROM sys.objects
  WHERE object_id = OBJECT_ID(N'[dbo].[cometchat_session]') AND type in (N'U'))
BEGIN
CREATE TABLE [cometchat_session] (
  [session_id] char(32) NOT NULL PRIMARY KEY,
  [session_data] text NOT NULL,
  [session_lastaccesstime] varchar(50) NOT NULL DEFAULT CURRENT_TIMESTAMP
)
END;

IF (EXISTS (SELECT * FROM [cometchat_settings] WHERE setting_key = 'extensions_core'))
        begin
          UPDATE [cometchat_settings] SET value = 'a:5:{s:3:"ads";s:14:"Advertisements";s:6:"jabber";s:10:"Gtalk Chat";s:9:"mobileapp";s:9:"Mobileapp";s:7:"desktop";s:7:"Desktop";s:4:"bots";s:4:"Bots";}'  WHERE setting_key = 'extensions_core'
        end
        else
        begin

          INSERT INTO [cometchat_settings] (setting_key, value, key_type) VALUES ('extensions_core', 'a:4:{s:3:"ads";s:14:"Advertisements";s:9:"mobileapp";s:9:"Mobileapp";s:7:"desktop";s:7:"Desktop";s:4:"bots";s:4:"Bots";}', 2)
        end;

IF (EXISTS (SELECT * FROM [cometchat_settings] WHERE setting_key = 'plugins_core'))
        begin
          UPDATE [cometchat_settings] SET value = 'a:18:{s:9:"audiochat";a:2:{i:0;s:10:"Audio Chat";i:1;i:0;}s:6:"avchat";a:2:{i:0;s:16:"Audio/Video Chat";i:1;i:0;}s:5:"block";a:2:{i:0;s:10:"Block User";i:1;i:1;}s:9:"broadcast";a:2:{i:0;s:21:"Audio/Video Broadcast";i:1;i:0;}s:11:"chathistory";a:2:{i:0;s:12:"Chat History";i:1;i:0;}s:17:"clearconversation";a:2:{i:0;s:18:"Clear Conversation";i:1;i:0;}s:12:"filetransfer";a:2:{i:0;s:11:"Send a file";i:1;i:0;}s:9:"handwrite";a:2:{i:0;s:19:"Handwrite a message";i:1;i:0;}s:6:"report";a:2:{i:0;s:19:"Report Conversation";i:1;i:1;}s:4:"save";a:2:{i:0;s:17:"Save Conversation";i:1;i:0;}s:11:"screenshare";a:2:{i:0;s:17:"Share Your Screen";i:1;i:0;}s:7:"smilies";a:2:{i:0;s:5:"Emoji";i:1;i:0;}s:8:"stickers";a:2:{i:0;s:8:"Stickers";i:1;i:0;}s:5:"style";a:2:{i:0;s:15:"Color your text";i:1;i:2;}s:13:"transliterate";a:2:{i:0;s:22:"Write in your language";i:1;i:0;}s:10:"whiteboard";a:2:{i:0;s:25:"Share Whiteboard Document";i:1;i:0;}s:10:"writeboard";a:2:{i:0;s:28:"Share Collaborative Document";i:1;i:0;}s:9:"voicenote";a:2:{i:0;s:16:"Share Voice Note";i:1;i:0;}}'  WHERE setting_key = 'plugins_core'
        end
        else
        begin

          INSERT INTO [cometchat_settings] (setting_key, value, key_type) VALUES ('plugins_core', 'a:18:{s:9:"audiochat";a:2:{i:0;s:10:"Audio Chat";i:1;i:0;}s:6:"avchat";a:2:{i:0;s:16:"Audio/Video Chat";i:1;i:0;}s:5:"block";a:2:{i:0;s:10:"Block User";i:1;i:1;}s:9:"broadcast";a:2:{i:0;s:21:"Audio/Video Broadcast";i:1;i:0;}s:11:"chathistory";a:2:{i:0;s:12:"Chat History";i:1;i:0;}s:17:"clearconversation";a:2:{i:0;s:18:"Clear Conversation";i:1;i:0;}s:12:"filetransfer";a:2:{i:0;s:11:"Send a file";i:1;i:0;}s:9:"handwrite";a:2:{i:0;s:19:"Handwrite a message";i:1;i:0;}s:6:"report";a:2:{i:0;s:19:"Report Conversation";i:1;i:1;}s:4:"save";a:2:{i:0;s:17:"Save Conversation";i:1;i:0;}s:11:"screenshare";a:2:{i:0;s:17:"Share Your Screen";i:1;i:0;}s:7:"smilies";a:2:{i:0;s:5:"Emoji";i:1;i:0;}s:8:"stickers";a:2:{i:0;s:8:"Stickers";i:1;i:0;}s:5:"style";a:2:{i:0;s:15:"Color your text";i:1;i:2;}s:13:"transliterate";a:2:{i:0;s:22:"Write in your language";i:1;i:0;}s:10:"whiteboard";a:2:{i:0;s:25:"Share Whiteboard Document";i:1;i:0;}s:10:"writeboard";a:2:{i:0;s:28:"Share Collaborative Document";i:1;i:0;}s:9:"voicenote";a:2:{i:0;s:16:"Share Voice Note";i:1;i:0;}}', 2)

        end;


INSERT INTO [cometchat_settings] (setting_key, value, key_type) VALUES ('modules_core', 'a:11:{s:13:"announcements";a:9:{i:0;s:13:"announcements";i:1;s:13:"Announcements";i:2;s:31:"modules/announcements/index.php";i:3;s:6:"_popup";i:4;s:3:"280";i:5;s:3:"310";i:6;s:0:"";i:7;s:1:"1";i:8;s:0:"";}s:16:"broadcastmessage";a:9:{i:0;s:16:"broadcastmessage";i:1;s:17:"Broadcast Message";i:2;s:34:"modules/broadcastmessage/index.php";i:3;s:6:"_popup";i:4;s:3:"385";i:5;s:3:"300";i:6;s:0:"";i:7;s:1:"1";i:8;s:0:"";}s:9:"chatrooms";a:9:{i:0;s:9:"chatrooms";i:1;s:9:"Chatrooms";i:2;s:27:"modules/chatrooms/index.php";i:3;s:6:"_popup";i:4;s:3:"600";i:5;s:3:"300";i:6;s:0:"";i:7;s:1:"1";i:8;s:1:"1";}s:8:"facebook";a:9:{i:0;s:8:"facebook";i:1;s:17:"Facebook Fan Page";i:2;s:26:"modules/facebook/index.php";i:3;s:6:"_popup";i:4;s:3:"500";i:5;s:3:"460";i:6;s:0:"";i:7;s:1:"1";i:8;s:0:"";}s:5:"games";a:9:{i:0;s:5:"games";i:1;s:19:"Single Player Games";i:2;s:23:"modules/games/index.php";i:3;s:6:"_popup";i:4;s:3:"465";i:5;s:3:"300";i:6;s:0:"";i:7;s:1:"1";i:8;s:0:"";}s:4:"home";a:8:{i:0;s:4:"home";i:1;s:4:"Home";i:2;s:1:"/";i:3;s:0:"";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";i:7;s:0:"";}s:17:"realtimetranslate";a:9:{i:0;s:17:"realtimetranslate";i:1;s:23:"Translate Conversations";i:2;s:35:"modules/realtimetranslate/index.php";i:3;s:6:"_popup";i:4;s:3:"280";i:5;s:3:"310";i:6;s:0:"";i:7;s:1:"1";i:8;s:0:"";}s:11:"scrolltotop";a:8:{i:0;s:11:"scrolltotop";i:1;s:13:"Scroll To Top";i:2;s:40:"javascript:jqcc.cometchat.scrollToTop();";i:3;s:0:"";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";i:7;s:0:"";}s:5:"share";a:8:{i:0;s:5:"share";i:1;s:15:"Share This Page";i:2;s:23:"modules/share/index.php";i:3;s:6:"_popup";i:4;s:3:"350";i:5;s:2:"50";i:6;s:0:"";i:7;s:1:"1";}s:9:"translate";a:9:{i:0;s:9:"translate";i:1;s:19:"Translate This Page";i:2;s:27:"modules/translate/index.php";i:3;s:6:"_popup";i:4;s:3:"280";i:5;s:3:"310";i:6;s:0:"";i:7;s:1:"1";i:8;s:0:"";}s:7:"twitter";a:8:{i:0;s:7:"twitter";i:1;s:7:"Twitter";i:2;s:25:"modules/twitter/index.php";i:3;s:6:"_popup";i:4;s:3:"500";i:5;s:3:"300";i:6;s:0:"";i:7;s:1:"1";}}', 2);


IF  NOT EXISTS (SELECT * FROM sys.objects
  WHERE object_id = OBJECT_ID(N'[dbo].[cometchat_languages]') AND type in (N'U'))
BEGIN
CREATE TABLE [cometchat_languages] (
  [lang_key] varchar(255) NOT NULL,
  [lang_text] varchar(255) NOT NULL,
  [code] varchar(20) NOT NULL,
  [type] varchar(20) NOT NULL,
  [name] varchar(50) NOT NULL
)
END;


ALTER TABLE [cometchat_languages]
  ADD CONSTRAINT lang_index UNIQUE (lang_key,code,type,name);


INSERT INTO [cometchat_languages] (lang_key, lang_text, code, type, name) VALUES ('rtl', '0', 'en', 'core', 'default');


IF  NOT EXISTS (SELECT * FROM sys.objects
  WHERE object_id = OBJECT_ID(N'[dbo].[cometchat_colors]') AND type in (N'U'))
BEGIN
CREATE TABLE [cometchat_colors] (
  [color_key] varchar(100) NOT NULL,
  [color_value] text NOT NULL,
  [color] varchar(50) NOT NULL
)
END;


ALTER TABLE [cometchat_colors]
   ADD CONSTRAINT color_index UNIQUE (color_key,color);


INSERT INTO [cometchat_colors] (color_key, color_value, color) VALUES
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


DELETE FROM [cometchat_colors] WHERE color_key NOT LIKE 'color%';


IF ( EXISTS (SELECT * FROM [cometchat_settings] WHERE 'setting_key' = 'theme' AND 'value' = 'docked' AND 'key_type' = 1 ))
begin
  UPDATE [cometchat_settings] set value = 'docked'
end
else
begin
  INSERT cometchat_settings ([setting_key], [value], [key_type]) VALUES ('theme','docked',1)
end


IF ( EXISTS (SELECT * FROM [cometchat_settings] WHERE 'setting_key' = 'color' AND 'value' = 'color1' AND 'key_type' = 1 ))
begin
  UPDATE [cometchat_settings] set value = 'color1'
end
else
begin
  INSERT cometchat_settings ([setting_key], [value], [key_type]) VALUES ('color','color1',1)
end


IF  NOT EXISTS (SELECT * FROM sys.objects
  WHERE object_id = OBJECT_ID(N'[dbo].[cometchat_users]') AND type in (N'U'))
BEGIN
CREATE TABLE [cometchat_users] (
  [userid] int NOT NULL IDENTITY(1,1) PRIMARY KEY,
  [username] varchar(100) NOT NULL UNIQUE,
  [displayname] varchar(100) NOT NULL,
  [password] varchar(100) NOT NULL,
  [avatar] varchar(200) NOT NULL,
  [link] varchar(200) NOT NULL,
  [grp] varchar(25) NOT NULL,
  [friends] varchar(25) NOT NULL,
  [uid] varchar(255) NOT NULL
)
END;

IF  NOT EXISTS (SELECT * FROM sys.objects
  WHERE object_id = OBJECT_ID(N'[dbo].[cometchat_bots]') AND type in (N'U'))
BEGIN
CREATE TABLE [cometchat_bots] (
  [id] int NOT NULL IDENTITY(1,1) PRIMARY KEY,
  [name] varchar(100) NOT NULL UNIQUE,
  [description] varchar(100) NOT NULL,
  [keywords] varchar(100) NOT NULL,
  [avatar] varchar(200) NOT NULL,
  [apikey] varchar(200) NOT NULL
)
END;


DROP TABLE dbo.cometchat_settings_old


IF ( EXISTS (SELECT * FROM [cometchat_settings] WHERE setting_key = 'dbversion'))
begin
  UPDATE [cometchat_settings] set value = '14' WHERE setting_key = 'dbversion'
end
else
begin
  INSERT cometchat_settings ([setting_key], [value], [key_type]) VALUES ('dbversion','14',1)
end

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
