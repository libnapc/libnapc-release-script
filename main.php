<?php

function swRelease_main($args, $initial_context = []) {
	$steps_entries = napphp::fs_scandir(__DIR__."/steps/");
	$steps = [];

	foreach ($steps_entries as $step_entry) {
		$tmp = explode(".", $step_entry, 2);

		array_push(
			$steps, [
				"name" => $step_entry,
				"position" => (int)$tmp[0],
				"fn"   => require __DIR__."/steps/$step_entry"
			]
		);
	}

	usort($steps, function($a, $b) {
		return $a["position"] - $b["position"];
	});

	$parsed_args = [];

	foreach ($args as $arg) {
		if (strtolower($arg) === "--dry-run") {
			$parsed_args["dry-run"] = true;
		}
	}

	$context = &$initial_context;

	foreach ($steps as $step) {
		$fn = $step["fn"];

		$fn($parsed_args, $context);
	}
}
