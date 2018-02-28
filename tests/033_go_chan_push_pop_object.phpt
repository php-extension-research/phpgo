--TEST--
Go Chan push pop object

--FILE--
<?php
use \go\Chan;
use \go\Scheduler;

function subtc($seq){
    echo "SUB-TC: #$seq\n";
}

class ObjectClass{
	private $name;
	
	public function __construct($name){
		$this->name = $name;
	}
	public function say(){
		echo "I am {$this->name}!\n";
	}
}

subtc(1);
$ch = new Chan(1);

go(function($ch){
	echo "push\n";
	$v = new ObjectClass("testObject");
	$v->say();
	$ch->push($v);
	echo "pushed:";
	var_dump($v);
},$ch);

go(function($ch){
	echo "pop\n";
	$v = $ch->pop();
	echo "popped:";
	var_dump($v);
	$v->say();
},$ch);

Scheduler::RunJoinAll();

?>
--EXPECT--
SUB-TC: #1
push
I am testObject!
pushed:object(ObjectClass)#4 (1) {
  ["name":"ObjectClass":private]=>
  string(10) "testObject"
}
pop
popped:object(ObjectClass)#4 (1) {
  ["name":"ObjectClass":private]=>
  string(10) "testObject"
}
I am testObject!


