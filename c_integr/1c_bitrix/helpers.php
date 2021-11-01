<?php

define ('DEBUG_XPRINT', 'true');
define ('XPRINT_TEXT', 'Отладочная информация');

function print_var_name($var) {
    foreach($GLOBALS as $var_name => $value) {
        if ($value === $var) {
            return $var_name;
        }
    }

    return false;
}

function d( $param, $title = XPRINT_TEXT )
{
	if (DEBUG_XPRINT == 'true')
	{
		if ($param == null && !is_array($param))
		{
		    if ($title == XPRINT_TEXT) {
                $param = $title;
            }

			/* $positions = array();
			$char = '|';
			$pos = 0;
			while ($pos = strpos($title, $char, $pos)) {
				$positions[$char][] = $pos;
				$pos += strlen($char);
			}

			$char = '$';
			$pos = 0;
			while ($pos = strpos($title, $char, $pos)) {
				$positions[$char][] = $pos;
				$pos += strlen($char);
			}
			var_dump($positions); */
		}
		else if ($title == XPRINT_TEXT) {
		    if (!is_array($param)) {
                $var_name = print_var_name($param);
            }
		    else { $var_name = null; }
            if ($var_name != null) { $title = $var_name; }
		}

		$param_hide = null;
		if (gettype($param) == 'string')
			if (strlen($param) > 150) {
				$param_hide = $param;
				$param = substr($param, 0, 150) . '...';
			}

		ini_set( 'xdebug.var_display_max_depth', 50 );
		ini_set( 'xdebug.var_display_max_children', 25600 );
		ini_set( 'xdebug.var_display_max_data', 9999999999 );
		if ( PHP_SAPI == 'cli' )
		{
			echo "\n---------------[ $title ]---------------\n";
			echo print_r( $param, true );
			echo "\n-------------------------------------------\n";
		}
		else
		{
			?>
			<style>
				.xprint-wrapper {
					padding: 0px;
					margin-top: 12px;
					margin-bottom: 25px;
					color: black;
					background: #f6f6f6;
					position: relative;
					border: 1px solid gray;
					font-size: 13px;
					font-family: InputMono, Monospace;
					width: 80%;
				}

				.xprint-title {
					display: inline-block;
					position: relative;
					padding-top: 3px;
					padding-bottom: 3px;
					padding-left: 10px;
					padding-right: 10px;
					color: #000;
					background: #ddd;
					top: -10px;
					left: 15px;
					min-width: 170px;
					height: 15px;
					text-align: center;
					border: 1px solid gray;
					font-family: InputMono, Monospace;
				}
				.xprint-wrapper:hover .xprint-hide {
					display: block;
				}
				.xprint-hide {
					background-color: #f6f6f6;
					border: 1px solid gray;
					padding: 10px;
					position: absolute;
					top: 40px;
					z-index: 10;
					left: 0;
					display: none;
				}
				.xprint-hide-hint {
					font-size: 20px;
					font-style: normal;
					position: absolute;
					right: 0;
					top: 0;
					color: blue;
				}
				pre {
					margin-top: 0;
					border-left: 1px dashed rgba(0,0,0,.3);
					padding-left: 3px;
					margin-left: 5px;
					margin-bottom: 10px;
					white-space: pre-wrap;       /* css-3 */
					white-space: -moz-pre-wrap;  /* Mozilla, since 1999 */
					white-space: -pre-wrap;      /* Opera 4-6 */
					white-space: -o-pre-wrap;    /* Opera 7 */
					word-wrap: break-word;
				}
			</style>
			<div class="xprint-wrapper">
			<div class="xprint-title"><?= $title ?></div>
			<pre style="color:#000;"><?= htmlspecialchars( print_r( $param, true ) ) ?></pre>
			<?php if (isset($param_hide)): ?>
				<pre style="color:#000;" class="xprint-hide"><?= htmlspecialchars( print_r( $param_hide, true ) ) ?></pre>
				<i class="xprint-hide-hint">!!!</i>
			<?php endif; ?>
			</div><?php
		}
	}
}

function xd( $val, $title = null )
{
    xprint( $val, $title );
    die();
}
