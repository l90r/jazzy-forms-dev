<?php

require('../../jazzy-forms/core/Template_Parser.php');
require('../../jazzy-forms/core/Graph.php');
require('../../jazzy-forms/core/Parser.php');
require('../../jazzy-forms/core/Tokenizer.php');

class Graph_Test extends PHPUnit_Framework_TestCase {
	function setUp() {
	}
	
	function tearDown() {
	}

	function get_graph_from_elements($elements) {
		return jzzf_get_graph((object) array("elements"=>$elements));
	}

    function test_empty() {
        $graph = $this->get_graph_from_elements(array());
		$this->assertEquals(array(), $graph->data);
		$this->assertEquals(array(), $graph->types);
		$this->assertEquals(array(), $graph->dependencies);
		$this->assertEquals(array(), $graph->formulas);
		$this->assertEquals(array(), $graph->templates);
		$this->assertEquals(array(), $graph->params);
		$this->assertEquals(array(), $graph->required);
		$this->assertEquals(null, $graph->email);
    }
	
	function test_simple() {
		$elements = array(
			(object) array('name'=> 'num', 'type'=> 'n', 'value' => 10)
		);
		$graph = $this->get_graph_from_elements($elements);
		$this->assertEquals(array('num'=>'n'), $graph->types);
		$this->assertEquals(array(), $graph->dependencies);
		$this->assertEquals(array(), $graph->formulas);
	}

	function test_single_dependency() {
		$elements = array(
			(object) array('name'=> 'num', 'type'=> 'n', 'value' => 10),
			(object) array('name'=> 'result', 'type'=> 'f', 'formula' => 'num*2')
		);
		$graph = $this->get_graph_from_elements($elements);
		$this->assertEquals(array('num'=>'n', 'result'=>'f'), $graph->types);
		$this->assertEquals(array('num'=>array('result')), $graph->dependencies);
		$this->assertEquals(array('result'=>array('o', '*', array('v', 'num'), array('n', '2'))), $graph->formulas);
	}
	
	function test_repeated_dependency() {
		$elements = array(
			(object) array('name'=> 'num', 'type'=> 'n', 'value' => 10),
			(object) array('name'=> 'result', 'type'=> 'f', 'formula' => 'num*num*num')
		);
		$graph = $this->get_graph_from_elements($elements);
		$this->assertEquals(array('num'=>'n', 'result'=>'f'), $graph->types);
		$this->assertEquals(array('num'=>array('result')), $graph->dependencies);
		$this->assertEquals(array('result'=>array('o', '*', array('v', 'num'), array('o', '*', array('v', 'num'), array('v', 'num')))), $graph->formulas);
	}

	function test_single_dependency_ugly_characters() {
		$elements = array(
			(object) array('name'=> 'Num1', 'type'=> 'n', 'value' => 10),
			(object) array('name'=> 'out_put', 'type'=> 'f', 'formula' => 'nuM1*2')
		);
		$graph = $this->get_graph_from_elements($elements);
		$this->assertEquals(array('num1'=>'n', 'out_put'=>'f'), $graph->types);
		$this->assertEquals(array('num1'=>array('out_put')), $graph->dependencies);
		$this->assertEquals(array('out_put'=>array('o', '*', array('v', 'num1'), array('n', '2'))), $graph->formulas);
	}
	
	function test_multiple_dependency() {
		$elements = array(
			(object) array('name'=> 'num', 'type'=> 'n', 'value' => 10),
			(object) array('name'=> 'subtotal', 'type'=> 'f', 'formula' => 'num*2'),
			(object) array('name'=> 'total', 'type'=> 'f', 'formula' => 'subtotal+1')
		);
		$graph = $this->get_graph_from_elements($elements);
		$this->assertEquals(array('num'=>'n', 'subtotal'=>'f', 'total'=>'f'), $graph->types);
		$this->assertEquals(array('num'=>array('subtotal', 'total')), $graph->dependencies);
		$this->assertEquals(
			array(
				'subtotal' => array('o', '*', array('v', 'num'), array('n', '2')),
				'total'=> array('o', '+', array('v', 'subtotal'), array('n', '1'))),
			$graph->formulas
		);
	}

