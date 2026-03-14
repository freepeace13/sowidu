### Types

#### Schema
Type of data (`RequestResponse<TData>`) received from an API call.

Example:
```
// FILE: infrastructure/schema/TeamMemberSchema.ts
export interface TeamMemberSchema extends Omit<User, "currentTeam" | "permissions"> {
    teamId: number
    membershipId: number
    isOwner: boolean
    teamRole: string
    roles: any[]
    rates?: {
      memberId: number
      teamId: number
      rate: number
      currency: Nullable<Currency>
    }
  }

// FILE: domain/types/http.ts
export interface RequestResponse<T> {
  data: T,
  meta: {
    //...
  },
}

// Fetching team members by teamId
// FILE: presentation/screens/TeamMemberListScreen.tsx
const response: Promise<RequestResponse<UserSchema[]>> = await teamService.getTeamMembers(<team-id>)
```
