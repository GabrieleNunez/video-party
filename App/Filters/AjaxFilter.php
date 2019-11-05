<?php namespace App\Filters;

use Library\Request;

class AjaxFilter
{
    // checks to make sure the currently logged in user is in fact an admin
    public function is_ajax()
    {
        return Request::isAjax();
    }
}
?>
