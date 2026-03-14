### Http

#### Endpoints
API request config for an api call.
```
interface APIEndpoint {
  url: string
  params?: any
  headers?: Record<string, any>
}
```

Creating Endpoint use to fetch all members of the team
```
// infrastructure/endpoints/teams.ts
type GetTeamMembersFilter = {
  teamId: number
}

export const fetchTeamMembers = ({
  search: '',
  sortBy: 'name',
  sortOp: 'desc',
  page: 2,
  perPage: 12,
}: GetTeamMembersFilter) => ({
  url: `api/v1/teams/${teamId}/members`
  params: {
    search,
    sortBy,
    sortOp,
    page,
    perPage,
  }
})

// presentation/screens/TeamMembersListScreen.tsx

const resonse = await api.askTo(fetchTeamMembers({ teamId }))
```
