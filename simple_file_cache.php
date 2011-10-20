<?php

/**
 * Simple file cache class
 *
 * @name SFCache - Simple file Cache
 * @author Jose' Pedro Saraiva <nocive@gmail.com>
 */
if (! defined( 'SFC_CACHE_PATH' )) {
	define( 'SFC_CACHE_PATH', realpath( dirname( __FILE__ ) ) . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR );
}

class SFCache
{
	/**
	 * Enter description here ...
	 *
	 * @var array
	 * @access public
	 */
	public static $config = array(
		'path' => SFC_CACHE_PATH,
		'prefix' => 'sfc_',
		'file_mod' => 0666,
		'duration' => '+24 hours',
		'hash_keys' => true
	);

	/**
	 * Enter description here ...
	 *
	 * @param string $key
	 * @return mixed|false
	 */
	public static function read( $key )
	{
		return self::_read( $key );
	} // read }}}


	/**
	 * Enter description here ...
	 *
	 * @param string $key
	 * @param mixed $data
	 * @param [ string $duration ]
	 * @param [ bool $writeIfEmpty ]
	 * @return bool
	 */
	public static function write( $key, $data, $duration = null, $writeIfEmpty = false )
	{
		$duration = $duration === null ? self::$config['duration'] : $duration;
		if (! $writeIfEmpty && empty( $data )) {
			return false;
		}
		return self::_write( $key, $data, $duration );
	} // write }}}


	/**
	 * Enter description here ...
	 *
	 * @param string $key
	 * @return mixed|false
	 */
	protected static function _read( $key )
	{
		$file = self::$config['path'] . self::$config['prefix'] . (self::$config['hash_keys'] ? md5( $key ) : $key );
		if (false !== ($cacheContent = @file_get_contents( $file ))) {
			$cacheContent = explode( "\n", $cacheContent );
			if (count( $cacheContent ) === 2) {
				if ((int) $cacheContent[0] >= time()) {
					return unserialize( $cacheContent[1] );
				}
			}
		}
		return false;
	} // _read }}}


	/**
	 * Enter description here ...
	 *
	 * @param string $key
	 * @param mixed $data
	 * @param string $duration
	 * @return bool
	 */
	protected static function _write( $key, $data, $duration )
	{
		if (! is_dir( self::$config['path'] )) {
			@mkdir( self::$config['path'] );
		}
		if (false === ($expire = strtotime( $duration ))) {
			throw new Exception( "Invalid duration, could not convert to timestamp: '$duration'" );
		}

		$data = $expire . "\n" . serialize( $data );
		$file = self::$config['path'] . self::$config['prefix'] . (self::$config['hash_keys'] ? md5( $key ) : $key );

		$status = (@file_put_contents( $file, $data ) !== false);
		if ($status && ! empty( self::$config['file_mod'] )) {
			@chmod( $file, self::$config['file_mod'] );
		}
		return $status;
	} // _write }}}
}


?>
