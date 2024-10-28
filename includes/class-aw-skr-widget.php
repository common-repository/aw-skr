<?php
/**
 * The widget functionality of the plugin.
 *
 * @link       http://scheidungskostenrechner.org
 * @since      1.0.0
 *
 * @package    AW_SKR
 * @subpackage AW_SKR/includes
 * @author     Active Websight <info@active-websight.de>
 */
class AW_SKR_Widget extends WP_Widget {
	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct(
			'aw_skr_widget',
			// Widget name will appear in UI.
			'AW Scheidungskostenrechner',
			// Widget description.
			array(
				'description' => 'Fügt den Scheidungskostenrechner ein. Alternativ können Sie den Shortcode [aw_skr] irgendwo in Ihrem Content/Template benutzen.',
			)
		);
	}

	/**
	 * Creating widget front-end.
	 *
	 * @param array $args .
	 * @param array $instance .
	 * @return void
	 */
	public function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', $instance['title'] );
		$style = $instance['style'];

		// before and after widget arguments are defined by themes.
		// echo $args['before_widget'];.
		if ( ! empty( $title ) ) {
			$title = ' title="' . $title . '"';
		}
		if ( ! empty( $style ) ) {
			$style = ' style="' . $style . '"';
		}
		// // This is where you run the code and display the output.
		// echo $args['after_widget'];.
		echo do_shortcode( '[aw_skr' . $title . $style . ']' );
	}

	/**
	 * Widget Backend.
	 *
	 * @param array $instance .
	 * @return void
	 */
	public function form( $instance ) {
		if ( isset( $instance['title'] ) ) {
			$title = $instance['title'];
		} else {
			$title = '';
		}
		if ( isset( $instance['style'] ) ) {
			$style = $instance['style'];
		} else {
			$style = '';
		}
		// Widget admin form. ?>
	<p>
	<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
	<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
	</p>
	<p>
	<label for="<?php echo $this->get_field_id( 'style' ); ?>"><?php _e( 'Style:' ); ?></label>
	<select class='widefat' id="<?php echo $this->get_field_id( 'style' ); ?>" name="<?php echo $this->get_field_name( 'style' ); ?>" type="text">
		<option value=''<?php echo ( '' === $style ) ? ' selected' : ''; ?>>Standard (schmal)</option>
		<option value='wide'<?php echo ( 'wide' === $style ) ? ' selected' : ''; ?>>Breiter</option>
	</select>
	</p>
	<?php
	}

	/**
	 * Updating widget replacing old instances with new.
	 *
	 * @param array $new_instance .
	 * @param array $old_instance .
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		$instance          = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['style'] = ( ! empty( $new_instance['style'] ) ) ? strip_tags( $new_instance['style'] ) : '';
		return $instance;
	}
}
