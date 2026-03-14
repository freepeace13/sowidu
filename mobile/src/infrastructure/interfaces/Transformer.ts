export interface Transformer<T, I> {
  transform: (schema: T) => I
  collection: (values: T[]) => I[]
}
