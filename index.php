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

ob_start();
?>

	<p>Version <?php echo $v; ?> is a major BuddyPress feature release.</p>
	<p>For Version <?php echo $v; ?>, the database version (_bp_db_version in wp_options) was <?php echo $db; ?>, and the Trac revision was <?php echo $last_rev; ?>. Read the full ticket log here <a href="https://buddypress.trac.wordpress.org/milestone/<?php echo $v; ?>">here</a>.</p>

<!-- highlights -->

<h2 id="highlights"><a href="#highlights">Highlights</a></h2>
<ul>
<li>FILL THIS IN!</li>
</ul>

<!-- tickets -->

<h2 id="changes"><a href="#changes">Changes</a></h2>

<?php
$html = ob_get_clean();

/** PHP DOM WRAPPER ******************************************************/

if ( false === file_exists( 'vendor/autoload.php' ) ) {
	die( 'Error: Please run "composer install" before running this script.' );
}

require 'vendor/autoload.php';
use DOMWrap\Document;

$dom = new Document();
$dom->html( file_get_contents( "https://buddypress.trac.wordpress.org/query?status=closed&milestone={$milestone}&group=component&col=id&col=summary&order=priority" ) );

$html .= '<h3 id="activity"><a href="#activity">Activity</a></h3>';

foreach ( $dom->find( 'table.tickets tbody' ) as $i => $elem ) {
	// Component tickets.
	$ticket_row = $elem->find( 'td' );
	if ( $ticket_row->count() ) {
		$html .= "<ul>\n";

		foreach ( $ticket_row as $j => $row ) {
			// Ticket number.
			if ( $row->hasClass( 'id' ) ) {
				$html .= "\t<li>" . str_replace( 'href="', 'target="_blank" href="https://buddypress.trac.wordpress.org', $row->html() );

			// Ticket title.
			} elseif ( $row->hasClass( 'summary' ) ) {
				// Handle backticks and convert to <code> HTML element.
				$title = strip_tags( $row->html() );
				$title = str_replace( '`, ', '</code>, ', $title );
				$title = str_replace( '` ', '</code> ', $title );
				if ( '`' === substr( $title, -1 ) ) {
					$title = substr_replace( $title, '</code>', -1, 7 );
				}
				$title = str_replace( '`', '<code>', $title );

				$html .= ' - ' . $title . "</li>\n";
			}
		}

		$html .= "</ul>\n\n";

	// Component heading.
	} else {
		$heading = $elem->find( 'th' );
		if ( $heading->count() ) {
			$h = strip_tags( $heading->html() );
			$h = trim( substr( $h, 0, strpos( $h, '(' ) ) );
			$h = str_replace( 'Component: ', '', $h );

			$class = filter_var( strtolower( $h ), FILTER_SANITIZE_EMAIL );

			$html .= "<h3 id='{$class}'><a href='#{$class}'>{$h}</a></h3>\n";
		}
	}
}

// Output contents into a .txt file.
file_put_contents( 'markup.txt', $html );

echo 'All done! Check markup.txt and copy that into a codex changelog post!';