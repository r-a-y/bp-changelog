## BuddyPress Changelog Generator

Takes all relevant commits from BuddyPress Trac and renders it to a format suitable for a changelog post on the BuddyPress codex.

### How to use

- Clone this repo
- Install Composer if you do not already have it
- Run `composer install` from the directory you cloned this repo to
- Next, either:
----

#### (1) Run script through PHP CLI
  - Run `php changelog.php milestone=2.9 db=123 rev=123`

#### (2) Run through web server
  - If you cloned this to a web server, go to `http://example.com/bp-changelog/changelog.php?milestone=2.9&db=123&rev=123`

----

The parameters are outlined below:
- (required) `milestone` is the milestone number used on Trac.  Check the [Trac Roadmap](https://buddypress.trac.wordpress.org/roadmap) if you are not sure of the current milestone number.
- (optional) `db` is the DB version located in `src/bp-loader.php`
- (optional) `rev` is the last Trac revision number before release

Once you have run the script, this will generate a file called `markup.txt` in the same directory.

Open `markup.txt` and copy the contents into the BP codex changelog post (eg. codex.buddypress.org/releases/version-2-X)

### Thanks
  - [PHP DOM Wrapper](https://github.com/scotteh/php-dom-wrapper) - A PHP library that parses HTML using jQuery's syntax.  Licensed under the BSD 3-Clause.