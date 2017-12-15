<p>
  <label for="<?php echo $title_id ?>">Title:</label>
  <input type="text" class="widefat" id="<?php echo $title_id ?>"
    name="<?php echo $title_name ?>" value="<?php echo $title ?>">
</p>

<p>
  <input type="checkbox" class="checkbox" id="<?php echo $show_parent_id ?>"
    name="<?php echo $show_parent_name ?>"<?php echo $show_parent_checked ?>>
  <label for="<?php echo $show_parent_id ?>">Show parent page in list</label>
<p>

<p>
  <label for="<?php echo $override_list_id ?>">Override with pages:</label>
  <input type="text" class="widefat" id="<?php echo $override_list_id ?>"
    name="<?php echo $override_list_name ?>" value="<?php echo $override_list ?>">
</p>
