--TEST--
Go closure with args

--FILE--
<?php
use \Go\Scheduler;

function subtc($seq){
    echo "SUB-TC: #$seq\n";
}

//str argument
go(function($str){
	subtc(2);
	echo "Hello $str\n";
}, "World");

//str var argument
$str = 'World';
go(function($str){
	subtc(3);
	echo "Hello $str\n";
}, $str);

//str reference argument
go(function(&$str){
	subtc(4);
	echo "Hello $str\n";
}, $str);

$str = "Tony";
//str reference argument modified
go(function(&$str){
	subtc(5);
	echo "Hello $str\n";
	$str = "$str Hello\n";
}, $str);

subtc(1);
echo $str .PHP_EOL;
Scheduler::join();
subtc(6);
echo $str;

//int argument
go(function($i){
	subtc(7);
	echo "Hello $i\n";
}, 100);

//int var argument
$i = 100;
go(function($i){
	subtc(8);
	echo "Hello $i\n";
}, $i);

//int reference argument
go(function(&$i){
	subtc(9);
	echo "Hello $i\n";
}, $i);

//int reference argument, modified
go(function(&$i){
	subtc(10);
	echo "Hello $i\n";
	$i = -99887766;
}, $i);

subtc(6.1);
echo "$i\n";
Scheduler::join();
subtc(10.1);
echo "$i\n";

class TestClass{
	private $value;
	public function __construct($value){
		$this->value = $value;
	}
	public function say(){
		echo $this->value . PHP_EOL;
	}
};

$obj = new TestClass("this is test class");

//array argument

go(
   function($arr){
	   subtc(11);
	   var_dump($arr);
	   $arr[2]->say();
   }, 
   ["key0"=>"0", 
    "key1"=>1, 
	"subArray"=>
	    ["subsubArray"=>
		    [ 1, 2, "3", false, null],  
	     "subkey0"=>0, 
		 "subkey1"=>"subvalue1", 
	    ],
    null,
    false,
    $obj,
   ]
);

//array var argument
$arr = 
   ["key0"=>"0", 
    "key1"=>1, 
	"subArray"=>
	    ["subsubArray"=>
		    [ 1, 2, "3", false, null],  
	     "subkey0"=>0, 
		 "subkey1"=>"subvalue1", 
	    ],
    null,
    false,
    $obj,
   ];

go(function($arr){
	subtc(12);
	var_dump($arr);
	$arr[2]->say();
}, $arr);

//array reference argument

go(function(&$arr){
	subtc(13);
	var_dump($arr);
	$arr[2]->say();
}, $arr);

//array reference argument, modified
go(function(&$arr){
	subtc(14);
	$arr["subArray"]["subsubArray"][1] = "replaced";
}, $arr);


Scheduler::join();
subtc(15);
var_dump($arr);

$i = 1;
$bool = false;
$null = null;
$string = "string";
$obj = new TestClass("test class obj");
$arr = [1,2,3,4];

ini_set('memory_limit', '200M');

go(function($i, $bool, $null, $string, $obj, $arr, &$i1, &$bool1, &$null1, &$string1, &$obj1, &$arr1){
	subtc(16);
	
	echo $i .PHP_EOL;
	echo $bool .PHP_EOL;
	echo $null .PHP_EOL;
	echo $string .PHP_EOL; 
	var_dump($obj);
	var_dump ($arr);
	
	echo $i1 .PHP_EOL;
	echo $bool1 .PHP_EOL;
	echo $null1 .PHP_EOL;
	echo $string1 .PHP_EOL;
	var_dump($obj1);
	var_dump ($arr1);
	
}, 1, true, null, "string", $obj, $arr, $i, $bool, $null, $string, $obj, $arr);

Scheduler::join();

