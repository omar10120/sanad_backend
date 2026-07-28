<?php

namespace App\Enums;

enum PermissionEnum: string
{
  
    case YOUTUBE_LINK_VIDEO_ADD = 'YoutubeLinkVideo-add';

   
    /**
     * Get all permissions as an array
     */
    public static function getAllPermissions(): array
    {
        return array_column(self::cases(), 'value');
    }
}
