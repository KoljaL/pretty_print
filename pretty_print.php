<?php

$width       = '800';
$height      = '400';
$innerHeight = $height;

$style                = [];
$style['hideContent'] = "\"
console.log(this.nextElementSibling.style.height);
if(this.nextElementSibling.style.height !== '".$height."px' ){
	this.nextElementSibling.style.height = '".$height."px';
	console.log('400')
		}else{
			this.nextElementSibling.style.height = '10px';
			console.log('0')
		}
		\"";

$style['showType'] = "\"
		document.querySelectorAll('[data-type]').forEach((item) => {
			item.style.opacity = ((item.style.opacity!= 0) ?  0  : 1);
		});
		\"";

$style['pretty_print'] = '"font-family:Consolas,monaco,monospace;font-size:14px;border-radius:5px;width:'.$width.'px;max-width:50vw;color:#abb2bf;margin:20px;overflow: auto;resize: both;"';
$style['label']        = '"box-sizing: border-box;display:block;background-color:#1b1e23;font-weight:700;color:#e3e3e3;border-radius:5px;margin-bottom:0;padding:.5em;padding-left:1em;cursor:pointer;z-index:5"';
$style['linenumber']   = '"position:relative;top:0px;right:10px;float:right;font-weight:400;font-size:80%;color:#e3e3e3"';
$style['content']      = '"height:'.$innerHeight.'px; transition:height 0.3s ease-out;overflow:hidden;margin-top: -3px;"';
$style['pre']          = '"position:relative;line-height:1.5em;width:100%;background:#282c34;margin:0;z-index:1;padding:1em;overflow:scroll;height:'.$height.'px;tab-size:4;scrollbar-color:#abb2bf #282c34;scrollbar-width:thin"';
$style['info']         = '"position:absolute;left: calc('.$width.'px - 25px);top: 5px; font-weight:bold;cursor:pointer;"';
$style['key']          = '"color:#e06c75"';
$style['null']         = '"color:#e6c07b"';
$style['boolean']      = '"color:#be5046"';
$style['double']       = '"color:#98c379"';
$style['integer']      = '"color:#98c379"';
$style['string']       = '"color:#61aeee; display:inline-flex"';
$style['array']        = '"color:#e3e3e3"';
$style['object']       = '"color:#e3e3e3"';
$style['type']         = '"color:#818896; padding-left:.5em;opacity:0; transition:opacity 0.3s ease-out;" data-type=" "';
$style['grey']         = '"color:#818896; font-weight:normal;"';
$style['public']       = '"color:#98c379"';
$style['protected']    = '"color:#be5046"';
$style['private']      = '"color:#d19a66"';

function pprint( $arr, $hide = 1, $type = 1, $printable = 1 ) {
    global $style, $height;
    $bt     = debug_backtrace();
    $caller = array_shift( $bt );
    // if($hide == 0) $style['content'] = str_replace($height,'10',$style['content']);
    // $debug = $height.$hide;
    echo "\n<!-- PRETTY_PRINT -->\n";
    // pprint_array($caller);
    // var_dump($bt);
    // echo $style['pretty_print'];
    echo '<div style='.$style['pretty_print'].">\n\t";

    echo '<label style='.$style['label'].' onclick='.$style['hideContent'].'>
			'.print_var_name( $arr ).'<span style='.$style['grey'].'> ('.gettype( $arr ).')</span>
			<span style='.$style['key'].'>'.$debug.'</span>
			<span style='.$style['linenumber'].'>&nbsp; '.basename( $caller['file'] ).':'.$caller['line']."</span>
			</label>\n\t";
    echo '<div style='.$style['content'].">\n";
    echo '<pre style='.$style['pre'].">\n";
    echo '<div style='.$style['info'].'  onclick='.$style['showType'].'>i</div>';
    pprint_array( $arr, '', $printable, $type );
    echo ( $printable ) ? ';' : '';
    echo "\t</pre>\n";
    echo "</div>\n";
    echo "</div>\n";
    echo "<!-- PRETTY_PRINT -->\n\n";

}

