<?php

if (!file_exists(__DIR__ . '/foo')) {
    // create the file if it doesn't exist
    file_put_contents(__DIR__ . '/foo', 'Hello, World!');
}
