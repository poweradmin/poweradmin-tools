<?php

include 'functions.php';
include 'config.php';

$max_domains = 10;	// define how many domain you want to generate

if ($dsn['host'] == '' || $dsn['name'] == '' || $dsn['user'] == '' || $dsn['pass'] == '') {
	echo 'Please define settings for database connection'.PHP_EOL;
	exit;
}

if ($soa['ns1'] == '' || $soa['hostmaster'] == '') {
	echo 'Please define settings for SOA record'.PHP_EOL;
	exit;
}

$link = db_connect($db_type, $dsn);

$date = get_current_date();
$serial = get_serial();

for ($index = 1; $index <= $max_domains; $index++) {
	$domain = get_random_domain();
	$domain_id = add_domain($db_type, $link, $domain);

	$record_array = array(
		'id' => $domain_id,
		'name' => $domain,
		'type' => 'SOA',
		'ns1' => $soa['ns1'],
		'hostmaster' => $soa['hostmaster'],
		'serial' => $serial,
		'ttl' => $soa['ttl'],
		'prio' => $soa['prio'], 
		'date' => $date,
	);

	add_record($db_type, $link, $record_array);

	add_zone($db_type, $link, $domain_id);
}

db_close($db_type, $link);
