<?php
/**
 * @package    WordPress
 * @subpackage Everything
 * @since      1.0
 */

// -----------------------------------------------------------------------------

namespace Everything\Shortcodes\Shortcode;
use \Drone\Shortcodes\Shortcode;
use \Drone\Func;
use \Drone\HTML;

// -----------------------------------------------------------------------------

if (!defined('ABSPATH')) {
	exit;
}

// -----------------------------------------------------------------------------

/**
 * Caption
 *
 * @since 1.6
 */
class Caption extends Shortcode\Caption
{

	// -------------------------------------------------------------------------

	protected function onShortcode($content, $code, \Drone\HTML &$html)
	{

		// ID
		$int_id = (int)$this->getID();

		// Retina
		if ($int_id && $this->so('width') && ($size = Func::wpGetImageSizeName($content)) !== false) {
			$content = str_replace('<img ', '<img '.trim(\Everything::getAttachmentImages($int_id, $size, 'html').' '), $content);
		}

		// Class
		$class = preg_match('/class="([^"]*)"/i', $content, $m) ? preg_replace('/\balign(none|left|right|center)\b/i', '', $m[1]) : '';

		// Content
		$content = preg_replace('/class="([^"]*)"/i', '', trim($content));

		// Images attributes
		$atts = array();
		if (preg_match_all('/\b(border|hover|fancybox)-([a-z]+)\b/i', $class, $matches, PREG_SET_ORDER) > 0) {
			foreach ($matches as $match) {
				$atts[$match[1]] = str_ireplace('none', '', $match[2]);
				$class = str_replace($match[0], '', $class);
			}
		}

		// Settings
		if (strpos($content, '<a ') === 0) {
			$content = str_replace('<a ', sprintf('<a %s data-fancybox-group="post-%d" data-fancybox-title="%s" ', \Everything::getImageAttrs('a', $atts, 'html'), get_the_ID(), esc_attr($this->so('caption'))), $content);
		} else {
			$content = sprintf('<div %s>%s</div>', \Everything::getImageAttrs('div', $atts, 'html'), $content);
		}

		// Figure
		$html = HTML::figure()
			->id($this->so('id') ? $this->so('id') : null)
			->addClass($class, $this->so('align'), $this->so('align') == 'alignleft' || $this->so('align') == 'alignright' ? 'fixed' : null)
			->style($this->so('width') ? "width: {$this->so('width')}px;" : null)
			->add($content);

		// Caption
		if ($this->so('caption')) {
			$html->addNew('figcaption')->add($this->so('caption'));
		}

	}

}

// -----------------------------------------------------------------------------

/**
 * Gallery
 *
 * @since 1.0
 */
class Gallery extends Shortcode\Gallery
{

	// -------------------------------------------------------------------------

	protected $instance_id = 0;

	// -------------------------------------------------------------------------

	protected function onSetupOptions(\Drone\Options\Group\Shortcode $options)
	{
		parent::onSetupOptions($options);
		$style = $options->addOption('select', 'style', 'classic', __('Style', 'everything'), '', array('options' => array(
			'classic'  => __('Classic', 'everything'),
			'bricks'   => __('Bricks', 'everything'),
			'slider'   => __('Slider', 'everything'),
			'scroller' => __('Scroller', 'everything'),
			'tabs'     => __('Tabs', 'everything')
		)));
		$options->addOption('boolean', 'captions', true, __('Captions', 'everything'), '', array('owner' => $style, 'owner_value' => array('classic', 'bricks', 'slider')));
		$options->addOption('boolean', 'full_width', false, __('Fit images', 'everything'), '', array('owner' => $style, 'owner_value' => 'classic'));
		$options->addOption('boolean', 'matrix', false, __('Matrix style', 'everything'), '', array('owner' => $style, 'owner_value' => 'bricks'));
		$slider = $options->addGroup('slider');
			$transition = $slider->addOption('select', 'transition', 'fade', __('Transition', 'everything'), '', array('options' => array(
				''           => __('None', 'everything'),
				'fade'       => __('Fade', 'everything'),
				'horizontal' => __('Horizontal', 'everything'),
				'vertical'   => __('Vertical', 'everything')
			), 'owner' => $style, 'owner_value' => 'slider'));
			$slider->addOption('number', 'speed', 600, __('Speed', 'everything'), '', array('unit' => 'ms', 'min' => 0, 'owner' => $transition, 'owner_value' => array('fade', 'horizontal', 'vertical')));
			$auto = $slider->addOption('boolean', 'auto', false, __('Auto play', 'everything'), '', array('owner' => $style, 'owner_value' => 'slider'));
			$slider->addOption('number', 'pause', 3000, __('Exposure time', 'everything'), '', array('unit' => 'ms', 'min' => 1000, 'owner' => $auto));
			$slider->addOption('boolean', 'controls', true, __('Arrows controls', 'everything'), '', array('owner' => $style, 'owner_value' => 'slider'));
			$slider->addOption('boolean', 'pager', true, __('Dots controls', 'everything'), '', array('owner' => $style, 'owner_value' => 'slider'));
		$options->addOption('boolean', 'buttons', false, __('Buttons', 'everything'), '', array('owner' => $style, 'owner_value' => 'scroller'));
		$options->addOption('boolean', 'ordered', true, __('Ordered', 'everything'), '', array('owner' => $style, 'owner_value' => 'tabs'));
		$options->addOption('boolean', 'descriptions', true, __('Descriptions', 'everything'), '', array('owner' => $style, 'owner_value' => 'tabs'));
		$image = $options->addGroup('image');
			$image->addOption('select', 'border', 'inherit', __('Border', 'everything'), '', array('options' => array(
				'inherit' => __('Inherit', 'everything'),
				'yes'     => __('Yes', 'everything'),
				'no'      => __('No', 'everything')
			), 'owner' => $style, 'owner_value' => array('classic', 'bricks', 'slider', 'scroller')));
			$image->addOption('select', 'hover', 'inherit', __('Hover effect', 'everything'), '', array('options' => array(
				'inherit'   => __('Inherit', 'everything'),
				''          => __('None', 'everything'),
				'zoom'      => __('Default', 'everything'),
				'grayscale' => __('Grayscale', 'everything')
			), 'owner' => $style, 'owner_value' => array('classic', 'bricks', 'slider', 'scroller')));
			$image->addOption('select', 'fancybox', 'inherit', __('Fancybox', 'everything'), '', array('options' => array(
				'inherit' => __('Inherit', 'everything'),
				'yes'     => __('Yes', 'everything'),
				'no'      => __('No', 'everything')
			), 'owner' => $style, 'owner_value' => array('classic', 'bricks', 'slider', 'scroller')));
		$size = $options->child('size');
			$size->owner = $style;
			$size->owner_value = array('classic', 'scroller');
	}

	// -------------------------------------------------------------------------

	protected function onShortcode($content, $code, \Drone\HTML &$html)
	{

		// Style
		switch ($this->so('style')) {
			case 'classic':
				$html = $this->getClassic();
				break;
			case 'bricks':
				$html = $this->getBricks();
				break;
			case 'slider':
				$html = $this->getSlider();
				break;
			case 'scroller':
				$html = $this->getScroller();
				break;
			case 'tabs':
				$html = $this->getTabs();
				break;
		}

	}

	// -------------------------------------------------------------------------

	protected function getAttachmentLinkURI(\WP_Post $attachment)
	{
		if ($attachment->post_content && preg_match('#((https?://|mailto:).+)(\b|["\'])#i', $attachment->post_content, $matches)) {
			return $matches[1];
		}
		return parent::getAttachmentLinkURI($attachment);
	}

	// -------------------------------------------------------------------------

	protected function getClassic()
	{

		// HTML
		$html = HTML::div()
			->id('gallery-'.(++$this->instance_id))
			->class('columns');

		// Items
		$ul = $html->addNew('ul');

		foreach ($this->getAttachments() as $attachment) {

			// Figure
			$figure = $ul->addNew('li')
				->class('col-1-'.$this->so('columns'))
				->addNew('figure');
			if ($this->so('full_width')) {
				$figure->class = 'full-width';
			} else {
				list(, $width) = wp_get_attachment_image_src($attachment->ID, $this->so('size'));
				$figure->class = 'aligncenter';
				$figure->style = sprintf('width: %dpx;', $width);
			}

			// Hyperlink and image
			if ($url = $this->getAttachmentLinkURI($attachment)) {
				$a = $figure->addNew('a')
					->attr(\Everything::getImageAttrs('a', $this->so_('image')->toArray()))
					->data('fancybox-group', $html->id)
					->data('fancybox-title', $attachment->post_excerpt)
					->href($url)
					->add(wp_get_attachment_image($attachment->ID, $this->so('size')));
			} else {
				$figure->addNew('div')
					->attr(\Everything::getImageAttrs('div', $this->so_('image')->toArray()))
					->add(wp_get_attachment_image($attachment->ID, $this->so('size')));
			}

			// Caption
			if ($this->so('captions') && trim($attachment->post_excerpt)) {
				$caption = $figure->addNew('figcaption')
					->add(wptexturize($attachment->post_excerpt));
			}

		}

		return $html;

	}

