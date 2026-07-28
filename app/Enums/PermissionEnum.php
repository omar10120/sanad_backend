<?php

namespace App\Enums;

enum PermissionEnum: string
{
   
    case YOUTUBE_LINK_VIDEO_SHOW_DELETED = 'YoutubeLinkVideo-show-deleted';
    case YOUTUBE_LINK_VIDEO_RESTORE_DELETED = 'YoutubeLinkVideo-restore-deleted';

    /**
     * Get all permissions as an array
     */
    public static function getAllPermissions(): array
    {
        return array_column(self::cases(), 'value');
    }
}
