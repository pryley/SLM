<?php

namespace Deployer;

/**
 * 1. Change "current" to {{public_dir}}
 * 2. Append {{public_dir}} to release path
 */
desc( 'Creating symlink to release' );
task( 'deploy:symlink', function() {
    if( run( 'if [[ "$(man mv)" =~ "--no-target-directory" ]]; then echo "true"; fi' )->toBool() ) {
        run( 'mv -T {{deploy_path}}/release/{{public_dir}} {{deploy_path}}/{{public_dir}}' );
    }
    else {
        // Atomic override symlink.
        run( '{{bin/symlink}} {{release_path}}/{{public_dir}} {{deploy_path}}' );
        // Remove release link.
        run( 'cd {{deploy_path}} && rm release' );
    }
});