	// -------------------------------------------------------------------------

	protected function getBricks()
	{

		// HTML
		$html = HTML::div()
			->id('gallery-'.(++$this->instance_id))
			->class('bricks')
			->data('bricks-columns', $this->so('columns'));

		if ($this->so('matrix')) {
			$html->addClass('matrix');
		}

		// Items
		foreach ($this->getAttachments() as $attachment) {

			// Figure
			$figure = $html->addNew('div')->addNew('figure')
				->class('full-width');

			// Hyperlink and image
			if ($url = $this->getAttachmentLinkURI($attachment)) {
				$a = $figure->addNew('a')
					->attr(\Everything::getImageAttrs('a', $this->so_('image')->toArray()))
					->data('fancybox-group', $html->id)
					->data('fancybox-title', $attachment->post_excerpt)
					->href($url)
					->add(wp_get_attachment_image($attachment->ID, 'auto'));
			} else {
				$figure->addNew('div')
					->attr(\Everything::getImageAttrs('div', $this->so_('image')->toArray()))
					->add(wp_get_attachment_image($attachment->ID, 'auto'));
			}

			// Caption
			if ($this->so('captions') && trim($attachment->post_excerpt) && !$this->so('matrix')) {
				$caption = $figure->addNew('figcaption')
					->add(wptexturize($attachment->post_excerpt));
			}

		}

		return $html;

	}

	// -------------------------------------------------------------------------

	protected function getSlider()
	{

		// Data
		$data = array_map(function($e) { return is_bool($e) || $e === '' ? Func::boolToString($e) : $e; }, $this->so_('slider')->toArray());
		$data = Func::arrayKeysMap(function($k) { return 'slider-'.$k; }, $data);

		// HTML
		$html = HTML::ul()
			->id('gallery-'.(++$this->instance_id))
			->class('slider')
			->data($data);

		foreach ($this->getAttachments() as $attachment) {

			$li = $html->addNew('li');

			// Caption
			$caption = HTML::make();
			if ($this->so('captions') && ($excerpt = trim($attachment->post_excerpt))) {
				$caption->addNew('h3')->add(wptexturize($excerpt));
			}

			// Hyperlink and image
			if ($attachment->post_content && preg_match('#<iframe.*?>\s*</iframe>#i', $attachment->post_content, $matches)) {
				$iframe = preg_replace_callback(
					'#src="(?P<url>(https?:)?//www.youtube.com/embed/[-_a-z0-9]+)\??(?P<get>.*?)"#i',
					function($m) {
						return sprintf('src="%s?wmode=opaque&amp;enablejsapi=1%s"', $m['url'], isset($m['get']) && $m['get'] ? '&amp;'.$m['get'] : '');
					},
					$attachment->post_content
				);
				$li->addNew('div')
					->class('embed')
					->add($iframe);
			} else if ($url = $this->getAttachmentLinkURI($attachment)) {
				$li->addNew('a')
					->attr(\Everything::getImageAttrs('a', $this->so_('image')->toArray()))
					->data('fancybox-group', $html->id)
					->data('fancybox-title', $attachment->post_excerpt)
					->href($url)
					->add(wp_get_attachment_image($attachment->ID, 'auto', false, array('title' => $caption->html())));
			} else {
				$li->addNew('div')
					->attr(\Everything::getImageAttrs('div', $this->so_('image')->toArray()))
					->add(wp_get_attachment_image($attachment->ID, 'auto', false, array('title' => $caption->html())));
			}

		}

		return $html;

	}

	// -------------------------------------------------------------------------

	protected function getScroller()
	{

		// HTML
		$html = HTML::div()
			->id('gallery-'.(++$this->instance_id))
			->class('movable-container');

		if ($this->so('size') == 'logo') {
			$html->addClass('content-size-logo');
		}

		if ($this->so('buttons')) {
			$html->data('movable-container-force-touch-device', 'true');
		}

		// Items
		foreach ($this->getAttachments() as $attachment) {

			// Figure
			$html->add(' ');
			$figure = $html->addNew();

			// Hyperlink and image
			if ($url = $this->getAttachmentLinkURI($attachment)) {
				$a = $figure->addNew('a')
					->attr(\Everything::getImageAttrs('a', $this->so_('image')->toArray()))
					->data('fancybox-group', $html->id)
					->data('fancybox-title', $attachment->post_excerpt)
					->href($url)
					->add(wp_get_attachment_image($attachment->ID, $this->so('size')));
			} else {
				$figure->addNew('div')
					->attr(\Everything::getImageAttrs('div', $this->so_('image')->toArray()))
					->add(wp_get_attachment_image($attachment->ID, $this->so('size')));
			}

		}

		return $html;

	}

	// -------------------------------------------------------------------------

	protected function getTabs()
	{

		// HTML
		$html = HTML::div()
			->id('gallery-'.(++$this->instance_id))
			->class('super-tabs')
			->data('super-tabs-ordered', $this->so_('ordered')->toString());

		// Tabs
		foreach ($this->getAttachments() as $attachment) {

			// Figure
			$figure = $html->addNew('div')
				->title($attachment->post_title);

			// Hyperlink and image
			if ($url = $this->getAttachmentLinkURI($attachment)) {
				$a = $figure->addNew('a')
					->attr(\Everything::getImageAttrs('a', array('border' => false, 'hover' => '')))
					->data('fancybox-group', $html->id)
					->data('fancybox-title', $attachment->post_excerpt)
					->href($url)
					->add(wp_get_attachment_image($attachment->ID, 'auto-2'));
			} else {
				$figure->add(wp_get_attachment_image($attachment->ID, 'auto-2'));
			}

			// Description
			if ($this->so('descriptions')) {
				$figure->data('super-tabs-description', $attachment->post_excerpt);
			}

		}

		return $html;

	}

}

// -----------------------------------------------------------------------------

/**
 * Hr
 *
 * @since 1.0
 */
class Hr extends Shortcode
{

	// -------------------------------------------------------------------------

	protected function onSetupOptions(\Drone\Options\Group\Shortcode $options)
	{
		$options->addOption('select', 'type', 'line', __('Type', 'everything'), '', array('options' => array(
			'line'    => __('Line', 'everything'),
			'divider' => __('Divider', 'everything')
		)));
		$options->addOption('text', 'text', '', __('Text', 'everything'));
		$options->addOption('boolean', 'spacer', false, __('Extra space', 'everything'), '', array('caption' => __('Yes', 'everything')));
	}

	// -------------------------------------------------------------------------

	protected function onShortcode($content, $code, \Drone\HTML &$html)
	{
		if ($this->so('text')) {
			$html = HTML::div()->class('hr');
			$html->addNew('div')->addNew('hr');
			$html->addNew('h4')->add($this->so('text'));
			$html->addNew('div')->addNew('hr');
		} else {
			$html = HTML::hr();
		}
		if ($this->so('type') != 'line') {
			$html->addClass($this->so('type'));
		}
		if ($this->so('spacer')) {
			$html->addClass('spacer');
		}
	}

	// -------------------------------------------------------------------------

	public function __construct()
	{
		parent::__construct(__('Horizontal line', 'everything'), array(
			'self_closing' => true
		));
	}

}

// -----------------------------------------------------------------------------

/**
 * Mark
 *
 * @since 1.0
 */
class Mark extends Shortcode
{

	// -------------------------------------------------------------------------

	protected function onSetupOptions(\Drone\Options\Group\Shortcode $options)
	{
		$options->addOption('select', 'color', '', __('Color', 'everything'), '', array('options' => array(
			''       => __('Default', 'everything'),
			'yellow' => __('Yellow', 'everything')
		)));
	}

	// -------------------------------------------------------------------------

	protected function onShortcode($content, $code, \Drone\HTML &$html)
	{
		$html = HTML::mark()
			->class($this->so('color'))
			->add(Func::wpShortcodeContent($content));
	}

	// -------------------------------------------------------------------------

