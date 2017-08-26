<?php

namespace Deployer;

require 'recipe/laravel.php';
require __DIR__ . '/deploy/recipe/common.php';
require __DIR__ . '/deploy/recipe/laravel.php';
// require __DIR__ . '/deploy/recipe/common/prepare.php';
// require __DIR__ . '/deploy/recipe/common/rollback.php';
// require __DIR__ . '/deploy/recipe/common/symlink.php';

configuration( 'deploy/config.yml' );
inventory( 'deploy/hosts.yml' );

before( 'deploy:symlink', 'artisan:migrate' );
before( 'deploy:symlink', 'deploy:public_disk' );
after( 'deploy:failed', 'deploy:unlock' );

// dep artisan:up [stage]
// dep artisan:down [stage]
// dep artisan:migrate [stage]
// dep artisan:migrate:rollback [stage]
// dep artisan:migrate:status [stage]
// dep artisan:db:seed [stage]
// dep artisan:cache:clear [stage]
// dep artisan:config:cache [stage]
// dep artisan:route:cache [stage]
// dep artisan:view:clear [stage]
// dep artisan:optimize [stage]
// dep artisan:queue:restart [stage]

// desc('Restart PHP-FPM service');
// task('php-fpm:restart', function () {
//     // The user must have rights for restart service
//     // /etc/sudoers: username ALL=NOPASSWD:/bin/systemctl restart php-fpm.service
//     run('sudo systemctl restart php-fpm.service');
// });
// after('deploy:symlink', 'php-fpm:restart');
