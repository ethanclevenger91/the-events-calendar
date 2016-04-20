<?php

class Tribe__Events__Organizer {
	const POSTTYPE = 'tribe_organizer';

	/**
	 * Args for organizer post type
	 * @var array
	 */
	public $post_type_args = array(
		'public'              => false,
		'rewrite'             => array( 'slug' => 'organizer', 'with_front' => false ),
		'show_ui'             => true,
		'show_in_menu'        => 0,
		'supports'            => array( 'title', 'editor' ),
		'capability_type'     => array( 'tribe_organizer', 'tribe_organizers' ),
		'map_meta_cap'        => true,
		'exclude_from_search' => true,
	);

	public static $valid_keys = array(
		'Organizer',
		'Phone',
		'Email',
		'Website',
	);

	public $singular_organizer_label;
	public $plural_organizer_label;

	protected static $instance;

	/**
	 * Returns a singleton of this class
	 *
	 * @return Tribe__Events__Linked_Posts
	 */
	public static function instance() {
		if ( ! self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Constructor!
	 */
	public function __construct() {
		$rewrite = Tribe__Events__Rewrite::instance();

		$this->singular_organizer_label                = $this->get_organizer_label_singular();
		$this->plural_organizer_label                  = $this->get_organizer_label_plural();

		$this->post_type_args['rewrite']['slug']   = $rewrite->prepare_slug( $this->singular_organizer_label, self::POSTTYPE, false );
		$this->post_type_args['show_in_nav_menus'] = class_exists( 'Tribe__Events__Pro__Main' ) ? true : false;
		$this->post_type_args['public']            = class_exists( 'Tribe__Events__Pro__Main' ) ? true : false;

		/**
		 * Provides an opportunity to modify the labels used for the organizer post type.
		 *
		 * @var array
		 */
		$this->post_type_args['labels'] = apply_filters( 'tribe_events_register_organizer_post_type_labels', array(
			'name'               => $this->plural_organizer_label,
			'singular_name'      => $this->singular_organizer_label,
			'add_new'            => esc_html__( 'Add New', 'the-events-calendar' ),
			'add_new_item'       => sprintf( esc_html__( 'Add New %s', 'the-events-calendar' ), $this->singular_organizer_label ),
			'edit_item'          => sprintf( esc_html__( 'Edit %s', 'the-events-calendar' ), $this->singular_organizer_label ),
			'new_item'           => sprintf( esc_html__( 'New %s', 'the-events-calendar' ), $this->singular_organizer_label ),
			'view_item'          => sprintf( esc_html__( 'View %s', 'the-events-calendar' ), $this->singular_organizer_label ),
			'search_items'       => sprintf( esc_html__( 'Search %s', 'the-events-calendar' ), $this->plural_organizer_label ),
			'not_found'          => sprintf( esc_html__( 'No %s found', 'the-events-calendar' ), strtolower( $this->plural_organizer_label ) ),
			'not_found_in_trash' => sprintf( esc_html__( 'No %s found in Trash', 'the-events-calendar' ), strtolower( $this->plural_organizer_label ) ),
		) );

		$this->register_post_type();

		add_filter( 'tribe_events_linked_post_id_field_index', array( $this, 'linked_post_id_field_index' ), 10, 2 );
		add_filter( 'tribe_events_linked_post_name_field_index', array( $this, 'linked_post_name_field_index' ), 10, 2 );
		add_filter( 'tribe_events_linked_post_type_container', array( $this, 'linked_post_type_container' ), 10, 2 );
		add_filter( 'tribe_events_linked_post_create_' . self::POSTTYPE, array( $this, 'save' ), 10, 5 );
		add_filter( 'tribe_events_linked_post_default', array( $this, 'linked_post_default' ), 10, 2 );
	}

	/**
	 * Registers the post type
	 */
	public function register_post_type() {
		register_post_type(
			self::POSTTYPE,
			apply_filters( 'tribe_events_register_organizer_type_args', $this->post_type_args )
		);
	}

	/**
	 * Allow users to specify their own singular label for Organizers
	 * @return string
	 */
	public function get_organizer_label_singular() {
		return apply_filters( 'tribe_organizer_label_singular', esc_html__( 'Organizer', 'the-events-calendar' ) );
	}

	/**
	 * Allow users to specify their own plural label for Organizers
	 *
	 * @return string
	 */
	public function get_organizer_label_plural() {
		return apply_filters( 'tribe_organizer_label_plural', esc_html__( 'Organizers', 'the-events-calendar' ) );
	}

	/**
	 * Filters the linked post id field
	 *
	 * @sinze 4.2
	 *
	 * @param string $id_field Field name of the field that will hold the ID
	 * @param string $post_type Post type of linked post
	 */
	public function linked_post_id_field_index( $id_field, $post_type ) {
		if ( self::POSTTYPE === $post_type ) {
			return 'OrganizerID';
		}

		return $id_field;
	}

	/**
	 * Filters the linked post name field
	 *
	 * @sinze 4.2
	 *
	 * @param string $name_field Field name of the field that will hold the post name
	 * @param string $post_type Post type of linked post
	 */
	public function linked_post_name_field_index( $name_field, $post_type ) {
		if ( self::POSTTYPE === $post_type ) {
			return 'Organizer';
		}

		return $name_field;
	}

	/**
	 * Filters the index that contains the linked post type data during form submission
	 *
	 * @sinze 4.2
	 *
	 * @param string $container Container index that holds submitted data
	 * @param string $post_type Post type of linked post
	 */
	public function linked_post_type_container( $container, $post_type ) {
		if ( self::POSTTYPE === $post_type ) {
			return 'organizer';
		}

		return $container;
	}

	/**
	 * Check to see if any organizer data set
	 *
	 * @param array $data the organizer data.
	 *
	 * @return bool If there is ANY organizer data set, return true.
	 */
	public function has_organizer_data( $data ) {
		foreach ( self::$valid_keys as $key ) {
			if ( isset( $data[ $key ] ) && $data[ $key ] ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Saves the event organizer information passed via an event
	 *
	 * @param int|null $id ID of event organizer
	 * @param array $data The organizer data
	 * @param string $post_type The post type
	 * @param string $post_status The intended post status
	 *
	 * @return mixed
	 */
	public function save( $id, $data, $post_type, $post_status ) {
		if ( isset( $data['OrganizerID'] ) && intval( $data['OrganizerID'] ) > 0 ) {
			if ( count( $data ) == 1 ) {
				// Only an ID was passed and we should do nothing.
				return $data['OrganizerID'];
			}

			$this->update( $data['OrganizerID'], $data );

			return $data['OrganizerID'];
		}

		return $this->create( $data, $post_status );
	}

	/**
	 * Saves organizer meta
	 *
	 * @param int   $organizerId The organizer ID.
	 * @param array $data        The organizer data.
	 *
	 */
	public function save_meta( $organizerId, $data ) {
		if ( isset( $data['FeaturedImage'] ) && ! empty( $data['FeaturedImage'] ) ) {
			update_post_meta( $organizerId, '_thumbnail_id', $data['FeaturedImage'] );
			unset( $data['FeaturedImage'] );
		}

		foreach ( $data as $key => $var ) {
			update_post_meta( $organizerId, '_Organizer' . $key, $var );
		}
	}

	/**
	 * Creates a new organizer
	 *
	 * @param array  $data        The organizer data.
	 * @param string $post_status the intended post status.
	 *
	 * @return mixed
	 */
	public function create( $data, $post_status = 'publish' ) {
		if ( ( isset( $data['Organizer'] ) && $data['Organizer'] ) || $this->has_organizer_data( $data ) ) {

			$organizer_label = tribe_get_organizer_label_singular();

			$title   = isset( $data['Organizer'] ) ? $data['Organizer'] : sprintf( __( 'Unnamed %s', 'the-events-calendar' ), ucfirst( $organizer_label ) );
			$content = isset( $data['Description'] ) ? $data['Description'] : '';
			$slug    = sanitize_title( $title );

			$postdata = array(
				'post_title'   => $title,
				'post_content' => $content,
				'post_name'    => $slug,
				'post_type'    => self::POSTTYPE,
				'post_status'  => $post_status,
			);

			$organizer_id = wp_insert_post( $postdata, true );

			if ( ! is_wp_error( $organizer_id ) ) {
				$this->save_meta( $organizer_id, $data );
				do_action( 'tribe_events_organizer_created', $organizer_id, $data );

				return $organizer_id;
			}
		}

		// if the venue is blank, let's save the value as 0 instead
		return 0;
	}

	/**
	 * Updates an organizer
	 *
	 * @param int   $organizerId The organizer ID to update.
	 * @param array $data        The organizer data.
	 *
	 */
	public function update( $id, $data ) {
		$this->save_meta( $id, $data );
		do_action( 'tribe_events_organizer_updated', $id, $data );
	}

	/**
	 * Deletes an organizer
	 *
	 * @param int  $organizerId  The organizer ID to delete.
	 * @param bool $force_delete Same as WP param.
	 *
	 */
	public function delete( $id, $force_delete = false ) {
		wp_delete_post( $id, $force_delete );
	}

	public function linked_post_default( $default, $post_type ) {
		if ( self::POSTTYPE !== $post_type ) {
			return $default;
		}

		return Tribe__Events__Main::instance()->defaults()->organizer_id();
	}
}
