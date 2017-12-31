<?php
/**
 * Creates a button or link similar to core/buttons/button.php, but has slight adjustments:
 * - uses tag <span> instead of <button> for elements without url
 * - btn class is not mandatory, to allow elements without padding
 * - icon class is arbitrary
 * - if icon and label is set a space is added between them
 */
if (!isset($this->buttons)) {
    $this->buttons['button'] = $this->button;
}

foreach ($this->buttons as $button) {
    if (!isset($button['attributes'])) {
        $button['attributes'] = [];
    }
    if (!isset($button['attributes']['class'])) {
        $button['attributes']['class'] = [];
    }
    if (!isset($button['label'])) {
        $button['label'] = '';
    }
    if (isset($button['hidden_label'])) {
        $button['label'] = '<span class="sr-only">' . $button['hidden_label'] . '</span>';
    }

    $icon = isset($button['icon']) ? '<i class="' . $button['icon'] . '"></i>' : '';

    $tag = 'span';
    $href = '';
    if (isset($button['url'])) {
        $tag = 'a';
        $href = ' href="' . $button['url'] . '"';
    }
    if ($icon && $button['label']) {
        $icon = $icon.' ';
    }

    echo '<' .$tag . $href . rex_string::buildAttributes($button['attributes']) . '>' . $icon . $button['label'] . '</' . $tag . '>';
}
