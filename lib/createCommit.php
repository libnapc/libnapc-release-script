<?php

function swRelease_createCommit($context, $cwd) {
	napphp::proc_changeWorkingDirectory($cwd, function() use (&$context) {
		$version = $context["release_version"];

		# branch has 'v' prefix like v1.2.3
		napphp::shell_execute("git", [
			"args" => ["checkout", "-b", "v$version"]
		]);

		napphp::shell_execute("git", [
			"args" => ["add", "."]
		]);

		napphp::shell_execute("git", [
			"args" => ["commit", "-m", "Release $version", "-S"]
		]);

		napphp::shell_execute("git", [
			"args" => ["tag", "-a", $version, "-m", "Release $version", "--sign"]
		]);

		if ($context["dry_run"]) {
			fwrite(STDERR, "Skipping push because of --dry-run\n");

			return;
		}

		napphp::shell_execute("git", [
			"args" => ["push", "-u", "origin", "v$version"]
		]);

		napphp::shell_execute("git", [
			"args" => ["push", "origin", "$version"]
		]);
	});
}
