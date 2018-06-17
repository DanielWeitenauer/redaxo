<?php

/**
 * @package redaxo5
 */

$data_provider = rex_structure_data_provider::factory();

// basic request vars
$category_id = $data_provider->getCategoryId();
$article_id = $data_provider->getArticleId();
$clang = $data_provider->getClangId();
$ctype = $data_provider->getClangId();

// additional request vars
$artstart = $data_provider->getArtStart();
$catstart = $data_provider->getCatStart();
$edit_id = $data_provider->getEditId();
$function = $data_provider->getFunction();

$info = '';
$warning = '';

// --------------------------------------------- Mountpoints

$mountpoints = $data_provider->getMountpoints();

// --------------------------------------------- Rechte prüfen
$KATPERM = rex::getUser()->getComplexPerm('structure')->hasCategoryPerm($category_id);

$context = $data_provider->getContext();

// --------------------- Extension Point
echo rex_extension::registerPoint(new rex_extension_point('PAGE_STRUCTURE_HEADER_PRE', '', [
    'context' => $context,
]));

// --------------------------------------------- TITLE
echo rex_view::title(rex_i18n::msg('title_structure'));

// --------------------------------------------- Languages
echo rex_view::clangSwitchAsButtons($context);

// --------------------------------------------- Path
require __DIR__ . '/../functions/function_rex_category.php';

// -------------- STATUS_TYPE Map
$catStatusTypes = rex_category_service::statusTypes();
$artStatusTypes = rex_article_service::statusTypes();

// --------------------------------------------- API MESSAGES
echo rex_api_function::getMessage();

// --------------------------------------------- KATEGORIE LISTE
$cat_name = rex_i18n::msg('root_level');
$category = rex_category::get($category_id, $clang);
if ($category) {
    $cat_name = $category->getName();
}

$add_category = '';
if ($KATPERM) {
    $add_category = '<a href="' . $context->getUrl(['function' => 'add_cat', 'catstart' => $catstart]) . '"' . rex::getAccesskey(rex_i18n::msg('add_category'), 'add') . '><i class="rex-icon rex-icon-add-category"></i></a>';
}

$data_colspan = 5;

// --------------------- Extension Point
echo rex_extension::registerPoint(new rex_extension_point('PAGE_STRUCTURE_HEADER', '', [
    'category_id' => $category_id,
    'clang' => $clang,
]));

// --------------------- SEARCH BAR
//require_once $this->getPath('functions/function_rex_searchbar.php');
//echo rex_structure_searchbar($context);

// --------------------- COUNT CATEGORY ROWS

$KAT = rex_sql::factory();
// $KAT->setDebug();
if (count($mountpoints) > 0 && $category_id == 0) {
    $parent_id = implode(',', $mountpoints);
    $KAT->setQuery('SELECT COUNT(*) as rowCount FROM ' . rex::getTablePrefix() . 'article WHERE id IN (' . $parent_id . ') AND startarticle=1 AND clang_id=' . $clang);
} else {
    $KAT->setQuery('SELECT COUNT(*) as rowCount FROM ' . rex::getTablePrefix() . 'article WHERE parent_id=' . $category_id . ' AND startarticle=1 AND clang_id=' . $clang);
}

// --------------------- ADD PAGINATION

$catPager = new rex_pager(30, 'catstart');
$catPager->setRowCount($KAT->getValue('rowCount'));
$catFragment = new rex_fragment();
$catFragment->setVar('urlprovider', $context);
$catFragment->setVar('pager', $catPager);
echo $catFragment->parse('core/navigations/pagination.php');

// --------------------- GET THE DATA

if (count($mountpoints) > 0 && $category_id == 0) {
    $parent_id = implode(',', $mountpoints);

    $KAT->setQuery('SELECT parent_id FROM ' . rex::getTable('article') . ' WHERE id IN (' . $parent_id . ') GROUP BY parent_id');
    $orderBy = $KAT->getRows() > 1 ? 'catname' : 'catpriority';

    $KAT->setQuery('SELECT * FROM ' . rex::getTablePrefix() . 'article WHERE id IN (' . $parent_id . ') AND startarticle=1 AND clang_id=' . $clang . ' ORDER BY ' . $orderBy . ' LIMIT ' . $catPager->getCursor() . ',' . $catPager->getRowsPerPage());
} else {
    $KAT->setQuery('SELECT * FROM ' . rex::getTablePrefix() . 'article WHERE parent_id=' . $category_id . ' AND startarticle=1 AND clang_id=' . $clang . ' ORDER BY catpriority LIMIT ' . $catPager->getCursor() . ',' . $catPager->getRowsPerPage());
}

