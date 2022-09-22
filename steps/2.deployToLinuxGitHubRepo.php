<?php

return function($args, &$context) {
	$git_repositories_dir = $context["git_repositories_dir"];

	napphp::proc_changeWorkingDirectory("$git_repositories_dir/libnapc/libnapc-linux-releases", function() use (&$context) {
		$project_root = $context["project_root"];
		$libnapc_version = $context["release_version"];

		napphp::fs_copyFile(
			"$project_root/build_files/bundles/linux.tar.gz",
			"libnapc-linux-v$libnapc_version.tar.gz"
		);

		napphp::fs_copyFile(
			"$project_root/build_files/processed_files/napc.h",
			"napc.h"
		);

		swRelease_createCommit($context, ".");
	});
};
