/** @flow */

export default {
    Notification: '.Illuminate\\Notifications\\Events\\BroadcastNotificationCreated',
    CommentCreated: '.Packages\\EloquentCommentable\\Events\\CommentCreated',
    CommentDeleted: '.Packages\\EloquentCommentable\\Events\\CommentDeleted',
    Contact: {
        RelationshipUpdate: 'Contact\\RelationshipUpdate',
    },
    Product: {
        ProductUpdate: 'Product\\ProductUpdate',
    },
    Task: {
        TaskUpdate: 'Task\\TaskUpdate',
    },
    Media: {
        MediaUpdate: 'Media\\MediaUpdate',
    },
    Delivery: {
        DeliveryUpdate: 'Delivery\\DeliveryUpdate',
    },
    Order: {
        OrderUpdate: 'Order\\OrderUpdate',
        DeliveriesUpdate: 'Order\\DeliveriesUpdate',
        MembersUpdate: 'Order\\MembersUpdate',
        TasksUpdate: 'Order\\TasksUpdate',
        ItemsUpdate: 'Order\\ItemsUpdate',
        MediaUpdate: 'Order\\MediaUpdate',
        ProgressUpdated: 'Order\\ProgressUpdated'
    },
    Invitation: {
        InvitationSent: 'Invitation\\InvitationSent',
    },
    Employment: {
        PushedEmployee: 'Employment\\PushedEmployee'
    }
}