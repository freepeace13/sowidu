type ExtractParams<T extends string> = T extends `${string}:${infer Param}/${infer Rest}`
  ? Param | ExtractParams<`/${Rest}`>
  : T extends `${string}:${infer Param}`
  ? Param
  : never

class UrlPattern<T extends string, P extends Record<ExtractParams<T>, string | number>> {
  constructor(private readonly _pattern: T) {}

  public keys(): ExtractParams<T>[] {
    const matches = [...this._pattern.matchAll(/:([a-zA-Z_]\w*)/g)]
    return matches.map((match) => match[1] as ExtractParams<T>)
  }

  public replace(params: P): string {
    return this._pattern.replace(/:([a-zA-Z_]\w*)/g, (_, key) => {
      if (key in params) {
        return encodeURIComponent(params[key as keyof P])
      }
      throw new Error(`Missing value for parameter: ${key}`)
    })
  }
}

export default UrlPattern
