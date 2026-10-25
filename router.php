<?php

if (PHP_SAPI !== 'cli-server') {
	http_response_code(404);
	exit('Not found');
}

$path = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
$base = basename((string) $path);
if ($base === '.env' || str_starts_with($base, '.env.')) {
	http_response_code(403);
	exit('Forbidden');
}

return false;
