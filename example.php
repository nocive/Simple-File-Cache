<?php

require_once( 'simple_file_cache.php' );
var_dump( SFCache::write( 'foobar', array( 'foo' => 'bat' ), '+30 minutes' ) );
var_dump( SFCache::read( 'foobar' ) );
