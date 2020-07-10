<?php

namespace Ferranfg\Base\Configuration;

trait ManagesModelOptions
{
    /**
     * The comment model class name.
     *
     * @var string
     */
    public static $commentModel = 'Ferranfg\Base\Models\Comment';

    /**
     * The post model class name.
     *
     * @var string
     */
    public static $postModel = 'Ferranfg\Base\Models\Post';

    /**
     * The post model class name.
     *
     * @var string
     */
    public static $productModel = 'Ferranfg\Base\Models\Product';

    /**
     * The tag model class name.
     *
     * @var string
     */
    public static $tagModel = 'Ferranfg\Base\Models\Tag';

    /**
     * Set the comment model class name.
     *
     * @param  string  $commentModel
     * @return void
     */
    public static function useCommentModel($commentModel)
    {
        static::$commentModel = $commentModel;
    }

    /**
     * Get a new comment model instance.
     *
     * @return \Ferranfg\Base\Models\Comment
     */
    public static function comment()
    {
        return new static::$commentModel;
    }

    /**
     * Set the post model class name.
     *
     * @param  string  $postModel
     * @return void
     */
    public static function usePostModel($postModel)
    {
        static::$postModel = $postModel;
    }

    /**
     * Get a new post model instance.
     *
     * @return \Ferranfg\Base\Models\Post
     */
    public static function post()
    {
        return new static::$postModel;
    }

    /**
     * Set the product model class name.
     *
     * @param  string  $productModel
     * @return void
     */
    public static function useProductModel($productModel)
    {
        static::$productModel = $productModel;
    }

    /**
     * Get a new product model instance.
     *
     * @return \Ferranfg\Base\Models\Product
     */
    public static function product()
    {
        return new static::$productModel;
    }

    /**
     * Set the tag model class name.
     *
     * @param  string  $tagModel
     * @return void
     */
    public static function useTagModel($tagModel)
    {
        static::$tagModel = $tagModel;
    }

    /**
     * Get a new tag model instance.
     *
     * @return \Ferranfg\Base\Models\Tag
     */
    public static function tag()
    {
        return new static::$tagModel;
    }

}