// import "./bootstrap"

import { userRepository } from "@infrastructure/repositories/UserRepository"
import { tokenRepository } from "@infrastructure/repositories/TokenRepository"

import { permissionGroupRepository } from "@infrastructure/repositories/PermissionGroupRepository"

import { authService as AuthService } from "@domain/auth/AuthService"
import { userService as UserService } from "@domain/user/UserService"
import { permissionGroupService as PermissionGroupService } from "@domain/auth/PermissionGroupService"
import { mediaService as MediaService } from "@domain/media/MediaService"
import { mediaUploadService as MediaUploadService } from "@domain/media/MediaUploadService"
import { mediaShareService as MediaShareService } from "@domain/media/MediaShareService"
import { teamService as TeamService } from "@domain/teams/TeamService"
import { teamRolePermissionService as TeamRolePermissionService } from "@domain/teams/TeamRolePermissionService"
import { teamMemberService as TeamMemberService } from "@domain/teams/TeamMemberService"
import { chatService as ChatService } from "@domain/chats/ChatService"

import { accountRepository } from "@infrastructure/repositories/AccountRepository"
import { userProfileRepository } from "@infrastructure/repositories/UserProfileRepository"
import { teamRepository } from "@infrastructure/repositories/TeamRepository"
import { teamRoleRepository } from "@infrastructure/repositories/TeamRoleRepository"
import { teamMemberRepository } from "@infrastructure/repositories/TeamMemberRepository"
import { mediaRepository } from "@infrastructure/repositories/MediaRepository"
import { mediaShareRepository } from "@infrastructure/repositories/MediaShareRepository"
import { mediaUploadRepository } from "@infrastructure/repositories/MediaUploadRepository"
import { apiClient } from "@infrastructure/api/client"

import { attributeValueService as TeamAttributeValueService } from "@domain/teams/AttributeValueService"
import { teamAttributeValueRepository } from "@infrastructure/repositories/TeamAttributeValueRepository"
import { chatsConversationRepository } from "@infrastructure/repositories/ChatsConversationRepository"
import { chatsMessageRepository } from "@infrastructure/repositories/ChatsMessageRepository"
import { chatsParticipantRepository } from "@infrastructure/repositories/ChatsParticipantRepository"

export const permissionGroupService = PermissionGroupService(permissionGroupRepository)

export const authService = AuthService(tokenRepository, userRepository, accountRepository)
export const userService = UserService(tokenRepository, userRepository, userProfileRepository)

export const teamService = TeamService(teamRepository)
export const teamAttributeService = TeamAttributeValueService(teamAttributeValueRepository)
export const teamRolePermissionService = TeamRolePermissionService(teamRoleRepository)
export const teamMemberService = TeamMemberService(teamMemberRepository)
export const teamRoleService = TeamRolePermissionService(teamRoleRepository)

export const mediaService = MediaService(mediaRepository)
export const mediaShareService = MediaShareService(mediaShareRepository)
export const mediaUploadService = MediaUploadService(mediaUploadRepository)

export const chatService = ChatService(
  chatsConversationRepository,
  chatsMessageRepository,
  chatsParticipantRepository
)

export { apiClient }