	public function __construct()
	{
		parent::__construct(__('Text mark', 'everything'), array(
			'visibility' => self::VISIBILITY_TINYMCE
		));
	}

}

// -----------------------------------------------------------------------------

/**
 * Dropcap
 *
 * @since 1.0
 */
class Dc extends Shortcode
{

	// -------------------------------------------------------------------------

	protected function onShortcode($content, $code, \Drone\HTML &$html)
	{
		$content = Func::wpShortcodeContent($content);
		$html = HTML::make()->add(
			HTML::span()->class('dropcap')->add($content[0]),
			substr($content, 1)
		);
	}

	// -------------------------------------------------------------------------

	public function __construct()
	{
		parent::__construct(__('Dropcap', 'everything'), array(
			'visibility' => self::VISIBILITY_TINYMCE
		));
	}

}

// -----------------------------------------------------------------------------

/**
 * Tooltip
 *
 * @since 1.0
 */
class Tooltip extends Shortcode
{

	// -------------------------------------------------------------------------

	protected function onSetupOptions(\Drone\Options\Group\Shortcode $options)
	{
		$options->addOption('text', 'title', '', __('Title', 'everything'));
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
		$options->addOption('boolean', 'fade', false, __('Fade', 'everything'), '', array('caption' => __('Yes', 'everything')));
		$advanced = $options->addGroup('advanced', __('Advanced', 'everything'));
			$advanced->addOption('text', 'tag', 'span', __('HTML tag', 'everything'));
	}

	// -------------------------------------------------------------------------

	protected function onShortcode($content, $code, \Drone\HTML &$html)
	{
		$html = HTML::make($this->so('advanced/tag'))
			->class('tipsy-tooltip')
			->title($this->so('title'))
			->data('tipsy-tooltip-gravity', $this->so('gravity'))
			->data('tipsy-tooltip-fade', $this->so_('fade')->toString())
			->add(Func::wpShortcodeContent($content)); // preg_replace('/ +/', '&nbsp;', $content)
	}

	// -------------------------------------------------------------------------

	public function __construct()
	{
		parent::__construct(__('Tooltip', 'everything'), array(
			'visibility' => self::VISIBILITY_TINYMCE
		));
	}

}

// -----------------------------------------------------------------------------

/**
 * Font
 *
 * @since 1.0
 */
class Font extends Shortcode
{

	// -------------------------------------------------------------------------

	protected function onSetupOptions(\Drone\Options\Group\Shortcode $options)
	{
		$options->addOption('select', 'index', '', __('Name', 'everything'), '', array('options' => function() {
			$options = array();
			foreach (\Everything::to('font/custom') as $index => $custom_font) {
				$options[$index] = $custom_font['id'];
			}
			if (count($options) == 0) {
				$options[''] = '('.__('No fonts defined in Theme Options / Fonts / Custom', 'everything').')';
			}
			return $options;
		}, 'required' => false));
		$advanced = $options->addGroup('advanced', __('Advanced', 'everything'));
			$advanced->addOption('text', 'tag', 'span', __('HTML tag', 'everything'));
			$advanced->addOption('text', 'class', '', __('CSS class', 'everything'));
			$advanced->addOption('text', 'style', '', __('CSS style', 'everything'));
	}

	// -------------------------------------------------------------------------

	protected function onShortcode($content, $code, \Drone\HTML &$html)
	{
		$html = HTML::make($this->so('advanced/tag'))
			->class($this->so('advanced/class'))
			->style($this->so('advanced/style'))
			->add(Func::wpShortcodeContent($content));
		if (!is_null($custom_font = \Everything::to_('font/custom')->option($this->so('index')))) {
			$html->style .= $custom_font->css();
		}
	}

	// -------------------------------------------------------------------------

	public function __construct()
	{
		parent::__construct(__('Font', 'everything'));
	}

}

// -----------------------------------------------------------------------------

/**
 * Vector icon
 *
 * @since 1.0
 */
class VectorIcon extends Shortcode
{

	// -------------------------------------------------------------------------

	protected function onSetupOptions(\Drone\Options\Group\Shortcode $options)
	{
		$options->addOption('image_select', 'name', 'arrow-line-right', __('Icon', 'everything'), '', array('font_path' => \Everything::ICON_FONT_PATH, 'options' => function() {
			return \Drone\Options\Option\ImageSelect::cssToOptions('data/img/icons/icons.css');
		}));
		$options->addOption('color', 'color', '', __('Color', 'everything'), '', array('required' => false, 'on_sanitize' => function($option, $original_value, &$value) {
			if (strtolower($original_value) == 'leading') {
				$value = 'leading';
			}
		}));
		$options->addOption('number', 'size', '', __('Size', 'everything'), __('If empty, default size will be used.', 'everything'), array('required' => false, 'unit' => 'px'));
		$advanced = $options->addGroup('advanced', __('Advanced', 'everything'));
			$advanced->addOption('text', 'class', '', __('CSS class', 'everything'));
			$advanced->addOption('text', 'style', '', __('CSS style', 'everything'));
	}

	// -------------------------------------------------------------------------

	protected function onShortcode($content, $code, \Drone\HTML &$html)
	{
		$html = HTML::i()->class('icon-'.$this->so('name'));
		if ($this->so('color')) {
			if ($this->so('color') == 'leading') {
				$html->addClass('color');
			} else {
				$html->style .= 'color: '.$this->so('color').';';
			}
		}
		if ($this->so('size')) {
			$html->style .= 'font-size: '.$this->so('size').'px;';
		}
		if ($this->so('advanced/class')) {
			$html->addClass($this->so('advanced/class'));
		}
		if ($this->so('advanced/style')) {
			$html->style .= $this->so('advanced/style');
		}
	}

	// -------------------------------------------------------------------------

	public function __construct()
	{
		parent::__construct(__('Vector icon', 'everything'), array(
			'self_closing' => true,
			'visibility'   => self::VISIBILITY_TINYMCE
		));
	}

}

// -----------------------------------------------------------------------------

/**
 * Image icon
 *
 * @since 1.0
 */
class ImageIcon extends Shortcode
{

	// -------------------------------------------------------------------------

	protected function onSetupOptions(\Drone\Options\Group\Shortcode $options)
	{
		$options->addOption('image_select', 'name', 'koloria/button-next', __('Icon', 'everything'), '', array('options' => function() {
			return
				\Drone\Options\Option\ImageSelect::dirToOptions('data/img/icons/essen', '/(?<!@2x)\.png$/i', 'essen/')+
				\Drone\Options\Option\ImageSelect::dirToOptions('data/img/icons/koloria', '/(?<!@2x)\.png$/i', 'koloria/')+
				array_filter(\Drone\Options\Option\ImageSelect::mediaToOptions(array(16, 24, 32, 48, 64, 128)), function($s) {
					return preg_match('/(?<!@2x)\.(png|gif|jpe?g)$/i', $s);
				});
		}));
		$advanced = $options->addGroup('advanced', __('Advanced', 'everything'));
			$advanced->addOption('text', 'class', '', __('CSS class', 'everything'));
			$advanced->addOption('text', 'style', '', __('CSS style', 'everything'));
	}

	// -------------------------------------------------------------------------

	protected function onShortcode($content, $code, \Drone\HTML &$html)
	{

		$everything = \Everything::getInstance();

		// HTML
		$html = HTML::img()
			->class('icon')
			->alt(basename($this->so('name')));

		// URI, width, height
		if (is_numeric($this->so('name'))) {
			if (($src = wp_get_attachment_image_src($this->so('name'), 'full')) === false) {
				return;
			}
			list($html->src, $html->width, $html->height) = $src;
			if (\Drone\Func::wpGetAttachmentID($src2x = preg_replace('/\.(png|gif|jpe?g)$/i', '@2x.$1', $html->src)) !== false) {
				$html->data('image'.($html->width*2), $src2x);
			}
		} else {
			if (!file_exists($everything->template_dir.'/data/img/icons/'.$this->so('name').'.png')) {
				return;
			}
			if (($is = getimagesize($everything->template_dir.'/data/img/icons/'.$this->so('name').'.png')) !== false) {
				list($html->width, $html->height) = $is;
			} else {
				$html->width = $html->height = 16;
			}
			$html->src = $everything->template_uri.'/data/img/icons/'.$this->so('name').'.png';
			if (file_exists($everything->template_dir.'/data/img/icons/'.$this->so('name').'@2x.png')) {
				$html->data('image'.($html->width*2), $everything->template_uri.'/data/img/icons/'.$this->so('name').'@2x.png');
			}
		}

		// Attributes
		if ($this->so('advanced/class')) {
			$html->addClass($this->so('advanced/class'));
		}
		if ($this->so('advanced/style')) {
			$html->style .= $this->so('advanced/style');
		}

	}

