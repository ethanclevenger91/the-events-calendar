<?php
/**
 * View: Events Bar Views
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/tribe/events/views/v2/events-bar/views.php
 *
 * See more documentation about our views templating system.
 *
 * @link {INSERT_ARTCILE_LINK_HERE}
 *
 * @version 4.9.3
 *
 */
use Tribe\Events\Views\V2\Manager;

$public_views = tribe( Manager::class )->get_publicly_visible_views();
?>
<div class="tribe-events-c-events-bar__views">
	<h3 class="tribe-common-a11y-visual-hide">
		<?php printf( esc_html__( '%s Views Navigation', 'the-events-calendar' ), tribe_get_event_label_singular() ); ?>
	</h3>
	<div class="tribe-common-form-control-tabs tribe-events-c-events-bar__views-tabs">
		<button
			class="tribe-common-form-control-tabs__button tribe-events-c-events-bar__views-tabs-button"
			id="tribe-views-button"
			aria-haspopup="listbox"
			aria-labelledby="tribe-views-button"
			aria-expanded="true"
		>
			<?php esc_html_e( 'Views', 'the-events-calendar' ); ?>
		</button>
		<?php $this->template( 'events-bar/views/list', [ 'views' => $public_views ] ); ?>
	</div>
</div>
