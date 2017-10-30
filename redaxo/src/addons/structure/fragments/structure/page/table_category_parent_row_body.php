<tr class="rex-structure rex-structure-category">
    <?php foreach ($this->category_actions as $category_action_group_key => $category_action_group): ?>
        <td class="rex-table-<?=rex_string::normalize($category_action_group_key, '-');?>">
            <?php
            switch ($category_action_group_key) {
                case 'icon':
                    echo '<i class="rex-icon rex-icon-open-category"></i>';
                    break;

                case 'id':
                    echo '-';
                    break;

                case 'category':
                    echo '<a href="'.$this->parent_url.'">..</a>';
                    break;

                default:
                    echo '&nbsp;';
            }
            ?>
        </td>
        <?php endforeach;?>
</tr>
