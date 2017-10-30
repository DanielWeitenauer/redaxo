<tr>
    <th class="rex-table-icon"><?=$this->table_icon;?></th>
    <?php foreach ($this->article_actions as $article_action_group_key => $article_action_group): ?>
        <?php if ($article_action_group_key == 'icon') continue;?>
        <th class="rex-table-<?=rex_string::normalize($article_action_group_key, '-');?>"><?=rex_i18n::msg('header_'.$article_action_group_key);?></th>
    <?php endforeach;?>
</tr>

