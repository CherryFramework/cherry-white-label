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
	        // Custom URL Admin Panel Authorization
	        add_filter('site_url', array($this, '_replace_login_url'), 10, 2);
	        add_action('login_init', array($this, '_custom_login_url'));

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
        }

		/**
		 * Replace login URL in Form authorization
		 *
		 * @param $url
		 *
		 * @return string
		 */
		public function _replace_login_url($url)
		{
			$scheme = 'http://';
			$domain = '';
			$now_path = '/wp-login.php';
			$url_info = $this->_get_url();
			$custom_admin_slug = get_option('custom_wp_admin_slug');

			if (isset($url_info['scheme']) && !empty($url_info['scheme']))
			{
				$scheme = $url_info['scheme'] . '://';
			}

			if (isset($url_info['domain']) && !empty($url_info['domain']))
			{
				$domain = $url_info['domain'];
			}

			if (isset($url_info['path']) && !empty($url_info['path']))
			{
				$now_path = $url_info['path'];
			}

			$wp_domain = $scheme . $domain;
			$wp_login_path = $scheme . $domain . $now_path;

			if ($wp_login_path == $url && get_option('is_admin_slug'))
			{
				return $wp_domain . '/' . $custom_admin_slug;
			}

			return $url;
		}

		/**
		 * Custom login URL
		 */
		public function _custom_login_url()
		{
			if (in_array($GLOBALS['pagenow'], array('wp-login.php', 'wp-register.php')) && (get_option('custom_wp_admin_slug') != FALSE && get_option('custom_wp_admin_slug') != ''))
			{
				// check if our plugin have written necesery line to .htaccess, sometimes WP doesn't write correctly so we don't want to disable login in that case
				$markerdata = explode("\n", implode('', file($this->_get_home_path() . '.htaccess')));
				$found = FALSE;
				$url = $this->_get_url();

				foreach ($markerdata as $line)
				{
					if (trim($line) == 'RewriteRule ^' . get_option('custom_wp_admin_slug') . '/?$ ' . ($url['rewrite_base'] ? '/'.$url['rewrite_base'] : '') . '/wp-login.php [QSA,L]')
					{
						$found = TRUE;
					}
				}

				if ($found)
				{
					$this->_custom_login();
				}
			}
		}

		/**
		 * Custom login (Redirecting)
		 */
		private function _custom_login()
		{
			if (is_user_logged_in())
			{
				wp_redirect( site_url('wp-admin') );
			}

			// start session if doesn't exist
			if (!session_id())
			{
				session_start();
			}

			$url = $this->_get_url();

			if ('/' . get_option('custom_wp_admin_slug') == $_SERVER['REQUEST_URI'] || '/' . get_option('custom_wp_admin_slug') . '/' == $_SERVER['REQUEST_URI'])
			{
				$file = $_SERVER['REQUEST_URI'];
				$arguments = '';
			}
			else
			{
				if (isset($_SERVER['REQUEST_METHOD']) && 'GET' == $_SERVER['REQUEST_METHOD'])
				{
					if ('action=lostpassword' == $url['query'] || 'action=postpass' == $url['query'])
					{
						// Let user to this pages
					}
					else
					{
						// TODO: Проверить ссылку: Если вылогиниваеться пользователь - перебросить на страницу авторизации.
						wp_redirect( home_url( '/' ), 301 );
					}
				}
				else
				{
					if (isset($_POST['redirect_to']) && preg_match('/wp-admin/', $_POST['redirect_to']))
					{
						list($file, $arguments) = explode("?", $_SERVER['REQUEST_URI']);
					}
					else if ('action=lostpassword' == $url['query'] || 'action=postpass' == $url['query'])
					{
						list($file, $arguments) = explode("?", $_SERVER['REQUEST_URI']);
					}
				}
			}

			// On localhost remove subdir
			$file = ($url['rewrite_base']) ? implode("", explode("/" . $url['rewrite_base'], $file)) : $file;

			if ("/wp-login.php?loggedout=true" == $file . "?" . $arguments)
			{
				session_destroy();
				header("location: " . $url['scheme'] . "://" . $url['domain'] . "/" . $url['rewrite_base']);
				exit();
			}
			else if ('action=logout' == substr($arguments, 0, 13))
			{
				unset($_SESSION['valid_login']);
			}
			else if ('action=lostpassword' == $url['query'] || 'action=postpass' == $url['query'])
			{
				// Let user to this pages
			}
			else if ($file == "/" . get_option('custom_wp_admin_slug') || $file == "/" . get_option('custom_wp_admin_slug') . "/")
			{
				$_SESSION['valid_login'] = TRUE;
			}
			else if (isset($_SESSION['valid_login']))
			{
				// Let them pass
			}
			else
			{
				header("location: " . $url['scheme'] . "://" . $url['domain'] . "/" . $url['rewrite_base']);
				exit();
			}
		}

		/**
		 * Taken from "/wp-admin/includes/file.php"
		 *
		 * @return string
		 */
		private function _get_home_path()
		{
			$home = get_option( 'home' );
			$site_url = get_option( 'siteurl' );

			if ( ! empty( $home ) && 0 !== strcasecmp( $home, $site_url ) )
			{
				$wp_path_rel_to_home = str_ireplace( $home, '', $site_url ); /* $site_url - $home */
				$pos = strripos( str_replace( '\\', '/', $_SERVER['SCRIPT_FILENAME'] ), trailingslashit( $wp_path_rel_to_home ) );
				$home_path = substr( $_SERVER['SCRIPT_FILENAME'], 0, $pos );
				$home_path = trailingslashit( $home_path );
			}
			else
			{
				$home_path = ABSPATH;
			}

			return $home_path;
		}

		/**
		 * Return parsed url
		 *
		 * @return array
		 */
		private function _get_url()
		{
			$url = array();
			$url['scheme'] = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != "off" ? "https" : "http";
			$url['domain'] = $_SERVER['HTTP_HOST'];
			$url['port'] = isset($_SERVER["SERVER_PORT"]) && $_SERVER["SERVER_PORT"] ? $_SERVER["SERVER_PORT"] : "";
			$url['rewrite_base'] = ($host = explode( $url['scheme'] . "://" . $_SERVER['HTTP_HOST'], get_bloginfo('url') ) ) ? preg_replace("/^\//", "", implode("", $host)) : "";
			$url['path'] = $url['rewrite_base'] ? implode("", explode("/" . $url['rewrite_base'], $_SERVER["SCRIPT_NAME"])) : $_SERVER["SCRIPT_NAME"];
			$url['query'] = $_SERVER['QUERY_STRING'];
			return $url;
		}

		/**
		 *  Create or Update custom admin URL (.htaccess)
		 */
		public function _tune_custom_admin_url()
		{
			$rules = array();

			// Sanitize input
			$custom_wp_admin_slug = trim(sanitize_key(wp_strip_all_tags($_POST['admin-panel-slug'])));
			$custom_wp_forgot_password_slug = trim(sanitize_key(wp_strip_all_tags($_POST['forgot-password-slug'])));

			if (!empty($custom_wp_admin_slug))
			{
				update_option('is_admin_slug', TRUE);
				$rules['custom_admin_slug'] = $custom_wp_admin_slug;
			}
			else
			{
				update_option('is_admin_slug', FALSE);
				$rules['custom_admin_slug'] = FALSE;
			}

			if (!empty($custom_wp_forgot_password_slug))
			{
				$rules['custom_forgot_password_slug'] = $custom_wp_forgot_password_slug;
			}
			else
			{
				$rules['custom_forgot_password_slug'] = FALSE;
			}

			// Overwrites the data file .htaccess
			$this->_overwrites_htaccess($rules);
		}

		/**
		 * Overwrites the data file .htaccess
		 *
		 * @param $rules
		 */
		private function _overwrites_htaccess($rules)
		{
			$ht_login = '';
			$ht_forgot_password = '';
			$home_path = get_home_path();
			$settings = $this->_get_settings();

			$old_ht_login = !empty($settings['admin-panel-slug']) ? $settings['admin-panel-slug'] : FALSE ;
			$old_ht_password_slug = !empty($settings['forgot-password-slug']) ? $settings['forgot-password-slug'] : FALSE ;

			if ($rules['custom_admin_slug'])
			{
				$ht_login = 'RewriteRule ^' . $rules['custom_admin_slug'] . '/?$ /wp-login.php [QSA,L]' . "\n";
			}

			if ($rules['custom_forgot_password_slug'])
			{
				$ht_forgot_password = 'RewriteRule ^' . $rules['custom_forgot_password_slug'] . '/?$ /wp-login.php?action=lostpassword [QSA,L]' . "\n";
			}

			if ((!file_exists($home_path . '.htaccess') && is_writable($home_path)) || is_writable($home_path . '.htaccess'))
			{
				if (file_exists($home_path . '.htaccess'))
				{
					$found = FALSE;
					$not_exist_rules = FALSE;
					$new_data = '';
					$marker_data = explode("\n", implode('', file($home_path . '.htaccess')));

					if (in_array('# BEGIN WordPress', $marker_data))
					{
						if ( in_array( '<IfModule mod_rewrite.c>', $marker_data ) )
						{
							foreach ( $marker_data as $line )
							{
								if ( $line == '# BEGIN WordPress' )
								{
									$found = TRUE;
								}

								if ( $found )
								{
									if ( 'RewriteRule ^' . $old_ht_login . '/?$ /wp-login.php [QSA,L]' == $line
									     || 'RewriteRule ^' . $old_ht_password_slug . '/?$ /wp-login.php?action=lostpassword [QSA,L]' == $line
									)
									{
										$line = '';
										$new_data .= $line;
									}
									else
									{
										$new_data .= $line . "\n";
									}

									if ( 'RewriteRule ^index\.php$ - [L]' == $line )
									{
										$new_data .= $ht_login;
										$new_data .= $ht_forgot_password;
									}
								}

								if ( $line == '# END WordPress' )
								{
									$found = FALSE;
								}
							}
						}
						else
						{
							$new_data .= "<IfModule mod_rewrite.c>\n";
							$new_data .= "RewriteEngine On\n";
							$new_data .= "RewriteBase /\n";
							$new_data .= "RewriteRule ^index\.php$ - [L]\n";

							if ($rules['custom_admin_slug'])
							{
								$new_data .= $ht_login;
							}

							if ($rules['custom_forgot_password_slug'])
							{
								$new_data .= $ht_forgot_password;
							}

							$new_data .= "RewriteCond %{REQUEST_FILENAME} !-f\n";
							$new_data .= "RewriteCond %{REQUEST_FILENAME} !-d\n";
							$new_data .= "RewriteRule . /index.php [L]\n";
							$new_data .= "</IfModule>\n";
							$not_exist_rules = TRUE;
						}
					}
					else
					{
						$new_data .= "# BEGIN WordPress\n";
						$new_data .= "<IfModule mod_rewrite.c>\n";
						$new_data .= "RewriteEngine On\n";
						$new_data .= "RewriteBase /\n";
						$new_data .= "RewriteRule ^index\.php$ - [L]\n";

						if ($rules['custom_admin_slug'])
						{
							$new_data .= $ht_login;
						}

						if ($rules['custom_forgot_password_slug'])
						{
							$new_data .= $ht_forgot_password;
						}

						$new_data .= "RewriteCond %{REQUEST_FILENAME} !-f\n";
						$new_data .= "RewriteCond %{REQUEST_FILENAME} !-d\n";
						$new_data .= "RewriteRule . /index.php [L]\n";
						$new_data .= "</IfModule>\n";
						$new_data .= "# END WordPress\n";
					}

					if ($not_exist_rules)
					{
						insert_with_markers($home_path . '.htaccess', 'WordPress', explode("\n", $new_data));
					}
					else
					{
						$fn_htaccess = $home_path . '.htaccess';
						file_put_contents($fn_htaccess, $new_data);
					}
				}
//				else
//				{
//					vd('Does not exist .htaccess' );
//				}

				// TODO: Нужно проверять, есть ли содержимое файла .htaccss и дописывать или редактировать правила по маркеру
				// TODO: Если нет файла .htaccess - создать его.
				// TODO: Если нет прав сообщить пользователю о создании вручную или дать права на создания файла на сервере
			}
		}

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
					// Authorization settings
					$this->_tune_custom_admin_url();

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