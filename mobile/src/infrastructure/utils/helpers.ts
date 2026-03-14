export function rejectNullableValues(obj: Record<string, any>) {
  return Object.keys(obj).reduce((acc, curr) => {
    if (obj[curr] != null && obj[curr] !== undefined) {
      acc[curr] = obj[curr]
    }
    return acc
  }, Object.create(null))
}
