<?php

require_once('phpFlickr.php');

$flickrObject = new phpFlickr("46dea5f6abb12aed7a823adb7ecf5816");
$pics_file = fopen("pics_file_temp.txt", "w") or die("Unable to open file!");

$photos = $flickrObject->photos_search(array( "tags"              =>"trip,travel,vacation,objects,color,- people,- person,- bikini,- human",
                                        "tag_mode"          =>"any",
                                        "per_page"          =>"50",
                                        "sort"              =>"interestingness-desc",
                                        "content_type"      => "1",
                                        "safe_search"       => "1",
                                        "license"           => "9"
));
//Initializing variables
$imageCounter = 0;
$idArrayImages = array();
//For each Picture
foreach ($photos['photo'] as $photo)
{
	//Console Log image message counter
    echo "Image: ".$imageCounter++." / ";
    //We get the Information of each photo giving the Photo_ID
    $photo_info = $flickrObject->photos_getInfo($photo['id']);

	echo $photo["id"].' - ';

	if(in_array($photo['id'], $idArrayImages))
		echo "Hay una imagen repetida";

	$idArrayImages[] = $photo['id'];

	//Uncomment this line to see the PUBLIC URL of the photo
    //echo $photo_info['photo']['urls']['url'][0]['_content']."\n";
    //We stract the TAGS array for each photo
    $array_tags = $photo_info['photo']['tags']['tag'];

	//Get the different sizes of the image
    $photo_sizes = $flickrObject->photos_getSizes($photo['id']);

	//We look the optimal size
    foreach ($photo_sizes as $size)
	{
		//Only get Medium Images for file sizes proposes
		if ($size['label'] == 'Medium 640')
			$url = $size['source'];
	}
    $line = $url.":";
	//Uncomment this to see in the browser the image in thumbnail size
    //echo '<img alt="'.$photo['title'].'" src="'.$flickrObject->buildPhotoURL($photo, "square").'" />';
	//Uncomment this line to see in the browser the link of the URL
    //echo '<a rel="nofollow" target="_blank" href="'.$url.'">Link : </a>';

    //Create the line with all the good tags
	//Initialize the clean line
    $tags_line = '';
	$wordsCounter = 1;
	//We process each tag to see if its work
    foreach($array_tags as $tags)
    {
		//We only consider less than 10 tags
		if($wordsCounter < 10)
		{
			$tag = $tags['_content'];
			//Clean the tags that has special characters
			if (!preg_match('/[^a-zA-Z]+/', $tag))
				if(strlen($tag) < 15 )
					$tags_line .= $tag.",";
			$wordsCounter++;
		}
    }
	echo $tags_line."\n";
	//Finish the line and end it with a NewLine command
    $line .= rtrim($tags_line, ",").";\n";
	//Write the line in the file
    fwrite($pics_file, $line);

}
//Close the file
fclose($pics_file);
copy("pics_file_temp.txt","pics_file.txt");