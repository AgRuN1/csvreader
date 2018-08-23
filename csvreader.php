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
	protected $length; // Length lines
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
		$head = $this->read_line();
		$heads = $this->get_array($head);
		$this->length = count($heads);
		if($is_head){
			$this->heads = $heads;
			$this->pointer = ftell($this->fp);
		}else{
			$nums = array();
			for($i = 0, $len = count($heads); $i < $len; $i++){
				array_push($nums, $i);
			}
			$this->heads = $nums;
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
				if(count($stack) && $stack[count($stack) - 1] == $char)
					array_pop($stack);
				else
					array_push($stack, $char);
			}
			$element .= $char;
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
		if(count($arr) != $this->length)
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