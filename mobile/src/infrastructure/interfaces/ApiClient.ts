export type Method = "GET" | "POST" | "PATCH" | "PUT" | "DELETE"

export type RequestHeaders = Record<string, string | number | boolean>

export type RequestConfig<D = any> = {
  method: Method
  url: string
  body?: D
  params?: any
  headers?: RequestHeaders
}

export type RequestResponse<R = any> = {
  data: R
}

export interface ApiClient {
  send<R = any, D = any>(config: RequestConfig<D>): Promise<RequestResponse<R>>
}
