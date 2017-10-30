<?php
/**
 * Each action must be an descendant of rex_fragment and implement the method get()
 * to return a string which is retrieved in this fragment
 */
?>
<tr class="rex-structure rex-structure-article<?=$this->table_classes;?>">
    <?php foreach ($this->article_actions as $article_action_group_key => $article_action_group): ?>
        <td class="rex-table-<?=rex_string::normalize($article_action_group_key, '-');?>" data-title="<?=rex_i18n::msg('header_'.$article_action_group_key);?>">
            <?php foreach ($article_action_group as $article_action): ?>
                <?php if ($article_action instanceof rex_fragment && method_exists($article_action, 'get')): ?>
                    <?= $article_action->get(); ?>
                <?php endif;?>
            <?php endforeach; ?>
        </td>
    <?php endforeach; ?>
</tr>
