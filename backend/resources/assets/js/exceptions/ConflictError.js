class ConflictError extends Error {
    constructor(message) {
        super(message)

        this.name = 'ConflictError'
        this.status = 403
    }
}

export default ConflictError
