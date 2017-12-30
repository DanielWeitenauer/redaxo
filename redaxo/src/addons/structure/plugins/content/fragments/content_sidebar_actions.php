<?php
/**
 * Generates a sidebar block
 */

$output_groups = [];
/** @var rex_structure_action_column $column */
foreach ($this->columns as $column_key => $column) {
    $is_horizontal = false;
    $output_item = '';

    if ($column->hasFields()) {
        /** @var rex_structure_action_field $field */
        foreach ($column->getFields() as $field_key => $field) {
            if (rex_i18n::hasMsg($field_key)) {
                $output_item .= '<dt>'.rex_i18n::msg($field_key).'</dt><dd>'.$field->get().'</dd>';
                $is_horizontal = true;
            } else {
                $output_item .= $field->get();
            }
        }
    }

    if ($output_item) {
        if ($is_horizontal) {
            $output_item = '<dl class="dl-horizontal text-left">'.$output_item.'</dl>';
        } elseif ($column->getVar('group')) {
            $output_item = '<div class="btn-group">'.$output_item.'</div>';
        }
    }

    if ($output_item) {
        $output_groups[$column_key] = $output_item;
    }
}
?>
<?php foreach ($output_groups as $output_key => $output_item): ?>
    <dl class="rex-table-<?=rex_string::normalize($output_key, '-');?>">
        <dt><?=rex_i18n::msg($output_key);?></dt>
        <dd>
            <?=$output_item;?>
        </dd>
    </dl>
<?php endforeach; ?>
