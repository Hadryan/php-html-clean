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
        
        //remove the error tag
        $len = count($t_text);
        for($index = $len-1 ;$index >=0; $index--){
            if($t_error[$index] != "" && $t_error[$index][0] != ""){
                $err = $t_text[$index][0];
                $pos = $t_text[$index][1];
                $input =substr($input, 0, $pos) .  str_replace($err, "", substr($input,$pos));
            }
        }
				
        $stack = array();
        for($index =0; $index < $len; $index++){
            if($t_begin[$index] != "" && $t_begin[$index][0] != ""){
                if(in_array($t_begin[$index][0],$tags)){
                    continue;
                }
                array_push($stack, $t_begin[$index][0]);
            }else if($t_close[$index] != "" && $t_close[$index][0] != ""){
                if(in_array($t_close[$index][0], $stack)){
                    $item = array_pop($stack);
                    while($item != $t_close[$index][0]){
                        $item = array_pop($stack);
                    }
                }
            }      
        }
        while(count($stack) >0 ){
            $item = array_pop($stack);
            $input = $input . "</{$item}>";
        }
        return $input;
    }

?>
