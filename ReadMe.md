The Custom Menu Walker shown on this page makes a number of changes to the contents of a menu.

First, we’ll start by creating a custom menu. We’ll call this “WalkerMenu” and put the following items in it:

 - “Menu Walk” – with a link to this page
 - “Presentations” – with a link to my Presentations page
 - “Search Engines” – with no link
	 - “Google” – with a link to Google’s page
	 - “Bing” – with a link to Bing’s page
	 - “Yandex” – with a link to Yandex’s page
 - “Social Networks” – with no link
	 - My Twitter handle, with a link to my Twitter stream
	 - My name, with a link to my Facebook profile
	 - My name, with a link to my LinkedIn profile
 - “Contact”, with a link to my Contact page
 - “Logout”, with a link to the WordPress logout action
 
The idea is to have a couple different levels of menus so that our Walker has something to work with.

What is the Walker going to do? A Walker class has the ability to modify the content of the menu as it’s being rendered to the page. This means that we can change the contents of the links, the titles, even the class names on the \<ul> and \<li> elements. I wanted to show a couple different things that can be done so I came up with the following:

1. Add a custom class, dropdown-menu, to the “Search Engines” menu.
3. Add a custom class, custom-menu-class, to the “Google” link in the “Search Engine” menu.
3. Remove the title= attribute from any links in the “Search Engines” menu.
4. Add a custom class to each of the links in the “Social Networks” menu so they can be individually addressed with CSS.
5. Remove the “Logout” item from the menu if the user is not currently logged in.


These changes allow a theme designer to target the various elements with CSS, giving them control over specific sub-menus and items within a menu. With this as a starting point, you can let your imagination give you additional ideas on what you can do with it.

Here’s the code for the Walker:

    class CustomMenuWalker extends Walker_Nav_Menu
    {
        private $se_menu = FALSE;
        private $last_item = NULL;
     
        public function start_lvl(&$output, $depth = 0, $args = array())
        {
    //      $indent = str_repeat("\t", $depth);
    //      $output .= "\n$indent<ul class=\"sub-menu\">\n";
     
            $classes = array('sub-menu');
            if ('Search Engines' === $this->last_item->title)
                $classes[] = 'custom-sub-menu';
     
            $indent = str_repeat("\t", $depth);
            $output .= "\n{$indent}<ul class=\"" . implode(' ', $classes) . "\">\n";
        }
     
        public function end_lvl(&$output, $depth = 0, $args = array())
        {
            $this->se_menu = FALSE;
     
            parent::end_lvl($output, $depth, $args);
        }
     
        public function start_el(&$output, WP_Post $item, $depth = 0, $args = array(), $id = 0)
        {
            $this->last_item = $item;
     
            // 1. Add a custom class, dropdown-menu, to the “Search Engines” menu.
            if ('Search Engines' === $item->title) {
                $this->se_menu = TRUE;
                $item->classes[] = 'dropdown-menu';
            }
     
            // 2. Add a custom class, custom-menu-class, to the “Google” link in the “Search Engine” menu.
            if ('Google' === $item->title)
                $item->classes[] = 'custom-menu-class';
     
            // 3. Remove the title= attribute from any links in the “Search Engines” menu.
            if ($this->se_menu)
                $item->attr_title = '';
     
            // 4. Add a custom class to each of the links in the “Social Networks” menu
            $domain = parse_url($item->url, PHP_URL_HOST);
            $sm = FALSE;
            switch ($domain)
            {
            case 'facebook.com':
                $item->classes[] = 'icon-facebook';
                $sm = TRUE;
                break;
            case 'twitter.com':
                $item->classes[] = 'icon-twitter';
                $sm = TRUE;
                break;
            case 'www.linkedin.com':
                $item->classes[] = 'icon-linkedin';
                $sm = TRUE;
                break;
            }
            if ($sm) {
                $item->title = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $item->title;
                $item->target = '_blank';
                $item->xfn = 'nofollow';
                $item->attr_title = 'Social networking: ' . $item->attr_title;
            }
     
            // 5. Remove the “Logout” item from the menu if the user is not currently logged in
            $call_parent = TRUE;
            if ('Logout' === $item->title && !is_user_logged_in())
                $call_parent = FALSE;
     
            // use parent class's start_el() method to render the HTML content
            if ($call_parent)
                parent::start_el($output, $item, $depth, $args, $id);
        }
     
        public function end_el(&$output, WP_Post $item, $depth = 0, $args = array())
        {
            $call_parent = TRUE;
            if ('Logout' === $item->title && !is_user_logged_in())
                $call_parent = FALSE;
            if ($call_parent)
                parent::end_el($output, $item, $depth, $args);
        }
    }

