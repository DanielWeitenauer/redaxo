<?php
/**
 * Generates a structure table body row
 */

?>
<tr class="rex-structure rex-structure-article<?=$this->sql->hasValue('startarticle') && $this->sql->getValue('startarticle') == 1 ? ' rex-startarticle' : '';?>">
    <?php foreach ($this->columns as $column_key => $column): /** @var rex_structure_action_column $column */ ?>
        <td class="rex-table-<?=rex_string::normalize($column_key, '-');?>" data-title="<?=rex_i18n::msg('body_'.$column_key);?>">
            <div class="btn-group">
                <?php foreach ($column->getFields() as $field):  /** @var rex_structure_action_field $field */ ?>
                    <?= $field->get(); ?>
                <?php endforeach; ?>
            </div>
        </td>
    <?php endforeach; ?>
</tr>
