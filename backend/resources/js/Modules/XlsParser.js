class XlsParser {
    constructor(htmlString) {
        this.doc = new DOMParser().parseFromString(htmlString, 'text/html')
        this.data = {
            type: 'xls',
        }
    }

    parseDeliveryAddress() {
        const rows = this.doc.querySelectorAll('table tbody tr')
        this.data.address = [
            rows[21].children[1]?.textContent.trim(),
            rows[22].children[1]?.textContent.trim(),
        ]
            .filter(Boolean)
            .join(', ')
    }

    parseExternalId() {
        const rows = this.doc.querySelectorAll('table tbody tr')
        this.data.externalId = rows[14]?.children[1]?.textContent.trim() || ''
    }

    parseItems() {
        this.data.items = []
        const rows = this.doc.querySelectorAll('table:nth-of-type(2) tbody tr')

        for (let i = 1; i < rows.length; i++) {
            const cells = rows[i].children
            if (cells.length > 1) {
                this.data.items.push({
                    articleNumber: cells[2]?.textContent.trim() || '',
                    name: cells[4]?.textContent.trim() || '',
                    quantity:
                        parseFloat(
                            cells[3]?.textContent.trim().replace(',', '.'),
                        ) || '',
                    description: cells[4]?.textContent.trim() || '',
                    purchasing_price:
                        parseFloat(
                            cells[5]?.textContent.trim().replace(',', '.'),
                        ) || '',
                    unitRaw: cells[6]?.textContent.trim() || 'NA',
                    manufacture_id:
                        cells[12]?.textContent.trim() ||
                        cells[2]?.textContent.trim() ||
                        'NA', // Assuming manufacturer ID is in column 12
                    type: cells[13]?.textContent.trim() || 'NA',
                    total:
                        parseFloat(
                            cells[8]?.textContent.trim().replace(',', '.'),
                        ) || '',
                })
            }
        }
    }

    parseDeliveryDate() {
        const rows = this.doc.querySelectorAll('table tbody tr')
        this.data.deliveryDate = rows[15]?.children[1]?.textContent.trim() || ''

        // Add this line
    }

    parse() {
        this.parseDeliveryAddress()
        this.parseExternalId()
        this.parseItems()
        this.parseDeliveryDate()
        return this.data
    }
}

export default XlsParser
