<?php

/**
 * PocketMine Tools - Tools to simplify PocketMine plugin development
 * Copyright (C) 2022 CzechPMDevs
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace czechpmdevs\pocketminetools {
	use function exec;
	use function fgets;
	use function file_get_contents;
	use function file_put_contents;
	use function getcwd;
	use function gmdate;
	use function json_encode;
	use function mkdir;
	use function str_replace;
	use function strtolower;
	use function trim;
	use function yaml_emit_file;
	use const JSON_PRETTY_PRINT;
	use const STDIN;

	const BASE_VERSION = "1.0.0";
	const BASE_API = "4.0.0";

	out("Welcome in PocketMine plugin bootstrap!");

	out("Specify plugin name: ");
	in($name);

	out("Specify plugin author:");
	in($author);

	out("Specify plugin description:");
	in($description);

	echo "\n\n";

	out("Generating plugin.yml...");
	yaml_emit_file("plugin.yml", [
		"name" => $name,
		"description" => $description,
		"main" => strtolower("$author\\$name\\") . $name,
		"version" => BASE_VERSION,
		"api" => BASE_API,
		"author" => $author
	]);

	out("Generating composer.json");
	file_put_contents("composer.json", json_encode([
		"name" => strtolower($author . "/" . $name),
		"description" => $description,
		"minimum-stability" => "dev",
		"require" => [
			"pocketmine/pocketmine-mp" => "^" . BASE_API
		]
	], JSON_PRETTY_PRINT));

	out("Would you like to install composer? [Y/n]");
	in($composer);
	if($composer === "Y") {
		out("Installing composer...");
		exec("composer update");
		out("Execute composer update command.");
	}

	$resources = false;

	out("Would you like to generate Main class? [Y/n]");
	in($generate);
	if($generate === "Y") {
		out("Generating workspace...");
		mkdir($dir = strtolower("src/$author/$name"), 0777, true);

		$namespace = strtolower("$author\\$name");
		file_put_contents("$dir/$name.php",
// Main class
			<<<PHP
<?php

declare(strict_types=1);

namespace $namespace;

use pocketmine\\event\\Listener;
use pocketmine\\plugin\\PluginBase;

class $name extends PluginBase implements Listener {
	protected function onEnable(): void {
		
	}
}
PHP
		);
		out("Would you like to generate config workspace too? [Y/n]");
		in($config);
		if($config === "Y") {
			$resources = true;

			mkdir("resources");
			yaml_emit_file("resources/config.yml", []);
		}

		out("Workspace generated!");
	}

	out("Would you like to generate plugin build script? [Y/n]");
	in($buildScript);
	if($buildScript) {
		out("Would you like to specify custom output path? [Y/n]");
		in($customOutPath);
		$out = getcwd() . "/out/";
		if($customOutPath === "Y") {
			out("Specify output path (eg. C:/PocketMine/plugins/):");
			in($out);
		}

		$out .= $name . ".phar";

		out("Plugin will be generated as $out");

		$script = file_get_contents("https://raw.githubusercontent.com/CzechPMDevs/PocketMineTools/master/tools/build-plugin-phar.php");
		$script = str_replace("const OUTPUT_FILE = \"\";", "const OUTPUT_FILE = \"$out\";", $script);

		mkdir("tools");
		file_put_contents("tools/build-plugin-phar.php", $script);
	}


	function out(string $message): void {
		echo "[".gmdate("H:i:s")."] $message\n";
	}

	function in(?string &$command = ""): void {
		$command = trim(fgets(STDIN));
	}
}