	// -------------------------------------------------------------------------

	public function __construct()
	{
		parent::__construct(__('Image icon', 'everything'), array(
			'self_closing' => true,
			'visibility'   => self::VISIBILITY_TINYMCE
		));
	}

}

// -----------------------------------------------------------------------------

/**
 * Button
 *
 * @since 1.0
 */
class Button extends Shortcode
{

	// -------------------------------------------------------------------------

	protected function onSetupOptions(\Drone\Options\Group\Shortcode $options)
	{
		$options->addOption('select', 'size', 'normal', __('Size', 'everything'), '', array('options' => array(
			'small'  => __('Small', 'everything'),
			'normal' => __('Normal', 'everything'),
			'big'    => __('Big', 'everything'),
			'huge'   => __('Huge', 'everything')
		)));
		$options->addOption('text', 'href', '', __('Hyperlink', 'everything'));
		$options->addOption('boolean', 'new_window', false, __('Open link in new window', 'everything'), '', array('caption' => __('Yes', 'everything')));
		$options->addOption('text', 'caption', '', __('Caption', 'everything'));
		$options->addOption('color', 'color', '', __('Forecolor', 'everything'), __('If empty, default color will be used.', 'everything'), array('required' => false));
		$background = $options->addGroup('background', __('Background', 'everything'));
			$background->addOption('color', 'color', '', __('Color', 'everything'), __('If empty, default color will be used.', 'everything'), array('required' => false));
			$background->addOption('number', 'opacity', 100, __('Opacity', 'everything'), '', array('min' => 0, 'max' => 100, 'unit' => '%'));
		$advanced = $options->addGroup('advanced', __('Advanced', 'everything'));
			$advanced->addOption('text', 'class', '', __('CSS class', 'everything'));
			$advanced->addOption('text', 'style', '', __('CSS style', 'everything'));
	}

	// -------------------------------------------------------------------------

	protected function onShortcode($content, $code, \Drone\HTML &$html)
	{
		$content = preg_replace('/ *(\[(vector|image)_icon[^\]]*\]) */', '</span>\1<span>', trim($content));
		$content = preg_replace('#^<span></span>|<span></span>$#', '', "<span>{$content}</span>");
		$html = HTML::a()
			->addClass('button', $this->so('size'), $this->so('advanced/class'))
			->style($this->so('advanced/style'));
		$html->add(Func::wpShortcodeContent($content));
		if ($this->so('href')) {
			$html->href = $this->so('href');
			if ($this->so('new_window')) {
				$html->target = '_blank';
			}
		}
		if ($this->so('color')) {
			$html->style .= sprintf('border-color: %1$s; color: %1$s;', $this->so('color'));
		}
		if ($this->so('background/opacity') == 0) {
			$html->style .= 'background-color: transparent;';
		} else if ($this->so('background/color')) {
			$html->style .= 'background-color: '.Func::cssHexToRGBA($this->so('background/color'), $this->so('background/opacity')/100).';';
		}
		if ($this->so('caption')) {
			$html = HTML::p()
				->class('horizontal-align text-right')
				->add(
					$html, '<br />',
					HTML::small()->class('caption')->add($this->so('caption'))
				);
		}
	}

	// -------------------------------------------------------------------------

	public function __construct()
	{
		parent::__construct(__('Button', 'everything'));
	}

}

// -----------------------------------------------------------------------------

/**
 * Quote
 *
 * @since 1.0
 */
class Quote extends Shortcode
{

	// -------------------------------------------------------------------------

	protected function onSetupOptions(\Drone\Options\Group\Shortcode $options)
	{
		$options->addOption('text', 'author', '', __('Author', 'everything'));
		$options->addOption('boolean', 'bar', true, __('Bar', 'everything'), '', array('caption' => __('Yes', 'everything')));
		$options->addOption('select', 'align', 'none', __('Align', 'everything'), '', array('options' => array(
			'none'  => __('None', 'everything'),
			'left'  => __('Left', 'everything'),
			'right' => __('Right', 'everything')
		)));
		$options->addOption('number', 'width', '', __('Width', 'everything'), '', array('required' => false, 'unit' => 'px'));
	}

	// -------------------------------------------------------------------------

	protected function onShortcode($content, $code, \Drone\HTML &$html)
	{
		$content = Func::wpShortcodeContent($content);
		if ($this->so('author')) {
			$content .= ' <cite>'.$this->so('author').'</cite>';
		}
		$html = HTML::blockquote()
			->class('align'.$this->so('align'))
			->add($content);
		if ($this->so('bar')) {
			$html->addClass('bar');
		}
		if ($this->so('width')) {
			$html->style = 'width: '.$this->so('width').'px;';
		}
	}

	// -------------------------------------------------------------------------

	public function __construct()
	{
		parent::__construct(__('Quote', 'everything'));
	}

}

// -----------------------------------------------------------------------------

/**
 * Message
 *
 * @since 1.0
 */
class Message extends Shortcode
{

	// -------------------------------------------------------------------------

	protected function onSetupOptions(\Drone\Options\Group\Shortcode $options)
	{
		$options->addOption('select', 'color', '', __('Color', 'everything'), '', array('options' => array(
			''       => __('Default', 'everything'),
			'blue'   => __('Blue', 'everything'),
			'green'  => __('Green', 'everything'),
			'orange' => __('Orange', 'everything'),
			'red'    => __('Red', 'everything')
		)));
		$options->addOption('boolean', 'closable', false, __('Closable', 'everything'), '', array('caption' => __('Yes', 'everything')));
		$options->addOption('boolean', 'preserve_state', false, __('Don\'t show again after closing', 'everything'), '', array('caption' => __('Yes', 'everything')));
	}

	// -------------------------------------------------------------------------

	protected function onShortcode($content, $code, \Drone\HTML &$html)
	{
		$html = HTML::div()
			->id('message-'.get_the_ID().'-'.md5($content))
			->class('message')
			->data('message-closable', $this->so_('closable')->toString())
			->data('message-preserve-state', $this->so_('preserve_state')->toString())
			->add(Func::wpShortcodeContent($content));
		if ($this->so('color')) {
			$html->addClass($this->so('color'));
		}
	}

	// -------------------------------------------------------------------------

	public function __construct()
	{
		parent::__construct(__('Message box', 'everything'));
	}

}

// -----------------------------------------------------------------------------

/**
 * Columns
 *
 * @since 1.0
 */
class Columns extends Shortcode
{

	// -------------------------------------------------------------------------

	protected function onSetupOptions(\Drone\Options\Group\Shortcode $options)
	{
		$options->addOption('boolean', 'separated', true, __('Separated', 'everything'), '', array('caption' => __('Yes', 'everything')));
	}

	// -------------------------------------------------------------------------

	protected function onShortcode($content, $code, \Drone\HTML &$html)
	{
		$html = HTML::div()
			->class('columns')
			->add(HTML::ul()->add(Func::wpShortcodeContent($content)));
		if ($this->so('separated')) {
			$html->addClass('separated');
		}
	}

	// -------------------------------------------------------------------------

	public function __construct()
	{
		parent::__construct(__('Columns', 'everything'), array(
			'visibility' => self::VISIBILITY_NONE
		));
	}

}

// -----------------------------------------------------------------------------

/**
 * Column
 *
 * @since 1.0
 */
class Column extends Shortcode
{

	// -------------------------------------------------------------------------

	protected function onSetupOptions(\Drone\Options\Group\Shortcode $options)
	{
		$options->addOption('text', 'width', '1/2', __('Width', 'everything'), __('In fraction format.', 'everything'), array('on_sanitize' => function($option, $original_value, &$value) {
			$value = str_replace(' ', '', $value);
			if (!preg_match('#^(?P<span>[0-9]+)/(?P<total>[0-9]+)$#', $value, $m) || ($m['total'] > 20 || $m['span'] > $m['total'])) {
				$value = $option->default;
			}
			if ($m['span'] == $m['total']) {
				$value = '1/1';
			}
		}));
	}

	// -------------------------------------------------------------------------

	protected function onShortcode($content, $code, \Drone\HTML &$html)
	{
		list($span, $total) = explode('/', $this->so('width'));
		$html = HTML::li()
			->class("col-{$span}-{$total}")
			->add(Func::wpShortcodeContent($content));
	}

