<?php

use Blog\Blog;

define('ROOTDIR', '../');

require_once ROOTDIR . 'app/autoload.php';

app()->run();
echo app();
