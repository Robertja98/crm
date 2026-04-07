<?php
file_put_contents(__DIR__ . '/test_write.txt', 'Test write at ' . date('Y-m-d H:i:s'));
echo 'Write attempted.';
?>