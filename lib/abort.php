<?php

function swRelease_abort($message) {
	fwrite(STDERR, "swRelease_abort(): $message\n");
	exit(1);
}
