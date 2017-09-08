<?php

namespace Tests\AppBundle\Validator;

use AppBundle\DTO\ProductDTO;
use AppBundle\Validator\Validator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Encoder\CsvEncoder;


Class ValidatorTest extends TestCase
{

    public $items;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $serializer = new Serializer([new ObjectNormalizer()], [new CsvEncoder()]);

        // decoding CSV contents
        $this->items = $serializer->decode(file_get_contents('csv/stock.csv'), 'csv');
    }
    /**
     * @dataProvider validateMinPriceAndStockProvider
     */
    public function testValidateMinPriceAndStock($data, $result)
    {
        $product = new ProductDTO($this->items[$data]);
        $validator = new Validator($product);
        $this->assertEquals($result, $validator->validateMinPriceAndStock());
    }

    /**
     * @dataProvider validateMaxPriceProvider
     */
    public function testValidateMaxPrice ($data, $result){
        $product = new ProductDTO($this->items[$data]);
        $validator = new Validator($product);

        $this->assertEquals($result, $validator->validateMaxPrice());
    }

    /**
     * @dataProvider getValidateProvider
     */
    public function testGetValidate ($data, $result){
        $product = new ProductDTO($this->items[$data]);
        $validator = new Validator($product);

        $this->assertEquals($result, $validator->getValidate());
    }

    public function validateMinPriceAndStockProvider()
    {
        return array(
            array(0, true),
            array(1, true),
            array(2, true),
            array(11, true),
            array(17, false),
        );
    }

    public function validateMaxPriceProvider()
    {
        return array(
            array(0, true),
            array(1, true),
            array(2, true),
            array(11, true),
            array(17, true),
            array(27, false),
            array(28, false),
        );
    }

    public function getValidateProvider()
    {
        return array(
            array(0, true),
            array(1, true),
            array(2, true),
            array(10, false),
            array(17, false),
            array(27, false),
            array(28, false),
        );
    }
}