<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PaymentController extends Controller
{
    
	 function index(){
		 
		 	$amount = 20000;
	
					/* PHP */
				$post_data = array();
				$post_data['store_id'] = "amade620e3a220bee8";
				$post_data['store_passwd'] = "amade620e3a220bee8@ssl";
				$post_data['total_amount'] = $amount;
				$post_data['currency'] = "BDT";
				$post_data['tran_id'] = "SSLCZ_TEST_".uniqid();
				$post_data['success_url'] = "http://127.0.0.1/lara_ssl_payment/success";
				$post_data['fail_url'] = "http://127.0.0.1/lara_ssl_payment/fail";
				$post_data['cancel_url'] = "http://127.0.0.1/lara_ssl_payment/cancel";
				# $post_data['multi_card_name'] = "mastercard,visacard,amexcard";  # DISABLE TO DISPLAY ALL AVAILABLE

				# EMI INFO
				$post_data['emi_option'] = "1";
				$post_data['emi_max_inst_option'] = "9";
				$post_data['emi_selected_inst'] = "9";

				# CUSTOMER INFORMATION
				$post_data['cus_name'] = "Test Customer";
				$post_data['cus_email'] = "test@test.com";
				$post_data['cus_add1'] = "Dhaka";
				$post_data['cus_add2'] = "Dhaka";
				$post_data['cus_city'] = "Dhaka";
				$post_data['cus_state'] = "Dhaka";
				$post_data['cus_postcode'] = "1000";
				$post_data['cus_country'] = "Bangladesh";
				$post_data['cus_phone'] = "01711111111";
				$post_data['cus_fax'] = "01711111111";

				# SHIPMENT INFORMATION
				$post_data['ship_name'] = "testamade71u9";
				$post_data['ship_add1 '] = "Dhaka";
				$post_data['ship_add2'] = "Dhaka";
				$post_data['ship_city'] = "Dhaka";
				$post_data['ship_state'] = "Dhaka";
				$post_data['ship_postcode'] = "1000";
				$post_data['ship_country'] = "Bangladesh";

				# OPTIONAL PARAMETERS
				$post_data['value_a'] = "ref001";
				$post_data['value_b '] = "ref002";
				$post_data['value_c'] = "ref003";
				$post_data['value_d'] = "ref004";

				# CART PARAMETERS
				$post_data['cart'] = json_encode(array(
					array("product"=>"DHK TO BRS AC A1","amount"=>"200.00"),
					array("product"=>"DHK TO BRS AC A2","amount"=>"200.00"),
					array("product"=>"DHK TO BRS AC A3","amount"=>"200.00"),
					array("product"=>"DHK TO BRS AC A4","amount"=>"200.00")
				));
				$post_data['product_amount'] = "100";
				$post_data['vat'] = "5";
				$post_data['discount_amount'] = "5";
				$post_data['convenience_fee'] = "3";



				//SEND

				# REQUEST SEND TO SSLCOMMERZ
				$direct_api_url = "https://sandbox.sslcommerz.com/gwprocess/v3/api.php";

				$handle = curl_init();
				curl_setopt($handle, CURLOPT_URL, $direct_api_url );
				curl_setopt($handle, CURLOPT_TIMEOUT, 30);
				curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 30);
				curl_setopt($handle, CURLOPT_POST, 1 );
				curl_setopt($handle, CURLOPT_POSTFIELDS, $post_data);
				curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, FALSE); # KEEP IT FALSE IF YOU RUN FROM LOCAL PC


				$content = curl_exec($handle );

				$code = curl_getinfo($handle, CURLINFO_HTTP_CODE);

				if($code == 200 && !( curl_errno($handle))) {
					curl_close( $handle);
					$sslcommerzResponse = $content;
				} else {
					curl_close( $handle);
					echo "FAILED TO CONNECT WITH SSLCOMMERZ API";
					exit;
				}

				# PARSE THE JSON RESPONSE
				$sslcz = json_decode($sslcommerzResponse, true );

				if(isset($sslcz['GatewayPageURL']) && $sslcz['GatewayPageURL']!="" ) {
						# THERE ARE MANY WAYS TO REDIRECT - Javascript, Meta Tag or Php Header Redirect or Other
						# echo "<script>window.location.href = '". $sslcz['GatewayPageURL'] ."';</script>";
					echo "<meta http-equiv='refresh' content='0;url=".$sslcz['GatewayPageURL']."'>";
					# header("Location: ". $sslcz['GatewayPageURL']);
					exit;
				} else {
					echo "JSON Data parsing error!";
				}
		 
	

	}
	
	
	
	
	function process(Request $request){
		
		$val_id=urlencode($_POST['val_id']);
		$store_id=urlencode("amade620e3a220bee8");
		$store_passwd=urlencode("amade620e3a220bee8@ssl");
		$requested_url = ("https://sandbox.sslcommerz.com/validator/api/validationserverAPI.php?val_id=".$val_id."&store_id=".$store_id."&store_passwd=".$store_passwd."&v=1&format=json");

		$handle = curl_init();
		curl_setopt($handle, CURLOPT_URL, $requested_url);
		curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false); # IF YOU RUN FROM LOCAL PC
		curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false); # IF YOU RUN FROM LOCAL PC

		$result = curl_exec($handle);

		$code = curl_getinfo($handle, CURLINFO_HTTP_CODE);

		if($code == 200 && !( curl_errno($handle)))
		{

			# TO CONVERT AS ARRAY
			# $result = json_decode($result, true);
			# $status = $result['status'];

			# TO CONVERT AS OBJECT
			$result = json_decode($result);

			# TRANSACTION INFO
			$status = $result->status;
			$tran_date = $result->tran_date;
			$tran_id = $result->tran_id;
			$val_id = $result->val_id;
			$amount = $result->amount;
			$store_amount = $result->store_amount;
			$bank_tran_id = $result->bank_tran_id;
			$card_type = $result->card_type;

			# EMI INFO
			$emi_instalment = $result->emi_instalment;
			$emi_amount = $result->emi_amount;
			$emi_description = $result->emi_description;
			$emi_issuer = $result->emi_issuer;

			# ISSUER INFO
			$card_no = $result->card_no;
			$card_issuer = $result->card_issuer;
			$card_brand = $result->card_brand;
			$card_issuer_country = $result->card_issuer_country;
			$card_issuer_country_code = $result->card_issuer_country_code;

			# API AUTHENTICATION
			$APIConnect = $result->APIConnect;
			$validated_on = $result->validated_on;
			$gw_version = $result->gw_version;
			
			echo "Your Payment Amount ".$amount."</br>";
			echo "Your Card Type : ".$card_type."</br>";
			echo "Store payment : ".$store_amount."</br>";

		} else {

			echo "Failed to connect with SSLCOMMERZ";
		}
		 
		
		 
		 
	 }
	 
	 
	 function success(Request $request){
		 
		   echo "Transaction is Successful";
		   
		   	$val_id=urlencode($_POST['val_id']);
		$store_id=urlencode("amade620e3a220bee8");
		$store_passwd=urlencode("amade620e3a220bee8@ssl");
		$requested_url = ("https://sandbox.sslcommerz.com/validator/api/validationserverAPI.php?val_id=".$val_id."&store_id=".$store_id."&store_passwd=".$store_passwd."&v=1&format=json");

		$handle = curl_init();
		curl_setopt($handle, CURLOPT_URL, $requested_url);
		curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($handle, CURLOPT_SSL_VERIFYHOST, false); # IF YOU RUN FROM LOCAL PC
		curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false); # IF YOU RUN FROM LOCAL PC

		$result = curl_exec($handle);

		$code = curl_getinfo($handle, CURLINFO_HTTP_CODE);

		if($code == 200 && !( curl_errno($handle)))
		{

			# TO CONVERT AS ARRAY
			# $result = json_decode($result, true);
			# $status = $result['status'];

			# TO CONVERT AS OBJECT
			$result = json_decode($result);

			# TRANSACTION INFO
			$status = $result->status;
			$tran_date = $result->tran_date;
			$tran_id = $result->tran_id;
			$val_id = $result->val_id;
			$amount = $result->amount;
			$store_amount = $result->store_amount;
			$bank_tran_id = $result->bank_tran_id;
			$card_type = $result->card_type;

			# EMI INFO
			$emi_instalment = $result->emi_instalment;
			$emi_amount = $result->emi_amount;
			$emi_description = $result->emi_description;
			$emi_issuer = $result->emi_issuer;

			# ISSUER INFO
			$card_no = $result->card_no;
			$card_issuer = $result->card_issuer;
			$card_brand = $result->card_brand;
			$card_issuer_country = $result->card_issuer_country;
			$card_issuer_country_code = $result->card_issuer_country_code;

			# API AUTHENTICATION
			$APIConnect = $result->APIConnect;
			$validated_on = $result->validated_on;
			$gw_version = $result->gw_version;
			
			echo "Your Payment Amount ".$amount."</br>";
			echo "Your Card Type : ".$card_type."</br>";
			echo "Store payment : ".$store_amount."</br>";

		} else {

			echo "Failed to connect with SSLCOMMERZ";
		}
     }		   
		   
		
	
	

	
	
}
