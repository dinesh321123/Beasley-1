<?php

namespace WPMVCVVS\MVC\Models;

use WPMVCVVS\MVC\Contracts\Modelable;
use WPMVCVVS\MVC\Contracts\Findable;
use WPMVCVVS\MVC\Contracts\Arrayable;
use WPMVCVVS\MVC\Contracts\JSONable;
use WPMVCVVS\MVC\Contracts\Stringable;
use WPMVCVVS\MVC\Traits\GenericModelTrait;
use WPMVCVVS\MVC\Traits\AliasTrait;
use WPMVCVVS\MVC\Traits\CastTrait;

/**
 * Abstract Model Class based on Wordpress Model.
 *
 * @author Alejandro Mostajo <http://about.me/amostajo>
 * @copyright 10Quality <http://www.10quality.com>
 * @license MIT
 * @package WPMVCVVS\MVC
 * @version 1.0.0
 */
abstract class OptionModel implements Findable, Modelable, Arrayable, JSONable, Stringable
{
    use GenericModelTrait, AliasTrait, CastTrait;
    /**
     * Option prefix.
     * @since 1.0.0
     * @var string
     */
    protected $prefix = 'model_';
    /**
     * Model id.
     * @since 1.0.0
     * @var string
     */
    protected $id;
    /**
     * Attributes in model.
     * @since 1.0.0
     * @var array
     */
    protected $attributes = array();
    /**
     * Attributes and aliases hidden from print.
     * @since 1.0.0
     * @var array
     */
    protected $hidden = array();
    /**
     * Default constructor.
     * @since 1.0.0
     */
    public function __construct($id = null)
    {
        if ( isset( $this->id ) && ! empty( $this->id )  )
            $this->load($this->id);
    }
    /**
     * Loads model from db.
     * @since 1.0.0
     *
     * @param string $id Option key ID.
     */
    public function load( $id )
    {
        $this->attributes = get_site_option( $this->prefix . $this->id );
        if ( $this->attributes == null )
            $this->attributes = array();
    }

    /**
     * Saves current model in the db.
     * @since 1.0.0
     *
     * @return mixed.
     */
    public function save()
    {
        if ( ! $this->is_loaded() ) return false;
        $this->fill_defaults();
        update_site_option( $this->prefix . $this->id, $this->attributes );
        return true;
    }
    /**
     * Deletes current model in the db.
     * @since 1.0.0
     *
     * @return mixed.
     */
    public function delete()
    {
        if ( ! $this->is_loaded() ) return false;
        delete_site_option( $this->prefix . $this->id);
        return true;
    }
    /**
     * Returns flag indicating if object is loaded or not.
     * @since 1.0.0
     *
     * @return bool
     */
    public function is_loaded()
    {
        return !empty( $this->attributes );
    }
    /**
     * Fills default when about to create object
     * @since 1.0.0
     */
    private function fill_defaults()
    {
        if ( ! array_key_exists('ID', $this->attributes) ) {
            $this->attributes['ID'] = $this->id;
        }
    }
}
