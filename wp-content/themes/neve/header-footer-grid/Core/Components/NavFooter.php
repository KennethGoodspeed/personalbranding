<?php
/**
 * Custom Component class for Header Footer Grid.
 *
 * Name:    Header Footer Grid
 * Author:  Bogdan Preda <bogdan.preda@themeisle.com>
 *
 * @version 1.0.0
 * @package HFG
 */

namespace HFG\Core\Components;

use HFG\Core\Settings;
use HFG\Core\Settings\Manager as SettingsManager;
use HFG\Main;
use Neve\Core\Settings\Config;
use Neve\Core\Styles\Dynamic_Selector;
use WP_Customize_Manager;

/**
 * Class NavFooter
 *
 * @package HFG\Core\Components
 */
class NavFooter extends Abstract_Component {
	const COMPONENT_ID   = 'footer-menu';
	const STYLE_ID       = 'style';
	const COLOR_ID       = 'color';
	const HOVER_COLOR_ID = 'hover_color';
	const ITEM_HEIGHT    = 'item_height';
	const SPACING        = 'spacing';

	/**
	 * Nav constructor.
	 *
	 * @since   1.0.0
	 * @access  public
	 */
	public function init() {
		$this->set_property( 'label', __( 'Footer Menu', 'neve' ) );
		$this->set_property( 'id', self::COMPONENT_ID );
		$this->set_property( 'width', 6 );
		$this->set_property( 'icon', 'tagcloud' );
		$this->set_property( 'section', 'footer_menu_primary' );
		$this->set_property( 'has_font_family_control', true );
		$this->set_property( 'has_typeface_control', true );
		$this->set_property( 'default_typography_selector', $this->default_typography_selector . '.builder-item--' . $this->get_id() . ' li > a' );
	}