function pprint_array( $arr, $p, $printable, $type ) {
    global $style;
    if ( 1 == $printable ) {
        $arround = ['array_1' => 'array(', 'array_2' => ')', 'key_1' => '[', 'key_2' => ']', 'value_1' => '"', 'value_2' => '"', 'type_1' => '[', 'type_2' => ']', 'sep' => ','];
    } else {
        $arround = ['array_1' => '', 'array_2' => '', 'key_1' => '', 'key_2' => '', 'value_1' => '', 'value_2' => '', 'type_1' => '', 'type_2' => '', 'sep' => ''];
    }
    $t = gettype( $arr );
    switch ( $t ) {
    case 'NULL':
        echo '<span class="null"><b>NULL</b></span>'.$arround['sep'];
        break;

    case 'boolean':
        echo '<span style='.$style['boolean'].'>'.( 0 == $arr ? 'false' : 'true' ).'</span>'.$arround['sep'].(  ( $type ) ? '<span style='.$style['type'].'>boolean</span>' : '' );
        break;

    case 'double':
        echo '<span style='.$style['double'].'>'.$arr.'</span>'.$arround['sep'].(  ( $type ) ? '<span style='.$style['type'].'>double</span>' : '' );
        break;

    case 'integer':
        echo '<span style='.$style['integer'].'>'.$arr.'</span>'.$arround['sep'].(  ( $type ) ? '<span style='.$style['type'].'>integer</span>' : '' );
        break;

    case 'string':
        echo '<span style='.$style['string'].'>'.$arround['value_1'].htmlspecialchars( $arr ).$arround['value_2'].$arround['sep'].'</span>'.(  ( $type ) ? '<span style='.$style['type'].'>string('.strlen( $arr ).')</span>' : '' );
        break;

    case 'array':
        echo '<span style='.$style['array'].'>Array</span>'.$arround['array_1'].(  ( $type ) ? ' <span style='.$style['type'].'>('.count( $arr ).')</span>' : '' )."\r\n";

        foreach ( $arr as $k => $v ) {
            if ( gettype( $k ) == 'string' ) {
                echo $p."\t<span style=".$style['key'].'>'.$arround['key_1'].$k.$arround['key_2'].'</span> => ';
            } else {
                echo $p."\t".''.$k.' => ';
            }
            pprint_array( $v, $p."\t", $printable, $type );
            echo "\r\n";
        } // foreach $arr
        echo $p.$arround['array_2'].$arround['sep'];
        break;

    case 'object':
        $class = get_class( $arr );
        $super = get_parent_class( $arr );
        echo '<span style='.$style['object'].'>Object</span>('.$class.( false != $super ? ' exdends '.$super : '' ).')<br>';
        // echo (  ( $printable ) ? '{' : '' )."\r\n";
        $o = (array)$arr;
        foreach ( $o as $k => $v ) {
            $o_type = '';
            $name   = '';
            if ( substr( $k, 1, 1 ) == '*' ) {
                $o_type = 'protected';
                $name   = substr( $k, 2 );
            } elseif ( substr( $k, 1, strlen( $class ) ) == $class ) {
                $o_type = 'private';
                $name   = substr( $k, strlen( $class ) + 1 );
            } elseif ( false != $super && substr( $k, 1, strlen( $super ) ) == $super ) {
                $o_type = $super.' private';
                $name   = substr( $k, strlen( $super ) + 1 );
            } else {
                $o_type = 'public';
                $name   = $k;
            }
            if ( $printable ) {
                echo $p."\t".$arround['type_1'].'<span style='.$style[$o_type].'>'.$o_type.': '.$name.'</span>'.$arround['type_2'].' => ';
            } else {
                echo $p."\t".'<span style='.$style['key'].'>'.$name.'</span> => ';
            }

            pprint_array( $v, $p."\t", $printable, $type );
            echo "\r\n";
        }
        // echo $p.( $printable ) ? '}' : '';
        break;

    default:
        break;
    } // switch
} // function

// get name of $var as string
function print_var_name( $var ) {
    foreach ( $GLOBALS as $var_name => $value ) {
        if ( $value === $var ) {
            return '$'.$var_name;
        }
    }
    return 'pprint';
}
