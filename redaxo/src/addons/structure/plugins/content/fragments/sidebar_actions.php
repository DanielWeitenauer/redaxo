<?php
$output_groups = [];
foreach ($this->article_actions as $article_action_group_key => $article_action_group) {
    $is_horizontal = false;
    $output_item = '';
    foreach ($article_action_group as $article_action_key => $article_action) {
        if ($article_action instanceof rex_fragment && method_exists($article_action, 'get')) {
            if (rex_i18n::hasMsg($article_action_key)) {
                $output_item .= '<dt>'.rex_i18n::msg($article_action_key).'</dt><dd>'.$article_action->get().'</dd>';
                $is_horizontal = true;
            } else {
                $output_item .= $article_action->get();
            }
        }
    }
    if ($is_horizontal) {
        $output_item = '<dl class="dl-horizontal text-left">'.$output_item.'</dl>';
    } else {
        $output_item = '<div class="btn-group">'.$output_item.'</div>';
    }

    $output_groups[$article_action_group_key] = $output_item;
}
?>
<?php foreach ($output_groups as $ouput_key => $output_item): ?>
    <dl class="rex-table-<?=rex_string::normalize($ouput_key, '-');?>">
        <dt><?=rex_i18n::msg($ouput_key);?></dt>
        <dd>
            <?=$output_item;?>
        </dd>
    </dl>
<?php endforeach; ?>
