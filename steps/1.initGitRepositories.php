<?php

return function($args, &$context) {
	$git_repositories = $context["git_repositories"];
	$git_repositories_dir = $context["git_repositories_dir"];
	$git_local_config = $context["git_local_config"];

	foreach ($git_repositories as $git_repository) {
		napphp::fs_mkdir("$git_repositories_dir/$git_repository");

		$repo_colored = napphp::terminal_colorString($git_repository, "cyan");

		fwrite(STDERR, "Initializing git repository '$repo_colored'\n");

		napphp::proc_changeWorkingDirectory("$git_repositories_dir/$git_repository", function() use (&$context, $git_repository, $git_local_config) {
			$github_push_key = $context["secrets"]["keys"]["github_push"]."/id_rsa";
			$github_push_key = escapeshellarg($github_push_key);

			napphp::shell_execute(
				"git", [
					"args" => [
						"clone",
						"git@github.com:$git_repository.git",
						"."
					],
					"env" => [
						"GIT_SSH_COMMAND" => "ssh -i $github_push_key -o UserKnownHostsFile=/dev/null -o StrictHostKeyChecking=no"
					]
				]
			);

			napphp::shell_execute("git", [
				"args" => [
					"checkout", "main"
				]
			]);

			napphp::shell_execute("git", [
				"args" => [
					"rm", "-rf", "."
				]
			]);

			napphp::shell_execute("git", [
				"args" => [
					"clean", "-fxd"
				]
			]);

			foreach ($git_local_config as $name => $value) {
				fwrite(STDERR, "Setting git config '$name' '$value'\n");

				napphp::shell_execute(
					"git", [
						"args" => [
							"config", $name, $value
						]
					]
				);
			}
		});
	}
};
