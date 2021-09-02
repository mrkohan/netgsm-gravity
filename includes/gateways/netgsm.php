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
		        return __('20', 'Mesaj metninde ki problemden dolayı gönderilemediğini veya standart maksimum mesaj karakter sayısını geçtiğini ifade eder.(Standart maksimum karakter sayısı 917 dir. Eğer mesajınız türkçe karakter içeriyorsa Türkçe Karakter Hesaplama menüsunden karakter sayılarının hesaplanış şeklini görebilirsiniz' );
		    }
		    elseif($send == 30){
		        __('Geçersiz kullanıcı adı , şifre veya kullanıcınızın API erişim izninin olmadığını gösterir. Ayrıca eğer API erişiminizde IP sınırlaması yaptıysanız ve sınırladığınız ip dışında gönderim sağlıyorsanız 30 hata kodunu alırsınız. API erişim izninizi veya IP sınırlamanızı , web arayüzden; sağ üst köşede bulunan ayarlar> API işlemleri menüsunden kontrol edebilirsiniz.', 'GF_SMS' );
		    }
			else if ( $send == 40 ){
				return __('Mesaj başlığınızın (gönderici adınızın) sistemde tanımlı olmadığını ifade eder. Gönderici adlarınızı API ile sorgulayarak kontrol edebilirsiniz.', 'GF_SMS' );
			}
			else if ( $send == 50 ){
				return __('Abone hesabınız ile İYS kontrollü gönderimler yapılamamaktadır.', 'GF_SMS' );
			}
			else if ( $send == 51 ){
				return __('Aboneliğinize tanımlı İYS Marka bilgisi bulunamadığını ifade eder.', 'GF_SMS' );
			}
			else if ( $send == 70 ){
				return __('Hatalı sorgulama. Gönderdiğiniz parametrelerden birisi hatalı veya zorunlu alanlardan birinin eksik olduğunu ifade eder.', 'GF_SMS' );
			}
			else if ( $send == 80 ){
				return __('Gönderim sınır aşımı', 'GF_SMS' );
			}
			else if ( $send == 85 ){
				return __('Mükerrer Gönderim sınır aşımı. Aynı numaraya 1 dakika içerisinde 20 den fazla görev oluşturulamaz.', 'GF_SMS' );
			}
				else if ( $send == 100 || $send == 101 ){
				return __('Sistem hatası.', 'GF_SMS' );
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
				// //return __('Geçersiz kullanıcı adı , şifre veya kullanıcınızın API erişim izninin olmadığını gösterir.', 'GF_SMS' );
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