<?php

namespace Gravity_Forms\Gravity_Tools\Utils;

/**
* Provides methods for retrieving lists of geological data. 
*
* Each of the public methods allows two arguments:
*
* - $as_json          - bool     - Whether the data should be returned as JSON instead of an array.
* - $process_callback - callable - An optional callback that takes the data as a parameter and returns a modified version. 
*								   This can be useful for manipulating/ordering/modifying the data before use.
*
*/
class GeoData {

	/**
	* Provides a list of Countries organized by their 2-character country codes.
	*
	* @return array
	*/
	private static function countries_list() {
		return array(
			'AF' => __( 'Afghanistan', 'gravitytools' ),
			'AX' => __( 'Åland Islands', 'gravitytools' ),
			'AL' => __( 'Albania', 'gravitytools' ),
			'DZ' => __( 'Algeria', 'gravitytools' ),
			'AS' => __( 'American Samoa', 'gravitytools' ),
			'AD' => __( 'Andorra', 'gravitytools' ),
			'AO' => __( 'Angola', 'gravitytools' ),
			'AI' => __( 'Anguilla', 'gravitytools' ),
			'AQ' => __( 'Antarctica', 'gravitytools' ),
			'AG' => __( 'Antigua and Barbuda', 'gravitytools' ),
			'AR' => __( 'Argentina', 'gravitytools' ),
			'AM' => __( 'Armenia', 'gravitytools' ),
			'AW' => __( 'Aruba', 'gravitytools' ),
			'AU' => __( 'Australia', 'gravitytools' ),
			'AT' => __( 'Austria', 'gravitytools' ),
			'AZ' => __( 'Azerbaijan', 'gravitytools' ),
			'BS' => __( 'Bahamas', 'gravitytools' ),
			'BH' => __( 'Bahrain', 'gravitytools' ),
			'BD' => __( 'Bangladesh', 'gravitytools' ),
			'BB' => __( 'Barbados', 'gravitytools' ),
			'BY' => __( 'Belarus', 'gravitytools' ),
			'BE' => __( 'Belgium', 'gravitytools' ),
			'PW' => __( 'Belau', 'gravitytools' ),
			'BZ' => __( 'Belize', 'gravitytools' ),
			'BJ' => __( 'Benin', 'gravitytools' ),
			'BM' => __( 'Bermuda', 'gravitytools' ),
			'BT' => __( 'Bhutan', 'gravitytools' ),
			'BO' => __( 'Bolivia', 'gravitytools' ),
			'BQ' => __( 'Bonaire, Saint Eustatius and Saba', 'gravitytools' ),
			'BA' => __( 'Bosnia and Herzegovina', 'gravitytools' ),
			'BW' => __( 'Botswana', 'gravitytools' ),
			'BV' => __( 'Bouvet Island', 'gravitytools' ),
			'BR' => __( 'Brazil', 'gravitytools' ),
			'IO' => __( 'British Indian Ocean Territory', 'gravitytools' ),
			'BN' => __( 'Brunei', 'gravitytools' ),
			'BG' => __( 'Bulgaria', 'gravitytools' ),
			'BF' => __( 'Burkina Faso', 'gravitytools' ),
			'BI' => __( 'Burundi', 'gravitytools' ),
			'KH' => __( 'Cambodia', 'gravitytools' ),
			'CM' => __( 'Cameroon', 'gravitytools' ),
			'CA' => __( 'Canada', 'gravitytools' ),
			'CV' => __( 'Cape Verde', 'gravitytools' ),
			'KY' => __( 'Cayman Islands', 'gravitytools' ),
			'CF' => __( 'Central African Republic', 'gravitytools' ),
			'TD' => __( 'Chad', 'gravitytools' ),
			'CL' => __( 'Chile', 'gravitytools' ),
			'CN' => __( 'China', 'gravitytools' ),
			'CX' => __( 'Christmas Island', 'gravitytools' ),
			'CC' => __( 'Cocos (Keeling) Islands', 'gravitytools' ),
			'CO' => __( 'Colombia', 'gravitytools' ),
			'KM' => __( 'Comoros', 'gravitytools' ),
			'CG' => __( 'Congo (Brazzaville)', 'gravitytools' ),
			'CD' => __( 'Congo (Kinshasa)', 'gravitytools' ),
			'CK' => __( 'Cook Islands', 'gravitytools' ),
			'CR' => __( 'Costa Rica', 'gravitytools' ),
			'HR' => __( 'Croatia', 'gravitytools' ),
			'CU' => __( 'Cuba', 'gravitytools' ),
			'CW' => __( 'Cura&ccedil;ao', 'gravitytools' ),
			'CY' => __( 'Cyprus', 'gravitytools' ),
			'CZ' => __( 'Czech Republic', 'gravitytools' ),
			'DK' => __( 'Denmark', 'gravitytools' ),
			'DJ' => __( 'Djibouti', 'gravitytools' ),
			'DM' => __( 'Dominica', 'gravitytools' ),
			'DO' => __( 'Dominican Republic', 'gravitytools' ),
			'EC' => __( 'Ecuador', 'gravitytools' ),
			'EG' => __( 'Egypt', 'gravitytools' ),
			'SV' => __( 'El Salvador', 'gravitytools' ),
			'GQ' => __( 'Equatorial Guinea', 'gravitytools' ),
			'ER' => __( 'Eritrea', 'gravitytools' ),
			'EE' => __( 'Estonia', 'gravitytools' ),
			'ET' => __( 'Ethiopia', 'gravitytools' ),
			'FK' => __( 'Falkland Islands', 'gravitytools' ),
			'FO' => __( 'Faroe Islands', 'gravitytools' ),
			'FJ' => __( 'Fiji', 'gravitytools' ),
			'FI' => __( 'Finland', 'gravitytools' ),
			'FR' => __( 'France', 'gravitytools' ),
			'GF' => __( 'French Guiana', 'gravitytools' ),
			'PF' => __( 'French Polynesia', 'gravitytools' ),
			'TF' => __( 'French Southern Territories', 'gravitytools' ),
			'GA' => __( 'Gabon', 'gravitytools' ),
			'GM' => __( 'Gambia', 'gravitytools' ),
			'GE' => __( 'Georgia', 'gravitytools' ),
			'DE' => __( 'Germany', 'gravitytools' ),
			'GH' => __( 'Ghana', 'gravitytools' ),
			'GI' => __( 'Gibraltar', 'gravitytools' ),
			'GR' => __( 'Greece', 'gravitytools' ),
			'GL' => __( 'Greenland', 'gravitytools' ),
			'GD' => __( 'Grenada', 'gravitytools' ),
			'GP' => __( 'Guadeloupe', 'gravitytools' ),
			'GU' => __( 'Guam', 'gravitytools' ),
			'GT' => __( 'Guatemala', 'gravitytools' ),
			'GG' => __( 'Guernsey', 'gravitytools' ),
			'GN' => __( 'Guinea', 'gravitytools' ),
			'GW' => __( 'Guinea-Bissau', 'gravitytools' ),
			'GY' => __( 'Guyana', 'gravitytools' ),
			'HT' => __( 'Haiti', 'gravitytools' ),
			'HM' => __( 'Heard Island and McDonald Islands', 'gravitytools' ),
			'HN' => __( 'Honduras', 'gravitytools' ),
			'HK' => __( 'Hong Kong', 'gravitytools' ),
			'HU' => __( 'Hungary', 'gravitytools' ),
			'IS' => __( 'Iceland', 'gravitytools' ),
			'IN' => __( 'India', 'gravitytools' ),
			'ID' => __( 'Indonesia', 'gravitytools' ),
			'IR' => __( 'Iran', 'gravitytools' ),
			'IQ' => __( 'Iraq', 'gravitytools' ),
			'IE' => __( 'Ireland', 'gravitytools' ),
			'IM' => __( 'Isle of Man', 'gravitytools' ),
			'IL' => __( 'Israel', 'gravitytools' ),
			'IT' => __( 'Italy', 'gravitytools' ),
			'CI' => __( 'Ivory Coast', 'gravitytools' ),
			'JM' => __( 'Jamaica', 'gravitytools' ),
			'JP' => __( 'Japan', 'gravitytools' ),
			'JE' => __( 'Jersey', 'gravitytools' ),
			'JO' => __( 'Jordan', 'gravitytools' ),
			'KZ' => __( 'Kazakhstan', 'gravitytools' ),
			'KE' => __( 'Kenya', 'gravitytools' ),
			'KI' => __( 'Kiribati', 'gravitytools' ),
			'KW' => __( 'Kuwait', 'gravitytools' ),
			'KG' => __( 'Kyrgyzstan', 'gravitytools' ),
			'LA' => __( 'Laos', 'gravitytools' ),
			'LV' => __( 'Latvia', 'gravitytools' ),
			'LB' => __( 'Lebanon', 'gravitytools' ),
			'LS' => __( 'Lesotho', 'gravitytools' ),
			'LR' => __( 'Liberia', 'gravitytools' ),
			'LY' => __( 'Libya', 'gravitytools' ),
			'LI' => __( 'Liechtenstein', 'gravitytools' ),
			'LT' => __( 'Lithuania', 'gravitytools' ),
			'LU' => __( 'Luxembourg', 'gravitytools' ),
			'MO' => __( 'Macao', 'gravitytools' ),
			'MK' => __( 'North Macedonia', 'gravitytools' ),
			'MG' => __( 'Madagascar', 'gravitytools' ),
			'MW' => __( 'Malawi', 'gravitytools' ),
			'MY' => __( 'Malaysia', 'gravitytools' ),
			'MV' => __( 'Maldives', 'gravitytools' ),
			'ML' => __( 'Mali', 'gravitytools' ),
			'MT' => __( 'Malta', 'gravitytools' ),
			'MH' => __( 'Marshall Islands', 'gravitytools' ),
			'MQ' => __( 'Martinique', 'gravitytools' ),
			'MR' => __( 'Mauritania', 'gravitytools' ),
			'MU' => __( 'Mauritius', 'gravitytools' ),
			'YT' => __( 'Mayotte', 'gravitytools' ),
			'MX' => __( 'Mexico', 'gravitytools' ),
			'FM' => __( 'Micronesia', 'gravitytools' ),
			'MD' => __( 'Moldova', 'gravitytools' ),
			'MC' => __( 'Monaco', 'gravitytools' ),
			'MN' => __( 'Mongolia', 'gravitytools' ),
			'ME' => __( 'Montenegro', 'gravitytools' ),
			'MS' => __( 'Montserrat', 'gravitytools' ),
			'MA' => __( 'Morocco', 'gravitytools' ),
			'MZ' => __( 'Mozambique', 'gravitytools' ),
			'MM' => __( 'Myanmar', 'gravitytools' ),
			'NA' => __( 'Namibia', 'gravitytools' ),
			'NR' => __( 'Nauru', 'gravitytools' ),
			'NP' => __( 'Nepal', 'gravitytools' ),
			'NL' => __( 'Netherlands', 'gravitytools' ),
			'NC' => __( 'New Caledonia', 'gravitytools' ),
			'NZ' => __( 'New Zealand', 'gravitytools' ),
			'NI' => __( 'Nicaragua', 'gravitytools' ),
			'NE' => __( 'Niger', 'gravitytools' ),
			'NG' => __( 'Nigeria', 'gravitytools' ),
			'NU' => __( 'Niue', 'gravitytools' ),
			'NF' => __( 'Norfolk Island', 'gravitytools' ),
			'MP' => __( 'Northern Mariana Islands', 'gravitytools' ),
			'KP' => __( 'North Korea', 'gravitytools' ),
			'NO' => __( 'Norway', 'gravitytools' ),
			'OM' => __( 'Oman', 'gravitytools' ),
			'PK' => __( 'Pakistan', 'gravitytools' ),
			'PS' => __( 'Palestinian Territory', 'gravitytools' ),
			'PA' => __( 'Panama', 'gravitytools' ),
			'PG' => __( 'Papua New Guinea', 'gravitytools' ),
			'PY' => __( 'Paraguay', 'gravitytools' ),
			'PE' => __( 'Peru', 'gravitytools' ),
			'PH' => __( 'Philippines', 'gravitytools' ),
			'PN' => __( 'Pitcairn', 'gravitytools' ),
			'PL' => __( 'Poland', 'gravitytools' ),
			'PT' => __( 'Portugal', 'gravitytools' ),
			'PR' => __( 'Puerto Rico', 'gravitytools' ),
			'QA' => __( 'Qatar', 'gravitytools' ),
			'RE' => __( 'Reunion', 'gravitytools' ),
			'RO' => __( 'Romania', 'gravitytools' ),
			'RU' => __( 'Russia', 'gravitytools' ),
			'RW' => __( 'Rwanda', 'gravitytools' ),
			'BL' => __( 'Saint Barth&eacute;lemy', 'gravitytools' ),
			'SH' => __( 'Saint Helena', 'gravitytools' ),
			'KN' => __( 'Saint Kitts and Nevis', 'gravitytools' ),
			'LC' => __( 'Saint Lucia', 'gravitytools' ),
			'MF' => __( 'Saint Martin (French part)', 'gravitytools' ),
			'SX' => __( 'Saint Martin (Dutch part)', 'gravitytools' ),
			'PM' => __( 'Saint Pierre and Miquelon', 'gravitytools' ),
			'VC' => __( 'Saint Vincent and the Grenadines', 'gravitytools' ),
			'SM' => __( 'San Marino', 'gravitytools' ),
			'ST' => __( 'S&atilde;o Tom&eacute; and Pr&iacute;ncipe', 'gravitytools' ),
			'SA' => __( 'Saudi Arabia', 'gravitytools' ),
			'SN' => __( 'Senegal', 'gravitytools' ),
			'RS' => __( 'Serbia', 'gravitytools' ),
			'SC' => __( 'Seychelles', 'gravitytools' ),
			'SL' => __( 'Sierra Leone', 'gravitytools' ),
			'SG' => __( 'Singapore', 'gravitytools' ),
			'SK' => __( 'Slovakia', 'gravitytools' ),
			'SI' => __( 'Slovenia', 'gravitytools' ),
			'SB' => __( 'Solomon Islands', 'gravitytools' ),
			'SO' => __( 'Somalia', 'gravitytools' ),
			'ZA' => __( 'South Africa', 'gravitytools' ),
			'GS' => __( 'South Georgia/Sandwich Islands', 'gravitytools' ),
			'KR' => __( 'South Korea', 'gravitytools' ),
			'SS' => __( 'South Sudan', 'gravitytools' ),
			'ES' => __( 'Spain', 'gravitytools' ),
			'LK' => __( 'Sri Lanka', 'gravitytools' ),
			'SD' => __( 'Sudan', 'gravitytools' ),
			'SR' => __( 'Suriname', 'gravitytools' ),
			'SJ' => __( 'Svalbard and Jan Mayen', 'gravitytools' ),
			'SZ' => __( 'Eswatini', 'gravitytools' ),
			'SE' => __( 'Sweden', 'gravitytools' ),
			'CH' => __( 'Switzerland', 'gravitytools' ),
			'SY' => __( 'Syria', 'gravitytools' ),
			'TW' => __( 'Taiwan', 'gravitytools' ),
			'TJ' => __( 'Tajikistan', 'gravitytools' ),
			'TZ' => __( 'Tanzania', 'gravitytools' ),
			'TH' => __( 'Thailand', 'gravitytools' ),
			'TL' => __( 'Timor-Leste', 'gravitytools' ),
			'TG' => __( 'Togo', 'gravitytools' ),
			'TK' => __( 'Tokelau', 'gravitytools' ),
			'TO' => __( 'Tonga', 'gravitytools' ),
			'TT' => __( 'Trinidad and Tobago', 'gravitytools' ),
			'TN' => __( 'Tunisia', 'gravitytools' ),
			'TR' => __( 'Turkey', 'gravitytools' ),
			'TM' => __( 'Turkmenistan', 'gravitytools' ),
			'TC' => __( 'Turks and Caicos Islands', 'gravitytools' ),
			'TV' => __( 'Tuvalu', 'gravitytools' ),
			'UG' => __( 'Uganda', 'gravitytools' ),
			'UA' => __( 'Ukraine', 'gravitytools' ),
			'AE' => __( 'United Arab Emirates', 'gravitytools' ),
			'GB' => __( 'United Kingdom (UK)', 'gravitytools' ),
			'US' => __( 'United States (US)', 'gravitytools' ),
			'UM' => __( 'United States (US) Minor Outlying Islands', 'gravitytools' ),
			'UY' => __( 'Uruguay', 'gravitytools' ),
			'UZ' => __( 'Uzbekistan', 'gravitytools' ),
			'VU' => __( 'Vanuatu', 'gravitytools' ),
			'VA' => __( 'Vatican', 'gravitytools' ),
			'VE' => __( 'Venezuela', 'gravitytools' ),
			'VN' => __( 'Vietnam', 'gravitytools' ),
			'VG' => __( 'Virgin Islands (British)', 'gravitytools' ),
			'VI' => __( 'Virgin Islands (US)', 'gravitytools' ),
			'WF' => __( 'Wallis and Futuna', 'gravitytools' ),
			'EH' => __( 'Western Sahara', 'gravitytools' ),
			'WS' => __( 'Samoa', 'gravitytools' ),
			'YE' => __( 'Yemen', 'gravitytools' ),
			'ZM' => __( 'Zambia', 'gravitytools' ),
			'ZW' => __( 'Zimbabwe', 'gravitytools' ),
		);
	}