	/**
	 * Called to register component controls.
	 *
	 * @since   1.0.0
	 * @access  public
	 */
	public function add_settings() {


		SettingsManager::get_instance()->add(
			[
				'id'                => self::STYLE_ID,
				'group'             => self::COMPONENT_ID,
				'tab'               => SettingsManager::TAB_STYLE,
				'transport'         => 'post' . self::COMPONENT_ID,
				'sanitize_callback' => 'wp_filter_nohtml_kses',
				'default'           => 'style-plain',
				'label'             => __( 'Hover Skin Mode', 'neve' ),
				'type'              => '\Neve\Customizer\Controls\React\Radio_Buttons',
				'section'           => $this->section,
				'options'           => [
					'large_buttons' => true,
					'is_for'        => 'menu',
				],
			]
		);

		SettingsManager::get_instance()->add(
			[
				'id'                    => self::COLOR_ID,
				'group'                 => self::COMPONENT_ID,
				'tab'                   => SettingsManager::TAB_STYLE,
				'transport'             => 'postMessage',
				'sanitize_callback'     => 'sanitize_hex_color',
				'default'               => '#404248',
				'label'                 => __( 'Items Color', 'neve' ),
				'type'                  => 'neve_color_control',
				'section'               => $this->section,
				'live_refresh_selector' => true,
				'live_refresh_css_prop' => [
					[
						'selector' => $this->default_typography_selector,
						'prop'     => 'color',
						'fallback' => 'inherit',
					],
				],
			]
		);

		SettingsManager::get_instance()->add(
			[
				'id'                    => self::HOVER_COLOR_ID,
				'group'                 => self::COMPONENT_ID,
				'tab'                   => SettingsManager::TAB_STYLE,
				'transport'             => 'postMessage',
				'sanitize_callback'     => 'sanitize_hex_color',
				'default'               => '#0366d6',
				'label'                 => __( 'Items Hover Color', 'neve' ),
				'type'                  => 'neve_color_control',
				'section'               => $this->section,
				'live_refresh_selector' => true,
				'live_refresh_css_prop' => [
					[
						'selector' => $this->default_typography_selector . ':after',
						'prop'     => 'background-color',
						'fallback' => 'inherit',
					],
					[
						'selector' => '.builder-item--' . $this->get_id() . ' .nav-menu-footer:not(.style-full-height) #footer-menu li:hover > a',
						'prop'     => 'color',
						'fallback' => 'inherit',
					],
				],
			]
		);

		SettingsManager::get_instance()->add(
			[
				'id'                => 'shortcut',
				'group'             => self::COMPONENT_ID,
				'tab'               => SettingsManager::TAB_GENERAL,
				'transport'         => 'postMessage',
				'sanitize_callback' => 'esc_attr',
				'type'              => '\Neve\Customizer\Controls\Button',
				'options'           => [
					'button_class' => 'nv-top-bar-menu-shortcut',
					'icon_class'   => 'menu',
					'button_text'  => __( 'Footer Menu', 'neve' ),
					'shortcut'     => true,
				],
				'section'           => $this->section,
			]
		);

		SettingsManager::get_instance()->add(
			[
				'id'                 => self::SPACING,
				'group'              => $this->get_class_const( 'COMPONENT_ID' ),
				'tab'                => SettingsManager::TAB_LAYOUT,
				'section'            => $this->section,
				'label'              => __( 'Items Spacing (px)', 'neve' ),
				'type'               => 'Neve\Customizer\Controls\React\Responsive_Range',
				'transport'          => 'post' . $this->get_class_const( 'COMPONENT_ID' ),
				'sanitize_callback'  => [ $this, 'sanitize_responsive_int_json' ],
				'default'            => $this->get_default_for_responsive_from_intval( self::SPACING, 20 ),
				'options'            => [
					'input_attrs' => [
						'min'        => 1,
						'max'        => 100,
						'units'      => [ 'px' ],
						'defaultVal' => [
							'mobile'  => 20,
							'tablet'  => 20,
							'desktop' => 20,
						],
					],
				],
				'conditional_header' => true,
			]
		);

		SettingsManager::get_instance()->add(
			[
				'id'                 => self::ITEM_HEIGHT,
				'group'              => $this->get_class_const( 'COMPONENT_ID' ),
				'tab'                => SettingsManager::TAB_LAYOUT,
				'label'              => __( 'Items Min Height (px)', 'neve' ),
				'sanitize_callback'  => [ $this, 'sanitize_responsive_int_json' ],
				'transport'          => 'post' . $this->get_class_const( 'COMPONENT_ID' ),
				'default'            => $this->get_default_for_responsive_from_intval( self::ITEM_HEIGHT, 25 ),
				'type'               => 'Neve\Customizer\Controls\React\Responsive_Range',
				'options'            => [
					'input_attrs' => [
						'min'        => 1,
						'max'        => 100,
						'units'      => [ 'px' ],
						'defaultVal' => [
							'mobile'  => 25,
							'tablet'  => 25,
							'desktop' => 25,
						],
					],
				],
				'section'            => $this->section,
				'conditional_header' => true,
			]
		);
	}

	/**
	 * The render method for the component.
	 *
	 * @since   1.0.0
	 * @access  public
	 */
	public function render_component() {
		Main::get_instance()->load( 'components/component-nav-footer' );
	}

