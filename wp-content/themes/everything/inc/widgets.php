<?php
/**
 * @package    WordPress
 * @subpackage Everything
 * @since      1.0
 */

// -----------------------------------------------------------------------------

namespace Everything\Widgets\Widget;
use \Drone\Widgets\Widget;
use \Drone\Func;
use \Drone\HTML;

// -----------------------------------------------------------------------------

if (!defined('ABSPATH')) {
	exit;
}

// -----------------------------------------------------------------------------

class SocialMedia extends Widget
{

	// -------------------------------------------------------------------------

	protected function onSetupOptions(\Drone\Options\Group\Widget $options)
	{
		$options->addOption('text', 'title', '', __('Title', 'everything'));
		$options->addOption('memo', 'description', '', __('Description', 'everything'));
		$options->addOption('collection', 'icons', array('icon' => '', 'title' => '', 'url' => 'http://'), __('Icons', 'everything'), '', array('type' => 'social_media'));
		$options->addOption('select', 'gravity', 's', __('Tooltip position', 'everything'), '', array('options' => array(
			'se' => __('Northwest', 'everything'),
			's'  => __('North', 'everything'),
			'sw' => __('Northeast', 'everything'),
			'e'  => __('West', 'everything'),
			'w'  => __('East', 'everything'),
			'ne' => __('Southwest', 'everything'),
			'n'  => __('South', 'everything'),
			'nw' => __('Southeast', 'everything')
		)));
		$options->addOption('boolean', 'native_colors', true, '', '', array('caption' => __('Native hover colors', 'everything')));
		$options->addOption('boolean', 'new_window', false, '', '', array('caption' => __('Open links in new window', 'everything')));
	}

	// -------------------------------------------------------------------------

	protected function onWidget(array $args, \Drone\HTML &$html)
	{
		if ($this->wo('description')) {
			$html->addNew('p')->add($this->wo_('description')->__());
		} else if (!$this->wo('icons')) {
			return;
		}
		$div = $html->addNew('div')->class('social-icons');
		$ul  = $div->addNew('ul')->class('alt');
		if ($this->wo('native_colors')) {
			$div->addClass('native-colors');
		}
		foreach ($this->wo('icons') as $icon) {
			if (!$icon['icon'] || !$icon['url']) {
				continue;
			}
			$li = $ul->addNew('li');
			$a = $li->addNew('a')->href($icon['url']);
			$a->addNew('i')->class('icon-'.$icon['icon']);
			if ($icon['title']) {
				$a->title = $icon['title'];
				$a->class = 'tipsy-tooltip';
				$a->data('tipsy-tooltip-gravity', $this->wo('gravity'));
			}
			if ($this->wo('new_window')) {
				$a->target = '_blank';
			}
		}
	}

	// -------------------------------------------------------------------------

	public function __construct()
	{
		parent::__construct(__('Social media', 'everything'), __('Social media icons.', 'everything'));
	}

}

// -----------------------------------------------------------------------------

class SocialButtons extends Widget
{

	// -------------------------------------------------------------------------

	protected function onSetupOptions(\Drone\Options\Group\Widget $options)
	{
		$options->addOption('text', 'title', '', __('Title', 'everything'));
		$options->addOption('memo', 'description', '', __('Description', 'everything'));
		$options->addOption('collection', 'media', '', __('Media', 'everything'), '', array('type' => 'select', 'trim_default' => false, 'options' => array(
			'facebook'   => __('Facebook', 'everything'),
			'twitter'    => __('Twitter', 'everything'),
			'googleplus' => __('Google+', 'everything'),
			'linkedin'   => __('LinkedIn', 'everything'),
			'pinterest'  => __('Pinterest', 'everything')
		)));
	}

	// -------------------------------------------------------------------------

	protected function onWidget(array $args, \Drone\HTML &$html)
	{
		if (!is_singular()) {
			return;
		}
		if ($this->wo('description')) {
			$html->addNew('p')->add($this->wo_('description')->__());
		}
		foreach (array_keys($this->wo_('media')->properties['options']) as $media) {
			$medias['media_'.$media] = \Drone\Func::boolToString(in_array($media, $this->wo('media')));
		}
		$sb = \Everything::getShortcodeOutput('social_buttons', array(
			'style' => 'big'
		)+$medias);
		$html->add($sb);
	}

