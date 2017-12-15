<?php

namespace JB\SPW;

use JB\SPW\Constants;
use JB\SPW\Helpers;

class SiblingPages extends \WP_Widget {

  public function __construct() {
    $args = array(
      Constants::$ID,
      Constants::$UI_NAME,
      array('description' => Constants::$DESCRIPTION)
    );

    parent::__construct(...$args);
  }

  public function widget($args, $instance) {
    echo $args['before_widget'];

    $show_parent = $instance['show_parent'] ? true : false;
    $override_ids = $this->parse_override_list($instance['override_list']);

    $list_options = array(
      'apex' => $show_parent,
      'override_ids' => $override_ids
    );

    $list = new PageList($list_options);
    $list->render();

    echo $args['after_widget'];
  }

  public function form($instance) {
    $template = Helpers::get_template_path('_widget_options.php');

    $title = $this->maybe($instance, 'title');
    $title_id = esc_attr($this->get_field_id('title'));
    $title_name = esc_attr($this->get_field_name('title'));

    $show_parent_id = esc_attr($this->get_field_id('show_parent'));
    $show_parent_name = esc_attr($this->get_field_name('show_parent'));
    $show_parent_checked = $instance['show_parent'] ? "checked" : "";

    $override_list = $this->maybe($instance, 'override_list');
    $override_list_id = esc_attr($this->get_field_id('override_list'));
    $override_list_name = esc_attr($this->get_field_name('override_list'));

    ob_start();
    include $template;
    $output = ob_get_contents();
    ob_end_clean();

    echo $output;
  }

  public function update($new_instance, $old_instance) {
    $instance = $old_instance;

    $instance['title'] = $this->maybe($new_instance, 'title');
    $instance['show_parent'] = $new_instance['show_parent'];
    $instance['override_list'] = $this->maybe($new_instance, 'override_list');

    return $instance;
  }

  /**
   * Returns the value if the key is set, or the empty string if not.
   */
  private function maybe($instance, $key) {
    $value = "";

    if (!empty($instance[$key])) {
      $value = $this->sanitize($instance[$key]);
    }

    return $value;
  }

  private function sanitize($unsafe_data) {
    $sanitized_data = "";

    if (is_array($unsafe_data)) {
      $sanitized_data = $this->sanitize_array($unsafe_data);
    } else {
      $sanitized_data = $this->sanitize_string($unsafe_data);
    }

    return $sanitized_data;
  }

  private function sanitize_array($unsafe_array) {
    $safe_array = array_map(function($value) {
      return trim(strip_tags($value));
    }, $unsafe_array);

    return $safe_array;
  }

  private function sanitize_string($unsafe_string) {
    return strip_tags($unsafe_string);
  }

  private function parse_override_list($list) {
    $parsed_list = null;

    if (!empty($list)) {
      $list = $this->split_and_trim_override_list($list);

      if ($this->all_valid_page_ids($list)) {
        $parsed_list = $list;
      }
    }

    return $parsed_list;
  }

  private function split_and_trim_override_list($csv_string) {
    return array_map('trim', explode(',', $csv_string));
  }

  private function all_valid_page_ids($array_of_ids) {
    $valid = true;

    foreach ($array_of_ids as $id) {
      if (!$this->valid_page_id($id)) {
        $valid = false;
      }
    }

    return $valid;
  }

  private function valid_page_id($post_id) {
    $valid_post = (get_post_status($post_id) ? true : false);
    $valid_archive = $this->valid_archive($post_id);

    return ($valid_post || $valid_archive);
  }

  private function valid_archive($post_id) {
    // Valid archives must be prefixed with 'archive:'
    $prefix_pattern = '/^archive:(.*)/';
    preg_match($prefix_pattern, $post_id, $matches);

    return(is_post_type_archive($matches[1]) ? true : false);
  }

}

