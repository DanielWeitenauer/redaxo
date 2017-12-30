<?php
/**
 * Generates a structure table head row
 */

?>
<tr>
    <?php foreach ($this->columns as $column_key => $column): /** @var rex_structure_action_column $column */?>
        <th class="rex-table-<?=rex_string::normalize($column_key, '-');?>">
            <?php if ($column->hasHead()): ?>
                <?= $column->getHead()->get(); ?>
            <?php else: ?>
                <?=rex_i18n::msg('header_'.$column_key);?>
            <?php endif;?>
        </th>
    <?php endforeach;?>
</tr>
