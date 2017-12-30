<?php
/**
 * @package redaxo5/structure
 */

// basic request vars
$category_id = rex_request('category_id', 'int');
$article_id = rex_request('article_id', 'int');
$clang = rex_request('clang', 'int');
$ctype = rex_request('ctype', 'int');

// additional request vars
$artstart = rex_request('artstart', 'int');
$catstart = rex_request('catstart', 'int');
$edit_id = rex_request('edit_id', 'int');
$function = rex_request('function', 'string');

$category_id = rex_category::get($category_id) ? $category_id : 0;
$article_id = rex_article::get($article_id) ? $article_id : 0;
$clang = rex_clang::exists($clang) ? $clang : rex_clang::getStartId();

// --------------------------------------------- Mountpoints
$mountpoints = rex::getUser()->getComplexPerm('structure')->getMountpoints();
if (count($mountpoints) == 1 && $category_id == 0) {
    // Nur ein Mointpoint -> Sprung in die Kategory
    $category_id = current($mountpoints);
}

// --------------------------------------------- Rechte prüfen
$KATPERM = rex::getUser()->getComplexPerm('structure')->hasCategoryPerm($category_id);

$stop = false;
if (rex_clang::count() > 1) {
    if (!rex::getUser()->getComplexPerm('clang')->hasPerm($clang)) {
        $stop = true;
        foreach (rex_clang::getAllIds() as $key) {
            if (rex::getUser()->getComplexPerm('clang')->hasPerm($key)) {
                $clang = $key;
                $stop = false;
                break;
            }
        }

        if ($stop) {
            echo rex_view::error('You have no permission to this area');
            exit;
        }
    }
} else {
    $clang = rex_clang::getStartId();
}

$context = new rex_context([
    'page' => 'structure',
    'category_id' => $category_id,
    'article_id' => $article_id,
    'clang' => $clang,
]);

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

// --------------------------------------------- API MESSAGES
echo rex_api_function::getMessage();

/**
 * KATEGORIE LISTE
 */
$cat_name = 'Homepage';
$category = rex_category::get($category_id, $clang);
if ($category) {
    $cat_name = $category->getName();
}

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
    $KAT->setQuery('SELECT COUNT(*) as rowCount FROM ' . rex::getTablePrefix() . 'article WHERE id IN (' . $parent_id . ') AND startarticle=1 AND clang_id=' . $clang . ' ORDER BY catname');
} else {
    $KAT->setQuery('SELECT COUNT(*) as rowCount FROM ' . rex::getTablePrefix() . 'article WHERE parent_id=' . $category_id . ' AND startarticle=1 AND clang_id=' . $clang . ' ORDER BY catpriority');
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
    $KAT->setQuery('SELECT * FROM ' . rex::getTablePrefix() . 'article WHERE id IN (' . $parent_id . ') AND startarticle=1 AND clang_id=' . $clang . ' ORDER BY catname LIMIT ' . $catPager->getCursor() . ',' . $catPager->getRowsPerPage());
} else {
    $KAT->setQuery('SELECT * FROM ' . rex::getTablePrefix() . 'article WHERE parent_id=' . $category_id . ' AND startarticle=1 AND clang_id=' . $clang . ' ORDER BY catpriority LIMIT ' . $catPager->getCursor() . ',' . $catPager->getRowsPerPage());
}

// --------------------- KATEGORIE LIST

$table_head = '';
$table_body = '';

// These Variables are passed to rows, columns and fields
$category_action_vars = [
    'category' => $category,
    'edit_id' => $category_id, // This key is overwritten in the loop
    'sql' => $KAT,
    'pager' => $catPager,
    'clang' => $clang,
    'context' => $context,
    'url_params' => [
        'artstart' => $artstart,
        'catstart' => $catstart,
    ],
];