	// -------------------------------------------------------------------------

	public function __construct()
	{
		parent::__construct(__('Social buttons', 'everything'), __('Social media buttons.', 'everything'));
	}

}

// -----------------------------------------------------------------------------

class Portfolio extends Widget
{

	// -------------------------------------------------------------------------

	protected function onSetupOptions(\Drone\Options\Group\Widget $options)
	{
		$options->addOption('text', 'title', '', __('Title', 'everything'));
		$options->addOption('select', 'id', 0, __('Children of', 'everything'), __('Displays child portfolios of selected portfolio.', 'everything'), array('required' => false, 'options' => function() {
			return array_map(function($s) { return Func::stringCut($s, 55); }, Func::wpPostsList(array('numberposts' => -1, 'post_type' => 'portfolio')));
		}));
		$options->addOption('select', 'columns', '2', __('Layout', 'everything'), '', array('options' => array(
			'1'  => __('One column', 'everything'),
			'1+' => __('One+ column', 'everything'),
			'2'  => __('Two columns', 'everything'),
			'3'  => __('Three columns', 'everything'),
			'4'  => __('Four columns', 'everything')
		)));
		$options->addOption('select', 'filter', '', __('Filter', 'everything'), '', array('options' => array(
			''         => '('.__('None', 'everything').')',
			'category' => __('Category', 'everything'),
			'tag'      => __('Tag', 'everything')
		)));
		$options->addOption('select', 'orderby', 'date', __('Sort by', 'everything'), '', array('options' => array(
			'title'         => __('Title', 'everything'),
			'date'          => __('Date', 'everything'),
			'modified'      => __('Modified date', 'everything'),
			'comment_count' => __('Comment count', 'everything'),
			'rand'          => __('Random order', 'everything'),
			'menu_order'    => __('Custom order', 'everything')
		)));
		$options->addOption('select', 'order', 'desc', __('Sort order', 'everything'), '', array('options' => array(
			'asc'  => __('Ascending', 'everything'),
			'desc' => __('Descending', 'everything')
		)));
		$options->addOption('number', 'count', '', __('Count', 'everything'), '', array('required' => false, 'min' => 1));
		$options->addOption('boolean', 'show_title', true, '', '', array('caption' => __('Show title', 'everything')));
		$content = $options->addGroup('content', __('Content', 'everything'));
			$visible = $content->addOption('boolean', 'visible', false, '', '', array('caption' => __('Show content', 'everything')));
			$content->addOption('group', 'content', 'excerpt_content', '', __('Regular content means everything before the "Read more" tag.', 'everything'), array('options' => array(
				'content'         => __('Regular content', 'everything'),
				'excerpt_content' => __('Excerpt or regular content', 'everything'),
				'excerpt'         => __('Excerpt', 'everything')
			), 'indent' => true, 'owner' => $visible));
		$taxonomy = $options->addGroup('taxonomy', __('Taxonomy', 'everything'));
			$visible = $taxonomy->addOption('boolean', 'visible', true, '', '', array('caption' => __('Show taxonomies', 'everything')));
			$taxonomy->addOption('select', 'taxonomy', 'tag', '', '', array('options' => array(
				'category' => __('Categories', 'everything'),
				'tag'      => __('Tags', 'everything')
			), 'indent' => true, 'owner' => $visible));
		$options->addOption('select', 'image_hover', 'inherit', __('Hover effect', 'everything'), '', array('options' => array(
			'inherit'   => __('Inherit', 'everything'),
			''          => __('None', 'everything'),
			'zoom'      => __('Default', 'everything'),
			'grayscale' => __('Grayscale', 'everything')
		)));
	}

	// -------------------------------------------------------------------------

