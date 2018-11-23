<?php
namespace mf\utils;

	class ClassLoader {
		private $prefix;

		public function __construct($e) {
			$this->prefix = $e;
		}
		
		public function __get($attr) {
			if(property_exists($this, $attr))
				return $this->$attr;
			throw new \Exception('L\'attribut '.$attr.' n\'existe pas');
		}

		public function __set($attr, $value) {
			if(property_exists($this, $attr))
				$this->$attr = $value;
			throw new \Exception('L\'attribut '.$attr.' n\'existe pas');
		}
		
		public function loadClass($classe) {
			$classe_temp_path = strtr($classe, "\\", DIRECTORY_SEPARATOR);
			$classe_path = $this->prefix.DIRECTORY_SEPARATOR.$classe_temp_path.".php";
			if (file_exists($classe_path)){require_once "$classe_path";}
		}
		
		public function register() {
			spl_autoload_register(array($this, 'loadClass'));
		}
	}
?>
