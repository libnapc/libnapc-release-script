<?php

function swRelease_check_checkSSHKeyPair($ssh_key_dir) {
	fwrite(STDERR, "Checking SSH-Key @ $ssh_key_dir\n");

	$private_key_path = "$ssh_key_dir/id_rsa";
	//$public_key_path  = "$ssh_key_dir/id_rsa.pub";

	if (!napphp::fs_isAFile($private_key_path)) {
		throw new Exception("Private key '$private_key_path': not found.");
	}

	$private_key_mode = napphp::fs_getFileMode($private_key_path);
//	$public_key_mode  = napphp::fs_getFileMode($public_key_path);

	if ($private_key_mode !== 0600) {
		$actual = decoct($private_key_mode);

		throw new Exception("Private key '$private_key_path': wrong file mode (actual: $actual, required: 0600).");
	} /*else if ($public_key_mode !== 0644) {
		$actual = decoct($public_key_mode);

		throw new Exception("Public key '$public_key_path': wrong file mode (actual: $actual, required: 0644).");
	}*/

	fwrite(STDERR, "$private_key_path: ok\n");
	//fwrite(STDERR, "$public_key_path: ok\n");
}

return function($args, &$context) {
	$project_root = $context["project_root"];

	if (!napphp::fs_isDirectory("$project_root/build_files/bundles")) {
		throw new Exception("build_files/bundles is missing.");
	} else if (!napphp::fs_isFile("$project_root/build_files/documentation.tar.gz")) {
		throw new Exception("build_files/documentation.tar.gz is missing.");
	}

	$context["release_version"] = napphp::proc_getEnvironmentVariable(
		"LIBNAPC_RELEASE_VERSION", ""
	);

	if (!strlen($context["release_version"])) {
		throw new Exception("Required environment variable 'LIBNAPC_RELEASE_VERSION' not set.");
	} else if (!napphp::str_startsWith($context["release_version"], "v")) {
		throw new Exception("LIBNAPC_RELEASE_VERSION must start with 'v'.");
	}

	$context["release_version"] = substr($context["release_version"], 1);
	$context["dry_run"] = $args["dry-run"] ?? false;

	$context["environment"] = [
		"site_deploy_host" => napphp::proc_getEnvironmentVariable("LIBNAPC_SSH_SITE_DEPLOY_HOST", ""),
		"site_deploy_user" => napphp::proc_getEnvironmentVariable("LIBNAPC_SSH_SITE_DEPLOY_USER", "")
	];

	if (!strlen($context["environment"]["site_deploy_host"])) {
		throw new Exception("Required environment variable 'LIBNAPC_SSH_SITE_DEPLOY_HOST' not set.");
	} else if (!strlen($context["environment"]["site_deploy_user"])) {
		throw new Exception("Required environment variable 'LIBNAPC_SSH_SITE_DEPLOY_USER' not set.");
	}

	foreach ($context["secrets"]["keys"] as $ssh_key_dir) {
		swRelease_check_checkSSHKeyPair($ssh_key_dir);
	}

	$context["secrets"]["github_access_token"] = napphp::fs_readFileString(
		$context["secrets"]["github_access_token"]
	);
};
