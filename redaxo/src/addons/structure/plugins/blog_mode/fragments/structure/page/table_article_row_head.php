<?php
/**
 * Generates a header row from the keys of $article_actions.
 * If the same key is set in $article_actions_header, it's value will be used as substitute
 */
?>
<?php if ($this->article_actions instanceof rex_structure_action_group): ?>
    <?php if ($this->category instanceof rex_category && $this->category->getValue('article_order') != 'priority, name'): ?>
        <tr>
            <th class="structure-blog-mode-info" colspan="<?=count($this->article_actions->getAll());?>">[<?=$this->category->getName();?>] <?=rex_i18n::msg('blog_mode');?></th>
        </tr>
    <?php endif;?>
    <tr>
        <?php foreach ($this->article_actions->getAll() as $group_key => $group): ?>
            <th class="rex-table-<?=rex_string::normalize($group_key, '-');?>">
                <?php if ($this->article_actions->getGroup($group_key, true)): ?>
                    <div class="btn-group">
                        <?php foreach ($this->article_actions->getGroup($group_key, true) as $action): ?>
                            <?php if ($action instanceof rex_structure_action): ?>
                                <?= $action->get(); ?>
                            <?php endif;?>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <?=rex_i18n::msg('header_'.$group_key);?>
                <?php endif;?>
            </th>
        <?php endforeach;?>
    </tr>
<?php endif; ?>
