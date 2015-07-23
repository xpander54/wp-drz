<?php
/**
 * @package    WordPress
 * @subpackage Everything
 * @since      1.0
 */

// -----------------------------------------------------------------------------

require get_template_directory().'/drone/drone.php'; // 5.2.2
require get_template_directory().'/inc/class-tgm-plugin-activation.php'; // 2.4

// -----------------------------------------------------------------------------

class Everything extends \Drone\Theme
{

	// -------------------------------------------------------------------------

	/**
	 * Default sidebar width
	 *
	 * @since 1.0
	 * @var int
	 */
	const DEFAULT_SIDEBAR_WIDTH = 308;

	/**
	 * Vector icons font path
	 *
	 * @since 1.0
	 * @var string
	 */
	const ICON_FONT_PATH = 'data/img/icons/icons.svg';

	/**
	 * LayerSlider WP plugin version
	 *
	 * @since 1.0
	 * @var string
	 */
	const LAYERSLIDER_VERSION = '5.3.2';

	/**
	 * Master Slider plugin version
	 *
	 * @since 1.0
	 * @var string
	 */
	const MASTERSLIDER_VERSION = '2.9.2';

	/**
	 * Revolution Slider plugin version
	 *
	 * @since 1.0
	 * @var string
	 */
	const REVSLIDER_VERSION = '4.6.5';

	// -------------------------------------------------------------------------

	/**
	 * Headline used status
	 *
	 * @since 1.0
	 * @var bool
	 */
	public static $headline_used = false;

	/**
	 * Previous posts stack
	 *
	 * @since 1.5
	 * @var array
	 */
	public static $posts_stack = array();

	/**
	 * Max width
	 *
	 * @since 1.7
	 * @var int
	 */
	protected static $max_width = 1140;

	// -------------------------------------------------------------------------

	/**
	 * Add meta options
	 *
	 * @since 1.0
	 *
	 * @param  object $group
	 * @param  bool   $default_visible
	 * @param  array  $default_items
	 * @return object
	 */
	protected function addMetaOptions($group, $default_visible, $default_items)
	{
		$visible = $group->addOption('boolean', 'visible', $default_visible, '', '', array('caption' => __('Visible', 'everything')));
		return $group->addOption('group', 'items', $default_items, '', '', array('options' => array(
			'date_time'  => __('Date &amp time', 'everything'),
			'date'       => __('Date', 'everything'),
			'mod_date'   => __('Modification date', 'everything'),
			'time_diff'  => __('Relative time', 'everything'),
			'comments'   => __('Comments number', 'everything'),
			'author'     => __('Author', 'everything'),
			'permalink'  => __('Permalink', 'everything')
		), 'indent' => true, 'multiple' => true, 'sortable' => true, 'owner' => $visible));
	}

	// -------------------------------------------------------------------------

	/**
	 * Add post meta options
	 *
	 * @since 1.0
	 *
	 * @param  object $group
	 * @param  bool   $default_visible
	 * @param  array  $default_items
	 * @return object
	 */
	protected function addPostMetaOptions($group, $default_visible, $default_items)
	{
		$visible = $group->addOption('boolean', 'visible', $default_visible, '', '', array('caption' => __('Visible', 'everything')));
		return $group->addOption('group', 'items', $default_items, '', '', array('options' => array(
			'date_time'  => __('Date &amp time', 'everything'),
			'date'       => __('Date', 'everything'),
			'mod_date'   => __('Modification date', 'everything'),
			'time_diff'  => __('Relative time', 'everything'),
			'comments'   => __('Comments number', 'everything'),
			'categories' => __('Categories', 'everything'),
			'tags'       => __('Tags', 'everything'),
			'author'     => __('Author', 'everything'),
			'permalink'  => __('Permalink', 'everything')
		), 'indent' => true, 'multiple' => true, 'sortable' => true, 'owner' => $visible));
	}

	// -------------------------------------------------------------------------

	/**
	 * Add social buttons options
	 *
	 * @since 1.0
	 *
	 * @param  object $group
	 * @param  bool   $default_visible
	 * @param  array  $default_items
	 * @return object
	 */
	protected function addSocialButtonsOptions($group, $default_visible, $default_items)
	{
		$visible = $group->addOption('boolean', 'visible', $default_visible, '', '', array('caption' => __('Visible', 'everything')));
		return $group->addOption('group', 'items', $default_items, '', '', array('options' => array(
			'facebook'   => __('Facebook', 'everything'),
			'twitter'    => __('Twitter', 'everything'),
			'googleplus' => __('Google+', 'everything'),
			'linkedin'   => __('LinkedIn', 'everything'),
			'pinterest'  => __('Pinterest', 'everything')
		), 'indent' => true, 'multiple' => true, 'sortable' => true, 'owner' => $visible));
	}

	// -------------------------------------------------------------------------

	/**
	 * Options setup
	 *
	 * @since 1.0
	 *
	 * @param $theme_options
	 */
	protected function onSetupOptions(\Drone\Options\Group\Theme $theme_options)
	{
		require $this->template_dir.'/inc/options-setup.php';
	}

	// -------------------------------------------------------------------------

	/**
	 * Theme options compatybility
	 *
	 * @since 1.7
	 *
	 * @param array  $data
	 * @param string $version
	 */
	public function onThemeOptionsCompatybility(array &$data, $version)
	{

		// 1.7
		if (version_compare($version, '1.7-alpha-3') < 0) {

			$conditional_tags_migrate = function($data, $sidebars_widgets = false) {
				foreach ($_ = $data as $tag => $value) {
					if ($sidebars_widgets) {
						if (!preg_match('/^footer-(?P<tag>.+)-(?P<i>[0-5])$/', $tag, $footer_sidebar)) {
							continue;
						}
						$tag = $footer_sidebar['tag'];
					}
					$new_tag = false;
					if (preg_match('/^(post_type_|term_|bbpress|woocommerce)/', $tag)) { // new format
						continue;
					}
					else if (in_array($tag, array('default', 'front_page', 'blog', 'search', '404'))) { // general
						continue;
					}
					else if (in_array($tag, array('forum', 'topic'))) { // bbpress
						$new_tag = 'bbpress_'.$tag;
					}
					else if (in_array($tag, array('shop', 'cart', 'checkout', 'order_received_page', 'account_page'))) { // woocommerce
						$new_tag = 'woocommerce_'.$tag;
					}
					else if (strpos($tag, 'template_') === 0) { // template
						if (!preg_match('/.\.php$/', $tag)) {
							foreach (array_keys(Everything::getInstance()->theme->get_page_templates()) as $template) {
								if ($tag == \Drone\Func::stringID('template_'.preg_replace('/\.php$/i', '', $template), '_')) {
									$new_tag = 'template_'.preg_replace('/\.(php)$/i', '_\1', $template);
									break;
								}
							}
						}
					}
					else if (preg_match('/^[_a-z]+_[0-9]+$/', $tag)) { // taxonomy
						if (preg_match('/^(portfolio_(category|tag)|topic_tag)_/', $tag)) {
							$new_tag = 'term_'.preg_replace('/_/', '-', $tag, 1);
						} else {
							$new_tag = 'term_'.$tag;
						}
					}
					else if (preg_match('/^[_a-z]+$/', $tag)) { // post type
						$new_tag = 'post_type_'.$tag;
					}
					if ($new_tag !== false) {
						if ($sidebars_widgets) {
							$tag = $footer_sidebar[0];
							$new_tag = "footer-{$new_tag}-{$footer_sidebar['i']}";
						}
						unset($data[$tag]);
						$data[$new_tag] = $value;
					}
				}
				return $data;
			};

			if (isset($data['general']['layout']) && is_array($data['general']['layout'])) {
				$data['general']['layout'] = $conditional_tags_migrate($data['general']['layout']);
			}
			if (isset($data['general']['max_width']) && is_array($data['general']['max_width'])) {
				$data['general']['max_width'] = $conditional_tags_migrate($data['general']['max_width']);
			}
			if (isset($data['general']['background']['background']) && is_array($data['general']['background']['background'])) {
				$data['general']['background']['background'] = $conditional_tags_migrate($data['general']['background']['background']);
			}
			if (isset($data['banner']['content']) && is_array($data['banner']['content'])) {
				$data['banner']['content'] = $conditional_tags_migrate($data['banner']['content']);
			}
			if (isset($data['nav']['secondary']['upper']) && is_array($data['nav']['secondary']['upper'])) {
				$data['nav']['secondary']['upper'] = $conditional_tags_migrate($data['nav']['secondary']['upper']);
			}
			if (isset($data['nav']['secondary']['lower']) && is_array($data['nav']['secondary']['lower'])) {
				$data['nav']['secondary']['lower'] = $conditional_tags_migrate($data['nav']['secondary']['lower']);
			}
			if (isset($data['nav']['headline']) && is_array($data['nav']['headline'])) {
				$data['nav']['headline'] = $conditional_tags_migrate($data['nav']['headline']);
			}
			if (isset($data['sidebar']['layout']) && is_array($data['sidebar']['layout'])) {
				$data['sidebar']['layout'] = $conditional_tags_migrate($data['sidebar']['layout']);
			}
			if (isset($data['footer']['layout']) && is_array($data['footer']['layout'])) {
				$data['footer']['layout'] = $conditional_tags_migrate($data['footer']['layout']);
			}

			if (($sidebars_widgets = get_option('sidebars_widgets')) !== false && is_array($sidebars_widgets)) {
				$new_sidebars_widgets = $conditional_tags_migrate($sidebars_widgets, true);
				if ($sidebars_widgets !== $new_sidebars_widgets) {
					update_option('sidebars_widgets', $new_sidebars_widgets);
				}
			}

		}

	}

	// -------------------------------------------------------------------------

