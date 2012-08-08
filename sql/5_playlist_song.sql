drop table if exists `playlist_song`;
create table `playlist_song` (
	fk_id_playlist int,
	fk_id_mp3_item int
);
