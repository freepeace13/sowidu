declare module 'media-source-type' {
    declare export type SourceType = {
        title?: ?string,
        description?: ?string,
        type: MimeType,
        src: string,
        id: number
    }
}

declare module 'media-api-payload' {
    declare export type APIPayload = {

    }
}

declare module 'media-service-payload' {
    declare export type ServicePayload = {

    }
}

declare module 'media-prop-types' {
    declare export type PropTypes = {
        id: number,
        name: string,
        format: string,
        url: string,
        mimetype: MimeType,
        description: string,
        formattedDates: {
            uploadedOn: string,
            yearUploaded: string,
            monthDayUploaded: string,
            uploadedAt: string,
        },
        createdAt: string,
        updatedAt: string,
        $alias: string,
    }
}