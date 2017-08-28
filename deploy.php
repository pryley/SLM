<?php

namespace Deployer;

require_once __DIR__ . '/deploy/recipe/common.php';
require_once __DIR__ . '/deploy/recipe/lumen.php';

configuration( 'deploy/config.yml' );
inventory( 'deploy/hosts.yml' );

after( 'deploy:failed', 'deploy:unlock' );

desc( 'Execute artisan slm:install' );
task( 'slm:install', function() {
	$output = run( '{{bin/php}} {{release_path}}/artisan slm:install' );
	writeln( '<info>' . $output . '</info>' );
});

desc( 'Execute artisan slm:clients' );
task( 'slm:clients', function() {
	$output = run( '{{bin/php}} {{release_path}}/artisan slm:clients' );
	writeln( '<info>' . $output . '</info>' );
});

desc( 'Execute artisan slm:users' );
task( 'slm:users', function() {
	$output = run( '{{bin/php}} {{release_path}}/artisan slm:users' );
	writeln( '<info>' . $output . '</info>' );
});

// dep ssh
// dep artisan:migrate [stage]
// dep artisan:migrate:rollback [stage]
// dep artisan:migrate:status [stage]
// dep artisan:db:seed [stage]
// dep artisan:cache:clear [stage]
// dep artisan:queue:restart [stage]
