<?php
$about = $cms->page('about');
$newest = array_slice($posts->all(), 0, 6);
$latestComments = $posts->latestComments();
