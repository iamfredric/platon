<?php

namespace Platon\Database;

trait FormatedContent
{
    public function getContentAttribute($content)
    {
        return apply_filters('the_content', $content);
    }
}