	/**
	* Provides a list of US states organized by their two-character state code.
	*
	* @return array
	*/
	private static function states_list() {
		return array(
			'AL' => 'Alabama',
			'AK' => 'Alaska',
			'AZ' => 'Arizona',
			'AR' => 'Arkansas',
			'CA' => 'California',
			'CO' => 'Colorado',
			'CT' => 'Connecticut',
			'DE' => 'Delaware',
			'DC' => 'District of Columbia',
			'FL' => 'Florida',
			'GA' => 'Georgia',
			'HI' => 'Hawaii',
			'ID' => 'Idaho',
			'IL' => 'Illinois',
			'IN' => 'Indiana',
			'IA' => 'Iowa',
			'KS' => 'Kansas',
			'KY' => 'Kentucky',
			'LA' => 'Louisiana',
			'ME' => 'Maine',
			'MD' => 'Maryland',
			'MA' => 'Massachusetts',
			'MI' => 'Michigan',
			'MN' => 'Minnesota',
			'MS' => 'Mississippi',
			'MO' => 'Missouri',
			'MT' => 'Montana',
			'NE' => 'Nebraska',
			'NV' => 'Nevada',
			'NH' => 'New Hampshire',
			'NJ' => 'New Jersey',
			'NM' => 'New Mexico',
			'NY' => 'New York',
			'NC' => 'North Carolina',
			'ND' => 'North Dakota',
			'OH' => 'Ohio',
			'OK' => 'Oklahoma',
			'OR' => 'Oregon',
			'PA' => 'Pennsylvania',
			'RI' => 'Rhode Island',
			'SC' => 'South Carolina',
			'SD' => 'South Dakota',
			'TN' => 'Tennessee',
			'TX' => 'Texas',
			'UT' => 'Utah',
			'VT' => 'Vermont',
			'VA' => 'Virginia',
			'WA' => 'Washington',
			'WV' => 'West Virginia',
			'WI' => 'Wisconsin',
			'WY' => 'Wyoming',
		);
	}

