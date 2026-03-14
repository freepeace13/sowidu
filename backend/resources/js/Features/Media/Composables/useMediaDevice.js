import { ref } from 'vue'

export default function useMediaDevice() {
    const devices = ref([])
    const navigator = window.navigator

    if (navigator && navigator.mediaDevices) {
        navigator.mediaDevices
            .enumerateDevices()
            .then((devs) => (devices.value = [...devs]))
            .catch((err) => {
                console.error(`${err.name}: ${err.message}`)
            })
    }

    return devices
}
