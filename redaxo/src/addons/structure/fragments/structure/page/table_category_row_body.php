<tr class="rex-structure rex-structure-category">
    <?php foreach ($this->category_actions as $group_key => $group): ?>
        <td class="rex-table-<?=rex_string::normalize($group_key, '-');?>" data-title="<?=rex_i18n::msg('header_'.$group_key);?>">
            <div class="btn-group">
                <?php foreach ($group as $category_action): ?>
                    <?php if ($category_action instanceof rex_fragment && method_exists($category_action, 'get')): ?>
                        <?= $category_action->get(); ?>
                    <?php endif;?>
                <?php endforeach; ?>
            </div>
        </td>
    <?php endforeach; ?>
</tr>
