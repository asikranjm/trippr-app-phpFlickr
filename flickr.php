<?php

require_once('phpFlickr.php');

$flickr = new phpFlickr("46dea5f6abb12aed7a823adb7ecf5816");
$pics_file = fopen("pics_file.txt", "w") or die("Unable to open file!");

$photos = $flickr->photos_search(array( "tags"              =>"objects,color,- people,- person,trip,travel,vacation",
                                        "tag_mode"          =>"any",
                                        "per_page"          =>"20",
                                        "sort"              =>"interestingness-desc",
                                        "content_type"      => "1",
                                        "safe_search"       => "1",
                                        "license"           => "9"
));

//$user     = $flickr->people_findByUsername('stathisg');
//$user_url = $flickr->urls_getUserPhotos($user['id']);

$count = 0;
foreach ($photos['photo'] as $photo)
{

    echo "Image: ".$count++;
    //We get the Information of each photo giving the Photo_ID
    $photo_info = $flickr->photos_getInfo($photo['id']);
    //We stract the TAGS array for each photo
    $arrya_tags = $photo_info['photo']['tags']['tag'];

    $photo_sizes = $flickr->photos_getSizes($photo['id']);

    //var_dump($photo_sizes);

    $url= '';
    foreach ($photo_sizes as $size)
    {
        //Only get Medium Images for file sizes proposes
        if($size['label'] == 'Medium')
            $url = $size['source'];
        /*
        if($size['label'] == 'Large')
            $url = $size['source'];
        */
    }
    echo ": ".$url."\n";
    $line = $url.":";
    //echo '<img alt="'.$photo['title'].'" src="'.$flickr->buildPhotoURL($photo, "square").'" />';
    //echo '<a rel="nofollow" target="_blank" href="'.$url.'">Link : </a>';

    // Texto que va en el archivo
    $tags_line = '';
    foreach($arrya_tags as $tag)
    {
        if (!preg_match('/[^a-zA-Z]+/', $tag['_content']))
        {
            $tags_line .= $tag['_content'].",";
        }
    }
    $line .= rtrim($tags_line, ",").";\n";
    fwrite($pics_file, $line);
}

fclose($pics_file);