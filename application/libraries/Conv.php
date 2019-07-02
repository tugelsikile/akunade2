<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Conv {
	function toNum($str) {
		$limit = 5; //apply max no. of characters
		$colLetters = strtoupper($str); //change to uppercase for easy char to integer conversion
		$strlen = strlen($colLetters); //get length of col string
		if($strlen > $limit)	return "Column too long!"; //may catch out multibyte chars in first pass
		preg_match("/^[A-Z]+$/",$colLetters,$matches); //check valid chars
		if(!$matches)return "Invalid characters!"; //should catch any remaining multibyte chars or empty string, numbers, symbols
		$it = 0; $vals = 0; //just start off the vars
		for($i=$strlen-1;$i>-1;$i--){ //countdown - add values from righthand side
			$vals += (ord($colLetters[$i]) - 64 ) * pow(26,$it); //cumulate letter value
			$it++; //simple counter
		}
		return $vals; //this is the answer
	}
	
	function toStr($n,$case = 'upper') {
		$alphabet   = array(
			'A',	'B',	'C',	'D',	'E',	'F',	'G',
			'H',	'I',	'J',	'K',	'L',	'M',	'N',
			'O',	'P',	'Q',	'R',	'S',	'T',	'U',
			'V',	'W',	'X',	'Y',	'Z'
		);
		$n 			= $n;
		if($n <= 26){
			$alpha 	=  $alphabet[$n-1];
		} elseif($n > 26) {
			$dividend   = ($n);
			$alpha      = '';
			$modulo;
			while($dividend > 0){
				$modulo     = ($dividend - 1) % 26;
				$alpha      = $alphabet[$modulo].$alpha;
				$dividend   = floor((($dividend - $modulo) / 26));
			}
		}
		if($case=='lower'){
			$alpha = strtolower($alpha);
		}
		return $alpha;
	} 
	function romawi($integer, $upcase = true) {
		$table = array('M'=>1000, 'CM'=>900, 'D'=>500, 'CD'=>400, 'C'=>100, 'XC'=>90, 'L'=>50, 'XL'=>40, 'X'=>10, 'IX'=>9, 'V'=>5, 'IV'=>4, 'I'=>1); 
		$return = ''; 
		while($integer > 0) 
		{ 
			foreach($table as $rom=>$arb) 
			{ 
				if($integer >= $arb) 
				{ 
					$integer -= $arb; 
					$return .= $rom; 
					break; 
				} 
			} 
		} 
		return $return; 
	} 
	function hariIndo($date){ //date('N');
		$hariIndo 	= array("Senin","Selasa","Rabu","Kamis","Jum'at","Sabtu","Minggu");
		return $hariIndo[(int)$date-1];
	}
	function bulanIndo($date){ //date('m');
		$BulanIndo	= array("Januari","Februari","Maret","April","Mei","Juni","Juli","Agustus","September","Oktober","Nopember","Desember");
		return $BulanIndo[(int)$date-1];
	}
	function tglIndo($date=FALSE){
		if (!$date){
			$date	= date('Y-m-d');
		}
		$BulanIndo	= array("Januari","Februari","Maret","April","Mei","Juni","Juli","Agustus","September","Oktober","Nopember","Desember");
		$tahun = substr($date, 0, 4);
		$bulan = substr($date, 5, 2);
		$tgl   = substr($date, 8, 2);
		
		$result = $tgl . " " . $BulanIndo[(int)$bulan-1] . " ". $tahun;		
		return($result);
	}
	function umur($date){
		$tz  = new DateTimeZone('Asia/Jakarta');
		$age = DateTime::createFromFormat('Y-m-d', $date, $tz)
	     		->diff(new DateTime('now', $tz))
    			->y;
		return $age;
	}
	function getmime($file){
		return mime_content_type($file);
	}
	function user_type($type){
		switch ($type){
			default		:
				null	:
			case 1		: $x = 'Guru'; break;
			case 50		: $x = 'Admin'; break;
			case 99		: $x = 'Super Admin'; break;
		}
		return $x;
	}
    function genPass($length=FALSE){
        if (!$length){
            $length 	= 8;
        }
        $characters = '0123456789ABCDEFGHJKLMNPRSTUVWXY';
        $string 	= "";
        for ($p = 0; $p < $length; $p++) {
            $string .= $characters[rand(0, strlen($characters)-1)];
        }
        return $string;
    }
    function get_client_ip() {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_X_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if(isset($_SERVER['REMOTE_ADDR']))
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }
    function terbilang($x) {
        $angka = ["", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas"];
        if ($x < 12)
            return " " . $angka[$x];
        elseif ($x < 20)
            return $this->terbilang($x - 10) . " belas";
        elseif ($x < 100)
            return $this->terbilang($x / 10) . " puluh" . $this->terbilang($x % 10);
        elseif ($x < 200)
            return " seratus" . $this->terbilang($x - 100);
        elseif ($x < 1000)
            return $this->terbilang($x / 100) . " ratus" . $this->terbilang($x % 100);
        elseif ($x < 2000)
            return " seribu" . $this->terbilang($x - 1000);
        elseif ($x < 1000000)
            return $this->terbilang($x / 1000) . " ribu" . $this->terbilang($x % 1000);
        elseif ($x < 1000000000)
            return $this->terbilang($x / 1000000) . " juta" . $this->terbilang($x % 1000000);
    }
    function keBulan($val){
	    $val = strtolower($val);
	    switch ($val){
            default :
            case    'januari' : $val = '01'; break;
            case    'februari' : $val = '02'; break;
            case    'maret' : $val = '03'; break;
            case    'april' : $val = '04'; break;
            case    'mei' : $val = '05'; break;
            case    'juni' : $val = '06'; break;
            case    'juli' : $val = '07'; break;
            case    'agustus' : $val = '08'; break;
            case    'september' : $val = '09'; break;
            case    'oktober' : $val = '10'; break;
            case    'nopember' : $val = '11'; break;
            case    'desember' : $val = '12'; break;
        }
        return $val;
    }
}