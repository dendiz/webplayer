drop table if exists `mp3_items`;
create table `mp3_items` (
	id_mp3_items INT not null auto_increment primary key,
	`title` varchar(100),
	`artist` varchar(100),
	`album` varchar(100),
	`year` varchar(100),
	`comment` varchar(100),
	`track` varchar(100),
	`genre` varchar(100),
	`band` varchar(100),
	`composer` varchar(100),
	`publisher` varchar(100),
	`track_number` varchar(100),
	`filepath` varchar(255),
	`filename` varchar(255),
	hash varchar(40),
	filesize int,
	samplerate int,
	playtime_seconds int,
	created_at timestamp default current_timestamp,
	UNIQUE (filename)
);
