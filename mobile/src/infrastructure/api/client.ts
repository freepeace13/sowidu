import type { ApiClient, RequestConfig } from "@infrastructure/interfaces/ApiClient"
import { axiosInstance } from "./axios"

export const apiClient: ApiClient = {
  async send<R = any, D = any>(config: RequestConfig<D>) {
    const { method = "GET", body: data, url, params, headers } = config
    return await axiosInstance.request<R, RequestConfig["body"]>({
      method,
      data,
      url,
      params,
      headers,
    })
  },
}
