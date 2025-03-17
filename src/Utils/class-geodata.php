<?php

namespace Gravity_Forms\Gravity_Tools\Utils;

/**
 * Provides methods for retrieving lists of geological data.
 *
 * Each of the public methods allows two arguments:
 *
 * @param $as_json          - bool     - Whether the data should be returned as JSON instead of an array.
 * @param $process_callback - callable - An optional callback that takes the data as a parameter and returns a modified version.
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
			'AL' => __( 'Alabama', 'gravitytools' ),
			'AK' => __( 'Alaska', 'gravitytools' ),
			'AZ' => __( 'Arizona', 'gravitytools' ),
			'AR' => __( 'Arkansas', 'gravitytools' ),
			'CA' => __( 'California', 'gravitytools' ),
			'CO' => __( 'Colorado', 'gravitytools' ),
			'CT' => __( 'Connecticut', 'gravitytools' ),
			'DE' => __( 'Delaware', 'gravitytools' ),
			'DC' => __( 'District of Columbia', 'gravitytools' ),
			'FL' => __( 'Florida', 'gravitytools' ),
			'GA' => __( 'Georgia', 'gravitytools' ),
			'HI' => __( 'Hawaii', 'gravitytools' ),
			'ID' => __( 'Idaho', 'gravitytools' ),
			'IL' => __( 'Illinois', 'gravitytools' ),
			'IN' => __( 'Indiana', 'gravitytools' ),
			'IA' => __( 'Iowa', 'gravitytools' ),
			'KS' => __( 'Kansas', 'gravitytools' ),
			'KY' => __( 'Kentucky', 'gravitytools' ),
			'LA' => __( 'Louisiana', 'gravitytools' ),
			'ME' => __( 'Maine', 'gravitytools' ),
			'MD' => __( 'Maryland', 'gravitytools' ),
			'MA' => __( 'Massachusetts', 'gravitytools' ),
			'MI' => __( 'Michigan', 'gravitytools' ),
			'MN' => __( 'Minnesota', 'gravitytools' ),
			'MS' => __( 'Mississippi', 'gravitytools' ),
			'MO' => __( 'Missouri', 'gravitytools' ),
			'MT' => __( 'Montana', 'gravitytools' ),
			'NE' => __( 'Nebraska', 'gravitytools' ),
			'NV' => __( 'Nevada', 'gravitytools' ),
			'NH' => __( 'New Hampshire', 'gravitytools' ),
			'NJ' => __( 'New Jersey', 'gravitytools' ),
			'NM' => __( 'New Mexico', 'gravitytools' ),
			'NY' => __( 'New York', 'gravitytools' ),
			'NC' => __( 'North Carolina', 'gravitytools' ),
			'ND' => __( 'North Dakota', 'gravitytools' ),
			'OH' => __( 'Ohio', 'gravitytools' ),
			'OK' => __( 'Oklahoma', 'gravitytools' ),
			'OR' => __( 'Oregon', 'gravitytools' ),
			'PA' => __( 'Pennsylvania', 'gravitytools' ),
			'RI' => __( 'Rhode Island', 'gravitytools' ),
			'SC' => __( 'South Carolina', 'gravitytools' ),
			'SD' => __( 'South Dakota', 'gravitytools' ),
			'TN' => __( 'Tennessee', 'gravitytools' ),
			'TX' => __( 'Texas', 'gravitytools' ),
			'UT' => __( 'Utah', 'gravitytools' ),
			'VT' => __( 'Vermont', 'gravitytools' ),
			'VA' => __( 'Virginia', 'gravitytools' ),
			'WA' => __( 'Washington', 'gravitytools' ),
			'WV' => __( 'West Virginia', 'gravitytools' ),
			'WI' => __( 'Wisconsin', 'gravitytools' ),
			'WY' => __( 'Wyoming', 'gravitytools' ),
		);
	}

	/**
	 * Provides a list of Canadian provinces, organized by their two-character province code.
	 *
	 * @return array
	 */
	private static function provinces_list() {
		return array(
			'AB' => __( 'Alberta', 'gravitytools' ),
			'BC' => __( 'British Columbia', 'gravitytools' ),
			'MB' => __( 'Manitoba', 'gravitytools' ),
			'NB' => __( 'New Brunswick', 'gravitytools' ),
			'NL' => __( 'Newfoundland and Labrador', 'gravitytools' ),
			'NS' => __( 'Nova Scotia', 'gravitytools' ),
			'NT' => __( 'Northwest Territories', 'gravitytools' ),
			'NU' => __( 'Nunavut', 'gravitytools' ),
			'ON' => __( 'Ontario', 'gravitytools' ),
			'PE' => __( 'Prince Edward Island', 'gravitytools' ),
			'QC' => __( 'Quebec', 'gravitytools' ),
			'SK' => __( 'Saskatchewan', 'gravitytools' ),
			'YT' => __( 'Yukon', 'gravitytools' ),
		);
	}

	/**
	 * Provides a list of phone number formatting info.
	 *
	 * @return array
	 */
	private static function phone_list() {
		return array(
			array(
				'iso'          => 'AF',
				'calling_code' => '93',
				'flag'         => '🇦🇫',
			),
			array(
				'iso'          => 'AX',
				'calling_code' => '358',
				'flag'         => '🇦🇽',
			),
			array(
				'iso'          => 'AL',
				'calling_code' => '355',
				'flag'         => '🇦🇱',
			),
			array(
				'iso'          => 'DZ',
				'calling_code' => '213',
				'flag'         => '🇩🇿',
			),
			array(
				'iso'          => 'AS',
				'calling_code' => '1',
				'flag'         => '🇦🇸',
			),
			array(
				'iso'          => 'AD',
				'calling_code' => '376',
				'flag'         => '🇦🇩',
			),
			array(
				'iso'          => 'AO',
				'calling_code' => '244',
				'flag'         => '🇦🇴',
			),
			array(
				'iso'          => 'AI',
				'calling_code' => '1',
				'flag'         => '🇦🇮',
			),
			array(
				'iso'          => 'AG',
				'calling_code' => '1',
				'flag'         => '🇦🇬',
			),
			array(
				'iso'          => 'AR',
				'calling_code' => '54',
				'flag'         => '🇦🇷',
			),
			array(
				'iso'          => 'AM',
				'calling_code' => '374',
				'flag'         => '🇦🇲',
			),
			array(
				'iso'          => 'AW',
				'calling_code' => '297',
				'flag'         => '🇦🇼',
			),
			array(
				'iso'          => 'AC',
				'calling_code' => '247',
				'flag'         => '🇦🇨',
			),
			array(
				'iso'          => 'AU',
				'calling_code' => '61',
				'flag'         => '🇦🇺',
			),
			array(
				'iso'          => 'AT',
				'calling_code' => '43',
				'flag'         => '🇦🇹',
			),
			array(
				'iso'          => 'AZ',
				'calling_code' => '994',
				'flag'         => '🇦🇿',
			),
			array(
				'iso'          => 'BS',
				'calling_code' => '1',
				'flag'         => '🇧🇸',
			),
			array(
				'iso'          => 'BH',
				'calling_code' => '973',
				'flag'         => '🇧🇭',
			),
			array(
				'iso'          => 'BD',
				'calling_code' => '880',
				'flag'         => '🇧🇩',
			),
			array(
				'iso'          => 'BB',
				'calling_code' => '1',
				'flag'         => '🇧🇧',
			),
			array(
				'iso'          => 'BY',
				'calling_code' => '375',
				'flag'         => '🇧🇾',
			),
			array(
				'iso'          => 'BE',
				'calling_code' => '32',
				'flag'         => '🇧🇪',
			),
			array(
				'iso'          => 'BZ',
				'calling_code' => '501',
				'flag'         => '🇧🇿',
			),
			array(
				'iso'          => 'BJ',
				'calling_code' => '229',
				'flag'         => '🇧🇯',
			),
			array(
				'iso'          => 'BM',
				'calling_code' => '1',
				'flag'         => '🇧🇲',
			),
			array(
				'iso'          => 'BT',
				'calling_code' => '975',
				'flag'         => '🇧🇹',
			),
			array(
				'iso'          => 'BO',
				'calling_code' => '591',
				'flag'         => '🇧🇴',
			),
			array(
				'iso'          => 'BA',
				'calling_code' => '387',
				'flag'         => '🇧🇦',
			),
			array(
				'iso'          => 'BW',
				'calling_code' => '267',
				'flag'         => '🇧🇼',
			),
			array(
				'iso'          => 'BR',
				'calling_code' => '55',
				'flag'         => '🇧🇷',
			),
			array(
				'iso'          => 'IO',
				'calling_code' => '246',
				'flag'         => '🇮🇴',
			),
			array(
				'iso'          => 'VG',
				'calling_code' => '1',
				'flag'         => '🇻🇬',
			),
			array(
				'iso'          => 'BN',
				'calling_code' => '673',
				'flag'         => '🇧🇳',
			),
			array(
				'iso'          => 'BG',
				'calling_code' => '359',
				'flag'         => '🇧🇬',
			),
			array(
				'iso'          => 'BF',
				'calling_code' => '226',
				'flag'         => '🇧🇫',
			),
			array(
				'iso'          => 'BI',
				'calling_code' => '257',
				'flag'         => '🇧🇮',
			),
			array(
				'iso'          => 'KH',
				'calling_code' => '855',
				'flag'         => '🇰🇭',
			),
			array(
				'iso'          => 'CM',
				'calling_code' => '237',
				'flag'         => '🇨🇲',
			),
			array(
				'iso'          => 'CA',
				'calling_code' => '1',
				'flag'         => '🇨🇦',
			),
			array(
				'iso'          => 'CV',
				'calling_code' => '238',
				'flag'         => '🇨🇻',
			),
			array(
				'iso'          => 'BQ',
				'calling_code' => '599',
				'flag'         => '🇧🇶',
			),
			array(
				'iso'          => 'KY',
				'calling_code' => '1',
				'flag'         => '🇰🇾',
			),
			array(
				'iso'          => 'CF',
				'calling_code' => '236',
				'flag'         => '🇨🇫',
			),
			array(
				'iso'          => 'TD',
				'calling_code' => '235',
				'flag'         => '🇹🇩',
			),
			array(
				'iso'          => 'CL',
				'calling_code' => '56',
				'flag'         => '🇨🇱',
			),
			array(
				'iso'          => 'CN',
				'calling_code' => '86',
				'flag'         => '🇨🇳',
			),
			array(
				'iso'          => 'CX',
				'calling_code' => '61',
				'flag'         => '🇨🇽',
			),
			array(
				'iso'          => 'CC',
				'calling_code' => '61',
				'flag'         => '🇨🇨',
			),
			array(
				'iso'          => 'CO',
				'calling_code' => '57',
				'flag'         => '🇨🇴',
			),
			array(
				'iso'          => 'KM',
				'calling_code' => '269',
				'flag'         => '🇰🇲',
			),
			array(
				'iso'          => 'CG',
				'calling_code' => '242',
				'flag'         => '🇨🇬',
			),
			array(
				'iso'          => 'CD',
				'calling_code' => '243',
				'flag'         => '🇨🇩',
			),
			array(
				'iso'          => 'CK',
				'calling_code' => '682',
				'flag'         => '🇨🇰',
			),
			array(
				'iso'          => 'CR',
				'calling_code' => '506',
				'flag'         => '🇨🇷',
			),
			array(
				'iso'          => 'CI',
				'calling_code' => '225',
				'flag'         => '🇨🇮',
			),
			array(
				'iso'          => 'HR',
				'calling_code' => '385',
				'flag'         => '🇭🇷',
			),
			array(
				'iso'          => 'CU',
				'calling_code' => '53',
				'flag'         => '🇨🇺',
			),
			array(
				'iso'          => 'CW',
				'calling_code' => '599',
				'flag'         => '🇨🇼',
			),
			array(
				'iso'          => 'CY',
				'calling_code' => '357',
				'flag'         => '🇨🇾',
			),
			array(
				'iso'          => 'CZ',
				'calling_code' => '420',
				'flag'         => '🇨🇿',
			),
			array(
				'iso'          => 'DK',
				'calling_code' => '45',
				'flag'         => '🇩🇰',
			),
			array(
				'iso'          => 'DJ',
				'calling_code' => '253',
				'flag'         => '🇩🇯',
			),
			array(
				'iso'          => 'DM',
				'calling_code' => '1',
				'flag'         => '🇩🇲',
			),
			array(
				'iso'          => 'DO',
				'calling_code' => '1',
				'flag'         => '🇩🇴',
			),
			array(
				'iso'          => 'EC',
				'calling_code' => '593',
				'flag'         => '🇪🇨',
			),
			array(
				'iso'          => 'EG',
				'calling_code' => '20',
				'flag'         => '🇪🇬',
			),
			array(
				'iso'          => 'SV',
				'calling_code' => '503',
				'flag'         => '🇸🇻',
			),
			array(
				'iso'          => 'GQ',
				'calling_code' => '240',
				'flag'         => '🇬🇶',
			),
			array(
				'iso'          => 'ER',
				'calling_code' => '291',
				'flag'         => '🇪🇷',
			),
			array(
				'iso'          => 'EE',
				'calling_code' => '372',
				'flag'         => '🇪🇪',
			),
			array(
				'iso'          => 'SZ',
				'calling_code' => '268',
				'flag'         => '🇸🇿',
			),
			array(
				'iso'          => 'ET',
				'calling_code' => '251',
				'flag'         => '🇪🇹',
			),
			array(
				'iso'          => 'FK',
				'calling_code' => '500',
				'flag'         => '🇫🇰',
			),
			array(
				'iso'          => 'FO',
				'calling_code' => '298',
				'flag'         => '🇫🇴',
			),
			array(
				'iso'          => 'FJ',
				'calling_code' => '679',
				'flag'         => '🇫🇯',
			),
			array(
				'iso'          => 'FI',
				'calling_code' => '358',
				'flag'         => '🇫🇮',
			),
			array(
				'iso'          => 'FR',
				'calling_code' => '33',
				'flag'         => '🇫🇷',
			),
			array(
				'iso'          => 'GF',
				'calling_code' => '594',
				'flag'         => '🇬🇫',
			),
			array(
				'iso'          => 'PF',
				'calling_code' => '689',
				'flag'         => '🇵🇫',
			),
			array(
				'iso'          => 'GA',
				'calling_code' => '241',
				'flag'         => '🇬🇦',
			),
			array(
				'iso'          => 'GM',
				'calling_code' => '220',
				'flag'         => '🇬🇲',
			),
			array(
				'iso'          => 'GE',
				'calling_code' => '995',
				'flag'         => '🇬🇪',
			),
			array(
				'iso'          => 'DE',
				'calling_code' => '49',
				'flag'         => '🇩🇪',
			),
			array(
				'iso'          => 'GH',
				'calling_code' => '233',
				'flag'         => '🇬🇭',
			),
			array(
				'iso'          => 'GI',
				'calling_code' => '350',
				'flag'         => '🇬🇮',
			),
			array(
				'iso'          => 'GR',
				'calling_code' => '30',
				'flag'         => '🇬🇷',
			),
			array(
				'iso'          => 'GL',
				'calling_code' => '299',
				'flag'         => '🇬🇱',
			),
			array(
				'iso'          => 'GD',
				'calling_code' => '1',
				'flag'         => '🇬🇩',
			),
			array(
				'iso'          => 'GP',
				'calling_code' => '590',
				'flag'         => '🇬🇵',
			),
			array(
				'iso'          => 'GU',
				'calling_code' => '1',
				'flag'         => '괌',
			),
			array(
				'iso'          => 'GT',
				'calling_code' => '502',
				'flag'         => '🇬🇹',
			),
			array(
				'iso'          => 'GG',
				'calling_code' => '44',
				'flag'         => '🇬🇬',
			),
			array(
				'iso'          => 'GN',
				'calling_code' => '224',
				'flag'         => '🇬🇳',
			),
			array(
				'iso'          => 'GW',
				'calling_code' => '245',
				'flag'         => '🇬🇼',
			),
			array(
				'iso'          => 'GY',
				'calling_code' => '592',
				'flag'         => '🇬🇾',
			),
			array(
				'iso'          => 'HT',
				'calling_code' => '509',
				'flag'         => '🇭🇹',
			),
			array(
				'iso'          => 'HN',
				'calling_code' => '504',
				'flag'         => '🇭🇳',
			),
			array(
				'iso'          => 'HK',
				'calling_code' => '852',
				'flag'         => '🇭🇰',
			),
			array(
				'iso'          => 'HU',
				'calling_code' => '36',
				'flag'         => '🇭🇺',
			),
			array(
				'iso'          => 'IS',
				'calling_code' => '354',
				'flag'         => '🇮🇸',
			),
			array(
				'iso'          => 'IN',
				'calling_code' => '91',
				'flag'         => '🇮🇳',
			),
			array(
				'iso'          => 'ID',
				'calling_code' => '62',
				'flag'         => '🇮🇩',
			),
			array(
				'iso'          => 'IR',
				'calling_code' => '98',
				'flag'         => '🇮🇷',
			),
			array(
				'iso'          => 'IQ',
				'calling_code' => '964',
				'flag'         => '🇮🇶',
			),
			array(
				'iso'          => 'IE',
				'calling_code' => '353',
				'flag'         => '🇮🇪',
			),
			array(
				'iso'          => 'IM',
				'calling_code' => '44',
				'flag'         => '🇮🇲',
			),
			array(
				'iso'          => 'IL',
				'calling_code' => '972',
				'flag'         => '🇮🇱',
			),
			array(
				'iso'          => 'IT',
				'calling_code' => '39',
				'flag'         => '🇮🇹',
			),
			array(
				'iso'          => 'JM',
				'calling_code' => '1',
				'flag'         => '🇯🇲',
			),
			array(
				'iso'          => 'JP',
				'calling_code' => '81',
				'flag'         => '🇯🇵',
			),
			array(
				'iso'          => 'JE',
				'calling_code' => '44',
				'flag'         => '🇯🇪',
			),
			array(
				'iso'          => 'JO',
				'calling_code' => '962',
				'flag'         => '🇯🇴',
			),
			array(
				'iso'          => 'KZ',
				'calling_code' => '7',
				'flag'         => '🇰🇿',
			),
			array(
				'iso'          => 'KE',
				'calling_code' => '254',
				'flag'         => '🇰🇪',
			),
			array(
				'iso'          => 'KI',
				'calling_code' => '686',
				'flag'         => '🇰🇮',
			),
			array(
				'iso'          => 'XK',
				'calling_code' => '383',
				'flag'         => '🇽🇰',
			),
			array(
				'iso'          => 'KW',
				'calling_code' => '965',
				'flag'         => '🇰🇼',
			),
			array(
				'iso'          => 'KG',
				'calling_code' => '996',
				'flag'         => '🇰🇬',
			),
			array(
				'iso'          => 'LA',
				'calling_code' => '856',
				'flag'         => '🇱🇦',
			),
			array(
				'iso'          => 'LV',
				'calling_code' => '371',
				'flag'         => '🇱🇻',
			),
			array(
				'iso'          => 'LB',
				'calling_code' => '961',
				'flag'         => '🇱🇧',
			),
			array(
				'iso'          => 'LS',
				'calling_code' => '266',
				'flag'         => '🇱🇸',
			),
			array(
				'iso'          => 'LR',
				'calling_code' => '231',
				'flag'         => '🇱🇷',
			),
			array(
				'iso'          => 'LY',
				'calling_code' => '218',
				'flag'         => '🇱🇾',
			),
			array(
				'iso'          => 'LI',
				'calling_code' => '423',
				'flag'         => '🇱🇮',
			),
			array(
				'iso'          => 'LT',
				'calling_code' => '370',
				'flag'         => '🇱🇹',
			),
			array(
				'iso'          => 'LU',
				'calling_code' => '352',
				'flag'         => '🇱🇺',
			),
			array(
				'iso'          => 'MO',
				'calling_code' => '853',
				'flag'         => '🇲🇴',
			),
			array(
				'iso'          => 'MG',
				'calling_code' => '261',
				'flag'         => '🇲🇬',
			),
			array(
				'iso'          => 'MW',
				'calling_code' => '265',
				'flag'         => '🇲🇼',
			),
			array(
				'iso'          => 'MY',
				'calling_code' => '60',
				'flag'         => '🇲🇾',
			),
			array(
				'iso'          => 'MV',
				'calling_code' => '960',
				'flag'         => '🇲🇻',
			),
			array(
				'iso'          => 'ML',
				'calling_code' => '223',
				'flag'         => '🇲🇱',
			),
			array(
				'iso'          => 'MT',
				'calling_code' => '356',
				'flag'         => '🇲🇹',
			),
			array(
				'iso'          => 'MH',
				'calling_code' => '692',
				'flag'         => '🇲🇭',
			),
			array(
				'iso'          => 'MQ',
				'calling_code' => '596',
				'flag'         => '🇲🇶',
			),
			array(
				'iso'          => 'MR',
				'calling_code' => '222',
				'flag'         => '🇲🇷',
			),
			array(
				'iso'          => 'MU',
				'calling_code' => '230',
				'flag'         => '🇲🇺',
			),
			array(
				'iso'          => 'YT',
				'calling_code' => '262',
				'flag'         => '🇾🇹',
			),
			array(
				'iso'          => 'MX',
				'calling_code' => '52',
				'flag'         => '🇲🇽',
			),
			array(
				'iso'          => 'FM',
				'calling_code' => '691',
				'flag'         => '🇫🇲',
			),
			array(
				'iso'          => 'MD',
				'calling_code' => '373',
				'flag'         => '🇲🇩',
			),
			array(
				'iso'          => 'MC',
				'calling_code' => '377',
				'flag'         => '🇲🇨',
			),
			array(
				'iso'          => 'MN',
				'calling_code' => '976',
				'flag'         => '🇲🇳',
			),
			array(
				'iso'          => 'ME',
				'calling_code' => '382',
				'flag'         => '🇲🇪',
			),
			array(
				'iso'          => 'MS',
				'calling_code' => '1',
				'flag'         => '🇲🇸',
			),
			array(
				'iso'          => 'MA',
				'calling_code' => '212',
				'flag'         => '🇲🇦',
			),
			array(
				'iso'          => 'MZ',
				'calling_code' => '258',
				'flag'         => '🇲🇿',
			),
			array(
				'iso'          => 'MM',
				'calling_code' => '95',
				'flag'         => '🇲🇲',
			),
			array(
				'iso'          => 'NA',
				'calling_code' => '264',
				'flag'         => '🇳🇦',
			),
			array(
				'iso'          => 'NR',
				'calling_code' => '674',
				'flag'         => '🇳🇷',
			),
			array(
				'iso'          => 'NP',
				'calling_code' => '977',
				'flag'         => '🇳🇵',
			),
			array(
				'iso'          => 'NL',
				'calling_code' => '31',
				'flag'         => '🇳🇱',
			),
			array(
				'iso'          => 'NC',
				'calling_code' => '687',
				'flag'         => '🇳🇨',
			),
			array(
				'iso'          => 'NZ',
				'calling_code' => '64',
				'flag'         => '🇳🇿',
			),
			array(
				'iso'          => 'NI',
				'calling_code' => '505',
				'flag'         => '🇳🇮',
			),
			array(
				'iso'          => 'NE',
				'calling_code' => '227',
				'flag'         => '🇳🇪',
			),
			array(
				'iso'          => 'NG',
				'calling_code' => '234',
				'flag'         => '🇳🇬',
			),
			array(
				'iso'          => 'NU',
				'calling_code' => '683',
				'flag'         => '🇳🇺',
			),
			array(
				'iso'          => 'NF',
				'calling_code' => '672',
				'flag'         => '🇳🇫',
			),
			array(
				'iso'          => 'KP',
				'calling_code' => '850',
				'flag'         => '🇰🇵',
			),
			array(
				'iso'          => 'MK',
				'calling_code' => '389',
				'flag'         => '🇲🇰',
			),
			array(
				'iso'          => 'MP',
				'calling_code' => '1',
				'flag'         => '🇲🇵',
			),
			array(
				'iso'          => 'NO',
				'calling_code' => '47',
				'flag'         => '🇳🇴',
			),
			array(
				'iso'          => 'OM',
				'calling_code' => '968',
				'flag'         => '🇴🇲',
			),
			array(
				'iso'          => 'PK',
				'calling_code' => '92',
				'flag'         => '🇵🇰',
			),
			array(
				'iso'          => 'PW',
				'calling_code' => '680',
				'flag'         => '🇵🇼',
			),
			array(
				'iso'          => 'PS',
				'calling_code' => '970',
				'flag'         => '🇵🇸',
			),
			array(
				'iso'          => 'PA',
				'calling_code' => '507',
				'flag'         => '🇵🇦',
			),
			array(
				'iso'          => 'PG',
				'calling_code' => '675',
				'flag'         => '🇵🇬',
			),
			array(
				'iso'          => 'PY',
				'calling_code' => '595',
				'flag'         => '🇵🇾',
			),
			array(
				'iso'          => 'PE',
				'calling_code' => '51',
				'flag'         => '🇵🇪',
			),
			array(
				'iso'          => 'PH',
				'calling_code' => '63',
				'flag'         => '🇵🇭',
			),
			array(
				'iso'          => 'PL',
				'calling_code' => '48',
				'flag'         => '🇵🇱',
			),
			array(
				'iso'          => 'PT',
				'calling_code' => '351',
				'flag'         => '🇵🇹',
			),
			array(
				'iso'          => 'PR',
				'calling_code' => '1',
				'flag'         => '🇵🇷',
			),
			array(
				'iso'          => 'QA',
				'calling_code' => '974',
				'flag'         => '🇶🇦',
			),
			array(
				'iso'          => 'RE',
				'calling_code' => '262',
				'flag'         => '🇷🇪',
			),
			array(
				'iso'          => 'RO',
				'calling_code' => '40',
				'flag'         => '🇷🇴',
			),
			array(
				'iso'          => 'RU',
				'calling_code' => '7',
				'flag'         => '🇷🇺',
			),
			array(
				'iso'          => 'RW',
				'calling_code' => '250',
				'flag'         => '🇷🇼',
			),
			array(
				'iso'          => 'WS',
				'calling_code' => '685',
				'flag'         => '🇼🇸',
			),
			array(
				'iso'          => 'SM',
				'calling_code' => '378',
				'flag'         => '🇸🇲',
			),
			array(
				'iso'          => 'ST',
				'calling_code' => '239',
				'flag'         => '🇸🇹',
			),
			array(
				'iso'          => 'SA',
				'calling_code' => '966',
				'flag'         => '🇸🇦',
			),
			array(
				'iso'          => 'SN',
				'calling_code' => '221',
				'flag'         => '🇸🇳',
			),
			array(
				'iso'          => 'RS',
				'calling_code' => '381',
				'flag'         => '🇷🇸',
			),
			array(
				'iso'          => 'SC',
				'calling_code' => '248',
				'flag'         => '🇸🇨',
			),
			array(
				'iso'          => 'SL',
				'calling_code' => '232',
				'flag'         => '🇸🇱',
			),
			array(
				'iso'          => 'SG',
				'calling_code' => '65',
				'flag'         => '🇸🇬',
			),
			array(
				'iso'          => 'SX',
				'calling_code' => '1',
				'flag'         => '🇸🇽',
			),
			array(
				'iso'          => 'SK',
				'calling_code' => '421',
				'flag'         => '🇸🇰',
			),
			array(
				'iso'          => 'SI',
				'calling_code' => '386',
				'flag'         => '🇸🇮',
			),
			array(
				'iso'          => 'SB',
				'calling_code' => '677',
				'flag'         => '🇸🇧',
			),
			array(
				'iso'          => 'SO',
				'calling_code' => '252',
				'flag'         => '🇸🇴',
			),
			array(
				'iso'          => 'ZA',
				'calling_code' => '27',
				'flag'         => '🇿🇦',
			),
			array(
				'iso'          => 'KR',
				'calling_code' => '82',
				'flag'         => '🇰🇷',
			),
			array(
				'iso'          => 'SS',
				'calling_code' => '211',
				'flag'         => '🇸🇸',
			),
			array(
				'iso'          => 'ES',
				'calling_code' => '34',
				'flag'         => '🇪🇸',
			),
			array(
				'iso'          => 'LK',
				'calling_code' => '94',
				'flag'         => '🇱🇰',
			),
			array(
				'iso'          => 'BL',
				'calling_code' => '590',
				'flag'         => '🇧🇱',
			),
			array(
				'iso'          => 'SH',
				'calling_code' => '290',
				'flag'         => '🇸🇭',
			),
			array(
				'iso'          => 'KN',
				'calling_code' => '1',
				'flag'         => '🇰🇳',
			),
			array(
				'iso'          => 'LC',
				'calling_code' => '1',
				'flag'         => '🇱🇨',
			),
			array(
				'iso'          => 'MF',
				'calling_code' => '590',
				'flag'         => '🇲🇫',
			),
			array(
				'iso'          => 'PM',
				'calling_code' => '508',
				'flag'         => '🇵🇲',
			),
			array(
				'iso'          => 'VC',
				'calling_code' => '1',
				'flag'         => '🇻🇨',
			),
			array(
				'iso'          => 'SD',
				'calling_code' => '249',
				'flag'         => '🇸🇩',
			),
			array(
				'iso'          => 'SR',
				'calling_code' => '597',
				'flag'         => '🇸🇷',
			),
			array(
				'iso'          => 'SJ',
				'calling_code' => '47',
				'flag'         => '🇸🇯',
			),
			array(
				'iso'          => 'SE',
				'calling_code' => '46',
				'flag'         => '🇸🇪',
			),
			array(
				'iso'          => 'CH',
				'calling_code' => '41',
				'flag'         => '🇨🇭',
			),
			array(
				'iso'          => 'SY',
				'calling_code' => '963',
				'flag'         => '🇸🇾',
			),
			array(
				'iso'          => 'TW',
				'calling_code' => '886',
				'flag'         => '🇹🇼',
			),
			array(
				'iso'          => 'TJ',
				'calling_code' => '992',
				'flag'         => '🇹🇯',
			),
			array(
				'iso'          => 'TZ',
				'calling_code' => '255',
				'flag'         => '🇹🇿',
			),
			array(
				'iso'          => 'TH',
				'calling_code' => '66',
				'flag'         => '🇹🇭',
			),
			array(
				'iso'          => 'TL',
				'calling_code' => '670',
				'flag'         => '🇹🇱',
			),
			array(
				'iso'          => 'TG',
				'calling_code' => '228',
				'flag'         => '🇹🇬',
			),
			array(
				'iso'          => 'TK',
				'calling_code' => '690',
				'flag'         => '🇹🇰',
			),
			array(
				'iso'          => 'TO',
				'calling_code' => '676',
				'flag'         => '🇹🇴',
			),
			array(
				'iso'          => 'TT',
				'calling_code' => '1',
				'flag'         => '🇹🇹',
			),
			array(
				'iso'          => 'TN',
				'calling_code' => '216',
				'flag'         => '🇹🇳',
			),
			array(
				'iso'          => 'TR',
				'calling_code' => '90',
				'flag'         => '🇹🇷',
			),
			array(
				'iso'          => 'TM',
				'calling_code' => '993',
				'flag'         => '🇹🇲',
			),
			array(
				'iso'          => 'TC',
				'calling_code' => '1',
				'flag'         => '🇹🇨',
			),
			array(
				'iso'          => 'TV',
				'calling_code' => '688',
				'flag'         => '🇹🇻',
			),
			array(
				'iso'          => 'UG',
				'calling_code' => '256',
				'flag'         => '🇺🇬',
			),
			array(
				'iso'          => 'UA',
				'calling_code' => '380',
				'flag'         => '🇺🇦',
			),
			array(
				'iso'          => 'AE',
				'calling_code' => '971',
				'flag'         => '🇦🇪',
			),
			array(
				'iso'          => 'GB',
				'calling_code' => '44',
				'flag'         => '🇬🇧',
			),
			array(
				'iso'          => 'US',
				'calling_code' => '1',
				'flag'         => '🇺🇸',
			),
			array(
				'iso'          => 'UY',
				'calling_code' => '598',
				'flag'         => '🇺🇾',
			),
			array(
				'iso'          => 'VI',
				'calling_code' => '1',
				'flag'         => '🇻🇮',
			),
			array(
				'iso'          => 'UZ',
				'calling_code' => '998',
				'flag'         => '🇺🇿',
			),
			array(
				'iso'          => 'VU',
				'calling_code' => '678',
				'flag'         => '🇻🇺',
			),
			array(
				'iso'          => 'VA',
				'calling_code' => '39',
				'flag'         => '🇻🇦',
			),
			array(
				'iso'          => 'VE',
				'calling_code' => '58',
				'flag'         => '🇻🇪',
			),
			array(
				'iso'          => 'VN',
				'calling_code' => '84',
				'flag'         => '🇻🇳',
			),
			array(
				'iso'          => 'WF',
				'calling_code' => '681',
				'flag'         => '🇼🇫',
			),
			array(
				'iso'          => 'EH',
				'calling_code' => '212',
				'flag'         => '🇪🇭',
			),
			array(
				'iso'          => 'YE',
				'calling_code' => '967',
				'flag'         => '🇾🇪',
			),
			array(
				'iso'          => 'ZM',
				'calling_code' => '260',
				'flag'         => '🇿🇲',
			),
			array(
				'iso'          => 'ZW',
				'calling_code' => '263',
				'flag'         => '🇿🇼',
			),
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
		switch ( $type ) {
			case 'country':
				$data = self::countries_list();
				break;
			case 'state':
				$data = self::states_list();
				break;
			case 'province':
				$data = self::provinces_list();
				break;
			case 'phone':
				$data = self::phone_list();
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

	/**
	 * Provides an array of phone format information.
	 *
	 * @param $as_json - boolean - Whether to retrieve this data as a JSON string.
	 * @param $process_callback - callable - An optional callback for transforming the data before returning.
	 *
	 * @return string|array
	 */
	public static function phone_info( $as_json = false, $process_callback = null ) {
		return self::get_data_by_type( 'phone', $as_json, $process_callback );
	}
}