	/**
	* Provides a list of Canadian provinces, organized by their two-character province code.
	*
	* @return array
	*/
	private static function provinces_list() {
		return array(
			'AB' => 'Alberta',
			'BC' => 'British Columbia',
			'MB' => 'Manitoba',
			'NB' => 'New Brunswick',
			'NL' => 'Newfoundland and Labrador',
			'NS' => 'Nova Scotia',
			'NT' => 'Northwest Territories',
			'NU' => 'Nunavut',
			'ON' => 'Ontario',
			'PE' => 'Prince Edward Island',
			'QC' => 'Quebec',
			'SK' => 'Saskatchewan',
			'YT' => 'Yukon',  
		);
	}

	/**
	* Retrieves the given list of data by type. Helper method used to route the individual type requests
	* through to the appropriate data list method.
	*
	* @param $type - string - The type of data to retrieve.
	* @param $as_json - boolean - Whether to retrieve this data as as JSON string.
	* @param $process_callback - callable - An optional callback for transforming the data before returning.
	*
	* @return string|array
	*/
	private static function get_data_by_type( $type, $as_json = false, $process_callback = null ) {
		switch( $type ) {
			case 'country':
				$data = self::countries_list();
				break;
			case 'state':
				$data = self::states_list();
				break;
			case 'province':
				$data = self::provinces_list();
				break;
			default:
				$data = array();
				break;
		}

		if ( ! is_null( $process_callback ) ) {
			$data = call_user_func( $process_callback, $data );
		}

		return $as_json ? json_encode( $data ) : $data;
	}

	/**
	* Provides an array of US States.
	*
	* @param $as_json - boolean - Whether to retrieve this data as a JSON string.
	* @param $process_callback - callable - An optional callback for transforming the data before returning.
	*
	* @return string|array
	*/
	public static function states( $as_json = false, $process_callback = null ) {
		return self::get_data_by_type( 'state', $as_json, $process_callback );
	}

	/**
	* Provides an array of Canadian Provinces.
	*
	* @param $as_json - boolean - Whether to retrieve this data as a JSON string.
	* @param $process_callback - callable - An optional callback for transforming the data before returning.
	*
	* @return string|array
	*/
	public static function provinces( $as_json = false, $process_callback = null ) {
		return self::get_data_by_type( 'province', $as_json, $process_callback );
	}

	/**
	* Provides an array of Countries.
	*
	* @param $as_json - boolean - Whether to retrieve this data as a JSON string.
	* @param $process_callback - callable - An optional callback for transforming the data before returning.
	*
	* @return string|array
	*/
	public static function countries( $as_json = false, $process_callback = null ) {
		return self::get_data_by_type( 'country', $as_json, $process_callback );
	}
}