	// -------------------------------------------------------------------------

	public function __construct()
	{
		parent::__construct(__('Column', 'everything'), array(
			'parent'     => 'columns',
			'visibility' => self::VISIBILITY_TINYMCE
		));
	}

}

// -----------------------------------------------------------------------------

/**
 * Tabs
 *
 * @since 1.0
 */
class Tabs extends Shortcode
{

	// -------------------------------------------------------------------------

	protected function onShortcode($content, $code, \Drone\HTML &$html)
	{
		$html = HTML::div()
			->class('tabs')
			->add(Func::wpShortcodeContent($content));
	}

	// -------------------------------------------------------------------------

	public function __construct()
	{
		parent::__construct(__('Tabs', 'everything'), array(
			'visibility' => self::VISIBILITY_NONE
		));
	}

}

// -----------------------------------------------------------------------------

/**
 * Tab
 *
 * @since 1.0
 */
class Tab extends Shortcode
{

	// -------------------------------------------------------------------------

	protected function onSetupOptions(\Drone\Options\Group\Shortcode $options)
	{
		$options->addOption('text', 'title', '', __('Title', 'everything'));
		$options->addOption('boolean', 'active', false, __('Active', 'everything'), '', array('caption' => __('Yes', 'everything')));
	}

	// -------------------------------------------------------------------------

	protected function onShortcode($content, $code, \Drone\HTML &$html)
	{
		$html = HTML::div()
			->title($this->so('title', '__empty', '&nbsp;'))
			->add(Func::wpShortcodeContent($content));
		if ($this->so('active')) {
			$html->addClass('active');
		}
	}

	// -------------------------------------------------------------------------

	public function __construct()
	{
		parent::__construct(__('Tab', 'everything'), array(
			'parent'     => 'tabs',
			'visibility' => self::VISIBILITY_TINYMCE
		));
	}

}

// -----------------------------------------------------------------------------

/**
 * Toggles
 *
 * @since 1.0
 */
class Toggles extends Shortcode
{

	// -------------------------------------------------------------------------

	protected function onSetupOptions(\Drone\Options\Group\Shortcode $options)
	{
		$options->addOption('boolean', 'singular', false, __('Singular', 'everything'), '', array('caption' => __('Yes', 'everything')));
	}

	// -------------------------------------------------------------------------

	protected function onShortcode($content, $code, \Drone\HTML &$html)
	{
		$html = HTML::ul()
			->class('toggles')
			->data('toggles-singular', $this->so_('singular')->toString())
			->add(Func::wpShortcodeContent($content));
	}

	// -------------------------------------------------------------------------

	public function __construct()
	{
		parent::__construct(__('Toggles', 'everything'), array(
			'visibility' => self::VISIBILITY_NONE
		));
	}

}

// -----------------------------------------------------------------------------

/**
 * Toggle
 *
 * @since 1.0
 */
class Toggle extends Shortcode
{

	// -------------------------------------------------------------------------

	protected function onSetupOptions(\Drone\Options\Group\Shortcode $options)
	{
		$options->addOption('text', 'title', '', __('Title', 'everything'));
		$options->addOption('boolean', 'active', false, __('Active', 'everything'), '', array('caption' => __('Yes', 'everything')));
	}

	// -------------------------------------------------------------------------

	protected function onShortcode($content, $code, \Drone\HTML &$html)
	{
		$html = HTML::li()
			->title($this->so('title', '__empty', '&nbsp;'))
			->add(Func::wpShortcodeContent($content));
		if ($this->so('active')) {
			$html->addClass('active');
		}
	}

	// -------------------------------------------------------------------------

	public function __construct()
	{
		parent::__construct(__('Toggle', 'everything'), array(
			'parent'     => 'toggles',
			'visibility' => self::VISIBILITY_TINYMCE
		));
	}

}

// -----------------------------------------------------------------------------

/**
 * Icon list
 *
 * @since 1.0
 */
class IconList extends Shortcode
{

	// -------------------------------------------------------------------------

	protected function onSetupOptions(\Drone\Options\Group\Shortcode $options)
	{
		$options->addOption('image_select', 'icon', 'right-open', __('Icon', 'everything'), '', array('font_path' => \Everything::ICON_FONT_PATH, 'options' => function() {
			return \Drone\Options\Option\ImageSelect::cssToOptions('data/img/icons/icons.css');
		}));
		$options->addOption('color', 'color', '', __('Color', 'everything'), __('If empty, leading color will be used.', 'everything'), array('required' => false));
	}

	// -------------------------------------------------------------------------

	protected function onShortcode($content, $code, \Drone\HTML &$html)
	{
		$content = Func::wpShortcodeContent($content);
		$content = str_replace('<ul>', '<ul class="fancy">', $content);
		$content = preg_replace('#<li>(?!\s*<i )#', '<li>'.\Everything::getShortcodeOutput('vector_icon', array('name' => $this->so('icon'), 'color' => $this->so('color') ? $this->so('color') : 'leading')), $content);
		$content = preg_replace('#<li>\s*(<i[^>]*></i>)\s*#', '<li>\1', $content);
		$html->add($content);
	}

	// -------------------------------------------------------------------------

	public function __construct()
	{
		parent::__construct(__('Icon list', 'everything'));
	}

}

// -----------------------------------------------------------------------------

/**
 * Rating
 *
 * @since 1.0
 */
class Rating extends Shortcode
{

	// -------------------------------------------------------------------------

	protected function onSetupOptions(\Drone\Options\Group\Shortcode $options)
	{
		$options->addOption('text', 'rate', '5/5', __('Rating', 'everything'), __('In a rate/max format. For example: 4/5 or 3.5/6.', 'everything'), array('on_sanitize' => function($option, $original_value, &$value) {
			$value = str_replace(' ', '', $value);
			if (!preg_match('#^[0-9]+([\.,][0-9]+)?/[0-9]+$#', $value)) {
				$value = $option->default;
			}
		}));
		$options->addOption('text', 'author', '', __('Author', 'everything'));
		$advanced = $options->addGroup('advanced', __('Advanced', 'everything'));
			$advanced->addOption('text', 'tag', 'p', __('HTML tag', 'everything'));
	}

	// -------------------------------------------------------------------------

	protected function onShortcode($content, $code, \Drone\HTML &$html)
	{

		// Rate, max
		list($rate, $max) = explode('/', $this->so('rate'));
		$rate = max((float)str_replace(',', '.', $rate), 0);
		$max  = (int)$max;

		// Content
		$content = Func::wpShortcodeContent($content);

		// Author
		if ($this->so('author')) {
			$content .= ' <cite>'.$this->so('author').'</cite>';
		}

		// Rating
		$html = HTML::make($this->so('advanced/tag'))->class('rating');
		$rate += 0.25;
		while ($rate >= 0.5 || $html->count() < $max) {
			$star = $html->addNew('i');
			if ($rate >= 1) {
				$rate -= 1.0;
				$star->class = 'icon-rating';
			} else if ($rate >= 0.5) {
				$rate -= 0.5;
				$star->class = 'icon-rating-half';
			} else {
				$star->class = 'icon-rating-empty';
			}
		}

		// Result
		if ($content) {
			$html->add('<br />', $content);
		}

	}

	// -------------------------------------------------------------------------

	public function __construct()
	{
		parent::__construct(__('Rating', 'everything'));
	}

}

// -----------------------------------------------------------------------------

/**
 * Social buttons
 *
 * @since 1.0
 */
class SocialButtons extends Shortcode
{

	// -------------------------------------------------------------------------

	protected function onSetupOptions(\Drone\Options\Group\Shortcode $options)
	{
		$options->addOption('select', 'size', 'big', __('Size', 'everything'), '', array('options' => array(
			'big'   => __('Big', 'everything'),
			'small' => __('Small', 'everything')
		)));
		$media = $options->addGroup('media', __('Media', 'everything'));
			$media->addOption('boolean', 'facebook', true, '', '', array('caption' => __('Facebook', 'everything')));
			$media->addOption('boolean', 'twitter', true, '', '', array('caption' => __('Twitter', 'everything')));
			$media->addOption('boolean', 'googleplus', true, '', '', array('caption' => __('Google+', 'everything')));
			$media->addOption('boolean', 'linkedin', true, '', '', array('caption' => __('LinkedIn', 'everything')));
			$media->addOption('boolean', 'pinterest', true, '', '', array('caption' => __('Pinterest', 'everything')));
	}

	// -------------------------------------------------------------------------

