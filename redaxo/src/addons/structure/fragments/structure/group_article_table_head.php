<tr>
    <?php foreach ($this->group as $field_key => $field): ?>
        <th class="rex-table-<?=rex_string::normalize($field_key, '-');?>">
            <?php if ($field instanceof rex_structure_field): ?>
                <?= $field->getField(); ?>
            <?php elseif (is_array($field)): ?>
                <div class="btn-group">
                    <?php foreach ($field as $sub_field): ?>
                        <?php if ($sub_field instanceof rex_structure_field): ?>
                            <?= $sub_field->getField(); ?>
                        <?php endif;?>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <?=$field_key;?>
            <?php endif;?>
        </th>
    <?php endforeach;?>
</tr>
