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

// These variables are passed to rows, columns and fields
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

// The category action row persists the initial edit_id
$category_row = rex_structure_action_row::factory($category_action_vars);

// The category action columns get an update of the edit_id in the loop
$category_columns = [
    'icon' => rex_structure_action_column::factory($category_action_vars),
    'id' => rex_structure_action_column::factory($category_action_vars),
    'category' => rex_structure_action_column::factory($category_action_vars),
    'priority' => rex_structure_action_column::factory($category_action_vars),
    'status' => rex_structure_action_column::factory($category_action_vars),
    'action' => rex_structure_action_column::factory($category_action_vars),
];
// To column heads the initial edit_id is passed, the fields will be updated
$category_columns['icon']
    ->setHead(rex_structure_category_add::factory($category_action_vars)
        ->setVar('hide_label', true)
        ->setVar('hide_border', true)
    )
    ->setField('category_icon', rex_structure_category_icon::factory($category_action_vars));
$category_columns['id']
    ->setField('category_id', rex_structure_category_id::factory($category_action_vars));
$category_columns['category']
    ->setField('category_name', rex_structure_category_name::factory($category_action_vars));
$category_columns['priority']
    ->setField('category_priority', rex_structure_category_priority::factory($category_action_vars));
$category_columns['status']
    ->setField('category_status', rex_structure_category_status::factory($category_action_vars));
$category_columns['action']
    ->setField('category_edit', rex_structure_category_edit::factory($category_action_vars))
    ->setField('category_delete', rex_structure_category_delete::factory($category_action_vars));

// Add table body actions and generate body output
do {
    $i_category_id = $KAT->getRows() ? $KAT->getValue('id') : 0;

    // Reset columns, otherwise the manipulations done by the extension point would affect the loop
    $category_row->setColumns($category_columns);

    // Overwrite id of active category with the id of the category currently looped over
    // this way all action classes and fragments can use the same variable names
    $category_row->getColumn('icon')->getField('category_icon')->setVar('edit_id', $i_category_id);
    $category_row->getColumn('id')->getField('category_id')->setVar('edit_id', $i_category_id);
    $category_row->getColumn('category')->getField('category_name')->setVar('edit_id', $i_category_id);
    $category_row->getColumn('priority')->getField('category_priority')->setVar('edit_id', $i_category_id);
    $category_row->getColumn('status')->getField('category_status')->setVar('edit_id', $i_category_id);
    $category_row->getColumn('action')->getField('category_edit')->setVar('edit_id', $i_category_id);
    $category_row->getColumn('action')->getField('category_delete')->setVar('edit_id', $i_category_id);

    $category_action_vars['edit_id'] = $i_category_id;

    // EXTENSION POINT to manipulate row, columns and fields
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

    // The article action row persists the initial edit_id
    $article_row = rex_structure_action_row::factory($article_action_vars);

    // The article action columns get an update of the edit_id in the loop
    $article_columns = [
        'icon' => rex_structure_action_column::factory($article_action_vars),
        'id' => rex_structure_action_column::factory($article_action_vars),
        'article' => rex_structure_action_column::factory($article_action_vars),
        'template' => rex_structure_action_column::factory($article_action_vars),
        'date' => rex_structure_action_column::factory($article_action_vars),
        'priority' => rex_structure_action_column::factory($article_action_vars),
        'status' => rex_structure_action_column::factory($article_action_vars),
        'action' => rex_structure_action_column::factory($article_action_vars),
    ];
    // To column heads the initial edit_id is passed
    $article_columns['icon']
        ->setHead(rex_structure_article_add::factory($article_action_vars)
            ->setVar('hide_label', true)
            ->setVar('hide_border', true)
        )
        ->setField('article_icon', rex_structure_article_icon::factory($article_action_vars));
    $article_columns['id']
        ->setField('article_id', rex_structure_article_id::factory($article_action_vars));
    $article_columns['article']
        ->setField('article_name', rex_structure_article_name::factory($article_action_vars));
    $article_columns['template']
        ->setField('article_template', rex_structure_article_template::factory($article_action_vars));
    $article_columns['date']
        ->setField('article_create_date', rex_structure_article_create_date::factory($article_action_vars));
    $article_columns['priority']
        ->setField('article_priority', rex_structure_article_priority::factory($article_action_vars));
    $article_columns['status']
        ->setField('article_status', rex_structure_article_status::factory($article_action_vars));
    $article_columns['action']
        ->setField('article_edit', rex_structure_article_edit::factory($article_action_vars))
        ->setField('article_delete', rex_structure_article_delete::factory($article_action_vars));

    // Add table body actions and generate body output
    do {
        $i_article_id = $sql->getValue('id');

        // Reset columns, otherwise the manipulations done by the extension point would affect the loop
        $article_row->setColumns($article_columns);

        // Overwrite id of active category with the id of the article currently looped over
        // this way all action classes and fragments can use the same variable names
        $article_row->getColumn('icon')->getField('article_icon')->setVar('edit_id', $i_article_id);
        $article_row->getColumn('id')->getField('article_id')->setVar('edit_id', $i_article_id);
        $article_row->getColumn('article')->getField('article_name')->setVar('edit_id', $i_article_id);
        $article_row->getColumn('template')->getField('article_template')->setVar('edit_id', $i_article_id);
        $article_row->getColumn('date')->getField('article_create_date')->setVar('edit_id', $i_article_id);
        $article_row->getColumn('priority')->getField('article_priority')->setVar('edit_id', $i_article_id);
        $article_row->getColumn('status')->getField('article_status')->setVar('edit_id', $i_article_id);
        $article_row->getColumn('action')->getField('article_edit')->setVar('edit_id', $i_article_id);
        $article_row->getColumn('action')->getField('article_delete')->setVar('edit_id', $i_article_id);

        $article_action_vars['edit_id'] = $i_article_id;

        // EXTENSION POINT to manipulate $article_row
        $article_row = rex_extension::registerPoint(new rex_extension_point('PAGE_STRUCTURE_ARTICLE_ACTIONS', $article_row, [
            'action_vars' => $article_action_vars,
        ]));

        $table_body .= $article_row->getFragment('structure/table_article_row_body.php');

        $sql->next();
    } while ($sql->hasNext());

    // The head is generated after the body, in case any changes where made via extension point
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
