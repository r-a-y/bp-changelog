<?php

/** SETTINGS *************************************************************/

// Parse CLI arguments into the $_GET global.
if ( isset( $argv ) ) {
	parse_str( implode( '&', array_slice( $argv, 1 ) ), $_GET );
}

// BP version number
$milestone = $_GET['milestone'];
if ( empty( $milestone ) ) {
	die( 'Error: Missing "milestone" parameter' );
}

// BP DB version number
// this is the "db_version" number in /src/bp-loader.php
$db = ! empty( $_GET['db'] ) ? $_GET['db'] : 'XXX';

// Last trac revision for the milestone
$rev = ! empty( $_GET['rev'] ) ? $_GET['rev'] : 'XXX';

/** OUTPUT ***************************************************************/

ob_start();
?>

<p>Version <?php echo $milestone; ?> is a major BuddyPress feature release.</p>
<p>For Version <?php echo $milestone; ?>, the database version (<code>_bp_db_version</code> in <code>wp_options</code>) was <code><?php echo $db; ?></code>, and the Trac revision was <code><?php echo $rev; ?></code>. Read the full ticket log <a href="https://buddypress.trac.wordpress.org/milestone/<?php echo $milestone; ?>">here</a>.</p>

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

// Set user agent to bypass 403 Forbidden.
@ini_set('user_agent','Mozilla/5.0 (Windows NT 10.0; rv:78.0) Gecko/20100101 Firefox/78.0');

require 'vendor/autoload.php';
use DOMWrap\Document;

$dom = new Document();
$opts = array(
  'http'=>array(
    'user_agent' => 'My company name',
    'method'=>"GET",
    'header'=> implode("\r\n", array(
      'Content-type: text/plain;'
    ))
  )
);

$context = stream_context_create($opts);
$dom->html( file_get_contents( "https://buddypress.trac.wordpress.org/query?status=closed&milestone={$milestone}&group=component&max=99999&col=id&col=summary&order=priority",false, $context ) );
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