// Predefine columns
$category_row = new rex_structure_action_row($category_action_vars);
$category_row['icon'] = new rex_structure_action_column();
$category_row['id'] = new rex_structure_action_column();
$category_row['category'] = new rex_structure_action_column();
$category_row['priority'] = new rex_structure_action_column();
$category_row['status'] = new rex_structure_action_column();
$category_row['action'] = new rex_structure_action_column();

// Add table head actions
$category_row_icon = new rex_structure_category_add($category_action_vars);
$category_row_icon
    ->setVar('hide_label', true)
    ->setVar('hide_border', true);
$category_row['icon']->setHead($category_row_icon);

// Add table body actions and generate body output
do {
    $i_category_id = $KAT->getRows() ? $KAT->getValue('id') : 0;
    // Overwrite id of active category with the id of the category currently looped over
    // this way all action classes and fragments can use the same variable names
    $category_action_vars['edit_id'] = $i_category_id;

    $category_row['icon']
        ->setField('category_icon', new rex_structure_category_icon($category_action_vars));
    $category_row['id']
        ->setField('category_id', new rex_structure_category_id($category_action_vars));
    $category_row['category']
        ->setField('category_name', new rex_structure_category_name($category_action_vars));
    $category_row['priority']
        ->setField('category_priority', new rex_structure_category_priority($category_action_vars));
    $category_row['status']
        ->setField('category_status', new rex_structure_category_status($category_action_vars));
    $category_row['action']
        ->setField('category_edit', new rex_structure_category_edit($category_action_vars))
        ->setField('category_delete', new rex_structure_category_delete($category_action_vars));

    // EXTENSION POINT to manipulate $category_row
    $category_row = rex_extension::registerPoint(new rex_extension_point('PAGE_STRUCTURE_CATEGORY_ACTIONS', $category_row, [
        'action_vars' => $category_action_vars,
    ]));

    if ($i_category_id && ($KATPERM || rex::getUser()->getComplexPerm('structure')->hasCategoryPerm($i_category_id))) {
        $table_body .= $category_row->getFragment('structure/table_category_row_body.php');
    }

    $KAT->next();
} while ($KAT->hasNext());

// The head is generated after the body, in case any changes where made via extension point
$table_head .= $category_row->getFragment('structure/table_category_row_head.php');

// Add link to parent category
if ($category instanceof rex_category) {
    $table_body = $category_row->getFragment('structure/table_category_parent_row_body.php').$table_body;
}

$fragment = new rex_fragment();
$fragment->setVar('table_head', $table_head, false);
$fragment->setVar('table_body', $table_body, false);
$table = $fragment->parse('structure/table.php');

$fragment = new rex_fragment();
$fragment->setVar('heading', rex_i18n::msg('structure_categories_caption', $cat_name), false);
$fragment->setVar('content', $table, false);
echo $fragment->parse('core/page/section.php');

/**
 * ARTIKEL LISTE
 */
