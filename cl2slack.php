<?php 

// echo time
echo "running: ". `date`;

// load composer
require __DIR__ . '/vendor/autoload.php';
// load db connection
require __DIR__ . '/private/db.php';
// load slack connection
require __DIR__ . '/private/slack.php';

// instantiate Craigslist composer class
use Craigslist\CraigslistRequest;
use Craigslist\CraigslistApi;

// define Slack webhook URL - integration_test
//$webhook_url = 'https://hooks.slack.com/services/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';

// define Slack webook URL - leads
$webhook_url = $slack_webhook_url;

// define Slack channel params
$slack_settings = [
    'username' => $slack_username,
    'channel' => $slack_channel,
    'link_names' => true
];


// build craigslist search

// define search query - see andrewevansmith/php-craigslist-api-utility for parameters
$search_query = 'storage|installation|remote|freelance|excel|tutoring|networking|network|unix|cabling|VPN|security|freelance|techs|cisco|mysql|database|linux|website|"datacenter"';


// define and instantiate craigslist client settings
$craigslist_request = array(
    new CraigslistRequest(array(
         'city' => 'boston',
         'category' => 'cpg',
         'query' => $search_query,
         'postedToday' => '1'
    )),
   

);


// instantiate Slack client
$slack_client = new Maknz\Slack\Client($webhook_url, $slack_settings);

// instantiate Craigslist API
$api = new CraigslistApi();

// get Craiglist results from search and populate object
$cl_result = $api->get($craigslist_request);


//print_r($cl_result);
//[id] => 5604755839
//            [link] => http://boston.craigslist.org/gbs/cpg/5604755839.html
//            [title] => Comcast router configuration (Woburn)
//            [description] => I need help with a Comcast router configuration for small 4-person office. I can't see Minolta printer on the network and the IP address assigned to my workstation via DHCP is not correct. Any and all help appreciated. Wireless is working just fine h [...]



// for each listing, do this
foreach ($cl_result as $row ) {

    // echo $row['id'] . PHP_EOL;    
    // echo $row['link'] . PHP_EOL . PHP_EOL;    
    
    // set the listing id
    $listing_id = $row['id'] ;      

    // here is the query to check if the listing id exists - i.e. have we seen this listing before
    $select_listings_sql = "SELECT listing_id from listings where listing_id = ?";
   
    // set the query to run with the connection
    $select_listings = $link->prepare($select_listings_sql);

    // define the parameter to search for
    $select_listings->bind_param('i', $listing_id );
   
    // run the select
    $select_listings->execute();
    
    // set the result into an object
    $result = $select_listings->get_result();
   
    // get the number of rows returned - expected ( 0 | 1) 
    $row_cnt = $result->num_rows;
    
    // free the result
    $select_listings->free_result();

    // heres where it gets fun - check the row count and if it exists, do nothing and move on to the next
    if ($row_cnt >  0) {
       // echo 'no rows found';
    } else {
      // if rows are found we come here
      // we have to first insert into the database

        // here is the query to insert if the listing id does not exist - i.e.  we  have not seen this listing before so we insert
        $insert_listings_sql = "INSERT INTO listings (listing_id) VALUES (?)";

        // set the query to run with the connection
        $insert_listings = $link->prepare($insert_listings_sql);

        // define the parameter to insert for
        $insert_listings->bind_param('i', $listing_id );

        // run the select
        $insert_listings->execute();

        // free the result
        $insert_listings->free_result();

        //$slack_client->send( $row['title'].": <".$row['link'].">" );
        $slack_client->attach([
             'text' => $row['title'] . " \n <" . $row['link'] . ">" 
        ])->send('');
  
        //echo 'there are rows';
    }   

//$slack_client->send($listing_id);
}


echo "finished: ". `date`;
?>
