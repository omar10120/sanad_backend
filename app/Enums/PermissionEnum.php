<?php

namespace App\Enums;

enum PermissionEnum: string
{
   
    case YOUTUBE_LINK_VIDEO_SHOW_DELETED = 'YoutubeLinkVideo-show-deleted';
    case YOUTUBE_LINK_VIDEO_RESTORE_DELETED = 'YoutubeLinkVideo-restore-deleted';

    // Role permissions
    case ROLE_SHOW = 'Role-show';
    case ROLE_ADD = 'Role-add';
    case ROLE_EDIT = 'Role-edit';
    case ROLE_DELETE = 'Role-delete';

    // Code permissions
    case CODE_SHOW = 'Code-show';
    case CODE_ADD = 'Code-add';
    case CODE_EDIT = 'Code-edit';
    case CODE_DELETE = 'Code-delete';

    // Phone verification codes permissions
    case PHONE_VERIFICATION_CODES = 'Phone-verification-codes';

    // Graphs permissions
    case GRAPHS = 'Graphs';

    // Notification permissions
    case NOTIFICATION_SHOW = 'Notification-show';
    case NOTIFICATION_ADD = 'Notification-add';
    case NOTIFICATION_EDIT = 'Notification-edit';
    case NOTIFICATION_DELETE = 'Notification-delete';
    case NOTIFICATION_SEND = 'Notification-send';

    /**
     * Get all permissions as an array
     */
    public static function getAllPermissions(): array
    {
        return array_column(self::cases(), 'value');
    }
}
