--TEST--
Go Select read write default timer

--FILE--
<?php
use \go\Chan;
use \go\Timer;
use \go\Scheduler;

function subtc($seq){
    echo "SUB-TC: #$seq\n";
}

subtc(1);
$done = new Chan(1);

go(function() use($done){

	$begin = time();
	
	$sel = select(
		_case(Timer::After(1000*1000), "->", $dummy, function($v) use($done, $begin){
			assert( $v===1 );
			assert( time()-$begin == 1 );
			
			$done->push("done");
		})
	);
	
	$ret = $sel->loop($done);
	if(assert($ret=="done")){
		echo "success\n";
	}

});

Scheduler::RunJoinAll();

subtc(2);
$done = new Chan(1);

go(function() use($done){

	$begin = microtime(true); $i=0;
	
	//echo $begin;
	
	$sel = select(
		_case(Timer::Tick(100*1000), "->", $dummy, function($v) use($done, &$begin, &$i){
			assert( $v===1 );
			assert( ($diff = abs( microtime(true)-$begin-0.1 )) < 0.005 );
			//echo $diff . " ";
			$begin = microtime(true);
			
			$i++;
		}),
		_default(function(){
			usleep(1*1000);
		})
	);
	 
	while($i<10){
		$sel = $sel->select();
	}
	
	echo "test completed\n";
	
	exit;

});

Scheduler::RunJoinAll();

?>
--EXPECT--
SUB-TC: #1
success
SUB-TC: #2
test completed

