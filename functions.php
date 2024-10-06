<?php

function db_connect($db_type, $dsn) {
	$link = null;

	if ($db_type == 'mysql') {
		$link = mysqli_connect($dsn['host'], $dsn['user'], $dsn['pass'])
			or die(mysqli_error($link));

		mysqli_select_db($link, $dsn['name']) or die(mysqli_error($link));
	} else if ($db_type == 'pgsql') {
		$link = pg_connect('host='.$dsn['host'].' dbname='.$dsn['name'].
			' user='.$dsn['user']. ' password='.$dsn['pass'])
			or die();
	} else {
		die('Unkown database type'.PHP_EOL);
	}

	return $link;
}

function db_close($db_type, $link) {
	if ($db_type == 'mysql') {
		mysqli_close($link);
	} else if ($db_type == 'pgsql') {
		pg_close($link);
	}
}

function get_random_name($len=6) {
	$chars = 'qwertyuiopasdfghjklzxcvbnm';
	$str = '';

	for ($i = 0; $i < $len; $i++) {
		$str .= $chars[rand(0, strlen($chars)-1)];
	}

	return $str;
}

function get_pronounceable_random_name($len = 6) {
    $consonants = 'bcdfghjklmnpqrstvwxyz';
    $vowels = 'aeiou';
    $name = '';

    for ($i = 0; $i < $len; $i++) {
        if ($i % 2 == 0) {
            $name .= $consonants[rand(0, strlen($consonants) - 1)];
        } else {
            $name .= $vowels[rand(0, strlen($vowels) - 1)];
        }
    }

    return $name;
}

function get_random_domain($tld='com') {
	return get_pronounceable_random_name().'.'.$tld;
}

function get_random_num($max=255) {
	return rand(0, $max);
}

function get_random_ip() {
	$triplet1 = get_random_num();
	$triplet2 = get_random_num();
	$triplet3 = get_random_num();
	$triplet4 = get_random_num();
	return "$triplet1.$triplet2.$triplet3.$triplet4"; 
}

function add_domain($db_type, $db_link, $domain) {
	$query = sprintf("INSERT INTO domains (name, type) VALUES ('%s', 'NATIVE')", $domain);
	
	if ($db_type == 'mysql') {
		mysqli_query($db_link, $query) or die(mysqli_error($db_link));
	
		$domain_id = mysqli_insert_id($db_link);
	} else if ($db_type == 'pgsql') {
		pg_query($db_link, $query) or die(pg_last_error($db_link));
		
		$result = pg_query($db_link, 'SELECT lastval()') or die(pg_last_error($db_link));
		$insert_row = pg_fetch_row($result);
		$domain_id = $insert_row[0];
	}
	
	return $domain_id;
}

function add_record($db_type, $db_link, $record_array) {
	if ($record_array['type'] == 'SOA') {
		$record_array['content'] = $record_array['ns1'].' '.$record_array['hostmaster'].' '.$record_array['serial'].' 28800 7200 604800 86400';
	}
	
	$query = sprintf("INSERT INTO records (domain_id, name, type, content, ttl, prio) VALUES (%d, '%s', '%s', '%s', %d, %d)", $record_array['id'], $record_array['name'], $record_array['type'], $record_array['content'], $record_array['ttl'], $record_array['prio']);
	
	if ($db_type == 'mysql') {
		mysqli_query($db_link, $query) or die(mysqli_error($db_link));
	} else if ($db_type == 'pgsql') {
		pg_query($db_link, $query) or die(pg_last_error($db_link));
	}
}

function add_zone($db_type, $db_link, $domain_id) {
	$query = sprintf("INSERT INTO zones (domain_id, owner, comment, zone_templ_id) VALUES (%d, 1, NULL, 0)", $domain_id);
	
	if ($db_type == 'mysql') {
		mysqli_query($db_link, $query) or die(mysqli_error($db_link));
	} else if ($db_type == 'pgsql') {
		pg_query($db_link, $query) or die(pg_last_error($db_link));
	}
}

function get_current_date() {
	date_default_timezone_set('UTC');	
	return time();
}

function get_serial($index='00') {
	return date("Ymd").$index;
}

function get_random_type()
{
	$types = array('A', 'AAAA', 'CNAME', 'LOC', 'MX', 'PTR', 'SPF', 'SRV', 'TXT');
	return $types[array_rand($types)];
}

function get_random_content_by_type($type)
{
    switch ($type) {
        case 'AAAA':
        case 'A':
            return get_random_ip();
        case 'MX':
        case 'PTR':
        case 'CNAME':
            return get_random_domain();
        case 'LOC':
            return '53 32 12.000 N 4 54 23.000 W 0.00m 1m 10000m 10m';
        case 'SPF':
            return 'v=spf1 a mx -all';
        case 'SRV':
            return '0 5 80 server.example.com';
        case 'TXT':
            return 'Hello world!';
        default:
            return '';
    }
}
