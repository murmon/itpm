<?php
class AccessFilter extends CFilter
{
    protected function preFilter($filterChain)
    {
        if(User::isActivated()){
            return true;
        } else {
            echo 'Admin needs to confirm your email.';
            return false;
        }
    }

    protected function postFilter($filterChain)
    {

    }
}