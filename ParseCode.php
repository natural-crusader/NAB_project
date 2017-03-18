<?php

class ParseCode {
	private $file;
	private $parsedFile;

	function __construct($filepath) {
		$this->file = $filepath;
		$this->parseFile();
	}

	public static function parseToString($filepath) {
		$instance = new ParseCode($filepath);
		return $instance->toString();
	}

	private function parseFile() {
		$info = new SplFileInfo($this->file);
		$ext = $info->getExtension();
		switch(strtolower($info->getExtension())) {
			case 'csv':
				$this->parseCSV();
				break;
			case 'xml':
				$this->parseXML();
				break;
			case 'json':
				$this->parseJSON();
				break;
		}
	}

	private function parseJSON() {
		$this->parsedFile = json_decode(file_get_contents($this->file), true);
	}

	/** XML format should be 
		<item id="67-VI-yW05" name="Apricot" quantity="7">
			<categories>
				<category>a</category>
				<category>b</category>
			</categories>
		</item>
	**/

	private function parseXML() {
		$this->parsedFile = simplexml_load_string(file_get_contents($this->file));
		// create array from XML
		$json = json_encode($this->parsedFile);
		$array = json_decode($json,TRUE);
		
		$this->parsedFile = [];

		// need to fix @attributes and categories
		if(isset($array['item']))
			foreach($array['item'] as $item) {
				// make empty categories if none exist
				$categories = ['categories' => []];
				if(isset($item['categories']['category'])){
					$categories = ['categories' => $item['categories']['category']];			
				}
				$this->parsedFile[] = array_merge($item['@attributes'], $categories);
			}
	}

	/** CSV format should be 
		72-Bx-hb21,Lemon,645,category 1;category2;my long category name;
	**/

	private function parseCSV() {
		$lines = file($this->file);
		$this->parsedFile = [];

		foreach($lines as $line) {
			$line = str_replace("\n", '', $line);
			list($id, $name, $quantity, $cats) = explode(',', $line);

			if($cats) {
				$cats = explode(';', $cats);
			}

			$this->parsedFile[] = ['id'=>$id, 'name' => $name, 'quantity' => $quantity, 'categories' => $cats];
		}
	}

	public function toString() {
		$str = '';
		//var_dump($this->parsedFile);
		foreach($this->parsedFile as $line) {
			$line = (object) $line;
			// skip if no id
			if(empty(trim($line->id))) continue;

			$str .= "{$line->id}";
			if(isset($line->name)) 
				$str .= " {$line->name}";
			if(isset($line->quantity) && !empty(trim($line->quantity)))
				$str.=" ({$line->quantity})\r\n";
			else
				$str.=" (0)\r\n";

			// from XML there may not be an array of categories
			if($line->categories && is_array($line->categories))
				foreach($line->categories as $cat) {
					$cat = trim($cat);
					if(!empty($cat)) {
						$str.=" - {$cat}\r\n";
					}
				}
			else if ($line->categories) { // not an array
				$cat = trim($line->categories);
				if(!empty($cat)) {
					$str.=" - {$cat}\r\n";
				}
			}
			$str.="\r\n";
		}
		return $str;
	}
}