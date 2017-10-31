<?php
/**
 * Generates a header row from the keys of $article_actions.
 * If the same key is set in $article_actions_header, it's value will be used as substitute
 */
?>
<tr>
    <?php foreach ($this->article_actions as $action_group_key => $action_group): ?>
        <th class="rex-table-<?=rex_string::normalize($action_group_key, '-');?>">
            <?php if (isset($this->article_actions_header[$action_group_key])): ?>
                <div class="btn-group">
                    <?php foreach ($this->article_actions_header[$action_group_key] as $action): ?>
                        <?php if ($action instanceof rex_fragment && method_exists($action, 'get')): ?>
                            <?= $action->get(); ?>
                        <?php endif;?>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <?=rex_i18n::msg('header_'.$action_group_key);?>
            <?php endif;?>
        </th>
    <?php endforeach;?>
</tr>

