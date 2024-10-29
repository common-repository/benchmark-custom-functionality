<?php
if ( !function_exists('debug')) {
	/*
	 * @author Oran Blackwell
	 * @version 1.2
	 * Last Modified 05-09-11
	 *
	 * @param mixed $args - Whatever you want to test
	 * @param string $title -
	 * @param boloean $toOutput
	 * @param boolean $isDebugMail passed TRUE from debug_mail to pass the IP restriction
	 * @param string $indent - used by internal recursive call (no known external value)
	 *
	 * I use this method of declaration  as some versions of PHP first parse the script for defined functions/classes
	 * so if(!function_exists('debug')) wasn't working on  a couple of servers.
	 * Inspiration from : PHP.net Contributions -> mainly stlawson *AT* joyfulearthtech *DOT* com's do_dump()
	 *
	 * # Testing
		$a = array (1, 2, array('aK' => 'aV', 'bK' => 'bV', 'cK' => 'cV'));	$b = (object) $a;	$c = "a simple string";
		$d = NULL;	$e = (int) 42;	$f = 42;	$g = '42';	$h = 4.2;	$i = TRUE;	$j = FALSE;	$k = 0;
		$testVars = array(	'a' => $a, 'b' => $b, 'c' => $c, 'd' => $d, 'e' => $e, 'f' => $f, 'g' => $g, 'h' => $h, 'i' => $i, 'j' => $j, 'k' => $k);
		foreach($testVars as $k=>  $v){
			debug($v);
		}
	 */
	function DEFINE_debug() {
		function debug($args, $title = '', $toOutput = TRUE, $isDebugMail = FALSE, $indent = NULL){
			$allowedIPs = array("93.107.35.85", "86.44.56.246" );

			$recursive_indent = "<span style='color:#AAAAAA;'>:</span>\t";
			if(in_array($_SERVER['REMOTE_ADDR'], $allowedIPs ) || $isDebugMail == true){
				$doc_root = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']);
				$backtrace = debug_backtrace();
				$line = htmlspecialchars($backtrace[0]['line']);
				$file = htmlspecialchars(str_replace(array('\\', $doc_root), array('/', ''), $backtrace[0]['file']));
				$class = !empty($backtrace[1]['class']) ? htmlspecialchars($backtrace[1]['class']) . '::' : '';
				$function = !empty($backtrace[1]['function']) ? htmlspecialchars($backtrace[1]['function']) . '() ' : '';
				$output = '<strong>'.$class.$function.' =&gt; '.$file.' #'.$line."</strong>\r\n";

				if(is_object($args)){
			        $type = get_class($args);
			        $toReturn .= $indent.$title.' <span style="color:#666666">'.$type.'</span><br>'.$indent.'(<br>';
            		foreach($args as $name => $value){
            			$toReturn .= debug($value, $name, FALSE, TRUE, $indent.$recursive_indent);
            		}
            		$toReturn .= $indent.')<br>';
				}

				if(is_array($args)){
			    	$count = count($args);
					$type= 'Array';
		            $toReturn .= $indent.$title .' = <span style="color:#666666">'.$type.' ('.$count.')</span><br>'.$indent.'(<br>';
		            $keys = array_keys($args);
		            if(array_keys($args) == range(0, count($args)))
		            	$isAssoc = FALSE;
		            else
		            	$isAssoc = TRUE;
		            foreach($keys as $name) {
		               $value = $args[$name];
		               $toReturn .= debug($value, ($isAssoc == TRUE ? '['.$name.']' : '---'.$name), FALSE, TRUE,  $indent.$recursive_indent);
		            }
		            $toReturn .= $indent.')<br>';
			    }

			    if(is_string($args)){
				 	$type = 'String';
				 	$type_color = '<span style="color:green">';
			        $toReturn .= $indent.$title.' = <span style="color:#666666">'.$type.'('.strlen($args).')</span> '
				        .$type_color.'"'.htmlentities($args).'"</span><br>';
			    }

			    if(is_int($args)){
			 		$type = 'Integer';
			 		$type_color = '<span style="color:red">';
			 		$toReturn .= $indent.$title.' = <span style="color:#666666">'.$type.'('.strlen($args).')</span> '
			 			.$type_color.htmlentities($args).'</span><br>';
			    }

				if(is_null($args)){
					$type = 'NULL';
					$type_color = '<span style="color:#666666">';
			      	$toReturn .= $indent.$title .'= <span style="color:#666666">'.$type.'('.strlen($args).')</span>'
			      		.$type_color.' -NULL-</span><br>';
			    }

			    if(is_bool($args)){
			        $type =  'boolean';
			        $type_color = '<span style="color:#92008d">';
			        $toReturn .= $indent.$title .'= <span style="color:#666666">'.$type.'('.strlen($args).')</span>'
			      		.$type_color.($args == 1 ? 'TRUE':'FALSE').'</span><br>';
			  	}

			  	if(is_float($args)){
			  		$type = 'Float';
			 		$type_color = '<span style="color:#0099c5">';
			 		$toReturn .= $indent.$title.' = <span style="color:#666666">'.$type.'('.strlen($args).')</span> '
			 			.$type_color.htmlentities($args).'</span><br>';
			    }

			    if(is_resource($args)){ //for things like myConnection
			      	$type = 'Resource';
			 		$type_color = '<span style="color:#FF8000">';
			 		ob_start();
			 		var_dump($args);
			 		$output = ob_get_clean();

			 		$toReturn .= $indent.$title.' = <span style="color:#666666">'.$type.' => '.$output.'</span> '
			 			.$type_color.htmlentities($args).'</span><br>';
			    }

			    /*if($args == "exit()"){
					echo 'Funciton was passed the String "exit()"';
					exit();
				}*/

				if($toOutput === TRUE){
					echo '<div style="text-align:left; background-color:white; font: 100% monospace; color:black;">';
					if($title != '' )	echo '<strong>'.$title.': </strong>';
					echo '<pre>';
					echo $output;
					echo $toReturn;
					echo '</pre>';
					echo '</div>';
				}
				else {
					return $toReturn;
				}
			}
		}
	}

	function DEFINE_debug_mail() {
		function debug_mail($args, $subject = 'debug'){

			$backtrace = debug_backtrace();
			$line = htmlspecialchars($backtrace[0]['line']);
			$file = htmlspecialchars(str_replace(array('\\', $doc_root), array('/', ''), $backtrace[0]['file']));
			$class = !empty($backtrace[1]['class']) ? htmlspecialchars($backtrace[1]['class']) . '::' : '';
			$function = !empty($backtrace[1]['function']) ? htmlspecialchars($backtrace[1]['function']) . '() ' : '';
			$output = "<strong>$class$function =&gt; $file #$line</strong>\r\n";


			$from = 'debug@'.$_SERVER['REMOTE_ADDR'].'.ie';
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
			$headers .= 'From: '.$from . "\r\n" .
				'Reply-To: '.$from . "\r\n" .
				'X-Mailer: PHP/' . phpversion();
			mail("oblackwell@benchmark.ie", $subject.' DATE: '.date('Y-m-d H:i:s'), $output.debug($args, $subject, FALSE, TRUE), $headers);
		}
	}
	DEFINE_debug();
	DEFINE_debug_mail();
}
?>