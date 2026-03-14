import { Observable } from "./Observable"

type Subscriber = (...args) => void

export class PubSub {
  public constructor(private _events: { [key: string]: Observable } = {}) {}

  public $on(event: string, listener: Subscriber) {
    if (!this._events[event]) {
      this._events[event] = new Observable()
    }
    const unsubscribe = this._events[event].$on(listener)
    return () => unsubscribe()
  }

  public $dispatch(event: any) {
    this.$emit(event.type, { payload: event.payload })
  }

  public $emit(event: string, ...args) {
    if (this._events[event]) {
      this._events[event].$emit(...args)
    }
  }
}
