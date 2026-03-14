import { TeamMember } from "@infrastructure/schema/TeamMember"
import { createTransformer } from "@infrastructure/utils/transformer"
import { Member } from "@domain/teams/members/Member"

export const teamMemberTransformer = createTransformer<TeamMember, Member>(
  ({ teamId, membershipId, roles, teamRole, isOwner, ...restAttributes }) => ({
    teamId: teamId,
    memberId: membershipId,
    teamRoles: roles,
    role: teamRole,
    rates: restAttributes.rates,
    ownsTeam: isOwner,
    userInfo: {
      id: restAttributes.id,
      name: restAttributes.fullName,
      email: restAttributes.email,
      photoURL: restAttributes.photo,
    },
  })
)
