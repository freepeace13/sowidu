export function objectToFormData(
  obj: Record<string, any>,
  form = new FormData(),
  parentKey: string = ""
) {
  for (const key in obj) {
    if (obj.hasOwnProperty(key)) {
      const propName = parentKey ? `${parentKey}[${key}]` : key
      const value = obj[key]

      if (value instanceof Date) {
        form.append(propName, value.toISOString())
      } else if (value instanceof File) {
        form.append(propName, value)
      } else if (typeof value === "object" && value !== null) {
        objectToFormData(value, form, propName) // Recursion for nested objects
      } else {
        form.append(propName, value)
      }
    }
  }
  return form
}
