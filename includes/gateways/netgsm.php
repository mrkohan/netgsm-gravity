<?php
/*
 * Class Name : GFHANNANSMS_Pro_{strtoupper( php file name) }	
 */
class GFHANNANSMS_Pro_NETGSM {

	/*
	* Gateway title	
	*/
	public static function name($gateways) {
	
		$name = __('NetGSM', 'GF_SMS' );
		
		$gateway = array( strtolower( str_replace( 'GFHANNANSMS_Pro_', '', get_called_class())) => $name );
		return array_unique( array_merge( $gateways , $gateway ) );
	}
	
	
	/*
	* Gateway parameters
	*/
	public static function options(){
		return array(
			'username'  => __('Username','GF_SMS'),
			'password'  => __('Password','GF_SMS')
		);
	}

	/*
	* Gateway credit
	*/
	public static function credit(){
		return true;
	}
	

	/*
	* Gateway action
	*/	
	public static function process( $options, $action, $from, $to, $messages ){
	
		if ( $action == 'credit' && !self::credit() ) {
			return false;
		}
		
		if ( ! extension_loaded('soap') )
			return __('Soap module is not active!','GF_SMS');
		
		
		
		$username = $options['username'];
		$password = $options['password'];
		
		ini_set("soap.wsdl_cache_enabled", "0");
		
		$to = explode(',', $to);
		$i = sizeOf($to);
		while($i--){
			$uNumber = Trim($to[$i]);
			$ret = &$uNumber;
			if (substr($uNumber,0, 5) == '%2B90'){ 
				$ret = substr($uNumber, 5);
			}
			if (substr($uNumber,0, 5) == '%2b90'){ 
				$ret = substr($uNumber, 5);
			}
			if (substr($uNumber,0, 4) == '0090'){ 
				$ret = substr($uNumber, 4);
			}
			if (substr($uNumber,0, 3) == '090')	{ 
				$ret = substr($uNumber, 3);
			}
			if (substr($uNumber,0, 3) == '+90'){ 
				$ret = substr($uNumber, 3);
			}
			if (substr($uNumber,0, 2) == '90'){ 
				$ret = substr($uNumber, 2);
			}
			$to[$i] =  '0' . $ret;
		}
		
			//#####################################################
			//
			//parameters for sendbybasenumber2 or sendbybasenumber FUNCTION
			//
			//#####################################################
			
			$from1=$from;
			$client = new SoapClient("http://soap.netgsm.com.tr:8080/Sms_webservis/SMS?wsdl");
	if ($action == "send"){
			
		$arraytext = explode("@@",$messages);
		$key = array_pop($arraytext);

			//sendsms func
			 try	{
				$parameters['username'] = $username;	
				$parameters['password'] = $password;
				$parameters['header'] 	= $from1;
				$parameters['msg'] 		= $messages;
				$parameters['gsm'] 		= $to;
				$parameters['filter'] 	= '';
				$parameters['startdate']= '';
				$parameters['stopdate'] = '';
				$parameters['encoding'] = '';

				$send = $client->smsGonder1NV2($parameters)->SendSmsResult;	
				}
				catch (SoapFault $ex) {
					//$errorstr = $ex->faultstring;
				}
			//}
	}
	if ($action == "credit") {
					 $Result = $client->kredi(array(
					'username'=>$username,
					'password' => $password)); // kredi sorgularken
					print_r($Result);
    			}
		if ($action == "send"){
			
		    if($send== 20){
		        return __('20', 'Mesaj metninde ki problemden dolay?? g??nderilemedi??ini veya standart maksimum mesaj karakter say??s??n?? ge??ti??ini ifade eder.(Standart maksimum karakter say??s?? 917 dir. E??er mesaj??n??z t??rk??e karakter i??eriyorsa T??rk??e Karakter Hesaplama men??sunden karakter say??lar??n??n hesaplan???? ??eklini g??rebilirsiniz' );
		    }
		    elseif($send == 30){
		        __('Ge??ersiz kullan??c?? ad?? , ??ifre veya kullan??c??n??z??n API eri??im izninin olmad??????n?? g??sterir. Ayr??ca e??er API eri??iminizde IP s??n??rlamas?? yapt??ysan??z ve s??n??rlad??????n??z ip d??????nda g??nderim sa??l??yorsan??z 30 hata kodunu al??rs??n??z. API eri??im izninizi veya IP s??n??rlaman??z?? , web aray??zden; sa?? ??st k????ede bulunan ayarlar> API i??lemleri men??sunden kontrol edebilirsiniz.', 'GF_SMS' );
		    }
			else if ( $send == 40 ){
				return __('Mesaj ba??l??????n??z??n (g??nderici ad??n??z??n) sistemde tan??ml?? olmad??????n?? ifade eder. G??nderici adlar??n??z?? API ile sorgulayarak kontrol edebilirsiniz.', 'GF_SMS' );
			}
			else if ( $send == 50 ){
				return __('Abone hesab??n??z ile ??YS kontroll?? g??nderimler yap??lamamaktad??r.', 'GF_SMS' );
			}
			else if ( $send == 51 ){
				return __('Aboneli??inize tan??ml?? ??YS Marka bilgisi bulunamad??????n?? ifade eder.', 'GF_SMS' );
			}
			else if ( $send == 70 ){
				return __('Hatal?? sorgulama. G??nderdi??iniz parametrelerden birisi hatal?? veya zorunlu alanlardan birinin eksik oldu??unu ifade eder.', 'GF_SMS' );
			}
			else if ( $send == 80 ){
				return __('G??nderim s??n??r a????m??', 'GF_SMS' );
			}
			else if ( $send == 85 ){
				return __('M??kerrer G??nderim s??n??r a????m??. Ayn?? numaraya 1 dakika i??erisinde 20 den fazla g??rev olu??turulamaz.', 'GF_SMS' );
			}
				else if ( $send == 100 || $send == 101 ){
				return __('Sistem hatas??.', 'GF_SMS' );
			}
			else if ( $send == 347022009 || $send == 1000){
				return 'OK';
			}
			else {
				//printf('hase been sent!', 'GF_SMS' );
			}
		}
		
		// if ($action == "credit") {
			// if ( $credit == 30 ) {
				// //return __('Ge??ersiz kullan??c?? ad?? , ??ifre veya kullan??c??n??z??n API eri??im izninin olmad??????n?? g??sterir.', 'GF_SMS' );
				// //print_r($Result);
			// }
			// return ( (int) $credit ) . __(' SMS', 'GF_SMS' );
		// }

		
		 if ($action == "range"){
			 $min = 100;
			$max = 200;
			 return array("min" => $min, "max" => $max);
		 }

	}
}