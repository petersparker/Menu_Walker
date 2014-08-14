<?=php
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
?>
