<?php
/*
Plugin Name: WP-MulticolLinks
Plugin URI: http://wordpress.org/extend/plugins/wp-multicollinks/
Plugin Description: Show the links with multiple columns layout in the sidebar.
Version: 1.0.2
Author: mg12
Author URI: http://www.fighton.cn/
*/

/** core functions */
include ('core.php');

/** l10n */
load_plugin_textdomain('wp-multicollinks', '/wp-content/plugins/wp-multicollinks/languages/');

/**
 * 打印多栏的列表
 * @param args		参数字符串
 */
function wp_multicollinks( $args = '' ) {
	echo create_multicollinks( $args );
}

// -- widget START ------------------------------------------------------------

/**
 * 定义 Widget
 * @param args		参数字符串
 */
function wp_widget_multicollinks($args) {
	if ( '%BEG_OF_TITLE%' != $args['before_title'] ) {
		if ( $output = wp_cache_get('widget_multicollinks', 'widget') ) {
			return print($output);
		}
		ob_start();
	}

	extract($args);
	$options = get_option('widget_multicollinks');
	$title = empty($options['title']) ? __('Links', 'wp-multicollinks') : $options['title'];

	// 转化参数
	$orderbyParam = 'name';
	if ($options['orderby'] == 2) {
		$orderbyParam = 'url';
	} else if ($options['orderby'] == 3) {
		$orderbyParam = 'rating';
	} else if ($options['orderby'] == 4) {
		$orderbyParam = 'rand';
	}
	$orderParam = 'ASC';
	if ($options['order'] == 2) {
		$orderParam = 'DESC';
	}

	// 组合参数字符串
	$argsBinding = 'limit='		. $options['number'] 
				. '&columns='	. $options['columns'] 
				. '&category='	. $options['category'] 
				. '&orderby='	. $orderbyParam 
				. '&order='		. $orderParam 
				. '&navigator='	. ($options['navigator'] ? 'true' : 'false');

	// 页面上打印
	echo $before_widget;
	echo $before_title . $title . $after_title;
	echo '<ul>';
	wp_multicollinks($argsBinding);
	echo '</ul>';
	echo $after_widget;

	if ( '%BEG_OF_TITLE%' != $args['before_title'] ) {
		wp_cache_add('widget_multicollinks', ob_get_flush(), 'widget');
	}
}

/*
 * 清除缓存
 */
function wp_delete_multicollinks_cache() {
	wp_cache_delete( 'widget_multicollinks', 'widget' );
}
add_action( 'comment_post', 'wp_delete_multicollinks_cache' );
add_action( 'wp_set_comment_status', 'wp_delete_multicollinks_cache' );

/**
 * Widget 选项控制
 */
