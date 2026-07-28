<?php

namespace App\Enums;

enum PermissionEnum: string
{
 
    // Youtube link video permissions
    case YOUTUBE_LINK_VIDEO_SHOW = 'YoutubeLinkVideo-show';



    /**
     * Get all permissions as an array
     */
    public static function getAllPermissions(): array
    {
        return array_column(self::cases(), 'value');
    }
}
