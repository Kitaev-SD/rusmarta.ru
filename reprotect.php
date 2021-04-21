<?php
if (!function_exists('json_encode')) {
    function json_encode_string($in_str)
    {
        mb_internal_encoding("UTF-8");
        $convmap = array(
            0x80,
            0xFFFF,
            0,
            0xFFFF
        );
        $str     = "";
        for ($i = mb_strlen($in_str) - 1; $i >= 0; $i--) {
            $mb_char = mb_substr($in_str, $i, 1);
            if (mb_ereg("&#(\\d+);", mb_encode_numericentity($mb_char, $convmap, "UTF-8"), $match)) {
                $str = sprintf("\\u%04x", $match[1]) . $str;
            } else {
                $str = $mb_char . $str;
            }
        }
        return $str;
    }
    function json_encode($arr)
    {
        $json_str = "";
        if (is_array($arr)) {
            $pure_array   = true;
            $array_length = count($arr);
            for ($i = 0; $i < $array_length; $i++) {
                if (!isset($arr[$i])) {
                    $pure_array = false;
                    break;
                }
            }
            if ($pure_array) {
                $json_str = "[";
                $temp     = array();
                for ($i = 0; $i < $array_length; $i++) {
                    $temp[] = sprintf("%s", json_encode($arr[$i]));
                }
                $json_str .= implode(",", $temp);
                $json_str .= "]";
            } else {
                $json_str = "{";
                $temp     = array();
                foreach ($arr as $key => $value) {
                    $temp[] = sprintf("\"%s\":%s", $key, json_encode($value));
                }
                $json_str .= implode(",", $temp);
                $json_str .= "}";
            }
        } else {
            if (is_string($arr)) {
                $json_str = "\"" . json_encode_string($arr) . "\"";
            } else if (is_numeric($arr)) {
                $json_str = $arr;
            } else {
                $json_str = "\"" . json_encode_string($arr) . "\"";
            }
        }
        return $json_str;
    }
};

/**
 * Real Recursive Directory Iterator
 */
class RRDI extends RecursiveIteratorIterator
{
    /**
     * Creates Real Recursive Directory Iterator
     * @param string $path
     * @param int $flags
     * @return DirectoryIterator
     */
    public function __construct($path, $flags = 0)
    {
		parent::__construct(new RecursiveDirectoryIterator($path, $flags));
    }
}

/**
 * Real RecursiveDirectoryIterator Filtered Class
 * Returns only those items which filenames match given regex
 */
class AdvancedDirectoryIterator extends FilterIterator
{
    /**
     * Regex storage
     * @var string
     */
    private $regex;
    private $not;
    private $type;
    private $mtime;
    private $name;
	private $maxdepth;
    /**
     * Creates new AdvancedDirectoryIterator
     * @param string $path, prefix with '-R ' for recursive, postfix with /[wildcards] for matching
     * @param int $flags
     * @return DirectoryIterator
     */
    public function __construct($path, $name, $recursive, $type, $not, $mtime, $maxdepth, $flags = 0)
    {
		$this->maxdepth = $maxdepth;
		$this->path = $path;
        $this->name = $name ? $name : '*';
        if (preg_match('~/?([^/]*\*[^/]*)$~', $name, $matches)) { // matched wildcards in filename
            $this->name        = substr($name, 0, -strlen($matches[1]) - 1); // strip wildcards part from path
            $this->regex = '~^' . str_replace('*', '.*', str_replace('.', '\.', $matches[1])) . '$~'; // convert wildcards to regex
            if (!$path)
                $this->path = '.'; // if no path given, we assume CWD
        }

        $this->not   = $not;
        $this->type  = $type;
        $this->mtime = $mtime;
        parent::__construct($recursive ? new RRDI($path, $flags) : new DirectoryIterator($path));
    }
    /**
     * Checks for regex in current filename, or matches all if no regex specified
     * @return bool
     */
    public function accept() // FilterIterator method
    {
		try
		{
			if ($this->getInnerIterator()->getFilename() == '..')
				return false;
			$type_accept     = $this->type == "d" ? $this->getInnerIterator()->isDir() : true;

			if ($this->type=="d" && $this->getInnerIterator()->getFilename() == '.')
			{
				$dir = explode("/",strrev($this->getInnerIterator()->getSubPath()));
				$dir = strrev($dir[0]);
				$filename_accept = $this->regex === null ? (($dir == $this->name) ? true : false) : preg_match($this->regex, $dir);
			}
			else $filename_accept = $this->regex === null ? (($this->getInnerIterator()->getBasename() == $this->name) ? true : false) : preg_match($this->regex, $this->getInnerIterator()->getFilename());
			if (strpos($this->mtime, '-') === 0 || strpos($this->mtime, '+') === 0) {
				if (!$this->not)
					$mtime_accept = $this->mtime === null ? true : $this->getInnerIterator()->getMTime() > strtotime($this->mtime." day") ? true : false;
				else
					$mtime_accept = $this->mtime === null ? true : $this->getInnerIterator()->getMTime() <= strtotime($this->mtime." day") ? true : false;
			} else if (is_numeric($this->mtime)) {
				if (!$this->not)
					$mtime_accept = (date('Ymd') == date('Ymd', $this->getInnerIterator()->getMTime())) ? true : false;
				else
					$mtime_accept = (date('Ymd') != date('Ymd', $this->getInnerIterator()->getMTime())) ? true : false;
			} else
				$mtime_accept = true;
		}
		catch(Exception $e)
		{};

        if ($type_accept && $filename_accept && $mtime_accept)
            return true;
    }
}

