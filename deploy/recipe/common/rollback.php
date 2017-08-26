<?php

namespace Deployer;

use Deployer\Task\Context;

/**
 * 1. Change "current" to {{public_dir}}
 * 2. Append {{public_dir}} to release path
 * 3. Log the rollback
 */
desc( 'Rollback to previous release' );
task( 'rollback', function() {
    $releases = get( 'releases_list' );
    $user = Context::get()->getServer()->getConfiguration()->getUser();

    if( isset( $releases[1] )) {

        $releaseDir = "{{deploy_path}}/releases/{$releases[1]}/{{public_dir}}";
        $date = date( 'Y-m-d H:i' );

        // Symlink to old release.
        run( "{{bin/symlink}} $releaseDir {{deploy_path}}/{{public_dir}}" );

        // Remove release
        run( "rm -rf {{deploy_path}}/releases/{$releases[0]}" );

        run( "echo '$date, $user rolled back to release {$releases[1]}' >> .dep/revision.log" );

        if( isVerbose() ) {
            writeln( "Rollback to `{$releases[1]}` release was successful." );
        }
    }
    else {
        writeln( '<comment>No more releases you can revert to.</comment>' );
    }
});
