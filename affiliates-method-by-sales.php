<?php
/**
 * Plugin Name: Affiliates Custom Method - By sales
 * Description: Custom method: different rates according to sales.
 * Version: 1.0
 * Author: eggemplo
 * Author URI: http://www.eggemplo.com
 */
class ACM {

	/*
	 * In this case:
	 * 0 <= num_referrals <= 10    rate= 10%
	 * 10 < num_referrals <= 20    rate= 20%
	 * 20 < num_referrals <= 30    rate= 30%
	 * 30 < num_referrals          rate= $max_rate = 50%
	 */
	public static $rates = array(
                        '30' => 0.25,
                       '100' => 0.20
        );
        public static $max_rate = 0.10;
		
		
	
	public static function init() {
		if ( class_exists( 'Affiliates_Referral' ) ) {
			Affiliates_Referral::register_referral_amount_method( array( __CLASS__, 'by_sales' ) );
		}
	}
	/**
	 * Custom referral amount method implementation.
	 * @param int $affiliate_id
	 * @param array $parameters
	 */
	public static function by_sales( $affiliate_id = null, $parameters = null ) {
		$result = '0';

		if ( isset( $parameters['post_id'] ) ) {
			$affiliate_id = $parameters['affiliate_ids'][0];
			$referrals = affiliates_get_affiliate_referrals( $affiliate_id );
			$commission = null;
			foreach ( self::$rates as $limit => $rate ) {
				if ( !$commission && ( ( $referrals <= intval($limit) ) ) ) {
					$commission = $rate;
				}
			}
			
			if ( !$commission )
				$commission = self::$max_rate;
			
			$result = bcmul( $commission, $parameters['base_amount'], 2 );
		}

		return $result;
	}
}
add_action( 'init', array( 'ACM', 'init' ) );
?>
