=== WP-MulticolLinks ===
Contributors: mg12
Donate link: http://www.neoease.com/plugins/
Tags: links, widget, sidebar, AJAX
Requires at least: 2.2
Tested up to: 2.7
Stable tag: 1.0.2

Show the links in multiple columns.

== Description ==

Show the links with multiple columns layout in the sidebar.
You can limit the number of links, switch it between one-column and multiple-column layouts in the sidebar. and you can sort the links or random, use the 'Show all' button.

在侧边栏显示以多栏的布局显示友情链接。
你可以限制显示链接的数量，在单栏和多栏之间随意切换。并且可以对链接进行排序或随机排列。还可以使用“显示全部”的按钮。

**Supported Languages:**

* US English (default)
* 简体中文

**Demo:**

[http://www.neoease.com/](http://www.neoease.com/)

== Installation ==

1. Unzip archive to the '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. This is two ways to add the WP-MulticolLinks widget to the sidebar:
    * Go to 'Design->Widgets', and add the WP-MulticolLinks to your blog.
    * In your 'sidebar.php' file add the following lines:
****

    <h3>Blogroll</h3>
    <?php wp_multicollinks(); ?>

**Arguments:**

    NAME            TYPE       DESCRIPTION                 DEFAULT VERSIONS
    limit           integer    the number of links.        0(all)  1.0+
    columns         integer    the number of columns.      1       1.0+
    category        string     the name of the category            1.0+
                               to show.
    orderby         string     sort by                     name    1.0+
                               name/url/rating/rand.
    order           ASC/DESC   how to sort.                ASC     1.0+
    navigator       true/false show navigator buttons.     true    1.0+

**Using Examples:**

    <?php wp_multicollinks('limit=20&orderby=rand&columns=2'); ?>
    <?php wp_multicollinks('category=blogroll&orderby=rand&order=DESC'); ?>
    <?php wp_multicollinks('limit=20&navigator=false'); ?>

**Custom CSS:**

* WP-MulticolLinks will load wp-multicollinks.css from your theme directory if it exists.
* If it doesn't exists, it will load the default style that comes with WP-MulticolLinks.

== Screenshots ==

1. This is the WP-MulticolLinks widget with 2 columns.

== Changelog ==

****

    VERSION DATE       TYPE   CHANGES
    1.0.2   2008/12/29 MODIFY Updated the JavaScript and using namespace.
                       FIX    Fixed DB table names.
    1.0.1   2008/08/20 FIX    Fixed display bug in IE6.
    1.0     2008/07/30 NEW    Add localization support.
                       NEW    Add Simplified Chinese language support.
                       DELETE Remove 'target' argument.
    0.6     2008/07/26 FIX    Fixed a bug of 'target' argument.
    0.5     2008/07/25 NEW    Add WordPress Widget support.
    0.4     2008/07/24 FIX    Fixed a display bug.
                       NEW    Add AJAX paging support.
    0.3     2008/07/21 NEW    Base features.
