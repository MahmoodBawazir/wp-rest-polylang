<?php
/**
 * Plugin Name: WP REST - Polylang
 * Description: Polylang integration for the WP REST API
 * Author: Marc-Antoine Ruel
 * Contributor: Mahmood Bawazir
 * Author URI: https://www.marcantoineruel.com
 * Contributor URI: https://mbz.codes
 * Version: 1.0.0
 * Plugin URI: https://github.com/MahmoodBawazir/wp-rest-polylang
 * License: gpl-3.0
 */


class WP_REST_polylang
{

	static $instance = false;

	private function __construct() {
		// Check if polylang is installed
		require_once ABSPATH . 'wp-admin/includes/plugin.php';

		if (!is_plugin_active('polylang/polylang.php')) {
			return;
		}

		add_action('rest_api_init', array($this, 'init'), 0);
	}

	public static function getInstance() {
		if ( !self::$instance )
			self::$instance = new self;
		return self::$instance;
	}

	public static function init() {
		global $polylang;

		if (isset($_GET['lang'])) {
			$current_lang = $_GET['lang'];

			$polylang->curlang = $polylang->model->get_language($current_lang);
		}

		$post_types = get_post_types( array( 'public' => true ), 'names' );

		foreach( $post_types as $post_type ) {
			if (pll_is_translated_post_type( $post_type )) {
				self::register_api_field($post_type);
			}
		}
	}

	public function register_api_field($post_type) {
    register_rest_field(
			$post_type,
			"pll_lang_name",
			array(
        "get_callback" => function($object){
          return $this->get_current_lang_field($object, 'name');
        },
				"schema" => null
			)
    );

		register_rest_field(
			$post_type,
			"pll_lang_locale",
			array(
				"get_callback" => function($object){
          return $this->get_current_lang_field($object, 'locale');
        },
				"schema" => null
			)
    );

    register_rest_field(
			$post_type,
			"pll_lang_tag",
			array(
				"get_callback" => function($object){
          return $this->get_current_lang_field($object, 'w3c');
        },
				"schema" => null
			)
    );
    
    register_rest_field(
			$post_type,
			"pll_lang_code",
			array(
				"get_callback" => function($object){
          return $this->get_current_lang_field($object, 'slug');
        },
				"schema" => null
			)
    );
    
    register_rest_field(
			$post_type,
			"pll_lang_rtl",
			array(
				"get_callback" => function($object){
          return (bool) $this->get_current_lang_field($object, 'is_rtl');
        },
				"schema" => null
			)
    );

		register_rest_field(
			$post_type,
			"pll_translations",
			array(
				"get_callback" => array( $this, "get_translations"  ),
				"schema" => null
			)
		);
	}

  public function get_current_lang_field( $object, $field ) {
		return pll_get_post_language($object['id'], $field);
  }

	public function get_translations( $object ) {
		$translations = pll_get_post_translations($object['id']);

		return array_reduce($translations, function ($carry, $translation) {
			$item = array(
        'name' => pll_get_post_language($translation, 'name'),
        'locale' => pll_get_post_language($translation, 'locale'),
        'tag' => pll_get_post_language($translation, 'w3c'),
        'code' => pll_get_post_language($translation, 'slug'),
        'rtl' => (bool) pll_get_post_language($translation, 'is_rtl'),
				'id' => $translation
			);

			array_push($carry, $item);

			return $carry;
		}, array());
	}
}

$WP_REST_polylang = WP_REST_polylang::getInstance();
