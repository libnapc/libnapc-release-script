<?php

function swRelease_uploadToSite($context, $src_path, $dst_path) {
	$hostname = $context["environment"]["site_deploy_host"];
	$username = $context["environment"]["site_deploy_user"];

	$dst_path = "/home/$username/$dst_path";

	if ($context["dry_run"]) {
		fwrite(STDERR, "Skipping upload of '$src_path' -> '$dst_path' because of --dry-run\n");

		return;
	}

	napphp::shell_execute("scp", [
		"args" => [
			"-o StrictHostKeyChecking=no",
			"-o UserKnownHostsFile=/dev/null",
			"-i",
			$context["secrets"]["keys"]["site_deploy"]."/id_rsa",
			$src_path,
			"$username@$hostname:$dst_path"
		]
	]);
}
