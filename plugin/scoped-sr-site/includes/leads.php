<?php
/**
 * Contact form: [sr_contact], and [sr_support_email].
 * Messages are saved in the {prefix}sr_messages table and listed under Messages in the admin.
 * Spam protection without cookies or third parties: a hidden field, a signed time stamp and a per-IP limit.
 *
 * @package scoped-sr-site
 */

defined( 'ABSPATH' ) || exit;

/** Table name. */
function srs_table() {
	global $wpdb;
	return $wpdb->prefix . 'sr_messages';
}

/** Creates or upgrades the table. */
function srs_install() {
	global $wpdb;
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	$charset = $wpdb->get_charset_collate();
	$table   = srs_table();
	dbDelta(
		"CREATE TABLE $table (
		id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		topic varchar(20) NOT NULL DEFAULT '',
		email varchar(190) NOT NULL,
		name varchar(190) NOT NULL DEFAULT '',
		website varchar(190) NOT NULL DEFAULT '',
		message text NOT NULL,
		page varchar(190) NOT NULL DEFAULT '',
		created_at datetime NOT NULL,
		PRIMARY KEY  (id),
		KEY created (created_at),
		KEY email (email)
		) $charset;"
	);
	update_option( 'srs_db_version', SRS_DB_VERSION );
}

/** Topics shown in the contact form; stored by key. */
function srs_topic_choices() {
	return array(
		'question' => __( 'A question about the plugin', 'scoped-sr-site' ),
		'bug'      => __( 'Something does not work', 'scoped-sr-site' ),
		'idea'     => __( 'An idea or feature request', 'scoped-sr-site' ),
		'other'    => __( 'Something else', 'scoped-sr-site' ),
	);
}

/** Signed time stamp: forms posted in under 3 seconds or with a forged stamp are refused. */
function srs_stamp() {
	$t = (string) time();
	return $t . '.' . hash_hmac( 'sha256', $t, wp_salt( 'nonce' ) );
}

/**
 * Checks a posted time stamp.
 *
 * @param string $stamp Value from the form.
 * @return bool
 */
function srs_stamp_ok( $stamp ) {
	$parts = explode( '.', (string) $stamp, 2 );
	if ( 2 !== count( $parts ) || ! ctype_digit( $parts[0] ) ) {
		return false;
	}
	if ( ! hash_equals( hash_hmac( 'sha256', $parts[0], wp_salt( 'nonce' ) ), $parts[1] ) ) {
		return false;
	}
	$age = time() - (int) $parts[0];
	return $age >= 3 && $age <= 14 * DAY_IN_SECONDS;
}

/** Message shown above the form after it was sent (read from the redirect's query string). */
function srs_form_notice() {
	// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- display only.
	$status   = isset( $_GET['sr_status'] ) ? sanitize_key( $_GET['sr_status'] ) : '';
	$messages = array(
		'ok'      => __( 'Thanks, your message has been sent. We reply within one working day.', 'scoped-sr-site' ),
		'email'   => __( 'Please enter a valid email address.', 'scoped-sr-site' ),
		'message' => __( 'Please write a message (up to 5,000 characters).', 'scoped-sr-site' ),
		'limit'   => __( 'Too many messages from your connection in the last hour. Please try again later.', 'scoped-sr-site' ),
		'expired' => __( 'The form had been open too long or was sent too quickly. Please send it again.', 'scoped-sr-site' ),
	);
	if ( ! isset( $messages[ $status ] ) ) {
		return '';
	}
	$class = 'ok' === $status ? 'sr-alert sr-alert--ok' : 'sr-alert sr-alert--error';
	return '<div class="' . esc_attr( $class ) . '" role="status">' . esc_html( $messages[ $status ] ) . '</div>';
}

