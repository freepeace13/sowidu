import ResourceRoute from './routes/resource-route';
import InvitationSent from './routes/invitation-sent';
import InvitationAccepted from './routes/invitation-accepted';

export default {
    'App\\Notifications\\Common\\MemberAdded': ResourceRoute,
    'App\\Notifications\\Common\\StateUpdated': ResourceRoute,
    'App\\Notifications\\Common\\CommentPosted': ResourceRoute,
    'App\\Notifications\\Invitation\\InvitationSent': InvitationSent,
    'App\\Notifications\\Invitation\\InvitationAccepted': InvitationAccepted,
}