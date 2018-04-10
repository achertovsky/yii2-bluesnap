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
        return array_search($countryName, self::COUNTRY_LIST);
    }
        
    /**
     * https://developers.bluesnap.com/docs/country-codes
     */
    const COUNTRY_LIST = [
        "ad" => "Andorra",
        "ae" => "United Arab Emirates",
        "ag" => "Antigua and Barbuda",
        "ai" => "Anguilla",
        "al" => "Albania",
        "am" => "Armenia",
        "an" => "Netherlands Antilles",
        "ao" => "Angola",
        "aq" => "Antarctica",
        "ar" => "Argentina",
        "as" => "American Samoa",
        "at" => "Austria",
        "au" => "Australia",
        "aw" => "Aruba",
        "az" => "Azerbaijan",
        "ba" => "Bosnia-Herzegovina",
        "bb" => "Barbados",
        "bd" => "Bangladesh",
        "be" => "Belgium",
        "bf" => "Burkina Faso",
        "bg" => "Bulgaria",
        "bh" => "Bahrain",
        "bi" => "Burundi",
        "bj" => "Benin",
        "bm" => "Bermuda",
        "bn" => "Brunei Darussalam",
        "bo" => "Bolivia",
        "br" => "Brazil",
        "bs" => "Bahamas",
        "bt" => "Bhutan",
        "bv" => "Bouvet Island",
        "bw" => "Botswana",
        "by" => "Belarus",
        "bz" => "Belize",
        "ca" => "Canada",
        "cc" => "Cocos (Keeling) Islands",
        "cd" => "Congo (Brazzaville)",
        "cf" => "Central African Republic",
        "cg" => "Congo",
        "ch" => "Switzerland",
        "ci" => "Ivory Coast (Cote D'Ivoire)",
        "ck" => "Cook Islands",
        "cl" => "Chile",
        "cm" => "Cameroon",
        "cn" => "China",
        "co" => "Colombia",
        "cr" => "Costa Rica",
        "cv" => "Cape Verde",
        "cx" => "Christmas Island",
        "cy" => "Cyprus",
        "cz" => "Czech Republic",
        "de" => "Germany",
        "dj" => "Djibouti",
        "dk" => "Denmark",
        "dm" => "Dominica",
        "do" => "Dominican Republic",
        "dz" => "Algeria",
        "ec" => "Ecuador",
        "ee" => "Estonia",
        "eg" => "Egypt",
        "eh" => "Western Sahara",
        "er" => "Eritrea",
        "es" => "Spain",
        "et" => "Ethiopia",
        "fi" => "Finland",
        "fj" => "Fiji",
        "fk" => "Falkland Islands",
        "fm" => "Micronesia",
        "fo" => "Faroe Islands",
        "fr" => "France",
        "fx" => "France (European Territory)",
        "ga" => "Gabon",
        "gb" => "Great Britain",
        "gd" => "Grenada",
        "ge" => "Georgia",
        "gf" => "French Guiana",
        "gg" => "Guernsey",
        "gi" => "Gibraltar",
        "gl" => "Greenland",
        "gm" => "Gambia",
        "gn" => "Guinea",
        "gp" => "Guadeloupe (French)",
        "gq" => "Equatorial Guinea",
        "gr" => "Greece",
        "gs" => "S. Georgia & S. Sandwich Isls.",
        "gt" => "Guatemala",
        "gu" => "Guam (USA)",
        "gw" => "Guinea Bissau",
        "gy" => "Guyana",
        "hk" => "Hong Kong",
        "hm" => "Heard and McDonald Islands",
        "hn" => "Honduras",
        "hr" => "Croatia",
        "ht" => "Haiti",
        "hu" => "Hungary",
        "id" => "Indonesia",
        "ie" => "Ireland",
        "il" => "Israel",
        "im" => "Isle of Man",
        "in" => "India",
        "io" => "British Indian Ocean Territory",
        "is" => "Iceland",
        "it" => "Italy",
        "je" => "Jersey",
        "jm" => "Jamaica",
        "jo" => "Jordan",
        "jp" => "Japan",
        "ke" => "Kenya",
        "kg" => "Kyrgyz Republic (Kyrgyzstan)",
        "kh" => "Cambodia",
        "ki" => "Kiribati",
        "km" => "Comoros",
        "kn" => "Saint Kitts & Nevis Anguilla",
        "kr" => "South Korea",
        "kw" => "Kuwait",
        "ky" => "Cayman Islands",
        "kz" => "Kazakhstan",
        "la" => "Laos",
        "lc" => "Saint Lucia",
        "li" => "Liechtenstein",
        "lk" => "Sri Lanka",
        "lr" => "Liberia",
        "ls" => "Lesotho",
        "lt" => "Lithuania",
        "lu" => "Luxembourg",
        "lv" => "Latvia",
        "ma" => "Morocco",
        "mc" => "Monaco",
        "md" => "Moldova",
        "me" => "Montenegro",
        "mf" => "Saint Martin",
        "mg" => "Madagascar",
        "mh" => "Marshall Islands",
        "mk" => "Macedonia",
        "ml" => "Mali",
        "mn" => "Mongolia",
        "mo" => "Macao",
        "mp" => "Northern Mariana Islands",
        "mq" => "Martinique (French)",
        "mr" => "Mauritania",
        "ms" => "Montserrat",
        "mt" => "Malta",
        "mu" => "Mauritius",
        "mv" => "Maldives",
        "mw" => "Malawi",
        "mx" => "Mexico",
        "my" => "Malaysia",
        "mz" => "Mozambique",
        "na" => "Namibia",
        "nc" => "New Caledonia (French)",
        "ne" => "Niger",
        "nf" => "Norfolk Island",
        "ng" => "Nigeria",
        "ni" => "Nicaragua",
        "nl" => "Netherlands",
        "no" => "Norway",
        "np" => "Nepal",
        "nr" => "Nauru",
        "nt" => "Neutral Zone",
        "nu" => "Niue",
        "nz" => "New Zealand",
        "om" => "Oman",
        "pa" => "Panama",
        "pe" => "Peru",
        "pf" => "Polynesia (French)",
        "pg" => "Papua New Guinea",
        "ph" => "Philippines",
        "pk" => "Pakistan",
        "pl" => "Poland",
        "pm" => "Saint Pierre and Miquelon",
        "pn" => "Pitcairn Island",
        "pr" => "Puerto Rico",
        "pt" => "Portugal",
        "pw" => "Palau",
        "py" => "Paraguay",
        "qa" => "Qatar",
        "re" => "Reunion (French)",
        "ro" => "Romania",
        "rs" => "Serbia",
        "ru" => "Russian Federation",
        "rw" => "Rwanda",
        "sa" => "Saudi Arabia",
        "sb" => "Solomon Islands",
        "sc" => "Seychelles",
        "se" => "Sweden",
        "sg" => "Singapore",
        "sh" => "Saint Helena",
        "si" => "Slovenia",
        "sj" => "Svalbard and Jan Mayen Islands",
        "sk" => "Slovak Republic",
        "sl" => "Sierra Leone",
        "sm" => "San Marino",
        "sn" => "Senegal",
        "so" => "Somalia",
        "sr" => "Suriname",
        "st" => "Saint Tome (Sao Tome) and Principe",
        "su" => "Former USSR",
        "sv" => "El Salvador",
        "sx" => "Sint Maarten",
        "sz" => "Swaziland",
        "tc" => "Turks and Caicos Islands",
        "td" => "Chad",
        "tf" => "French Southern Territories",
        "tg" => "Togo",
        "th" => "Thailand",
        "tj" => "Tadjikistan",
        "tk" => "Tokelau",
        "tm" => "Turkmenistan",
        "tn" => "Tunisia",
        "to" => "Tonga",
        "tp" => "East Timor",
        "tr" => "Turkey",
        "tt" => "Trinidad and Tobago",
        "tv" => "Tuvalu",
        "tw" => "Taiwan",
        "tz" => "Tanzania",
        "ua" => "Ukraine",
        "ug" => "Uganda",
        "uk" => "United Kingdom",
        "um" => "USA Minor Outlying Islands",
        "us" => "United States",
        "uy" => "Uruguay",
        "uz" => "Uzbekistan",
        "va" => "Holy See (Vatican City State)",
        "vc" => "Saint Vincent & Grenadines",
        "ve" => "Venezuela",
        "vg" => "Virgin Islands (British)",
        "vi" => "Virgin Islands (USA)",
        "vn" => "Vietnam",
        "vu" => "Vanuatu",
        "wf" => "Wallis and Futuna Islands",
        "ws" => "Samoa",
        "yt" => "Mayotte",
        "za" => "South Africa",
        "zm" => "Zambia",
        "zw" => "Zimbabwe",
    ];
}