if ($category_id > 0 || ($category_id == 0 && !rex::getUser()->getComplexPerm('structure')->hasMountpoints())) {
    // EXTENSION POINT to manipulate $article_order
    $article_order = rex_extension::registerPoint(new rex_extension_point('PAGE_STRUCTURE_ARTICLE_ORDER', 'priority, name', [
        'category_id' => $category_id,
        'clang_id' => $clang,
    ]));

    // ---------- COUNT DATA
    $sql = rex_sql::factory();
    //$sql->setDebug();
    $sql->setQuery('SELECT COUNT(*) as artCount
                FROM
                    ' . rex::getTablePrefix() . 'article
                WHERE
                    ((parent_id=' . $category_id . ' AND startarticle=0) OR (id=' . $category_id . ' AND startarticle=1))
                    AND clang_id=' . $clang . '
                ORDER BY
                    '.$article_order
    );

    // --------------------- ADD PAGINATION
    $artPager = new rex_pager(30, 'artstart');
    $artPager->setRowCount($sql->getValue('artCount'));
    $artFragment = new rex_fragment();
    $artFragment->setVar('urlprovider', $context);
    $artFragment->setVar('pager', $artPager);
    echo $artFragment->parse('core/navigations/pagination.php');

    // ---------- READ DATA
    $sql->setQuery('SELECT *
                FROM
                    ' . rex::getTablePrefix() . 'article
                WHERE
                    ((parent_id=' . $category_id . ' AND startarticle=0) OR (id=' . $category_id . ' AND startarticle=1))
                    AND clang_id=' . $clang . '
                ORDER BY
                    '.$article_order.'
                LIMIT ' . $artPager->getCursor() . ',' . $artPager->getRowsPerPage());

    // --------------------- ARTIKEL LIST

    $table_head = '';
    $table_body = '';

    // These params are passed to the structure actions and infos
    $article_action_vars = [
        'category' => $category,
        'edit_id' => $category_id, // This key is overwritten in the loop
        'sql' => $sql,
        'pager' => $artPager,
        'clang' => $clang,
        'context' => $context,
        'url_params' => [
            'artstart' => $artstart,
            'catstart' => $catstart,
        ],
    ];

    // Predefine columns
    $article_row = new rex_structure_action_row($article_action_vars);
    $article_row['icon'] = new rex_structure_action_column();
    $article_row['id'] = new rex_structure_action_column();
    $article_row['article'] = new rex_structure_action_column();
    $article_row['template'] = new rex_structure_action_column();
    $article_row['date'] = new rex_structure_action_column();
    $article_row['priority'] = new rex_structure_action_column();
    $article_row['status'] = new rex_structure_action_column();
    $article_row['action'] = new rex_structure_action_column();

    // Add table head actions
    $article_row_icon = new rex_structure_article_add($article_action_vars);
    $article_row_icon
        ->setVar('hide_label', true)
        ->setVar('hide_border', true);
    $article_row['icon']->setHead($article_row_icon);

    // Add table body actions and generate body output
    do {
        // Overwrite id of active category with the id of the article currently looped over
        // this way all action classes and fragments can use the same variable names
        $article_action_vars['edit_id'] = $sql->getValue('id');

        $article_row['icon']
            ->setField('article_icon', new rex_structure_article_icon($article_action_vars));
        $article_row['id']
            ->setField('article_id', new rex_structure_article_id($article_action_vars));
        $article_row['article']
            ->setField('article_name', new rex_structure_article_name($article_action_vars));
        $article_row['template']
            ->setField('article_template', new rex_structure_article_template($article_action_vars));
        $article_row['date']
            ->setField('article_create_date', new rex_structure_article_create_date($article_action_vars));
        $article_row['priority']
            ->setField('article_priority', new rex_structure_article_priority($article_action_vars));
        $article_row['status']
            ->setField('article_status', new rex_structure_article_status($article_action_vars));
        $article_row['action']
            ->setField('article_edit', new rex_structure_article_edit($article_action_vars))
            ->setField('article_delete', new rex_structure_article_delete($article_action_vars));

        // EXTENSION POINT to manipulate $article_row
        $article_row = rex_extension::registerPoint(new rex_extension_point('PAGE_STRUCTURE_ARTICLE_ACTIONS', $article_row, [
            'action_vars' => $article_action_vars,
        ]));

        $table_body .= $article_row->getFragment('structure/table_article_row_body.php');

        $sql->next();
    } while ($sql->hasNext());

    // Table head
    $table_head .= $article_row->getFragment('structure/table_article_row_head.php');

    $fragment = new rex_fragment();
    $fragment->setVar('table_head', $table_head, false);
    $fragment->setVar('table_body', $table_body, false);
    $table = $fragment->parse('structure/table.php');

    $fragment = new rex_fragment();
    $fragment->setVar('heading', rex_i18n::msg('structure_articles_caption', $cat_name), false);
    $fragment->setVar('content', $table, false);
    echo $fragment->parse('core/page/section.php');
}