function chmod_R($path, $filemode)
{
    if (is_dir($path)) {
        if (!@chmod($path, $filemode)) {
            return FALSE;
        }
        $dh = opendir($path);
        while (($file = readdir($dh)) !== false) {
            if ($file != '.' && $file != '..' && $file != 'logs') {
                $fullpath = $path . '/' . $file;
                chmod_R($fullpath, $filemode);
            }
        }
        closedir($dh);
    } else {
        if (@is_link($path))
            return FALSE;
        if (!@chmod($path, $filemode))
            return FALSE;
    }

}

function calc_mode($file, $str_mode)
{
    $perms = fileperms($file);
    if ($str_mode == "a+w") {
        $perms = (($perms & 0x0080) ? $perms : $perms | 0x0080);
        $perms = (($perms & 0x0010) ? $perms : $perms | 0x0010);
        $perms = (($perms & 0x0002) ? $perms : $perms | 0x0002);

    } else if ($str_mode == "u+w") {
        $perms = (($perms & 0x0080) ? $perms : $perms | 0x0080);
    } else if ($str_mode == "a-w") {
        $perms = (($perms & 0x0080) ? $perms ^ 0x0080 : $perms);
        $perms = (($perms & 0x0010) ? $perms ^ 0x0010 : $perms);
        $perms = (($perms & 0x0002) ? $perms ^ 0x0002 : $perms);
    }
    return $perms;
}

function chmod_exec($chmod_str)
{
    $chmod_str = explode(" ", $chmod_str);
    if ($chmod_str[0] == "chmod" && $chmod_str[1] == "-R") {
        $mode  = $chmod_str[2];
        $files = array_splice($chmod_str, 3);
        foreach ($files as $file) {
            $file = trim($file);
            if (file_exists($file)) {
                if (is_numeric($mode))
                    $mode = octdec($mode);
                else
                    $mode = calc_mode($file, $mode);
                chmod_R($file, $mode);
            }
        }
    } else if ($chmod_str[0] == "chmod") {
        $mode  = $chmod_str[1];
        $files = array_splice($chmod_str, 2);
        foreach ($files as $file) {
            $file = trim($file);
            if (file_exists($file)) {
                if (is_numeric($mode))
                    $mode = octdec($mode);
                else
                    $mode = calc_mode($file, $mode);
                if ($file === '`pwd`')
                    $file = @getcwd();
                if (fileperms($file) != $mode)
                    @chmod($file, $mode);
            }
        }
    }
}
function find_exec($find_str)
{
    $re = '`find\s*(?P<dir>[^\s]+)\s*(-maxdepth (?P<maxdepth>\d+))?\s*(-name [\'"](?P<filename>[^\'^"]+)[\'"])?\s*(-type (?P<type>\w))?\s*(?P<not>!)?\s*(-mtime (?P<mod>-?\d+))?\s*(-exec (?P<exec>[^\\\\]+)?)\\\\;`';
    preg_match($re, $find_str, $matches);
	if (file_exists($matches['dir']) && is_dir($matches['dir']))
	{
		$files = new AdvancedDirectoryIterator($matches['dir'], $matches['filename'], true, $matches['type'], ($matches['not'] != ""), $matches['mod'], $matches['maxdepth']);
		if(isset($matches['maxdepth']) && is_integer($matches['maxdepth'])) $files->setMaxDepth(intval(trim($matches['maxdepth'])));
		foreach ($files as $i)
		{

			if (strpos($matches['exec'], "chmod") === 0) {
				$chmod_str = str_replace('{}', str_replace('./', getcwd() . '/', $i->getPathname()), $matches['exec']);
				$chmod_str = str_replace('/./', '/', $chmod_str);
				$chmod_str = str_replace(' \;', '', $chmod_str);
				chmod_exec(trim($chmod_str));
			}
		}
	}
}
function check_compatibility()
{
	if (!function_exists('chmod') || !is_callable('chmod')) {
		echo '<font color=red>chmod() не доступна. Скрипт работать не будет.</font><br/>';
		echo '<br/>Данная функция должна быть разрешена в настройках PHP (например, в файле ' . php_ini_loaded_file() . ')';
		return FALSE;
	}
	if (!class_exists("RecursiveDirectoryIterator") || !class_exists("SplFileObject"))
	{
		echo '<font color=red>Старая версия PHP. Скрипт работать не будет.</font><br/>';
		return FALSE;
	}
	if (!file_exists("protect.sh"))
	{
		echo '<font color=red>Отсутствует файл protect.sh. Скрипт работать не будет.</font><br/>';
		echo '<br/>Обратитесь, пожалуйста, в <a href="https://revisium.com/">компанию Ревизиум</a> по данной проблеме.';
		return FALSE;
	}

	return TRUE;
}