	/**
	 * Add styles to the component.
	 *
	 * @param array $css_array rules array.
	 *
	 * @return array
	 */
	public function add_style( array $css_array = array() ) {
		$css_array[] = [
			Dynamic_Selector::KEY_SELECTOR => '.nav-menu-footer #footer-menu > li > a',
			Dynamic_Selector::KEY_RULES    => [
				Config::CSS_PROP_COLOR => [
					Dynamic_Selector::META_KEY     => $this->get_id() . '_' . self::COLOR_ID,
					Dynamic_Selector::META_DEFAULT => SettingsManager::get_instance()->get_default( $this->get_id() . '_' . self::COLOR_ID ),
				],
			],
		];

		$css_array[] = [
			Dynamic_Selector::KEY_SELECTOR => '#footer-menu > li > a:after',
			Dynamic_Selector::KEY_RULES    => [
				Config::CSS_PROP_BACKGROUND_COLOR => [
					Dynamic_Selector::META_KEY     => $this->get_id() . '_' . self::HOVER_COLOR_ID,
					Dynamic_Selector::META_DEFAULT => SettingsManager::get_instance()->get_default( $this->get_id() . '_' . self::HOVER_COLOR_ID ),
				],
			],
		];
		$css_array[] = [
			Dynamic_Selector::KEY_SELECTOR => '.nav-menu-footer:not(.style-full-height) #footer-menu > li:hover > a',
			Dynamic_Selector::KEY_RULES    => [
				Config::CSS_PROP_COLOR => [
					Dynamic_Selector::META_KEY     => $this->get_id() . '_' . self::HOVER_COLOR_ID,
					Dynamic_Selector::META_DEFAULT => SettingsManager::get_instance()->get_default( $this->get_id() . '_' . self::HOVER_COLOR_ID ),
				],
			],
		];


		$is_rtl = is_rtl();
		$last   = $is_rtl ? 'first' : 'last';

		$css_array[] = [
			Dynamic_Selector::KEY_SELECTOR => '.builder-item--' . $this->get_id() . ' .nav-ul > li:not(:' . $last . '-of-type)',
			Dynamic_Selector::KEY_RULES    => [
				Config::CSS_PROP_MARGIN_RIGHT => [
					Dynamic_Selector::META_KEY           => $this->get_id() . '_' . self::SPACING,
					Dynamic_Selector::META_IS_RESPONSIVE => true,
					Dynamic_Selector::META_FILTER        => function ( $css_prop, $value, $meta, $device ) {
						return sprintf( '%s:%s;', $css_prop, absint( $value ) . 'px' );
					},
					Dynamic_Selector::META_DEFAULT       => SettingsManager::get_instance()->get_default( $this->get_id() . '_' . self::SPACING ),
				],
			],
		];

		$css_array[] = [
			Dynamic_Selector::KEY_SELECTOR => '.builder-item--' . $this->get_id() . ' .style-full-height .nav-ul#footer-menu > li > a:after',
			Dynamic_Selector::KEY_RULES    => [
				'position' => [
					Dynamic_Selector::META_KEY           => $this->get_id() . '_' . self::SPACING,
					Dynamic_Selector::META_IS_RESPONSIVE => true,
					Dynamic_Selector::META_FILTER        => function ( $css_prop, $value, $meta, $device ) {
						if ( $device !== Dynamic_Selector::DESKTOP ) {
							return '';
						}
						$value = absint( $value );

						return sprintf( 'left:%s;right:%s', - $value / 2 . 'px', - $value / 2 . 'px' );
					},
					Dynamic_Selector::META_DEFAULT       => SettingsManager::get_instance()->get_default( $this->get_id() . '_' . self::SPACING ),
				],
			],
		];

		$css_array[] = [
			Dynamic_Selector::KEY_SELECTOR => '.builder-item--' . $this->get_id() . ' .style-full-height .nav-ul#footer-menu > li:hover > a:after',
			Dynamic_Selector::KEY_RULES    => [
				Config::CSS_PROP_WIDTH => [
					Dynamic_Selector::META_KEY           => $this->get_id() . '_' . self::SPACING,
					Dynamic_Selector::META_IS_RESPONSIVE => true,
					Dynamic_Selector::META_FILTER        => function ( $css_prop, $value, $meta, $device ) {
						return sprintf( 'width: calc(100%% + %s);', absint( $value ) . 'px' );
					},
					Dynamic_Selector::META_DEFAULT       => SettingsManager::get_instance()->get_default( $this->get_id() . '_' . self::SPACING ),
				],
			],
		];

		$css_array[] = [
			Dynamic_Selector::KEY_SELECTOR => '.builder-item--' . $this->get_id() . ' .nav-ul a',
			Dynamic_Selector::KEY_RULES    => [
				Config::CSS_PROP_MIN_HEIGHT => [
					Dynamic_Selector::META_KEY           => $this->get_id() . '_' . self::ITEM_HEIGHT,
					Dynamic_Selector::META_IS_RESPONSIVE => true,
					Dynamic_Selector::META_DEFAULT       => SettingsManager::get_instance()->get_default( $this->get_id() . '_' . self::ITEM_HEIGHT ),
				],
			],
		];

		return parent::add_style( $css_array );
	}

}