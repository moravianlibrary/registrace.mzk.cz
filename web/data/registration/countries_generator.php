<?php
// https://datahub.io/core/country-list#data
$source = 'https://pkgstore.datahub.io/core/country-list/data_json/data/8c458f2d15d9f2119654b29ede6e45b8/data_json.json';
$countries = json_decode(file_get_contents($source));
$output = fopen("countries.ini", "w");
foreach($countries as $country) {
    // space at the beginning of the key is necessary, otherwise PHP parse_ini_file fails for NO = Norway
    $line = ' ' . $country->Code . ' = "' . str_replace('"', ' ', $country->Name) . "\"\n";
    fwrite($output, $line);
}
fclose($output);
?>
