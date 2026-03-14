import { authService } from "@application/main"
import { sharedApi } from "@presentation/features/shared/api"
import { User } from "@domain/user/User"

const accountApi = sharedApi.injectEndpoints({
  overrideExisting: false,
  endpoints: (build) => ({
    switchAccount: build.mutation<User["currentTeam"], { urn: string }>({
      queryFn: ({ urn }) =>
        authService
          .switchAccount(urn)
          .then((data) => ({ data: data ?? null }))
          .catch((error) => ({ error })),
    }),
  }),
})

export const { switchAccount } = accountApi.endpoints

export const { useSwitchAccountMutation } = accountApi
