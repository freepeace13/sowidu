export class Observable {
  public constructor(private _observers = []) {}

  public $on(callback) {
    this._observers.push(callback)
    return () => {
      this._observers = this._observers.filter((o) => o !== callback)
    }
  }

  public $emit(...args) {
    this._observers.forEach((callback) => {
      callback(...args)
    })
  }
}
