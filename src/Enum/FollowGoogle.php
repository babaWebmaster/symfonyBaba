<?php
namespace App\Enum;

enum FollowGoogle: string
{
    case NULL = "";
    case FOLLOW = "follow";
    case NOFOLLOW = "nofollow";
}

?>