import { apiClient } from "@infrastructure/api/client"
import type { RequestResponse, RequestConfig } from "@infrastructure/interfaces/ApiClient"
// import axios from "axios"

interface ResponseTransformer<IReceived, IReturn> {
  (response: RequestResponse<IReceived>): IReturn
}

type RequestOptions<IReceived, IReturn> = RequestConfig & {
  transformResponse?: ResponseTransformer<IReceived, IReturn>
}

export const request = <IReceived, IReturn>({
  transformResponse,
  ...restOptions
}: RequestOptions<IReceived, IReturn>): Promise<IReturn> => {
  return apiClient
    .send<IReceived>(restOptions)
    .then((response) => {
      if (typeof transformResponse === "function") {
        return transformResponse(response)
      }
      return response as IReturn
    })
    .catch((e) => {
      throw e
    })
}
