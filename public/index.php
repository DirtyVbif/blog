<?php

use Blog\Blog;

define('ROOTDIR', '../');

require_once ROOTDIR . 'app/autoload.php';

$blog = new Blog;
echo $blog;
