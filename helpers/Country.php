<?php

namespace achertovsky\bluesnap\helpers;

/**
 * @author alexander
 */
class Country
{
    /**
     * @param string $countryName
     * @return mixed
     */
    public static function getCountryCode($countryName)
    {
        $list = self::COUNTRY_LIST;
        return isset($list[$countryName]) ? $list[$countryName] : false;
    }

    /**
     * Returns handy dropdown format
     *
     * @return array
     */
    public static function getDropdownFormat()
    {
        $list = array_flip(self::COUNTRY_LIST);
        foreach ($list as $code => $item) {
            $list[strtoupper($code)] = $item;
            unset($list[$code]);
        }
        return $list;
    }
        
    /**
     * https://developers.bluesnap.com/docs/country-codes
     * updating the list compare difference and put the difference by hand
     * list in here has some tweaks to get more matches
     */
    const COUNTRY_LIST = [
        "Albania" => "al",
        "Algeria" => "dz",
        "American Samoa" => "as",
        "Andorra" => "ad",
        "Angola" => "ao",
        "Anguilla" => "ai",
        "Antarctica" => "aq",
        "Antigua and Barbuda" => "ag",
        "Argentina" => "ar",
        "Armenia" => "am",
        "Aruba" => "aw",
        "Australia" => "au",
        "Austria" => "at",
        "Azerbaijan" => "az",
        "Bahamas" => "bs",
        "Bahrain" => "bh",
        "Bangladesh" => "bd",
        "Barbados" => "bb",
        "Belarus" => "by",
        "Belgium" => "be",
        "Belize" => "bz",
        "Benin" => "bj",
        "Bermuda" => "bm",
        "Bhutan" => "bt",
        "Bolivia" => "bo",
        "Bosnia and Herzegovina" => "ba",
        "Bosnia-Herzegovina" => "ba",
        "Botswana" => "bw",
        "Bouvet Island" => "bv",
        "Brazil" => "br",
        "British Indian Ocean Territory" => "io",
        "Brunei Darussalam" => "bn",
        "Bulgaria" => "bg",
        "Burkina Faso" => "bf",
        "Burundi" => "bi",
        "Cambodia" => "kh",
        "Cameroon" => "cm",
        "Canada" => "ca",
        "Cape Verde" => "cv",
        "Cayman Islands" => "ky",
        "Central African Republic" => "cf",
        "Chad" => "td",
        "Chile" => "cl",
        "China" => "cn",
        "Christmas Island" => "cx",
        "Cocos (Keeling) Islands" => "cc",
        "Colombia" => "co",
        "Comoros" => "km",
        "Congo" => "cg",
        "Congo (Brazzaville)" => "cd",
        "Cook Islands" => "ck",
        "Costa Rica" => "cr",
        "Croatia" => "hr",
        "Curacao" => "cw",
        "Curaçao" => "cw",
        "Cyprus" => "cy",
        "Czech Republic" => "cz",
        "Czechia" => "cz",
        "Denmark" => "dk",
        "Djibouti" => "dj",
        "Dominica" => "dm",
        "Dominican Republic" => "do",
        "East Timor" => "tp",
        "Ecuador" => "ec",
        "Egypt" => "eg",
        "El Salvador" => "sv",
        "Equatorial Guinea" => "gq",
        "Eritrea" => "er",
        "Estonia" => "ee",
        "Ethiopia" => "et",
        "Falkland Islands" => "fk",
        "Faroe Islands" => "fo",
        "Fiji" => "fj",
        "Finland" => "fi",
        "Former USSR" => "su",
        "France" => "fr",
        "France (European Territory)" => "fx",
        "French Guiana" => "gf",
        "French Southern Territories" => "tf",
        "Gabon" => "ga",
        "Gambia" => "gm",
        "Georgia" => "ge",
        "Germany" => "de",
        "Ghana" => "gh",
        "Gibraltar" => "gi",
        "Great Britain" => "gb",
        "Greece" => "gr",
        "Greenland" => "gl",
        "Grenada" => "gd",
        "Guadeloupe (French)" => "gp",
        "Guadeloupe" => "gp",
        "Guam (USA)" => "gu",
        "Guam" => "gu",
        "Guatemala" => "gt",
        "Guernsey" => "gg",
        "Guinea" => "gn",
        "Guinea Bissau" => "gw",
        "Guyana" => "gy",
        "Haiti" => "ht",
        "Heard and McDonald Islands" => "hm",
        "Holy See (Vatican City State)" => "va",
        "Honduras" => "hn",
        "Hong Kong" => "hk",
        "Hungary" => "hu",
        "Iceland" => "is",
        "India" => "in",
        "Indonesia" => "id",
        "Ireland" => "ie",
        "Isle" => "of",
        "Israel" => "il",
        "Italy" => "it",
        "Ivory Coast (Cote D'Ivoire)" => "ci",
        "Ivory Coast" => "ci",
        "Jamaica" => "jm",
        "Japan" => "jp",
        "Jersey" => "je",
        "Hashemite Kingdom of Jordan" => "jo",
        "Jordan" => "jo",
        "Kazakhstan" => "kz",
        "Kenya" => "ke",
        "Kiribati" => "ki",
        "Kuwait" => "kw",
        "Kyrgyzstan" => "kg",
        "Kyrgyz Republic" => "kg",
        "Laos" => "la",
        "Latvia" => "lv",
        "Lesotho" => "ls",
        "Liberia" => "lr",
        "Liechtenstein" => "li",
        "Republic of Lithuania" => "lt",
        "Lithuania" => "lt",
        "Luxembourg" => "lu",
        "Macao" => "mo",
        "Macedonia" => "mk",
        "North Macedonia" => "mk",
        "Madagascar" => "mg",
        "Malawi" => "mw",
        "Malaysia" => "my",
        "Maldives" => "mv",
        "Mali" => "ml",
        "Malta" => "mt",
        "Marshall Islands" => "mh",
        "Martinique (French)" => "mq",
        "Mauritania" => "mr",
        "Mauritius" => "mu",
        "Mayotte" => "yt",
        "Mexico" => "mx",
        "Micronesia" => "fm",
        "Republic of Moldova" => "md",
        "Moldova" => "md",
        "Monaco" => "mc",
        "Mongolia" => "mn",
        "Montenegro" => "me",
        "Montserrat" => "ms",
        "Morocco" => "ma",
        "Mozambique" => "mz",
        "Namibia" => "na",
        "Nauru" => "nr",
        "Nepal" => "np",
        "Netherlands" => "nl",
        "Netherlands Antilles" => "an",
        "Neutral Zone" => "nt",
        "New Caledonia (French)" => "nc",
        "New Zealand" => "nz",
        "Nicaragua" => "ni",
        "Niger" => "ne",
        "Nigeria" => "ng",
        "Niue" => "nu",
        "Norfolk Island" => "nf",
        "Northern Mariana Islands" => "mp",
        "Norway" => "no",
        "Oman" => "om",
        "Pakistan" => "pk",
        "Palau" => "pw",
        "Palestine" => "ps",
        "Panama" => "pa",
        "Papua" => "New",
        "Paraguay" => "py",
        "Peru" => "pe",
        "Philippines" => "ph",
        "Pitcairn Island" => "pn",
        "Poland" => "pl",
        "Polynesia (French)" => "pf",
        "Portugal" => "pt",
        "Puerto Rico" => "pr",
        "Qatar" => "qa",
        "Reunion (French)" => "re",
        "Réunion" => "re",
        "Romania" => "ro",
        "Russian Federation" => "ru",
        "Russia" => "ru",
        "Rwanda" => "rw",
        "S. Georgia & S. Sandwich Isls." => "gs",
        "Saint Helena" => "sh",
        "Saint Kitts & Nevis Anguilla" => "kn",
        "St Kitts and Nevis" => "kn",
        "Saint Lucia" => "lc",
        "Saint Martin" => "mf",
        "Saint Pierre and Miquelon" => "pm",
        "Saint Tome (Sao Tome) and Principe" => "st",
        "Saint Vincent & Grenadines" => "vc",
        "Samoa" => "ws",
        "San Marino" => "sm",
        "Saudi Arabia" => "sa",
        "Senegal" => "sn",
        "Serbia" => "rs",
        "Seychelles" => "sc",
        "Sierra Leone" => "sl",
        "Singapore" => "sg",
        "Sint Maarten" => "sx",
        "Slovak Republic" => "sk",
        "Slovakia" => "sk",
        "Slovenia" => "si",
        "Solomon Islands" => "sb",
        "Somalia" => "so",
        "South Africa" => "za",
        "South Korea" => "kr",
        "Spain" => "es",
        "Sri Lanka" => "lk",
        "Suriname" => "sr",
        "Svalbard and Jan Mayen Islands" => "sj",
        "Swaziland" => "sz",
        "Sweden" => "se",
        "Switzerland" => "ch",
        "Tadjikistan" => "tj",
        "Taiwan" => "tw",
        "Tanzania" => "tz",
        "Thailand" => "th",
        "Togo" => "tg",
        "Tokelau" => "tk",
        "Tonga" => "to",
        "Trinidad and Tobago" => "tt",
        "Tunisia" => "tn",
        "Turkey" => "tr",
        "Turkmenistan" => "tm",
        "Turks and Caicos Islands" => "tc",
        "Tuvalu" => "tv",
        "Uganda" => "ug",
        "Ukraine" => "ua",
        "United Arab Emirates" => "ae",
        "United Kingdom" => "uk",
        "United States" => "us",
        "Uruguay" => "uy",
        "USA Minor Outlying Islands" => "um",
        "Uzbekistan" => "uz",
        "Vanuatu" => "vu",
        "Venezuela" => "ve",
        "Vietnam" => "vn",
        "Virgin Islands (British)" => "vg",
        "Virgin Islands (USA)" => "vi",
        "U.S. Virgin Islands" => "vi",
        "Wallis and Futuna Islands" => "wf",
        "Western Sahara" => "eh",
        "Zambia" => "zm",
        "Zimbabwe" => "zw",
    ];
}
