<?php
require_once("requests.php");

file_put_contents("fb.txt",(file_get_contents("php://input")));
$request=file_get_contents("fb.txt");
$request=json_decode($request);
$param=$request->result->parameters;
$param=(array) $param;
$output_Response_JSON=array();
$currentValueCount=0;

// parameters from api.ai
$definedParams=array("Category","Languages");

//Values of the corresponding params
$defined_Categories=array("Comedy"=>"10","Adventure"=>"78","Action"=>"19","Emotional"=>"49","Horror"=>"26","Love"=>"45","Friendship"=>"38");
$defined_Languages=array("Tamil","Telugu","Malayalam","Kannada");
$video_Path="http://www.shortfundly.com/video/";


//Facebook Response Templates...
$CarouselResponseTemplate=array("facebook"=>array());
$text=array("text"=>"");
$attachment=array("attachment"=>array("type"=>"template","payload"=>array("template_type"=>"generic","elements"=>array())));
$buttons=array("attachment"=>array("type"=>"template","payload"=>array("template_type"=>"button","text"=>"","buttons"=>array(array("type"=>"postback","title"=>"Start Over","payload"=>"")))));
$CarouselResponseTemplate["facebook"][]=$attachment;
$CarouselResponseTemplate["facebook"][]=$text;
$CarouselResponseTemplate["facebook"][]=$buttons;
/////////////////////////////////////////////////////////////////////////////////////////////////
if($param){
    foreach($definedParams as $key=>$value){
        if(array_key_exists($value,$param)){
            $value($param[$value]);
        }
    }
}


function Category($resolvedValue){
    global $defined_Categories,$CarouselResponseTemplate,$resolvedQuery;
    $CarouselResponseTemplate['facebook'][2]['attachment']['payload']['buttons']['0']['payload']="Change Category";
    foreach($defined_Categories as $key=>$value){
        if($resolvedValue == $key){
            $resolvedQuery=$key." Category";
            fetch_category_Result($value);
        }
    }
}   

function Languages($resolvedValue){
    global $defined_Languages,$resolvedQuery,$CarouselResponseTemplate;
    $CarouselResponseTemplate['facebook'][2]['attachment']['payload']['buttons']['0']['payload']="Change Language";
    foreach($defined_Languages as $key=>$value){
        if($resolvedValue == $value){
            $resolvedQuery=$value;
            $value();
        }
    }
}

function fetch_category_Result($categoryID){
    global $search_category;
    $search_category=$search_category.$categoryID;
    $output=getRequest($search_category);
    $filtered_result=filter_First_10_Videos($output);
    prepareCarousel($filtered_result);
}

function Tamil(){
    global $recent_tamil_films;
    $output=getRequest($recent_tamil_films);
    $filtered_result=filter_First_10_Videos($output);
    prepareCarousel($filtered_result);
}
function Telugu(){
    global $recent_telugu_films;
    $output=getRequest($recent_telugu_films);
    $filtered_result=filter_First_10_Videos($output);
    prepareCarousel($filtered_result);
}
function Malayalam(){
    global $recent_malayalam_films;
    $output=getRequest($recent_malayalam_films);
    $filtered_result=filter_First_10_Videos($output);
    prepareCarousel($filtered_result);
}
function Kannada(){
    global $recent_kannada_films;
    $output=getRequest($recent_kannada_films);
    $filtered_result=filter_First_10_Videos($output);
    prepareCarousel($filtered_result);
}
function filter_First_10_Videos($data_array){
    global $currentValueCount;
    $output=json_decode($data_array);
    $sortingResult=$output->results;
    $sortingResult=array_slice($sortingResult,$currentValueCount,10,true);
    return $sortingResult;
}

function prepareCarousel($filteredArray){
    global $request,$video_Path,$CarouselResponseTemplate,$output_Response_JSON,$resolvedQuery;
    foreach($filteredArray as $key=>$value){
        $value=(array) $value;
        $singleElement=array();
        $singleElement['title']=$value['title'];
        $singleElement['subtitle']=$value['description'];
        $singleElement['image_url']=$value['thumb'];
        $singleElement['buttons']=array();
        $singleElement['buttons'][]=array("title"=>"View","type"=>"web_url","url"=>$video_Path.$value['id']);
        $CarouselResponseTemplate['facebook'][0]['attachment']['payload']['elements'][]=$singleElement;
    }
    $responseText="Listed above with top $resolvedQuery short-films..";
    $resetText="If you would like to change your search you could start over again!!!";
    $CarouselResponseTemplate['facebook'][1]['text']=$responseText;
    $CarouselResponseTemplate['facebook'][2]['attachment']['payload']['text']=$resetText;
    $output_Response_JSON=$CarouselResponseTemplate;
    $responseSource=$request->result->source;
    $emptyText="";
    send_Response($emptyText,$emptyText,$output_Response_JSON,$responseSource);
}

function getRequest($url){
    global $X_API_KEY;
    $curl = curl_init();
    curl_setopt_array($curl, array(
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_HTTPHEADER => array(
        "Content-Type: application/json",
        "x-api-key: $X_API_KEY"
    )
    ));
    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);   
    return $response;
}



function send_Response($speech,$diplayText,$data,$source,$contextOut=array()){
    $response=array(
        "speech"=>$speech,
        "displayText"=>$diplayText,
        "data"=>$data,
        "contextOut"=>$contextOut,
        "source"=>$source
        );
    header("Content-Type: application/json");
    print_r(json_encode($response));
}
?>