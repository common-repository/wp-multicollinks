<?php/** * 点击 Show all 时触发的 Ajax 方法 */function ml_ajax() {	if( $_GET['action'] == 'ml_ajax' ) {		$argsf = $_GET["args"];		$args = str_replace('---', '&', $argsf);		$args = str_replace('--', '=', $args);		// 重定义 limit 参数		if ( strstr($args, 'limit=') ) {			$args = str_replace('limit=', 'unknown=', $args);		} else {			$args .= '&limit=0';		}		echo create_multicollinks( $args );		die();	}}add_action('init', 'ml_ajax');
/**
 * 获取多栏的列表
 * @param args		参数字符串
 */
function create_multicollinks( $args = '' ) {	// AJAX 翻页时用的参数	$argsf = str_replace('=', '--', $args);	$argsf = str_replace('&', '---', $argsf);

	// 默认参数
	$defaults = array(
		'limit' => 0,				// 收藏数量
		'columns' => 1,				// 分栏数量
		'category' => '',			// 分类名称
		'orderby' => 'name', 		// 排序对象
		'order' => 'ASC',			// 排序方法		'navigator' => 'true'		// 是否显示翻页的导航, true: 显示; false: 不显示
	);

	// 替换参数
	$args = wp_parse_args( $args, $defaults );

	// 限定参数
	if ( $args['limit'] < 0 ) {
		$args['limit'] = 0;
	}
	if ( $args['columns'] < 1 ) {
		$args['columns'] = 1;
	} else if ( $args['columns'] > 4 ) {
		$args['columns'] = 4;
	}
	if ( $args['order'] != 'DESC' ) {
		$args['order'] = 'ASC';
	}
	if ( $args['orderby'] == 'rand' ) {
		$args['orderby'] = 'rand()';
		$args['order'] = '';
	} else if ( $args['orderby'] == 'url' ) {
		$args['orderby'] = 'url';
	} else if ( $args['orderby'] == 'rating' ) {
		$args['orderby'] = 'rating';
	} else {
		$args['orderby'] = 'name';
	}
	if ( $args['navigator'] != 'false' ) {
		$args['navigator'] = 'true';
	}

	// 在数据库中获取评论相关信息
	global $wpdb, $links, $link;

	// 数量限制
	$limit = '';
	if ($args['limit'] > 0) {		// 准备多取一个, 以便获知是否有更多链接存在
		$limit = ' LIMIT ' . ($args['limit'] + 1);
	}

	// SQL 查找数据集合
	if ( $args['category'] == '' ) {
		// 显示所有分类的数据集合
		$links = $wpdb->get_results("SELECT T1.link_name AS name,T1.link_url AS url,T1.link_description AS description,T1.link_target AS target,T1.link_rating AS rating FROM $wpdb->links T1 ORDER BY " . $args['orderby'] . " " . $args['order'] . $limit);
	} else {
		// 只显示指定分类的数据集合
		$links = $wpdb->get_results("SELECT T1.link_name AS name,T1.link_url AS url,T1.link_description AS description,T1.link_target AS target,T1.link_rating AS rating FROM $wpdb->links T1,$wpdb->term_relationships T2,$wpdb->term_taxonomy T3,$wpdb->terms T4 WHERE link_visible='Y' AND T1.link_id = T2.object_id AND T2.term_taxonomy_id = T3.term_taxonomy_id AND T3.term_id = T4.term_id AND T4.name='" . $args['category'] . "' ORDER BY " . $args['orderby'] . " " . $args['order'] . $limit);
	}	if ($args['limit'] > 0) {		// 如果能够获取多一个元组, 证明有更多链接存在		$hasMore = (count($links) - $args['limit'] > 0);		// 有更多链接存在时, 删除最后那个多余的		if ( $hasMore ) {			array_pop($links);		}	}

	// 获取列表
	$result = '';
	$count = 0;
	if ( $links ) {
		foreach ($links as $link) {
			// target
			$target = '';
			if ($link->target != '') {
				$target = ' target="' . $link->target . '"';
			}

			// 只有一列的时候
			if ( $args['columns'] == 1 ) {
				$result .= '<li class="ml_item"><a href="' . $link->url .'" title="' . $link->description .'"' . $target . '>' . $link->name .'</a></li>';

			// 两或以上的时候
			} else {
				if ( $count % $args['columns'] == 0 ) {
					$result .= '<li class="ml_item"><div>';
				}
				$result .= '<div class="ml_col ml_col_' . $args['columns'] . '"><a href="' . $link->url .'" title="' . $link->description .'"' . $target . '>' . $link->name .'</a></div>';
				++$count;
				if ( $count % $args['columns'] == 0 ) {
					$result .= '<div class="ml_fixed"></div></div></li>';
				}
			}
		} // foreach ($links as $link)

		// 显示多列而不能正常结束一行时, 为其补上结束标记 (显示一列时不存在此问题)
		if ( $count % $args['columns'] != 0 ) {
			$result .= '<div class="ml_fixed"></div></div></li>';
		}

		// 当需要显示 Show all 按钮时才显示这一栏
		if ( $hasMore && $args['navigator'] == 'true' ) {
			// Show all 按钮
			$showAll = '<a class="ml_showall" href="javascript:void(0);" onclick="MLJS.showall(\'' . get_bloginfo('wpurl') . '\',\'' . $argsf . '\',\'' . __('Loading', 'wp-multicollinks') . '\');">' . __('Show all &raquo;', 'wp-multicollinks') . '</a>';
			$result .= '<li id="ml_nav"><div>' . $showAll . '<div class="ml_fixed"></div></div></li>';
		}

		// 返回 HTML 格式的字符串
		return $result;

	} // if ( $links )
}

?>
