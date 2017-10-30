<?php
$output_groups = [];
foreach ($this->category_actions as $category_action_group_key => $category_action_group) {
    $output_item = '';
    foreach ($category_action_group as $category_action_key => $category_action) {
        if ($category_action instanceof rex_fragment && method_exists($category_action, 'get')) {
            if (rex_i18n::hasMsg($category_action_key)) {
                $output_item .= '<dt>'.rex_i18n::msg($category_action_key).'</dt><dd>'.$category_action->get().'</dd>';
            } else {
                $output_item .= $category_action->get();
            }
        }
    }

    if ($output_item) {
        $output_item = '<div class="btn-group">'.$output_item.'</div>';
    } else {
        $output_item = '&nbsp;';
    }

    $output_groups[$category_action_group_key] = $output_item;
}
?>
<tr class="rex-structure rex-structure-category">
    <?php foreach ($output_groups as $output_key => $output_item): ?>
        <td class="rex-table-<?=rex_string::normalize($output_key, '-');?>" data-title="<?=rex_i18n::msg('header_'.$output_key);?>">
            <?=$output_item;?>
        </td>
    <?php endforeach; ?>
</tr>

