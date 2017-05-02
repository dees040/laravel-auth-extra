<?php

namespace dees040\AuthExtra;

use Illuminate\Http\Request;

class Locator
{
    /**
     * The Request instance.
     *
     * @var \Illuminate\Http\Request
     */
    private $request;

    /**
     * Determine if the user already has been located.
     *
     * @var bool
     */
    private $located = false;

    /**
     * The user location info.
     *
     * @var array
     */
    private $info = [
        'city' => '',
        'state' => '',
        'country' => '',
        'country_code' => '',
        'continent' => '',
        'continent_code' => '',
    ];

    /**
     * Locator constructor.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Get the IP address of the visitor.
     *
     * @return string
     */
    public function getIp()
    {
        return '178.238.99.230'; // $this->request->ip();
    }

    /**
     * Get the country of visitor.
     *
     * @return string
     */
    public function getCountry()
    {
        if (! $this->located) {
            $this->getVisitorInfo();
        }

        return $this->info['country'];
    }

    /**
     * Get the city of visitor.
     *
     * @return string
     */
    public function getCity()
    {
        if (! $this->located) {
            $this->getVisitorInfo();
        }

        return $this->info['city'];
    }

    /**
     * Gather information from the IP address of a visitor.
     */
    public function getVisitorInfo()
    {
        $ip = $this->getIp();

        $data = json_decode(file_get_contents('http://www.geoplugin.net/json.gp?ip='.$ip));

        if (strlen(trim($data->geoplugin_countryCode)) == 2) {
            $this->info = [
                'city' => $data->geoplugin_city,
                'state' => $data->geoplugin_regionName,
                'country' => $data->geoplugin_countryName,
                'country_code' => $data->geoplugin_countryCode,
                'continent' => $this->convertContinent($data->geoplugin_continentCode),
                'continent_code' => $data->geoplugin_continentCode,
            ];

            $this->located = true;
        }

        return $this->info;
    }

    /**
     * Convert a continent code to a full continent name.
     *
     * @param  string  $code
     * @return string
     */
    protected function convertContinent($code)
    {
        $continents = [
            'AF' => 'Africa',
            'AN' => 'Antarctica',
            'AS' => 'Asia',
            'EU' => 'Europe',
            'OC' => 'Australia (Oceania)',
            'NA' => 'North America',
            'SA' => 'South America',
        ];

        return $continents[strtoupper($code)];
    }
}