	/**
	 * Theme setup
	 *
	 * @since 1.0
	 * @see \Drone\Theme::onSetupTheme()
	 */
	protected function onSetupTheme()
	{

		// Theme features
		$this->addThemeFeature('x-ua-compatible');
		$this->addThemeFeature('nav-menu-current-item');
		$this->addThemeFeature('force-img-caption-shortcode-filter');

		// Editor style
		add_editor_style('data/css/wp/editor.css');

		// Menus
		register_nav_menus(array(
			'main-desktop'      => __('Main menu (desktop)', 'everything'),
			'main-mobile'       => __('Main menu (mobile)', 'everything'),
			'additional-mobile' => __('Additional menu (mobile)', 'everything'),
			'top-bar-desktop'   => __('Top bar menu (desktop)', 'everything'),
			'top-bar-mobile'    => __('Top bar menu (mobile)', 'everything'),
			'secondary-upper'   => __('Upper secondary menu', 'everything'),
			'secondary-lower'   => __('Lower secondary menu', 'everything')
		));

		// Title
		add_theme_support('title-tag');
		if (version_compare($this->wp_version, '4.1-beta1') < 0) { // @deprecated 1.5

			add_action('wp_head', function() {
				echo '<title>'.wp_title('-', false, 'right').'</title>';
			});

			add_filter('wp_title', function($title, $sep) {
				if (is_feed()) {
					return $title;
				}
				if ((is_home() || is_front_page()) && ($description = get_bloginfo('description', 'display'))) {
					return $sep ? get_bloginfo('name')." {$sep} {$description}" : get_bloginfo('name');
				} else {
					return $sep ? $title.get_bloginfo('name') : $title;
				}
			}, 10, 2);

		}

		// Images
		add_theme_support('post-thumbnails');
		if (Everything::to('general/retina')) {
			$this->addThemeFeature('retina-image-size');
		}

		$thumbnail_size = Everything::to('post/thumbnail/size');
		$max_width = Everything::to_('general/max_width')->value('default');

		add_image_size('post-thumbnail', $thumbnail_size['width'], $thumbnail_size['height'], true);
		add_image_size('post-thumbnail-mini', 56, 84, false);
		add_image_size('post-thumbnail-mini-crop', 56, 56, true);
		add_image_size('logo', 999, 60, false);
		add_image_size('full-hd', 1920, 2880, false);

		add_image_size('auto',   236, 354,  false); // mobile 3 columns
		add_image_size('auto-1', 364, 546,  false); // mobile 2 columns
		add_image_size('auto-2', 748, 1122, false); // mobile 1 column
		add_image_size('auto-3', 960, 1140, false);
		add_image_size('max-width', $max_width['default'], round($max_width['default']*1.5), false);

		// Post formats
		add_theme_support('post-formats', array(
			'aside', 'audio', 'chat', 'gallery', 'image', 'link', 'quote', 'status', 'video'
		));

		// Shortcodes
		$this->addThemeFeature('shortcode-page');
		$this->addThemeFeature('shortcode-no-format');

		// Classes
		\Drone\Options\Option\Font::$always_used = array('Open Sans', 'Montserrat');

		// bbPress
		if (Everything::isPluginActive('bbpress')) {
			$func = function($show) { return is_bbpress() ? false : $show; };
			add_filter('everything_meta_display', $func, 20);
			add_filter('everything_social_buttons_display', $func, 20);
		}

		// Captcha
		if (Everything::isPluginActive('captcha')) {
			if (has_action('comment_form_after_fields', 'cptch_comment_form_wp3')) {
				remove_action('comment_form_after_fields', 'cptch_comment_form_wp3', 1);
				remove_action('comment_form_logged_in_after', 'cptch_comment_form_wp3', 1);
				add_filter('comment_form_field_comment', function($comment_field) {
					$captcha = \Drone\Func::functionGetOutputBuffer('cptch_comment_form_wp3');
					$captcha = preg_replace('#<br( /)?>#', '', $captcha);
					return $comment_field.$captcha;
				});
			}
		}

		// WooCommerce
		if (Everything::isPluginActive('woocommerce')) {
			require $this->template_dir.'/inc/woocommerce.php';
			add_theme_support('woocommerce');
		}

		// WooCommerce Brands
		if (Everything::isPluginActive('woocommerce-brands')) {
			remove_action('woocommerce_product_meta_end', array($GLOBALS['WC_Brands'], 'show_brand'));
		}

		// WPML
		if (Everything::isPluginActive('wpml')) {
			define('ICL_DONT_LOAD_LANGUAGE_SELECTOR_CSS', true);
		}

		// Illegal
		if (!is_admin() && Everything::isIllegal()) {
			Everything::to_('footer/end_note/right')->value = Everything::to_('footer/end_note/right')->default;
		}

	}

	// -------------------------------------------------------------------------

	/**
	 * Initialization
	 *
	 * @since 1.0
	 * @see \Drone\Theme::onInit()
	 */
	public function onInit()
	{

		// Gallery
 		register_post_type('gallery', apply_filters('everything_register_post_type_gallery_args', array(
			'label'       => __('Galleries', 'everything'),
			'description' => __('Galleries', 'everything'),
			'public'      => true,
			'menu_icon'   => 'dashicons-images-alt2',
			'supports'    => array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'trackbacks', 'custom-fields', 'comments', 'revisions'),
			'rewrite'     => array('slug' => Everything::to('gallery/slug')),
			'labels'      => array(
				'name'               => __('Galleries', 'everything'),
				'singular_name'      => __('Gallery', 'everything'),
				'add_new'            => _x('Add New', 'Gallery', 'everything'),
				'all_items'          => __('All Galleries', 'everything'),
				'add_new_item'       => __('Add New Gallery', 'everything'),
				'edit_item'          => __('Edit Gallery', 'everything'),
				'new_item'           => __('New Gallery', 'everything'),
				'view_item'          => __('View Gallery', 'everything'),
				'search_items'       => __('Search Galleries', 'everything'),
				'not_found'          => __('No Galleries found', 'everything'),
				'not_found_in_trash' => __('No Galleries found in Trash', 'everything'),
				'menu_name'          => __('Galleries', 'everything')
			)
		)));

