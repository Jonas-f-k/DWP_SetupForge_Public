# SetupForge

php site to share desk / pc setups.

working rn:
- login + register
- upload pics
- posts, likes, comments
- profiles
- some admin stuff

still todo:
- featured pics
- more admin options
- site colours / themes
- post history
- check the other admin pages
- report stuff

setup:
import `sql/setup.sql` in a fresh db. it drops existing tables so dont run it on a db with data.
make your own `config/database.php` for the db login. dont upload that file to github.
`uploads/` needs write access.
