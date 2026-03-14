export function useRippleEffect(element) {
    let x = 0

    let event = new Event('mousedown')
    let offset = element.getBoundingClientRect()

    event.clientX = offset.left + offset.width / 2
    event.clientY = offset.top + offset.height / 2

    var interval = setInterval(function () {
        element.dispatchEvent(event)

        setTimeout(function () {
            element.dispatchEvent(new Event('mouseup'))
        }, 700)

        if (++x === 2) {
            window.clearInterval(interval)
        }
    }, 600)
}
