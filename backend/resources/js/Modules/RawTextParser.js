class RawTextParser {
    constructor(text) {
        this.lines = text.split('\n').map((line) => line.trim())
        this.data = {
            type: 'raw',
        }
    }

    parseDeliveryAddress() {
        this.data.address = this.lines[0]?.substring(40, 80).trim()
    }

    parseExternalID() {
        this.data.externalId = this.lines[0].split(/\s{2,}/).filter(Boolean)[3]
    }

    parsePrice(primary, secondary) {
        if (/[a-zZ-a]/.test(primary)) {
            return this.parsePurchase(secondary)
        }

        return this.parsePurchase(primary)
    }

    parsePurchase(value) {
        let purchasePrice = parseFloat(value.substring(0, 11)) / 100
        let total = parseFloat(value.substring(12, 24)) / 100
        let quantity = total / purchasePrice

        return { purchasePrice, quantity, total }
    }

    parseItems() {
        this.data.items = this.lines
            .filter(Boolean)
            .filter(
                (line, key) =>
                    key !== 0 &&
                    key !== this.lines.length - 1 &&
                    key !== this.lines.length - 2,
            )
            .map((line) => line.split(/\s{2,}/).filter(Boolean))
            .reduce((acc, item, index) => {
                const regex = /^POA\d+/g
                let [
                    serialRaw,
                    quantityDescription,
                    purchaseSalePrice,
                    secondaryPurchaseSalePrice,
                ] = item

                const { purchasePrice, quantity, total } = this.parsePrice(
                    purchaseSalePrice,
                    secondaryPurchaseSalePrice,
                )

                const parts = serialRaw.split(/\s+/)
                const articleNumber = parts.shift()
                const remainingText = parts.join(' ')

                // 🛠 Extract description from quantityDescription

                let match =
                    quantityDescription.match(/^0*(\d+)([A-Za-z].*?)(?=,|$)/) ??
                    []
                let description =
                    match[2]?.trim() ||
                    remainingText ||
                    'No description available'

                let manufacturerMatch = serialRaw.match(
                    /([A-Z][a-z]+|[A-Z]{2,})/g,
                )

                let manufacture_id = manufacturerMatch
                    ? manufacturerMatch[1]
                    : articleNumber.replace(regex, '')
                let descriptionCleaned = description.replace(/^\d+/, '').trim()
                let unitMatch = descriptionCleaned.match(
                    /(\d+\.?\d*)\s*(mm|cm|m|pcs|kg|bar|l\/min|V|Ah)/i,
                )
                let unitRaw = unitMatch ? unitMatch[2] : 'NA'

                acc[index] = {
                    articleNumber: articleNumber.replace(regex, ''),
                    quantity: Number(quantity),
                    name: descriptionCleaned,
                    description: descriptionCleaned,
                    purchasing_price: Number(purchasePrice),
                    total,
                    type: articleNumber.replace(regex, ''),
                    manufacture_id,
                    unitRaw,
                }
                return acc
            }, [])
    }

    parse() {
        this.parseDeliveryAddress()
        this.parseExternalID()
        this.parseItems()
        return this.data
    }
}

export default RawTextParser
