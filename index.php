<?php

/** SETTINGS *************************************************************/

// BP version number
$v = '2.3.0';

// BP DB version number
// this is the "db_version" number in /src/bp-loader.php
$db = '9848';

// changesets
$first_rev = '9443'; // 2.3 begins
$last_rev  = '9895'; // 2.3 ends

/** OUTPUT ***************************************************************/
?>

	<p>Version <?php echo $v; ?> is a major BuddyPress feature release.</p>
	<p>For Version <?php echo $v; ?>, the database version (_bp_db_version in wp_options) was <?php echo $db; ?>, and the Trac revision was <?php echo $last_rev; ?>. Read the full ticket log here <a href="https://buddypress.trac.wordpress.org/milestone/<?php echo $v; ?>">here</a>.</p>

<!-- highlights -->

<h2 id="highlights"><a href="#highlights">Highlights</a></h2>
<ul>
</ul>

<!-- user features -->

<h2 id="user-features"><a href="#user-features">User Features</a></h2>

<h3 id="activity"><a href="#activity">Activity</a></h3>
<ul>
<li></li>
</ul>

<h3 id="administration"><a href="#administration">Administration</a></h3>
<ul>
<li></li>
</ul>

<h3 id="blogs"><a href="#blogs">Blogs</a></h3>
<ul>
<li></li>
</ul>

<h3 id="core"><a href="#core">Core</a></h3>
<ul>
<li></li>
</ul>

<h3 id="friends"><a href="#friends">Friends</a></h3>
<ul>
<li></li>
</ul>

<h3 id="general"><a href="#general">General</a></h3>
<ul>
<li></li>
</ul>

<h3 id="groups"><a href="#groups">Groups</a></h3>
<ul>
<li></li>
</ul>

<h3 id="members"><a href="#members">Members</a></h3>
<ul>
<li></li>
</ul>

<h3 id="messages"><a href="#messages">Messages</a></h3>
<ul>
<li></li>
</ul>

<h3 id="notifications"><a href="#notifications">Notifications</a></h3>
<ul>
<li></li>
</ul>

<h3 id="settings"><a href="#settings">Settings</a></h3>
<ul>
<li></li>
</ul>

<h3 id="theme"><a href="#theme">Theme</a></h3>
<ul>
<li></li>
</ul>

<!-- dev -->

<h2 id="development-themes-plugins"><a href="#development-themes-plugins">Development, Themes, Plugins</a></h2>

<h3 id="dev-activity"><a href="#dev-activity">Activity</a></h3>
<ul>
<li></li>
</ul>

<h3 id="dev-administration"><a href="#dev-administration">Administration</a></h3>
<ul>
<li></li>
</ul>

<h3 id="dev-blogs"><a href="#dev-blogs">Blogs</a></h3>
<ul>
<li></li>
</ul>

<h3 id="dev-core"><a href="#dev-core">Core</a></h3>
<ul>
<li></li>
</ul>

<h3 id="dev-friends"><a href="#dev-friends">Friends</a></h3>
<ul>
<li></li>
</ul>

<h3 id="dev-general"><a href="#dev-general">General</a></h3>
<ul>
<li></li>
</ul>

<h3 id="dev-groups"><a href="#dev-groups">Groups</a></h3>
<ul>
<li></li>
</ul>

<h3 id="dev-members"><a href="#dev-members">Members</a></h3>
<ul>
<li></li>
</ul>

<h3 id="dev-messages"><a href="#dev-messages">Messages</a></h3>
<ul>
<li></li>
</ul>

<h3 id="dev-notifications"><a href="#dev-notifications">Notifications</a></h3>
<ul>
<li></li>
</ul>

<h3 id="dev-settings"><a href="#dev-settings">Settings</a></h3>
<ul>
<li></li>
</ul>

<h3 id="dev-theme"><a href="#dev-theme">Theme</a></h3>
<ul>
<li></li>
</ul>

<!-- REMOVE THIS WHEN DONE -->

<hr />

<p><strong>MOVE THE FOLLOWING INTO THE SECTIONS ABOVE</strong> - use your best judgment</p>

<p><strong>MODIFY CONTENT TO SUIT USER CONSUMPTION</strong> (eg. remove "props ...", "see ...", "fixes ..."; merge multiple commits referencing the same ticket; remove commits referencing the older version branch; rephrase or summarize where needed, etc.)</p>

<p><strong>REMOVE THIS BLOCK ONCE YOU'RE DONE</strong></p>

<style type="text/css">
li {margin-bottom: 1em;}
ul li {margin-bottom: .5em;}
</style>

<hr />

<!-- END REMOVE -->

<?php
/** PHPQUERY **************************************************************/

/**
 * Load the phpQuery library.
 *
 * Parse HTML using jQuery syntax.
 *
 * @link https://github.com/phpquery/phpquery
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */
require 'phpQuery.php';

// parse Trac log
phpQuery::newDocumentFileHTML( "https://buddypress.trac.wordpress.org/log/?action=stop_on_copy&mode=stop_on_copy&rev={$last_rev}&stop_rev={$first_rev}&limit=999&verbose=on" );

// get all changeset links
$changesets = array();
foreach ( pq( 'a.chgset' ) as $changeset ) {
	// grab changeset number
	$n = str_replace(
		array( '/changeset/', '/' ),
		array( '', '' ),
		pq( $changeset )->attr( 'href' )
	);

	$changesets[] = '(<a href="https://buddypress.trac.wordpress.org' . pq( $changeset )->attr( 'href' ) . '">r' . $n . '</a>)';
}

// get all commit messages
$msgs = array();
foreach ( pq( 'td.log' ) as $msg ) {
	$msgs[] = pq( $msg )->html();
}

// iterate commit messages and output!
foreach ( $msgs as $key => $msg ) {
	// replace relative links with absolute links
	// replace <tt> tag with <code>
	$msg = str_replace(
		array( 'href="', '<tt>', '</tt>' ),
		array( 'href="https://buddypress.trac.wordpress.org', '<code>', '</code>' ),
		$msg
	);

	// strip breakline tags
	$msg = strip_tags( $msg, '<p><code><a><ul><li>' );

	// output time!
	echo '
<li>' . $msg;
	// add changeset to the very end of commit msg
	// handy if you want to keep the changeset in the changelog
	echo ' ' . $changesets[$key];
	echo '
</li>';
}
