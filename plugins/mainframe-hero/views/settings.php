<?php
/**
 * Settings screen: template maintenance and environment report.
 *
 * @package MainframeHero
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$manifests = \MFH\Templates::manifests();
$known     = get_option( \MFH\Templates::OPTION, array() );
$reseeded  = isset( $_GET['mfh'] ) && 'reseeded' === sanitize_key( wp_unslash( $_GET['mfh'] ) );
?>
<header class="mfh-masthead mfh-masthead--compact">
	<div class="mfh-masthead__mark" aria-hidden="true">
		<svg viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
			<circle cx="24" cy="24" r="8" />
			<path d="M24 4v8M24 36v8M4 24h8M36 24h8M10.3 10.3l5.7 5.7M32 32l5.7 5.7M37.7 10.3L32 16M16 32l-5.7 5.7" />
		</svg>
	</div>
	<div class="mfh-masthead__text">
		<h1>تنظیمات</h1>
		<p>نگه‌داریِ الگوها و گزارشِ محیط.</p>
	</div>
</header>

<div class="mfh-layout mfh-layout--single">
	<main class="mfh-content">

		<?php if ( $reseeded ) : ?>
			<div class="mfh-alert mfh-alert--ok">الگوها با نسخهٔ اصلی جایگزین شدند.</div>
		<?php endif; ?>

		<section id="templates">
			<h2><span class="mfh-num">۱</span> الگوهای درون‌ریزی‌شده</h2>

			<table class="mfh-table">
				<thead>
					<tr>
						<th>الگو</th>
						<th>گونه</th>
						<th>وضعیت</th>
						<th>عمل</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $manifests as $manifest ) : ?>
						<?php
						$post_id = \MFH\Templates::find( $manifest['slug'] );
						$record  = isset( $known[ $manifest['slug'] ] ) ? $known[ $manifest['slug'] ] : array();
						?>
						<tr>
							<td>
								<strong><?php echo esc_html( $manifest['title'] ); ?></strong>
								<?php if ( ! empty( $manifest['description'] ) ) : ?>
									<p class="mfh-table__sub"><?php echo esc_html( $manifest['description'] ); ?></p>
								<?php endif; ?>
							</td>
							<td><code><?php echo esc_html( $manifest['type'] ); ?></code></td>
							<td>
								<?php if ( $post_id ) : ?>
									<span class="mfh-badge mfh-badge--ok">در کتابخانه</span>
									<p class="mfh-table__sub"><?php echo esc_html( mfh_human_time( isset( $record['at'] ) ? $record['at'] : 0 ) ); ?></p>
								<?php else : ?>
									<span class="mfh-badge mfh-badge--warn">یافت نشد</span>
								<?php endif; ?>
							</td>
							<td>
								<?php if ( $post_id ) : ?>
									<a class="button button-small" href="<?php echo esc_url( admin_url( 'post.php?post=' . $post_id . '&action=elementor' ) ); ?>">ویرایش در المنتور</a>
								<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>

			<div class="mfh-note">
				<p class="mfh-note__k">بازگردانی چیست</p>
				<p>الگوی موجود را پاک می‌کند و نسخهٔ اصلیِ همراهِ افزونه را دوباره می‌سازد. هر تغییری که خودتان در آن داده باشید از بین می‌رود؛ اما پیوندها و متن‌هایِ داخلِ ویجت‌ها هم به حالتِ اول برمی‌گردند. اگر می‌خواهید تغییرات را نگه دارید، پیش از این کار یک کپی از الگو بسازید.</p>
			</div>

			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<input type="hidden" name="action" value="mfh_reseed" />
				<?php wp_nonce_field( 'mfh_reseed' ); ?>
				<?php submit_button( 'بازگردانیِ الگوها', 'secondary', 'submit', false ); ?>
			</form>
		</section>

		<section id="environment">
			<h2><span class="mfh-num">۲</span> گزارشِ محیط</h2>

			<table class="mfh-table">
				<thead><tr><th>مورد</th><th>مقدار</th><th>انتظار</th></tr></thead>
				<tbody>
					<tr>
						<td>نسخهٔ افزونه</td>
						<td><code><?php echo esc_html( MFH_VERSION ); ?></code></td>
						<td>—</td>
					</tr>
					<tr>
						<td>نسخهٔ پی‌اچ‌پی</td>
						<td><code><?php echo esc_html( PHP_VERSION ); ?></code></td>
						<td><?php echo esc_html( MFH_MIN_PHP ); ?> یا بالاتر</td>
					</tr>
					<tr>
						<td>نسخهٔ وردپرس</td>
						<td><code><?php echo esc_html( get_bloginfo( 'version' ) ); ?></code></td>
						<td>—</td>
					</tr>
					<tr>
						<td>المنتور</td>
						<td>
							<code><?php echo esc_html( mfh_elementor_version() ? mfh_elementor_version() : 'فعال نیست' ); ?></code>
						</td>
						<td><?php echo esc_html( MFH_MIN_ELEMENTOR ); ?> یا بالاتر</td>
					</tr>
					<tr>
						<td>نسخهٔ المنتور کافی است</td>
						<td><?php echo mfh_elementor_meets_minimum() ? '<span class="mfh-badge mfh-badge--ok">بله</span>' : '<span class="mfh-badge mfh-badge--warn">خیر</span>'; ?></td>
						<td>بله</td>
					</tr>
					<tr>
						<td>جهتِ سند</td>
						<td><code><?php echo esc_html( is_rtl() ? 'راست‌به‌چپ' : 'چپ‌به‌راست' ); ?></code></td>
						<td>—</td>
					</tr>
					<tr>
						<td>بستهٔ زبانِ بارگیری‌شده</td>
						<td><code><?php echo esc_html( is_textdomain_loaded( 'mainframe-hero' ) ? 'mainframe-hero' : 'بارگیری نشده' ); ?></code></td>
						<td>—</td>
					</tr>
				</tbody>
			</table>
		</section>

	</main>
</div>
