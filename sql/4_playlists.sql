drop table if exists `playlists`;
create table `playlists` (
	id_playlists INT not null auto_increment primary key,
	fk_id_user int,
	`name` varchar(100),
	created_at timestamp default current_timestamp,
	unique (fk_id_user, name),
	Foreign Key (fk_id_user) references users(id_users)
);
