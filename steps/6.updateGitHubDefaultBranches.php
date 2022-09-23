<?php

return function($args, &$context) {
	if ($context["dry_run"]) {
		fwrite(STDERR, "Skipping setting default branches because of --dry-run\n");

		return;
	}

	$git_repositories = $context["git_repositories"];
	$libnapc_version = $context["release_version"];

	foreach ($git_repositories as $git_repository) {
		napphp::shell_execute(
			"gh", [
				"args" => [
					"api",
					"repos/$git_repository",
					"--method",
					"PATCH",
					"--field",
					"default_branch=v$libnapc_version"
				],
				"env" => [
					"GH_TOKEN" => $context["secrets"]["github_access_token"]
				]
			]
		);
	}
};