function wp_widget_multicollinks_control() {
	$options = $newoptions = get_option('widget_multicollinks');
	if ( $_POST["multicollinks-submit"] ) {
		$newoptions['title'] = strip_tags(stripslashes($_POST["multicollinks-title"]));
		$newoptions['number'] = (int)$_POST["multicollinks-number"];
		$newoptions['columns'] = (int)$_POST["multicollinks-columns"];
		$newoptions['category'] = strip_tags(stripslashes($_POST["multicollinks-category"]));
		$newoptions['orderby'] = (int)$_POST["multicollinks-orderby"];
		$newoptions['order'] = (int)$_POST["multicollinks-order"];
		$newoptions['navigator'] = (bool)$_POST["multicollinks-navigator"];
	}
	if ( $options != $newoptions ) {
		$options = $newoptions;
		update_option('widget_multicollinks', $options);
		wp_delete_multicollinks_cache();
	}

	// 限定参数
	$title = attribute_escape($options['title']);
	if ( !$number = (int) $options['number'] ) {
		$number = 0;
	} else if ( $number < 0 ) {
		$number = 0;
	}
	if ( !$columns = (int) $options['columns'] ) {
		$columns = 1;
	} else if ( $columns < 1 ) {
		$columns = 1;
	}
	$category = attribute_escape($options['category']);
	if ( !$orderby = (int) $options['orderby']) {
		$orderby = 1;
	}
	if ( !$order = (int) $options['order']) {
		$order = 1;
	}

			// 后台选项的显示
?>
			<p>
				<label for="multicollinks-title">
					<?php _e('Title: ', 'wp-multicollinks'); ?>
					<input class="widefat" id="multicollinks-title" name="multicollinks-title" type="text" value="<?php echo $title; ?>" />
				</label>
			</p>

			<p>
				<label for="multicollinks-number">
					<?php _e('Number of links to show: ', 'wp-multicollinks'); ?>
					<input style="width: 25px;" id="multicollinks-number" name="multicollinks-number" type="text" value="<?php echo $number; ?>" />
				</label>
				<br />
				<small><?php _e('(0 for ∞)', 'wp-multicollinks'); ?></small>
			</p>

			<p>
				<label for="multicollinks-columns">
					<?php _e('Number of columns: ', 'wp-multicollinks'); ?>
					<input style="width: 25px;" id="multicollinks-columns" name="multicollinks-columns" type="text" value="<?php echo $columns; ?>" />
				</label>
				<br />
				<small><?php _e('(default: 1)', 'wp-multicollinks'); ?></small>
			</p>

			<p>
				<label for="multicollinks-category">
					<?php _e('Name of the category: ', 'wp-multicollinks'); ?>
					<input style="width: 25px;" id="multicollinks-category" name="multicollinks-category" type="text" value="<?php echo $category; ?>" />
				</label>
			</p>

			<p>
				<label for="multicollinks-orderby">
					<?php _e('Sort by: ', 'wp-multicollinks'); ?>
					<select id="multicollinks-orderby" name="multicollinks-orderby" size="1">
						<option value="1" <?php if($orderby != 2 && $orderby != 3 && $orderby != 4) echo ' selected '; ?>>name</option>
						<option value="2" <?php if($orderby == 2) echo ' selected '; ?>>url</option>
						<option value="3" <?php if($orderby == 3) echo ' selected '; ?>>rating</option>
						<option value="4" <?php if($orderby == 4) echo ' selected '; ?>>rand</option>
					</select>
				</label>
			</p>

			<p>
				<label for="multicollinks-order">
					<?php _e('How to sort? ', 'wp-multicollinks'); ?>
					<select id="multicollinks-order" name="multicollinks-order" size="1">
						<option value="1" <?php if($order != 2) echo ' selected '; ?>>ASC</option>
						<option value="2" <?php if($order == 2) echo ' selected '; ?>>DESC</option>
					</select>
				</label>
			</p>

			<p>
				<label for="multicollinks-navigator">
					<input name="multicollinks-navigator" type="checkbox" value="checkbox" <?php if($options['navigator']) echo "checked='checked'"; ?> />
					 <?php _e('Show \'Show all\' button?', 'wp-multicollinks'); ?>
				</label>
			</p>

			<input type="hidden" id="multicollinks-submit" name="multicollinks-submit" value="1" />
<?php
}

/**
 * 初始化 Widget
 */
function wp_widgets_multicollinks_init() {
	if ( !is_blog_installed() )
		return;

	$widget_ops = array('classname' => 'widget_multicollinks', 'description' => __("The links in multiple columns", 'wp-multicollinks') );
	wp_register_sidebar_widget('multicollinks', __('WP-MulticolLinks', 'wp-multicollinks'), 'wp_widget_multicollinks', $widget_ops);
	wp_register_widget_control('multicollinks', __('WP-MulticolLinks', 'wp-multicollinks'), 'wp_widget_multicollinks_control' );
}

/**
 * 执行 Widget 初始化
 */
add_action('widgets_init', 'wp_widgets_multicollinks_init');

// -- widget END ------------------------------------------------------------

// -- head START ------------------------------------------------------------

/**
 * 打印样式和脚本代码
 */
function multicollinks_head() {
	$css_url = get_bloginfo("wpurl") . '/wp-content/plugins/wp-multicollinks/wp-multicollinks.css';
	if ( file_exists(TEMPLATEPATH . "/wp-multicollinks.css") ){
		$css_url = get_bloginfo("template_url") . "/wp-multicollinks.css";
	}
	echo "\n" . '<!-- START of script generated by WP-MulticolLinks -->';
	echo "\n" . '<link rel="stylesheet" href="' . $css_url . '" type="text/css" media="screen" />';
	echo "\n" . '<script type="text/javascript" src="' . get_bloginfo('wpurl') . '/wp-content/plugins/wp-multicollinks/wp-multicollinks.js"></script>';
	echo "\n" . '<!-- END of script generated by WP-MulticolLinks -->' . "\n";
}

/**
 * 在页面 head 部分插入代码
 */
add_action('wp_head', 'multicollinks_head');

// -- head END ------------------------------------------------------------
?>
