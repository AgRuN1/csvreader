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
	protected $line; // Current line
	protected $current_array; // Items array of current line
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
		if(empty($line))
			throw new Exception('Empty line number ' . $this->line);
		return explode($this->sep, $line);
	}
	/*
		Check either file ended or not
	*/
	public function valid(){
		return $this->current_array;
	}
	/*
		Return next array
	*/
	public function next(){
		$line = $this->read_line();
		if(feof($this->fp)){
			$this->current_array = false;
			return;
		}
		$arr = $this->get_array($line);
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
			$this->current_array = $result;
		}else{
			$this->current_array = $arr;
		}
	}

	public function current(){
		return $this->current_array;
	}
	public function rewind(){
		fseek($this->fp, $this->pointer);
		$this->line = (int) $this->is_head;
		$this->next();
	}
	public function key(){
		return $this->line;
	}
}