$echo = '';
// ---------- INLINE THE EDIT/ADD FORM
if ($function == 'add_cat' || $function == 'edit_cat') {
    $echo .= '
    <form action="' . $context->getUrl(['catstart' => $catstart]) . '" method="post">
        <fieldset>

            <input type="hidden" name="edit_id" value="' . $edit_id . '" />';
}

// --------------------- PRINT CATS/SUBCATS
$echo .= '
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th class="rex-table-icon">' . $add_category . '</th>
                        <th class="rex-table-id">' . rex_i18n::msg('header_id') . '</th>
                        <th>' . rex_i18n::msg('header_category') . '</th>
                        <th class="rex-table-priority">' . rex_i18n::msg('header_priority') . '</th>
                        <th class="rex-table-action" colspan="3">' . rex_i18n::msg('header_status') . '</th>
                    </tr>
                </thead>
                <tbody>';
if ($category_id != 0 && ($category = rex_category::get($category_id))) {
    $echo .= '  <tr>
                    <td class="rex-table-icon"><i class="rex-icon rex-icon-open-category"></i></td>
                    <td class="rex-table-id">-</td>
                    <td data-title="' . rex_i18n::msg('header_category') . '"><a href="' . $context->getUrl(['category_id' => $category->getParentId()]) . '">..</a></td>
                    <td class="rex-table-priority" data-title="' . rex_i18n::msg('header_priority') . '">&nbsp;</td>
                    <td class="rex-table-action" colspan="3">&nbsp;</td>
                </tr>';
}

// --------------------- KATEGORIE ADD FORM

if ($function == 'add_cat' && $KATPERM) {
    $meta_buttons = rex_extension::registerPoint(new rex_extension_point('CAT_FORM_BUTTONS', '', [
        'id' => $category_id,
        'clang' => $clang,
    ]));
    $add_buttons = rex_api_category_add::getHiddenFields().'
        <input type="hidden" name="parent-category-id" value="' . $category_id . '" />
        <button class="btn btn-save" type="submit" name="category-add-button"' . rex::getAccesskey(rex_i18n::msg('add_category'), 'save') . '>' . rex_i18n::msg('add_category') . '</button>';

    $class = 'mark';

    $echo .= '
                <tr class="' . $class . '">
                    <td class="rex-table-icon"><i class="rex-icon rex-icon-category"></i></td>
                    <td class="rex-table-id" data-title="' . rex_i18n::msg('header_id') . '">-</td>
                    <td data-title="' . rex_i18n::msg('header_category') . '"><input class="form-control" type="text" name="category-name" class="rex-js-autofocus" autofocus /></td>
                    <td class="rex-table-priority" data-title="' . rex_i18n::msg('header_priority') . '"><input class="form-control" type="text" name="category-position" value="' . ($catPager->getRowCount() + 1) . '" /></td>
                    <td class="rex-table-action">' . $meta_buttons . '</td>
                    <td class="rex-table-action" colspan="2">' . $add_buttons . '</td>
                </tr>';

    // ----- EXTENSION POINT
    $echo .= rex_extension::registerPoint(new rex_extension_point('CAT_FORM_ADD', '', [
        'id' => $category_id,
        'clang' => $clang,
        'data_colspan' => ($data_colspan + 1),
    ]));
}

