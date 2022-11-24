<?php

namespace Ferranfg\Base\Nova\Actions;

class PostCustomersNewsletter extends PostNewsletter
{
    /**
     * The type of newsletter we are sending.
     *
     * @var string
     */
    public $newsletter_type = 'customers';
}