	protected function onWidget(array $args, \Drone\HTML &$html)
	{

		if (!$this->wo('id')) {
			return;
		}

		// Columns
		$columns_int  = (int)rtrim($this->wo('columns'), '+');
		$columns_plus = $this->wo('columns') != (string)$columns_int;

		// Posts
		$query = new \WP_Query(array(
			'posts_per_page' => $this->wo('count', '__empty', -1),
			'post_status'    => 'publish',
			'post_type'      => 'portfolio',
			'post_parent'    => $this->wo('id'),
			'post__not_in'   => is_single() ? array(get_the_ID()) : array(),
			'orderby'        => $this->wo('orderby'),
			'order'          => $this->wo('order')
		));
		if (!$query->have_posts()) {
			return;
		}

		// Bricks
		$bricks = $html->addNew('div')
			->class('bricks')
			->data('bricks-columns', $columns_int)
			->data('bricks-filter', Func::boolToString($this->wo('filter')));

		while ($query->have_posts()) {

			$query->the_post();

			$div = $bricks->addNew('div');

			// Item
			$item = $div->addNew('article')
				->id('portfolio-item-'.get_the_ID())
				->addClass(get_post_class('portfolio-item'));
			if ($this->wo('show_title') || $this->wo('content') || $this->wo('taxonomy')) {
				$item->addClass('bordered');
			}

			// Relation
			if ($this->wo('filter')) {
				$terms = Func::wpPostTermsList(get_the_ID(), 'portfolio-'.$this->wo('filter'));
				if (count($terms) > 0) {
					$div->data('bricks-terms', json_encode(array_values($terms)));
				}
			}

			// Columns +
			if ($columns_plus) {
				$ul = $item->addNew('div')->class('columns')->addNew('ul');
				$item_featured = $ul->addNew('li')->class('col-2-3');
				$item_desc     = $ul->addNew('li')->class('col-1-3');
			} else {
				$item_featured = $item_desc = $item;
			}

			// Featured image
			if (has_post_thumbnail()) {
				$item_featured->addNew('figure')
					->class('thumbnail featured full-width')
				 	->addNew('a')
						->attr(\Everything::getImageAttrs('a', array('border' => false, 'hover' => $this->wo('image_hover'), 'fancybox' => false)))
						->href(get_permalink())
						->add(get_the_post_thumbnail(null, $columns_plus ? 'auto-2' : 'auto'));
			}

			// Title
			if ($this->wo('show_title')) {
				$item_desc->addNew($columns_int == 1 ? 'h2' : 'h3')->addNew('a')
					->href(get_permalink())
					->title(the_title_attribute(array('echo' => false)))
					->add(get_the_title());
			}

			// Content
			switch ($this->wo('content/content', '__hidden')) {
				case 'excerpt':
					$item_desc->addNew('p')->add(get_the_excerpt());
					break;
				case 'excerpt_content':
					if (has_excerpt()) {
						$item_desc->addNew('p')->add(get_the_excerpt());
						break;
					}
				case 'content':
					$GLOBALS['more'] = 0;
					$item_desc->add(\Drone\Func::wpProcessContent(get_the_content(\Everything::getReadMore())));
					break;
			}

			// Taxonomy
			if ($this->wo('taxonomy/visible')) {
				$item_desc->add(get_the_term_list(get_the_ID(), 'portfolio-'.$this->wo('taxonomy/taxonomy'), '<p class="small alt">', ', ', '</p>'));
			}

		}

		wp_reset_postdata();

	}

	// -------------------------------------------------------------------------

	public function __construct()
	{
		parent::__construct(__('Portfolio', 'everything'), __('Displays Portfolio or child portfolios of a specified parent porfolio.', 'everything'));
	}

}

// -----------------------------------------------------------------------------

class Contact extends Widget
{

	// -------------------------------------------------------------------------

	protected function onSetupOptions(\Drone\Options\Group\Widget $options)
	{
		$options->addOption('text', 'title', '', __('Title', 'everything'));
		$options->addOption('memo', 'description', '', __('Description', 'everything'));
	}

	// -------------------------------------------------------------------------

	protected function onWidget(array $args, \Drone\HTML &$html)
	{
		if ($this->wo('description')) {
			$html->addNew('p')->add($this->wo_('description')->__());
		}
		$cf = \Everything::getShortcodeOutput('contact');
		$cf = str_replace('<textarea ', '<textarea class="full-width" ', $cf);
		$html->add($cf);
	}

	// -------------------------------------------------------------------------

	public function __construct()
	{
		parent::__construct(__('Contact form', 'everything'), __('Displays contact form, which can be configured in Theme Options.', 'everything'));
	}

}