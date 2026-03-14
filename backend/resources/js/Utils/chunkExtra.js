const chunk = (items, firstChunkSize, subsequentChunkSize) => {
    const result = []
    // First chunk with customizable size
    result.push(items.slice(0, firstChunkSize))
    // Remaining items chunked by subsequent customizable size
    let remainingItems = items.slice(firstChunkSize)
    while (remainingItems.length > 0) {
        result.push(remainingItems.slice(0, subsequentChunkSize))
        remainingItems = remainingItems.slice(subsequentChunkSize)
    }

    return result
}

export default chunk
