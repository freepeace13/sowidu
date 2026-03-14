export const shapeGridItem = (item) => ({
    id: item.id,
    count: item.quantity || 1,
    unit: item.unit,
    price: item.retail_price,
    total: ((item.quantity || 1) * item.retail_price).toFixed(2),
    sequence: item.sequence instanceof Function
        ? item.sequence() : item.sequence
})

export const shapeGridItems = (items) => {
    return items.map((v, k) => {
        return {
            ...shapeGridItem(v),
            sequence: k
        }
    })
}
