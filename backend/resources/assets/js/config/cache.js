import SecureLocalStorage from '~/services/store/storages/secureLS';

export default {
    default: 'secure-ls',

    key: 'cache',

    stores: {
        'secure-ls': SecureLocalStorage({
            isCompression: false,
        })
    }
}