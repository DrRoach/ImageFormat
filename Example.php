<h1>Productizer</h1>

<?php

$start = microtime(true);

require_once 'Create.php';
new Create([
    'image' => 'snorkel.jpg',
    'width' => 400,
    'height' => 400,
    'background' => '#9b59b6',
    'name' => 'thumb',
    'accuracy' => 40 //Optional
]);

$end = microtime(true);
echo "Total = " .($end-$start);