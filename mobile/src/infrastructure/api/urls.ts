import { UrlPrefix } from "@infrastructure/utils/urlPrefix"

const url = new UrlPrefix("/api/v1")

/**
 * SHARED
 */
export const GetInstitutionTypesUri = url.path("/public/institutions")
export const GetLegalFormsUri = url.path("/public/legal-forms")
export const GetPermissionsByGroupUri = url.path("/permissions")

/**
 * USER
 */

export const GetCurrentUserUri = url.path("/user")
export const SwitchAccountUri = url.path("/auth/switch")
export const LoginUserUri = url.path("/auth/login")
export const RegisterUserUri = url.path("/auth/register")
export const LogoutUserUri = url.path("/auth/logout")

export const UpdateUserAvatar = url.path("/user/change-avatar")
export const UpdateUserInfo = url.path("/user/profile")

/**
 * TEAMS
 */

export const GetTeamsUri = url.path("/teams")
export const CreateTeamUri = url.path("/teams")
export const GetTeamInfoUri = url.path("/teams/:teamId")
export const UpdateTeamInfoUri = url.path("/teams/:teamId/profile")
export const UpdateTeamAvatarUri = url.path("/teams/:teamId/change-avatar")
export const SearchMembersForInvitationUri = url.path("/teams/:teamId/new-members/search")

export const GetTeamMembersURi = url.path("/teams/:teamId/members")
export const SendTeamInvitationUri = url.path("/teams/:teamId/members")
export const SearchNewMembersUri = url.path("/teams/:teamId/new-members/search")
export const GetTeamMembersInfoUri = url.path("/teams/:teamId/members/:memberId")
export const UpdateTeamMembersInfoUri = url.path("/teams/:teamId/members/:memberId")

export const GetTeamRolesUri = url.path("/teams/:teamId/roles")
export const UpdateTeamRolesUri = url.path("/teams/:teamId/roles/:roleId/permissions")
export const CreateTeamRoleUri = url.path("/teams/:teamId/roles")
export const GetTeamRolePermissionsUri = url.path("/teams/:teamId/roles/:roleId")

/**
 * CHATS
 */
export const GetChatConversationsUri = url.path("/chats")
export const CreateChatConversationUri = url.path("/chats")
export const GetChatConversatioInfoUri = url.path("/chats/:conversationId")
export const GetChatConversationMessagesUri = url.path("/chats/:conversationId/messages")
export const SendChatConversationMessageUri = url.path("/chats/:conversationId/messages")
export const DeleteChatConversationMessageUri = url.path(
  "/chats/:conversationId/messages/:messageId"
)
export const UpdateChatConversationMessageUri = url.path(
  "/chats/:conversationId/messages/:messageId"
)
export const UpdateChatConversationInfoUri = url.path("/chats/:conversationId")
export const GetChatConversationParticipantsUri = url.path("/chats/:conversationId/participants")
export const AddChatConversationParticipantUri = url.path("/chats/:conversationId/participants")
export const RemoveChatConversationParticipantUri = url.path(
  "/chats/:conversationId/participants/:participationId"
)

/**
 * MEDIA
 */
export const GetMediaFilesUri = url.path("/media")
export const GetMediaInfoUri = url.path("/media/:mediaId")
export const UpdateMediaInfoUri = url.path("/media/:mediaId")
export const GetMediaSharedUsersUri = url.path("/media/:mediaId/users")
export const SearchMediaShareableUsersUri = url.path("/media/:mediaId/shareable-users")
export const AddMediaShareUri = url.path("/media/:mediaId/share")
export const RevokeMediaShareUri = url.path("/media/:mediaId/share")
