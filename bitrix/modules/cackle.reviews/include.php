<?
CModule::AddAutoloadClasses(
	"cackle.reviews",
	array(
		"CackleReviewsApi" => "classes/general/cackle_reviews_api.php",
		"CackleReviewsSync" => "classes/general/cackle_reviews_sync.php",
		"cackle_reviews_orders_realtime" =>"classes/general/cackle_orders_realtime.php",
        "cackle_admin" =>"classes/general/cackle.min.js"
	)
);
?>