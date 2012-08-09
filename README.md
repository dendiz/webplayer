webplayer
=========
A web based mp3 player.

Installation
============

- clone/extract the code into a directory
- setup your virtual hosts (use the examples in config/ for apache)
- add the host names to /etc/hosts
- copy config/config.php.example to config/config.php and edit accordingly
- create a database (mysql)
- run the scripts in sql/ e.g 'cat *.sql | mysql -u root -pPassWord webplayer'
- run the cli application cli/scan.php -d <directory to your music files>
- optional: add an entry in your crontab for cli/artfetch.php to fetch your albums arts. (limited to 100 request per day)

Screenshot
==========
<a href="http://static.dendiz.com/webplayer-ss.png">
<img src="http://static.dendiz.com/webplayer-thumb.png">
</a>
