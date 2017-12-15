<?php

namespace JB\SPW;

class Helpers {

  public static function get_template_path($template_filename) {
    $templates_path = plugin_dir_path(dirname(__FILE__)) . "templates";
    $template_path = "$templates_path/$template_filename";

    return $template_path;
  }

  public static function build_sidebar_link($post) {
    $name = $post['title'];
    $url = $post['url'];

    return "<a href=\"$url\">$name</a>";
  }

}

