drop table if exists `users`;
create table `users` (
	id_users INT not null auto_increment primary key,
	`email` varchar(100),
	`password` varchar(100),
	created_at timestamp default current_timestamp,
	UNIQUE (email)
);
