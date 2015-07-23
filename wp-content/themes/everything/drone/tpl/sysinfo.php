<?php
 ?>

<h3><?php _e('Informations', $this->instance->domain); ?></h3>
<table class="widefat">
	<colgroup>
		<col width="25%" />
		<col width="25%" />
		<col width="50%" />
	</colgroup>
	<thead>
		<tr>
			<th><?php _e('Name', $this->instance->domain); ?></th>
			<th><?php _e('Value', $this->instance->domain); ?></th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td><?php _e('PHP version', $this->instance->domain); ?></td>
			<td><?php echo PHP_VERSION; ?></td>
			<td><?php if ($this->notices['outdated_php']): ?>
				<span class="drone-info"><?php _e('You have outdated PHP version on your server. For better performance and reliability make an update (or ask your hosting provider for that).', $this->instance->domain); ?></span>
			<?php endif; ?></td>
		</tr>
		<tr>
			<td><?php _e('MySQL version', $this->instance->domain); ?></td>
			<td><?php
 if (isset($GLOBALS['wpdb']->dbh->server_info)) { echo esc_html($GLOBALS['wpdb']->dbh->server_info); } else if (function_exists('mysql_get_server_info')) { echo esc_html(mysql_get_server_info()); } ?></td>
			<td></td>
		</tr>
		<tr>
			<td><?php _e('WordPress version', $this->instance->domain); ?></td>
			<td><?php echo esc_html($this->instance->wp_version); ?></td>
			<td></td>
		</tr>
		<tr>
			<td><?php $this->instance->parent_theme === null ? _e('Theme version', $this->instance->domain) : _e('Parent theme version', $this->instance->domain); ?></td>
			<td><?php echo esc_html($this->instance->base_theme->version); ?></td>
			<td><?php if ($this->notices['version_corrupted']): ?>
				<span class="drone-error"><?php _e('Theme name/version information is corrupted. Probably one of theme files is damaged or modified. If you want to modify theme files, use child theme for that purpose.', $this->instance->domain); ?></span>
			<?php elseif ($this->notices['update_available']): ?>
				<span class="drone-info"><?php printf(__('New version (%1$s) of %2$s theme is available. <a href="%3$s">Update now</a>.'), $this->update->version, $this->instance->base_theme->name, get_admin_url(null, 'update-core.php')); ?></span>
			<?php endif; ?></td>
		</tr>
		<?php if ($this->instance->parent_theme !== null): ?>
			<tr>
				<td><?php _e('Theme version', $this->instance->domain); ?></td>
				<td><?php echo esc_html($this->instance->theme->version); ?></td>
				<td></td>
			</tr>
		<?php endif; ?>
	</tbody>
</table>

<h3><?php _e('Configuration', $this->instance->domain); ?></h3>
<table class="widefat">
	<colgroup>
		<col width="25%" />
		<col width="75%" />
	</colgroup>
	<thead>
		<tr>
			<th><?php _e('Name', $this->instance->domain); ?></th>
			<th><?php _e('Value', $this->instance->domain); ?></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td><?php _e('PHP max. execution time', $this->instance->domain); ?></td>
			<td><?php if (function_exists('ini_get')) echo ini_get('max_execution_time'); ?>s</td>
		</tr>
		<tr>
			<td><?php _e('PHP memory limit', $this->instance->domain); ?></td>
			<td><?php if (function_exists('ini_get')) echo ini_get('memory_limit').'B'; ?></td>
		</tr>
		<tr>
			<td><?php _e('WordPress memory limit', $this->instance->domain); ?></td>
			<td><?php echo WP_MEMORY_LIMIT; ?>B</td>
		</tr>
	</tbody>
</table>

<h3><?php _e('Paths', $this->instance->domain); ?></h3>
<table class="widefat">
	<colgroup>
		<col width="25%" />
		<col width="75%" />
	</colgroup>
	<thead>
		<tr>
			<th><?php _e('Name', $this->instance->domain); ?></th>
			<th><?php _e('Value', $this->instance->domain); ?></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td><?php _e('Home URL', $this->instance->domain); ?></td>
			<td class="code"><?php echo home_url(); ?></td>
		</tr>
		<tr>
			<td><?php _e('Site URL', $this->instance->domain); ?></td>
			<td class="code"><?php echo site_url(); ?></td>
		</tr>
		<tr>
			<td><?php _e('Template URL', $this->instance->domain); ?></td>
			<td class="code"><?php echo get_template_directory_uri(); ?></td>
		</tr>
		<tr>
			<td><?php _e('Stylesheet URL', $this->instance->domain); ?></td>
			<td class="code"><?php echo get_stylesheet_directory_uri(); ?></td>
		</tr>
		<tr>
			<td><?php _e('Template directory', $this->instance->domain); ?></td>
			<td class="code"><?php echo get_template_directory(); ?></td>
		</tr>
		<tr>
			<td><?php _e('Stylesheet directory', $this->instance->domain); ?></td>
			<td class="code"><?php echo get_stylesheet_directory(); ?></td>
		</tr>
	</tbody>
</table>

<h3><?php _e('Settings', $this->instance->domain); ?></h3>
<form action="<?php echo $_SERVER['REQUEST_URI'] ?>" method="post">
	<p><?php _e('Export Theme Options settings to file.', $this->instance->domain); ?></p>
	<p><input class="button" type="submit" value="<?php _e('Export settings', $this->instance->domain) ?>" name="settings-export" /></p>
</form>
<form action="<?php echo $_SERVER['REQUEST_URI'] ?>" method="post" enctype="multipart/form-data">
	<p>
		<label for="settings-file"><?php _e('Choose a file from your computer', $this->instance->domain); ?>:</label>
		<input type="file" name="settings-import-file" />
	</p>
	<p><input class="button" type="submit" value="<?php _e('Import settings', $this->instance->domain) ?>" name="settings-import" /></p>
</form>

<h3><?php _e('Options', $this->instance->domain); ?></h3>