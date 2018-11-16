<?php

/**
 * Allow including of Gutenberg Template
 *
 * @since TBD
 */
class Tribe__Events__Editor__Template extends Tribe__Template {
	/**
	 * Building of the Class template configuration
	 *
	 * @since TBD
	 */
	public function __construct() {
		$this->set_template_origin( tribe( 'tec.main' ) );
		// todo: update to the plugins directory only
		$this->set_template_folder( 'src/views' );

		$this->set_notices();

		// Configures this templating class extract variables
		$this->set_template_context_extract( true );

		// Uses the public folders
		$this->set_template_folder_lookup( true );
	}

	/**
	 * Return the attributes of the template
	 *
	 * @since TBD
	 *
	 * @param array $default_attributes
	 * @return array
	 */
	public function attributes( $default_attributes = array() ) {
		return wp_parse_args(
			$this->get( 'attributes', array() ),
			$default_attributes
		);
	}

	/**
	 * Return a specific attribute
	 *
	 * @since TBD
	 *
	 * @param  mixed $default
	 * @return mixed
	 */
	public function attr( $index, $default = null ) {

		$attribute = $this->get( array_merge( array( 'attributes' ), (array) $index ), array(), $default );

		return $attribute;

	}

	/**
	 * Set notices
	 *
	 * @since TBD
	 *
	 * @return void
	 */
	public function set_notices() {
		$events_label_singular_lowercase = tribe_get_event_label_singular_lowercase();

		if ( ! tribe_is_showing_all() && tribe_is_past_event() ) {
			Tribe__Notices::set_notice( 'event-past', sprintf( esc_html__( 'This %s has passed.', 'the-events-calendar' ), $events_label_singular_lowercase ) );
		}
	}
}