	function test_dependency_no_input() {
		$elements = array(
			(object) array('name'=> 'num', 'type'=> 'f', 'formula' => "10"),
			(object) array('name'=> 'subtotal', 'type'=> 'f', 'formula' => 'num*2'),
			(object) array('name'=> 'total', 'type'=> 'f', 'formula' => 'subtotal+1')
		);
		$graph = $this->get_graph_from_elements($elements);
		$this->assertEquals(array('num'=>'f', 'subtotal'=>'f', 'total'=>'f'), $graph->types);
		$this->assertEquals(array(), $graph->dependencies);
		$this->assertEquals(
			array(
				'num' => array('n', 10),
				'subtotal' => array('o', '*', array('v', 'num'), array('n', '2')),
				'total'=> array('o', '+', array('v', 'subtotal'), array('n', '1'))),
			$graph->formulas
		);
	}

	function test_dependency_circular_dependency() {
		$elements = array(
			(object) array('name'=> 'num', 'type'=> 'n', 'value' => 10),
			(object) array('name'=> 'subtotal', 'type'=> 'f', 'formula' => 'num+total'),
			(object) array('name'=> 'total', 'type'=> 'f', 'formula' => 'subtotal+1')
		);
		$graph = $this->get_graph_from_elements($elements);
		$this->assertEquals(array('num'=>'n', 'subtotal'=>'f', 'total'=>'f'), $graph->types);
		$this->assertEquals(array('num'=>array('subtotal', 'total')), $graph->dependencies);
		$this->assertEquals(
			array(
				'subtotal' => array('o', '+', array('v', 'num'), array('v', 'total')),
				'total'=> array('o', '+', array('v', 'subtotal'), array('n', '1'))),
			$graph->formulas
		);
	}
	
	function test_types_and_data() {
		$elements = array(
			(object) array('name'=> 'eins', 'type'=> 'n', 'value'=> 10),
			(object) array('name'=> 'zwei', 'type'=> 'f','formula' => '3*x'),
			(object) array(
				'name'=> 'drei',
				'type'=> 'r',
				'options' => array(
					(object) array('value' => 10),
					(object) array('value' => 20),
					(object) array('value' => 30),
				)
			),
			(object) array(
				'name'=> 'vier',
				'type'=> 'd',
				'options' => array(
					(object) array('value' => 1),
					(object) array('value' => 2),
					(object) array('value' => 3),
				)
			),
			(object) array('name'=> 'fuenf', 'type'=> 'c', 'value'=>10, 'value2'=>5)
		);
		$graph = $this->get_graph_from_elements($elements);
		$types = $graph->types;
		$data = $graph->data;
		$this->assertEquals(5, count($types));
		$this->assertEquals(4, count($data));
		$this->assertEquals('n', $types['eins']);
		$this->assertEquals('f', $types['zwei']);
		$this->assertEquals('r', $types['drei']);
		$this->assertEquals('d', $types['vier']);
		$this->assertEquals('c', $types['fuenf']);
		$this->assertEquals(10, $data['eins']);
		$this->assertArrayNotHasKey('zwei', $data);
		$this->assertEquals(array(10,20,30), $data['drei']);
		$this->assertEquals(array(1,2,3), $data['vier']);
		$this->assertEquals(array(5,10), $data['fuenf']);
	}

