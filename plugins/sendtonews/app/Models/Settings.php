<?php

namespace SendtoNews\Models;

use WPMVC\MVC\Traits\FindTrait;
use WPMVC\MVC\Models\OptionModel as Model;

/**
 * STN Video Settings model.
 *
 * @author STN Video
 * @copyright STN Video <https://www.stnvideo.com>
 * @package SendtoNews
 * @version 1.0.0
 */
class Settings extends Model
{
    use FindTrait;

    /**
     * Model id.
     * @since 0.1.0
     *
     * @var string
     */
    protected $id = 'sendtonews_settings';

    /**
     * Field aliases and definitions.
     * @since 0.1.0
     * 
     * @var array
     */
    protected $aliases = [
        'authcode' => 'field_authcode',
        'cid'      => 'field_cid',
    ];

}