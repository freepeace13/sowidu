import PlaceService from '../Services/PlaceService'

export function useGetCountries(keyword = null) {
    return new Promise((resolve, reject) => {
        new PlaceService().getCountries(keyword).then(resolve).catch(reject)
    }).then((response) => {
        return response
    })
}

export function useGetCountryStates(countryCode, keyword = null) {
    return new Promise((resolve, reject) => {
        new PlaceService()
            .getCountryStates(countryCode, keyword)
            .then(resolve)
            .catch(reject)
    }).then((response) => {
        return response
    })
}

export function useGetCountryCities(countryCode, keyword = null) {
    return new Promise((resolve, reject) => {
        new PlaceService()
            .getCountryCities(countryCode, keyword)
            .then(resolve)
            .catch(reject)
    }).then((response) => {
        return response
    })
}
