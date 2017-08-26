<?php

namespace Deployer;

desc( 'Execute artisan migrate:refresh' );
task( 'artisan:migrate:refresh', function () {
	$output = run('{{bin/php}} {{release_path}}/artisan migrate:refresh --seed' );
	writeln( '<info>' . $output . '</info>' );
});
