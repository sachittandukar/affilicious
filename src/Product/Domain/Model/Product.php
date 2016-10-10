<?php
namespace Affilicious\Product\Domain\Model;

use Affilicious\Common\Domain\Exception\InvalidTypeException;
use Affilicious\Common\Domain\Model\AbstractEntity;
use Affilicious\Common\Domain\Model\Content;
use Affilicious\Common\Domain\Model\Image\Image;
use Affilicious\Common\Domain\Model\Name;
use Affilicious\Common\Domain\Model\Title;
use Affilicious\Product\Domain\Exception\DuplicatedDetailGroupException;
use Affilicious\Product\Domain\Exception\DuplicatedShopException;
use Affilicious\Product\Domain\Exception\DuplicatedVariantException;
use Affilicious\Product\Domain\Model\DetailGroup\DetailGroup;
use Affilicious\Product\Domain\Model\Review\Review;
use Affilicious\Product\Domain\Model\Shop\Shop;
use Affilicious\Product\Domain\Model\Variant\ProductVariant;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

class Product extends AbstractEntity
{
    const POST_TYPE = 'product';
    const SLUG = 'product';

    /**
     * The unique ID of the product
     *
     * Note that you just get the ID in Wordpress, if you store a post.
     *
     * @var ProductId
     */
    protected $id;

    /**
     * The type of the product like simple or variants
     *
     * @var Type
     */
    protected $type;

    /**
     * The title of the product for display usage
     *
     * @var Title
     */
    protected $title;

    /**
     * The unique name of the product for url usage
     *
     * @var Name
     */
    protected $name;

    /**
     * The content of the product
     *
     * @var Content
     */
    protected $content;

    /**
     * The thumbnail of the product
     *
     * @var Image
     */
    protected $thumbnail;

    /**
     * Holds the shops like Amazon, Affilinet or Ebay.
     *
     * @var Shop[]
     */
    protected $shops;

    /**
     * Holds all product variants of the product
     *
     * @var ProductVariant[]
     */
    protected $variants;

    /**
     * Holds the detail groups of the product
     *
     * @var DetailGroup[]
     */
    protected $detailGroups;

    /**
     * Stores the rating in 0.5 steps from 0 to 5 and the number of votes
     *
     * @var Review
     */
    protected $review;

    /**
     * Stores the IDs of the related products
     *
     * @var int[]
     */
    protected $relatedProducts;

    /**
     * Stores the IDs of the related accessories
     *
     * @var int[]
     */
    protected $relatedAccessories;

    /**
     * Stores the IDs of the image gallery attachments
     *
     * @var int[]
     */
    protected $imageGallery;

    /**
     * @since 0.6
     * @param Title $title
     * @param Name $name
     * @param Type $type
     */
    public function __construct(Title $title, Name $name, Type $type)
    {
        $this->title = $title;
        $this->name = $name;
        $this->type = Type::simple();
        $this->shops = array();
        $this->variants = array();
        $this->detailGroups = array();
        $this->relatedProducts = array();
        $this->relatedAccessories = array();
        $this->imageGallery = array();
    }

    /**
     * Check if the product has an ID
     *
     * @since 0.6
     * @return bool
     */
    public function hasId()
    {
        return $this->id !== null;
    }

    /**
     * Get the product ID
     *
     * @since 0.6
     * @return ProductId
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the product ID
     *
     * Note that you just get the ID in Wordpress, if you store a post.
     * Normally, you place the ID to the constructor, but it's not possible here
     *
     * @since 0.6
     * @param null|ProductId $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Get the type like simple or variants.
     *
     * @since 0.6
     * @return Type
     */
    public function getType()
    {
        $type = count($this->variants) == 0 ? Type::simple() : Type::variants();

        return $type;
    }

    /**
     * Set the type like simple or variants
     *
     * @since 0.6
     * @param Type $type
     */
    public function setType(Type $type)
    {
        $this->type = $type;
    }

    /**
     * Get the title
     *
     * @since 0.6
     * @return Title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set the title
     *
     * @since 0.6
     * @param Title $title
     */
    public function setTitle(Title $title)
    {
        $this->title = $title;
    }

    /**
     * Get the name for url usage
     *
     * @since 0.6
     * @return Name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the name for the url usage
     *
     * @since 0.6
     * @param Name $name
     */
    public function setName(Name $name)
    {
        $this->name = $name;
    }

    /**
     * Check if the product has any content
     *
     * @since 0.6
     * @return bool
     */
    public function hasContent()
    {
        return $this->content !== null;
    }

    /**
     * Get the content
     *
     * @since 0.6
     * @return Content
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set the content
     *
     * @since 0.6
     * @param Content $content
     */
    public function setContent(Content $content)
    {
        $this->content = $content;
    }

    /**
     * Check if the product has a thumbnail
     *
     * @since 0.6
     * @return bool
     */
    public function hasThumbnail()
    {
        return $this->thumbnail !== null;
    }

