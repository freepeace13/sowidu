import AutoCompleteService from '../Services/AutoCompleteService'

export function useAutocompleteJobTitle(text, size) {
    return new Promise((resolve, reject) => {
        new AutoCompleteService()
            .jobTitle(text, size)
            .then(resolve)
            .catch(reject)
    }).then((response) => {
        return response
    })
}

export function useAutocompleteHouseNumber(text, size) {
    return new Promise((resolve, reject) => {
        new AutoCompleteService()
            .houseNumber(text, size)
            .then(resolve)
            .catch(reject)
    }).then((response) => {
        return response
    })
}

export function useAutocompleteStreet(text, size) {
    return new Promise((resolve, reject) => {
        new AutoCompleteService().street(text, size).then(resolve).catch(reject)
    }).then((response) => {
        return response
    })
}

export function useAutocompleteZipcode(text, size) {
    return new Promise((resolve, reject) => {
        new AutoCompleteService()
            .zipcode(text, size)
            .then(resolve)
            .catch(reject)
    }).then((response) => {
        return response
    })
}

/**
 * @param string text
 * @param int size
 */
export function useAutocompleteAddress(text, size) {
    return new Promise((resolve, reject) => {
        new AutoCompleteService()
            .address(text, size)
            .then(resolve)
            .catch(reject)
    }).then((response) => {
        return response
    })
}
