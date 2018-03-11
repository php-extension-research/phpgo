--TEST--
Go Chan(1,copy) push pop

--FILE--
<?php
use \go\Chan;
use \go\Scheduler;

function subtc($seq){
    echo "SUB-TC: #$seq\n";
}

subtc(1);
$ch = new Chan(["capacity"=>1, "copy"=>true]);

go(function($ch){
	echo "push\n";
	$v = "abc";
	$ch->push($v);
	echo "pushed $v\n";
	$ch->push(100);
	echo "pushed 100\n";
	$ch->push(true);
	echo "pushed true\n";
	$ch->push(false);
	echo "pushed false\n";
},$ch);

go(function($ch){
	echo "pop\n";
	$v = $ch->pop();
	echo "popped:";
	var_dump($v);
	$v = $ch->pop();
	echo "popped:";
	var_dump($v);
	$v = $ch->pop();
	echo "popped:";
	var_dump($v);
	$v = $ch->pop();
	echo "popped:";
	var_dump($v);
},$ch);

Scheduler::RunJoinAll();

?>
--EXPECT--
SUB-TC: #1
push
pushed abc
pop
popped:string(3) "abc"
pushed 100
popped:int(100)
pushed true
popped:bool(true)
pushed false
popped:bool(false)

