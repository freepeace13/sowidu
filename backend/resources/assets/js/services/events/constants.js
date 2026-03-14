export const MODAL_SIZES = {
    XSMALL: 'xs',
    SMALL: 'sm',
    MEDIUM: 'md',
    LARGE: 'lg',
    XLARGE: 'xl'
}

export const MODULES = {
    MODAL: 'modal',
    SNACKBAR: 'snackbar',
    LOCKSCREEN: 'lockscreen',
    MEDIAVIEWER: 'viewer',
    CHUNK_UPLOADER: 'uploader',
    CONFIRM_DIALOG: 'confirm'
}

export const DEFAULT_CONFIG = {
    [MODULES.MODAL]: {
        size: MODAL_SIZES.MEDIUM
    },
    [MODULES.SNACKBAR]: {
        timeout: 3000
    }
}