// --------------------- KATEGORIE LIST
if ($KAT->getRows() > 0) {
    for ($i = 0; $i < $KAT->getRows(); ++$i) {
        // These params are passed to the structure fields
        $category_provider = rex_structure_data_provider::factory();
        $category_provider->setSql($KAT);

        $kat_status = rex_structure_field_category_status::factory($category_provider)->getField();
        $category_delete = rex_structure_field_category_delete::factory($category_provider)->getField();
        $category_edit = rex_structure_field_category_edit::factory($category_provider)->getField();
        $category_name = rex_structure_field_category_name::factory($category_provider)->getField();
        $category_icon = rex_structure_field_category_icon::factory($category_provider)->getField();
        $category_id_field = rex_structure_field_category_id::factory($category_provider)->getField();
        $category_priority = rex_structure_field_category_priority::factory($category_provider)->getField();

        $echo .= '
            <tr>
                <td class="rex-table-icon">'.$category_icon.'</td>
                <td class="rex-table-id" data-title="' . rex_i18n::msg('header_id') . '">'.$category_id_field.'</td>
                <td data-title="'.rex_i18n::msg('header_category').'">'.$category_name.'</td>
                <td class="rex-table-priority" data-title="'.rex_i18n::msg('header_priority').'">'.$category_priority.'</td>
                <td class="rex-table-action">'.$category_edit.'</td>
                <td class="rex-table-action">'.$category_delete.'</td>
                <td class="rex-table-action">'.$kat_status.'</td>
            </tr>';

        $KAT->next();
    }
} else {
    $echo .= '
                <tr>
                    <td>&nbsp;</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>';
}

$echo .= '
            </tbody>
        </table>';

if ($function == 'add_cat' || $function == 'edit_cat') {
    $echo .= '
    </fieldset>
</form>';
}

$heading = rex_i18n::msg('structure_categories_caption', $cat_name);
if ($category_id == 0) {
    $heading = rex_i18n::msg('structure_root_level_categories_caption');
}
$fragment = new rex_fragment();
$fragment->setVar('heading', $heading, false);
$fragment->setVar('content', $echo, false);
echo $fragment->parse('core/page/section.php');

// --------------------------------------------- ARTIKEL LISTE

$echo = '';

// --------------------- READ TEMPLATES

