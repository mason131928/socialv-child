<?php


function send_sms($phone_number,$verification_code,$sms_text){			
	$strOnlineSend  = 'http://www.smsgo.com.tw/sms_gw/sendsms.aspx?';
	$strOnlineSend .= 'username=mason@guppy3.com';
	$strOnlineSend     .= '&password=079cda5b';
	$strOnlineSend .= "&dstaddr=$phone_number";
	$strOnlineSend .= '&encoding=BIG5';
	$strOnlineSend .= '&smbody=' . urlencode( $sms_text . $verification_code );
	$strOnlineSend .= '&response=' . urlencode( 'http://localhost:8888/index3.php' );
	/**
	 * The following action is commented out intentionally because...
	 * echo ($strOnlineSend);
	 *  */
	$file = @fopen( $strOnlineSend, 'r' );
}