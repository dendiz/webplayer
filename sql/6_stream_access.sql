drop table if exists `stream_access`;
create table `stream_access` (
	id_stream_access INT not null auto_increment primary key,
	fk_id_user int,
	token varchar(100),
	used int(1) default 0,
	created_at timestamp default current_timestamp
);
