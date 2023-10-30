<?php
// require_once "IP/GeoCountry/geoip2.phar";
use \GeoIp2\Database\Reader;
function njt_eu_settings($country = null)
{
    $defaults = array(
        'is_enable' => '1',
        'options' => 'block', //block or work
        'conuntries_to_block' => array(''),
        'page_redirect' => '',
    );
    $setting = (array) get_option('njt_gdpr_eu', array());
    $setting = wp_parse_args($setting, $defaults);
    if ($setting['is_enable'] == '1') {
        if (is_null($country)) {
            $country = njt_ip_to_country();
        }
        $country = strtoupper($country);
        
        if ($setting['options'] == 'work') {
            return array('status' => 'work', 'country_is_blocked' => in_array($country, $setting['conuntries_to_block']));
        } else {
            $redirect_to = '';
            if (in_array($country, $setting['conuntries_to_block'])) {
                $redirect_to = $setting['page_redirect'];
            }
            return array('status' => 'block', 'redirect_to' => $redirect_to);
        }
    } else {
        return array('status' => 'block-all');
    }
}

function njt_get_ip_address(){
    foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key){
        if (array_key_exists($key, $_SERVER) === true){
            foreach (explode(',', $_SERVER[$key]) as $ip){
                $ip = trim($ip); // just to be safe

                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false){
                    return $ip;
                }
            }
        }
    }
}

