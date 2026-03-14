type ValidationErrors = Record<string, string[]>

export class MessageBag {
  public constructor(private readonly _messages: ValidationErrors = {}) {}

  public keys(): string[] {
    return Object.keys(this._messages)
  }

  public add(key: string, message: string): this {
    if (this.isUnique(key, message)) {
      const curr = this._messages[key] ?? []
      this._messages[key] = [...curr, message]
    }

    return this
  }

  public addIf(condition: boolean, key: string, message: string): this {
    return condition ? this.add(key, message) : this
  }

  private isUnique(key: string, message: string): boolean {
    const messages = this._messages[key] ?? []

    return typeof this._messages[key] === "undefined" || !messages.includes(message)
  }

  public has(key: string): boolean {
    if (this.isEmpty()) {
      return false
    }

    return this.first(key) !== undefined
  }

  public hasAny(keys: string[]): boolean {
    if (this.isEmpty()) {
      return false
    }

    return keys.some((key) => this.has(key))
  }

  public first(key: string): string {
    const messages = this.get(key) ?? []

    if (messages.length > 0) {
      return messages[0]
    }

    return undefined
  }

  public get(key: string): string[] {
    return this._messages[key] ?? null
  }

  public all(): Record<string, string[]> {
    return { ...this._messages }
  }

  public spread(callback: (key: any, message: string) => unknown) {
    this.keys().forEach((i) => callback(i, this.first(i)))
  }

  public forget(key: string): this {
    delete this._messages[key]

    return this
  }

  public isEmpty(): boolean {
    return this.keys().length === 0
  }

  public isNotEmpty(): boolean {
    return !this.isEmpty()
  }
}

export const asyncThunkRejectable = (error) => {
  if ([422].includes(error.code)) {
    return error
  }
  throw error
}

export const validationErrors = (error): MessageBag => {
  if (error.code === 422) {
    return new MessageBag(error.errors)
  }
  throw error
}
