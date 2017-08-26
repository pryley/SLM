<?php

namespace Deployer;

use Symfony\Component\Yaml\Yaml;

/**
 * @throws \RuntimeException
 * @return void
 */
function configuration( $file ) {
	$configFileContent = Yaml::parse( file_get_contents( $file ));
	if( !is_array( $configFileContent )) {
		throw new \RuntimeException( "Error in parsing " . $file . " file." );
	}
	foreach( $configFileContent as $key => $value ) {
		set( $key, $value );
	}
}

/**
 * Use {{deploy_path}}/public dir instead of {{deploy_path}}/current for serverpilot.
 */
// set( 'current_path', function() {
// 	$link = run( "readlink {{deploy_path}}/{{public_dir}}" )->toString();
// 	return substr( $link, 0, 1 ) !== '/'
// 		? sprintf( '%s/%s', get( 'deploy_path' ), $link )
// 		: $link;
// });

/**
 * Install composer to {{deploy_path}} instead of the {{release_path}}.
 */
// set( 'bin/composer', function() {
// 	if( commandExist( 'composer' )) {
// 		$composer = run( 'which composer' )->toString();
// 	}
// 	if( empty( $composer )) {
// 		run( "cd {{deploy_path}} && curl -sS https://getcomposer.org/installer | {{bin/php}}" );
// 		$composer = '{{bin/php}} {{deploy_path}}/composer.phar';
// 	}
// 	return $composer;
// });
