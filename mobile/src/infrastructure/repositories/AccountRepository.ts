import { AccountRepository } from "@domain/auth/AccountRepository"
import { User } from "@domain/user/User"
import { User as UserSchema } from "@infrastructure/schema/User"
import { SwitchAccountUri } from "@infrastructure/api/urls"
import { teamTransformer } from "@infrastructure/transformers/TeamTransformer"
import { request } from "@infrastructure/api/request"

export const accountRepository: AccountRepository = {
  async switchAccount(urn) {
    return await request<{ currentTeam: UserSchema["currentTeam"] }, User["currentTeam"]>({
      url: SwitchAccountUri.replace({}),
      method: "POST",
      body: { urn },
      transformResponse: ({ data }) =>
        data.currentTeam
          ? {
              ...teamTransformer.transform(data.currentTeam),
              membershipId: data.currentTeam.membershipId,
            }
          : undefined,
    })
  },
}
