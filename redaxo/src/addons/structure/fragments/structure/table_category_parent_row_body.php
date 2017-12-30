<?php
/**
 * Generates a row with a link to the parent category
 */

$category = rex_category::get($this->edit_id);
$parent_url = $category instanceof rex_category ? $this->context->getUrl(['category_id' => $category->getParentId()]) : '';

?>
<tr class="rex-structure rex-structure-category rex-structure-category-to-parent">
    <?php foreach ($this->columns as $group_key => $group): ?>
        <td class="rex-table-<?=rex_string::normalize($group_key, '-');?>">
            <?php
            switch ($group_key) {
                case 'icon':
                    echo '<i class="rex-icon rex-icon-open-category"></i>';
                    break;

                case 'id':
                    echo '-';
                    break;

                case 'category':
                    echo '<a href="'.$parent_url.'">..</a>';
                    break;

                default:
                    echo '&nbsp;';
            }
            ?>
        </td>
    <?php endforeach;?>
</tr>
