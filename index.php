#!/usr/bin/env php
<?php

require_once __DIR__."/lib/abort.php";
require_once __DIR__."/lib/createCommit.php";
require_once __DIR__."/lib/uploadToSite.php";
require_once __DIR__."/lib/executeCommand.php";
require_once __DIR__."/main.php";

define("LIBNAPC_PROJECT_ROOT_DIR", getcwd());

if (!is_file(LIBNAPC_PROJECT_ROOT_DIR."/load-napphp.php")) {
	swRelease_abort(
		"load-napphp.php not found, are you sure you're in the project root?"
	);
}

require LIBNAPC_PROJECT_ROOT_DIR."/load-napphp.php";

// argv[0] is always program's name
array_shift($argv);

try {
	swRelease_main($argv, [
		"project_root" => LIBNAPC_PROJECT_ROOT_DIR,

		"secrets" => [
			"keys" => [
				"commit_signing" => LIBNAPC_PROJECT_ROOT_DIR."/.secrets/keys/commit_signing/",
				"github_push" => LIBNAPC_PROJECT_ROOT_DIR."/.secrets/keys/github_push/",
				"site_deploy" => LIBNAPC_PROJECT_ROOT_DIR."/.secrets/keys/site_deploy/"
			],
			"github_access_token" => LIBNAPC_PROJECT_ROOT_DIR."/.secrets/github_access_token"
		],

		"git_repositories_dir" => napphp::tmp_createDirectory(),

		"git_repositories" => [
			"libnapc/libnapc-arduino-releases",
			"libnapc/libnapc-linux-releases",
			"libnapc/libnapc-documentation"
		],

		"git_local_config" => [
			"user.email"      => "libnapc-bot@nap.software",
			"user.name"       => "libnapc (bot)",
			"gpg.format"      => "ssh",
			"user.signingkey" => LIBNAPC_PROJECT_ROOT_DIR."/.secrets/keys/commit_signing/id_rsa",
			"core.sshCommand" => "ssh -i ".escapeshellarg(LIBNAPC_PROJECT_ROOT_DIR."/.secrets/keys/github_push/id_rsa")
		]
	]);
} catch (Exception $e) {
	swRelease_abort($e->getMessage());
} finally {
	napphp::tmp_cleanup();
}
