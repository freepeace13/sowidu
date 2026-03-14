export default function usePlatform() {
    const pattern =
        /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i
    const navigator = window.navigator

    if (pattern.test(navigator.userAgent)) {
        return 'mobile'
    } else {
        return 'desktop'
    }
}
