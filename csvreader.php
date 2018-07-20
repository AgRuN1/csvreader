<?php
/*
	CSV Reader Library
	Web Site - http://inmtoo.com
	Owner/Investor - https://vk.com/maxsharun
	Developer - https://vk.com/agrun1
	License - MIT
*/
class CSVReader implements Iterator{
	protected $is_head; // either there are header or aren't
	protected $heads; // Array of headers
	protected $line; // Number of current line
	protected $current_line; // Current line
	protected $length = 0; // Length lines
	protected $pointer = 0; // Pointer to begin of data
	public $sep; // Separate symbol
	/*
		Get: path to file, separator char and 
		either there are headers or aren't
	*/
	public function __construct($file, $sep = ',', $is_head = true){
		if(file_exists($file)){
			$this->fp = fopen($file, 'r');
		}else{
			throw new Exception('File not found');
		}
		if(!$this->fp)
			throw new Exception('File are not opened');
		$this->sep = $sep;
		$this->is_head = (bool)  $is_head;
		if($is_head){
			$head = $this->read_line();
			$this->heads = $this->get_array($head);
			$this->length = count($this->heads);
			$this->pointer = ftell($this->fp);
		}
	}
	/*
		Close the file
	*/
	public function __destruct(){
		fclose($this->fp);
	}
	/*
		Read and return a line from the file
	*/
	protected function read_line(){
		$line = fgets($this->fp);
		$this->line++;
		return trim($line);
	}

	public function headers(){
		if(!$this->is_head)
			throw new Exception('There are not headers');
		return $this->heads;
	}
	/*
		Get a line from file
		Return an array separated by separator char
	*/
	protected function get_array($line){
		$arr = array();
		$stack = array();
		$quotes = array('"', "'");
		$element = '';
		$chars = str_split($line);
		foreach($chars as $char){
			if($char == ';' && !count($stack)){
				array_push($arr, trim($element, "\'\""));
				$element = '';
				continue;
			}
			if(in_array($char, $quotes)){
				if(count($stack) && $stack[0] == $char)
					array_shift($stack);
				else
					array_unshift($stack, $char);
			}
			$element .= $char;
		}
		if(count($stack){
			throw new Exception('There are not closing quote at the line number '
			. $this->line);
		}
		array_push($arr, trim($element, "\'\""));
		return $arr;
	}
	/*
		Return the current line
	*/
	public function get_line(){
		return $this->current_line;
	}
	/*
		Check either file ended or not
	*/
	public function valid(){
		return $this->current_line;
	}
	/*
		Return next array
	*/
	public function next(){
		$this->current_line = $this->read_line();
	}
	/* 
		Return the current value 
	*/
	public function current(){
		$arr = $this->get_array($this->current_line);
		if($this->length === 0)
			$this->length = count($arr);
		else if(count($arr) != $this->length)
			throw new Exception('Invalid count of items at the line number ' . $this->line);
		if($this->is_head){
			$result = array();
			for($i = 0, $len = count($arr); $i < $len; $i++){
				$key = $this->heads[$i];
				$value = $arr[$i];
				$result[$key] = $value;
			}
			return $result;
		}else{
			return $arr;
		}
	}
	/*
		Start iteration from begin
	*/
	public function rewind(){
		fseek($this->fp, $this->pointer);
		$this->line = (int) $this->is_head;
		$this->next();
	}
	/*
		Return number of the current line
	*/
	public function key(){
		return $this->line;
	}
}
