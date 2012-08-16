<?
// Retina screen image replacement
// see also js/2x.js
// via: http://css3.bradshawenterprises.com/blog/retina-image-replacement-for-new-ipad/

function is_in_string($haystack, $needle) { 
    if (strpos($haystack, $needle) !== false) {
        return 1;
    } else {
        return 0;
    }
}

function get_dir_contents($web_directory) {
    $directory = $_SERVER['DOCUMENT_ROOT'].$web_directory;
    if(file_exists($directory)) {
        $myDirectory = opendir($directory);

        while($entryName = readdir($myDirectory)) {
            if (is_in_string($entryName, "_2x")){
                $dirArray[] = $entryName;   
            }
        }

        return $dirArray;
    }
}
$web_directory="/wp-content/themes/boilerplate/images/";
header('Content-type: application/json');
echo json_encode(get_dir_contents($web_directory));
?>