	function test_params() {
		$elements = array(
			(object) array('name'=> 'eins', 'type'=> 'n', 'value'=> 10, 'fixed'=>false, 'decimals'=>3, 'zeros'=>0, 'prefix'=>'', 'postfix'=>'', 'thousands'=>'', 'point'=>'.'),
			(object) array('name'=> 'zwei', 'type'=> 'f','formula' => '3*x',  'fixed'=>false, 'decimals'=>3, 'zeros'=>0, 'prefix'=>'', 'postfix'=>'', 'thousands'=>'', 'point'=>'.')
		);
		$graph = $this->get_graph_from_elements($elements);
		$types = $graph->types;
		$params = $graph->params;
		$this->assertEquals(2, count($types));
		$this->assertEquals(1, count($params));
		$this->assertArrayHasKey('zwei', $params);
		$this->assertSame(false, $params['zwei']['fixed']);
		$this->assertSame(3, $params['zwei']['decimals']);
		$this->assertSame(0, $params['zwei']['zeros']);
		$this->assertSame('', $params['zwei']['prefix']);
		$this->assertSame('', $params['zwei']['postfix']);
		$this->assertSame('', $params['zwei']['thousands']);
		$this->assertSame('.', $params['zwei']['point']);
		$this->assertArrayNotHasKey('type', $params['zwei']);
		$this->assertArrayNotHasKey('value', $params['zwei']);
		$this->assertArrayNotHasKey('name', $params['zwei']);
		$this->assertArrayNotHasKey('formula', $params['zwei']);
	}
	
	function test_email() {
		$form = (object) array(
			"elements" => array(),
			"email" => (object) array(
				"to" => "User <{{user}}>",
				"from" => "Company <{{id}}>",
				"cc" =>  "User2 <{{user2}}>, <{{user}}>",
				"bcc" =>  "User3 <{{a+b}}>",
				"subject" => "Your inquiry {{count}}",
				"message" => "Here goes some {{price1+price2}}"
			)
		);
		$graph = jzzf_get_graph($form);
		$email = $graph->email;
		$this->assertEquals(json_decode('{
			"user": ["v", "user"],
			"id": ["v", "id"],
			"user2": ["v", "user2"],
			"_inline_0_bcc": ["o", "+", ["v", "a"], ["v", "b"]],
			"count": ["v", "count"],
			"_inline_0_message": ["o", "+", ["v", "price1"], ["v", "price2"]]
		}', true), $email);
	}

	function test_no_template() {
		$elements = array(
			(object) array('name'=> 'a', 'type'=> 't', 'title'=>'Pure text.')
		);
		$graph = $this->get_graph_from_elements($elements);
		$this->assertEquals(array(), $graph->templates);
	}

	function test_template_variable_only() {
		$elements = array(
			(object) array('name'=> 'a', 'type'=> 'n', 'title'=>'A', 'value'=>''),
			(object) array('name'=> 'out', 'type'=> 't', 'title'=>'{{a}}')
		);
		$graph = $this->get_graph_from_elements($elements);
		$templates = $graph->templates;
		$this->assertEquals(json_decode('{
			"out": [["v", "a"]]}', true), $graph->templates);
		$this->assertEquals(json_decode('{
			"a": ["out"]}', true), $graph->dependencies);
	}

	function test_template_mixed() {
		$elements = array(
			(object) array('name'=> 'a', 'type'=> 'n', 'title'=>'A', 'value'=>''),
			(object) array('name'=> 'out', 'type'=> 't', 'title'=>'The result is {{a}}')
		);
		$graph = $this->get_graph_from_elements($elements);
		$templates = $graph->templates;
		$this->assertEquals(json_decode('{
			"out": ["The result is ", ["v", "a"]]}', true), $graph->templates);
		$this->assertEquals(json_decode('{
			"a": ["out"]}', true), $graph->dependencies);
	}

    function test_required() {
        $elements = array(
   			(object) array('name'=> 'a', 'type'=> 'n', 'title'=>'A', 'value'=>''),
			(object) array('name'=> 'b', 'type'=> 'r', 'title'=>'B', 'options' => array(), 'required'=>true, 'missing'=>'Give me a Bee'),
			(object) array('name'=> 'c', 'type'=> 'd', 'title'=>'C', 'options' => array(), 'required'=>true, 'missing'=>'Give me a Sea')
        );
		$graph = $this->get_graph_from_elements($elements);
   		$this->assertEquals(array('b'=>'Give me a Bee', 'c'=>'Give me a Sea'), $graph->required);
    }
}

