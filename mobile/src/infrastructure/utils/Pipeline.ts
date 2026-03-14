interface Pipe<T, R> {
  (passable: T): R
}

export class Pipeline<T> {
  constructor(private passable: T, private pipes: Pipe<any, any>[] = []) {}

  public static send<T>(passable: T): Pipeline<T> {
    return new Pipeline(passable)
  }

  public through(pipes: Pipe<T, any>[]): Pipeline<T> {
    this.pipes = pipes
    return this
  }

  public pipe<R>(pipe: Pipe<T, R>): Pipeline<T> {
    this.pipes.push(pipe)
    return this
  }

  public then<R>(callback: (passable: T) => R): R {
    return callback(this.pipes.reduce((prev, pipe) => pipe(prev), this.passable))
  }

  public thenReturn(): T {
    return this.then((passable) => passable)
  }
}