	protected function onShortcode($content, $code, \Drone\HTML &$html)
	{

		$media = array_keys(array_filter($this->so_('media')->toArray(), function($m) { return $m; }));

		if ($this->so('size') == 'big') {

			// Big
			$html = HTML::div()->class('social-buttons');
			$ul = $html->addNew('ul');
			foreach ($media as $media) {
				switch ($media) {
					case 'facebook':
						$ul->addNew('li')->add(
							\Everything::getPostMetaFormat('<div class="fb-like" data-href="%link%" data-send="false" data-layout="box_count" data-show-faces="false"></div>')
						);
						break;
					case 'twitter':
						$ul->addNew('li')->add(
							\Everything::getPostMetaFormat('<a class="twitter-share-button" href="https://twitter.com/share" data-url="%link%" data-text="%title_esc%" data-count="vertical">Tweet</a>')
						);
						break;
					case 'googleplus':
						$ul->addNew('li')->add(
							\Everything::getPostMetaFormat('<div class="g-plusone" data-href="%link%" data-size="tall" data-annotation="bubble"></div>')
						);
						break;
					case 'linkedin':
						$ul->addNew('li')->add(
							\Everything::getPostMetaFormat('<script class="inshare" type="IN/Share" data-url="%link%" data-counter="top" data-showzero="true"></script>')
						);
						break;
					case 'pinterest':
						if (has_post_thumbnail()) {
							list($thumbnail_src) = wp_get_attachment_image_src(get_post_thumbnail_id());
						} else {
							$thumbnail_src = '';
						}
						$ul->addNew('li')->add(
							sprintf('<a data-pin-config="above" href="//pinterest.com/pin/create/button/?url=%s&amp;media=%s&amp;description=%s" data-pin-do="buttonPin"><img src="//assets.pinterest.com/images/pidgets/pin_it_button.png" /></a>', urlencode(get_permalink()), urlencode($thumbnail_src), urlencode(get_the_title()))
						);
						break;
				}
			}

		} else {

			// Small
			$html = HTML::ul()->class('meta social');
			foreach ($media as $media) {
				switch ($media) {
					case 'facebook':
						$html->addNew('li')->add(
							\Everything::getPostMetaFormat('<div class="fb-like" data-href="%link%" data-send="false" data-layout="button_count" data-show-faces="false"></div>')
						);
						break;
					case 'twitter':
						$html->addNew('li')->add(
							\Everything::getPostMetaFormat('<a class="twitter-share-button" href="https://twitter.com/share" data-url="%link%" data-text="%title_esc%" data-count="horizontal">Tweet</a>')
						);
						break;
					case 'googleplus':
						$html->addNew('li')->add(
							\Everything::getPostMetaFormat('<div class="g-plusone" data-href="%link%" data-size="medium" data-annotation="bubble"></div>')
						);
						break;
					case 'linkedin':
						$html->addNew('li')->add(
							\Everything::getPostMetaFormat('<script class="inshare" type="IN/Share" data-url="%link%" data-counter="right" data-showzero="true"></script>')
						);
						break;
					case 'pinterest':
						if (has_post_thumbnail()) {
							list($thumbnail_src) = wp_get_attachment_image_src(get_post_thumbnail_id());
						} else {
							$thumbnail_src = '';
						}
						$html->addNew('li')->add(
							sprintf('<a data-pin-config="beside" href="//pinterest.com/pin/create/button/?url=%s&amp;media=%s&amp;description=%s" data-pin-do="buttonPin"><img src="//assets.pinterest.com/images/pidgets/pin_it_button.png" /></a>', urlencode(get_permalink()), urlencode($thumbnail_src), urlencode(get_the_title()))
						);
						break;
				}
			}

		}

	}

	// -------------------------------------------------------------------------

	public function __construct()
	{
		parent::__construct(__('Social buttons', 'everything'), array(
			'self_closing' => true
		));
	}

}

// -----------------------------------------------------------------------------

/**
 * Search form
 *
 * @since 1.0
 */
class Search extends Shortcode
{

	// -------------------------------------------------------------------------

	protected function onShortcode($content, $code, \Drone\HTML &$html)
	{
		$html->add(get_search_form(false));
	}

	// -------------------------------------------------------------------------

	public function __construct()
	{
		parent::__construct(__('Search form', 'everything'), array(
			'self_closing' => true
		));
	}

}

// -----------------------------------------------------------------------------

/**
 * Contact form
 *
 * @since 1.0
 */
class Contact extends Shortcode
{

	// -------------------------------------------------------------------------

	protected function onShortcode($content, $code, \Drone\HTML &$html)
	{
		$html->add(\Everything::contactForm(
			function($field, $required, $label) {
				$input = HTML::p();
				if ($field == 'message') {
					$input->addNew('textarea')->name($field)->placeholder(strtolower($label));
				} else if ($field == 'captcha') {
					$input->add('%s');
				} else {
					if ($required) {
						$label .= '*';
					}
					$input->addNew('input')->type('text')->name($field)->placeholder(strtolower($label));
				}
				return $input->html();
			},
			'',
			sprintf('<p><input type="submit" value="%s&nbsp;&rsaquo;" /><i class="icon-arrows-ccw load"></i><span class="msg small"></span></p>', __('Send', 'everything')),
			true
		));
	}

	// -------------------------------------------------------------------------

	public function __construct()
	{
		parent::__construct(__('Contact form', 'everything'), array(
			'self_closing' => true
		));
	}

}

// -----------------------------------------------------------------------------

/**
 * Device
 *
 * @since 1.0
 */
class Device extends Shortcode
{

	// -------------------------------------------------------------------------

	protected function onSetupOptions(\Drone\Options\Group\Shortcode $options)
	{
		$options->addOption('select', 'type', 'desktop', __('Type', 'everything'), '', array('options' => array(
			'desktop' => __('Desktop', 'everything'),
			'mobile' => __('Mobile', 'everything')
		)));
		$advanced = $options->addGroup('advanced', __('Advanced', 'everything'));
			$advanced->addOption('text', 'tag', 'div', __('HTML tag', 'everything'));
	}

	// -------------------------------------------------------------------------

	protected function onShortcode($content, $code, \Drone\HTML &$html)
	{
		$html = HTML::make($this->so('advanced/tag'))
			->class($this->so('type').'-only')
			->add(Func::wpShortcodeContent($content));
	}

	// -------------------------------------------------------------------------

	public function __construct()
	{
		parent::__construct(__('Device', 'everything'));
	}

}

// -----------------------------------------------------------------------------

/**
 * Posts
 *
 * @since 1.0
 */
class Posts extends Shortcode
{

	// -------------------------------------------------------------------------

	protected function onSetupOptions(\Drone\Options\Group\Shortcode $options)
	{
		$options->addOption('select', 'id', 0, __('Specific post/page', 'everything'), '', array('options' => function() {
			return
				array(0 => '('.__('Many posts', 'everything').')') +
				array_map(function($s) { return Func::stringCut($s, 55); }, Func::wpPostsList(array('numberposts' => -1, 'post_type' => 'any')));
		}));
		$options->addOption('select', 'type', 'post', __('Post type', 'everything'), '', array('options' => function() {
			$options = array();
			foreach ($GLOBALS['wp_post_types'] as $post_type) {
				if ($post_type->public) {
					$options[$post_type->name] = $post_type->labels->name;
				}
			}
			return $options;
		}));
		$options->addOption('select', 'category', 0, __('Category', 'everything'), __('Only for posts.', 'everything'), array('options' => function() {
			return
				array(0 => __('All categories', 'everything')) +
				Func::wpTermsList('category', array('hide_empty' => false));
		}));
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
		$options->addOption('boolean', 'exclude_previous', false, __('Duplicates', 'everything'), '', array('caption' => __('Exclude already displayed posts', 'everything')));
		$options->addOption('select', 'style', 'gallery', __('Style', 'everything'), '', array('options' => array(
			'gallery' => __('Gallery', 'everything'),
			'slider'  => __('Slider', 'everything')
		)));
		$options->addOption('number', 'columns', '', __('Columns', 'everything'), __('Only for gallery style.', 'everything'), array('required' => false, 'min' => 1, 'max' => 10));
		$options->addOption('boolean', 'thumbnail', true, __('Thumbnail', 'everything'), __('Only for gallery style.', 'everything'), array('caption' => __('Yes', 'everything')));
		$options->addOption('boolean', 'date', false, __('Date', 'everything'), '', array('caption' => __('Yes', 'everything')));
		$options->addOption('boolean', 'title', true, __('Title', 'everything'), '', array('caption' => __('Yes', 'everything')));
		$options->addOption('select', 'teaser', 'excerpt_content', __('Teaser', 'everything'), __('Regular content means everything before the "Read more" tag.', 'everything'), array('options' => array(
			''                => '('.__('None', 'everything').')',
			'content'         => __('Regular content', 'everything'),
			'excerpt_content' => __('Excerpt or regular content', 'everything'),
			'excerpt'         => __('Excerpt', 'everything')
		)));
		//$options->addOption('number', 'excerpt_length', 55, __('Excerpt length', 'everything'), '', array('unit' => __('words', 'everything'), 'min' => 1, 'max' => 100));
		$options->addOption('select', 'taxonomy', 0, __('Taxonomy', 'everything'), __('Only for gallery style.', 'everything'), array('options' => function() {
			$options = array(0 => '('.__('None', 'everything').')');
			foreach ($GLOBALS['wp_taxonomies'] as $taxonomy) {
				if ($taxonomy->public) {
					$post_type = isset($taxonomy->object_type[0], $GLOBALS['wp_post_types'][$taxonomy->object_type[0]]) ? $GLOBALS['wp_post_types'][$taxonomy->object_type[0]] : null;
					if (!is_null($post_type)) {
						$options[$taxonomy->name] = sprintf('%s (%s)', ucfirst($taxonomy->labels->singular_name), $post_type->labels->singular_name);
					} else {
						$options[$taxonomy->name] = ucfirst($taxonomy->labels->singular_name);
					}
				}
			}
			return $options;
		}));
	}