    /**
     * Get the thumbnail
     *
     * @since 0.6
     * @return Image
     */
    public function getThumbnail()
    {
        return $this->thumbnail;
    }

    /**
     * Set the thumbnail
     *
     * @since 0.6
     * @param Image $thumbnail
     */
    public function setThumbnail(Image $thumbnail)
    {
        $this->thumbnail = $thumbnail;
    }

    /**
     * Check if the product has a specific shop by the name
     *
     * @since 0.6
     * @param Name $name
     * @return bool
     */
    public function hasShop(Name $name)
    {
        return isset($this->shops[$name->getValue()]);
    }

    /**
     * Add a new shop
     *
     * @since 0.6
     * @param Shop $shop
     * @throws DuplicatedShopException
     */
    public function addShop(Shop $shop)
    {
        if($this->hasShop($shop->getName())) {
            throw new DuplicatedShopException($shop, $this);
        }

        $this->shops[$shop->getName()->getValue()] = $shop;
    }

    /**
     * Remove a shop by the name
     *
     * @since 0.6
     * @param Name $name
     */
    public function removeShop(Name $name)
    {
        unset($this->shops[$name->getValue()]);
    }

    /**
     * Get a shop by the name
     *
     * @since 0.6
     * @param Name $name
     * @return null|Shop
     */
    public function getShop(Name $name)
    {
        if(!$this->hasShop($name)) {
            return null;
        }

        $shop = $this->shops[$name->getValue()];

        return $shop;
    }

    /**
     * Get the cheapest shop
     *
     * @since 0.6
     * @return null|Shop
     */
    public function getCheapestShop()
    {
        /** @var Shop $cheapestShop */
        $cheapestShop = null;
        foreach ($this->shops as $shop) {
            if ($cheapestShop === null ||
                ($cheapestShop->hasPrice() && $cheapestShop->getPrice()->isGreaterThan($shop->hasPrice()))) {
                $cheapestShop = $shop;
            }
        }

        return $cheapestShop;
    }

    /**
     * Get all shops
     *
     * @since 0.6
     * @return Shop[]
     */
    public function getShops()
    {
        $shops = array_values($this->shops);

        return $shops;
    }

    /**
     * Set all shops
     * If you do this, the old shops going to be replaced.
     *
     * @since 0.6
     * @param Shop[] $shops
     * @throws InvalidTypeException
     */
    public function setShops($shops)
    {
        $this->shops = array();

        // addShop checks for the type
        foreach ($shops as $shop) {
            $this->addShop($shop);
        }
    }

    /**
     * Check if the product has a specific variant by the name
     *
     * @since 0.6
     * @param Name $name
     * @return bool
     */
    public function hasVariant(Name $name)
    {
        return isset($this->variants[$name->getValue()]);
    }

    /**
     * Add a new product variant
     *
     * @since 0.6
     * @param ProductVariant $variant
     */
    public function addVariant(ProductVariant $variant)
    {
        if($this->hasVariant($variant->getName())) {
            throw new DuplicatedVariantException($variant, $this);
        }

        $this->variants[$variant->getName()->getValue()] = $variant;
    }

    /**
     * Remove an existing product variant by the name
     *
     * @since 0.6
     * @param Name $name
     */
    public function removeVariant(Name $name)
    {
        unset($this->variants[$name->getValue()]);
    }

    /**
     * Get the product variant by the name
     *
     * @since 0.6
     * @param Name $name
     * @return null|ProductVariant
     */
    public function getVariant(Name $name)
    {
        if(!$this->hasVariant($name)) {
            return null;
        }

        $variant = $this->variants[$name->getValue()];

        return $variant;
    }

    /**
     * Get all product variants
     *
     * @since 0.6
     * @return ProductVariant[]
     */
    public function getVariants()
    {
        $variants = array_values($this->variants);

        return $variants;
    }

    /**
     * Set all product variants
     * If you do this, the old product variants going to be replaced.
     *
     * @since 0.6
     * @param ProductVariant[] $variants
     * @throws InvalidTypeException
     */
    public function setVariants($variants)
    {
        $this->variants = array();

        // addVariant checks for the type
        foreach ($variants as $variant) {
            $this->addVariant($variant);
        }
    }

    /**
     * Check if the product has a specific detail group
     *
     * @since 0.6
     * @param Name $name
     * @return bool
     */
    public function hasDetailGroup(Name $name)
    {
        return isset($this->detailGroups[$name->getValue()]);
    }

    /**
     * Add a new detail group
     *
     * @since 0.6
     * @param DetailGroup $detailGroup
     * @throws DuplicatedDetailGroupException
     */
    public function addDetailGroup(DetailGroup $detailGroup)
    {
        if($this->hasDetailGroup($detailGroup->getName())) {
            throw new DuplicatedDetailGroupException($detailGroup, $this);
        }

        $this->detailGroups[$detailGroup->getName()->getValue()] = $detailGroup;
    }

