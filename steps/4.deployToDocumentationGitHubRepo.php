<?php

return function($args, &$context) {
	$git_repositories_dir = $context["git_repositories_dir"];

	napphp::proc_changeWorkingDirectory("$git_repositories_dir/libnapc/libnapc-documentation", function() use (&$context) {
		$project_root = $context["project_root"];

		napphp::shell_execute("tar", [
			"args" => [
				"-xzvf",
				"$project_root/build_files/documentation.tar.gz",
				"-C",
				"."
			]
		]);

		swRelease_createCommit($context, ".");
	});
};