 		// Portfolio
		register_post_type('portfolio', apply_filters('everything_register_post_type_portfolio_args', array(
			'label'        => __('Portfolios', 'everything'),
			'description'  => __('Portfolios', 'everything'),
			'public'       => true,
			'menu_icon'    => 'dashicons-exerpt-view',
			'hierarchical' => true,
			'supports'     => array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'trackbacks', 'custom-fields', 'comments', 'revisions', 'page-attributes'),
			//'taxonomies'   => array('portfolio-category', 'portfolio-tag'),
			'has_archive'  => true,
			'rewrite'      => array('slug' => Everything::to('portfolio/slug')),
			'labels'       => array(
				'name'               => __('Portfolios', 'everything'),
				'singular_name'      => __('Portfolio', 'everything'),
				'add_new'            => _x('Add New', 'Portfolio', 'everything'),
				'all_items'          => __('All Portfolios', 'everything'),
				'add_new_item'       => __('Add New Portfolio', 'everything'),
				'edit_item'          => __('Edit Portfolio', 'everything'),
				'new_item'           => __('New Portfolio', 'everything'),
				'view_item'          => __('View Portfolio', 'everything'),
				'search_items'       => __('Search Portfolios', 'everything'),
				'not_found'          => __('No Portfolios found', 'everything'),
				'not_found_in_trash' => __('No Portfolios found in Trash', 'everything'),
				'menu_name'          => __('Portfolios', 'everything')
			)
		)));
		register_taxonomy('portfolio-category', array('portfolio'), apply_filters('everything_register_taxonomy_portfolio_category_args', array(
			'label'        => __('Categories', 'everything'),
			'hierarchical' => true,
			'rewrite'      => array('slug' => Everything::to('portfolio/slug').'-category')
		)));
		register_taxonomy('portfolio-tag', array('portfolio'), apply_filters('everything_register_taxonomy_portfolio_tag_args', array(
			'label'        => __('Tags', 'everything'),
			'hierarchical' => false,
			'rewrite'      => array('slug' => Everything::to('portfolio/slug').'-tag')
		)));

	}

	// -------------------------------------------------------------------------

	/**
	 * Widgets initialization
	 *
	 * @since 1.0
	 * @see \Drone\Theme::onWidgetsInit()
	 */
	public function onWidgetsInit()
	{

		// Built-in sidebars
		foreach (Everything::to_('sidebar/list/builtin')->childs() as $id => $sidebar) {
			register_sidebar(array(
				'id'            => $id,
				'name'          => $sidebar->label,
				'before_widget' => '<section id="%1$s" class="section widget %2$s">',
				'after_widget'  => '</section>',
				'before_title'  => '<h2 class="title">',
				'after_title'   => '</h2>'
			));
		}

		// Additional sidebars
		foreach (Everything::to('sidebar/list/additional') as $id => $sidebar) {
			register_sidebar(array(
				'id'            => $id,
				'name'          => $sidebar['id'],
				'before_widget' => '<section id="%1$s" class="section widget %2$s">',
				'after_widget'  => '</section>',
				'before_title'  => '<h2 class="title">',
				'after_title'   => '</h2>'
			));
		}

		// Footer sidebars
		if (is_admin()) {
			$tags = \Drone\Options\Option\ConditionalTags::getTagsList();
		}
		foreach (Everything::to('footer/layout') as $tag => $layout) {

			for ($i = 0; $i < count(Everything::stringToColumns($layout)); $i++) {

				if ($tag == 'default') {
					$name = sprintf(__('Footer column %d', 'everything'), $i+1);
				} else {
					$name = sprintf(__('(%1$s) Footer column %2$d', 'everything'), isset($tags[$tag]) ? $tags[$tag]['caption'] : '~'.$tag, $i+1);
				}

				register_sidebar(array(
					'name'          => $name,
					'id'            => "footer-{$tag}-{$i}",
					'before_widget' => '<div id="%1$s" class="widget %2$s">',
					'after_widget'  => '</div>',
					'before_title'  => '<h2 class="title">',
					'after_title'   => '</h2>'
				));

			}

		}

		// Widgets
		$this->addThemeFeature('widget-unwrapped-text');
		$this->addThemeFeature('widget-page');
		$this->addThemeFeature('widget-posts-list');
		$this->addThemeFeature('widget-twitter');
		$this->addThemeFeature('widget-flickr');
		$this->addThemeFeature('widget-facebook-like-box');

		// WooCommerce
		if (Everything::isPluginActive('woocommerce')) {

			require $this->template_dir.'/inc/woocommerce-widgets.php';

			foreach (array(
				'WC_Widget_Best_Sellers',
				'WC_Widget_Cart',
				'WC_Widget_Featured_Products',
				'WC_Widget_Layered_Nav_Filters',
				'WC_Widget_Layered_Nav',
				'WC_Widget_Onsale',
				'WC_Widget_Price_Filter',
				'WC_Widget_Product_Categories',
				'WC_Widget_Product_Search',
				'WC_Widget_Product_Tag_Cloud',
				'WC_Widget_Products',
				'WC_Widget_Random_Products',
				'WC_Widget_Recent_Products',
				'WC_Widget_Recent_Reviews',
				'WC_Widget_Recently_Viewed',
				'WC_Widget_Top_Rated_Products'
			) as $class) {
				if (class_exists($class)) {
					unregister_widget($class);
					register_widget('Everything_'.$class);
				}
			}

		}

		// WooCommerce Brands
		if (Everything::isPluginActive('woocommerce-brands')) {

			require $this->template_dir.'/inc/woocommerce-brands-widgets.php';

			foreach (array(
				'WC_Widget_Brand_Nav'
			) as $class) {
				if (class_exists($class)) {
					unregister_widget($class);
					register_widget('Everything_'.$class);
				}
			}

		}

	}

	// -------------------------------------------------------------------------

	/**
	 * tgmpa_register action
	 *
	 * @internal action: tgmpa_register
	 * @since 1.0
	 */
	public function actionTGMPARegister()
	{
		$plugins = array(
			array(
			    'name'               => 'LayerSlider',
			    'slug'               => 'LayerSlider',
			    'source'             => $this->template_dir.'/plugins/layerslider.zip',
			    'required'           => false,
			    'version'            => Everything::LAYERSLIDER_VERSION,
			    'force_activation'   => false,
			    'force_deactivation' => true
			),
			array(
			    'name'               => 'Master Slider',
			    'slug'               => 'masterslider',
			    'source'             => $this->template_dir.'/plugins/masterslider.zip',
			    'required'           => false,
			    'version'            => Everything::MASTERSLIDER_VERSION,
			    'force_activation'   => false,
			    'force_deactivation' => true
			),
			array(
			    'name'               => 'Revolution Slider',
			    'slug'               => 'revslider',
			    'source'             => $this->template_dir.'/plugins/revslider.zip',
			    'required'           => false,
			    'version'            => Everything::REVSLIDER_VERSION,
			    'force_activation'   => false,
			    'force_deactivation' => true
			)
		);
		tgmpa($plugins);
	}

	// -------------------------------------------------------------------------

	/**
	 * wp_enqueue_scripts action
	 *
	 * @internal action: wp_enqueue_scripts
	 * @since 1.0
	 */
	public function actionWPEnqueueScripts()
	{

		// Debug
		$this->beginMarker(__METHOD__);

		$min_sufix = !$this->debug_mode ? '.min' : '';
		$ver = $this->base_theme->version;

		// 3rd party styles
		wp_enqueue_style('everything-3rd-party', $this->template_uri.'/data/css/3rd-party.min.css', array(), $ver);

		// Main style
		wp_enqueue_style('everything-style', $this->template_uri."/data/css/style{$min_sufix}.css", array(), $ver);

		// Color scheme
		wp_enqueue_style('everything-scheme', $this->template_uri.'/data/css/'.Everything::to('general/scheme').$min_sufix.'.css', array(), $ver);

		// Responsive design
		if (Everything::to('general/responsive')) {
			wp_enqueue_style('everything-mobile', $this->template_uri."/data/css/mobile{$min_sufix}.css", array(), $ver, 'only screen and (max-width: 767px)');
		}

		// Stylesheet
		wp_enqueue_style('everything-stylesheet', get_stylesheet_uri());

		// Leading color
		$this->addDocumentStyle(sprintf(
<<<'EOS'
			a,
			a.alt:hover,
			.alt a:hover,
			h1 a:hover, h2 a:hover, h3 a:hover, h4 a:hover, h5 a:hover, h6 a:hover,
			.color,
			.toggles > div > h3:hover > i,
			.nav-menu a:hover,
			.nav-menu .current > a, .nav-menu .current > a:hover,
			.mobile-nav-menu a:hover,
			.mobile-nav-menu .current > a, .mobile-nav-menu .current > a:hover,
			.aside-nav-menu a:hover,
			.aside-nav-menu .current:not(.current-menu-parent):not(.current-menu-ancestor) > a, .aside-nav-menu .current:not(.current-menu-parent):not(.current-menu-ancestor) > a:hover {
				color: %1$s;
			}

			mark,
			.background-color,
			.sy-pager li.sy-active a {
				background-color: %1$s;
			}

			.zoom-hover > .zoom-hover-overlay {
				background-color: rgba(%2$s, 0.75);
			}

			blockquote.bar,
			.sticky:before {
				border-color: %1$s;
			}
EOS
			,
			Everything::to('general/color'),
			implode(', ', array_map('hexdec', str_split(substr(Everything::to('general/color'), 1), 2)))
		));

		// Comment reply
		if (is_singular() && comments_open() && get_option('thread_comments')) {
			wp_enqueue_script('comment-reply');
		}

		// 3rd party scripts
		wp_enqueue_script('everything-3rd-party', $this->template_uri.'/data/js/3rd-party.min.js', array('jquery'), $ver, true);

		// Main script
		wp_enqueue_script('everything-script', $this->template_uri."/data/js/everything{$min_sufix}.js", array('jquery'), $ver, true);

		// WooCommerce
		if (Everything::isPluginActive('woocommerce')) {
			wp_deregister_script('wc-single-product');
			wp_register_script('wc-single-product', $this->template_uri."/data/js/woocommerce/single-product{$min_sufix}.js", array('jquery'), $ver, true);
		}

		// Configuration
		$this->addDocumentScript(sprintf('everythingConfig = %s;', json_encode(array(
			'retinaSupport'   => Everything::to('general/retina'),
			'fancyboxOptions' => Everything::to('site/image/fancybox_horizontal_fit_only') ? array('maxWidth' => '100%', 'fitToView' => false) : array(),
			'zoomHoverIcons'  => array_map(function($s) { return 'icon-'.$s; }, Everything::to_('site/hover_icons')->toArray()),
			'captions'        => array('bricksAllButton' => __('all', 'everything'))
		))));

		// Max. width style
		if (!Everything::to_('general/max_width')->isDefault()) {
			Everything::$max_width = Everything::to_('general/max_width')->value();
			$this->addDocumentStyle(sprintf(
<<<'EOS'
				.layout-boxed .outer-container, .container {
					max-width: %dpx;
				}
EOS
			, Everything::$max_width));
		}

		// Colors styles
		foreach (Everything::to_('color')->childs() as $name => $group) {
			if ($group->child('enabled')->value) {
				$color = $group->child($name);
				$hsl = \Drone\Func::colorRGBToHSL(\Drone\Func::cssColorToDec($color->value));
				$hsl[2] = max($hsl[2]-0.12, 0);
				$darken = \Drone\Func::cssDecToColor(\Drone\Func::colorHSLToRGB($hsl));
				$this->addDocumentStyle(sprintf($color->tag, $color->value, $darken));
			}
		}

		// Header style
		if (Everything::to_('header/style/settings')->value('floated')) {
			if (Everything::to('header/style/opacity')) {
				$this->addDocumentStyle(sprintf('#header:before { opacity: %.2f; }', Everything::to('header/style/opacity')/100));
			} else {
				$this->addDocumentStyle('#header:before { display: none; }');
			}
		}

		// Content
		if (!is_null($background = Everything::io_('layout/background/background', 'general/background/background', '__hidden_ns', '__hidden'))) {
			if ($background instanceof \Drone\Options\Option\ConditionalTags) {
				$background = $background->option();
			}
			if (!$background->option('opacity')->isDefault()) {
				$this->addDocumentStyle(sprintf(
<<<'EOS'
					.layout-boxed #content aside.aside .aside-nav-menu .current:not(.current-menu-parent):not(.current-menu-ancestor) > a:before {
						display: none;
					}
					#content:before {
						opacity: %.2f;
					}
EOS
				, $background->property('opacity')/100));
			}
		}

		// Fonts styles
		foreach (\Drone\Options\Option\Font::getInstances() as $font) {
			if ($font->isVisible() && !is_null($font->tag)) {
				foreach ((array)$font->tag as $selector) {
					$this->addDocumentStyle($font->css($selector));
				}
			}
		}

		// MediaElement.js progress bar color
		if (version_compare($this->wp_version, '4.0') >= 0) {
			$this->addDocumentStyle(sprintf(
<<<'EOS'
				.mejs-container .mejs-controls .mejs-time-rail .mejs-time-current {
					background-color: %s;
				}
EOS
			, Everything::to('general/color')));
		}

		// Google Chrome fonts fix
		if (Everything::to('advanced/chrome_fonts_fix')) {
			$this->addDocumentStyle(
<<<'EOS'
				@media screen and (-webkit-min-device-pixel-ratio: 0) {
					* {
						-webkit-font-smoothing: antialiased;
						-webkit-text-stroke: 0.1pt;
					}
					h1, h2, h3, .button.big *, .button.huge *, #header * {
						-webkit-text-stroke: 0.2pt;
					}
				}
EOS
			);
		}

		// List widgets script
		if (is_active_widget(false, false, 'pages') ||
			is_active_widget(false, false, 'archives') ||
			is_active_widget(false, false, 'categories') ||
			is_active_widget(false, false, 'recent-posts') ||
			is_active_widget(false, false, 'recent-comments') ||
			is_active_widget(false, false, 'bbp_forums_widget') ||
			is_active_widget(false, false, 'bbp_replies_widget') ||
			is_active_widget(false, false, 'bbp_topics_widget') ||
			is_active_widget(false, false, 'bbp_views_widget')) {
			$this->addDocumentJQueryScript(
<<<'EOS'
				$('.widget_pages, .widget_archive, .widget_categories, .widget_recent_entries, .widget_recent_comments, .widget_display_forums, .widget_display_replies, .widget_display_topics, .widget_display_views').each(function() {
					$('ul', this).addClass('fancy alt');
					$('li', this).prepend($('<i />', {'class': 'icon-right-open'}));
					if ($(this).closest('#content').length > 0) {
						$('li > .icon-right-open', this).addClass('color');
					}
				});
EOS
			);
		}

		// Menu widgets script
		if (is_active_widget(false, false, 'meta') ||
			is_active_widget(false, false, 'nav_menu')) {
			$this->addDocumentJQueryScript(
<<<'EOS'
				$('.widget_meta, .widget_nav_menu').each(function() {
					if ($(this).is('#content .widget')) {
						$('> div:has(> ul)', this).replaceWith(function() { return $(this).contents(); });
						$('ul:first', this).wrap('<nav class="aside-nav-menu"></nav>');
					} else {
						$('ul', this).addClass('fancy alt');
						$('li', this).prepend($('<i />', {'class': 'icon-right-open'}));
					}
				});
EOS
			);
		}

		// Calendar widget
		if (is_active_widget(false, false, 'calendar')) {
			$this->addDocumentJQueryScript(
<<<'EOS'
				$('.widget_calendar #calendar_wrap > table').unwrap();
EOS
			);
		}

		// Tag cloud widget
		if (is_active_widget(false, false, 'tag_cloud')) {
			$this->addDocumentJQueryScript(
<<<'EOS'
				$('.widget_tag_cloud .tagcloud').wrapInner('<div />').find('a').addClass('button small').css('font-size', '');
EOS
			);
		}

		// bbPress
		if (Everything::isPluginActive('bbpress') && is_active_widget(false, false, 'bbp_replies_widget')) {
			$this->addDocumentJQueryScript(
<<<'EOS'
				$('.widget_display_replies li > div').addClass('small');
EOS
			);
		}

		// Disqus Comment System
		if (Everything::isPluginActive('disqus')) {
			$this->addDocumentJQueryScript(
<<<'EOS'
				$('#disqus_thread').addClass('section');
EOS
			);
		}

		// WooCommerce
		if (Everything::isPluginActive('woocommerce')) {
			if (!is_null($color = Everything::to('woocommerce/cart/color', '__default'))) {
				$this->addDocumentStyle(sprintf(
<<<'EOS'
					i.icon-woocommerce-cart {
						color: %s;
					}
EOS
					,
					$color
				));
			}
			$this->addDocumentStyle(sprintf(
<<<'EOS'
				.widget_price_filter .ui-slider .ui-slider-range,
				.widget_price_filter .ui-slider .ui-slider-handle {
					background-color: %s;
				}
EOS
				,
				Everything::to('general/color')
			));
			if (Everything::to('woocommerce/onsale/custom')) {
				$this->addDocumentStyle(sprintf(
<<<'EOS'
					.woocommerce .onsale,
					.woocommerce-page .onsale {
						background: %s;
						color: %s;
					}
EOS
					,
					Everything::to('woocommerce/onsale/background'),
					Everything::to('woocommerce/onsale/color')
				));
			}
			if (Everything::to('woocommerce/rating/custom')) {
				$this->addDocumentStyle(sprintf(
<<<'EOS'
					.woocommerce .rating i:not(.pad),
					.woocommerce-page .rating i:not(.pad) {
						color: %s;
					}
EOS
					,
					Everything::to('woocommerce/rating/color')
				));
			}
			if (\Drone\Options\Option\ConditionalTags::is('account_page')) {
				$this->addDocumentJQueryScript(
<<<'EOS'
					$('.woocommerce .my_account_orders .order-actions .button').addClass('small');
EOS
				);
			}
		}

		// WooCommerce Brands
		if (Everything::isPluginActive('woocommerce-brands')) {
			wp_dequeue_style('brands-styles');
		}

		// WPML
		if (Everything::isPluginActive('wpml')) {
			if (is_active_widget(false, false, 'icl_lang_sel_widget')) {
				$this->addDocumentJQueryScript(
<<<'EOS'
					$('.widget_icl_lang_sel_widget').each(function() {
						$('ul', this).unwrap().addClass('simple alt');
						$('img', this).addClass('icon');
					});
EOS
				);
			}
		}

		// Debug
		$this->endMarker(__METHOD__);

	}

	// -------------------------------------------------------------------------

	/**
	 * pre_get_posts action
	 *
	 * @internal action: pre_get_posts
	 * @since 1.0
	 *
	 * @param object $query
	 */
	public function actionPreGetPosts($query)
	{
		if ($query->is_tax('portfolio-category') || $query->is_tax('portfolio-tag')) {
			$query->query_vars['posts_per_page'] = Everything::to('portfolio/archive/count');
		}
	}

	// -------------------------------------------------------------------------

	/**
	 * the_post action
	 *
	 * @internal action: the_post
	 * @since 1.5
	 *
	 * @param \WP_Query $query
	 */
	public function actionThePost(&$post)
	{
		Everything::$posts_stack[] = $post->ID;
	}

	// -------------------------------------------------------------------------

	/**
	 * comment_form_before_fields action
	 *
	 * @internal action: comment_form_before_fields
	 * @since 1.0
	 */
	public function actionCommentFormBeforeFields()
	{
		echo '<div class="columns alt-mobile"><ul>';
	}

	// -------------------------------------------------------------------------

	/**
	 * comment_form_after_fields action
	 *
	 * @internal action: comment_form_after_fields
	 * @since 1.0
	 */
	public function actionCommentFormAfterFields()
	{
		echo '</ul></div>';
	}

	// -------------------------------------------------------------------------

	/**
	 * layerslider_ready action
	 *
	 * @internal action: layerslider_ready
	 * @since 1.0
	 */
	public function actionLayersliderReady()
	{
		$GLOBALS['lsAutoUpdateBox'] = false;
	}

	// -------------------------------------------------------------------------

	/**
	 * woocommerce_single_product_summary action
	 *
	 * @internal action: woocommerce_single_product_summary, 35
	 * @since 1.0
	 */
	public function actionWoocommerceSingleProductSummary()
	{

		if (!Everything::isPluginActive('woocommerce-brands') || !Everything::to('woocommerce/product/brands')) {
			return;
		}

		// Brand
		$brands = wp_get_post_terms(get_the_ID(), 'product_brand', array('fields' => 'ids'));

		if (count($brands) == 0) {
			return;
		}
		$brand = get_term($brands[0], 'product_brand');

		// Validation
		if (!$brand->description) {
			return;
		}

		// HTML
		$html = \Drone\HTML::make();
		$html->addNew('hr');

		// Thumbnail
		if ($thumbnail_id = get_woocommerce_term_meta($brand->term_id, 'thumbnail_id', true)) {
			$html->addNew('figure')
				->class('alignleft')
				->addNew('a')
					->attr(Everything::getImageAttrs('a', array('border' => false, 'hover' => '')))
					->href(get_term_link($brand, 'product_brand'))
					->title($brand->name)
					->add(wp_get_attachment_image($thumbnail_id, 'logo'));
		}

		// Description
		$html->add(wpautop(wptexturize($brand->description)));

		echo $html->html();

	}

	// -------------------------------------------------------------------------

	/**
	 * image_size_names_choose filter
	 *
	 * @internal filter: image_size_names_choose, 100
	 * @since 1.0
	 *
	 * @param  array $sizes
	 * @return array
	 */
	public function filterImageSizeNamesChoose($sizes)
	{
		$sizes = \Drone\Func::arrayInsert($sizes, array('logo' => __('Logo', 'everything')), 'thumbnail');
		$sizes = \Drone\Func::arrayInsert($sizes, array('max-width' => __('Site width', 'everything')), 'full');
		return $sizes;
	}

	// -------------------------------------------------------------------------

	/**
	 * body_class filter
	 *
	 * @internal filter: body_class
	 * @since 1.0
	 *
	 * @param  array $classes
	 * @return array
	 */
	public function filterBodyClass($classes)
	{
		if (Everything::isPluginActive('wpml')) {
			$classes[] = 'lang-'.ICL_LANGUAGE_CODE;
		}
		$classes[] = 'layout-'.Everything::to_('general/layout')->value();
		$classes[] = 'scheme-'.Everything::to('general/scheme');
		return $classes;
	}

	// -------------------------------------------------------------------------

	/**
	 * wp_nav_menu_items and wp_list_pages filter
	 *
	 * @internal filter: wp_nav_menu_items
	 * @internal filter: wp_list_pages
	 * @since 1.0
	 *
	 * @param  string $items
	 * @param  array  $args
	 * @return string
	 */
	public function filterWPNavMenuItems($items, $args)
	{

		// Theme location
		if (isset($args->theme_location)) {
			$theme_location = $args->theme_location;
		} else if (isset($args['theme_location'])) {
			$theme_location = $args['theme_location'];
		} else {
			return $items;
		}

		// Icons
		$items = preg_replace_callback('#<li(?P<li_attrs>[^>]*)>\s*<a(?P<a_attrs>[^>]*)>(?P<label>[^<>]*?)</a>#i', function($matches) {
			if (!preg_match('/[ "](?P<class>icon-(?P<icon>[-_a-z0-9]+))[ "]/', $matches['li_attrs'], $class_matches)) {
				return $matches[0];
			}
			return sprintf(
				'<li%s><a%s>%s%s</a>',
				str_replace($class_matches['class'], '', $matches['li_attrs']),
				$matches['a_attrs'],
				Everything::getShortcodeOutput(
					strpos($class_matches['icon'], '_') === false ? 'vector_icon' : 'image_icon',
					array('name' => str_replace('_', '/', $class_matches['icon']))
				),
				$matches['label']
			);
		}, $items);

		// Result
		return $items;

	}

	// -------------------------------------------------------------------------

	/**
	 * get_search_form filter
	 *
	 * @internal filter: get_search_form
	 * @since 1.0
	 *
	 * @return string
	 */
	public function filterGetSearchForm()
	{
		$search_form = \Drone\HTML::form()
			->method('get')
			->action(esc_url(home_url('/')))
			->class('search')
			->role('search');
		$search_form->addNew('input')
			->type('text')
			->name('s')
			->value(get_search_query())
			->placeholder(__('Search site', 'everything'));
		$search_form->addNew('button')
			->type('submit')
			->addNew('i')
				->class('icon-search');
		return $search_form->html();
	}

	// -------------------------------------------------------------------------

	/**
	 * wp_get_attachment_image_attributes filter
	 *
	 * @internal filter: wp_get_attachment_image_attributes
	 * @since 1.0
	 *
	 * @param  array  $attr
	 * @param  object $attachment
	 * @return string
	 */
	public function filterWPGetAttachmentImageAttributes($attr, $attachment)
	{

		// Size name
		if (!preg_match('/attachment-(?P<size>[-_a-z0-9]+)/i', $attr['class'], $matches)) {
			return $attr;
		}

		// Images
		if (count($images = Everything::getAttachmentImages($attachment->ID, $matches['size'])) == 0) {
			return $attr;
		}

		return $attr+$images;

	}

	// -------------------------------------------------------------------------

	/**
	 * get_calendar filter
	 *
	 * @internal filter: get_calendar
	 * @since 1.0
	 *
	 * @param  string $calendar_output
	 * @return string
	 */
	public function filterGetCalendar($calendar_output)
	{
		return str_replace('<table ', '<table class="fixed" ', $calendar_output);
	}

	// -------------------------------------------------------------------------

	/**
	 * the_content filter
	 *
	 * @internal filter: the_content, 1
	 * @since 1.0
	 *
	 * @param  string $content
	 * @return string
	 */
	public function filterTheContent1($content)
	{

		// More anchor
		if (stripos($content, 'id="more-') !== false) {
			$content = preg_replace('#^\s*<span id="more-[0-9]+"></span>\s*#i', '', $content);
		}

		// Align-none
		if (stripos($content, 'alignnone') !== false) {
			$content = preg_replace(
				'#(<p([^>]*)>)?(( *(<a[^>]*>)?<img[^>]*class="[^"]*alignnone[^"]*"[^>]*>(</a>)? *){2,})(</p>)?#i',
				'<div class="figuregroup"\2>\3</div>',
				$content
			);
		}

		return $content;

	}

	// -------------------------------------------------------------------------

	/**
	 * the_content filter
	 *
	 * @internal filter: the_content, 15
	 *
	 * @param  string $content
	 * @return string
	 */
	public function filterTheContent($content)
	{
		if (stripos($content, 'iframe') !== false || stripos($content, 'embed') !== false) {
			$content = preg_replace('#(<p>)?(<(iframe|embed).*?></\3>)(</p>)?#i', '<div class="embed">\2</div>', $content);
		}
		return $content;
	}

	// -------------------------------------------------------------------------

	/**
	 * wp_trim_excerpt filter
	 *
	 * @internal filter: wp_trim_excerpt
	 * @since 1.0
	 *
	 * @param  string $text
	 * @return string
	 */
	public function filterWPTrimExcerpt($text)
	{
		if (is_feed() || is_attachment() || trim($text) == '') {
			return $text;
		}
		return sprintf('%s <a href="%s" class="more-link">%s</a>', $text, get_permalink(), Everything::getReadMore());
	}

	// -------------------------------------------------------------------------

	/**
	 * the_password_form filter
	 *
	 * @internal filter: the_password_form
	 * @since 1.0
	 *
	 * @param  string $html
	 * @return string
	 */
	public function filterThePasswordForm($html)
	{
		return str_replace('</label> <input ', '</label><input ', $html);
	}

	// -------------------------------------------------------------------------

	/**
	 * comment_form_defaults filter
	 *
	 * @internal filter: comment_form_defaults
	 * @since 1.0
	 *
	 * @param  array $defaults
	 * @return array
	 */
	public function filterCommentFormDefaults($defaults)
	{
		$commenter = wp_get_current_commenter();
		return array_merge($defaults, array(
			'fields'               => array(
				'author' => '<li class="col-1-3"><input class="full-width" type="text" name="author" placeholder="'.__('Name', 'everything').'*" value="'.esc_attr($commenter['comment_author']).'" required /></li>',
				'email'  => '<li class="col-1-3"><input class="full-width" type="email" name="email" placeholder="'.__('E-mail', 'everything').' ('.__('not published', 'everything').')*" value="'.esc_attr($commenter['comment_author_email']).'" required /></li>',
				'url'    => '<li class="col-1-3"><input class="full-width" type="text" name="url" placeholder="'.__('Website', 'everything').'" value="'.esc_attr($commenter['comment_author_url']).'" /></li>'
			),
			'comment_field'        => '<p><textarea class="full-width" name="comment" placeholder="'.__('Message', 'everything').'" required></textarea></p>',
			'must_log_in'          => str_replace('<p class="must-log-in">', '<p class="must-log-in small">', $defaults['must_log_in']),
			'logged_in_as'         => str_replace('<p class="logged-in-as">', '<p class="logged-in-as small">', $defaults['logged_in_as']),
			'comment_notes_before' => '',
			'comment_notes_after'  => '',
			'title_reply'          => __('Leave a comment', 'everything'),
			'title_reply_to'       => __('Leave a reply to %s', 'everything'),
			'cancel_reply_link'    => __('Cancel reply', 'everything'),
			'label_submit'         => __('Send &rsaquo;', 'everything'),
			'format'               => 'html5'
		));
	}

	// -------------------------------------------------------------------------

	/**
	 * wp_video_extensions filter
	 *
	 * @internal filter: wp_video_extensions
	 * @since 1.0
	 *
	 * @param  array $exts
	 * @return array
	 */
	public function filterWPVideoExtensions($exts)
	{
		$exts[] = 'ogg';
		return $exts;
	}

	// -------------------------------------------------------------------------

	/**
	 * wp_audio_shortcode and wp_video_shortcode filter
	 *
	 * @internal filter: wp_audio_shortcode
	 * @internal filter: wp_video_shortcode
	 * @since 1.0
	 *
	 * @param  string $html
	 * @return string
	 */
	public function filterWPAudioVideoShortcode($html)
	{
		$class = preg_match('/<(audio|video) /i', $html, $m) ? strtolower($m[1]) : '';
		return "<div class=\"embed {$class}\">".preg_replace('#^<div.*?>(.+?)</div>$#i', '\1', $html).'</div>';
	}

	// -------------------------------------------------------------------------

	/**
	 * get_previous_post_where and get_next_post_where filter
	 *
	 * @internal filter: get_previous_post_where
	 * @internal filter: get_next_post_where
	 * @since 1.0
	 *
	 * @param  string $query
	 * @return string
	 */
	public function filterGetAdjacentPostWhere($query)
	{
		if (is_singular('portfolio')) {
			$query .= $GLOBALS['wpdb']->prepare(' AND p.post_parent = %d', $GLOBALS['post']->post_parent);
		}
		return $query;
	}

	// -------------------------------------------------------------------------

	/**
	 * everything_headline filter
	 *
	 * @internal filter: everything_headline
	 * @since 1.0
	 *
	 * @param  string $headline
	 * @return string
	 */
	public function filterBBPEverythingHeadline($headline)
	{
		if (Everything::isPluginActive('bbpress') && is_bbpress()) {
			switch ($headline) {
				case 'mixed':      return Everything::isPluginActive('breadcrumb-navxt', 'breadcrumb-trail', 'wordpress-seo') ? 'breadcrumbs' : 'none';
				case 'navigation': return 'none';
			}
		}
		return $headline;
	}

	// -------------------------------------------------------------------------

	/**
	 * bbp_get_breadcrumb filter
	 *
	 * @internal filter: bbp_get_breadcrumb
	 * @since 1.0
	 *
	 * @param  string $trail
	 * @param  array  $crumbs
	 * @param  array  $r
	 * @return bool
	 */
	public function filterBBPGetBreadcrumb($trail, $crumbs, $r)
	{
		return !$r['before'] || !$r['after'] ? $trail : '';
	}

	// -------------------------------------------------------------------------

	/**
	 * breadcrumb_trail filter
	 *
	 * @internal filter: breadcrumb_trail
	 * @since 1.0
	 *
	 * @param  string $breadcrumb
	 * @param  array  $args
	 * @return string
	 */
	public function filterBreadcrumbTrail($breadcrumb, $args)
	{
		return preg_replace('#</?(div|span).*?>#i', '', $breadcrumb);
	}

	// -------------------------------------------------------------------------

	/**
	 * masterslider_disable_auto_update filter
	 *
	 * @internal filter: masterslider_disable_auto_update
	 * @since 1.0
	 *
	 * @return bool
	 */
	public function filterMastersliderDisableAutoUpdate()
	{
		return true;
	}

	// -------------------------------------------------------------------------

	/**
	 * masterslider_panel_default_setting filter
	 *
	 * @internal filter: masterslider_panel_default_setting
	 * @since 1.0
	 *
	 * @param  array $options
	 * @return array
	 */
	public function filterMastersliderPanelDefaultSetting($options)
	{
		$options['width']      = Everything::to_('general/max_width')->value('default');
		$options['height']     = round($options['width'] / 1.7778);
		$options['layout']     = Everything::to_('general/layout')->value('default') == 'open' ? 'fullwidth' : 'boxed';
		$options['autoHeight'] = Everything::to_('general/layout')->value('default') == 'boxed';
		return $options;
	}

	// -------------------------------------------------------------------------

	/**
	 * everything_headline filter
	 *
	 * @internal filter: everything_headline
	 * @since 1.0
	 *
	 * @param  string $headline
	 * @return string
	 */
	public function filterWoocommerceEverythingHeadline($headline)
	{
		if (Everything::isPluginActive('woocommerce') && is_product()) {
			switch ($headline) {
				case 'mixed':      return Everything::isPluginActive('breadcrumb-navxt', 'breadcrumb-trail', 'wordpress-seo') ? 'breadcrumbs' : 'none';
				case 'navigation': return 'none';
			}
		}
		return $headline;
	}

	// -------------------------------------------------------------------------

	/**
	 * everything_author_bio_display, everything_social_buttons_display, everything_meta_display filter
	 *
	 * @internal filter: everything_author_bio_display
	 * @internal filter: everything_social_buttons_display
	 * @internal filter: everything_meta_display
	 * @since 1.0
	 *
	 * @param  bool $show
	 * @return bool
	 */
	public function filterWoocommerceEverythingDisplay($show)
	{
		if (Everything::isPluginActive('woocommerce') && (is_cart() || is_checkout() || is_account_page() || is_order_received_page())) {
			return false;
		}
		return $show;
	}

	// -------------------------------------------------------------------------

	/**
	 * woocommerce_enqueue_styles filter
	 *
	 * @internal filter: woocommerce_enqueue_styles
	 * @since 1.0
	 *
	 * @return boolean
	 */
	public function filterWoocommerceEnqueueStyles()
	{
		return false;
	}

	// -------------------------------------------------------------------------

	/**
	 * loop_shop_per_page filter
	 *
	 * @internal filter: loop_shop_per_page
	 * @since 1.0
	 *
	 * @return int
	 */
	public function filterWoocommerceLoopShopPerPage()
	{
		return Everything::to('woocommerce/shop/per_page');
	}

	// -------------------------------------------------------------------------

	/**
	 * woocommerce_output_related_products_args filter
	 *
	 * @internal filter: woocommerce_output_related_products_args
	 * @since 1.0
	 *
	 * @param  array $args
	 * @return array
	 */
	public function filterWoocommerceOutputRelatedProductsArgs($args)
	{
		$args['posts_per_page'] = Everything::to('woocommerce/related_products/total');
		$args['columns']        = Everything::to('woocommerce/related_products/columns');
		return $args;
	}

	// -------------------------------------------------------------------------

	/**
	 * woocommerce_cross_sells_total filter
	 *
	 * @internal filter: woocommerce_cross_sells_total
	 * @since 1.0
	 *
	 * @param  int $posts_per_page
	 * @return int
	 */
	public function filterWoocommercCrossSellsTotal($posts_per_page)
	{
		return Everything::to('woocommerce/cross_sells/total');
	}

	// -------------------------------------------------------------------------

	/**
	 * woocommerce_cross_sells_columns filter
	 *
	 * @internal filter: woocommerce_cross_sells_columns
	 * @since 1.0
	 *
	 * @param  int $columns
	 * @return int
	 */
	public function filterWoocommercCrossSellsColumns($columns)
	{
		return Everything::to('woocommerce/cross_sells/columns');
	}

	// -------------------------------------------------------------------------

	/**
	 * woocommerce_show_page_title filter
	 *
	 * @internal filter: woocommerce_show_page_title
	 * @since 1.0
	 *
	 * @return bool
	 */
	public function filterWoocommerceShowPageTitle()
	{
		return !Everything::$headline_used;
	}

	// -------------------------------------------------------------------------

	/**
	 * woocommerce_placeholder_img_src filter
	 *
	 * @internal filter: woocommerce_placeholder_img_src
	 * @since 1.0
	 *
	 * @return string
	 */
	public function filterWoocommercePlaceholderImgSrc()
	{
		return $this->template_uri.'/data/img/woocommerce/placeholder.jpg';
	}

	// -------------------------------------------------------------------------

	/**
	 * woocommerce_placeholder_img filter
	 *
	 * @internal filter: woocommerce_placeholder_img
	 * @since 1.0
	 *
	 * @param  string $html
	 * @return string
	 */
	public function filterWoocommercePlaceholderImg($html)
	{
		return str_replace('<img ', '<img data-image1496="'.$this->template_uri.'/data/img/woocommerce/placeholder@2x.jpg" ', $html);
	}

	// -------------------------------------------------------------------------

	/**
	 * wc_add_to_cart_message filter
	 *
	 * @internal filter: wc_add_to_cart_message
	 * @since 1.0
	 *
	 * @param  string $html
	 * @return string
	 */
	public function filterWoocommerceAddToCartMessage($html)
	{
		return str_replace('class="button ', 'class="button small ', $html);
	}

	// -------------------------------------------------------------------------

	/**
	 * woocommerce_loop_add_to_cart_link filter
	 *
	 * @internal filter: woocommerce_loop_add_to_cart_link
	 * @since 1.0
	 *
	 * @param  string $html
	 * @return string
	 */
	public function filterWoocommerceLoopAddToCartLink($html)
	{
		return str_replace('class="button ', 'class="button small ', $html);
	}

	// -------------------------------------------------------------------------

	/**
	 * woocommerce_order_button_html filter
	 *
	 * @internal filter: woocommerce_order_button_html
	 * @since 1.0
	 *
	 * @param  string $html
	 * @return string
	 */
	public function filterWoocommerceOrderButtonHTML($html)
	{
		if (preg_match('/value="(.*?)"/', $html, $matches)) {
			return '<button type="submit" class="big" name="woocommerce_checkout_place_order" id="place_order" style="border-color: #129a00; color: #129a00;"><span>'.$matches[1].'</span><i class="icon-right-bold"></i></button>';
		} else {
			return $html;
		}
	}

	// -------------------------------------------------------------------------

	/**
	 * get_product_search_form filter
	 *
	 * @internal filter: get_product_search_form
	 * @since 1.0
	 *
	 * @param  string $html
	 * @return string
	 */
	public function filterWoocommerceGetProductSearchForm($html)
	{
		return preg_replace('/\s*\n\s*/', '', $html);
	}

	// -------------------------------------------------------------------------

	/**
	 * add_to_cart_fragments filter
	 *
	 * @internal filter: add_to_cart_fragments
	 * @since 1.2
	 *
	 * @param  array $fragments
	 * @return array
	 */
	public function filterWoocommerceAddToCartFragments($fragments)
	{
		$fragments['.cart-info.small'] = Everything::woocommerceGetCartInfo('small');
		$fragments['.cart-info.big']   = Everything::woocommerceGetCartInfo('big', Everything::to('header/cart/visible', '__hidden', array()));
		return $fragments;
	}

	// -------------------------------------------------------------------------

	/**
	 * products_shortcode_tag filter
	 *
	 * @internal filter: products_shortcode_tag
	 * @since 1.0
	 *
	 * @param  string $tag
	 * @return string
	 */
	public function filterWoocommerceProductsShortcodeTag($tag)
	{

		add_shortcode($tag, function($atts) {

			// class-wc-shortcodes.php snippet
			extract( shortcode_atts( array(
				'columns' 	=> '1',
				'orderby'   => 'title',
				'order'     => 'asc'
			), $atts ) );

			$args = array(
				'post_type'				=> 'product',
				'post_status' 			=> 'publish',
				'ignore_sticky_posts'	=> 1,
				'orderby' 				=> $orderby,
				'order' 				=> $order,
				'posts_per_page' 		=> -1,
				'meta_query' 			=> array(
					array(
						'key' 		=> '_visibility',
						'value' 	=> array('catalog', 'visible'),
						'compare' 	=> 'IN'
					)
				)
			);

			if ( isset( $atts['skus'] ) ) {
				$skus = explode( ',', $atts['skus'] );
				$skus = array_map( 'trim', $skus );
				$args['meta_query'][] = array(
					'key' 		=> '_sku',
					'value' 	=> $skus,
					'compare' 	=> 'IN'
				);
			}

			if ( isset( $atts['ids'] ) ) {
				$ids = explode( ',', $atts['ids'] );
				$ids = array_map( 'trim', $ids );
				$args['post__in'] = $ids;
			}
			// class-wc-shortcodes.php

			$products = new WP_Query(apply_filters('woocommerce_shortcode_products_query', $args, $atts));

			if (!$products->have_posts()) {
				return;
			}

			$html = \Drone\HTML::div()
				->class('woocommerce columns')
				->add(
					$ul = \Drone\HTML::ul()
				);

			$posts_list = array();
			for ($i = 0; $i < $columns; $i++) {
				$posts_list[$i] = $ul->addNew('li')
					->class('col-1-'.$columns)
					->addNew('ul')
						->class('posts-list');
			}

			$i = 0;
			while ($products->have_posts()) {
				$products->the_post();
				$posts_list[$i % $columns]->add(str_replace('<figure class="alignright fixed">', '<figure class="alignleft fixed">',
					\Drone\Func::functionGetOutputBuffer('wc_get_template', 'content-widget-product.php', array('show_rating' => true))
				));
				$i++;
			}

			wp_reset_postdata();

			return $html->html();

		});

		return '__'.$tag;

	}

	// -------------------------------------------------------------------------

	/**
	 * drone_widget_posts_list_on_setup_options action
	 *
	 * @internal action: drone_widget_posts_list_on_setup_options
	 * @since 1.0
	 *
	 * @param object $options
	 * @param object $widget
	 */
	public function actionWidgetPostsListOnSetupOptions($options, $widget)
	{
		$options->addOption('select', 'orientation', 'scrollable', __('Orientation', 'everything'), '', array('options' => array(
			'vertical'   => __('Vertical', 'everything'),
			'scrollable' => __('Scrollable', 'everything')
		)), 'count');
		$options->addOption('boolean', 'thumbnail', true, '', '', array('caption' => __('Show thumbnail', 'everything')), 'author');
		$options->addOption('boolean', 'date', true, '', '', array('caption' => __('Show date', 'everything')), 'author');
		$options->deleteChild('author');
	}

	// -------------------------------------------------------------------------

	/**
	 * drone_widget_posts_list_widget filter
	 *
	 * @internal filter: drone_widget_posts_list_widget
	 * @since 1.0
	 *
	 * @param object $html
	 * @param object $widget
	 */
	public function filterWidgetPostsListWidget($html, $widget)
	{
		$html->addClass('posts-list');
		if ($widget->wo('orientation') == 'scrollable') {
			$html->addClass('scroller');
		}
		return $html;
	}

	// -------------------------------------------------------------------------

	/**
	 * drone_widget_posts_list_post filter
	 *
	 * @internal filter: drone_widget_posts_list_post
	 * @since 1.0
	 *
	 * @param object $li
	 * @param object $widget
	 * @param object $post
	 */
	public function filterWidgetPostsListPost($li, $widget, $post)
	{
		$li = \Drone\HTML::li();
		if ($widget->wo('thumbnail') && has_post_thumbnail($post->ID)) {
			$li->addNew('figure')
				->class('alignleft fixed')
				->addNew('a')
					->attr(Everything::getImageAttrs('a', array('border' => true, 'hover' => '', 'fanbcybox' => false)))
					->href(get_permalink($post->ID))
					->add(get_the_post_thumbnail($post->ID, 'post-thumbnail-mini-crop'));
		}
		if ($widget->wo('date')) {
			$GLOBALS['post'] = $post;
			$li->addNew('p')->class('small')->add(Everything::getPostMeta('date'));
			wp_reset_postdata();
		}
		$li->addNew('h3')->addNew('a')
			->href(get_permalink($post->ID))
			->title(esc_attr($post->post_title))
			->add(wp_trim_words($post->post_title, $widget->wo('limit')));
		if ($widget->wo('comments')) {
			$GLOBALS['post'] = $post;
			$li->addNew('p')->class('small')->add(Everything::getPostMeta('comments_number'));
			wp_reset_postdata();
		}
		return $li;
	}

	// -------------------------------------------------------------------------

	/**
	 * drone_widget_twitter_on_setup_options action
	 *
	 * @internal action: drone_widget_twitter_on_setup_options
	 * @since 1.0
	 *
	 * @param object $options
	 * @param object $widget
	 */
	public function actionWidgetTwitterOnSetupOptions($options, $widget)
	{
		$options->addOption('select', 'orientation', 'vertical', __('Orientation', 'everything'), '', array('options' => array(
			'vertical'   => __('Vertical', 'everything'),
			'horizontal' => __('Horizontal', 'everything'),
			'scrollable' => __('Scrollable', 'everything')
		)), 'count');
		$options->addOption('boolean', 'follow_me_button', true, '', '', array('caption' => __('Add "follow me" button', 'everything')), 'oauth');
	}

	// -------------------------------------------------------------------------

	/**
	 * drone_widget_twitter_widget filter
	 *
	 * @internal filter: drone_widget_twitter_widget
	 * @since 1.0
	 *
	 * @param object $html
	 * @param object $widget
	 */
	public function filterWidgetTwitterWidget($html, $widget)
	{
		$html = \Drone\HTML::div()->class('twitter')->add($html);
		if ($widget->wo('orientation') == 'horizontal') {
			$ul = $html->child(0);
			$class = 'col-1-'.$ul->count();
			foreach ($ul->childs() as $li) {
				$li->addClass($class);
			}
			$ul->wrap('div');
			$html->child(0)->class = 'columns';
		} else if ($widget->wo('orientation') == 'scrollable') {
			$html->child(0)->addClass('scroller');
		}
		if ($widget->wo('follow_me_button')) {
			$html->addNew('p')->addNew('a')
				->class('button small')
				->href('https://twitter.com/'.$widget->wo('username'))
				->add(__('follow me', 'everything').' &rsaquo;');
		}
		return $html;
	}

	// -------------------------------------------------------------------------

	/**
	 * drone_widget_twitter_tweet filter
	 *
	 * @internal filter: drone_widget_twitter_tweet
	 * @since 1.0
	 *
	 * @param object $li
	 * @param object $widget
	 * @param object $tweet
	 */
	public function filterWidgetTwitterTweet($li, $widget, $tweet)
	{
		$li->insert('<i class="icon-twitter"></i>')->child(3)->addClass('alt');
		return $li;
	}

	// -------------------------------------------------------------------------

	/**
	 * drone_widget_flickr_widget filter
	 *
	 * @internal filter: drone_widget_flickr_widget
	 * @since 1.0
	 *
	 * @param object $html
	 * @param object $widget
	 */
	public function filterWidgetFlickrWidget($html, $widget)
	{
		return \Drone\HTML::div()->class('flickr')->add($html);
	}

	// -------------------------------------------------------------------------

	/**
	 * drone_widget_flickr_photo filter
	 *
	 * @internal filter: drone_widget_flickr_photo
	 * @since 1.0
	 *
	 * @param object $li
	 * @param object $widget
	 * @param object $photo
	 */
	public function filterWidgetFlickrPhoto($li, $widget, $photo)
	{
		$li->child(0)->attr(Everything::getImageAttrs('a', array('border' => false)))->title(null);
		return $li;
	}

	// -------------------------------------------------------------------------

	/**
	 * drone_widget_facebook_like_box_widget filter
	 *
	 * @internal filter: drone_widget_facebook_like_box_widget
	 * @since 1.0
	 *
	 * @param object $html
	 * @param object $widget
	 * @param array  $args
	 */
	public function filterWidgetFacebookLikeBoxWidget($html, $widget, $args)
	{
		$html->child(0)
			->data('width', strpos($args['id'], 'footer-') !== 0 ? $GLOBALS['content_width'] : 258)
			->data('colorscheme', Everything::to('general/scheme') == 'bright' ? 'light' : 'dark');
		return $html;
	}

	// -------------------------------------------------------------------------

	/**
	 * Begin content layer
	 *
	 * @since 1.0
	 *
	 * @param  string $content
	 * @return string
	 */
	public static function beginContent()
	{

		if (is_page_template('tpl-hidden-content.php')) {
			return '';
		}

		// Content
		$content = \Drone\HTML::make();
		$main = $content->addNew('div')->class('main');

		// Layout
		$layout = Everything::po('layout/layout/layout', '__hidden_ns', Everything::to_('sidebar/layout')->value());
		$layout = apply_filters('everything_layout', $layout);

		// Sidebars
		$pad  = array('left' => 0, 'right' => 0);
		$side = 'left';

		foreach ($layout as $sidebar) {

			if ($sidebar == '#') {

				$side = 'right';

			} else if ($sidebar) {

				$sidebar = apply_filters('everything_sidebar', $sidebar, 'aside');

				$width = Everything::to(array('sidebar/list/builtin/'.$sidebar), null, Everything::to_('sidebar/list/additional')->value($sidebar));
				if (is_array($width)) {
					$width = $width['width'];
				}

				$pad[$side] += $side == 'right' ? $width : Everything::DEFAULT_SIDEBAR_WIDTH;
				$GLOBALS['content_width'] = $width - 50;

				$aside = \Drone\HTML::aside()
					->addClass('aside', $side == 'left' ? 'alpha' : 'beta')
					->add(\Drone\Func::functionGetOutputBuffer('dynamic_sidebar', $sidebar));
				if ($side == 'right') {
					$aside->style = "width: {$width}px;";
				}

				if ($side == 'left' && $layout[0] && $layout[1] == '#' && $layout[2]) { // left-content-right
					$content->insert($aside);
				} else if ($side == 'right' && $layout[0] == '#') { // content-right-right
					$content->insert($aside, 1);
				} else {
					$content->add($aside);
				}

			}

		}

		$main->addClass($pad['right'] ? 'alpha' : ($pad['left'] ? 'beta' : ''));
		$main->style = sprintf('padding: 0 %2$dpx 0 %1$dpx; margin: 0 -%2$dpx 0 -%1$dpx;', $pad['left'], $pad['right']);

		// Content width
		$GLOBALS['content_width'] = apply_filters('everything_content_width', Everything::$max_width - array_sum($pad) - 50);
		if (false) {
			global $content_width; // Theme-Check
		}

		// Content
		ob_start(function($buffer) use ($content, $main) {
			$main->add($buffer);
			return $content->html();
		});

	}

	// -------------------------------------------------------------------------

	/**
	 * End content layer
	 *
	 * @since 1.0
	 */
	public static function endContent()
	{
		if (!is_page_template('tpl-hidden-content.php')) {
			ob_end_flush();
		}
	}

	// -------------------------------------------------------------------------

	/**
	 * Columns from string definition
	 *
	 * @since 1.0
	 *
	 * @param  string $s
	 * @return array
	 */
	public static function stringToColumns($s)
	{
		$s = str_replace(' ', '', $s);
		if (!$s) {
			return array();
		}
		return array_map(function($s) {
			list($span, $total) = strpos($s, '/') === false ? str_split($s) : explode('/', $s);
			return array(
				'span'  => $span,
				'total' => $total,
				'width' => $span/$total,
				'class' => sprintf('col-%d-%d', $span, $total)
			);
		}, preg_split('/[_\+]/', $s));
	}

	// -------------------------------------------------------------------------

	/**
	 * Get attachment images
	 *
	 * @since 1.0
	 *
	 * @param  int    $id
	 * @param  string $size
	 * @param  string $format
	 * @return array
	 */
	public static function getAttachmentImages($id, $size, $format = 'array')
	{

		// Meta data
		if (($metadata = wp_get_attachment_metadata($id)) === false || !isset($metadata['sizes'][$size])) {
			return $format == 'html' ? '' : array();
		}

		$file = explode('/', $metadata['file']);
		$metadata['sizes']['full'] = array(
			'file'   => array_pop($file),
			'width'  => $metadata['width'],
			'height' => $metadata['height']
		);

		// Current
		$current = $metadata['sizes'][$size];
		$current['ratio'] = $current['width'] / $current['height'];
		$current['crop'] = isset($GLOBALS['_wp_additional_image_sizes'][$size]) ? $GLOBALS['_wp_additional_image_sizes'][$size]['crop'] : null;

		// Images
		$images = array();
		foreach ($metadata['sizes'] as $_size => $size) {
			if (
				$size['width'] > $current['width'] &&
				(
					($current['crop'] === false && isset($GLOBALS['_wp_additional_image_sizes'][$_size]) && !$GLOBALS['_wp_additional_image_sizes'][$_size]['crop']) ||
					abs(($size['width'] / $size['height']) - $current['ratio']) <= 0.015
				)
			) {
				list($images[$size['width']]) = wp_get_attachment_image_src($id, $_size);
			}
		}

		// Sort
		ksort($images);

		// Keys prefix
		$images = \Drone\Func::arrayKeysMap(function($s) { return 'data-image'.$s; }, $images);

		// Output
		switch ($format) {
			case 'html': return \Drone\Func::arraySerialize($images, 'html');
			default:     return $images;
		}

	}

	// -------------------------------------------------------------------------

	/**
	 * Get image attributes
	 *
	 * @since 1.0
	 *
	 * @param  string $tag
	 * @param  array  $atts
	 * @param  string $format
	 * @return array
	 */
	public static function getImageAttrs($tag, $atts = array(), $format = 'array')
	{

		// Image settings
		$settings = Everything::to_('site/image/settings');

		// Attributes
		extract(array_merge($defaults = array(
			'border'   => $settings->value('border'),
			'hover'    => $settings->value('hover') ? 'zoom' : '',
			'fancybox' => $settings->value('fancybox')
		), $atts));

		// Border
		$border = $border === 'inherit' ? $defaults['border'] : \Drone\Func::stringToBool($border);

		// Hover
		if ($hover === 'inherit' || !in_array($hover, array('', 'zoom', 'image', 'grayscale'), true)) {
			$hover = $defaults['hover'];
		}

		// Fancybox
		$fancybox = $fancybox === 'inherit' ? $defaults['fancybox'] : \Drone\Func::stringToBool($fancybox);

		// Properties
		$attrs = array('class' => array());

		if ($border) {
			$attrs['class'][] = 'inset-border';
		}
		if ($tag == 'a') {
			if ($hover) {
				$attrs['class'][] = $hover.'-hover';
			}
			if ($fancybox) {
				$attrs['class'][] = 'fb';
			}
		}

		$attrs['class'] = implode(' ', $attrs['class']);

		// Output
		switch ($format) {
			case 'html': return \Drone\Func::arraySerialize($attrs, 'html');
			default:     return $attrs;
		}

	}

	// -------------------------------------------------------------------------

	/**
	 * Image attributes
	 *
	 * @since 1.0
	 *
	 * @param string $tag
	 * @param array  $atts
	 */
	public static function imageAttrs($tag, $atts = array())
	{
		echo Everything::getImageAttrs($tag, $atts, 'html');
	}

	// -------------------------------------------------------------------------

	/**
	 * Current blog style
	 *
	 * @since 1.0
	 *
	 * @return string
	 */
	public static function getBlogStyle()
	{
		return \Drone\Shortcodes\Shortcode::inShortcode('blog') ? \Drone\Shortcodes\Shortcode::getInstance('blog')->so('style') : Everything::to('site/blog/style');
	}

	// -------------------------------------------------------------------------

	/**
	 * Post format icon
	 *
	 * @since 1.0
	 *
	 * @return string
	 */
	public static function getPostIcon()
	{
		if (Everything::to('post/hide_icons')) {
			return;
		}
		if (($post_format = get_post_format()) === false) {
			return;
		}
		$icons = apply_filters('everything_post_formats_icons', array(
			'aside'   => 'doc-text',
			'audio'   => 'mic',
			'chat'    => 'chat',
			'gallery' => 'picture',
			'image'   => 'camera',
			'link'    => 'link',
			'quote'   => 'quote',
			'status'  => 'comment',
			'video'   => 'youtube-alt'
		));
		if (!isset($icons[$post_format])) {
			return;
		}
		return \Drone\HTML::i()->class('icon-'.$icons[$post_format])->html();
	}

	// -------------------------------------------------------------------------

	/**
	 * Read more phrase and icon
	 *
	 * @since 1.0
	 *
	 * @return string
	 */
	public static function getReadMore()
	{
		$readmore = Everything::to_(array(get_post_type().'/readmore', 'post/readmore'));
		$html = \Drone\HTML::make()->add($readmore->value('phrase'));
		if ($readmore->value('icon')) {
			$html->addNew('i')->class('icon-'.$readmore->value('icon'));
		}
		return $html->html();
	}

	// -------------------------------------------------------------------------

	/**
	 * Paginate links
	 *
	 * @since 1.0
	 *
	 * @param  string   $name
	 * @param  WP_Query $query
	 * @return string
	 */
	public static function getPaginateLinks($name, $query = null)
	{

		if (!apply_filters('everything_pagination_display', true, $name)) {
			return '';
		}

		// Paginate links
		switch ($name) {

			// Page
			case 'page':
				if (!is_singular()) {
					return '';
				}
				$pagination = wp_link_pages(array(
					'before'           => ' ',
					'after'            => ' ',
					'next_or_number'   => rtrim(Everything::to('site/page_pagination'), 's'),
					'previouspagelink' => '<i class="icon-left-open"></i><span>'.__('Previous page', 'everything').'</span>',
					'nextpagelink'     => '<span>'.__('Next page', 'everything').'</span><i class="icon-right-open"></i>',
					'echo'             => false
				));
				$pagination = str_replace('<a ', '<a class="button small" ', $pagination);
				$pagination = preg_replace('/ ([0-9]+) /',' <span class="button small active">\1</span> ', $pagination);
				break;

			// Comment
			case 'comments':
				if (!is_singular()) {
					return '';
				}
				$pagination = paginate_comments_links(array(
					'prev_next' => Everything::to('site/comments/pagination') == 'numbers_navigation',
					'prev_text' => '<i class="icon-left-open"></i>',
					'next_text' => '<i class="icon-right-open"></i>',
					'echo'      => false
				));
				$pagination = str_replace(array('page-numbers', 'current'), array('button small', 'active'), $pagination);
				break;

			// Default
			default:
				$args = array(
					'prev_next' => Everything::to('site/pagination') == 'numbers_navigation',
					'prev_text' => '<i class="icon-left-open"></i>',
					'next_text' => '<i class="icon-right-open"></i>',
					'end_size'  => 1,
					'mid_size'  => 2
				);
				if ($name == 'woocommerce') {
					$args['base'] = esc_url(str_replace('99999999', '%#%', remove_query_arg('add-to-cart', htmlspecialchars_decode(get_pagenum_link(99999999)))));
				}
				$pagination = \Drone\Func::wpPaginateLinks($args, $query);
				$pagination = preg_replace_callback(
						'/class=[\'"](?P<dir>prev |next )?page-numbers(?P<current> current)?[\'"]()/i',
						function($m) { return "class=\"{$m['dir']}button small".str_replace('current', 'active', $m['current']).'"'; },
						$pagination
				);

		}

		if (!$pagination) {
			return '';
		}

		return \Drone\HTML::div()->class('pagination')->add($pagination)->html();

	}

	// -------------------------------------------------------------------------

	/**
	 * Navigation menu
	 *
	 * @since 1.0
	 *
	 * @param string $theme_location
	 * @param int    $menu
	 * @param int    $depth
	 */
	public static function navMenu($theme_location, $menu = null, $depth = 0)
	{
		echo wp_nav_menu(array(
			'theme_location' => $theme_location,
			'menu'           => apply_filters('everything_menu', $menu, $theme_location),
			'depth'          => $depth,
			'container'      => '',
			'menu_id'        => '',
			'menu_class'     => '',
			'echo'           => false,
			'fallback_cb'    => function() use ($theme_location, $depth) {
				return '<ul>'.wp_list_pages(array(
					'theme_location' => $theme_location,
					'title_li'       => '',
					'depth'          => $depth,
					'echo'           => false
				)).'</ul>';
			}
		));
	}


	// -------------------------------------------------------------------------

	/**
	 * Languages menus
	 *
	 * @since 1.0
	 */
	public static function langMenu()
	{

		if (count($langs = icl_get_languages('skip_missing=0&orderby=code')) == 0) {
			return;
		}

		$html = \Drone\HTML::ul();
		$main = $html->addNew('li');
		$sub  = $main->addNew('ul');

		foreach ($langs as $lang) {

			$li = $sub->addNew('li');

			$a = $li->addNew('a')
				->href($lang['url'])
				->title($lang['native_name'])
				->add(
					$lang['native_name'],
					\Drone\HTML::span()->class('flag-'.$lang['language_code'])
				);

			if ($lang['active']) {
				$li->class = 'current';
				$main->insertNew('a')
					->href('#')
					->title($lang['native_name'])
					->add(
						\Drone\HTML::span()->class('flag-'.$lang['language_code']),
						\Drone\HTML::i()->class('icon-down-open')
					);
			}

		}

		echo $html->html();

	}

	// -------------------------------------------------------------------------

	/**
	 * Title
	 *
	 * @since 1.6
	 */
	public static function title()
	{

		if (Everything::isPluginActive('bbpress') && is_bbpress()) {
			if (!Everything::$headline_used) {
				echo '<h1 class="title">'.get_the_title().'</h1>';
			}
		}

		else if (!is_singular()) {
			echo '<h1 class="title"><a href="'.esc_url(apply_filters('the_permalink', get_permalink())).'" rel="bookmark">'.Everything::getPostIcon().get_the_title().'</a></h1>';
		}

		else if (!Everything::$headline_used && !Everything::io('layout/page/hide_title/hide_title', array(get_post_type().'/hide_title', 'page/hide_title'), '__hidden')) {
			echo '<h1 class="title">'.Everything::getPostIcon().get_the_title().'</h1>';
		}

	}

	// -------------------------------------------------------------------------

	/**
	 * Social buttons
	 *
	 * @since 1.6
	 */
	public static function socialButtons()
	{

		if (is_search()) {
			return;
		}

		$items = Everything::to_(array(
			sprintf('%s/social_buttons/%s/items', get_post_type(), is_singular() ? 'single' : 'list'),
			sprintf('%s/social_buttons/items', get_post_type()),
			'page/social_buttons/items'
		));

		if (!$items->value || !apply_filters('everything_social_buttons_display', (bool)Everything::po('layout/page/social_buttons/social_buttons', '__hidden', $items->isVisible()))) {
			return;
		}

		$html = is_singular() ? \Drone\HTML::section()->class('section') : \Drone\HTML::make();

		foreach (array_keys($items->options) as $item) {
			$media['media_'.$item] = in_array($item, $items->value);
		}
		$html->add(Everything::getShortcodeOutput('social_buttons', array('size' => 'small')+$media));

		echo $html->html();

	}

	// -------------------------------------------------------------------------

	/**
	 * Meta
	 *
	 * @since 1.6
	 *
	 * @param string $position
	 */
	public static function meta($position = 'after')
	{

		if (is_search()) {
			return;
		}
		if ($position == 'before' && (is_single() || get_post_type() != 'post')) {
			return;
		}

		if ($position == 'before') {
			$items = Everything::to_('post/meta/before/items');
		} else {
			$items = Everything::to_(array(
				sprintf('%s/meta/%s/items', get_post_type(), is_singular() ? 'single' : 'list'),
				sprintf('%s/meta/items', get_post_type()),
				'page/meta/items'
			));
		}

		if (!$items->value || !apply_filters('everything_meta_display', (bool)Everything::po('layout/page/meta/meta', '__hidden', $items->isVisible()), $position)) {
			return;
		}

		$html = is_singular() ? \Drone\HTML::section()->class('section') : \Drone\HTML::make();

		$ul = $html->addNew('ul')->class('meta alt');
		foreach ((array)$items->value as $item) {
			switch ($item) {
				case 'date_time':
					$ul->add(Everything::getPostMetaFormat(
						'<li><a href="%date_month_link%" title="%s"><i class="icon-clock"></i>%s</a></li>',
						sprintf(__('View all posts from %s', 'everything'), get_the_date('F')),
						sprintf(__('%1$s at %2$s', 'everything'), Everything::getPostMeta('date'), Everything::getPostMeta('everything'))
					));
					break;
				case 'date':
					$ul->add(Everything::getPostMetaFormat('<li><a href="%date_month_link%" title="%s"><i class="icon-clock"></i>%date%</a></li>', sprintf(__('View all posts from %s', 'everything'), get_the_date('F'))));
					break;
				case 'mod_date':
					$ul->add(Everything::getPostMetaFormat('<li><a href="%link%" title="%title_esc%"><i class="icon-clock"></i>%date_modified%</a></li>'));
					break;
				case 'time_diff':
					$ul->add(Everything::getPostMetaFormat('<li><a href="%link%" title="%title_esc%"><i class="icon-clock"></i>%time_diff%</a></li>'));
					break;
				case 'comments':
					if (Everything::isPluginActive('disqus')) {
						$ul->add(Everything::getPostMetaFormat('<li><i class="icon-comment"></i><a href="%comments_link%">%comments_number%</a></li>'));
					} else {
						$ul->add(Everything::getPostMetaFormat('<li><a href="%comments_link%" title="%comments_number_esc%"><i class="icon-comment"></i>%comments_number%</a></li>'));
					}
					break;
				case 'author':
					$ul->add(Everything::getPostMetaFormat('<li><a href="%author_link%" title="%author_name_esc%"><i class="icon-user"></i>%author_name%</a></li>'));
					break;
				case 'categories':
					if (get_post_type() == 'portfolio') {
						$ul->add(get_the_term_list(get_the_ID(), 'portfolio-category', '<li><i class="icon-list"></i>', ', ', '</li>'));
					} else {
						$ul->add(Everything::getPostMetaFormat('[%category_list%]<li><i class="icon-list"></i>%category_list%</li>[/%category_list%]'));
					}
					break;
				case 'tags':
					if (get_post_type() == 'portfolio') {
						$ul->add(get_the_term_list(get_the_ID(), 'portfolio-tag', '<li><i class="icon-tag"></i>', ', ', '</li>'));
					} else {
						$ul->add(Everything::getPostMetaFormat('[%tags_list%]<li><i class="icon-tag"></i>%tags_list%</li>[/%tags_list%]'));
					}
					break;
				case 'permalink':
					$ul->add(Everything::getPostMetaFormat('<li><a href="%link%" title="%title_esc%"><i class="icon-link"></i>%s</a></li>', __('Permalink', 'everything')));
					break;
			}
		}

		echo $html->html();

	}

	// -------------------------------------------------------------------------

	/**
	 * Cart info
	 *
	 * @since 1.0
	 *
	 * @param  string $type
	 * @param  array  $visible
	 * @return string
	 */
	public static function woocommerceGetCartInfo($type, $visible = array('desktop', 'mobile'))
	{

		if (count($visible) == 0) {
			return '';
		}

		// HTML
		$a = \Drone\HTML::a()
			->href($GLOBALS['woocommerce']->cart->get_cart_url())
			->title(__('Cart', 'everything'))
			->addClass('cart-info', $type);

		// Visibility
		if (count($visible) == 1) {
			$a->addClass($visible[0].'-only');
		}

		// Icon
		$a->addNew('i')
			->addClass('icon-woocommerce-cart', 'icon-'.Everything::to('woocommerce/cart/icon'));

		// Content
		if (Everything::to_('header/cart/content')->value('count')) {
			$a->addNew('span')
				->class('count')
				->add($GLOBALS['woocommerce']->cart->get_cart_contents_count());
		}
		if (Everything::to_('header/cart/content')->value('total') && $GLOBALS['woocommerce']->cart->get_cart_contents_count()) {
			$cart_total = strip_tags($GLOBALS['woocommerce']->cart->get_cart_total());
			$cart_total = preg_replace('/('.preg_quote(get_option('woocommerce_price_decimal_sep'), '/').')([0-9]+)/', '\1<small>\2</small>', $cart_total);
			$a->addNew('span')
				->class('total')
				->add($cart_total);
		}

		return $a->html();

	}

	// -------------------------------------------------------------------------

	/**
	 * Get thumbnail caption for WooCommerce product image
	 *
	 * @since 1.0
	 *
	 * @param  int|object $thumbnail
	 * @return string
	 */
	public static function woocommerceGetThumbnailCaption($thumbnail)
	{
		if (!Everything::to('woocommerce/product/captions')) {
			return '';
		}
		if (!is_object($thumbnail)) {
			$thumbnail = get_post($thumbnail);
		}
		switch (Everything::to('woocommerce/product/captions')) {
			case 'title':
				return trim($thumbnail->post_title);
			case 'caption':
				return trim($thumbnail->post_excerpt);
			case 'caption_title':
				$caption = trim($thumbnail->post_excerpt) or $caption = trim($thumbnail->post_title);
				return $caption;
		}
	}

	// -------------------------------------------------------------------------

	/**
	 * Parse/fix WooCommerce widget list
	 *
	 * @since 1.0
	 *
	 * @param  string $s
	 * @return string
	 */
	public static function woocommerceWidgetParseList($s)
	{
		return preg_replace('#<ul class="([^"]*product_list_widget[^"]*)">#i', '<ul class="\1 posts-list">', $s);
	}

	// -------------------------------------------------------------------------

	/**
	 * Parse/fix WooCommerce widget navigation
	 *
	 * @since 1.0
	 *
	 * @param  string $s
	 * @return string
	 */
	public static function woocommerceWidgetparseNav($s)
	{
		$s = preg_replace('#<ul[^<>]*>.*</ul>#is', '<nav class="aside-nav-menu">\0</nav>', $s);
		$s = preg_replace('#(<a href="[^"]*">)([^<>]*)(</a>)\s*<(span|small) class="count">\(?([0-9]+)\)?</\4>#i', '\1\2 <small>(\5)</small>\3', $s);
		return $s;
	}

}

// -----------------------------------------------------------------------------

Everything::getInstance();