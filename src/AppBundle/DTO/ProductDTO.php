<?php
namespace AppBundle\DTO;
use AppBundle\Validator\MaxPriceValidator;
use AppBundle\Validator\MinPriceAndStockValidator;
use Carbon\Carbon;
/**
 * @ORM\Entity
 * @ORM\Table(name="products")
 */
class ProductDTO
{
    public $item;
    public $id;
    public $product_code;
    public $name;
    public $description;
    public $price;
    public $qty;
    public $created_at;
    public $updated_at;
    public $discontinued_at;

    public function __construct(array $item)
    {
        $this->item = $item;
        $this->name = $item['Product Name'];
        $this->product_code = $item['Product Code'];
        $this->description = $item['Product Description'];
        $this->price = array_key_exists('Cost in GBP', $item) ? $item['Cost in GBP'] : NULL;
        $this->qty = array_key_exists('Stock', $item) ? $item['Stock'] : NULL;
        $this->created_at = Carbon::now();
        $this->updated_at = Carbon::now();
        $this->discontinued_at = array_key_exists('Discontinued', $item);
    }
}