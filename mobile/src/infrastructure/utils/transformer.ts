import type { Transformer } from "@infrastructure/interfaces/Transformer"

export const createTransformer = <I = any, O = any>(
  transform: (schema: I) => O
): Transformer<I, O> => ({
  transform,
  collection(values) {
    return values.map(this.transform)
  },
})
