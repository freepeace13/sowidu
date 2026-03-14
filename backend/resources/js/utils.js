import mime from 'mime-types'

export const getIcon = (type) =>
    ({
        folder: 'folder',
        pdf: 'picture_as_pdf',
        image: 'image',
        video: 'movie',
    }[type])

export const getIndex = (files, file) =>
    files.findIndex((i) => {
        return i.uniqueIdentifier == file.uniqueIdentifier
    })

export const getCustomFieldNames = (category) =>
    ({
        invoice: ['invoice_number', 'invoice_date', 'due_date'],
        offer: [],
        order: [],
        delivery: [],
    }[category] || [])

export const makeCustomField = (field) =>
    ({
        ['invoice_number']: {
            ...field,
            placeholder: 'Enter invoice number',
            returnMaskedValue: false,
        },

        ['invoice_date']: {
            ...field,
            mask: '##/##/####',
            placeholder: 'MM/DD/YYYY',
            returnMaskedValue: true,
        },

        ['due_date']: {
            ...field,
            mask: '##/##/####',
            placeholder: 'MM/DD/YYYY',
            returnMaskedValue: true,
        },
    }[field.name])

export const makeDetails = (media) => {
    const { category, ...metaFields } = media.metadata

    let fields = {
        id: media.id,
        uuid: media.uuid,
        name: media.name,
        size: media.size || `${media.files} file(s)`,
        type: media.file_type || 'folder',
        fileName: media.file_name || media.name,
        modified: media.modified,
        category: category,
        customFields: {
            modifiedLast: metaFields.edited_at,
            modifiedBy: metaFields.edited_by,
        },
        fileIcon: getIcon(media.file_type || 'folder'),
    }

    ;(getCustomFieldNames(category) || []).forEach((i) => {
        fields.customFields[i] = metaFields[i]
    })

    fields.members = [
        {
            id: media.owner.id,
            name: media.owner.name,
            photo: media.owner.photo,
            isOwner: true,
        },
        ...media.members.map((i) => ({
            id: i.id,
            name: i.name,
            photo: i.photo,
            isOwner: false,
        })),
    ]

    return fields
}

export const createNativeFileFromResumableFile = (f) =>
    !f
        ? {}
        : {
              uniqueIdentifier: f.uniqueIdentifier,
              fileName: f.fileName,
              type: f.file.type,
              file: f.file,
              extension: mime.extension(f.file.type),
              size: f.size,
              progress: Math.floor(f.progress() * 100),
              isComplete: f.isComplete(),
              isPaused: f.isPaused(),
              isUploading: f.isUploading(),
              isFail: false,
              isSuccess: false,
              errors: [],
          }
