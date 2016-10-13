<?php
namespace Affilicious\Product\Domain\Model;

use Affilicious\Common\Domain\Exception\InvalidPostTypeException;
use Affilicious\Common\Domain\Model\RepositoryInterface;
use Affilicious\Product\Domain\Exception\FailedToDeleteProductException;
use Affilicious\Product\Domain\Exception\ProductNotFoundException;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

interface ProductRepositoryInterface extends RepositoryInterface
{
    /**
     * Store the product
     *
     * @since 0.6
     * @param Product $product
     * @return Product
     */
    public function store(Product $product);

    /**
     * Store all products
     *
     * @param $products
     * @return Product[]
     */
    public function storeAll($products);

    /**
     * Delete the product
     *
     * @since 0.6
     * @param ProductId $productId
     * @return Product
     * @throws ProductNotFoundException
     * @throws InvalidPostTypeException
     * @throws FailedToDeleteProductException
     */
    public function delete(ProductId $productId);

    /**
     * Delete all products
     *
     * @param Product[] $products
     * @return Product[]
     * @throws ProductNotFoundException
     * @throws InvalidPostTypeException
     * @throws FailedToDeleteProductException
     */
    public function deleteAll($products);

    /**
     * Delete all variants from the parent product except the given ones.
     * This method will be replaced with the specification pattern in future versions
     *
     * @deprecated
     * @param Product[] $productVariants
     * @param ProductId $parentProductId
     * @throw InvalidTypeException
     */
    public function deleteAllVariantsFromParentExcept($productVariants, ProductId $parentProductId);

    /**
     * Find a product by the given ID
     *
     * @since 0.3
     * @param ProductId $productId
     * @return null|Product
     */
    public function findById(ProductId $productId);

    /**
     * Find all products
     *
     * @since 0.3
     * @return Product[]
     */
    public function findAll();
}
