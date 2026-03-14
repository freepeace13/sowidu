import { useState } from "react"
import { useSearchUsersForInvitationQuery } from "@presentation/features/teams/teamsApi"
import debounce from "lodash/debounce"

type Options = {
  limit?: number
}

export type DataType = {
  name: string
  email: string
  photo: string
}

export function useInviteTeamMemberSearchAsync(teamId: number, { limit = 3 }: Options) {
  const [keyword, setKeyword] = useState("")
  const searchQueryParams = {
    teamId,
    limit,
    keyword,
  }
  const { data = [], isLoading } = useSearchUsersForInvitationQuery(searchQueryParams, {
    skip: keyword.length === 0,
  })

  const clearData = () => {
    setKeyword("")
  }

  const searchPeople = debounce(setKeyword, 300)

  return {
    searchPeople,
    isLoading,
    data: data.map<DataType>((i: any) => ({
      email: i.email,
      name: i.fullName,
      photo: i.photo,
    })),
    clearData,
  }
}
