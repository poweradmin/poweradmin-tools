<?php

include 'functions.php';

include 'config.php';

$max_domains = 1000; // define how many domains you want to add

if ($dsn['host'] == '' || $dsn['name'] == '' || $dsn['user'] == '' || $dsn['pass'] == '') {
	echo 'Please define settings for database connection'.PHP_EOL;
	exit;
}

if ($soa['ns1'] == '' || $soa['hostmaster'] == '') {
	echo 'Please define settings for SOA record'.PHP_EOL;
	exit;
}

echo 'Connecting to the database...' . PHP_EOL;
$link = db_connect($db_type, $dsn);
echo 'Connected to the database.' . PHP_EOL;

$date = get_current_date();
$serial = get_serial();

for ($index = 1; $index <= $max_domains; $index++) {
	echo 'Adding domain ' . $index . ' of ' . $max_domains . PHP_EOL;
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

    echo "S";
    add_record($db_type, $link, $record_array);

	$max_records = rand(1, 100); // define how many records you want to add for each domain
	for ($rindex = 1; $rindex < $max_records; $rindex++) {
		$subdomain = get_pronounceable_random_name();

        $type = get_random_type();
		$record_array = array(
			'id' => $domain_id,
			'name' => $subdomain,
			'type' => $type,
			'content' => get_random_content_by_type($type),
			'ttl' => $soa['ttl'],
			'prio' => $soa['prio'], 
			'date' => $date,
		);
        echo substr($type, 0, 1);
        add_record($db_type, $link, $record_array);
	}
    echo PHP_EOL; // Move to the next line after all records for a domain are added

    echo "Adding zone for domain: $domain" . PHP_EOL;
    add_zone($db_type, $link, $domain_id);

    echo PHP_EOL;
}

echo 'Closing the database connection...' . PHP_EOL;
db_close($db_type, $link);
echo 'Database connection closed.' . PHP_EOL;