<?php

function swRelease_executeCommand($context, $cmd) {
	$hostname = $context["environment"]["site_deploy_host"];
	$username = $context["environment"]["site_deploy_user"];

	if ($context["dry_run"]) {
		fwrite(STDERR, "Skipping execution of '$cmd' because of --dry-run\n");

		return;
	}

	napphp::shell_execute(
		"ssh", [
			"args" => [
				"-o StrictHostKeyChecking=no",
				"-o UserKnownHostsFile=/dev/null",
				"-i",
				$context["secrets"]["keys"]["site_deploy"]."/id_rsa",
				"$username@$hostname",
				"--",
				$cmd
			]
		]
	);
}
