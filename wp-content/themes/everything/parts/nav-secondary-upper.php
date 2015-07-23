<?php
/**
 * @package    WordPress
 * @subpackage Everything
 * @since      1.0
 */

$nav = Everything::po('layout/nav_secondary/upper/upper', '__hidden_ns', Everything::to_('nav/secondary/upper')->value());
if (!(\Drone\Func::wpAssignedMenu('secondary-upper') || is_numeric($nav)) || !apply_filters('everything_nav_secondary_upper_display', (bool)$nav)) {
	return;
}

?>

<div class="outer-container">
	<nav class="nav-menu secondary upper">
		<div class="container">
			<div class="section">
				<?php Everything::navMenu('secondary-upper', is_numeric($nav) ? $nav : null); ?>
			</div>
		</div>
	</nav>
</div><!-- // .outer-container -->