/** [sr_contact] */
function srs_contact_shortcode() {
	$topics = '';
	foreach ( srs_topic_choices() as $key => $label ) {
		$topics .= '<option value="' . esc_attr( $key ) . '">' . esc_html( $label ) . '</option>';
	}
	ob_start();
	?>
	<div id="sr-form-contact">
		<?php echo srs_form_notice(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped inside. ?>
		<form class="sr-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="srs_submit">
			<input type="hidden" name="sr_stamp" value="<?php echo esc_attr( srs_stamp() ); ?>">
			<input type="hidden" name="sr_page" value="<?php echo esc_attr( (string) get_queried_object_id() ); ?>">
			<div class="sr-hp" aria-hidden="true"><label>Website <input type="text" name="sr_homepage" tabindex="-1" autocomplete="off"></label></div>
			<div class="sr-row">
				<label><?php esc_html_e( 'Name', 'scoped-sr-site' ); ?>
					<input type="text" name="sr_name" maxlength="190" autocomplete="name">
				</label>
				<label><?php esc_html_e( 'Email', 'scoped-sr-site' ); ?>
					<input type="email" name="sr_email" required maxlength="190" autocomplete="email">
				</label>
			</div>
			<div class="sr-row">
				<label><?php esc_html_e( 'What is it about?', 'scoped-sr-site' ); ?>
					<select name="sr_topic"><?php echo $topics; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above. ?></select>
				</label>
				<label><?php esc_html_e( 'Your website', 'scoped-sr-site' ); ?> <span class="sr-opt"><?php esc_html_e( '(optional)', 'scoped-sr-site' ); ?></span>
					<input type="text" name="sr_website" maxlength="190" placeholder="example.com">
				</label>
			</div>
			<label><?php esc_html_e( 'Message', 'scoped-sr-site' ); ?>
				<textarea name="sr_message" required maxlength="5000" placeholder="<?php esc_attr_e( 'For a problem: your WordPress and PHP version, what you searched for, the scope you chose and what happened.', 'scoped-sr-site' ); ?>"></textarea>
			</label>
			<button type="submit"><?php esc_html_e( 'Send message', 'scoped-sr-site' ); ?></button>
			<p class="sr-fine"><?php esc_html_e( 'We use your details only to answer you. Never send passwords or database dumps.', 'scoped-sr-site' ); ?> <a href="<?php echo esc_url( home_url( '/privacy-policy/' ) ); ?>"><?php esc_html_e( 'Privacy policy', 'scoped-sr-site' ); ?></a></p>
		</form>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'sr_contact', 'srs_contact_shortcode' );

/** [sr_support_email]: the support address as a link, or a link to the contact page when none is set. */
function srs_support_email_shortcode() {
	$email = srs_setting( 'support_email' );
	if ( $email ) {
		return '<a href="mailto:' . esc_attr( antispambot( $email ) ) . '">' . esc_html( antispambot( $email ) ) . '</a>';
	}
	return '<a href="' . esc_url( home_url( '/contact/' ) ) . '">' . esc_html__( 'our contact form', 'scoped-sr-site' ) . '</a>';
}
add_shortcode( 'sr_support_email', 'srs_support_email_shortcode' );

/**
 * Sends the visitor back to the form with a status.
 *
 * @param string $status Status key for srs_form_notice().
 * @param int    $page   Page the form was on.
 */
function srs_back( $status, $page ) {
	$url = $page ? get_permalink( $page ) : '';
	if ( ! $url ) {
		$url = home_url( '/contact/' );
	}
	wp_safe_redirect( add_query_arg( 'sr_status', $status, $url ) . '#sr-form-contact', 303 );
	exit;
}

/**
 * Field from the posted form, cleaned and cut to a length.
 *
 * @param string $name Field name.
 * @param int    $max  Max characters.
 * @return string
 */
function srs_post_field( $name, $max = 190 ) {
	// phpcs:ignore WordPress.Security.NonceVerification.Missing -- public form; protected by the signed stamp, honeypot and rate limit.
	$value = isset( $_POST[ $name ] ) ? sanitize_text_field( wp_unslash( $_POST[ $name ] ) ) : '';
	return mb_substr( $value, 0, $max );
}

/** Handles the form (admin-post.php?action=srs_submit). */
function srs_handle_submit() {
	// phpcs:disable WordPress.Security.NonceVerification.Missing -- see srs_post_field().
	$page = isset( $_POST['sr_page'] ) ? absint( $_POST['sr_page'] ) : 0;

	// A filled honeypot is a bot: pretend it worked.
	if ( ! empty( $_POST['sr_homepage'] ) ) {
		srs_back( 'ok', $page );
	}
	if ( ! srs_stamp_ok( isset( $_POST['sr_stamp'] ) ? sanitize_text_field( wp_unslash( $_POST['sr_stamp'] ) ) : '' ) ) {
		srs_back( 'expired', $page );
	}

	$email = sanitize_email( srs_post_field( 'sr_email' ) );
	if ( ! is_email( $email ) ) {
		srs_back( 'email', $page );
	}

	$message = isset( $_POST['sr_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['sr_message'] ) ) : '';
	if ( '' === trim( $message ) || mb_strlen( $message ) > 5000 ) {
		srs_back( 'message', $page );
	}
	// phpcs:enable

	// At most 6 messages per hour from one connection. The IP itself is never stored.
	$ip    = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	$key   = 'srs_rl_' . substr( hash_hmac( 'sha256', $ip, wp_salt( 'auth' ) ), 0, 32 );
	$count = (int) get_transient( $key );
	if ( $count >= 6 ) {
		srs_back( 'limit', $page );
	}
	set_transient( $key, $count + 1, HOUR_IN_SECONDS );

	$topic = srs_post_field( 'sr_topic', 20 );
	$row   = array(
		'topic'      => isset( srs_topic_choices()[ $topic ] ) ? $topic : 'other',
		'email'      => $email,
		'name'       => srs_post_field( 'sr_name' ),
		'website'    => srs_post_field( 'sr_website' ),
		'message'    => $message,
		'page'       => $page ? (string) get_the_title( $page ) : '',
		'created_at' => current_time( 'mysql', true ),
	);

	global $wpdb;
	$wpdb->insert( srs_table(), $row ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
	srs_notify( $row );
	srs_back( 'ok', $page );
}
add_action( 'admin_post_nopriv_srs_submit', 'srs_handle_submit' );
add_action( 'admin_post_srs_submit', 'srs_handle_submit' );

/**
 * Emails a new message to the owner. Failure is fine: the message is already saved.
 *
 * @param array $row Saved row.
 */
function srs_notify( $row ) {
	$to = srs_setting( 'notify_email' );
	if ( ! $to ) {
		$to = get_option( 'admin_email' );
	}
	$lines = array(
		'Email: ' . $row['email'],
		'Name: ' . $row['name'],
		'Topic: ' . ( srs_topic_choices()[ $row['topic'] ] ?? '' ),
		'Website: ' . $row['website'],
		'Page: ' . $row['page'],
		'',
		$row['message'],
		'',
		'All messages: ' . admin_url( 'admin.php?page=srs-messages' ),
	);
	wp_mail( $to, 'New message from the Scoped Search & Replace website', implode( "\n", $lines ), array( 'Reply-To: ' . $row['email'] ) );
}
