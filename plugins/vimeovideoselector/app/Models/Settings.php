<?php

namespace VimeoVideoSelector\Models;

use WPMVC\MVC\Traits\FindTrait;
use WPMVC\MVC\Models\OptionModel as Model;

/**
 * Vimeo Video Settings model.
 *
 * @author Vimeo Video
 * @copyright Vimeo Video <https://www.vvs.com>
 * @package VimeoVideoSelector
 * @version 1.0.1.2
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
    protected $id = 'vimeovideoselector_settings';

    /**
     * Field aliases and definitions.
     * @since 0.1.0
     * 
     * @var array
     */
    protected $aliases = [
        'client_id' => 'field_client_id',
        'client_secret'      => 'field_client_secret',
        'access_token'      => 'field_access_token',
        'channel'      => 'field_channel',
        'vvs_is_active'      => 'field_vvs_is_active',
    ];
}