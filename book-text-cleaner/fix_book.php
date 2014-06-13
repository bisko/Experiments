<?php


ini_set('memory_limit', '2G');

$book = file_get_contents('output.txt');

// clean rogue texts
$book = preg_replace('!\n[^\n][^\n]?\n!uis', "\n", $book);




$resulting_words = array();

preg_match_all('!(?<=\n\n)([^\n]+)(?=\n+)!uis', $book, $matches);

$resulting_words = array_merge($resulting_words, $matches[1]);





$resulting_words = array_unique($resulting_words);

$str_in_group = array();

$groups = array();





$words = array();

foreach($resulting_words as $v) {
    $clean = clean_string($v);
    $len = mb_strlen($clean);
    
    if ($len < 5 || $len > 40) {
        continue;
    }

    $words[] = array(
        'orig' => $v,
        'clean' => $clean,
    );
}


$num_words = count($words);


foreach($words as $k=>$v) {    
    
    
    if (isset($str_in_group[$k])) {
        continue;
    }
    
    
    for($i = $k+1; $i < $num_words; $i++ ) {
        $dist = levenshtein($v['clean'], $words[$i]['clean'], 2,2,1);
        
        if ($dist < 4) {
            $str_in_group[$k] = 1;
            $str_in_group[$i] = 1;
            
            $groups[$v['orig']][] = $words[$i]['orig'];
        }
    }
    
}


//print_r($words);
print_r($groups);

foreach($groups as $k=>$v) {
    if (count($v) < 2) {
        continue;
    }

    $v[] = $k;
    
    foreach($v as $word) {
        $book = preg_replace('!\n+'.preg_quote($word,'!').'\n+!uis','',$book);
    }
}

// fix output

$book = preg_replace('!\n\n\n\n+!is', "\n", $book);


file_put_contents('output2.txt',$book);

function clean_string($str) {
    return mb_strtolower(preg_replace('![^a-z]+!uis', '',$str));
}