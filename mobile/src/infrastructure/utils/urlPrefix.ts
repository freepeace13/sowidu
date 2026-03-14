import UrlPattern from "./UrlPattern"

export class UrlPrefix {
  constructor(private readonly _baseUrl: string) {}

  public path<T extends string>(path: T) {
    const baseUrl = this._baseUrl.toString()
    return new UrlPattern(`${baseUrl}${path}`)
  }
}
