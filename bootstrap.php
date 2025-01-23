<?php

/**
 * Bootstrap for WP Unit Tests
 */
require __DIR__ . '/wp-loader.php';
$core_loader = new WpLoader();
$core_loader->init();