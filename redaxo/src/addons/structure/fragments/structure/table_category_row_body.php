<?php
/**
 * Generates a structure table body row
 */

$output_groups = [];

/** @var rex_structure_action_column $column */
foreach ($this->columns as $column_key => $column) {
    $output_item = '';

    if ($column->hasFields()) {
        foreach ($column->getFields() as $field_key => $field) {
            if ($field instanceof rex_structure_action_field) {
                if (rex_i18n::hasMsg($field_key)) {
                    $output_item .= '<dt>'.rex_i18n::msg($field_key).'</dt><dd>'.$field->get().'</dd>';
                } else {
                    $output_item .= $field->get();
                }
            }
        }
    }

    if ($output_item) {
        $output_item = '<div class="btn-group">'.$output_item.'</div>';
    } else {
        $output_item = '&nbsp;';
    }

    $output_groups[$column_key] = $output_item;
}

?>
<tr class="rex-structure rex-structure-category">
    <?php foreach ($output_groups as $output_key => $output_item): ?>
        <td class="rex-table-<?=rex_string::normalize($output_key, '-');?>" data-title="<?=rex_i18n::msg('header_'.$output_key);?>">
            <?=$output_item;?>
        </td>
    <?php endforeach; ?>
</tr>
