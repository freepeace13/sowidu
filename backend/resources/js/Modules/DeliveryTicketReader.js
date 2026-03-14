class DeliveryTicketReader {
    constructor(file) {
        this.file = file
    }

    async read() {
        return new Promise((resolve, reject) => {
            const reader = new FileReader()
            reader.onload = (event) => {
                let binaryData = new Uint8Array(event.target.result)
                let decoder = new TextDecoder('windows-1252') // Use Windows-1252 decoder
                let utf8Text = decoder.decode(binaryData)
                resolve(utf8Text)
            }
            reader.onerror = (error) => reject(error)

            reader.readAsArrayBuffer(this.file) // Use ArrayBuffer instead of readAsText
        })
    }
}

export default DeliveryTicketReader
