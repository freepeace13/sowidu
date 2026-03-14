export function debounce(func, timeout = 300) {
    let timer
    return (...args) => {
        clearTimeout(timer)
        timer = setTimeout(() => {
            func.apply(this, args)
        }, timeout)
    }
}

export function asyncDebounce(asyncFn, timeout = 300) {
    return debounce((callback) => {
        callback((...args) => asyncFn(...args))
    }, timeout)
}
