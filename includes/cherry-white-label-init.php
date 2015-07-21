<?php

/**
 * Init class for tables - register post type etc.
 *
 * @package   cherry-white-label
 * @author
 * @license   GPL-2.0+
 * @link
 * @copyright 2014
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // disable direct access
}

if ( !class_exists( 'CherryWhiteLabelInit' ) ) {

	/**
	 * Cherry white label init class
	 *
	 * @since  1.0.0
	 */
	class CherryWhiteLabelInit {

        /**
         * Developer website URL
         * @var $dev_website_url
         */
        private $_dev_website_url;

        /**
         * Developer website text link
         * @var $dev_website_text
         */
        private $_dev_website_text;

        private $_custom_logo_admin_bar;
        private $_custom_login_logo;
        private $_custom_login_background;
        private $_custom_login_css;

		public function __construct()
        {
			if ( is_admin() )
            {
//				add_action('load-index.php', array( $this, 'admin_assets' ));

                // Load admin scripts
				add_action( 'admin_enqueue_scripts', array( $this, 'admin_assets' ) );

				// Menu plugin
				add_action( 'admin_menu', array($this, 'register_menu_plugin') );

                // Hide admin bar options
                add_action( 'wp_before_admin_bar_render', array($this, 'hide_options_admin_bar') );

			}

            $this->_custom_settings_admin_panel();
		}

        private function _custom_settings_admin_panel()
        {
//            global $wp_meta_boxes;
//            vd($wp_meta_boxes, false);
//            unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']);

            $settings = $this->_get_settings();

            // Hide welcome panel
            if (isset($settings['visible-welcome-panel']) && 'on' == $settings['visible-welcome-panel'])
            {
                add_action( 'load-index.php', array($this, 'hide_welcome_panel') );
            }

            // Hide screen options
            if (isset($settings['visible-screen-options']) && 'on' == $settings['visible-screen-options'])
            {
                add_filter( 'screen_options_show_screen', array($this, 'remove_screen_options_tab') );
            }

            // Hide help box
            if (isset($settings['visible-help-box']) && 'on' == $settings['visible-help-box'])
            {
                add_action( 'admin_head', array($this, 'hide_help_box') );
            }

            // Hide version Wordpress
            if (isset($settings['visible-wp-version']) && 'on' == $settings['visible-wp-version'])
            {
                add_action('admin_menu', array($this, 'hide_footer_wp_version'));
            }

            // Developer link
            if (isset($settings['dev-website-name']) && !empty($settings['dev-website-name'])
                && isset($settings['dev-website-url']) && !empty($settings['dev-website-url']))
            {
                $this->_dev_website_text = $settings['dev-website-name'];
                $this->_dev_website_url = $settings['dev-website-url'];

                add_filter( 'admin_footer_text', array($this, 'custom_footer_text') );
            }

            // Hide Wordpress logo in admin bar
            if (isset($settings['visible-wp-logo']) && 'on' == $settings['visible-wp-logo'])
            {
                add_action( 'wp_before_admin_bar_render', array($this, 'hide_logo_admin_bar') );
            }

	        // Custom logo admin bar
            if (isset($settings['wp-logo-admin']) && !empty($settings['wp-logo-admin']))
            {
                $this->_custom_logo_admin_bar = $settings['wp-logo-admin'];
                add_action('add_admin_bar_menus', array($this, 'custom_logo_admin_bar'));
            }

	        // Custom logo in Login form
            if (isset($settings['custom-wp-login-logo']) && !empty($settings['custom-wp-login-logo']))
            {
                $this->_custom_login_logo = $settings['custom-wp-login-logo'];
                add_action( 'login_enqueue_scripts', array($this, 'custom_login_logo') );
            }

	        // Custom background in Login form
            if (isset($settings['background-wp-login-page']) && !empty($settings['background-wp-login-page']))
            {
                $this->_custom_login_background = $settings['background-wp-login-page'];
                add_action( 'login_enqueue_scripts', array($this, 'custom_login_background') );
            }

	        // Custom Login form css
            if (isset($settings['custom-login-css']) && !empty($settings['custom-login-css']))
            {
                $this->_custom_login_css = $settings['custom-login-css'];
                add_action( 'login_enqueue_scripts', array($this, 'custom_login_css') );
            }


////	        vd(__FILE__);
////	        register_activation_hook( __FILE__ , array($this, 'custom_rewrite_rules') );
//	        add_option('htaccess_rules', '');
	        add_action( 'init', array($this, 'custom_rewrite_rules') );
////	        vd(get_option('htaccess_rules'));
        }



//----------------------------------------------------------------------------------------------------
//                  TEST
//----------------------------------------------------------------------------------------------------


//		public function custom_rewrite_rules()
//		{
//			add_option('path-admin-panel', 'admin');
//		}

		//
		public function custom_rewrite_rules()
		{
			global $wp_rewrite;
			$other_rules = array();
			$settings = get_option('cherry-white-label-cms-settings');

			if (isset($settings['path-admin-panel'])
			   && !empty($settings['path-admin-panel']))
			{
				$path_admin_panel = $settings['path-admin-panel'];
				add_rewrite_rule( $path_admin_panel . '/(.*?)$', 'wp-admin/$1?%{QUERY_STRING}', 'top' );
				$other_rules[$path_admin_panel . '$'] = 'WITH_SLASH';


				// Login
				add_rewrite_rule( $path_admin_panel . '/?$', 'wp-login.php', 'top' );


			}

			$wp_rewrite->non_wp_rules = $other_rules + $wp_rewrite->non_wp_rules;
//			vd($wp_rewrite);
			$wp_rewrite->flush_rules(true);
		}


//		function ht_rules($rules)
//		{
//			vd('tyt');
//			$rules = str_replace("/WITH_SLASH [QSA,L]", "%{REQUEST_URI}/ [R=301,L]", $rules);
//			update_option("htaccess_rules", $rules);
//			return $rules;
//		}

//----------------------------------------------------------------------------------------------------

        /**
         * Custom login page background
         */
        public function custom_login_background()
        {
            echo '
                <style type="text/css" media="screen">
                    .login-action-login{
                        background-image:url("' . $this->_custom_login_background . '") !important;
                        background-size: cover;
                    }
                </style>
            ';
        }

        /**
         * Custom login page logo
         */
        public function custom_login_logo()
        {
            echo '
                <style type="text/css" media="screen">
                    .login h1 a { background-image: url("'.$this->_custom_login_logo.'") !important; }
                </style>
            ';
        }

        /**
         * Custom login page css
         */
        public function custom_login_css()
        {
            echo '
                <style type="text/css" media="screen">'.$this->_custom_login_css.'</style>
            ';
        }

        /**
         * Custom logo admin bar
         */
        public function custom_logo_admin_bar()
        {
            remove_action( 'admin_bar_menu', 'wp_admin_bar_wp_menu', 10 );
            add_action( 'admin_bar_menu', array($this, 'custom_logo_admin_bar_bottom'), 10 );
        }

        /**
         * Custom admin bar Logo Bottom
         * @param $wp_admin_bar
         */
        public function custom_logo_admin_bar_bottom( $wp_admin_bar )
        {
            $wp_admin_bar->add_menu( array(
                'id'    => 'wp-logo',
                'title' => '<img style="max-width:16px; height:16px; padding-top: 8px; padding-left: 5px; padding-right: 5px;" src="'. $this->_custom_logo_admin_bar .'" alt="" >',
                'href'  => home_url(''),
            ) );
        }

        /**
         * Hide options admin bar
         */
        public function hide_options_admin_bar()
        {
            global $wp_admin_bar;
            $wp_admin_bar->remove_menu('updates');
            $wp_admin_bar->remove_menu('site-name');
        }

        /**
         * Hide logo Wordpress in admin bar
         */
        public function hide_logo_admin_bar()
        {
            global $wp_admin_bar;
            $wp_admin_bar->remove_menu('custom-wp-logo');
        }

        /**
         * Developer link
         */
        public function custom_footer_text()
        {
            echo '<a href="' . $this->_dev_website_url . '">' . $this->_dev_website_text . '</a>';
        }

        /**
         * Hide "Footer version Wordpress"
         */
        public function hide_footer_wp_version()
        {
            remove_filter( 'update_footer', 'core_update_footer' );
        }

        /**
         * Hide "Help box"
         */
        public function hide_help_box()
        {
            echo '<style type="text/css">#contextual-help-link-wrap { display: none !important; }</style>';
        }

        /**
         * Hide "Screen options"
         * @return bool
         */
        public function remove_screen_options_tab()
        {
            return FALSE;
        }

        /**
         * Hide "Welcome panel"
         */
        public function hide_welcome_panel()
        {
            $user_info = wp_get_current_user();
	        $settings = $this->_get_settings();

	        if (isset($user_info->roles) && !empty($user_info->roles))
	        {
		        $role_user = $user_info->roles[0];

		        if (isset($settings['visible-welcome-group']) && !empty($settings['visible-welcome-group']))
		        {
			        if (!in_array($role_user, $settings['visible-welcome-group']))
			        {
				        remove_action ('welcome_panel', 'wp_welcome_panel');
			        }
		        }
	        }
        }

        /**
		 * Register menu plugin
		 */
		public function register_menu_plugin()
		{
			add_menu_page(
				'Cherry White Label',
				'Cherry White Label',
				'manage_options',
				'cherry-white-label-settings',
				array($this, '_plugin_settings_page'),
				'',
				6
			);

		}

        /**
		 * Plugin settings page options
		 */
		public function _plugin_settings_page()
		{
			if (file_exists(CHERRY_WHITE_LABEL_DIR . 'templates/admin/page-settings.php'))
			{
				$data = $this->_get_settings();

				if ( !empty($_POST)
				     && wp_verify_nonce($_POST['cherry-white-label-settings-value'], 'cherry-white-label-settings')
					 && !$error_message = $this->_validate_settings($_POST))
				{
                    delete_option('cherry-white-label-settings');

					if (add_option('cherry-white-label-settings', $_POST))
					{
						$success_message = __('Saved successfully');
						$data = $_POST;
					}
				}

				$width_image    = 90;
				$height_image   = 90;

				$image_src      = $no_image = CHERRY_WHITE_LABEL_URI . 'images/no-image90x90.png';
				$roles          = get_editable_roles();

				if (isset($roles) && !empty($roles))
				{
					foreach($roles as $role => $role_info)
					{
						$roles[$role] = array(
							'name' => $role_info['name'],
						);

						if (isset($data['visible-welcome-group']) && !empty($data['visible-welcome-group']))
						{
							foreach ($data['visible-welcome-group'] as $visible_role)
							{
								if ($role == $visible_role)
								{
									$roles[$role] = array_merge($roles[$role], array('selected' => TRUE));
								}
							}
						}
					}
				}

				require_once CHERRY_WHITE_LABEL_DIR . 'templates/admin/page-settings.php';
			}
		}

		/**
		 * Get settings Cherry White Label
		 *
		 * @return mixed|void
		 */
		private function _get_settings()
		{
			return get_option('cherry-white-label-settings');
		}

		/**
		 * Validate form settings
		 * @param $data
		 *
		 * @return array|bool
		 */
		private function _validate_settings($data)
		{
			$errors = array();
/*
			if (isset($data['visible-wp-logo']) && 'on' == $data['visible-wp-logo'])
			{
				if (!isset($data['wp-logo-admin']) || empty($data['wp-logo-admin']))
				{
					$errors['errors']['wp-logo-admin'] = TRUE;
				}
			}

			if (!isset($data['wp-logo-dashboard']) || empty($data['wp-logo-dashboard']))
			{
				$errors['errors']['wp-logo-dashboard'] = TRUE;
			}

			if (!isset($data['dashboard-heading']) || empty($data['dashboard-heading']))
			{
				$errors['errors']['dashboard-heading'] = TRUE;
			}

			if (!isset($data['dashboard-heading']) || empty($data['dashboard-heading']))
			{
				$errors['errors']['dashboard-heading'] = TRUE;
			}

			if (!isset($data['dev-website-name']) || empty($data['dev-website-name']))
			{
				$errors['errors']['dev-website-name'] = TRUE;
			}

			if (!isset($data['dev-website-url']) || empty($data['dev-website-url']))
			{
				$errors['errors']['dev-website-url'] = TRUE;
			}

			if (!isset($data['custom-wp-login-logo']) || empty($data['custom-wp-login-logo']))
			{
				$errors['errors']['custom-wp-login-logo'] = TRUE;
			}

			if (!isset($data['background-wp-login-page']) || empty($data['background-wp-login-page']))
			{
				$errors['errors']['background-wp-login-page'] = TRUE;
			}

			if (!isset($data['custom-login-css']) || empty($data['custom-login-css']))
			{
				$errors['errors']['custom-login-css'] = TRUE;
			}

			if (!isset($data['path-admin-panel']) || empty($data['path-admin-panel']))
			{
				$errors['errors']['path-admin-panel'] = TRUE;
			}

			if (!isset($data['visible-welcome-panel']) || empty($data['visible-welcome-panel']))
			{
				$errors['errors']['visible-welcome-panel'] = TRUE;
			}

			if (!isset($data['visible-welcome-group']) || empty($data['visible-welcome-group']))
			{
				$errors['errors']['visible-welcome-group'] = TRUE;
			}

			if (!isset($data['visible-help-box']) || empty($data['visible-help-box']))
			{
				$errors['errors']['visible-help-box'] = TRUE;
			}

			if (!isset($data['visible-screen-options']) || empty($data['visible-screen-options']))
			{
				$errors['errors']['visible-screen-options'] = TRUE;
			}
*/
			if (!empty($errors))
			{
				$errors = array_merge(
					array('error_message' => __('Error validate data')),
					$errors
				);

				return $errors;
			}

			return FALSE;
		}

		/**
		 * Enqueue admin assets
		 *
		 * @since  1.0.0
		 *
		 * @param  string  $hook  admin page hook
		 */
		public function admin_assets( $hook ) {

            // Styles
            wp_enqueue_style(
                'cherry-white-label-style',
                CHERRY_WHITE_LABEL_URI . 'assets/admin/css/style.css',
                array(),
                CHERRY_WHITE_LABEL_VERSION
            );

			if ( ! did_action( 'wp_enqueue_media' ) ) {
				wp_enqueue_media();
			}

			// Scripts
			wp_enqueue_script(
				'cherry-white-label-script',
				CHERRY_WHITE_LABEL_URI . 'assets/admin/js/script.js',
				array( 'jquery' ),
				CHERRY_WHITE_LABEL_VERSION
			);

            $settings = $this->_get_settings();

			$optionsPageSettings = array();

            if (isset($settings['dashboard-heading']) && !empty($settings['dashboard-heading']))
            {
	            $optionsPageSettings = array_merge($optionsPageSettings, array('dashboard_heading' => $settings['dashboard-heading']));

                if (isset($settings['wp-logo-dashboard']) && !empty($settings['wp-logo-dashboard']))
                {
	                $optionsPageSettings = array_merge($optionsPageSettings, array('dashboard_logo' => $settings['wp-logo-dashboard']));
                }
            }

			wp_localize_script( 'cherry-white-label-script', 'optionsPageSettings', $optionsPageSettings);
        }

//		/**
//		 * Include frontend assets
//		 *
//		 * @since 1.0.0
//		 */
//		function public_assets() {
//			// Scripts
////			wp_enqueue_script(
////				'tables-public',
////				CHERRY_WHITE_LABEL_URI . 'assets/public/js/script.js', array( 'jquery' ), CHERRY_WHITE_LABEL_VERSION, true
////			);
////
////			// Styles
////			wp_enqueue_style(
////				'style-cherry-tables-public',
////				CHERRY_WHITE_LABEL_URI . 'assets/public/css/cherry-tables.css', array(), CHERRY_WHITE_LABEL_VERSION
////			);
//		}

	}

	new CherryWhiteLabelInit();
}