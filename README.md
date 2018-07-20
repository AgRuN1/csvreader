# Create a reader
    $reader = new CSVReader('file.csv', ',', false);
* The first required argument is path to a csv file
* The second optional argument is delimiter symbol by default it's a comma
* The third optional argument is either there are headers or not
May be two exceptions at the time creation of a reader:
* File not found
* File are not opened
# Iterate a reader
    foreach($reader as $number => $line):
        echo $line['column1'];
	echo "line number: " . $number;
	echo "headers: ";
	foreach($reader->headers as $header){
		echo "$header : ";
	}
        echo $line['column2'];
    endforeach;
Methods:
* headers() returns an array of headers or exception if there are not it
* current_line() returns the current line
* key() returns the number of the current line
* rewind() starts iteration from begin
* current() returns the current array
Use quotes or double quotes for escaping delimiters
At the time of iteration may be thrown two exceptions:
* Empty line
* Invalid count of elements at the line
* There are not closing quote
# Made by
* [Web Site](http://inmtoo.com/)
* [Investor](https://vk.com/maxsharun)
* [Developer](https://vk.com/agrun1)
