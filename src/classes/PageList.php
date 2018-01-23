<?php

namespace JB\SPW;

use JB\SPW\Helpers;

class PageList {

  private $origin;
  private $include_apex = false;
  private $is_apex;
  private $override_ids = null;

  public function __construct($list_options) {
    $this->origin = $this->find_origin($list_options);
    $this->include_apex = $list_options['apex'];
    $this->override_ids = $list_options['override_ids'];
  }

  public function get_origin() {
    return $this->origin;
  }

  public function render() {
    $template = Helpers::get_template_path('_page_list.php');
    $pages = $this->find_pages();

    ob_start();
    include $template;
    $output = ob_get_contents();
    ob_end_clean();

    echo $output;
  }

  private function find_origin($options) {
    $post_id = $origin = get_the_ID();
    $post = get_post($post_id);

    if ($post->post_parent) {
      $this->is_apex = false;

      $ancestors = get_post_ancestors($post);
      $origin_index = count($ancestors) - 1;
      $origin = $ancestors[$origin_index];
    } else {
      $this->is_apex = true;
    }

    return $origin;
  }

  private function find_pages() {
    $pages = null;

    if ($this->override_ids) {
      $pages = $this->find_override_pages();
    } else {
      $pages = $this->find_sibling_pages();
    }

    return $pages;
  }

  private function find_override_pages() {
    $override_pages = [];
    $title = "";
    $url = "";

    foreach ($this->override_ids as $id) {
      if (get_post_status($id)) {
        $post = get_post($id);

        $title = $post->post_title;
        $url = get_permalink($post->ID);
      } else {
        $prefix_pattern = '/^archive:(.*)/';
        preg_match($prefix_pattern, $id, $matches);
        $post_type = $matches[1];

        $post_type_object = get_post_type_object($post_type);

        $title = $post_type_object->labels->name;
        $url = get_post_type_archive_link($post_type);
      }

      $override_pages[] = array(
        'title' => $title,
        'url' => $url
      );
    }

    return $override_pages;
  }

  private function valid_page_id($post_id) {
    $valid_post = (get_post_status($post_id) ? true : false);
    $valid_archive = $this->valid_archive($post_id);

    return ($valid_post || $valid_archive);
  }

  private function find_sibling_pages() {
    $args = array(
      'sort_column' => 'menu_order',
    );

    if ($this->is_apex) {
      $args['child_of'] = $this->origin;
    } else {
      $args['parent'] = $this->origin;
    }

    $pages = get_pages($args);

    $pages = array_map(function($page) {
      return array(
        'title' => $page->post_title,
        'url' => get_permalink($page->ID)
      );
    }, get_pages($args));

    if ($this->include_apex) {
      $post = get_post($this->origin);

      array_unshift($pages, array(
        'title' => $post->post_title,
        'url' => get_permalink($post->ID)
      ));
    }

    return $pages;
  }

}

