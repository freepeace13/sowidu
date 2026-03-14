export function ucFirst(str: string) {
  return str.charAt(0).toUpperCase() + str.slice(1)
}

export function title(str: string) {
  const words = str.replace(/_/g, " ").split(" ")
  return words.map((word) => ucFirst(word)).join(" ")
}