With this Walker class defined, you can add the following code into your template file to have the wp_nav_menu() function make use of your new walker:

	$walker = new CustomMenuWalker();
	 
	$args = array(
	    'menu' => 'WakerMenu',
	    'walker' => $walker,
	);
	wp_nav_menu($args);


If you’d like to read more, you can look at the Codex pages for: wp_nav_menu() and the Walker_Nav_Menu class .

You can see the results of this in the menu at the top of the page here. The “Search Engines” menu has a gray background. It can be targeted with CSS because it has a class added to it. The “Google” item in the menu also has a unique class added to it, so it’s background can be set to yellow. All of the sub-menu links within the “Search Engines” menu have their title attribute removed.

The other unique modifications are to the sub-menu items in the “Social Networks” menu. Each of these are identified by the URL that the link is pointing to. This way they can each get a different class added to the \<li> element, allowing us to add custom background images that are unique to each link.

Just in case you’re curious about that $item parameter sent to the start_el() method, this is what it looks like:

	WP_Post::__set_state(array(
	   'ID' => 40,
	   'post_author' => '1',
	   'post_date' => '2014-08-02 22:31:11',
	   'post_date_gmt' => '2014-08-02 22:31:11',
	   'post_content' => '',
	   'post_title' => 'Logout',
	   'post_excerpt' => '',
	   'post_status' => 'publish',
	   'comment_status' => 'open',
	   'ping_status' => 'open',
	   'post_password' => '',
	   'post_name' => 'logout',
	   'to_ping' => '',
	   'pinged' => '',
	   'post_modified' => '2014-08-02 22:31:11',
	   'post_modified_gmt' => '2014-08-02 22:31:11',
	   'post_content_filtered' => '',
	   'post_parent' => 0,
	   'guid' => 'http://domain.com/?p=40',
	   'menu_order' => 6,
	   'post_type' => 'nav_menu_item',
	   'post_mime_type' => '',
	   'comment_count' => '0',
	 
	   'filter' => 'raw',
	   'db_id' => 40,
	   'menu_item_parent' => '0',
	   'object_id' => '40',
	   'object' => 'custom',
	   'type' => 'custom',
	   'type_label' => 'Custom',
	   'title' => 'Logout',
	   'url' => '/wp-login.php?action=logout',
	   'target' => '',
	   'attr_title' => '',
	   'description' => '',
	   'classes' =>
	  array (
	    0 => '',
	    1 => 'menu-item',
	    2 => 'menu-item-type-custom',
	    3 => 'menu-item-object-custom',
	    4 => 'custom-menu-class',
	  ),
	   'xfn' => '',
	   'current' => false,
	   'current_item_ancestor' => false,
	   'current_item_parent' => false,
	))

It’s a WP_Post object, with a few more pieces of data added to it.

With Walkers, you have complete control of the appearance of the menu items, and even whether or not an item will be displayed within the menu. Try some experiments with this and see what you can come up with.

<br>


---
Link to original page: [DaveJesch]<br>
Link to [Advanced Menu Settings]<br>


[Advanced Menu Settings]:http://en.support.wordpress.com/advanced-menu-settings/
[DaveJesch]:http://davejesch.com/presentations/menu-walker/
