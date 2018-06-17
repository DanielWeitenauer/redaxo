<tr class="rex-structure rex-structure-article<?=$this->is_startarticle ? ' rex-startarticle' : '';?>">
    <?php foreach ($this->group as $field_key => $field): ?>
        <td class="rex-table-<?=rex_string::normalize($field_key, '-');?>"<?=rex_i18n::hasMsg('header_'.$field_key) ? ' data-title="'.rex_i18n::msg('header_'.$field_key).'"' : '';?>>
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
            <?php endif;?>
        </td>
    <?php endforeach; ?>
</tr>
