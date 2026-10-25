<?php

function load_env($path)
{
	if (!is_readable($path)) {
		return;
	}

	$lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
	if ($lines === false) {
		return;
	}

	foreach ($lines as $line) {
		$line = trim($line);
		if ($line === '' || $line[0] === '#') {
			continue;
		}

		$eq = strpos($line, '=');
		if ($eq === false) {
			continue;
		}

		$key = trim(substr($line, 0, $eq));
		$value = trim(substr($line, $eq + 1));
		if ($key === '') {
			continue;
		}

		$length = strlen($value);
		if ($length >= 2) {
			$quote = $value[0];
			if (($quote === '"' || $quote === "'") && $value[$length - 1] === $quote) {
				$value = substr($value, 1, -1);
			}
		}

		if (getenv($key) === false) {
			putenv($key . '=' . $value);
			$_ENV[$key] = $value;
		}
	}
}

function env_value($key)
{
	$value = getenv($key);
	if ($value === false) {
		return '';
	}
	return $value;
}

function h($value)
{
	return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function database_error()
{
	http_response_code(500);
	exit('Database error');
}

function resolve_db_path($path)
{
	if ($path === '') {
		return '';
	}
	if ($path[0] === '/' || preg_match('#^[A-Za-z]:[\\\\/]#', $path)) {
		return $path;
	}
	return __DIR__ . DIRECTORY_SEPARATOR . $path;
}

function database_connect()
{
	$path = resolve_db_path(env_value('DB_PATH'));
	if ($path === '') {
		database_error();
	}

	try {
		$db = new PDO('sqlite:' . $path, null, null, [
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
		]);
	} catch (Exception $e) {
		database_error();
	}

	return $db;
}

load_env(__DIR__ . '/.env');
?>
