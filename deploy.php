<?php

namespace Deployer;

require __DIR__ . '/deploy/recipe/common.php';
require __DIR__ . '/deploy/recipe/lumen.php';
// require __DIR__ . '/deploy/recipe/common/prepare.php';
// require __DIR__ . '/deploy/recipe/common/rollback.php';
// require __DIR__ . '/deploy/recipe/common/symlink.php';

configuration( 'deploy/config.yml' );
inventory( 'deploy/hosts.yml' );

after( 'deploy:failed', 'deploy:unlock' );

// dep artisan:migrate [stage]
// dep artisan:migrate:rollback [stage]
// dep artisan:migrate:status [stage]
// dep artisan:db:seed [stage]
// dep artisan:cache:clear [stage]
// dep artisan:queue:restart [stage]

// desc('Restart PHP-FPM service');
// task('php-fpm:restart', function () {
//     // The user must have rights for restart service
//     // /etc/sudoers: username ALL=NOPASSWD:/bin/systemctl restart php-fpm.service
//     run('sudo systemctl restart php-fpm.service');
// });
// after('deploy:symlink', 'php-fpm:restart');
