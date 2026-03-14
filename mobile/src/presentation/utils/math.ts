export function progressPercentage(processed: number, expectedToProcess: number) {
  return Math.floor((processed / expectedToProcess) * 100)
}
