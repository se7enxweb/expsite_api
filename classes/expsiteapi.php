<?php
class expSiteApi
{
    public static function content()
    {
        return new expSiteApiContentService();
    }

    public static function location()
    {
        return new expSiteApiLocationService();
    }

    public static function filter()
    {
        return new expSiteApiFilterService();
    }
}
