<?php namespace TCG;

class Reins {

	// the lua script to be run in redis
	protected static $script =<<<EOS
local key      = KEYS[1];
local since    = ARGV[1];
local interval = ARGV[2];
local now      = since + interval;

-- forget requests that are older than the interval
redis.call("ZREMRANGEBYSCORE", key, 0, since);

-- record this request
redis.call("ZADD", key, now, now);

-- release memory after the interval
redis.call("EXPIRE", key, interval/1000000);

-- count all requests from the interval
return redis.call("ZCOUNT", key, "-inf", "+inf");
EOS;

	public function __construct($client, $interval = 1000){
		$this->client = $client;
		$this->interval = $interval;
	}

	public function grab($id, $interval = null) {

		// use default interval
		if($interval === null)
			$interval = $this->interval;

		// put the interval in µs
		$interval = $interval * 1000;
		$since = microtime(true) - $interval;
		$key = $id . '::' . $interval;

		// run the script in redis
		return $this->client->eval(self::$script, 1, $key, $since, $interval);
	}
}
