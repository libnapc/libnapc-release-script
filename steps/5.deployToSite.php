<?php

return function($args, &$context) {
	$project_root = $context["project_root"];
	$libnapc_version = $context["release_version"];

	$upload_files = [
		"$project_root/build_files/documentation.tar.gz"   => "tmp/libnapc-documentation-v$libnapc_version.tar.gz",
		"$project_root/build_files/bundles/linux.tar.gz"   => "tmp/libnapc-linux-v$libnapc_version.tar.gz",
		"$project_root/build_files/bundles/arduino.zip"    => "tmp/libnapc-arduino-v$libnapc_version.zip",
		"$project_root/build_files/processed_files/napc.h" => "tmp/libnapc-v$libnapc_version.h"
	];

	$check_integrity_script = "#!/bin/bash -euf\n";

	foreach ($upload_files as $source => $destination) {
		$sha256_hash = napphp::fs_hashFile($source, "sha256");
		$destination_basename = basename($destination);

		$check_integrity_script .= "printf \"Checking $destination_basename ... \"\n";
		$check_integrity_script .= "printf \"$sha256_hash $destination_basename\" | sha256sum --check --status\n";
		$check_integrity_script .= "printf \"ok\\n\"\n";
	}

	$check_integrity_script .= "printf \"Successfully checked integrity!\\n\"\n";

	$check_integrity_script_path = napphp::tmp_createFile(".sh");
	napphp::fs_writeFileStringAtomic($check_integrity_script_path, $check_integrity_script);

	foreach ($upload_files as $source => $destination) {
		swRelease_uploadToSite($context, $source, $destination);
	}

	swRelease_uploadToSite(
		$context,
		$check_integrity_script_path,
		"tmp/check-integrity-v$libnapc_version.sh"
	);

	$install_template_script = napphp::fs_readFileString(__DIR__."/../lib/installScriptTemplate.sh");
	$install_script = napphp::str_replace($install_template_script, [
		"%%%LIBNAPC_DEPLOY_USER%%%",
		"%%%LIBNAPC_RELEASE_VERSION%%%"
	], [
		$context["environment"]["site_deploy_user"],
		$context["release_version"]
	]);

	$install_script_path = napphp::tmp_createFile(".sh");
	napphp::fs_writeFileStringAtomic($install_script_path, $install_script);

	swRelease_uploadToSite(
		$context,
		$install_script_path,
		"tmp/install-v$libnapc_version.sh"
	);

	swRelease_executeCommand(
		$context,
		"chmod +x tmp/install-v$libnapc_version.sh"
	);

	swRelease_executeCommand(
		$context,
		"./tmp/install-v$libnapc_version.sh"
	);
};
