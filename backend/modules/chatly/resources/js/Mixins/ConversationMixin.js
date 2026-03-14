import { truncate, isNil } from 'lodash'

export default {
    computed: {
        participants() {
            return this.conversation.participants
        },

        participantNames() {
            let participants = this.participants
            if (participants.length > 1) {
                return participants
                    .map((p) =>
                        truncate(p.name, {
                            length: 10,
                        }),
                    )
                    .join(', ')
            }
            return participants[0].name
        },

        companyName() {
            if (isNil(this.participants[0].organization)) return ''
            return `(${this.participants[0].organization.name})`
        },
    },
}