    /**
     * Remove a detail group by the name
     *
     * @since 0.6
     * @param Name $name
     */
    public function removeDetailGroup(Name $name)
    {
        unset($this->detailGroups[$name->getValue()]);
    }

    /**
     * Get a detail group by the name
     *
     * @since 0.6
     * @param Name $name
     * @return null|DetailGroup
     */
    public function getDetailGroup(Name $name)
    {
        if(!$this->hasDetailGroup($name)) {
            return null;
        }

        $detailGroup = $this->detailGroups[$name->getValue()];

        return $detailGroup;
    }

    /**
     * Get all detail groups
     *
     * @since 0.6
     * @return DetailGroup[]
     */
    public function getDetailGroups()
    {
        $detailGroups = array_values($this->detailGroups);

        return $detailGroups;
    }

    /**
     * Set all detail groups
     * If you do this, the old detail groups going to be replaced.
     *
     * @since 0.6
     * @param DetailGroup[] $detailGroups
     * @throws InvalidTypeException
     */
    public function setDetailGroups($detailGroups)
    {
        $this->detailGroups = array();

        // addDetailGroup checks for the type
        foreach ($detailGroups as $detail) {
            $this->addDetailGroup($detail);
        }
    }

    /**
     * Check if the product has a review
     *
     * @since 0.6
     * @return bool
     */
    public function hasReview()
    {
        return $this->review !== null;
    }

    /**
     * Get the optional review
     *
     * @since 0.6
     * @return null|Review
     */
    public function getReview()
    {
        return $this->review;
    }

    /**
     * Set the optional review
     *
     * @since 0.6
     * @param null|Review $review
     */
    public function setReview($review)
    {
        if($review !== null && !($review instanceof Review)) {
            throw new InvalidTypeException($review, 'Affilicious\Product\Domain\Model\Review\Review');
        }

        $this->review = $review;
    }

    /**
     * Get the IDs of all related products
     *
     * @since 0.6
     * @return ProductId[]
     */
    public function getRelatedProducts()
    {
        return $this->relatedProducts;
    }

    /**
     * Set the IDs of all related products
     * If you do this, the old IDs going to be replaced.
     *
     * @since 0.6
     * @param ProductId[] $relatedProducts
     * @throws InvalidTypeException
     */
    public function setRelatedProducts($relatedProducts)
    {
        foreach ($relatedProducts as $relatedProduct) {
            if (!($relatedProduct instanceof ProductId)) {
                throw new InvalidTypeException($relatedProduct, get_class(new ProductId(0)));
            }
        }

        $this->relatedProducts = $relatedProducts;
    }

    /**
     * Get the IDs of all related accessories
     *
     * @since 0.6
     * @return ProductId[]
     */
    public function getRelatedAccessories()
    {
        return $this->relatedAccessories;
    }

    /**
     * Set the IDs of all related accessories
     * If you do this, the old IDs going to be replaced.
     *
     * @since 0.6
     * @param ProductId[] $relatedAccessories
     * @throws InvalidTypeException
     */
    public function setRelatedAccessories($relatedAccessories)
    {
        foreach ($relatedAccessories as $relatedAccessory) {
            if (!($relatedAccessory instanceof ProductId)) {
                throw new InvalidTypeException($relatedAccessory, 'Affilicious\Product\Domain\Model\ProductId');
            }
        }

        $this->relatedAccessories = $relatedAccessories;
    }

    /**
     * Get the IDs of the media attachments for the image gallery
     *
     * @since 0.6
     * @return Image[]
     */
    public function getImageGallery()
    {
        return $this->imageGallery;
    }

    /**
     * Set the IDs of the media attachments for the image gallery
     * If you do this, the old images going to be replaced.
     *
     * @since 0.6
     * @param Image[] $imageGallery
     * @throws InvalidTypeException
     */
    public function setImageGallery($imageGallery)
    {
        foreach ($imageGallery as $image) {
            if (!($image instanceof Image)) {
                throw new InvalidTypeException($image, 'Affilicious\Common\Domain\Model\Image\Image');
            }
        }

        $this->imageGallery = $imageGallery;
    }

    /**
     * Get the raw Wordpress post
     *
     * @since 0.6
     * @return null|\WP_Post
     */
    public function getRawPost()
    {
        if(!$this->hasId()) {
            return null;
        }

        return get_post($this->id->getValue());
    }

    /**
     * @inheritdoc
     * @since 0.6
     */
    public function isEqualTo($object)
    {
        return
            $object instanceof self &&
            $this->getId()->isEqualTo($object->getId()) &&
            $this->getTitle()->isEqualTo($object->getTitle()) &&
            $this->getName()->isEqualTo($object->getName());
            // TODO: Compare the rest and check the best way to compare two arrays with objects inside
    }
}
