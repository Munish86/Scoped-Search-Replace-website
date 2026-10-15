<?php
/**
 * Messages page in the admin: contact form messages, CSV download, delete.
 *
 * @package scoped-sr-site
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'admin_menu',
	function () {
		add_menu_page(
			__( 'Messages', 'scoped-sr-site' ),
			__( 'Messages', 'scoped-sr-site' ),
			'manage_options',
			'srs-messages',
			'srs_render_messages_page',
			'dashicons-email-alt',
			26
		);
	}
);

/** The Messages page. */
function srs_render_messages_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	global $wpdb;
	$table = srs_table();
	// phpcs:disable WordPress.Security.NonceVerification.Recommended -- read-only view parameters.
	$paged = isset( $_GET['paged'] ) ? max( 1, absint( $_GET['paged'] ) ) : 1;
	$done  = isset( $_GET['srs_deleted'] );
	// phpcs:enable
	$per    = 50;
	$total  = (int) $wpdb->get_var( "SELECT COUNT(*) FROM $table" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	$rows   = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $table ORDER BY created_at DESC, id DESC LIMIT %d OFFSET %d", $per, ( $paged - 1 ) * $per ), ARRAY_A ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	$base   = admin_url( 'admin.php?page=srs-messages' );
	$export = wp_nonce_url( admin_url( 'admin-post.php?action=srs_export' ), 'srs_export' );
	$topics = srs_topic_choices();
	?>
	<div class="wrap">
		<h1 class="wp-heading-inline"><?php esc_html_e( 'Messages', 'scoped-sr-site' ); ?> <span class="count">(<?php echo (int) $total; ?>)</span></h1>
		<a href="<?php echo esc_url( $export ); ?>" class="page-title-action"><?php esc_html_e( 'Download CSV', 'scoped-sr-site' ); ?></a>
		<hr class="wp-header-end">
		<?php if ( $done ) : ?>
			<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Message deleted.', 'scoped-sr-site' ); ?></p></div>
		<?php endif; ?>
		<table class="widefat striped" style="margin-top:12px">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Date', 'scoped-sr-site' ); ?></th>
					<th><?php esc_html_e( 'From', 'scoped-sr-site' ); ?></th>
					<th><?php esc_html_e( 'Topic', 'scoped-sr-site' ); ?></th>
					<th><?php esc_html_e( 'Website', 'scoped-sr-site' ); ?></th>
					<th style="width:45%"><?php esc_html_e( 'Message', 'scoped-sr-site' ); ?></th>
					<th></th>
				</tr>
			</thead>
			<tbody>
			<?php if ( ! $rows ) : ?>
				<tr><td colspan="6"><?php esc_html_e( 'Nothing yet.', 'scoped-sr-site' ); ?></td></tr>
			<?php endif; ?>
			<?php foreach ( $rows as $r ) : ?>
				<?php $del = wp_nonce_url( admin_url( 'admin-post.php?action=srs_delete&id=' . (int) $r['id'] ), 'srs_delete_' . (int) $r['id'] ); ?>
				<tr>
					<td><?php echo esc_html( get_date_from_gmt( $r['created_at'], 'j M Y, H:i' ) ); ?></td>
					<td><?php echo esc_html( $r['name'] ); ?><br><a href="mailto:<?php echo esc_attr( $r['email'] ); ?>"><?php echo esc_html( $r['email'] ); ?></a></td>
					<td><?php echo esc_html( $topics[ $r['topic'] ] ?? '' ); ?></td>
					<td><?php echo esc_html( $r['website'] ); ?></td>
					<td><?php echo nl2br( esc_html( $r['message'] ) ); ?></td>
					<td><a href="<?php echo esc_url( $del ); ?>" onclick="return confirm('Delete this message?')" style="color:#b32d2e"><?php esc_html_e( 'Delete', 'scoped-sr-site' ); ?></a></td>
				</tr>
			<?php endforeach; ?>
			</tbody>
		</table>
		<?php
		$pages = (int) ceil( $total / $per );
		if ( $pages > 1 ) {
			echo '<div class="tablenav"><div class="tablenav-pages">';
			echo wp_kses_post(
				paginate_links(
					array(
						'base'    => add_query_arg( 'paged', '%#%', $base ),
						'format'  => '',
						'current' => $paged,
						'total'   => $pages,
					)
				)
			);
			echo '</div></div>';
		}
		?>
	</div>
	<?php
}

/**
 * A CSV cell that spreadsheet programs will not run as a formula.
 *
 * @param string $value Cell value.
 * @return string
 */
function srs_csv_cell( $value ) {
	$value = (string) $value;
	if ( '' !== $value && in_array( $value[0], array( '=', '+', '-', '@', "\t", "\r" ), true ) ) {
		$value = "'" . $value;
	}
	return $value;
}

add_action(
	'admin_post_srs_export',
	function () {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Not allowed.', 'scoped-sr-site' ), 403 );
		}
		check_admin_referer( 'srs_export' );
		global $wpdb;
		$table  = srs_table();
		$rows   = $wpdb->get_results( "SELECT * FROM $table ORDER BY created_at ASC, id ASC", ARRAY_A ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$topics = srs_topic_choices();

		nocache_headers();
		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename="scoped-sr-messages-' . gmdate( 'Y-m-d' ) . '.csv"' );
		header( 'X-Content-Type-Options: nosniff' );
		$out = fopen( 'php://output', 'w' );
		fwrite( $out, chr( 0xEF ) . chr( 0xBB ) . chr( 0xBF ) ); // BOM, so Excel reads UTF-8.
		fputcsv( $out, array( 'Date (UTC)', 'Email', 'Name', 'Topic', 'Website', 'Message', 'Page' ) );
		foreach ( $rows as $r ) {
			fputcsv(
				$out,
				array_map(
					'srs_csv_cell',
					array( $r['created_at'], $r['email'], $r['name'], $topics[ $r['topic'] ] ?? '', $r['website'], $r['message'], $r['page'] )
				)
			);
		}
		fclose( $out ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose
		exit;
	}
);

add_action(
	'admin_post_srs_delete',
	function () {
		$id = isset( $_GET['id'] ) ? absint( $_GET['id'] ) : 0;
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Not allowed.', 'scoped-sr-site' ), 403 );
		}
		check_admin_referer( 'srs_delete_' . $id );
		global $wpdb;
		$wpdb->delete( srs_table(), array( 'id' => $id ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
		wp_safe_redirect( add_query_arg( 'srs_deleted', 1, admin_url( 'admin.php?page=srs-messages' ) ) );
		exit;
	}
);
