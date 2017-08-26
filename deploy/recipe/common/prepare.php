<?php

namespace Deployer;

use Deployer\Task\Context;

desc( 'Preparing server for deploy' );
task( 'deploy:prepare', function() {
    // test remote shell is POSIX-compliant
    try {
        $result = run('echo $0')->toString();
        if ($result == 'stdin: is not a tty') {
            throw new \RuntimeException(
                "Looks like ssh inside another ssh.\n" .
                "Help: http://goo.gl/gsdLt9"
            );
        }
    } catch (\RuntimeException $e) {
        $formatter = Deployer::get()->getHelper('formatter');

        $errorMessage = [
            "Shell on your server is not POSIX-compliant. Please change to sh, bash or similar.",
            "Usually, you can change your shell to bash by running: chsh -s /bin/bash",
        ];
        write($formatter->formatBlock($errorMessage, 'error', true));

        throw $e;
    }

    run('if [ ! -d {{deploy_path}} ]; then mkdir -p {{deploy_path}}; fi');

    // Check for existing /current directory (not symlink)
    $result = run('if [ ! -L {{deploy_path}}/{{public_dir}} ] && [ -d {{deploy_path}}/{{public_dir}} ]; then echo true; fi')->toBool();
    if ($result) {
        // throw new \RuntimeException('There already is a directory (not symlink) named "' . get('public_dir') . '" in ' . get('deploy_path') . '. Remove this directory so it can be replaced with a symlink for atomic deployments.');
        run( 'mv {{deploy_path}}/{{public_dir}} {{deploy_path}}/{{public_dir}}.bak' );
        writeln( '<comment>Renamed ' . get('public_dir') . ' to: ' . get('public_dir') . '.bak</comment>' );
    }

    cd( '{{deploy_path}}' );
    run( 'if [ ! -d .dep ]; then mkdir .dep; fi' );
    run( 'if [ ! -f .dep/revision.log ]; then touch .dep/revision.log; fi' );
    run( 'if [ ! -d releases ]; then mkdir releases; fi' );
    run( 'if [ ! -d shared ]; then mkdir shared; fi' );
});
