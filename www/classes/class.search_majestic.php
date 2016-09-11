<?php



class search_majestic
{

    var $search_form = "http://www.majestic.co.uk/find/keyword-is-"; 


    function cleanUpQuery($q)
    {
        return urlencode($q);
    }


    function search($q)
    {
        if ($this->cleanUpQuery($q)){
            echo $q;
            $majestic_result = file_get_contents ($this->search_form.$this->cleanUpQuery($q));
            echo "<hr>";
            //echo $majestic_result;

            if ( preg_match("/<p>There are 0 products, showing 1-0<\/p>/i", $majestic_result) ){
                //nothing found
                return "No Results";
                echo "No Results";
            } else {
                if (preg_match_all('/<div class="info full-message">\s*<a href="\/find\/keyword-is-(.*)\/product-is-(\d*)" .* title="(.*)">\s*.*src="(.*)" alt/',$majestic_result,$results, PREG_SET_ORDER ))
                {
                        $majestic_wines_array = array();
                        foreach ($results as $value){
                                //print_r($value);
                                $keyword = $value[1];
                                $id = $value[2];
                                $title = $value[3];
                                $image = $value[4];
                                
                                $wine_link = $value[5] = "http://www.majestic.co.uk/find/keyword-is-$keyword/product-is-$id";
                                $image_src = $value[6] = "http://www.majestic.co.uk/$image";
                                $this_wine_array = array($value[1], $value[2], $value[3], $value[4], $value[5], $value[6]) ;
                                echo "<br/><a href=\"$wine_link\" >id=$id title=$title</a>";
                                echo "<img src=\"$image_src\" height=\"150px\" />";
                                
                                array_push($majestic_wines_array, $this_wine_array);
                        }
                        
                        return $majestic_wines_array;
                }
            }
        }
        else
        {
            return false;
        }
    }

} 

?>