?>
--EXPECT--
SUB-TC: #1
Tony
SUB-TC: #2
Hello World
SUB-TC: #3
Hello World
SUB-TC: #4
Hello World
SUB-TC: #5
Hello Tony
SUB-TC: #6
Tony Hello
SUB-TC: #6.1
100
SUB-TC: #7
Hello 100
SUB-TC: #8
Hello 100
SUB-TC: #9
Hello 100
SUB-TC: #10
Hello 100
SUB-TC: #10.1
-99887766
SUB-TC: #11
array(6) {
  ["key0"]=>
  string(1) "0"
  ["key1"]=>
  int(1)
  ["subArray"]=>
  array(3) {
    ["subsubArray"]=>
    array(5) {
      [0]=>
      int(1)
      [1]=>
      int(2)
      [2]=>
      string(1) "3"
      [3]=>
      bool(false)
      [4]=>
      NULL
    }
    ["subkey0"]=>
    int(0)
    ["subkey1"]=>
    string(9) "subvalue1"
  }
  [0]=>
  NULL
  [1]=>
  bool(false)
  [2]=>
  object(TestClass)#1 (1) {
    ["value":"TestClass":private]=>
    string(18) "this is test class"
  }
}
this is test class
SUB-TC: #12
array(6) {
  ["key0"]=>
  string(1) "0"
  ["key1"]=>
  int(1)
  ["subArray"]=>
  array(3) {
    ["subsubArray"]=>
    array(5) {
      [0]=>
      int(1)
      [1]=>
      int(2)
      [2]=>
      string(1) "3"
      [3]=>
      bool(false)
      [4]=>
      NULL
    }
    ["subkey0"]=>
    int(0)
    ["subkey1"]=>
    string(9) "subvalue1"
  }
  [0]=>
  NULL
  [1]=>
  bool(false)
  [2]=>
  object(TestClass)#1 (1) {
    ["value":"TestClass":private]=>
    string(18) "this is test class"
  }
}
this is test class
SUB-TC: #13
array(6) {
  ["key0"]=>
  string(1) "0"
  ["key1"]=>
  int(1)
  ["subArray"]=>
  array(3) {
    ["subsubArray"]=>
    array(5) {
      [0]=>
      int(1)
      [1]=>
      int(2)
      [2]=>
      string(1) "3"
      [3]=>
      bool(false)
      [4]=>
      NULL
    }
    ["subkey0"]=>
    int(0)
    ["subkey1"]=>
    string(9) "subvalue1"
  }
  [0]=>
  NULL
  [1]=>
  bool(false)
  [2]=>
  object(TestClass)#1 (1) {
    ["value":"TestClass":private]=>
    string(18) "this is test class"
  }
}
this is test class
SUB-TC: #14
SUB-TC: #15
array(6) {
  ["key0"]=>
  string(1) "0"
  ["key1"]=>
  int(1)
  ["subArray"]=>
  array(3) {
    ["subsubArray"]=>
    array(5) {
      [0]=>
      int(1)
      [1]=>
      string(8) "replaced"
      [2]=>
      string(1) "3"
      [3]=>
      bool(false)
      [4]=>
      NULL
    }
    ["subkey0"]=>
    int(0)
    ["subkey1"]=>
    string(9) "subvalue1"
  }
  [0]=>
  NULL
  [1]=>
  bool(false)
  [2]=>
  object(TestClass)#1 (1) {
    ["value":"TestClass":private]=>
    string(18) "this is test class"
  }
}
SUB-TC: #16
1
1

string
object(TestClass)#5 (1) {
  ["value":"TestClass":private]=>
  string(14) "test class obj"
}
array(4) {
  [0]=>
  int(1)
  [1]=>
  int(2)
  [2]=>
  int(3)
  [3]=>
  int(4)
}
1


string
object(TestClass)#5 (1) {
  ["value":"TestClass":private]=>
  string(14) "test class obj"
}
array(4) {
  [0]=>
  int(1)
  [1]=>
  int(2)
  [2]=>
  int(3)
  [3]=>
  int(4)
}
