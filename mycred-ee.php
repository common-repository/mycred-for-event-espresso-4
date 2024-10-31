<?php
/**
 * Plugin Name: myCRED for Event Espresso 4
 * Plugin URI: http://mycred.me
 * Description: Allows users to pay for event tickets using myCRED points.
 * Version: 1.0.6
 * Tags: mycred, points, event, espresso
 * Author: myCRED
 * Author URI: https://www.mycred.me
 * Author Email: support@mycred.me
 * Requires at least: WP 4.0
 * Tested up to: WP 6.6.1
 * Text Domain: mycred_ee
 * Domain Path: /lang
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
if ( ! class_exists( 'myCRED_EventEspresso' ) ) :
	final class myCRED_EventEspresso {

		// Plugin Version
		public $version             = '1.0.6';

		// Instnace
		protected static $_instance = NULL;

		// Current session
		public $session             = NULL;

		public $slug                = '';
		public $domain              = '';
		public $plugin              = NULL;
		public $plugin_name         = '';

		/**
		 * Setup Instance
		 * @since 1.0
		 * @version 1.0
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		/**
		 * Not allowed
		 * @since 1.0
		 * @version 1.0
		 */
		public function __clone() { _doing_it_wrong( __FUNCTION__, 'Cheatin&#8217; huh?', '1.0' ); }

		/**
		 * Not allowed
		 * @since 1.0
		 * @version 1.0
		 */
		public function __wakeup() { _doing_it_wrong( __FUNCTION__, 'Cheatin&#8217; huh?', '1.0' ); }

		/**
		 * Define
		 * @since 1.0
		 * @version 1.0
		 */
		private function define( $name, $value, $definable = true ) {
			if ( ! defined( $name ) )
				define( $name, $value );
		}

		/**
		 * Require File
		 * @since 1.0
		 * @version 1.0
		 */
		public function file( $required_file ) {
			if ( file_exists( $required_file ) )
				require_once $required_file;
		}

		/**
		 * Construct
		 * @since 1.0
		 * @version 1.0
		 */
		public function __construct() {

			$this->slug        = 'mycred-ee';
			$this->plugin      = plugin_basename( __FILE__ );
			$this->domain      = 'mycred_ee';
			$this->plugin_name = 'myCRED for Event Espresso 4';

			$this->define_constants();

			add_action( 'mycred_init',                           array( $this, 'load_textdomain' ) );
			add_action( 'mycred_all_references',                 array( $this, 'add_badge_support' ) );
			add_action( 'AHEE__EE_System__load_espresso_addons', array( $this, 'setup_gateway' ) );

		}

		/**
		 * Define Constants
		 * @since 1.0
		 * @version 1.0
		 */
		public function define_constants() {

			$this->define( 'EE_MYCRED_PAYMENT_METHOD_VERSION',     '1.0' );
			$this->define( 'EE_MYCRED_PAYMENT_METHOD_PLUGIN_FILE', __FILE__ );
			$this->define( 'MYCRED_EE_SLUG',                       $this->slug );
			$this->define( 'MYCRED_DEFAULT_TYPE_KEY',              'mycred_default' );

		}

		/**
		 * Includes
		 * @since 1.0
		 * @version 1.0
		 */
		public function includes() { }

		public function setup_gateway() {

			if ( version_compare( EVENT_ESPRESSO_VERSION, '4.6', '<' ) ) return;

			if ( ! function_exists( 'mycred' ) ) return;

			if ( class_exists( 'EE_Addon' ) ) {

				$this->file( plugin_dir_path( EE_MYCRED_PAYMENT_METHOD_PLUGIN_FILE ) . 'EE_MyCRED_Payment_Method.class.php' );
				EE_MyCRED_Payment_Method::register_addon();

			}

		}

		/**
		 * Load Textdomain
		 * @since 1.0
		 * @version 1.0
		 */
		public function load_textdomain() {

			// Load Translation
			$locale = apply_filters( 'plugin_locale', get_locale(), $this->domain );

			load_textdomain( $this->domain, WP_LANG_DIR . '/' . $this->slug . '/' . $this->domain . '-' . $locale . '.mo' );
			load_plugin_textdomain( $this->domain, false, dirname( $this->plugin ) . '/lang/' );

		}

		/**
		 * Add Badge Support
		 * @since 1.0
		 * @version 1.0
		 */
		public function add_badge_support( $references ) {

			$references['event_ticket_payment'] = __( 'Ticket Payment (Event Espresso)', $this->domain );

			return $references;

		}

	}
endif;

function mycred_event_espresso_plugin() {
	return myCRED_EventEspresso::instance();
}
mycred_event_espresso_plugin();