if ($category_id > 0 || ($category_id == 0 && !rex::getUser()->getComplexPerm('structure')->hasMountpoints())) {
    $withTemplates = $this->getPlugin('content')->isAvailable();
    $tmpl_head = '';
    if ($withTemplates) {
        $template_select = new rex_select();
        $template_select->setName('template_id');
        $template_select->setSize(1);
        $template_select->setStyle('class="form-control"');

        $templates = rex_template::getTemplatesForCategory($category_id);
        if (count($templates) > 0) {
            foreach ($templates as $t_id => $t_name) {
                $template_select->addOption(rex_i18n::translate($t_name, false), $t_id);
                $TEMPLATE_NAME[$t_id] = rex_i18n::translate($t_name);
            }
        } else {
            $template_select->addOption(rex_i18n::msg('option_no_template'), '0');
        }
        $TEMPLATE_NAME[0] = rex_i18n::msg('template_default_name');
        $tmpl_head = '<th>' . rex_i18n::msg('header_template') . '</th>';
    }

    // --------------------- ARTIKEL LIST

    // ---------- COUNT DATA
    $sql = rex_sql::factory();
    // $sql->setDebug();
    $sql->setQuery('SELECT COUNT(*) as artCount
                FROM
                    ' . rex::getTablePrefix() . 'article
                WHERE
                    ((parent_id=' . $category_id . ' AND startarticle=0) OR (id=' . $category_id . ' AND startarticle=1))
                    AND clang_id=' . $clang . '
                ORDER BY
                    priority, name');

    // --------------------- ADD PAGINATION

    $article_provider = rex_structure_data_provider::factory();
    $article_provider->setSql($sql);
    $artPager = $article_provider->getArtPager();
    $artFragment = new rex_fragment();
    $artFragment->setVar('urlprovider', $context);
    $artFragment->setVar('pager', $artPager);
    echo $artFragment->parse('core/navigations/pagination.php');

    $art_add_link = rex_structure_field_article_add::factory($article_provider)->setHiddenLabel(true)->getField();

    // ---------- READ DATA
    $sql->setQuery('SELECT *
                FROM
                    ' . rex::getTablePrefix() . 'article
                WHERE
                    ((parent_id=' . $category_id . ' AND startarticle=0) OR (id=' . $category_id . ' AND startarticle=1))
                    AND clang_id=' . $clang . '
                ORDER BY
                    priority, name
                LIMIT ' . $artPager->getCursor() . ',' . $artPager->getRowsPerPage());

    // ---------- INLINE THE EDIT/ADD FORM
    if ($function == 'add_art' || $function == 'edit_art') {
        $echo .= '
        <form action="' . $context->getUrl(['artstart' => $artstart]) . '" method="post">
            <fieldset>';
    }

    // ----------- PRINT OUT THE ARTICLES

    $echo .= '
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th class="rex-table-icon">'.$art_add_link.'</th>
                        <th class="rex-table-id">' . rex_i18n::msg('header_id') . '</th>
                        <th>' . rex_i18n::msg('header_article_name') . '</th>
                        ' . $tmpl_head . '
                        <th>' . rex_i18n::msg('header_date') . '</th>
                        <th class="rex-table-priority">' . rex_i18n::msg('header_priority') . '</th>
                        <th class="rex-table-action" colspan="3">' . rex_i18n::msg('header_status') . '</th>
                    </tr>
                </thead>
                ';

    // tbody nur anzeigen, wenn später auch inhalt drinnen stehen wird
    if ($sql->getRows() > 0 || $function == 'add_art') {
        $echo .= '<tbody>
                    ';
    }

    // --------------------- ARTIKEL LIST

    for ($i = 0; $i < $sql->getRows(); ++$i) {
        $class_startarticle = '';
        if ($sql->getValue('startarticle') == 1) {
            $class_startarticle = ' rex-startarticle';
        }

        // These params are passed to the structure fields
        $article_provider
            ->setEditId($sql->getValue('id'))
            ->setSql($sql);

        $article_status = rex_structure_field_article_status::factory($article_provider)->getField();
        $article_delete = rex_structure_field_article_delete::factory($article_provider)->getField();
        $article_edit = rex_structure_field_article_edit::factory($article_provider)->getField();
        $article_name = rex_structure_field_article_name::factory($article_provider)->getField();
        $article_icon = rex_structure_field_article_icon::factory($article_provider)->getField();
        $article_id_field = rex_structure_field_article_id::factory($article_provider)->getField();
        $article_template = rex_structure_field_article_template::factory($article_provider)->getField();
        $article_create_date = rex_structure_field_article_create_date::factory($article_provider)->getField();
        $article_priority = rex_structure_field_article_priority::factory($article_provider)->getField();

        $echo .= '
            <tr' . (($class_startarticle != '') ? ' class="' . trim($class_startarticle) . '"' : '') . '>
                <td class="rex-table-icon">'.$article_icon.'</td>
                <td class="rex-table-id" data-title="' . rex_i18n::msg('header_id') . '">'.$article_id_field.'</td>
                <td data-title="' . rex_i18n::msg('header_article_name') . '">'.$article_name.'</td>
                <td data-title="' . rex_i18n::msg('header_template') . '">'.$article_template.'</td>
                <td data-title="' . rex_i18n::msg('header_date') . '">'.$article_create_date.'</td>
                <td class="rex-table-priority" data-title="' . rex_i18n::msg('header_priority') . '">'.$article_priority.'</td>
                <td class="rex-table-action">'.$article_edit.'</td>
                <td class="rex-table-action">'.$article_delete.'</td>
                <td class="rex-table-action">'.$article_status.'</td>
            </tr>
        ';

        $sql->next();
    }

    // tbody nur anzeigen, wenn später auch inhalt drinnen stehen wird
    if ($sql->getRows() > 0 || $function == 'add_art') {
        $echo .= '
                </tbody>';
    }

    $echo .= '
            </table>';

    if ($function == 'add_art' || $function == 'edit_art') {
        $echo .= '
        </fieldset>
    </form>';
    }
}

$heading = rex_i18n::msg('structure_articles_caption', $cat_name);
if ($category_id == 0) {
    $heading = rex_i18n::msg('structure_root_level_articles_caption');
}
$fragment = new rex_fragment();
$fragment->setVar('heading', $heading, false);
$fragment->setVar('content', $echo, false);
echo $fragment->parse('core/page/section.php');
