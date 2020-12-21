<?php

function pprint_css() {
	echo <<<EOL
<style>
div.pretty_print {font-family: Consolas, monaco, monospace;font-size: 1em;background-color: #b1b1b1;border: 1px solid #949494;border-radius: 5px;width: max-content;margin: 20px;}
div.pretty_print label {display: inline-block;width: 100%;font-weight: bold;margin: .2em;cursor: pointer;}
div.pretty_print label span.linenumber {position: relative;top: 3px;right: 10px;float:right;font-weight: normal;font-size: 80%; color:white;}
div.pretty_print pre {background: lightgray;margin: 0px;padding: 5px;overflow-y: scroll;max-height: 400px;padding-right: 50px;}
div.pretty_print pre {scrollbar-width: none;}
div.pretty_print pre::-webkit-scrollbar {display: none;}
div.pretty_print .visually-hidden {position: absolute;left: -100vw;}
div.pretty_print pre span {line-height: 1.5em;}
div.pretty_print pre span.null {color: black;}
div.pretty_print pre span.boolean {color: brown;}
div.pretty_print pre span.double {color: darkgreen;}
div.pretty_print pre span.integer {color: green;}
div.pretty_print pre span.string {color: darkblue;}
div.pretty_print pre span.array {color: black;}
div.pretty_print pre span.object {color: black;}
div.pretty_print pre span.type {color: grey;}
div.pretty_print pre span.public {color: darkgreen;}
div.pretty_print pre span.protected {color: red;}
div.pretty_print pre span.private {color: darkorange;}
</style>
EOL;
}


function pprint($arr, $printable = 0, $type = 0, $hide = 0) {
	$bt = debug_backtrace();
	$caller = array_shift($bt);
	$id = random_int(0, 999);
	echo "\n<!-- PRETTY_PRINT -->\n";
	// var_dump($caller);
	echo "<style>#hide_$id:checked ~ pre{display: none;}</style>\n";
	echo "<div class='pretty_print'>\n\t";
	echo "<label for='hide_$id'>$".print_var_name($arr). " <span class='linenumber'>&nbsp; ".basename($caller['file']).":".$caller['line']."</span></label>\n\t";
	echo "<input type='checkbox' id='hide_$id' class='visually-hidden' ".(($hide) ? ' checked' : '')." >\n\t";
	echo "<pre>\n";
	pprint_array($arr, "", $printable, $type);
	echo ($printable) ? ";" : '';
	echo "\t</pre>\n";
	echo "</div>\n";
	echo "<!-- PRETTY_PRINT -->\n\n";
}

function pprint_array($arr, $p, $printable, $type) {
	if ($printable == 1) {
		$arround = array('array_1' => 'array(', 'array_2' => ')', 'key_1' => '[', 'key_2' => ']', 'value_1' => '"', 'value_2' => '"', 'type_1' => '[', 'type_2' => ']', 'sep' => ',');
	} else {
		$arround = array('array_1' => '', 'array_2' => '', 'key_1' => '', 'key_2' => '', 'value_1' => '', 'value_2' => '', 'type_1' => '', 'type_2' => '', 'sep' => '');
	}
	$t = gettype($arr);
	switch ($t) {

		case "NULL":
			echo '<span class="null"><b>NULL</b></span>'.$arround['sep'];
			break;

		case "boolean":
			echo '<span class="boolean">'.($arr == 0 ? "false" : "true").'</span>'.$arround['sep'].(($type) ? ' <span class="type">boolean</span>' : '');
			break;

		case "double":
			echo '<span class="double">'.$arr.'</span>'.$arround['sep'].(($type) ? ' <span class="type">double</span>' : '');
			break;

		case "integer":
			echo '<span class="integer">'.$arr.'</span>'.$arround['sep'].(($type) ? ' <span class="type">integer</span>' : '');
			break;

		case "string":
			echo $arround['value_1'].'<span class="string">'.$arr.'</span>'.$arround['value_2'].$arround['sep'].(($type) ? ' <span class="type">string('.strlen($arr).')</span>' : '');
			break;

		case "array":
			echo $arround['array_1'].(($type) ? ' <span class="type">('.count($arr).')</span>' : '')."\r\n";

			foreach ($arr as $k => $v) {
				if (gettype($k) == "string") {
					echo $p."\t".$arround['key_1'].$k.$arround['key_2'].' => ';
				} else {
					echo $p."\t"."".$k." => ";
				}
				pprint_array($v, $p."\t", $printable, $type);
				echo "\r\n";
			} // foreach $arr
			echo $p.$arround['array_2'].$arround['sep'];
			break;

		case "object":
			$class = get_class($arr);
			$super = get_parent_class($arr);
			echo "<span class='object'>Object</span>(".$class.($super != false ? " exdends ".$super : "").")";
			echo (($printable) ? "{" : '')."\r\n";
			$o = (array)$arr;
			foreach ($o as $k => $v) {
				$o_type = "";
				$name = "";
				if (substr($k, 1, 1) == "*") {
					$o_type = "protected";
					$name = substr($k, 2);
				} else if (substr($k, 1, strlen($class)) == $class) {
					$o_type = "private";
					$name = substr($k, strlen($class) + 1);
				} else if ($super != false && substr($k, 1, strlen($super)) == $super) {
					$o_type = $super." private";
					$name = substr($k, strlen($super) + 1);
				} else {
					$o_type = "public";
					$name = $k;
				}
				if ($printable) {
					echo $p."\t".$arround['type_1']."<span class='$o_type'>".$o_type.": ".$name."</span>".$arround['type_2']." => ";
				} else {
					echo $p."\t"."<span class='$o_type'>".$name."</span> => ";
				}

				pprint_array($v, $p."\t", $printable, $type);
				echo "\r\n";
			}
			echo $p.($printable) ? "}" : '';
			break;

		default:
			break;
	} // switch
} // function


// get name of $var as string
function print_var_name($var) {
	foreach ($GLOBALS as $var_name => $value) {
		if ($value === $var) {
			return $var_name;
		}
	}
	return 'array';
}
