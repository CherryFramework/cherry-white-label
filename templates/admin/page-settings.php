<h1><?php echo __('Cherry White Label'); ?></h1>
<br/>

<?php if ( isset($error_message['error_message']) && !empty($error_message['error_message']) ) : ?>
	<div id="message" class="updated notice error is-dismissible below-h2">
		<p><?php echo $error_message['error_message']; ?></p>
		<button type="button" class="notice-dismiss">
			<span class="screen-reader-text"><?php echo __('Dismiss this notice.'); ?></span>
		</button>
	</div>
<?php endif; ?>

<?php if ( isset($success_message) ): ?>
	<div id="message" class="updated notice notice-success is-dismissible below-h2">
		<p><?php echo $success_message; ?></p>
		<button type="button" class="notice-dismiss">
			<span class="screen-reader-text"><?php echo __('Dismiss this notice.'); ?></span>
		</button>
	</div>
<?php endif; ?>

<form method="POST" action="">
	<h2><?php echo __('Tab brand'); ?></h2>
	<hr/>
	<div class="form-wrapper">
		<ul>
			<li>
				<h3><?php echo __('Admin bar');?></h3>
				<div class="form-str">
					<div class="form-row">
						<lable><?php echo __('Hide WordPress logo:'); ?></lable>
						<input type="checkbox" <?php echo (isset($data['visible-wp-logo']) && 'on' == $data['visible-wp-logo']) ? 'checked="checked"' : '' ; ?> name="visible-wp-logo"/>
					</div>
					<div class="form-row">
						<lable><?php echo __('Upload your Logo 16x16px:'); ?></lable>
						<div class="form-element">
							<div class="form-image">
								<?php if (isset($data['wp-logo-admin']) && !empty($data['wp-logo-admin'])){ ?>
									<img data-src="<?php echo $no_image; ?>" src="<?php echo $data['wp-logo-admin']; ?>" height="<?php echo $height_image; ?>px" />
								<?php }else{ ?>
									<img data-src="<?php echo $no_image; ?>" src="<?php echo $image_src; ?>" height="<?php echo $height_image; ?>px" />
								<?php } ?>
							</div>
							<button type="button" class="upload_image_button button button-primary" data-browse="wp-logo-admin"><?php echo __('Browse'); ?></button>
							<?php if (isset($data['wp-logo-admin']) && !empty($data['wp-logo-admin'])){ ?>
								<button type="button" class="remove_image_button button button-cancel"><?php echo __('Remove'); ?></button>
							<?php } ?>
							<input type="hidden" name="wp-logo-admin" id="wp-logo-admin" value="<?php echo isset($data['wp-logo-admin']) && !empty($data['wp-logo-admin']) ? $data['wp-logo-admin'] : '' ; ?>" />
						</div>
					</div>
				</div>

				<h3>Dashboard bar</h3>
				<div class="form-str">
					<div class="form-row">
						<lable><?php echo __('Add Dashboard logo:'); ?></lable>
						<div class="form-element">
							<div class="form-image">
								<?php if (isset($data['wp-logo-dashboard']) && !empty($data['wp-logo-dashboard'])){ ?>
									<img data-src="<?php echo $no_image; ?>" src="<?php echo $data['wp-logo-dashboard']; ?>" height="<?php echo $height_image; ?>px" />
								<?php }else{ ?>
									<img data-src="<?php echo $no_image; ?>" src="<?php echo $image_src; ?>" height="<?php echo $height_image; ?>px" />
								<?php } ?>
							</div>
							<button type="button" class="upload_image_button button button-primary" data-browse="wp-logo-dashboard"><?php echo __('Browse'); ?></button>
							<?php if (isset($data['wp-logo-dashboard']) && !empty($data['wp-logo-dashboard'])){ ?>
								<button type="button" class="remove_image_button button button-cancel"><?php echo __('Remove'); ?></button>
							<?php } ?>
							<input type="hidden" name="wp-logo-dashboard" id="wp-logo-dashboard" value="<?php echo isset($data['wp-logo-dashboard']) && !empty($data['wp-logo-dashboard']) ? $data['wp-logo-dashboard'] : '' ; ?>" />
						</div>
					</div>
					<div class="form-row">
						<lable><?php echo __('Dashboard heading:'); ?></lable>
						<input type="text" name="dashboard-heading" <?php echo isset($data['dashboard-heading']) && !empty($data['dashboard-heading']) ? 'value="' . $data['dashboard-heading'] . '"' : '' ; ?> />
					</div>
				</div>

				<h3><?php echo __('Footer'); ?></h3>
				<div class="form-str">
					<div class="form-row">
						<lable><?php echo __('Hide Wordpress version:'); ?></lable>
						<input type="checkbox" name="visible-wp-version" <?php echo isset($data['visible-wp-version']) && 'on' == $data['visible-wp-version'] ? 'checked="checked"' : '' ; ?> />
					</div>
					<div class="form-row">
						<lable><?php echo __('Developer Website Name:'); ?></lable>
						<input type="text" name="dev-website-name" <?php echo isset($data['dev-website-name']) && !empty($data['dev-website-name']) ? 'value="' . $data['dev-website-name'] . '"' : '' ; ?> />
					</div>
					<div class="form-row">
						<lable><?php echo __('Developer Website URL:'); ?></lable>
						<input type="text" name="dev-website-url" <?php echo isset($data['dev-website-url']) && !empty($data['dev-website-url']) ? 'value="' . $data['dev-website-url'] . '"' : '' ; ?> />
					</div>
				</div>

				<h3>Login</h3>
				<div class="form-str">
					<div class="form-row">
						<lable><?php echo __('Custom login logo:'); ?></lable>
						<div class="form-element">
							<div class="form-image">
								<?php if (isset($data['custom-wp-login-logo']) && !empty($data['custom-wp-login-logo'])){ ?>
									<img data-src="<?php echo $no_image; ?>" src="<?php echo $data['custom-wp-login-logo']; ?>" height="<?php echo $height_image; ?>px" />
								<?php }else{ ?>
									<img data-src="<?php echo $no_image; ?>" src="<?php echo $image_src; ?>" height="<?php echo $height_image; ?>px" />
								<?php } ?>
							</div>
							<button type="button" class="upload_image_button button button-primary" data-browse="custom-wp-login-logo"><?php echo __('Browse'); ?></button>
							<?php if (isset($data['custom-wp-login-logo']) && !empty($data['custom-wp-login-logo'])){ ?>
								<button type="button" class="remove_image_button button button-cancel"><?php echo __('Remove'); ?></button>
							<?php } ?>
							<input type="hidden" name="custom-wp-login-logo" id="custom-wp-login-logo" value="<?php echo isset($data['custom-wp-login-logo']) && !empty($data['custom-wp-login-logo']) ? $data['custom-wp-login-logo'] : '' ; ?>" />
						</div>
					</div>
					<div class="form-row">
						<lable><?php echo __('Login page Background:'); ?></lable>
						<div class="form-element">
							<div class="form-image">
								<?php if (isset($data['background-wp-login-page']) && !empty($data['background-wp-login-page'])){ ?>
									<img data-src="<?php echo $no_image; ?>" src="<?php echo $data['background-wp-login-page']; ?>" height="<?php echo $height_image; ?>px" />
								<?php }else{ ?>
									<img data-src="<?php echo $no_image; ?>" src="<?php echo $image_src; ?>" height="<?php echo $height_image; ?>px" />
								<?php } ?>
							</div>
							<button type="button" class="upload_image_button button button-primary" data-browse="background-wp-login-page"><?php echo __('Browse'); ?></button>
							<?php if (isset($data['background-wp-login-page']) && !empty($data['background-wp-login-page'])){ ?>
								<button type="button" class="remove_image_button button button-cancel"><?php echo __('Remove'); ?></button>
							<?php } ?>
							<input type="hidden" name="background-wp-login-page" id="background-wp-login-page" value="<?php echo isset($data['background-wp-login-page']) && !empty($data['background-wp-login-page']) ? $data['background-wp-login-page'] : '' ; ?>" />
						</div>
					</div>
					<div class="form-row">
						<lable><?php echo __('Custom login css:'); ?></lable>
						<textarea name="custom-login-css"><?php echo isset($data['custom-login-css']) && !empty($data['custom-login-css']) ? $data['custom-login-css'] : '' ; ?></textarea>
					</div>
				</div>
			</li>
		</ul>
	</div>


	<h2><?php echo __('Dashboard settings'); ?></h2>
	<hr/>
	<div class="form-wrapper">
		<ul>
			<li>
				<div class="form-str">
					<div class="form-row">
						<lable><?php echo __('Change admin panel path:'); ?></lable>
						<input type="text" name="path-admin-panel" <?php echo isset($data['path-admin-panel']) && !empty($data['path-admin-panel']) ? 'value="' . $data['path-admin-panel'] . '"' : '' ; ?> />
					</div>
				</div>
				<div class="form-str">
					<div class="form-row">
						<lable><?php echo __('Add your own Welcome Panel?:'); ?></lable>
						<input type="checkbox" name="visible-welcome-panel" <?php echo isset($data['visible-welcome-panel']) && 'on' == $data['visible-welcome-panel'] ? 'checked="checked"' : '' ; ?> />
					</div>
				</div>
				<div id="visible-to" class="form-str">
					<div class="form-row">
						<lable><?php echo __('Visible To:'); ?></lable>

						<?php if (isset($roles) && !empty($roles)){ ?>
							<select multiple size="5" name="visible-welcome-group[]">
								<?php foreach($roles as $role => $role_info){ ?>
									<option value="<?php echo $role; ?>" <?php echo (isset($role_info['selected']) && FALSE !== $role_info['selected']) ? 'selected="selected"' : '' ; ?>><?php echo $role_info['name']; ?></option>
								<?php } ?>
							</select>
						<?php } ?>

<!--						<input type="text" name="visible-welcome-group" --><?php //echo isset($data['visible-welcome-group']) && !empty($data['visible-welcome-group']) ? 'value="' . $data['visible-welcome-group'] . '"' : '' ; ?><!-- />-->
					</div>
				</div>
				<div class="form-str">
					<div class="form-row">
						<lable><?php echo __('Hide Help Box:'); ?></lable>
						<input type="checkbox" name="visible-help-box" <?php echo isset($data['visible-help-box']) && 'on' == $data['visible-help-box'] ? 'checked="checked"' : '' ; ?> />
					</div>
				</div>
				<div class="form-str">
					<div class="form-row">
						<lable><?php echo __('Hide Screen Options:'); ?></lable>
						<input type="checkbox" name="visible-screen-options" <?php echo isset($data['visible-screen-options']) && 'on' == $data['visible-screen-options'] ? 'checked="checked"' : '' ; ?> />
					</div>
				</div>
			</li>
		</ul>
	</div>
	<hr/>
	<div>
		<?php wp_nonce_field('cherry-white-label-settings','cherry-white-label-settings-value'); ?>
		<input type="submit" class="button button-primary" value="<?php echo __('Save Change'); ?>s">
	</div>
</form>