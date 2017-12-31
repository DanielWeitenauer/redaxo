<?php
/**
 * @author Daniel Weitenauer
 * @copyright (c) 2017 studio ahoi
 */

if (rex::isBackend() && rex::getUser() && !rex::isSetup()) {
    structure_custom_url_page::init();
}

if (!rex::isBackend() && !rex::isSetup()) {
    structure_custom_url_replace::init();
}
