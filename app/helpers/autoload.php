<?php

$helpers = [];

foreach ([] as $helper_file) {
    require_once HELPERS . $helper_file;
}

return;
