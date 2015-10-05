<?php 
// Not completley done yet, but it works already.
class Captcha
{

	private $captchaImage;
	private $captchaControl;
	private $width = 120;
	private $height = 50;
	private $distanceBetweenNumbers = 17;
	private $minColorValue = 0;
	private $maxColorValue = 180; 
	private $minBackgroundColorValue = 200;
	private $maxBackgroundColorValue = 255;
	private $captchaInfoText = "";
	private $captchaOriginProperty = "captchaOrigin";
	private $getCaptchaProperty = "captcha";
	private $fileName = "";

	public function __construct( $alternativeInfoText = "captcha.info" )
	{
		$this->captchaInfoText = "Subtrahieren Sie von jeder Zahl eines und tragen sie sie in das texfeld ein";
		// load from configuration object to the captcha properties: TODO
	}

	public function getCaptchaOriginProperty(  )
	{
		return $this->captchaOriginProperty;
	}

	public function getCaptchaProperty(  )
	{
		return $this->getCaptchaProperty;
	}

	public function generateCaptcha( )
	{
		$this->captchaImage = imagecreatetruecolor($this->width, $this->height); 
		
		$white = imagecolorallocate($this->captchaImage, $this->generateRandomNumber($this->minBackgroundColorValue, $this->maxBackgroundColorValue), $this->generateRandomNumber($this->minBackgroundColorValue, $this->maxBackgroundColorValue), $this->generateRandomNumber($this->minBackgroundColorValue, $this->maxBackgroundColorValue));
		imagefill($this->captchaImage, 0, 0, $white);
		
		$captchaNumbers = "";

		for ( $i = 0; $i < 7; $i++ )
		{
			$nextNumber = $this->generateRandomNumber(1, 9);
			$captchaNumbers .= $nextNumber;
			imagestring($this->captchaImage, 5, $i*$this->distanceBetweenNumbers + 5, $this->generateRandomNumber(3, 30), $nextNumber, $this->generateRandomColor());
		}
		 
		$this->saveCaptcha($captchaNumbers);
	}

	public function validate( $captchaFileName, $enteredCaptcha )
	{
		if ( $captchaFileName == "" || $enteredCaptcha == "" )
		{
			return false;
		}
		
		$originalNumbers = explode("_", $captchaFileName)[1];
		$originalNumbers = explode(".", $originalNumbers)[0];
		
		$parsedCaptcha = "";

		for ( $i = 0; $i < strlen($originalNumbers); $i++ )
		{
			$parsedCaptcha .= "" . ((int)substr($originalNumbers, $i, 1))-1; 
		}

		return $parsedCaptcha == $enteredCaptcha;
	}

	public function getForm( )
	{
		$html = "<div>{$this->captchaInfoText}</div><img src='{$this->fileName}' style='width:{$this->width}px;height:{$this->height}px;'><input type='text' name='{$this->captchaProperty}' id='{$this->captchaProperty}'><input type='hidden' value='{$this->fileName}' name='{$this->captchaOriginProperty}' id='{$this->captchaOriginProperty}'>";
		echo "abc";
		return $html;
	}	

	private function saveCaptcha( $captchaNumbers )
	{
		ob_start(); 
		imagejpeg($this->captchaImage, NULL, 100); 
		$contents = ob_get_contents(); 
		ob_end_clean();
		 
		imagedestroy($this->captchaImage);
		$this->fileName = time() . "_{$captchaNumbers}.jpg";		

		$fh = fopen($this->fileName, "w" );
		fwrite( $fh, $contents );
		fclose( $fh );	
	}

	private function generateRandomNumber( $min, $max )
	{
		return rand($min, $max);
	}

	private function generateRandomColor( )
	{ 
		return imagecolorallocate($this->captchaImage, $this->generateRandomNumber($this->minColorValue, $this->maxColorValue), $this->generateRandomNumber($this->minColorValue, $this->maxColorValue), $this->generateRandomNumber($this->minColorValue, $this->maxColorValue));
	}

}
