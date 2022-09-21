<?php

return function($args, &$context) {
	$git_repositories_dir = $context["git_repositories_dir"];

	napphp::proc_changeWorkingDirectory("$git_repositories_dir/libnapc/libnapc-arduino-releases", function() use (&$context) {
		$project_root = $context["project_root"];

		napphp::shell_execute("unzip", [
			"args" => [
				"$project_root/build_files/bundles/arduino.zip",
				"-d",
				"."
			]
		]);

		swRelease_createCommit($context, ".");
	});
};
