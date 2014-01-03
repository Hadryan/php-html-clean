<?php 
  	function html_clean($input){
        $tags = array("br","img");
        $pattern = "/<(?'t_whole'\w+)([^<>]*)?\/(\s*)?>|<(?'t_begin'\w+)([^<>]*)?>|<(?'t_error'\w+)([^<>]*)?|<\/(?'t_close'\w+)>/i";
        $matches = array();
        $times = preg_match_all($pattern, $input, $matches, PREG_OFFSET_CAPTURE);
        $t_text =  $matches[0];
        $t_error = $matches["t_error"];
        $t_begin = $matches["t_begin"];
        $t_close = $matches["t_close"];
        $t_whole = $matches["t_whole"];
        

		$offset = 0;
		$stack = array();
        $len = count($t_text);
		for($i = 0 ; $i < $len; $i++){
			if($t_error[$i]!="" && $t_error[$i][0] != ""){
				$text   = $t_text[$i][0];
				$pos    = $t_text[$i][1] + $offset;
                $input  = substr($input, 0, $pos) . substr($input, $pos + strlen($text));
				$offset = $offset - strlen($text);
				echo "REMOVE:". $text. PHP_EOL;
			}
			else if($t_begin[$i]!="" && $t_begin[$i][0] != ""){
                array_push($stack, $t_begin[$i][0]);
			}
			else if($t_close[$i]!="" && $t_close[$i][0] != ""){
				$text = $t_text[$i][0];
				$pos  = $t_text[$i][1] + $offset;
				$tag  = $t_close[$i][0];
				if(in_array($tag, $stack)){
					$item = array_pop($stack);
					while($item != $tag && count($stack) > 0){
						if(in_array($item, $tags) === false){
							$temp   = "</{$item}>";
							$input  = substr($input, 0, $pos) . $temp. substr($input, $pos);
							$offset = $offset + strlen($temp);
							echo "INSERT:" . $temp . PHP_EOL;
						}
						$item = array_pop($stack);
						$pos  = $t_text[$i][1] + $offset;
					}
				}else{
					$input  = substr($input, 0, $pos) . substr($input, $pos +strlen($text));
					$offset = $offset - strlen($text);
					echo "REMOVE:" . $text . PHP_EOL;
				}
				
			}
		}
		
        while(count($stack) >0 ){
            $item = array_pop($stack);
			if(in_array($item, $tags) === false){
				$input = $input . "</{$item}>";
				echo "APPEND:" . "</{$item}>" . PHP_EOL;
			}
        }
        return $input;
    }
?>