<?php

################
#   VALIDATE   #
################
/**
 * @param string $email
 * @return bool
 */
function is_email(string $email):bool
{
    return filter_var($email,FILTER_VALIDATE_EMAIL);
}

/**
 * @param string $password
 * @return bool
 */
function is_passwd(string $password):bool
{
    if (password_get_info($password) || (mb_strlen($password) >= CONF_PASSWD_MIN_LEN && mb_strlen($password) <= CONF_PASSWD_MAX_LEN)){
        return true;
    }
    return false;
}

/**
 * @param string $pass
 * @return string
 */
function passwd(string $pass): string
{
    if (!empty(password_get_info($pass)['algo'])){
        return $pass;
    }
    return password_hash($pass,CONF_PASSWD_ALGO,CONF_PASSWD_OPTIONS);
}

/**
 * @param $pass
 * @param $hash
 * @return bool
 */
function passwd_verify($pass ,$hash) :bool
{
    return password_verify($pass,$hash);
}

/**
 * @param $hash
 */
function passwd_rehash($hash)
{
    password_needs_rehash($hash,CONF_PASSWD_ALGO,CONF_PASSWD_OPTIONS);
}
##############
#   REQUEST  #
##############
/**
 * @return string
 * @throws Exception
 */
function csrf_input(): string
{
    session()->csrf();
    return "<input  type='hidden' name='csrf' value='".(session()->csrf_token ?? "")."'>";
}

/**
 * @param $request
 * @return bool
 */
function csrf_verify($request):bool
{
    if (empty(session()->csrf_token) || empty($request['csrf']) || $request['csrf'] != session()->csrf_token){
        return false;
    }
    return true;
}

function flash() :?string
{   $session = new \Source\Core\Session();
    if ($flash = $session->flash() ){
        echo $flash;
    }
    return null;
}

#############
#   STRING  #
#############
/**
 * @param string $string
 * @return string
 */
function str_slug(string $string):string
{
    $string = filter_var(mb_strtolower($string),FILTER_SANITIZE_STRIPPED);
    $formats = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜüÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿRr"!@#$%&*()_-+={[}]/?;:.,\\\'<>°ºª';
    $replace = 'aaaaaaaceeeeiiiidnoooooouuuuuybsaaaaaaaceeeeiiiidnoooooouuuyybyRr                                 ';

    return str_replace(["-----","----","---","--"],"-",
        str_replace(" ","-",
            trim(strtr(utf8_decode($string),utf8_decode($formats),$replace))
        ));

}

/**
 * @param string $string
 * @return string
 */
function str_studly_case(string $string):string
{
    $string = str_slug($string);
    return str_replace(" ","",
        mb_convert_case(str_replace("-"," ",$string),MB_CASE_TITLE)

    );

}

/**
 * @param string $string
 * @return string
 */
function str_camel_case(string $string) :string
{
    return lcfirst(str_studly_case($string));
}

/**
 * @param string $string
 * @return false|string|string[]
 */
function str_title(string $string)
{
    return mb_convert_case(filter_var($string,FILTER_SANITIZE_SPECIAL_CHARS),MB_CASE_TITLE);
}

/**
 * @param string $string
 * @param int $limit
 * @param string $pointer
 * @return string
 */
function str_limit_words(string $string, int $limit,string $pointer = '...'):string
{
    $string = trim(filter_var($string,FILTER_SANITIZE_SPECIAL_CHARS));
    $arrWords = explode(" ",$string);
    $numWords = count($arrWords);

    if ($numWords < $limit){
        return $string;
    }

     $words = implode(" ",array_splice($arrWords,0,$limit));
    return "{$words}{$pointer}";
}

/**
 * @param string $string
 * @param int $limit
 * @param string $pointer
 * @return string
 */
function str_limit_chars(string $string, int $limit,string $pointer = '...'):string
{
    $string = trim(filter_var($string,FILTER_SANITIZE_SPECIAL_CHARS));

    if (mb_strlen($string) < $limit){
        return $string;
    }

    $chars = mb_substr($string,0,mb_strrpos(mb_substr($string,0,$limit)," "));
    return "{$chars}{$pointer}";
}


/**
 * @param string|null $path
 * @return string
 */
function url(string $path = null): string
{
    if(strpos($_SERVER["HTTP_HOST"],"localhost") !== false){
        if ($path){
            return CONF_URL_TEST ."/".($path[0] == "/" ? mb_substr($path,1):$path);
        }
        return CONF_URL_TEST;
    }

    if ($path){
        return CONF_URL_BASE ."/".($path[0] == "/" ? mb_substr($path,1):$path);
    }
    return CONF_URL_BASE;

}

function url_back() :string
{
    return ($_SERVER["HTTP_REFERER"] ?? url());
}



function uri(string $uri = null) :string
{
    if($uri){
        return CONF_URL_TEST."/{$uri}";
    }
    return CONF_URL_TEST;
}

/**
 * @param string $url
 */
function redirect(string $url):void
{
    header("HTTP/1.1 302 Redirect");
    if (filter_var($url, FILTER_VALIDATE_URL)){
        header("Location: {$url}");
        exit;
    }
    if (filter_input(INPUT_GET, "route",FILTER_DEFAULT) != $url)
    {
        $location = url($url);
        header("Location: {$location}");
        exit;
    }

}

############
#  ASSETS  #
############

function themes(string $path = null): string
{
    if(strpos($_SERVER["HTTP_HOST"],"localhost") !== false){
        if ($path){
            return CONF_URL_TEST ."/themes/".CONF_VIEW_THEME. "/" .($path[0] == "/" ? mb_substr($path,1):$path);
        }
        return CONF_URL_TEST ."/themes/". CONF_VIEW_THEME;
    }

    if ($path){
        return CONF_URL_BASE ."/themes/".CONF_VIEW_THEME. "/" .($path[0] == "/" ? mb_substr($path,1):$path);
    }
    return CONF_URL_BASE ."/themes/". CONF_VIEW_THEME;

}
function image(string $image, int $width, int $height = null):string
{
    return url() ."/". (new \Source\Support\Thumb())->make($image,$width,$height);

}


############
#   CORE   #
############

/**
 * @return PDO
 */
function db() :PDO
{
    return \Source\Core\Connect::getInstance();
}

/**
 * @return \Source\Core\Message
 */
function message(): \Source\Core\Message
{
    return new \Source\Core\Message();
}

/**
 * @return \Source\Core\Session
 */
function session (): \Source\Core\Session
{
    return new \Source\Core\Session();
}


function date_fmt( string $date = "now", string  $format = "d/m/y H\hi"):string
{
    return (new DateTime($date))->format($format);
}
function date_fm_br( string $date = "now"):string
{
    return (new DateTime($date))->format(CONF_DATE_BR);
}


function format_money($value)
{
    $fmt = new NumberFormatter( 'pt_BR', NumberFormatter::CURRENCY );
    return $fmt->formatCurrency($value, "BRL");
}