if (!function_exists('njt_ip_to_country')) {
    function njt_ip_to_country($ip_check = null)
    {
        if (is_null($ip_check)) {
            $ip_check = njt_get_ip_address();
            // if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {$ip_check = $_SERVER['HTTP_X_FORWARDED_FOR'];
            //     } else { $ip_check = $_SERVER['REMOTE_ADDR'];
            //    }
        }
        if (is_null($ip_check) || empty($ip_check)) {
            return '';
        } else {
            // if(!class_exists('IP_Country_Fast')) {
            //     include(NJT_GDPR_DIR . "/src/IP_Country_Fast.php");
            // }
            //$ip = new IP_Country_Fast(NJT_GDPR_DIR . '/src/IP/Country/Fast/');
            $reader = new Reader(NJT_GDPR_DIR . '/src/IP/GeoCountry/GeoLite2-Country.mmdb');
            
            try {
                $record = $reader->country($ip_check);
                $ip = $record->country->isoCode;
            } catch (Exception $e) {
                $ip = null;
            }
            //var_dump($record);
            return $ip;
            //return $ip->inet_atocc($ip_check);
        }
    }
}
function njt_gdpr_all_countries($return_type = 'object')
{
    $countries = array(
        'AF' => __('Afghanistan', NJT_GDPR_I18N),
        'AL' => __('Albania', NJT_GDPR_I18N),
        'DZ' => __('Algeria', NJT_GDPR_I18N),
        'AR' => __('Argentina', NJT_GDPR_I18N),
        'AM' => __('Armenia', NJT_GDPR_I18N),
        'AU' => __('Australia', NJT_GDPR_I18N),
        'AT' => __('Austria', NJT_GDPR_I18N),
        'BH' => __('Bahrain', NJT_GDPR_I18N),
        'BD' => __('Bangladesh', NJT_GDPR_I18N),
        'ES' => __('Basque', NJT_GDPR_I18N),
        'BY' => __('Belarus', NJT_GDPR_I18N),
        'BE' => __('Belgium', NJT_GDPR_I18N),
        'BZ' => __('Belize', NJT_GDPR_I18N),
        'VE' => __('Bolivarian Republic of Venezuela', NJT_GDPR_I18N),
        'BO' => __('Bolivia', NJT_GDPR_I18N),
        'BR' => __('Brazil', NJT_GDPR_I18N),
        'BN' => __('Brunei Darussalam', NJT_GDPR_I18N),
        'BG' => __('Bulgaria', NJT_GDPR_I18N),
        'KH' => __('Cambodia', NJT_GDPR_I18N),
        'CA' => __('Canada', NJT_GDPR_I18N),
        //'29' => __('Caribbean', NJT_GDPR_I18N),
        'CL' => __('Chile', NJT_GDPR_I18N),
        'CO' => __('Colombia', NJT_GDPR_I18N),
        'CR' => __('Costa Rica', NJT_GDPR_I18N),
        'HR' => __('Croatia', NJT_GDPR_I18N),
        'AZ' => __('Cyrillic, Azerbaijan', NJT_GDPR_I18N),
        'BA' => __('Cyrillic, Bosnia and Herzegovina', NJT_GDPR_I18N),
        'MN' => __('Cyrillic, Mongolia', NJT_GDPR_I18N),
        'ME' => __('Cyrillic, Montenegro', NJT_GDPR_I18N),
        'RS' => __('Cyrillic, Serbia', NJT_GDPR_I18N),
        'CS' => __('Cyrillic, Serbia and Montenegro (Former', NJT_GDPR_I18N),
        'TJ' => __('Cyrillic, Tajikistan', NJT_GDPR_I18N),
        'UZ' => __('Cyrillic, Uzbekistan', NJT_GDPR_I18N),
        'CY' => __('Cyprus', NJT_GDPR_I18N),
        'CZ' => __('Czech Republic', NJT_GDPR_I18N),
        'DK' => __('Denmark', NJT_GDPR_I18N),
        'DO' => __('Dominican Republic', NJT_GDPR_I18N),
        'EC' => __('Ecuador', NJT_GDPR_I18N),
        'EG' => __('Egypt', NJT_GDPR_I18N),
        'SV' => __('El Salvador', NJT_GDPR_I18N),
        'EE' => __('Estonia', NJT_GDPR_I18N),
        'ET' => __('Ethiopia', NJT_GDPR_I18N),
        'FO' => __('Faroe Islands', NJT_GDPR_I18N),
        'FI' => __('Finland', NJT_GDPR_I18N),
        'MK' => __('Former Yugoslav Republic of Macedonia', NJT_GDPR_I18N),
        'FR' => __('France', NJT_GDPR_I18N),
        'GE' => __('Georgia', NJT_GDPR_I18N),
        'DE' => __('Germany', NJT_GDPR_I18N),
        'GR' => __('Greece', NJT_GDPR_I18N),
        'GL' => __('Greenland', NJT_GDPR_I18N),
        'GT' => __('Guatemala', NJT_GDPR_I18N),
        'HN' => __('Honduras', NJT_GDPR_I18N),
        'HU' => __('Hungary', NJT_GDPR_I18N),
        'IS' => __('Iceland', NJT_GDPR_I18N),
        'IN' => __('India', NJT_GDPR_I18N),
        'ID' => __('Indonesia', NJT_GDPR_I18N),
        'IR' => __('Iran', NJT_GDPR_I18N),
        'IQ' => __('Iraq', NJT_GDPR_I18N),
        'IE' => __('Ireland', NJT_GDPR_I18N),
        'PK' => __('Islamic Republic of Pakistan', NJT_GDPR_I18N),
        'IL' => __('Israel', NJT_GDPR_I18N),
        'IT' => __('Italy', NJT_GDPR_I18N),
        'JM' => __('Jamaica', NJT_GDPR_I18N),
        'JP' => __('Japan', NJT_GDPR_I18N),
        'JO' => __('Jordan', NJT_GDPR_I18N),
        'KZ' => __('Kazakhstan', NJT_GDPR_I18N),
        'KE' => __('Kenya', NJT_GDPR_I18N),
        'KR' => __('Korea', NJT_GDPR_I18N),
        'KW' => __('Kuwait', NJT_GDPR_I18N),
        'KG' => __('Kyrgyzstan', NJT_GDPR_I18N),
        'LA' => __('Lao P.D.R.', NJT_GDPR_I18N),
        'NG' => __('Latin, Nigeria', NJT_GDPR_I18N),
        'LV' => __('Latvia', NJT_GDPR_I18N),
        'LB' => __('Lebanon', NJT_GDPR_I18N),
        'LY' => __('Libya', NJT_GDPR_I18N),
        'LI' => __('Liechtenstein', NJT_GDPR_I18N),
        'LT' => __('Lithuania', NJT_GDPR_I18N),
        'LU' => __('Luxembourg', NJT_GDPR_I18N),
        'MY' => __('Malaysia', NJT_GDPR_I18N),
        'MV' => __('Maldives', NJT_GDPR_I18N),
        'MT' => __('Malta', NJT_GDPR_I18N),
        'MX' => __('Mexico', NJT_GDPR_I18N),
        'MC' => __('Monaco', NJT_GDPR_I18N),
        'MA' => __('Morocco', NJT_GDPR_I18N),
        'NP' => __('Nepal', NJT_GDPR_I18N),
        'NL' => __('Netherlands', NJT_GDPR_I18N),
        'NZ' => __('New Zealand', NJT_GDPR_I18N),
        'NI' => __('Nicaragua', NJT_GDPR_I18N),
        'NO' => __('Norway', NJT_GDPR_I18N),
        'OM' => __('Oman', NJT_GDPR_I18N),
        'PA' => __('Panama', NJT_GDPR_I18N),
        'PY' => __('Paraguay', NJT_GDPR_I18N),
        'PE' => __('Peru', NJT_GDPR_I18N),
        'PH' => __('Philippines', NJT_GDPR_I18N),
        'PL' => __('Poland', NJT_GDPR_I18N),
        'PT' => __('Portugal', NJT_GDPR_I18N),
        'CN' => __('PRC', NJT_GDPR_I18N),
        'PR' => __('Puerto Rico', NJT_GDPR_I18N),
        'QA' => __('Qatar', NJT_GDPR_I18N),
        'RO' => __('Romania', NJT_GDPR_I18N),
        'RU' => __('Russia', NJT_GDPR_I18N),
        'RW' => __('Rwanda', NJT_GDPR_I18N),
        'SA' => __('Saudi Arabia', NJT_GDPR_I18N),
        'SN' => __('Senegal', NJT_GDPR_I18N),
        'SG' => __('Simplified, Singapore', NJT_GDPR_I18N),
        'SK' => __('Slovakia', NJT_GDPR_I18N),
        'SI' => __('Slovenia', NJT_GDPR_I18N),
        'ZA' => __('South Africa', NJT_GDPR_I18N),
        'ES-2' => __('Spain', NJT_GDPR_I18N),
        'LK' => __('Sri Lanka', NJT_GDPR_I18N),
        'SE' => __('Sweden', NJT_GDPR_I18N),
        'CH' => __('Switzerland', NJT_GDPR_I18N),
        'SY' => __('Syria', NJT_GDPR_I18N),
        'TH' => __('Thailand', NJT_GDPR_I18N),
        'HK' => __('Traditional, Hong Kong S.A.R.', NJT_GDPR_I18N),
        'MO' => __('Traditional, Macao S.A.R.', NJT_GDPR_I18N),
        'TW' => __('Traditional, Taiwan', NJT_GDPR_I18N),
        'TT' => __('Trinidad and Tobago', NJT_GDPR_I18N),
        'TN' => __('Tunisia', NJT_GDPR_I18N),
        'TR' => __('Turkey', NJT_GDPR_I18N),
        'TM' => __('Turkmenistan', NJT_GDPR_I18N),
        'AE' => __('U.A.E.', NJT_GDPR_I18N),
        'UA' => __('Ukraine', NJT_GDPR_I18N),
        'GB' => __('United Kingdom', NJT_GDPR_I18N),
        'US' => __('United States', NJT_GDPR_I18N),
        'UY' => __('Uruguay', NJT_GDPR_I18N),
        'VN' => __('Vietnam', NJT_GDPR_I18N),
        'YE' => __('Yemen', NJT_GDPR_I18N),
        'ZW' => __('Zimbabwe', NJT_GDPR_I18N),
    );
    $countries = apply_filters('njt_gdpr_countries', $countries);
    if ($return_type == 'object') {
        return $countries;
    } else {
        $return = array();
        foreach ($countries as $k => $v) {
            $return[] = array('id' => $k, 'text' => $v);
        }
        return $return;
    }
}
function njt_gdpr_get_permission()
{
    $arr = array('cookie' => '0', 'fb' => '0', 'gg' => '0');
    $current_user_id = get_current_user_id();
    
    if ($current_user_id == 0) {
        if (isset($_COOKIE['njt_gdpr_allow_permissions'])) {
            $cookie = json_decode(base64_decode($_COOKIE['njt_gdpr_allow_permissions']), true);
            
            if (isset($cookie['cookie'])) {
                $arr['cookie'] = njt_gdpr_maybe_sanitize_array($cookie['cookie']);
            }
            if (isset($cookie['fb'])) {
                $arr['fb'] = njt_gdpr_maybe_sanitize_array($cookie['fb']);
            }
            if (isset($cookie['gg'])) {
                $arr['gg'] = njt_gdpr_maybe_sanitize_array($cookie['gg']);
            }
        }
    } else {
        $user_meta = get_user_meta($current_user_id, 'njt_gdpr_allow_permissions', true);
        if (is_array($user_meta)) {
            if (isset($user_meta['cookie'])) {
                $arr['cookie'] = njt_gdpr_maybe_sanitize_array($user_meta['cookie']);
            }
            if (isset($user_meta['fb'])) {
                $arr['fb'] = njt_gdpr_maybe_sanitize_array($user_meta['fb']);
            }
            if (isset($user_meta['gg'])) {
                $arr['gg'] = njt_gdpr_maybe_sanitize_array($user_meta['gg']);
            }
        }
    }
    return $arr;
}
function njt_gdpr_maybe_sanitize_array($var)
{
    if (is_array($var)) {
        return array_map('njt_gdpr_maybe_sanitize_array', $var);
    } else {
        return is_scalar($var) ? sanitize_text_field($var) : $var;
    }
}
function njt_get_client_ip() {
    //source https://stackoverflow.com/a/15699240/3057774
    // $ipaddress = '';
    // if (getenv('HTTP_CLIENT_IP'))
    //     $ipaddress = getenv('HTTP_CLIENT_IP');
    // else if(getenv('HTTP_X_FORWARDED_FOR'))
    //     $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    // else if(getenv('HTTP_X_FORWARDED'))
    //     $ipaddress = getenv('HTTP_X_FORWARDED');
    // else if(getenv('HTTP_FORWARDED_FOR'))
    //     $ipaddress = getenv('HTTP_FORWARDED_FOR');
    // else if(getenv('HTTP_FORWARDED'))
    //    $ipaddress = getenv('HTTP_FORWARDED');
    // else if(getenv('REMOTE_ADDR'))
    //     $ipaddress = getenv('REMOTE_ADDR');
    // else
    //     $ipaddress = 'UNKNOWN';
    // return $ipaddress;
    $ipaddress = '';
    if (isset($_SERVER['HTTP_CLIENT_IP']))
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_X_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if(isset($_SERVER['REMOTE_ADDR']))
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}
function njt_get_transient_key($key) {
    return $key . '_' . njt_get_client_ip();
}
