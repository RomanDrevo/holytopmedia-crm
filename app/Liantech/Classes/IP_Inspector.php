<?php

namespace App\Liantech\Classes;

/**
* Get all user information based on IP and GeoLocation
*/
class IP_Inspector
{
	/**
	 * current user ip
	 * @var string
	 */
	protected $userIp;

	/**
	 * current user location array
	 * @var Array
	 */
	protected $location;

	/**
	 * all possible continents
	 * @var Array
	 */
	protected $continents = [
		"AF" => "Africa",
        "AN" => "Antarctica",
        "AS" => "Asia",
        "EU" => "Europe",
        "OC" => "Australia (Oceania)",
        "NA" => "North America",
        "SA" => "South America"
    ];

	function __construct()
	{
		$this->generateUserGeoData();
	}

	/**
	 * getters
	 */
	public function getIp()
	{
		return $this->userIp;
	}

	public function getLocationArray()
	{
		return $this->location;
	}

	/**
	 * Get all user data based on IP
	 */
	private function generateUserGeoData()
	{
		$ip = $_SERVER["REMOTE_ADDR"];

		if (filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP))
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        if (filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP))
            $ip = $_SERVER['HTTP_CLIENT_IP'];

        $this->userIp = $ip;

	    if (filter_var($ip, FILTER_VALIDATE_IP)){
	    	$ipdat = json_decode( file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $ip) );
	    	if (strlen(trim($ipdat->geoplugin_countryCode)) == 2) {
	    		$this->location = array(
                    "city"           => $ipdat->geoplugin_city,
                    "state"          => $ipdat->geoplugin_regionName,
                    "country"        => $ipdat->geoplugin_countryName,
                    "country_code"   => $ipdat->geoplugin_countryCode,
                    "continent"      => $this->continents[strtoupper($ipdat->geoplugin_continentCode)],
                    "continent_code" => $ipdat->geoplugin_continentCode
                );
	    	}
	    }
	}

	/**
	 * Get the user's currency
	 * if not in array returns USD as default
	 * 
	 * @return string
	 */
	public function getCurrency() {

		$countries = Array(
			"austria" => "EUR",
			"belgium" => "EUR",
			"bulgaria" => "EUR",
			"croatia" => "EUR",
			"cyprus" => "EUR",
			"czech republic" => "EUR",
			"denmark" => "EUR",
			"estonia" => "EUR",
			"finaland" => "EUR",
			"france" => "EUR",
			"germany" => "EUR",
			"greece" => "EUR",
			"hungary" => "EUR",
			"ireland" => "GBP",
			"italy" => "EUR",
			"latvia" => "EUR",
			"lithuania" => "EUR",
			"luxembourg" => "EUR",
			"malta" => "EUR",
			"netherlands" => "EUR",
			"poland" => "EUR",
			"portugal" => "EUR",
			"romania" => "EUR",
			"slovakia" => "EUR",
			"spain" => "EUR",
			"sweden" => "EUR",
			"united kingdom" => "GBP",
			"south africa" => "GBP"
		);

		return isset($countries[ $this->location["country"] ]) ? $countries[ $this->location["country"] ] : "USD";
	}

	public function getCountryId()
	{
		$countries = [
      		"1" => "Afghanistan",
			"2" => "Albania",
			"3" => "Algeria",
			"4" => "American Samoa",
			"5" => "Andorra",
			"6" => "Angola",
			"7" => "Anguilla",
			"8" => "Antarctica",
			"9" => "Antigua and Barbuda",
			"10" => "Argentina",
			"11" => "Armenia",
			"12" => "Aruba",
			"13" => "Australia",
			"14" => "Austria",
			"15" => "Azerbaijan",
			"16" => "Bahamas",
			"17" => "Bahrain",
			"18" => "Bangladesh",
			"19" => "Barbados",
			"20" => "Belarus",
			"21" => "Belgium",
			"22" => "Belize",
			"23" => "Benin",
			"24" => "Bermuda",
			"25" => "Bhutan",
			"26" => "Bolivia",
			"27" => "Bosnia and Herzegovina",
			"28" => "Botswana",
			"29" => "Bouvet Island",
			"30" => "Brazil",
			"31" => "British Indian...",
			"32" => "Brunei Darussalam",
			"33" => "Bulgaria",
			"34" => "Burkina Faso",
			"35" => "Burundi",
			"36" => "Cambodia",
			"37" => "Cameroon",
			"38" => "Canada",
			"39" => "Cape Verde",
			"40" => "Cayman Islands",
			"41" => "Central African...",
			"42" => "Chad",
			"43" => "Chile",
			"44" => "China",
			"45" => "Christmas Island",
			"46" => "Cocos (Keeling)...",
			"47" => "Colombia",
			"48" => "Comoros",
			"49" => "Congo",
			"50" => "Congo, the...",
			"51" => "Cook Islands",
			"52" => "Costa Rica",
			"53" => "Cote D\'Ivoire",
			"54" => "Croatia",
			"55" => "Cuba",
			"56" => "Cyprus",
			"57" => "Czech Republic",
			"58" => "Denmark",
			"59" => "Djibouti",
			"60" => "Dominica",
			"61" => "Dominican Republic",
			"62" => "Ecuador",
			"63" => "Egypt",
			"64" => "El Salvador",
			"65" => "Equatorial Guinea",
			"66" => "Eritrea",
			"67" => "Estonia",
			"68" => "Ethiopia",
			"69" => "Falkland Islands...",
			"70" => "Faroe Islands",
			"71" => "Fiji",
			"72" => "Finland",
			"73" => "France",
			"74" => "French Guiana",
			"75" => "French Polynesia",
			"76" => "French Southern...",
			"77" => "Gabon",
			"78" => "Gambia",
			"79" => "Georgia",
			"80" => "Germany",
			"81" => "Ghana",
			"82" => "Gibraltar",
			"83" => "Greece",
			"84" => "Greenland",
			"85" => "Grenada",
			"86" => "Guadeloupe",
			"87" => "Guam",
			"88" => "Guatemala",
			"89" => "Guinea",
			"90" => "Guinea-Bissau",
			"91" => "Guyana",
			"92" => "Haiti",
			"93" => "Heard Island and...",
			"94" => "Holy See (Vatican...",
			"95" => "Honduras",
			"96" => "Hong Kong",
			"97" => "Hungary",
			"98" => "Iceland",
			"99" => "India",
			"100" => "Indonesia",
			"101" => "Iran, Islamic...",
			"102" => "Iraq",
			"103" => "Ireland",
			"104" => "Israel",
			"105" => "Italy",
			"106" => "Jamaica",
			"107" => "Japan",
			"108" => "Jordan",
			"109" => "Kazakhstan",
			"110" => "Kenya",
			"111" => "Kiribati",
			"112" => "Korea, Democratic...",
			"113" => "Korea, Republic of",
			"114" => "Kuwait",
			"115" => "Kyrgyzstan",
			"116" => "Lao People\'s...",
			"117" => "Latvia",
			"118" => "Lebanon",
			"119" => "Lesotho",
			"120" => "Liberia",
			"121" => "Libyan Arab Jamahiriya",
			"122" => "Liechtenstein",
			"123" => "Lithuania",
			"124" => "Luxembourg",
			"125" => "Macao",
			"126" => "Macedonia, the...",
			"127" => "Madagascar",
			"128" => "Malawi",
			"129" => "Malaysia",
			"130" => "Maldives",
			"131" => "Mali",
			"132" => "Malta",
			"133" => "Marshall Islands",
			"134" => "Martinique",
			"135" => "Mauritania",
			"136" => "Mauritius",
			"137" => "Mayotte",
			"138" => "Mexico",
			"139" => "Micronesia,...",
			"140" => "Moldova, Republic of",
			"141" => "Monaco",
			"142" => "Mongolia",
			"143" => "Montserrat",
			"144" => "Morocco",
			"145" => "Mozambique",
			"146" => "Myanmar",
			"147" => "Namibia",
			"148" => "Nauru",
			"149" => "Nepal",
			"150" => "Netherlands",
			"151" => "Netherlands Antilles",
			"152" => "New Caledonia",
			"153" => "New Zealand",
			"154" => "Nicaragua",
			"155" => "Niger",
			"156" => "Nigeria",
			"157" => "Niue",
			"158" => "Norfolk Island",
			"159" => "Northern Mariana...",
			"160" => "Norway",
			"161" => "Oman",
			"162" => "Pakistan",
			"163" => "Palau",
			"164" => "Palestinian...",
			"165" => "Panama",
			"166" => "Papua New Guinea",
			"167" => "Paraguay",
			"168" => "Peru",
			"169" => "Philippines",
			"170" => "Pitcairn",
			"171" => "Poland",
			"172" => "Portugal",
			"173" => "Puerto Rico",
			"174" => "Qatar",
			"175" => "Reunion",
			"176" => "Romania",
			"177" => "Russia",
			"178" => "Rwanda",
			"179" => "Saint Helena",
			"180" => "Saint Kitts and Nevis",
			"181" => "Saint Lucia",
			"182" => "Saint Pierre and...",
			"183" => "Saint Vincent and...",
			"184" => "Samoa",
			"185" => "San Marino",
			"186" => "Sao Tome and Principe",
			"187" => "Saudi Arabia",
			"188" => "Senegal",
			"189" => "Serbia and Montenegro",
			"190" => "Seychelles",
			"191" => "Sierra Leone",
			"192" => "Singapore",
			"193" => "Slovakia",
			"194" => "Slovenia",
			"195" => "Solomon Islands",
			"196" => "Somalia",
			"197" => "South Africa",
			"198" => "South Georgia and...",
			"199" => "Spain",
			"200" => "Sri Lanka",
			"201" => "Sudan",
			"202" => "Suriname",
			"203" => "Svalbard and Jan Mayen",
			"204" => "Swaziland",
			"205" => "Sweden",
			"206" => "Switzerland",
			"207" => "Syrian Arab Republic",
			"208" => "Taiwan, Province of...",
			"209" => "Tajikistan",
			"210" => "Tanzania, United...",
			"211" => "Thailand",
			"212" => "Timor-Leste",
			"213" => "Togo",
			"214" => "Tokelau",
			"215" => "Tonga",
			"216" => "Trinidad and Tobago",
			"217" => "Tunisia",
			"218" => "Turkey",
			"219" => "Turkmenistan",
			"220" => "Turks and Caicos...",
			"221" => "Tuvalu",
			"222" => "Uganda",
			"223" => "Ukraine",
			"224" => "United Arab Emirates",
			"225" => "United Kingdom",
			"226" => "United States",
			"227" => "United States Minor...",
			"228" => "Uruguay",
			"229" => "Uzbekistan",
			"230" => "Vanuatu",
			"231" => "Venezuela",
			"232" => "Viet Nam",
			"233" => "Virgin Islands,...",
			"234" => "Virgin Islands, U.s.",
			"235" => "Wallis and Futuna",
			"236" => "Western Sahara",
			"237" => "Yemen",
			"238" => "Zambia",
			"239" => "Zimbabwe"
		];

		return array_search( $this->location["country"], $countries);
	}
}