	// -------------------------------------------------------------------------

	public function onOptionsCompatybility(array &$data)
	{
		if (isset($data['content'])) {
			$data['teaser'] = $data['content'];
		}
	}

	// -------------------------------------------------------------------------

	protected function onShortcode($content, $code, \Drone\HTML &$html)
	{

		// Posts
		if ($this->so('id')) {
			$query = new \WP_Query(array(
				'p'         => (int)$this->so('id'),
				'post_type' => 'any'
			));
		} else {
			$query = new \WP_Query(array(
				'posts_per_page'      => $this->so('count', '__empty', -1),
				'cat'                 => $this->so('type') == 'post' ? $this->so('category') : 0,
				'post_status'         => 'publish',
				'post_type'           => $this->so('type'),
				'orderby'             => $this->so('orderby'),
				'order'               => $this->so('order'),
				'post__not_in'        => $this->so('exclude_previous') ? array_unique(\Everything::$posts_stack) : array(),
				'ignore_sticky_posts' => true
			));
		}
		if (!$query->have_posts()) {
			return;
		}

		// Style
		switch ($this->so('style')) {
			case 'gallery':
				$html = $this->getGallery($query);
				break;
			case 'slider':
				$html = $this->getSlider($query);
				break;
		}

	}

	// -------------------------------------------------------------------------

	protected function getGallery($query)
	{

		// Columns
		$columns = $this->so('columns', '__empty', min($this->so('count'), 4));

		// HTML
		$html = HTML::make();
		if ($columns > 1 || $this->so('count') > 1) {
			$ul = $html->tag('div')->class('columns')->addNew('ul');
			if ($this->so('thumbnail')) {
				$html->addClass('separated');
			}
		}

		while ($query->have_posts()) {

			$query->the_post();

			$_html = HTML::make();

			// Featured image
			if ($this->so('thumbnail') && has_post_thumbnail()) {
				$_html->addNew('figure')
					->class('full-width featured')
				 	->addNew('a')
						->attr(\Everything::getImageAttrs('a'))
						->href(get_permalink())
						->add(get_the_post_thumbnail(null, 'auto'));
			}

			// Date
			if ($this->so('date')) {
				$_html->addNew('p')->class('small featured')->add(\Everything::getPostMeta('date'));
			}

			// Title
			if ($this->so('title')) {
				$title_a = $_html->addNew('h3')->addNew('a')
					->href(get_permalink())
					->title(the_title_attribute(array('echo' => false)))
					->add(get_the_title());
				if (!$this->so('thumbnail') && !$this->so('teaser')) {
					$title_a
						->add('&nbsp;')
						->addNew('i')
							->class('icon-arrow-line-right');
				}
			}

			// Teaser
			switch ($this->so('teaser')) {
				case 'excerpt':
					$_html->addNew('p')->add(get_the_excerpt());
					break;
				case 'excerpt_content':
					if (has_excerpt()) {
						$_html->addNew('p')->add(get_the_excerpt());
						break;
					}
				case 'content':
					$GLOBALS['more'] = 0;
					$_html->add(\Drone\Func::wpProcessContent(get_the_content(\Everything::getReadMore())));
					break;
			}

			//$_html->addNew('p')->add(wp_trim_words(has_excerpt() ? get_the_excerpt() : strip_shortcodes($GLOBALS['post']->post_content), $this->so('excerpt_length'), ' [&hellip;]'));

			// Taxonomy
			if ($this->so('taxonomy')) {
				$_html->add(get_the_term_list(get_the_ID(), $this->so('taxonomy'), '<p class="small alt">', ', ', '</p>'));
			}

			// HTML
			if (isset($ul)) {
				$ul->addNew('li')->class('col-1-'.$columns)->add($_html);
			} else {
				$html->add($_html);
			}

		}

		wp_reset_postdata();

		return $html;

	}

	// -------------------------------------------------------------------------

	protected function getSlider($query)
	{

		$html = HTML::ul()->class('slider');

		while ($query->have_posts()) {

			$query->the_post();

			if (!has_post_thumbnail()) {
				continue;
			}

			$li = $html->addNew('li');

			// Date, title, teaser
			$caption = HTML::make();
			if ($this->so('date')) {
				$caption->addNew('p')->class('small featured')->add(\Everything::getPostMeta('date'));
			}
			if ($this->so('title')) {
				$caption->addNew('h3')->add(get_the_title());
			}
			if ($this->so('teaser') && ($excerpt = get_the_excerpt())) {
				$caption->addNew('p')->add($excerpt);
			}

			// Featured image
			$li->addNew('a')
				->attr(\Everything::getImageAttrs('a'))
				->href(get_permalink())
				->add(get_the_post_thumbnail(null, 'auto', array('title' => $caption->html())));

		}

		wp_reset_postdata();

		return $html;

	}

	// -------------------------------------------------------------------------

	public function __construct()
	{
		parent::__construct(__('Posts', 'everything'), array(
			'self_closing' => true
		));
	}

}

// -----------------------------------------------------------------------------

/**
 * Portfolio
 *
 * @since 1.0
 */
class Portfolio extends Shortcode
{

	// -------------------------------------------------------------------------