if(isset($_SERVER['HTTP_X_REQUESTED_WITH'])){
  $http_x_requested_with = $_SERVER['HTTP_X_REQUESTED_WITH'];
} else {
  $http_x_requested_with = '';
}

if ($http_x_requested_with != 'XMLHttpRequest') {
	header('Content-type: text/html; charset=utf-8');
	if (!check_compatibility()) die();
    echo '<html>
  <head>
    <title>Восстановление "цементирования"</title>

	<script   src="https://code.jquery.com/jquery-1.12.4.min.js"   integrity="sha256-ZosEbRLbNQzLpnKIkEdrPv7lOy9C27hHQ+Xp8a4MxAQ="   crossorigin="anonymous"></script>

    <meta charset="utf-8">

  <!-- Latest compiled and minified CSS -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">

  <!-- Optional theme -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">

  <!-- Latest compiled and minified JavaScript -->
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>

  <script type="text/javascript">
    function setCookie (url, offset){
        var ws=new Date();
        if (!offset && !url) {
                ws.setMinutes(10 - ws.getMinutes());
            } else {
                ws.setMinutes(10 + ws.getMinutes());
            }
        document.cookie="scriptOffsetUrl=" + url + ";expires="+ws.toGMTString();
        document.cookie="scriptOffsetOffset=" + offset + ";expires="+ws.toGMTString();
    }

function getCookie(name) {
        var cookie = " " + document.cookie;
        var search = " " + name + "=";
        var setStr = null;
        var offset = 0;
        var end = 0;
        if (cookie.length > 0) {
            offset = cookie.indexOf(search);
            if (offset != -1) {
                offset += search.length;
                end = cookie.indexOf(";", offset)
                if (end == -1) {
                    end = cookie.length;
                }
                setStr = unescape(cookie.substring(offset, end));
            }
        }
        return(setStr);
    }

function showProcess (url, success, offset, action) {
        $(\'#url, #refreshScript\').hide();
        $(\'.progress\').show();
        $(\'#runScript\').text(\'Стоп!\');
        $(\'.bar\').text(url);
        $(\'.bar\').css(\'width\', success * 100 + \'%\');
        setCookie(url, offset);

        $(\'#runScript\').click(function(){
            document.location.href=document.location.href;
            });

        scriptOffset(url, offset, action);
    }

function scriptOffset (url, offset, action) {
    $.ajax({
            url: "/' . trim($_SERVER['PHP_SELF'], DIRECTORY_SEPARATOR) . '",
            type: "POST",
            data: {
        "action":action
              , "url":url
              , "offset":offset
            },
            success: function(data){
                try{
                    data = $.parseJSON(data);
                    if(data.success != "finish") {
                        showProcess(url, data.success, data.offset, action);
                    } else if (data.success=="finish") {
                        setCookie();
                        $(\'.bar\').css(\'width\',\'100%\');
                        $(\'.bar\').text(\'Успешно завершено\');
                        $(\'#runScript\').text(\'Еще\');
                    }
                } catch(e) {
                    setCookie();
                    $(\'.bar\').css(\'width\',\'100%\');
                    $(\'.bar\').text(\'OK\');
                    $(\'#runScript\').text(\'Еще\');
                    document.open("text/html", "replace");
                    document.write(data);
                    document.close();
                }
            },
			fail: function (data) {
				alert("Ошибка: " + data);
			}
    });
}

$(document).ready(function() {

    var url = getCookie("scriptOffsetUrl");
    var offset = getCookie("scriptOffsetOffset");
    if (url && url != \'undefined\') {
        $(\'#refreshScript\').show();
        $(\'#runScript\').text(\'Продолжить\');
        $(\'#url\').val(url);
        $(\'#offset\').val(offset);
    }

    $(\'#runScript\').click(function() {
        $(\'.progress\').show();
        $(\'#runScript\').text(\'Стоп!\');

        var action = $(\'#runScript\').data(\'action\');
        var offset = $(\'#offset\').val();
        var url = $(\'#url\').val();

        if ($(\'#url\').val() != getCookie("scriptOffsetUrl")) {
            setCookie();
            scriptOffset(url, 0, action);
        } else {
            scriptOffset(url, offset, action);
        }
        return false;
    });

    $(\'#refreshScript\').click(function() {

        var action = $(\'#runScript\').data(\'action\');
        var url = $(\'#url\').val();

        setCookie();
        scriptOffset(url, 0, action);
        return false;
    });

});
</script>

<style>
body {
  font-family: Helvetica, Tahoma;
}


input {
    font-size: 15px;
    margin: 0;
    padding: 0 3px;
    vertical-align: middle;
    border: 1px solid #CCCCCC;
    border-radius: 3px 3px 3px 3px;
    color: #808080;
    display: inline-block;
    font-size: 13px;
    height: 26px;
    line-height: 18px;
    width: 243px;
}

.btn {
    font-size: 15px;
    padding: 5px 8px;
    background-color: #409cd1;
    background-image: -moz-linear-gradient(center top , #049CDB, #0064CD);
    background-repeat: repeat-x;
    border-color: #409cd1;
    color: #FFFFFF;
    display: inline-block;
    vertical-align: middle;
    border-radius: 3px 3px 3px 3px;
}

.btn:hover {
    background-position: 0 -15px;
	background: none;
	color:  #409cd1;
}

.progress {
    font-size: 13px;
    margin: 0;
    vertical-align: middle;
    background-color: #F7F7F7;
    background-image: -moz-linear-gradient(center top , #F5F5F5, #F9F9F9);
    background-repeat: repeat-x;
    border-radius: 4px 4px 4px 4px;
    height: 28px;
    width: 500px;
    overflow: hidden;
    display: inline-block;
}

.bar {
    background-color: #0E90D2;
    background-image: -moz-linear-gradient(center top , #149BDF, #0480BE);
    background-size: 40px 40px;
    -moz-box-sizing: border-box;
    -moz-transition: width 0.6s ease 0s;
    background-repeat: repeat-x;
    color: #FFFFFF;
    float: left;
    font-size: 12px;
    height: 100%;
    text-align: left;
    padding: 5px 8px;
    font-size: 13px;
    white-space: nowrap;
}

</style>
  </head>
  <body>
<div class="container">
  <div class="row">

    <div class="col-md-12 text-center">
      <div style="margin: 230px 0 30px 0"><img src="https://revisium.com/images/logo.png"></div>

      <input id="url" name="url" type="hidden">
      <input id="offset" name="offset" type="hidden">

      <div  style="margin: 30px 0 30px 0">
        <a href="#" id="runScript"  class="btn" style="margin-left: 20px" data-action="run">Восстановить "цементирование" файлов</a>
      </div>

      <div class="progress" style="display: none">
        <div class="bar" style="width: 0%;"></div>
      </div>
    </div>

   </div>
</div>
  </body>
</html>

';
} else {
    $file = new SplFileObject('protect.sh');
    $file->seek($file->getSize());
    $linesTotal = $file->key();
    $file->seek(intval($_POST['offset']));
    $offset      = intval($_POST['offset']);
    if (intval($_POST['offset']) >= $linesTotal) {
        $success = "finish";
        $output  = Array(
            'offset' => $offset,
            'success' => $success
        );
        echo json_encode($output);
        die();
    } else {
        if ($http_x_requested_with != 'XMLHttpRequest')
            return;
        $protect_cmd = trim($file->current());
        //echo $protect_cmd."\n";
        $protect_cmd = str_replace('`pwd`', getcwd(), $protect_cmd);
        if (strpos($protect_cmd, "chmod") === 0)
            chmod_exec($protect_cmd);
        else if (strpos($protect_cmd, "find") === 0)
            find_exec($protect_cmd);
        $success = round($offset / $linesTotal, 2);
        $offset++;
        $output = Array(
            'offset' => $offset,
            'success' => $success,
            'file' => $protect_cmd
        );
        echo json_encode($output);
        die();

    }
    echo 'OK';
    die();
}
