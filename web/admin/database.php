<?php
$query = <<<eot
DROP TABLE IF EXISTS `{$tblprefix}_schools`;
CREATE TABLE `{$tblprefix}_schools`(
    `school_id` int NOT NULL primary key auto_increment,
    `school_name_cn` char(50),
    `school_name_en` char(200),
    `school_type` int
);

INSERT INTO `{$tblprefix}_schools` VALUES(NULL, "武汉大学", "Wuhan University", 7);

DROP TABLE IF EXISTS `{$tblprefix}_teams`;
CREATE TABLE `{$tblprefix}_teams`(
    `team_id` int NOT NULL primary key auto_increment,
    `school_id` int, 
    `team_name` char(50) ,
    `password` char(50) ,
    `vcode` char(10),
    `email` char(50),
    `address` char(100),
    `postcode` char(20),
    `telephone` char(20),
    `contact` char(100),
    `valid_for_final` int DEFAULT 1,  
    `pre_solved` int DEFAULT -1,
    `pre_penalty` int DEFAULT -1,
    `pre_rank` int DEFAULT -1,
    `final_id` int DEFAULT -1,  
    `final_solved` int DEFAULT -1,
    `final_penalty` int DEFAULT -1,
    `final_rank` int DEFAULT -1,
    `hotel_id` int DEFAULT -1, 
    `hotel_id1` int DEFAULT -1, 
    `hotel_id2` int DEFAULT -1,  
    `requirement` varchar(1000),
    `remark` varchar(1000)
);

DROP TABLE IF EXISTS `{$tblprefix}_members`;
CREATE TABLE `{$tblprefix}_members`(
    `member_id` int NOT NULL primary key auto_increment,
    `type` int,
    `team_id` int,
    `member_name` char(50),
    `member_name_pinyin` char(50),
    `gender` int,
    `school_id` int,  
    `faculty_major` char(50),
    `grade_class` char(50),
    `stu_number` char(50),
    `email` char(50),
    `telephone` char(20),
    `contact` char(100),
    `remark` varchar(1000)
);

DROP TABLE IF EXISTS `{$tblprefix}_articles`;
CREATE TABLE `{$tblprefix}_articles`(
    `article_id` int primary key auto_increment,
    `pub_time` int,
    `title` char(100),
    `content` text,
    `content_type` int DEFAULT 1, 
    `priority` int DEFAULT 0, 
    `permission` int DEFAULT 1, 
    `views` int DEFAULT 0
);

DROP TABLE IF EXISTS `{$tblprefix}_messages`;
CREATE TABLE `{$tblprefix}_messages`(
    `message_id` int primary key auto_increment,
    `pub_time` int,
    `from_id` int, 
    `to_id` int, 
    `message_content` varchar(1000),
    `read` int DEFAULT 0, 
    `replied` int DEFAULT 0
);

DROP TABLE IF EXISTS `{$tblprefix}_hotels`;
CREATE TABLE `{$tblprefix}_hotels`(
    `hotel_id` int primary key auto_increment,
    `address` char(50),
    `telephone` char(20),
    `online_map_pos` char(100), 
    `price` varchar(1000),
    `addition` varchar(1000)
);

eot;
?>