	protected function onSetupOptions(\Drone\Options\Group\Shortcode $options)
	{
		$options->addOption('select', 'id', 0, __('Children of', 'everything'), __('Displays child portfolios of selected portfolio.', 'everything'), array('options' => function() {
			return
				array(0 => '('.__('This', 'everything').')') +
				array_map(function($s) { return Func::stringCut($s, 55); }, Func::wpPostsList(array('numberposts' => -1, 'post_type' => 'portfolio')));
		}));
		$options->addOption('select', 'columns', '4', __('Layout', 'everything'), '', array('options' => array(
			'1'  => __('One column', 'everything'),
			'1+' => __('One+ column', 'everything'),
			'2'  => __('Two columns', 'everything'),
			'3'  => __('Three columns', 'everything'),
			'4'  => __('Four columns', 'everything'),
			'5'  => __('Five columns', 'everything'),
			'6'  => __('Six columns', 'everything'),
			'7'  => __('Seven columns', 'everything'),
			'8'  => __('Eight columns', 'everything')
		)));
		$options->addOption('select', 'filter', 'category', __('Filter', 'everything'), '', array('options' => array(
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
		$options->addOption('boolean', 'pagination', true, __('Pagination', 'everything'), '', array('caption' => __('Yes', 'everything')));
		$options->addOption('boolean', 'title', true, __('Title', 'everything'), '', array('caption' => __('Yes', 'everything')));
		$options->addOption('select', 'teaser', 'excerpt_content', __('Teaser', 'everything'), __('Regular content means everything before the "Read more" tag.', 'everything'), array('options' => array(
			''                => '('.__('None', 'everything').')',
			'content'         => __('Regular content', 'everything'),
			'excerpt_content' => __('Excerpt or regular content', 'everything'),
			'excerpt'         => __('Excerpt', 'everything')
		)));
		//$options->addOption('number', 'excerpt_length', 55, __('Excerpt length', 'everything'), '', array('unit' => __('words', 'everything'), 'min' => 1, 'max' => 100));
		$options->addOption('select', 'taxonomy', 'tag', __('Taxonomy', 'everything'), '', array('options' => array(
			''         => '('.__('None', 'everything').')',
			'category' => __('Category', 'everything'),
			'tag'      => __('Tag', 'everything')
		)));
		$options->addOption('select', 'image_hover', 'inherit', __('Hover effect', 'everything'), '', array('options' => array(
			'inherit'   => __('Inherit', 'everything'),
			''          => __('None', 'everything'),
			'zoom'      => __('Default', 'everything'),
			'grayscale' => __('Grayscale', 'everything')
		)));

	}

	// -------------------------------------------------------------------------

	public function onOptionsCompatybility(array &$data)
	{
		if (isset($data['content'])) {
			$data['teaser'] = $data['content'];
		}
	}

	// -------------------------------------------------------------------------

	protected function onShortcode($content, $code, \Drone\HTML &$html)
	{

		// Columns
		$columns_int  = (int)rtrim($this->so('columns'), '+');
		$columns_plus = $this->so('columns') != (string)$columns_int;

		// Posts
		$query = new \WP_Query(array(
			'posts_per_page' => $this->so('count', '__empty', -1),
			'post_status'    => 'publish',
			'post_type'      => 'portfolio',
			'post_parent'    => $this->so('id', '__default', get_the_ID()),
			'post__not_in'   => is_single() ? array(get_the_ID()) : array(),
			'orderby'        => $this->so('orderby'),
			'order'          => $this->so('order'),
			'paged'          => max(get_query_var('page'), get_query_var('paged'), 1)
		));
		if (!$query->have_posts()) {
			return;
		}

		// Bricks
		$bricks = $html->addNew('div')
			->class('bricks')
			->data('bricks-columns', $columns_int)
			->data('bricks-filter', Func::boolToString($this->so('filter')));

		while ($query->have_posts()) {

			$query->the_post();

			$div = $bricks->addNew('div');
			if ($columns_plus) {
				$div->addClass('one-plus');
			}

			// Item
			$item = $div->addNew('article')
				->id('portfolio-item-'.get_the_ID())
				->addClass(get_post_class('portfolio-item'));
			if ($this->so('title') || $this->so('teaser') || $this->so('taxonomy')) {
				$item->addClass('bordered');
			}

			// Terms
			if ($this->so('filter')) {
				$terms = Func::wpPostTermsList(get_the_ID(), 'portfolio-'.$this->so('filter'));
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
						->attr(\Everything::getImageAttrs('a', array('border' => false, 'hover' => $this->so('image_hover'), 'fancybox' => false)))
						->href(get_permalink())
						->add(get_the_post_thumbnail(null, $columns_plus ? 'auto-2' : 'auto'));
			}

			// Title
			if ($this->so('title')) {
				$item_desc->addNew($columns_int == 1 ? 'h2' : 'h3')->addNew('a')
					->href(get_permalink())
					->title(the_title_attribute(array('echo' => false)))
					->add(get_the_title());
			}

			// Teaser
			switch ($this->so('teaser')) {
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

			//$item_desc->addNew('p')->add(wp_trim_words(has_excerpt() ? get_the_excerpt() : strip_shortcodes($GLOBALS['post']->post_content), $this->so('excerpt_length'), ' [&hellip;]'));

			// Taxonomy
			if ($this->so('taxonomy')) {
				$item_desc->add(get_the_term_list(get_the_ID(), 'portfolio-'.$this->so('taxonomy'), '<p class="small alt">', ', ', '</p>'));
			}

		}

		wp_reset_postdata();

		// Paginate links
		if ($this->so('pagination') && ($pagination = \Everything::getPaginateLinks('portfolio', $query))) {
			$html->add($pagination);
		}

	}

	// -------------------------------------------------------------------------

	public function __construct()
	{
		parent::__construct(__('Portfolio', 'everything'), array(
			'self_closing' => true
		));
	}

}

// -----------------------------------------------------------------------------

/**
 * Blog
 *
 * @since 1.0
 */
class Blog extends Shortcode
{

	// -------------------------------------------------------------------------

	protected function onSetupOptions(\Drone\Options\Group\Shortcode $options)
	{
		$options->addOption('select', 'category', 0, __('Category', 'everything'), __('Only for posts.', 'everything'), array('options' => function() {
			return
				array(0 => __('All categories', 'everything')) +
				Func::wpTermsList('category', array('hide_empty' => false));
		}));
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
		$options->addOption('number', 'count', get_option('posts_per_page'), __('Posts per page', 'everything'), '', array('min' => 1));
		$options->addOption('boolean', 'exclude_previous', false, __('Duplicates', 'everything'), '', array('caption' => __('Exclude already displayed posts', 'everything')));
		$options->addOption('boolean', 'pagination', true, __('Pagination', 'everything'), '', array('caption' => __('Yes', 'everything')));
		$options->addOption('boolean', 'ignore_sticky_posts', false, __('Sticky posts', 'everything'), '', array('caption' => __('Ignore', 'everything')));
		$options->addOption('select', 'style', 'classic', __('Style', 'everything'), '', array('options' => array(
			'classic' => __('Classic', 'everything'),
			'bricks'  => __('Columns', 'everything')
		)));
		$options->addOption('number', 'columns', 2, __('Columns', 'everything'), __('Only for columns style.', 'everything'), array('min' => 1, 'max' => 8));
		$options->addOption('select', 'filter', '', __('Filter', 'everything'), __('Only for columns style.', 'everything'), array('options' => array(
			''         => '('.__('None', 'everything').')',
			'category' => __('Category', 'everything'),
			'post_tag' => __('Tag', 'everything')
		)));
	}

	// -------------------------------------------------------------------------

	protected function onShortcode($content, $code, \Drone\HTML &$html)
	{

		// Posts
		$GLOBALS['wp_query'] = $query = new \WP_Query(array(
			'posts_per_page'      => $this->so('count'),
			'cat'                 => $this->so('category'),
			'post_status'         => 'publish',
			'post_type'           => 'post',
			'orderby'             => $this->so('orderby'),
			'order'               => $this->so('order'),
			'paged'               => max(get_query_var('page'), get_query_var('paged'), 1),
			'post__not_in'        => $this->so('exclude_previous') ? array_unique(\Everything::$posts_stack) : array(),
			'ignore_sticky_posts' => $this->so('ignore_sticky_posts')
		));
		if (!$query->have_posts()) {
			wp_reset_query();
			return;
		}

		// Style
		switch ($this->so('style')) {
			case 'classic':
				$html = $this->getClassic($query);
				break;
			case 'bricks':
				$html = $this->getBricks($query);
				break;
		}

	}

	// -------------------------------------------------------------------------

	protected function getClassic($query)
	{

		// HTML
		$html = HTML::div()->class('anti-section');

		while (have_posts()) {

			the_post();
			$GLOBALS['more'] = 0;

			$html->addNew('section')
				->class('section')
				->add(\Drone\Func::functionGetOutputBuffer('get_template_part', 'parts/post'));

		}

		wp_reset_query();

		// Paginate links
		if ($this->so('pagination') && ($pagination = \Everything::getPaginateLinks('blog', $query))) {
			$html->addNew('section')->class('section')->add($pagination);
		}

		return $html;

	}

	// -------------------------------------------------------------------------

	protected function getBricks($query)
	{

		// HTML
		$html = HTML::div()
			->class('bricks')
			->data('bricks-columns', $this->so('columns'))
			->data('bricks-filter', Func::boolToString($this->so('filter')));

		while (have_posts()) {

			the_post();
			$GLOBALS['more'] = 0;

			$brick = $html->addNew('div')
				->add(\Drone\Func::functionGetOutputBuffer('get_template_part', 'parts/post'));

			if ($this->so('filter')) {
				$terms = \Drone\Func::wpPostTermsList(get_the_ID(), $this->so('filter'));
				if (is_category() && ($term_id = array_search(single_cat_title('', false), $terms)) !== false) {
					unset($terms[$term_id]);
				}
				$brick->data('bricks-terms', json_encode(array_values($terms)));
			}

		}

		wp_reset_query();

		// Paginate links
		if ($this->so('pagination') && ($pagination = \Everything::getPaginateLinks('blog', $query))) {
			$html = HTML::make()->add($html, $pagination);
		}

		return $html;

	}

	// -------------------------------------------------------------------------

	public function __construct()
	{
		parent::__construct(__('Blog', 'everything'), array(
			'self_closing' => true
		));
	}

}