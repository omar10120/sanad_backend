<?php

namespace App\Enums;

enum PermissionEnum: string
{
    // User permissions
    case USER_SHOW = 'User-show';
    case USER_ADD = 'User-add';
    case USER_EDIT = 'User-edit';
    case USER_DELETE = 'User-delete';
    case USER_SHOW_DELETED = 'User-show-deleted';
    case USER_RESTORE_DELETED = 'User-restore-deleted';

    // Student permissions
    case STUDENT_SHOW = 'Student-show';
    case STUDENT_ADD = 'Student-add';
    case STUDENT_EDIT = 'Student-edit';
    case STUDENT_DELETE = 'Student-delete';
    case STUDENT_SHOW_DELETED = 'Student-show-deleted';
    case STUDENT_RESTORE_DELETED = 'Student-restore-deleted';

    // Type permissions
    case TYPE_SHOW = 'Type-show';
    case TYPE_ADD = 'Type-add';
    case TYPE_EDIT = 'Type-edit';
    case TYPE_DELETE = 'Type-delete';
    case TYPE_SHOW_DELETED = 'Type-show-deleted';
    case TYPE_RESTORE_DELETED = 'Type-restore-deleted';

    // Subject permissions
    case SUBJECT_SHOW = 'Subject-show';
    case SUBJECT_ADD = 'Subject-add';
    case SUBJECT_EDIT = 'Subject-edit';
    case SUBJECT_DELETE = 'Subject-delete';
    case SUBJECT_SHOW_DELETED = 'Subject-show-deleted';
    case SUBJECT_RESTORE_DELETED = 'Subject-restore-deleted';

    // Lesson permissions
    case LESSON_SHOW = 'Lesson-show';
    case LESSON_ADD = 'Lesson-add';
    case LESSON_EDIT = 'Lesson-edit';
    case LESSON_DELETE = 'Lesson-delete';
    case LESSON_SHOW_DELETED = 'Lesson-show-deleted';
    case LESSON_RESTORE_DELETED = 'Lesson-restore-deleted';

    // Question permissions
    case QUESTION_SHOW = 'Question-show';
    case QUESTION_ADD = 'Question-add';
    case QUESTION_EDIT = 'Question-edit';
    case QUESTION_DELETE = 'Question-delete';
    case QUESTION_SHOW_DELETED = 'Question-show-deleted';
    case QUESTION_RESTORE_DELETED = 'Question-restore-deleted';

    // Tag permissions
    case TAG_SHOW = 'Tag-show';
    case TAG_ADD = 'Tag-add';
    case TAG_EDIT = 'Tag-edit';
    case TAG_DELETE = 'Tag-delete';
    case TAG_SHOW_DELETED = 'Tag-show-deleted';
    case TAG_RESTORE_DELETED = 'Tag-restore-deleted';


    // Teacher permissions
    case TEACHER_SHOW = 'Teacher-show';
    case TEACHER_ADD = 'Teacher-add';
    case TEACHER_EDIT = 'Teacher-edit';
    case TEACHER_DELETE = 'Teacher-delete';
    case TEACHER_SHOW_DELETED = 'Teacher-show-deleted';
    case TEACHER_RESTORE_DELETED = 'Teacher-restore-deleted';

    // Subject video (courses) permissions
    case SUBJECT_VIDEO_SHOW = 'SubjectVideo-show';
    case SUBJECT_VIDEO_ADD = 'SubjectVideo-add';
    case SUBJECT_VIDEO_EDIT = 'SubjectVideo-edit';
    case SUBJECT_VIDEO_DELETE = 'SubjectVideo-delete';
    case SUBJECT_VIDEO_SHOW_DELETED = 'SubjectVideo-show-deleted';
    case SUBJECT_VIDEO_RESTORE_DELETED = 'SubjectVideo-restore-deleted';

    // Unit permissions
    case UNIT_SHOW = 'Unit-show';
    case UNIT_ADD = 'Unit-add';
    case UNIT_EDIT = 'Unit-edit';
    case UNIT_DELETE = 'Unit-delete';

    // Lesson video permissions
    case LESSON_VIDEO_SHOW = 'LessonVideo-show';
    case LESSON_VIDEO_ADD = 'LessonVideo-add';
    case LESSON_VIDEO_EDIT = 'LessonVideo-edit';
    case LESSON_VIDEO_DELETE = 'LessonVideo-delete';
    case LESSON_VIDEO_SHOW_DELETED = 'LessonVideo-show-deleted';
    case LESSON_VIDEO_RESTORE_DELETED = 'LessonVideo-restore-deleted';

    // Youtube link video permissions
    case YOUTUBE_LINK_VIDEO_SHOW = 'YoutubeLinkVideo-show';
    case YOUTUBE_LINK_VIDEO_ADD = 'YoutubeLinkVideo-add';
    case YOUTUBE_LINK_VIDEO_EDIT = 'YoutubeLinkVideo-edit';
    case YOUTUBE_LINK_VIDEO_DELETE = 'YoutubeLinkVideo-delete';

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
