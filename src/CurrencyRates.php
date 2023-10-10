<?php

namespace AmrShawky;

use AmrShawky\Traits\ParamsOverload;
use GuzzleHttp\Client;

/**
 * @method self source(string $source)
 * @method self base(string $base)
 */

class CurrencyRates extends API
{
    use ParamsOverload;

    /**
     * @var null
     */
    private $currencies = null;

    /**
     * @var null
     */
    private $places = null;

    /**
     * @var float
     */
    private $amount = 1.00;

    /**
     * @var array
     */
    protected $available_params = [
        'base',
        'source'
    ];

    /**
     * CurrencyRates constructor.
     *
     * @param Client|null $client
     */
    public function __construct(?Client $client = null)
    {
        parent::__construct($client);

        $this->setQueryParams(function () {
            $params = ['amount' => $this->amount];

            if ($this->places) {
                $params['places'] = $this->places;
            }

            if ($this->currencies) {
                $params['currencies'] = implode(',', $this->currencies);
            }

            return $params;
        });
    }

    /**
     * @param float $amount
     *
     * @return $this
     */
    public function amount(float $amount)
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * @param array $symbols
     *
     * @return $this
     */
    public function currencies(array $currencies)
    {
        $this->currencies = $currencies;
        return $this;
    }

    /**
     * @param $places
     *
     * @return $this
     */
    public function round(int $places)
    {
        $this->places = $places;
        return $this;
    }

    /**
     * @param object $response
     *
     * @return mixed|null
     */
    protected function getResults(object $response)
    {
        if (!empty($rates = (array) $response->quotes)) {

            unset($response->quotes);

            $quotes = [];
            foreach ($rates as $key => $value) {
                if (strpos($key, $response->source) !== false) {
                    $quotes[substr($key, strlen($response->source))] = $value;
                } else {
                    $quotes[$key] = $value;
                }
            }
            $quotes[$response->source] = $this->amount;

            return $quotes;
        }

        return null;
    }
}