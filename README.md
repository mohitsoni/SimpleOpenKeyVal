# SimpleOpenKeyVal

SimpleOpenKeyVal is a PHP library to interface with *[OpenKeyval.org](http://openkeyval.org/)*, which is a service for easily storing and retrieving key/value pairs via HTTP.

## Examples
### Simple Usage
	require_once('SimpleOpenKeyVal.php');
	$openKeyVal = new SimpleOpenKeyVal();	
	$key = 'foobar-name';	
	$res = $openKeyVal->set($key, 'John Doe');
	$val = $kv->get($key);	
	$res = $kv->del($key);

### Caching
To cache retrieved values locally, set the `cacheTime` argument while creating `SimpleOpenKeyVal` object. The values will be cached upto `cacheTime` seconds.
	require_once('SimpleOpenKeyVal.php');
	$openKeyVal = new SimpleOpenKeyVal($cacheTime=60);	
	$key = 'foobar-name';	
	$res = $openKeyVal->set($key, 'John Doe');
	$val = $kv->get($key);	
	$res = $kv->del($key);