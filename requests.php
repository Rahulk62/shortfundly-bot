<?php
$baseURL="http://api.shortfundly.com/";

//Access Key
$X_API_KEY="e7c1948d540f80a297e55a02e68dd8c1bac9b1bb";

//API's URL
$recent_films=$baseURL."film/recent_films/";
$most_viewed_movies=$baseURL." film/most_viewed/";
$most_liked_movies=$baseURL."film/most_liked/";
$trending_films=$baseURL."film/trending_films/";
$top_rated_films=$baseURL."film/toprated/";

// Languages......................
$languages=$baseURL."auth/language/";
$recent_tamil_films=$baseURL."film/recent_tamil/";
$recent_telugu_films=$baseURL."film/recent_telugu/";
$recent_malayalam_films=$baseURL."film/recent_malayalam/";
$recent_kannada_films=$baseURL."film/recent_kannada/";


//sortBased on CategoryId................
$search_category=$baseURL."channel/search/";        //Pass Category ID as Param.. ie : /channel/search/4

?>